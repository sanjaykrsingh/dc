<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;

class StockImagesTable
{
   protected $tableGateway;

    public function __construct() {
        $config = $this->getConfig();
        $dbAdapter          = new \Zend\Db\Adapter\Adapter($config['db']);
        $this->tableGateway = new TableGateway('stock_images', $dbAdapter);
    }
    
    public function getConfig() {
		return include 'config/autoload/database.local.php';
    }
	public function fetchStockImageList($arrWhere = array())
	{
		$resultSet = $this->tableGateway->select(function($select) use($arrWhere){
			$select->where($arrWhere);
			$select->order(array('dis_order'));

		});
        return $resultSet;
	}
	
	public function insertStockImage($arrData)
	{
		$this->tableGateway->insert($arrData);
		return $this->tableGateway->getLastInsertValue();
	}
	public function updateStockImage($arrData,$arrWhere)
	{
		$this->tableGateway->update($arrData,$arrWhere);
	}
	public function deleteStockImage($arrWhere)
	{
		$this->tableGateway->delete($arrWhere);
	}
}