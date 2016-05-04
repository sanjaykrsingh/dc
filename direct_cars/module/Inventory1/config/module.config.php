<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Inventory\Controller\Inventory' => 'Inventory\Controller\InventoryController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'inventory' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/inventory[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Inventory\Controller\Inventory',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'inventory' => __DIR__ . '/../view',
        ),
    ),
);