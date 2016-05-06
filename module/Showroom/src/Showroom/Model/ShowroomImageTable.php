<?php
namespace Showroom\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class ShowroomImageTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('showroom_image', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	
	public function fetchCustomShoroomImageList($arrWhere = array(),$arrParam = array())
	{
		$adapter = $this->tableGateway->getAdapter(); 
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('showroom_image');
		$select->where($arrWhere);
		if(isset($arrParam) && !empty($arrParam))
		{
			$select->order($arrParam['order_by'] . ' ' . $arrParam['order']);
		}
        $statement = $sql->prepareStatementForSqlObject($select);
		$rowset = $statement->execute();
         if ($rowset->count()) {
         $rows = new ResultSet();
         return $rows->initialize($rowset)->toArray();
		}
		return array();
	}
}