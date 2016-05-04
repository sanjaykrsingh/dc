<?php
namespace Testimonial;

// Add these import statements:
use Testimonial\Model\Testimonial;
use Testimonial\Model\TestimonialTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getServiceConfig()
    {
        return array('factories' => array(
					'Testimonial\Model\TestimonialTable' =>  function($sm) {
						$tableGateway = $sm->get('TestimonialTableGateway');
						$table = new TestimonialTable($tableGateway);
						return $table;
					},
					'TestimonialTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new Testimonial());
						return new TableGateway('testimonial', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}