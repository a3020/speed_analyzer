<?php

namespace Concrete\Package\SpeedAnalyzer\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class SpeedAnalyzer extends DashboardPageController
{
    public function view()
    {
        return Redirect::to('/dashboard/speed_analyzer/reports');
    }
}
