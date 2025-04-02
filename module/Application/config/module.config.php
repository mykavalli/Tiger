<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            // 'application' => [
            //     'type'    => Segment::class,
            //     'options' => [
            //         'route'    => '/application[/:action]',
            //         'defaults' => [
            //             'controller' => Controller\IndexController::class,
            //             'action'     => 'index',
            //         ],
            //     ],
            // ],

            // Manage authentication routes
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'recover-password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/password[/:action]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'forgot',
                    ],
                ],
            ],
        	'profile' => [
        		'type'    => Literal::class,
        		'options' => [
        			'route'    => '/profile',
        			'defaults' => [
        				'controller' => Controller\AuthController::class,
        				'action'     => 'profile',
        			],
        		],
        	],
        	'manage-attendance-v2' => [
        		'type'    => Literal::class,
        		'options' => [
        			'route'    => '/v2/manage-attendance',
        			'defaults' => [
        				'controller' => Controller\IndexController::class,
        				'action'     => 'ManageAttendanceV2',
        			],
        		],
        	],
        	'detail-attendance-v2' => [
        		'type'    => Literal::class,
        		'options' => [
        			'route'    => '/v2/detail-attendance',
        			'defaults' => [
        				'controller' => Controller\IndexController::class,
        				'action'     => 'DetailAttendanceV2',
        			],
        		],
        	],
        	'manage-job-v2' => [
        		'type'    => Literal::class,
        		'options' => [
        			'route'    => '/v2/manage-job',
        			'defaults' => [
        				'controller' => Controller\IndexController::class,
        				'action'     => 'ManageJobV2',
        			],
        		],
        	],
        	'manage-user' => [
        		'type'    => Literal::class,
        		'options' => [
        			'route'    => '/manage-user',
        			'defaults' => [
        				'controller' => Controller\IndexController::class,
        				'action'     => 'ManageUser',
        			],
        		],
        	],
        	'logger' => [
        		'type'    => Literal::class,
        		'options' => [
        			'route'    => '/logger',
        			'defaults' => [
        				'controller' => Controller\LoggerController::class,
        				'action'     => 'index',
        			],
        		],
        	],
        ],
    ],
    'controllers' => [
        'factories' => [
            // Controller\IndexController::class => InvokableFactory::class,
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\LoggerController::class => Controller\Factory\LoggerControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout_backend.phtml',
            'layout/layout_blank'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
