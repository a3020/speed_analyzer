<?php

namespace A3020\SpeedAnalyzer\Ajax\Diagnosis;

use A3020\SpeedAnalyzer\PermissionsTrait;
use Concrete\Core\Geolocator\GeolocationResult;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\View\View;
use Exception;

class Location extends \Concrete\Core\Controller\Controller
{
    use PermissionsTrait;

    public function view()
    {
        $this->checkPermissions();

        $serverIp = $this->getServerIp();

        $view = new View('diagnosis/location');
        $view->setPackageHandle('speed_analyzer');
        $view->addScopeItems([
            'serverIp' => $serverIp,
            'clientIp' => $this->getClientIP(),
            'serverCountry' => $this->getServerCountry($serverIp),
            'clientCountry' => $this->getClientCountry(),
        ]);

        return Response::create($view->render());
    }

    private function getServerCountry($serverIp)
    {
        if (!$serverIp) {
            return false;
        }

        try {
            /** @var GeolocationResult\ $geoLocationResult */
            $geoLocationResult = $this->app->make(GeoLocationResult::class, [
                'ip' => $serverIp,
            ]);

            return $geoLocationResult->getCountryNameLocalized();
        } catch (Exception $e) {
            return false;
        }
    }

    private function getClientCountry()
    {
        try {
            /** @var GeolocationResult\ $geoLocationResult */
            $geoLocationResult = $this->app->make(GeoLocationResult::class);

            return $geoLocationResult->getCountryNameLocalized();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return string|false
     */
    private function getServerIp()
    {
        /** @var Request $request */
        $request = $this->app->make(Request::class);

        if ($request->server->has('REMOTE_ADDR')) {
            return $request->server->get('REMOTE_ADDR');
        }

        return false;
    }

    private function getClientIP()
    {
        /* @var \Concrete\Core\Permission\IPService $ipService */
        $ipService = $this->app->make('ip');

        return (string) $ipService->getRequestIPAddress();
    }
}
