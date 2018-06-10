<?php

namespace A3020\SpeedAnalyzer\Ajax\Diagnosis;

use A3020\SpeedAnalyzer\PermissionsTrait;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Response;
use Concrete\Core\Support\Facade\Package;
use Concrete\Core\View\View;

class Packages extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait, PermissionsTrait;

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
