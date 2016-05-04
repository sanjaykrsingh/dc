<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Enquiry\Controller\Enquiry' => 'Enquiry\Controller\EnquiryController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'enquiry' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/enquiry[/:action][/:id][/page/:page][/order_by/:order_by][/:order]',
                    'constraints' => array(
                       'action' => '(?!\bpage\b)(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
						'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'Enquiry\Controller\Enquiry',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'enquiry' => __DIR__ . '/../view',
        ),
    ),
);