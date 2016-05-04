<?php
namespace Enquiry\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class SellerEnquiryTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('seller_enquiry', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	public function fetchCustomEnquiryList($arrPost = array(),$arrParam = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("se"=>"seller_enquiry"));
		$select->join(array("ec"=>"enquiry_customer"),'se.customer_id=ec.customer_id');
		$select->join(array("mk"=>"make_master"),'mk.make_id=se.make_id',array("make_name"),$select::JOIN_LEFT); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=se.model_id',array("model_name"),$select::JOIN_LEFT);
		if(@$arrPost['start_date'] != "" || @$arrPost['end_date'] != "")
		{
			$select->where->between("enquiry_on",$arrPost['start_date'],$arrPost['end_date']);
		}
		if(isset($arrParam) && !empty($arrParam))
		{
			$select->order($arrParam['order_by'] . ' ' . $arrParam['order']);
		}
        $statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
        $rowset->buffer();
		return $rowset;
	}
	
	public function countSellerEnquery($dateRange)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("enq_id"));
		$select->from(array("se"=>"seller_enquiry"));
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
	
	public function fetchSellerEnquiryDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("se"=>"seller_enquiry"));
		$select->join(array("ec"=>"enquiry_customer"),'se.customer_id=ec.customer_id');
		$select->join(array("mk"=>"make_master"),'mk.make_id=se.make_id',array("make_name"),$select::JOIN_LEFT); 
		$select->join(array("mo"=>"model_master"),'mo.model_id=se.model_id',array("model_name"),$select::JOIN_LEFT);
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
	
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	public function updateSellerInquiry($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	public function insertSellerInquiry($arrData)
	{
		$this->tableGateway->insert($arrData);
	}
}