<?php
namespace Home\Model;

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
	
	public function getStockBodyStyle()
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('cnt' =>  new Expression('count(stock_id)')));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array('bod_style'));
		$select->where("status= '1'");
		$select->group("bod_style");
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
         if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	public function getHotDeal()
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),
				"is_profile_img" => new Expression("GROUP_CONCAT(is_profile_img)")),$select::JOIN_LEFT);
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mm"=>"make_master"),'mm.make_id=mmv.make_id',array('make_name'));
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array('model_name'));
		$select->join(array("vm"=>"variant_master"),'vm.variant_id=mmv.variant_id',array('variant_name'));
		$select->where("status= '1'");
		$select->where("hot_deal= '1'");
		$rand = new \Zend\Db\Sql\Expression('RAND()');
        $select->order($rand);
		$select->limit(1);
		$statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
        if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockModel($make_name = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array());
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array());
		$select->join(array("mm"=>"model_master"),'mm.model_id=mmv.model_id',array('model_id','model_name'));
		if($make_name  != "")	$select->where("make_name = '$make_name'");
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
	
	public function getStockList($strWhere="",$strOrder="")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);

		$select2 = $sql->select();

		$select2->columns(array("stock_id"));
		$select2->from(array("sm"=>"stock_master"));
		$select2->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select2->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array()); 		
		$select2->where("sm.status = '1'");
		if($strWhere != "")		$select2->where($strWhere);
		
		$select1 = $sql->select();

		$select1->from(array("sm"=>"stock_master"));
		$select1->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array('noofcylinders','engine_cc','aircondition','alloy_wheels','leather_seats'));
		$select1->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),
				"is_profile_img" => new Expression("GROUP_CONCAT(is_profile_img)")),$select1::JOIN_LEFT);
		$select1->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select1->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select1->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select1->join(array("sr"=>"showroom_master"),'sm.showroom_id=sr.showroom_id',array("showroom_id","showroom_name",
							"land_mark","locality","city","state","pin_code","phone_1"));
		$select1->where("sm.status = '1'");
		if($strWhere != "")		$select1->where->notIn("sm.stock_id",$select2);
		if($strOrder != "")		$select1->order($strOrder);
		$select1->group("stock_id");
		
		
		$select = $sql->select();

		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array('noofcylinders','engine_cc','aircondition','alloy_wheels','leather_seats'));
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),
				"is_profile_img" => new Expression("GROUP_CONCAT(is_profile_img)")),$select::JOIN_LEFT);
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->join(array("sr"=>"showroom_master"),'sm.showroom_id=sr.showroom_id',array("showroom_id","showroom_name",
							"land_mark","locality","city","state","pin_code","phone_1"));
		$select->where("sm.status = '1'");
		if($strWhere != "")		$select->where($strWhere);
		if($strOrder != "")		$select->order($strOrder);
		$select->group("stock_id");
		
		if($strWhere != "")	$select->combine ( $select1 );
		//echo  $select->getSqlString(); 
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		$rowset->buffer();
		return $rowset;
	}
	
	public function getStockMake()
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('cnt' =>  new Expression('count(stock_id)')));
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mm"=>"make_master"),'mm.make_id=mmv.make_id',array('make_name'));
		$select->where("status= '1'");
		$select->group("make_name");
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
         if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockDetail($stock_id)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id');
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name"));
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),),$select::JOIN_LEFT);
		$select->join(array("sr"=>"showroom_master"),'sm.showroom_id=sr.showroom_id',array("showroom_id","showroom_name",
							"land_mark","locality","city","state","pin_code","phone_1"));
		$select->where("sm.stock_id = $stock_id");
		$select->group("sm.stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getShowroomStock($showroom_id)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id');
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name"));
		$select->join(array("si"=>"stock_images"),'sm.stock_id=si.stock_id',array("image_name" => new Expression("GROUP_CONCAT(image_name)"),"is_profile_img" => new Expression("GROUP_CONCAT(is_profile_img)")),$select::JOIN_LEFT);
		$select->where("sm.showroom_id = $showroom_id");
		$select->where("status= '1'");
		$select->group("sm.stock_id");
		$select->limit(3);
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
}