<?php
namespace Core\Router;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Router\RoutePluginManager;

class RoutePluginManagerFactory implements FactoryInterface
{

    /**
     * @param  ContainerInterface $container
     * @param  string $name
     * @param  array $options
     * @return RoutePluginManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['router']['plugin_manager']) ? $config['router']['plugin_manager'] : [];

        return new RoutePluginManager($container, $config);
    }

}
