<?php
namespace Rctransfer\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class AgentMasterTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('agent_master', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchAgentDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("am"=>"agent_master"));
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();

		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function insertAgent($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	
}