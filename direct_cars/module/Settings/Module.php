<?php
namespace Settings;

// Add these import statements:
use Settings\Model\Settings;
use Settings\Model\SettingsTable;
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
					'Settings\Model\SettingsTable' =>  function($sm) {
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