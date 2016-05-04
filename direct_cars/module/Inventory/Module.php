<?php
namespace Inventory;

// Add these import statements:
use Inventory\Model\StockMaster;
use Inventory\Model\StockMasterTable;
use Inventory\Model\StockImages;
use Inventory\Model\StockImagesTable;
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
					'Inventory\Model\StockMasterTable' =>  function($sm) {
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
					'Inventory\Model\StockImagesTable' =>  function($sm) {
						$tableGateway = $sm->get('StockImagesTableGateway');
						$table = new StockImagesTable($tableGateway);
						return $table;
					},
					'StockImagesTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new StockImages());
						return new TableGateway('stock_images', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}