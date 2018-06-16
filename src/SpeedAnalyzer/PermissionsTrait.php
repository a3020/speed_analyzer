<?php

namespace A3020\SpeedAnalyzer;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

trait PermissionsTrait
{
    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/speed_analyzer');
        $cp = $this->app->make(Checker::class, [$page]);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            throw new UserMessageException(t('Access Denied'));
        }
    }
}
