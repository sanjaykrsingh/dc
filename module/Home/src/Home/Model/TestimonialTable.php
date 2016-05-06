<?php
namespace Home\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class TestimonialTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('testimonial', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchTestimonialList($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('testimonial');
		$select->where($arrWhere);
		$select->where("status='Publish'");
        $statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
        $rowset->buffer();
		return $rowset;
	}
}