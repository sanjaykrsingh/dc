<?php
namespace Inventory\Model;

class StockImages
{
    public $stock_image_id;
    public $stock_id;	
	public $image_name;
    public $image_title;	
	public $created_on;
	public $dis_order;
	public $is_profile_img;
	
    public function exchangeArray($data)
    {
		$this->stock_image_id	= (isset($data['stock_image_id'])) ? $data['stock_image_id'] : null;
		$this->stock_id			= (isset($data['stock_id'])) ? $data['stock_id'] : null;	
		$this->image_name		= (isset($data['image_name'])) ? $data['image_name'] : null;
		$this->image_title		= (isset($data['image_title'])) ? $data['image_title'] : null;
		$this->created_on		= (isset($data['created_on'])) ? $data['created_on'] : null;
		$this->dis_order		= (isset($data['dis_order'])) ? $data['dis_order'] : null;
		$this->is_profile_img	= (isset($data['is_profile_img'])) ? $data['is_profile_img'] : null;
    }
}