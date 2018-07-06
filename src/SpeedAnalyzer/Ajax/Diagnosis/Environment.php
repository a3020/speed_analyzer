<?php

namespace A3020\SpeedAnalyzer\Ajax\Diagnosis;

use A3020\SpeedAnalyzer\Environment\LatestPhpVersion;
use A3020\SpeedAnalyzer\Environment\MysqlVersion;
use A3020\SpeedAnalyzer\PermissionsTrait;
use Concrete\Core\Http\Response;
use Concrete\Core\Updater\Update;
use Concrete\Core\View\View;

class Environment extends \Concrete\Core\Controller\Controller
{
    use PermissionsTrait;

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

    /**
     * Get current concrete5 version
     *
     * @return string
     */
    private function getConcrete5Version()
    {
        return APP_VERSION;
    }

    /**
     * Get latest concrete5 version or null if not available
     *
     * @return string|null
     */
    private function getLatestConcrete5Version()
    {
        return Update::getLatestAvailableVersionNumber();
    }

    /**
     * Get latest PHP version or null if not available
     *
     * @param $currentVersion
     *
     * @return array|null
     */
    private function getLatestPhpVersion($currentVersion)
    {
        /** @var LatestPhpVersion $service */
        $service = $this->app->make(LatestPhpVersion::class);

        return $service->getFor($currentVersion);
    }

    /**
     * Return the current MySQL version via a query
     *
     * @return string
     */
    private function getMysqlVersion()
    {
        /** @var MysqlVersion $service */
        $service = $this->app->make(MysqlVersion::class);

        return $service->get();
    }

    /**
     * Return true if Opcache is enabled
     *
     * @return bool
     */
    private function isOpcacheEnabled()
    {
        if (!function_exists('opcache_get_configuration')) {
            return false;
        }

        $configuration = opcache_get_configuration();

        return (bool) $configuration['directives']['opcache.enable'];
    }

    /**
     * Return true if 'validate_timestamps' is enabled
     *
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
     * Return an array of information about opcache
     *
     * E.g. how much memory is used and whether the cache is full.
     *
     * @see http://php.net/manual/en/function.opcache-get-status.php
     *
     * @return array|null
     */
    private function getOpcacheStatus()
    {
        if (!$this->isOpcacheEnabled()) {
            return null;
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
     * Returns the max real path cache size
     *
     * @return int
     */
    private function getMaxRealPathCacheSize()
    {
        // E.g. '16m'
        $value = ini_get('realpath_cache_size');

        // E.g. 'm'
        $unit = strtolower(substr($value, -1, 1));

        // Now we convert back to bytes
        return (int) $value * pow(1024, array_search($unit, [1 =>'k','m','g']));
    }

    /**
     * Get version number of Xdebug
     *
     * @return string
     */
    private function getXdebugVersion()
    {
        return phpversion('xdebug');
    }
}
