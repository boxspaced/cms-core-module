<?php
namespace Boxspaced\CmsCoreModule\Controller;

use Interop\Container\ContainerInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractControllerFactory
{

    /**
     * @param AbstractActionController $controller
     * @param ContainerInterface $container
     * @return AbstractActionController
     */
    protected function forceHttps(
        AbstractActionController $controller,
        ContainerInterface $container
    )
    {
        if (!$container->get('config')['core']['has_ssl']) {
            return $controller;
        }

        $events = $container->get('EventManager');

        $events->attach('dispatch', function ($event) use ($controller) {

            $result = $controller->forceHttps();

            if ($result instanceof Response) {
                return $result;
            }

        }, 100);

        $controller->setEventManager($events);

        return $controller;
    }

    /**
     * @todo review this as it is just a hack to get the nav on every page
     * @param AbstractActionController $controller
     * @return AbstractActionController
     */
    protected function adminNavigationWidget(AbstractActionController $controller)
    {
        $adminNavigation = $controller->adminNavigationWidget();

        if (null !== $adminNavigation) {
            $controller->layout()->addChild($adminNavigation, 'adminNavigation');
        }

        return $controller;
    }

}
