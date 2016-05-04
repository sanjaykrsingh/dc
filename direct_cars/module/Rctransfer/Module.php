<?php
namespace Rctransfer;

// Add these import statements:
use Rctransfer\Model\AgentMaster;
use Rctransfer\Model\AgentMasterTable;
use Rctransfer\Model\RcTransfer;
use Rctransfer\Model\RcTransferTable;
use Rctransfer\Model\RcTransferLog;
use Rctransfer\Model\RcTransferLogTable;
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
					'Rctransfer\Model\AgentMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('AgentMasterTableGateway');
						$table = new AgentMasterTable($tableGateway);
						return $table;
					},
					'AgentMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new AgentMaster());
						return new TableGateway('agent_master', $dbAdapter, null, $resultSetPrototype);
					},
					'Rctransfer\Model\RcTransferTable' =>  function($sm) {
						$tableGateway = $sm->get('RcTransferTableGateway');
						$table = new RcTransferTable($tableGateway);
						return $table;
					},
					'RcTransferTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new RcTransfer());
						return new TableGateway('rc_transfer', $dbAdapter, null, $resultSetPrototype);
					},
					'Rctransfer\Model\RcTransferLogTable' =>  function($sm) {
						$tableGateway = $sm->get('RcTransferLogTableGateway');
						$table = new RcTransferLogTable($tableGateway);
						return $table;
					},
					'RcTransferLogTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new RcTransferLog());
						return new TableGateway('rc_transfer_log', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}