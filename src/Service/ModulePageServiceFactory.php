<?php
namespace Core\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Core\Model;
use Account\Model\UserRepository;
use Block\Model\BlockRepository;
use Core\Model\EntityFactory;

class ModulePageServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ModulePageService(
            $container->get('Cache\Long'),
            $container->get(Logger::class),
            $container->get(AuthenticationService::class),
            $container->get(EntityManager::class),
            $container->get(UserRepository::class),
            $container->get(Model\ModulePageRepository::class),
            $container->get(BlockRepository::class),
            $container->get(EntityFactory::class)
        );
    }

}
