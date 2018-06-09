<?php

namespace A3020\SpeedAnalyzer\Environment;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Exception;

class LatestPhpVersion implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    const ENDPOINT = 'https://php.net/releases/index.php?json';
    const CACHE_IN_SECONDS = 86400; // 1 day

    /**
     * @param string $currentVersion
     *
     * @return array|bool
     */
    public function getFor($currentVersion)
    {
        $versionsAvailable = $this->get();
        if ($versionsAvailable === false) {
            return false;
        }

        $newerVersions = [];
        foreach ($versionsAvailable as $version) {
            if (version_compare($version, $currentVersion, '>')) {
                $newerVersions[] = $version;
            }
        }

        return $newerVersions;
    }

    /**
     * @return array|bool
     */
    private function get()
    {
        $value = false;

        try {
            $expensiveCache = $this->app->make('cache/expensive');
            $cacheObject = $expensiveCache->getItem('SpeedAnalyzer/LatestPhpVersion');
            if ($cacheObject->isMiss()) {
                $json = $this->doRequest();

                if ($json) {
                    $value = array_column($json, 'version');
                    $expensiveCache->save($cacheObject->set($value, self::CACHE_IN_SECONDS));
                }
            } else {
                $value = $cacheObject->get();
            }
        } catch (Exception $e) {
            $value = false;
        }

        return $value;
    }

    /**
     * Get release information from php.net
     *
     * I don't feel comfortable using a client wrapper
     * seeing the upcoming changes in v9:
     * https://github.com/concrete5/concrete5/commit/525354244786db6ac2469ad837f912c5ee1109c1#diff-08e106abf73e298b97f9aa4effb66b86R48
     *
     * @return string
     */
    private function doRequest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ENDPOINT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }
}
