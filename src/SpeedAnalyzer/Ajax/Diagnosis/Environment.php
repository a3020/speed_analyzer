<?php

namespace A3020\SpeedAnalyzer\Ajax\Diagnosis;

use A3020\SpeedAnalyzer\Environment\LatestPhpVersion;
use A3020\SpeedAnalyzer\Environment\MysqlVersion;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Update;
use Concrete\Core\View\View;

class Environment extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function view()
    {
        $this->checkPermissions();

        $view = new View('diagnosis/environment');
        $view->setPackageHandle('speed_analyzer');
        $view->addScopeItems([
            'concrete5Version' => $this->getConcrete5Version(),
            'concrete5VersionAvailable' => $this->getLatestConcrete5Version(),
            'phpVersion' => phpversion(),
            'phpVersionAvailable' => $this->getLatestPhpVersion(phpversion()),
            'mysqlVersion' => $this->getMysqlVersion(),
            'xdebugVersion' => $this->getXdebugVersion(),
            'opcacheEnabled' => $this->isOpcacheEnabled(),
            'opcacheValidateTimestamps' => $this->getOpcacheValidateTimestamps(),
            'opcacheStatus' => $this->getOpcacheStatus(),
            'realPathCacheSize' => $this->getRealPathCacheSize(),
            'maxRealPathCacheSize' => $this->getMaxRealPathCacheSize(),
        ]);

        return Response::create($view->render());
    }

    private function getConcrete5Version()
    {
        return APP_VERSION;
    }

    private function getLatestConcrete5Version()
    {
        return Update::getLatestAvailableVersionNumber();
    }

    private function getLatestPhpVersion($currentVersion)
    {
        /** @var LatestPhpVersion $service */
        $service = $this->app->make(LatestPhpVersion::class);

        return $service->getFor($currentVersion);
    }

    private function getMysqlVersion()
    {
        /** @var MysqlVersion $service */
        $service = $this->app->make(MysqlVersion::class);

        return $service->get();
    }

    private function isOpcacheEnabled()
    {
        if (!function_exists('opcache_get_configuration')) {
            return false;
        }

        $configuration = opcache_get_configuration();

        return (bool) $configuration['directives']['opcache.enable'];
    }

    /**
     * @return bool
     */
    private function getOpcacheValidateTimestamps()
    {
        if (!$this->isOpcacheEnabled()) {
            return false;
        }

        return (bool) ini_get('opcache.validate_timestamps');
    }

    /**
     * @see http://php.net/manual/en/function.opcache-get-status.php
     *
     * @return array|bool
     */
    private function getOpcacheStatus()
    {
        if (!$this->isOpcacheEnabled()) {
            return false;
        }

        return opcache_get_status();
    }

    /**
     * "realpath_cache_size" is used by PHP to cache the real file system paths of file names referenced instead of looking them up each time.
     * Every time you perform any of the various file functions or include/require a file and use a relative path,
     * PHP has to look up where that file really exists. PHP caches those values so it doesn't have to search the current
     * working directory and include_path for the file you are working on.
     *
     * @return int
     */
    private function getRealPathCacheSize()
    {
        return realpath_cache_size();
    }

    /**
     * @return string
     */
    private function getMaxRealPathCacheSize()
    {
        $memoryInBytes = function ($value) {
            $unit = strtolower(substr($value, -1, 1));
            return (int) $value * pow(1024, array_search($unit, array(1 =>'k','m','g')));
        };

        return $memoryInBytes(ini_get('realpath_cache_size'));
    }

    private function getXdebugVersion()
    {
        return phpversion('xdebug');
    }

    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/speed_analyzer');
        $cp = new \Permissions($page);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            die(t('Access Denied'));
        }
    }
}
