<?php

namespace Concrete\Package\SpeedAnalyzer;

use A3020\SpeedAnalyzer\Installer\Installer;
use A3020\SpeedAnalyzer\Installer\Uninstaller;
use A3020\SpeedAnalyzer\Provider\SpeedAnalyzerServiceProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageFacade;

final class Controller extends Package
{
    protected $pkgHandle = 'speed_analyzer';
    protected $appVersionRequired = '8.2.1';
    protected $pkgVersion = '1.2.3';
    protected $pkgAutoloaderRegistries = [
        'src/SpeedAnalyzer' => '\A3020\SpeedAnalyzer',
    ];

    public function getPackageName()
    {
        return t('Speed Analyzer');
    }

    public function getPackageDescription()
    {
        return t('Analyze the loading times of your pages.');
    }

    public function on_start()
    {
        $provider = $this->app->make(SpeedAnalyzerServiceProvider::class);
        $provider->register();
    }

    public function install()
    {
        $pkg = parent::install();

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();

        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle($this->pkgHandle);

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }

    public function uninstall()
    {
        $uninstaller = $this->app->make(Uninstaller::class);
        $uninstaller->uninstall();

        parent::uninstall();
    }
}
