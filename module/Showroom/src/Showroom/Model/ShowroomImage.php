<?php
namespace Showroom\Model;

class ShowroomImage
{
	public $showroom_img_id;
    public $showroom_id;	
	public $img_name;
    public $img_title;	
    public $created_on;
	public $dis_order;

    public function exchangeArray($data)
    {
		$this->showroom_img_id	= (isset($data['showroom_img_id'])) ? $data['showroom_img_id'] : null;
		$this->showroom_id		= (isset($data['showroom_id'])) ? $data['showroom_id'] : null;
		$this->img_name			= (isset($data['img_name'])) ? $data['img_name'] : null;
		$this->img_title		= (isset($data['img_title'])) ? $data['img_title'] : null;
		$this->created_on		= (isset($data['created_on'])) ? $data['created_on'] : null;
		$this->dis_order		= (isset($data['dis_order'])) ? $data['dis_order'] : null;
    }
}