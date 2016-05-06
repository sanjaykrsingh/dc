<?php
namespace Usedcar\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;


class MmvDetailTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('make_model_variant', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function getMmvMakeList($year)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("make_id"));
		$select->from(array("mmv"=>"make_model_variant"));
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->where("start_year <= $year AND (end_year >= $year || end_year is NULL)");
		$select->group("make_id");
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getMmvModelList($make_id,$year)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("model_id"));
		$select->from(array("mmv"=>"make_model_variant"));
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->where("make_id = $make_id AND start_year <= $year AND (end_year >= $year || end_year is NULL)");
		$select->group("model_id");
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function get_min_start_year()
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array(new Expression('min(start_year) as min_start_year')));
		$select->from("make_model_variant");
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		$row = $rowset->current();
		return $row['min_start_year'];
	}
	
	public function get_make_name($make_id)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('make_name'));
		$select->from("make_master");
		$select->where(array("make_id"=>$make_id));
			
//echo $sql->getSqlstringForSqlObject($select); die ;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		$row = $rowset->current();
		return $row['make_name'];
	}
	
	public function get_model_name($model_id)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('model_name'));
		$select->from("model_master");
		$select->where(array("model_id"=>$model_id));
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		$row = $rowset->current();
		return $row['model_name'];
	}
}