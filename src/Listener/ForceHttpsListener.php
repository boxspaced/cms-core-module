<?php
namespace Boxspaced\CmsCoreModule\Listener;

use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class ForceHttpsListener
{

    /**
     * @param MvcEvent $event
     * @return Response|null
     */
    public function __invoke(MvcEvent $event)
    {
        $config = $event->getApplication()->getServiceManager()->get('config');

        if (!$config['core']['has_ssl']) {
            return null;
        }

        $controller = $event->getTarget();

        $result = $controller->forceHttps();

        if ($result instanceof Response) {
            return $result;
        }

        return null;
    }

}
