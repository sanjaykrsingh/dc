<?php
namespace Mmv;

// Add these import statements:
use Mmv\Model\MakeMaster;
use Mmv\Model\MakeMasterTable;
use Mmv\Model\ModelMaster;
use Mmv\Model\ModelMasterTable;
use Mmv\Model\VariantMaster;
use Mmv\Model\VariantMasterTable;
use Mmv\Model\MmvDetail;
use Mmv\Model\MmvDetailTable;
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
					'Mmv\Model\MmvDetailTable' =>  function($sm) {
						$tableGateway = $sm->get('MmvDetailTableGateway');
						$table = new MmvDetailTable($tableGateway);
						return $table;
					},
					'MmvDetailTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new MmvDetail());
						return new TableGateway('make_model_variant', $dbAdapter, null, $resultSetPrototype);
					},
					'Mmv\Model\MakeMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('MakeMasterTableGateway');
						$table = new MakeMasterTable($tableGateway);
						return $table;
					},
					'MakeMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new MakeMaster());
						return new TableGateway('make_master', $dbAdapter, null, $resultSetPrototype);
					},
					'Mmv\Model\ModelMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('ModelMasterTableGateway');
						$table = new ModelMasterTable($tableGateway);
						return $table;
					},
					'ModelMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new ModelMaster());
						return new TableGateway('model_master', $dbAdapter, null, $resultSetPrototype);
					},
					'Mmv\Model\VariantMasterTable' =>  function($sm) {
						$tableGateway = $sm->get('VariantMasterTableGateway');
						$table = new VariantMasterTable($tableGateway);
						return $table;
					},
					'VariantMasterTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new VariantMaster());
						return new TableGateway('variant_master', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}