<?php
namespace Showroom;

// Add these import statements:
use Showroom\Model\ShowroomMaster;
use Showroom\Model\ShowroomMasterTable;
use Showroom\Model\ShowroomImage;
use Showroom\Model\ShowroomImageTable;
use Showroom\Model\Cities;
use Showroom\Model\CitiesTable;
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
					'Showroom\Model\ShowroomMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('ShowroomMasterTableGateway');
						$table = new ShowroomMasterTable($tableGateway);
						return $table;
					},
					'ShowroomMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new ShowroomMaster());
						return new TableGateway('showroom_master', $dbAdapter, null, $resultSetPrototype);
					},
					'Showroom\Model\ShowroomImageTable' =>  function($sm) {
						$tableGateway = $sm->get('ShowroomImageTableGateway');
						$table = new ShowroomImageTable($tableGateway);
						return $table;
					},
					'ShowroomImageTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new ShowroomImage());
						return new TableGateway('showroom_image', $dbAdapter, null, $resultSetPrototype);
					},
					'Showroom\Model\CitiesTable' =>  function($sm) {
						$tableGateway = $sm->get('CitiesTableGateway');
						$table = new CitiesTable($tableGateway);
						return $table;
					},
					'CitiesTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new Cities());
						return new TableGateway('cities', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}