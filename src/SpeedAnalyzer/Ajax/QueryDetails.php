<?php

namespace A3020\SpeedAnalyzer\Ajax;

use A3020\SpeedAnalyzer\Event\EventRepository;
use A3020\SpeedAnalyzer\PermissionsTrait;
use A3020\SpeedAnalyzer\Query\QueryRepository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\View\View;

class QueryDetails extends \Concrete\Core\Controller\Controller
{
    use PermissionsTrait;

    public function view($eventId = null)
    {
        $this->checkPermissions();

        /** @var \A3020\SpeedAnalyzer\Event\EventRepository $repository */
        $repository = $this->app->make(EventRepository::class);
        $event = $repository->find($eventId);

        if (!$event) {
            throw new UserMessageException(t('Event not found.'));
        }

        /** @var QueryRepository $repository */
        $repository = $this->app->make(QueryRepository::class);
        $queries = $repository->findBy([
            'event' => $event,
        ]);

        if (count($queries) === 0) {
            throw new UserMessageException(t('No queries found.'));
        }

        $view = new View('query_details');
        $view->setPackageHandle('speed_analyzer');
        $view->addScopeItems([
            'queries' => $queries,
        ]);

        return Response::create($view->render());
    }
}
