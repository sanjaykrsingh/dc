<?php


return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'db' => array(
        'driver'    => 'pdo',
        'dsn'       => 'mysql:dbname=db_direct_cars;host=ec2-54-169-249-58.ap-southeast-1.compute.amazonaws.com',
        'username'  => 'directcars',
        'password'  => 'cars123',
    ),
);
