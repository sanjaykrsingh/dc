<?php
namespace Testimonial\Model;

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
	
	public function fetchTestimonialDetail($arrWhere = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array("tm"=>"testimonial"));
		$select->where($arrWhere);
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$rowset = $statement->execute();

		 if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
	
	public function fetchTestimonialList($arrWhere = array(),$arrParam = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('testimonial');
		$select->where($arrWhere);
		if(isset($arrParam) && !empty($arrParam))
		{
			$select->order($arrParam['order_by'] . ' ' . $arrParam['order']);
		}
        $statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
        $rowset->buffer();
		return $rowset;
	}
	
	public function insertTestimonial($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	
	public function updateTestimonial($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	
	public function deleteTestimonial($arrWhere)
	{
		$this->tableGateway->delete($arrWhere);
	}
}