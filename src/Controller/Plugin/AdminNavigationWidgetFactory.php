<?php
namespace Boxspaced\CmsCoreModule\Controller\Plugin;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsAccountModule\Service\AccountService;

class AdminNavigationWidgetFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AdminNavigationWidget(
            $container->get(AccountService::class),
            $container->get('config')
        );
    }

}
