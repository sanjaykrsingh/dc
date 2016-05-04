<?php
namespace Enquiry\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class GeneralEnquiryTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('general_enquiry', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchCustomEnquiryList($arrPost = array(),$arrParam = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("ge"=>"general_enquiry"));
		$select->join(array("ec"=>"enquiry_customer"),'ge.customer_id=ec.customer_id');
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
	
	public function countGeneralEnquery($dateRange)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array("enq_id"));
		$select->from(array("ge"=>"general_enquiry"));
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
	
	public function fetchGeneralEnquiryDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("ge"=>"general_enquiry"));
		$select->join(array("ec"=>"enquiry_customer"),'ge.customer_id=ec.customer_id');
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();
	
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	public function updateGenralInquiry($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
}