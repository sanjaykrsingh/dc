<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class CustomerMasterTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('customer_master', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchCustomerDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("cm"=>"customer_master"));
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();

		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	
	public function getCustomerDetail($arrData)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("cm"=>"customer_master"));
		$arrWhere = "";
		if(@$arrData->customer_mobile != "")
		{
			$arrWhere .= "customer_mobile = '".$arrData->customer_mobile."' OR customer_alt_mobile = '".$arrData->customer_mobile."'";
		}
		if(@$arrData->customer_id != "")
		{
			$arrWhere .= "customer_id = '".$arrData->customer_id."'";
		}
		
		if($arrWhere != "")		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		//echo $sql->getSqlstringForSqlObject($select); die ; // ( die/exit to debugging purpose )
		$rowset = $statement->execute();
	
		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchCustomerList($arrPost = array(),$arrParam = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('customer_master');
		if(!empty($arrPost))
		{
			if(isset($arrPost['start_date']) && $arrPost['start_date'] != "") {
				if(isset($arrPost['end_date']))
					$select->where->between("created_date",$arrPost['start_date'],$arrPost['end_date']);
				else
					$select->where->between("created_date",$arrPost['start_date']." 00:00:00",$arrPost['start_date']." 23:59:59");
			}
		}
		if($arrPost['customer_name'] != "")
		{
			$select->where("customer_name like '%".$arrPost['customer_name']."%'");
		}
		if($arrPost['customer_email'] != "")
		{
			$select->where("customer_email like '%".$arrPost['customer_email']."%'");
		}
		if($arrPost['customer_mobile'] != "")
		{
			$select->where("customer_mobile like '%".$arrPost['customer_mobile']."%'");
		}
		//$select->where($arrWhere);
		if(isset($arrParam) && !empty($arrParam))
		{
			$select->order($arrParam['order_by'] . ' ' . $arrParam['order']);
		}
        $statement = $sql->prepareStatementForSqlObject($select);
		//echo $select->getSqlString();
		$rowset = $statement->execute();
        $rowset->buffer();
		return $rowset;
	}
	
	public function insertCustomer($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	
	public function updateCustomer($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	
	public function deleteCustomer($arrWhere)
	{
		$this->tableGateway->delete($arrWhere);
	}
}