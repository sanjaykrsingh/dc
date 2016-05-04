<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Mmv\Controller\Mmv' => 'Mmv\Controller\MmvController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'mmv' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/mmv[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Mmv\Controller\Mmv',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'mmv' => __DIR__ . '/../view',
        ),
    ),
);