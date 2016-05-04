<?php
namespace Showroom\Model;

class Cities
{
	public $city_id;
    public $city_name;	
	public $city_state;
    
    public function exchangeArray($data)
    {
		$this->city_id		= (isset($data['city_id'])) ? $data['city_id'] : null;
		$this->city_name	= (isset($data['city_name'])) ? $data['city_name'] : null;
		$this->city_state	= (isset($data['city_state'])) ? $data['city_state'] : null;
    }
}