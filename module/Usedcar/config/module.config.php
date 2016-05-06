<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Usedcar\Controller\Usedcar' => 'Usedcar\Controller\UsedcarController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'usedcar' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/used-cars[/:action][/:id][/page/:page][/:keyword]',
                    'constraints' => array(
                       'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
						'page' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Usedcar\Controller\Usedcar',
                        'action'     => 'index',
                    ),
                ),
				'may_terminate' => true,
				'child_routes' => array(
                    'view-car' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/used-cars[/:action][:id]',
                            'defaults' => array(
                                'controller' => 'Usedcar\Controller\Usedcar',
								'action'     => 'view-car',
                            ),
                        ),
                    ),
				),
            ),
        ),
    ),

    'view_manager' => array(

        'template_path_stack' => array(
            'usedcar' => __DIR__ . '/../view',
        ),
    ),
);