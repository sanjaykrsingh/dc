<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Testimonial\Controller\Testimonial' => 'Testimonial\Controller\TestimonialController',
        ),
    ),

    // The following section is new and should be added to your file
   'router' => array(
        'routes' => array(
            'testimonial' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/testimonial[/:action][/:id][/page/:page][/order_by/:order_by][/:order]',
                    'constraints' => array(
                       'action' => '(?!\bpage\b)(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
						'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'Testimonial\Controller\Testimonial',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'testimonial' => __DIR__ . '/../view',
        ),
    ),
);