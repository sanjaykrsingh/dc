<?php
namespace Mmv\Model;

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
	/**

	public function fetchAllClient($arrParam)
	{
		$resultSet = $this->tableGateway->select(function ($select) use($arrParam) {
			$select->order($arrParam['order_by'] . ' ' . $arrParam['order']);
			});
			 $resultSet->buffer();
        return $resultSet;
	}
	**/
	
	public function getMmvMakeList($year)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("make_id"));
		$select->from(array("mmv"=>"make_model_variant"));
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		//$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		//$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->where("start_year <= $year AND (end_year >= $year || end_year is NULL)");
		//AND (end_year >= $year || end_year is NULL)
		//echo  $select->getSqlString(); die;
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
		//$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		//$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->where("make_id = $make_id AND start_year <= $year AND (end_year >= $year || end_year is NULL)");
		// AND (end_year >= $year || end_year is NULL)
		//echo  $select->getSqlString(); die;
		$select->group("model_id");
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getMmvVariantList($model_id,$year)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("variant_id","fuel_type","transmission_type","mmv_id"));
		$select->from(array("mmv"=>"make_model_variant"));
		//$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		//$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->where("model_id = $model_id  AND start_year <= $year AND (end_year >= $year || end_year is NULL)");
		// AND (end_year >= $year || end_year is NULL)
		//echo  $select->getSqlString(); die;
		$select->group("variant_id");
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchMmvExport()
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("mmv"=>"make_model_variant"));
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 	
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchMmvList($arrWhere = array())
	{
		$resultSet = $this->tableGateway->select($arrWhere);
        return $resultSet;
	}
	public function insertMMV($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	
	public function updateMMV($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}

	public function selectMmvWhereCount($whereCond)
	{
		$resultSet = $this->tableGateway->select($whereCond);
		$count = $resultSet->count();
		return $count;
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
}