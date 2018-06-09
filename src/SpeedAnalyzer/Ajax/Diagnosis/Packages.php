<?php

namespace A3020\SpeedAnalyzer\Ajax\Diagnosis;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Package;
use Concrete\Core\View\View;

class Packages extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function view()
    {
        $this->checkPermissions();

        $view = new View('diagnosis/packages');
        $view->setPackageHandle('speed_analyzer');
        $view->addScopeItems([
            'imageOptimizerInstalled' => $this->isInstalled('image_optimizer'),
            'minifyHtmlInstalled' => $this->isInstalled('minify_html'),
        ]);

        return Response::create($view->render());
    }

    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/speed_analyzer');
        $cp = new \Permissions($page);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            throw new UserMessageException(t('Access Denied'));
        }
    }

    /**
     * @param string $pkgHandle
     *
     * @return bool
     */
    private function isInstalled($pkgHandle)
    {
        /** @see \Concrete\Core\Package\PackageService */
        /** @var \Concrete\Core\Entity\Package $pkg */
        $pkg = Package::getByHandle($pkgHandle);
        if (!$pkg) {
            return false;
        }

        return $pkg->isPackageInstalled();
    }
}
