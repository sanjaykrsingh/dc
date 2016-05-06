<?php
namespace Home;

use Home\Model\Testimonial;
use Home\Model\TestimonialTable;
use Home\Model\StockMaster;
use Home\Model\StockMasterTable;
use Home\Model\Settings;
use Home\Model\SettingsTable;
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
					'Home\Model\TestimonialTable' =>  function($sm) {
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
					'Home\Model\StockMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('StockMasterTableGateway');
						$table = new StockMasterTable($tableGateway);
						return $table;
					},
					'StockMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new StockMaster());
						return new TableGateway('stock_master', $dbAdapter, null, $resultSetPrototype);
					},
					'Home\Model\SettingsTable' =>  function($sm) {
						$tableGateway = $sm->get('SettingsTableGateway');
						$table = new SettingsTable($tableGateway);
						return $table;
					},
					'SettingsTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new Settings());
						return new TableGateway('settings', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}