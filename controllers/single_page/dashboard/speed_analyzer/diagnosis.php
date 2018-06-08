<?php

namespace Concrete\Package\SpeedAnalyzer\Controller\SinglePage\Dashboard\SpeedAnalyzer;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;

final class Diagnosis extends DashboardPageController
{
    /** @var Repository $config */
    protected $config;

    public function on_before_render()
    {
        parent::on_before_render();

        $al = AssetList::getInstance();

        $al->register('css', 'speed_analyzer/style', 'css/style.css', [], 'speed_analyzer');
        $this->requireAsset('css', 'speed_analyzer/style');
    }

    public function view()
    {
        $this->config = $this->app->make(Repository::class);

    }
}
