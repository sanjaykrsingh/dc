<?php
namespace Mmv\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;

class ModelMasterTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('model_master', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	public function fetchModelList($arrWhere = array())
	{
		$resultSet = $this->tableGateway->select($arrWhere);
        return $resultSet;
	}
	
	public function selectModelWhereCount($whereCond)
	{
		$resultSet = $this->tableGateway->select($whereCond);
		$count = $resultSet->count();
		return $count;
	}
	
	public function insertModel($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	
	public function updateModel($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
}