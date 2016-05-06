<?php
namespace Usedcar\Model;

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
	
	public function insertGeneralEnquiry($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
}