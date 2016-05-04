<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Notify\Controller\Notify' => 'Notify\Controller\NotifyController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'notify' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/notify[/:action][/:id][/page/:page][/order_by/:order_by][/:order]',
                    'constraints' => array(
                       'action' => '(?!\bpage\b)(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
						'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'Notify\Controller\Notify',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'notify' => __DIR__ . '/../view',
        ),
    ),
);