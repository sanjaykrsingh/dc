<?php
namespace Customer;

// Add these import statements:
use Customer\Model\CustomerMaster;
use Customer\Model\CustomerMasterTable;
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
					'Customer\Model\CustomerMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('CustomerMasterTableGateway');
						$table = new CustomerMasterTable($tableGateway);
						return $table;
					},
					'CustomerMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new CustomerMaster());
						return new TableGateway('customer_master', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}