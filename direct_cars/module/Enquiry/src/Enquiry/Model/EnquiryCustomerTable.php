<?php
namespace Enquiry\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class EnquiryCustomerTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('enquiry_customer', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function getCustomerDetail($arrData)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("ec"=>"enquiry_customer"));
		$arrWhere = "";
		if(@$arrData->customer_email != "")
		{
			$arrWhere .= "email = '".$arrData->customer_email."'";
		}
		if(@$arrData->customer_email != "" && @$arrData->customer_mobile != "")
		{ 
			$arrWhere .= " OR ";
		}
		if(@$arrData->customer_mobile != "")
		{ 
			$arrWhere .= "email = '".$arrData->customer_email."'";
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
	
	public function updateCustomer($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	public function insertCustomer($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
}