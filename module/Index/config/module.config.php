<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Index\Controller\Index' => 'Index\Controller\IndexController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'index' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '[/:action][.:keyword]',
                    'constraints' => array(
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z]*',
						'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'Index\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(

        'template_path_stack' => array(
            'index' => __DIR__ . '/../view',
        ),
    ),
);