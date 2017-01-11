<?php
namespace Boxspaced\CmsCoreModule\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\CmsCoreModule\Model;
use Boxspaced\CmsAccountModule\Model\UserRepository;
use Boxspaced\CmsBlockModule\Model\BlockRepository;
use Boxspaced\CmsCoreModule\Model\EntityFactory;

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
