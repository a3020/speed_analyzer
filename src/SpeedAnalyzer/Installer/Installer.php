<?php

namespace A3020\SpeedAnalyzer\Installer;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Doctrine\ORM\EntityManager;

class Installer
{
    /** @var Repository */
    private $config;

    /** @var EntityManager */
    private $entityManager;

    /** @var DatabaseStructureManager */
    private $structureManager;

    public function __construct(
        Repository $config,
        EntityManager $entityManager,
        DatabaseStructureManager $structureManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->structureManager = $structureManager;
    }

    /**
     * @param \Concrete\Core\Package\Package $pkg
     */
    public function install($pkg)
    {
        $this->refreshEntities();
        $this->configure();
        $this->dashboardPages($pkg);
    }

    private function configure()
    {
        // Enable Speed Analyzer if it's installed for the first time
        if ($this->config->get('speed_analyzer.enabled') === null) {
            $this->config->save('speed_analyzer.enabled', true);
            $this->config->save('speed_analyzer.reports.log_sql_queries', true);
        }
    }

    private function dashboardPages($pkg)
    {
        $pages = [
            '/dashboard/speed_analyzer' => t('Speed Analyzer'),
            '/dashboard/speed_analyzer/reports' => t('Reports'),
            '/dashboard/speed_analyzer/diagnosis' => t('Diagnosis'),
            '/dashboard/speed_analyzer/settings' => t('Settings'),
        ];

        // Using for loop because additional pages
        // may be added in the future.
        foreach ($pages as $path => $name) {
            /** @var Page $page */
            $page = Page::getByPath($path);
            if ($page && !$page->isError()) {
                continue;
            }

            $singlePage = Single::add($path, $pkg);
            $singlePage->update([
                'cName' => $name,
            ]);
        }
    }

    private function refreshEntities()
    {
        // Only refresh if the package is installed
        if ($this->config->get('speed_analyzer.enabled') !== null) {
            $this->structureManager->clearCacheAndProxies();
        }
    }
}
