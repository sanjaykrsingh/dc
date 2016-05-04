<?php
namespace Enquiry\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class BuyerEnquiryTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('buyer_enquiry', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchCustomEnquiryList($arrPost = array(),$arrParam = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("be"=>"buyer_enquiry"));
		$select->join(array("ec"=>"enquiry_customer"),'be.customer_id=ec.customer_id');
		$select->join(array("mmv"=>"make_model_variant"),'be.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 	
		if(@$arrPost['start_date'] != "" || @$arrPost['end_date'] != "")
		{
			$select->where->between("enquiry_on",$arrPost['start_date'],$arrPost['end_date']);
		}
		if(isset($arrParam) && !empty($arrParam))
		{
			$select->order($arrParam['order_by'] . ' ' . $arrParam['order']);
		}
		//echo $sql->getSqlstringForSqlObject($select); die ; // ( die/exit to debugging purpose )
        $statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
        $rowset->buffer();
		return $rowset;
	}
	
	public function countBuyerEnquery($dateRange)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("enq_id"));
		$select->from(array("be"=>"buyer_enquiry"));
		if(isset($dateRange['start'])) {
			if(isset($dateRange['end']))
			$select->where->between("enquiry_on",$dateRange['start'],$dateRange['end']);
			else
			$select->where->between("enquiry_on",$dateRange['start']." 00:00:00",$dateRange['start']." 23:59:59");
		}
		//echo $sql->getSqlstringForSqlObject($select); die ; // ( die/exit to debugging purpose )
        $statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
		return $rowset->count();
	}
	
	public function getStockCnt($dateRange,$strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('enq_id'));
		$select->from(array("be"=>"buyer_enquiry"));
		$select->join(array("sm"=>"stock_master"),'sm.stock_id=be.stock_id',array());
		$select->where($strWhere);
		
		if(isset($dateRange['end']))
			$select->where->between("enquiry_on",$dateRange['start'],$dateRange['end']);
		else
			$select->where->between("enquiry_on",$dateRange['start']." 00:00:00",$dateRange['start']." 23:59:59");
		
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		return $rowset->count();
	}
	
	public function getStockCntByFuel($dateRange)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('cnt' => new Expression("count(enq_id)")));
		$select->from(array("be"=>"buyer_enquiry"));
		$select->join(array("mmv"=>"make_model_variant"),'mmv.mmv_id=be.mmv_id',array('fuel_type'));
		
		if(isset($dateRange['end']))
			$select->where->between("enquiry_on",$dateRange['start'],$dateRange['end']);
		else
			$select->where->between("enquiry_on",$dateRange['start']." 00:00:00",$dateRange['start']." 23:59:59");
		$select->group('fuel_type');
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function getStockCntByModel($dateRange)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('cnt' => new Expression("count(enq_id)")));
		$select->from(array("be"=>"buyer_enquiry"));
		$select->join(array("mmv"=>"make_model_variant"),'be.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name"));
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));		
		if(isset($dateRange['end']))
			$select->where->between("enquiry_on",$dateRange['start'],$dateRange['end']);
		else
			$select->where->between("enquiry_on",$dateRange['start']." 00:00:00",$dateRange['start']." 23:59:59");
		$select->group(array("make_name","model_name"));
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
		if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchBuyerEnquiryDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("be"=>"buyer_enquiry"));
		$select->join(array("ec"=>"enquiry_customer"),'be.customer_id=ec.customer_id');
		$select->join(array("mmv"=>"make_model_variant"),'be.mmv_id=mmv.mmv_id',array());
		$select->join(array("mk"=>"make_master"),'mk.make_id=mmv.make_id',array("make_name")); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=mmv.model_id',array("model_name"));
		$select->join(array("va"=>"variant_master"),'va.variant_id=mmv.variant_id',array("variant_name")); 	
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
	
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	public function updateBuyerInquiry($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	public function insertBuyerInquiry($arrData)
	{
		$this->tableGateway->insert($arrData);
	}
}