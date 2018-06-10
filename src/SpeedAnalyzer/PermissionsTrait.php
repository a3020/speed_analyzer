<?php

namespace A3020\SpeedAnalyzer;

use A3020\ConditionalContent\Checker\Checker;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;

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
