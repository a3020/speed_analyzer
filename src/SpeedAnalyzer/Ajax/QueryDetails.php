<?php

namespace A3020\SpeedAnalyzer\Ajax;

use A3020\SpeedAnalyzer\Event\EventRepository;
use A3020\SpeedAnalyzer\Query\QueryRepository;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\View\View;

class QueryDetails extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function view($eventId = null)
    {
        $this->checkPermissions();

        /** @var \A3020\SpeedAnalyzer\Event\EventRepository $repository */
        $repository = $this->app->make(EventRepository::class);
        $event = $repository->find($eventId);

        if (!$event) {
            die(t('Event not found.'));
        }

        /** @var QueryRepository $repository */
        $repository = $this->app->make(QueryRepository::class);
        $queries = $repository->findBy([
            'event' => $event,
        ]);

        if (count($queries) === 0) {
            die(t("No queries found."));
        }

        $view = new View('query_details');
        $view->setPackageHandle('speed_analyzer');
        $view->addScopeItems([
            'queries' => $queries,
        ]);

        return Response::create($view->render());
    }

    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/speed_analyzer');
        $cp = new \Permissions($page);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            die(t('Access Denied'));
        }
    }
}
