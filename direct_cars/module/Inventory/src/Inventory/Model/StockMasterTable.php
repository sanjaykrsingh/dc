<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
//use Zend\Db\Sql;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class StockMasterTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('stock_master', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	public function fetchStockList($arrWhere = array())
	{
		$resultSet = $this->tableGateway->select($arrWhere);
        return $resultSet;
	}
	
	public function getStockDetail($stock_id)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array('make_id','model_id','variant_id'));
		$select->join(array("se"=>"customer_master"),'se.cust_id=sm.seller_id',array("seller_number"=>"customer_id","seller_name"=>"customer_name","seller_mobile"=>"customer_mobile"),$select::JOIN_LEFT);
		$select->join(array("sb"=>"customer_master"),'sb.cust_id=sm.buyer_id',array("buyer_number"=>"customer_id","buyer_name"=>"customer_name","buyer_mobile"=>"customer_mobile"),$select::JOIN_LEFT);
		$select->where("stock_id = $stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	public function getStockExportDetail($stock_id)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array('make_id','model_id','variant_id'));
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 
		$select->where("stock_id = $stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockModel($strWhere = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array());
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mm"=>"model_master"),'mm.model_id=mmv.model_id',array('model_id','model_name'));
		if($strWhere != "")	$select->where($strWhere);
		$select->group("mm.model_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockMake($strWhere = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array());
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mm"=>"make_master"),'mm.make_id=mmv.make_id',array('make_id','make_name'));
		if($strWhere != "")	$select->where($strWhere);
		$select->group("mm.make_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockStatus($strWhere = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array(new Expression('DISTINCT(car_status) as car_status')));
		$select->from(array("sm"=>"stock_master"));
		
		if($strWhere != "")	$select->where($strWhere);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	
	
	public function fetchSelectedStockList($arrWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();

		//$select->columns(array("make_id"));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),
				"is_profile_img" => new Expression("GROUP_CONCAT(is_profile_img)")),$select::JOIN_LEFT);
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 
		$select->join(array("sr"=>"showroom_master"),'sm.showroom_id=sr.showroom_id',array("showroom_name",
							"land_mark","locality","city","state","pin_code","phone_1"));		
		$select->where("sm.stock_id IN (" . implode(',', $arrWhere) . ")");
		$select->group("sm.stock_id");

		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchVehicleDetail($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();

		//$select->columns(array("make_id"));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->join(array("si"=>"user"),'sm.delivered_by=si.user_id',array("first_name","last_name"),$select::JOIN_LEFT);
		$select->join(array("se"=>"customer_master"),'se.cust_id=sm.seller_id',array("seller_number"=>"customer_id","seller_name"=>"customer_name","seller_mobile"=>"customer_mobile"),$select::JOIN_LEFT);
		$select->join(array("be"=>"customer_master"),'be.cust_id=sm.buyer_id',array("buyer_number"=>"customer_id","buyer_name"=>"customer_name","buyer_mobile"=>"customer_mobile","buyer_address"=>"customer_address","buyer_city"=>"customer_city","buyer_state"=>"customer_state","buyer_pin"=>"customer_pin"),$select::JOIN_LEFT);
		if($strWhere != "")		$select->where($strWhere);
		//echo  $select->getSqlString(); die;
		$select->group("stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	
	public function fetchCustomStockList($strWhere,$strOrder)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();

		//$select->columns(array("make_id"));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),
				"is_profile_img" => new Expression("GROUP_CONCAT(is_profile_img)")),$select::JOIN_LEFT);
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		if($strWhere != "")		$select->where($strWhere);
		if($strOrder != "")		$select->order($strOrder);
		//echo  $select->getSqlString(); die;
		$select->group("stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchStockCountByStatus($strWhere,$strOrder)
	{
	
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();

		$select->columns(array('num' => new \Zend\Db\Sql\Expression('COUNT(*)'),'status'));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		if($strWhere != "")		$select->where($strWhere);
		if($strOrder != "")		$select->order($strOrder);
		//echo  $select->getSqlString(); die;
		$select->group("status");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
		
	}
	
	public function getStockCnt($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('stock_id'));
		$select->from(array("sm"=>"stock_master"));
		$select->where($strWhere);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		return $rowset->count();
	}
	public function getStockCntByFuel($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('cnt' => new Expression("count(stock_id)"),"fuel_type"));
		$select->from(array("sm"=>"stock_master"));
		$select->where($strWhere);
		$select->group('fuel_type');
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	public function getStockCntByModel($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('cnt' => new Expression("count(stock_id)"),"fuel_type"));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name"));
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));		
		$select->where($strWhere);
		$select->group(array("make_name","model_name"));
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockWithImgCnt($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('stock_id' => new Expression("distinct(sm.stock_id)")));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array());
		$select->where($strWhere);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		return $rowset->count();
		
	}

	
	
	public function updateStock($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	
	public function insertStock($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
}