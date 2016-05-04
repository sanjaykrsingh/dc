<?php
namespace Rctransfer\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class RcTransferTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('rc_transfer', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchRctransferDetail($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();

		//$select->columns(array("make_id"));
		$select->from(array("rt"=>"rc_transfer"));
		$select->join(array("sm"=>"stock_master"),'sm.stock_id=rt.stock_id',array("registration_no","make_year","make_month","fuel_type"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->join(array("si"=>"user"),'rt.delivered_by=si.user_id',array("first_name","last_name"),$select::JOIN_LEFT);
		$select->join(array("se"=>"customer_master"),'se.cust_id=rt.seller_id',array("seller_number"=>"customer_id","seller_name"=>"customer_name","seller_mobile"=>"customer_mobile","seller_email"=>"customer_email"),$select::JOIN_LEFT);
		$select->join(array("be"=>"customer_master"),'be.cust_id=rt.buyer_id',array("buyer_number"=>"customer_id","buyer_name"=>"customer_name","buyer_mobile"=>"customer_mobile","buyer_address"=>"customer_address","buyer_city"=>"customer_city","buyer_state"=>"customer_state","buyer_pin"=>"customer_pin","buyer_email"=>"customer_email"),$select::JOIN_LEFT);
		$select->join(array("am"=>"agent_master"),'am.agent_id=rt.agent_id',array("agent_number"=>"agent_number","agent_name"=>"agent_name","agent_mobile"=>"agent_mobile"),$select::JOIN_LEFT);
		if($strWhere != "")		$select->where($strWhere);
		//echo  $select->getSqlString(); die;
		//$select->group("stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockList($strWhere = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$select->from(array("rt"=>"rc_transfer"));
		$select->join(array("sm"=>"stock_master"),'sm.stock_id=rt.stock_id',array("registration_no","make_year","make_month","fuel_type"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		if($strWhere != "")		$select->where($strWhere);

		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getLoginStockList($strWhere = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$select->from(array("sm"=>"stock_master"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		if($strWhere != "")		$select->where($strWhere);

		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchPendencyRecord($strWhere = "")
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();

		//$select->columns(array("make_id"));
		$select->from(array("rt"=>"rc_transfer"));
		$select->join(array("sm"=>"stock_master"),'sm.stock_id=rt.stock_id',array("car_status","registration_no","make_year","make_month","fuel_type"));
		$select->join(array("mmv"=>"make_model_variant"),'sm.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 		
		$select->join(array("se"=>"customer_master"),'se.cust_id=rt.seller_id',array("seller_number"=>"customer_id","seller_name"=>"customer_name","seller_mobile"=>"customer_mobile","seller_email"=>"customer_email"),$select::JOIN_LEFT);
		$select->join(array("be"=>"customer_master"),'be.cust_id=rt.buyer_id',array("buyer_number"=>"customer_id","buyer_name"=>"customer_name","buyer_mobile"=>"customer_mobile","buyer_address"=>"customer_address","buyer_city"=>"customer_city","buyer_state"=>"customer_state","buyer_pin"=>"customer_pin","buyer_email"=>"customer_email"),$select::JOIN_LEFT);
		if($strWhere != "")		$select->where($strWhere);
		//echo  $select->getSqlString(); die;
		//$select->group("stock_id");
		//echo  $select->getSqlString(); die;
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchRcDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("rt"=>"rc_transfer"));
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();

		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function insertRc($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	public function updateRc($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	
}