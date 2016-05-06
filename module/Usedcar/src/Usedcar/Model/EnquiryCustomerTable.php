<?php
namespace Usedcar\Model;

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
	
	public function getCustomerId($strWhere)
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("ec"=>"enquiry_customer"));
		$select->where($strWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();

		 if ($rowset->count()) {
         $rows = new ResultSet();
         $arrData = $rows->initialize($rowset)->toArray();
		 return $arrData[0]['customer_id'];
		}
		return 0;
	}
	
	public function insertEnquiryCustomer($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
}