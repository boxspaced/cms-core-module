<?php
namespace Boxspaced\CmsCoreModule;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Mapper\Conditions;
use Boxspaced\EntityManagerModule\Mapper\ConditionsFactory;
use Zend\ServiceManager\Factory\InvokableFactory;
use Boxspaced\CmsSlugModule\Model\Route;
use Boxspaced\CmsBlockModule\Model\Block;
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
                        'beneath_menu_item_id' => [
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
                        'route_controller' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'route_action' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                    ],
                    'one_to_many' => [
                        'routes' => [
                            'type' => Route::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'module.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'pages' => [
                            'type' => Model\ModulePage::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_module.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ModulePage::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page',
                        'columns' => [
                            'parent_module' => 'module_id',
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
                        'parent_module' => [
                            'type' => Model\Module::class,
                        ],
                    ],
                    'one_to_many' => [
                        'free_blocks' => [
                            'type' => Model\ModulePageFreeBlock::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_module_page.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'block_sequences' => [
                            'type' => Model\ModulePageBlockSequence::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_module_page.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'blocks' => [
                            'type' => Model\ModulePageBlock::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_module_page.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ModulePageBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page_block',
                        'columns' => [
                            'parent_module_page' => 'module_page_id',
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
                        'admin_label' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'sequence' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'parent_module_page' => [
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
                            'parent_module_page' => 'module_page_id',
                            'module_page_block' => 'module_page_block_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_module_page' => [
                            'type' => Model\ModulePage::class,
                        ],
                        'module_page_block' => [
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
                            'parent_module_page' => 'module_page_id',
                            'module_page_block' => 'module_page_block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_module_page' => [
                            'type' => Model\ModulePage::class,
                        ],
                        'module_page_block' => [
                            'type' => Model\ModulePageBlock::class,
                        ],
                    ],
                    'one_to_many' => [
                        'blocks' => [
                            'type' => Model\ModulePageBlockSequenceBlock::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_block_sequence.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                    'ordering' => [
                                        [
                                            'field' => 'order_by',
                                            'direction' => Conditions::ORDER_ASC,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ModulePageBlockSequenceBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'module_page_block_sequence_block',
                        'columns' => [
                            'parent_block_sequence' => 'block_sequence_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'order_by' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_block_sequence' => [
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
