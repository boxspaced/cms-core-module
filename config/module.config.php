<?php
namespace Core;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Mapper\Conditions\Conditions;
use Zend\ServiceManager\Factory\InvokableFactory;
use Slug\Model\Route;
use Block\Model\Block;
use Zend\Router\RoutePluginManager;
use Zend\Mail\Transport\Smtp;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Log\Writer\Stream;
use Zend\Cache\Service\StorageCacheFactory;
use Zend\Log\Logger;
use Zend\Log\LoggerServiceFactory;

return [
    'core' => [
        'hostname' => '',
        'email' => '',
        'has_ssl' => false,
        'admin_show_per_page' => 10,
    ],
    'service_manager' => [
        'factories' => [
            'Cache\Long' => StorageCacheFactory::class,
            Logger::class => LoggerServiceFactory::class,
            RoutePluginManager::class => Router\RoutePluginManagerFactory::class,
            Smtp::class => Mail\TransportFactory::class,
            Service\ModulePageService::class => Service\ModulePageServiceFactory::class,
            Model\EntityFactory::class => Model\EntityFactoryFactory::class,
            Model\ModulePageRepository::class => Model\RepositoryFactory::class,
            Model\ModuleRepository::class => Model\RepositoryFactory::class,
        ]
    ],
    'controller_plugins' => [
        'aliases' => [
            'adminNavigationWidget' => Controller\Plugin\AdminNavigationWidget::class,
            'modulePageAdminWidget' => Controller\Plugin\ModulePageAdminWidget::class,
            'forceHttps' => Controller\Plugin\ForceHttps::class,
            'modulePageBlocks' => Controller\Plugin\ModulePageBlocks::class,
        ],
        'factories' => [
            Controller\Plugin\AdminNavigationWidget::class => Controller\Plugin\AdminNavigationWidgetFactory::class,
            Controller\Plugin\ModulePageAdminWidget::class => Controller\Plugin\ModulePageAdminWidgetFactory::class,
            Controller\Plugin\ForceHttps::class => InvokableFactory::class,
            Controller\Plugin\ModulePageBlocks::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'date' => View\Helper\Date::class,
            'adminFormElement' => View\Helper\AdminFormElement::class,
            'teaserTruncate' => View\Helper\TeaserTruncate::class,
            'messages' => View\Helper\Messages::class,
        ],
    ],
    'cache' => [
        'adapter' => Filesystem::class,
        'ttl'  => 604800,
        'options' => [
            'cache_dir' => 'data/cache',
        ],
        'plugins' => [
            Serializer::class,
        ],
    ],
    'log' => [
        'writers' => [
            [
                'name' => Stream::class,
                'options' => [
                    'stream' => 'data/logs/application.log',
                ],
            ],
        ],
    ],
    'session_manager' => [
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    'entity_manager' => [
        'types' => [
            Model\ProvisionalLocation::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'provisional_location',
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'to' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'beneathMenuItemId' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                    ],
                ],
            ],
            Model\Module::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module',
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'enabled' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'routeController' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'routeAction' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                    ],
                    'children' => [
                        'routes' => [
                            'type' => Route::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('module.id')->eq($id);
                            },
                        ],
                        'pages' => [
                            'type' => Model\ModulePage::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentModule.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\ModulePage::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page',
                        'columns' => [
                            'parentModule' => 'module_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'parentModule' => [
                            'type' => Model\Module::class,
                        ],
                    ],
                    'children' => [
                        'freeBlocks' => [
                            'type' => Model\ModulePageFreeBlock::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentModulePage.id')->eq($id);
                            },
                        ],
                        'blockSequences' => [
                            'type' => Model\ModulePageBlockSequence::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentModulePage.id')->eq($id);
                            },
                        ],
                        'blocks' => [
                            'type' => Model\ModulePageBlock::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentModulePage.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\ModulePageBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page_block',
                        'columns' => [
                            'parentModulePage' => 'module_page_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'adminLabel' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'sequence' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'parentModulePage' => [
                            'type' => Model\ModulePage::class,
                        ],
                    ],
                ],
            ],
            Model\ModulePageFreeBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page_free_block',
                        'columns' => [
                            'parentModulePage' => 'module_page_id',
                            'modulePageBlock' => 'module_page_block_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentModulePage' => [
                            'type' => Model\ModulePage::class,
                        ],
                        'modulePageBlock' => [
                            'type' => Model\ModulePageBlock::class,
                        ],
                        'block' => [
                            'type' => Block::class,
                        ],
                    ],
                ],
            ],
            Model\ModulePageBlockSequence::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page_block_sequence',
                        'columns' => [
                            'parentModulePage' => 'module_page_id',
                            'modulePageBlock' => 'module_page_block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentModulePage' => [
                            'type' => Model\ModulePage::class,
                        ],
                        'modulePageBlock' => [
                            'type' => Model\ModulePageBlock::class,
                        ],
                    ],
                    'children' => [
                        'blocks' => [
                            'type' => Model\ModulePageBlockSequenceBlock::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentBlockSequence.id')->eq($id)
                                        ->order('orderBy', Conditions::ORDER_ASC);
                            }
                        ],
                    ],
                ],
            ],
            Model\ModulePageBlockSequenceBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page_block_sequence_block',
                        'columns' => [
                            'parentBlockSequence' => 'block_sequence_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'orderBy' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentBlockSequence' => [
                            'type' => Model\ModulePageBlockSequence::class,
                        ],
                        'block' => [
                            'type' => Block::class,
                        ],
                    ],
                ],
            ],
        ],
    ],
];