<?php
namespace Settings\Model;

class Settings
{
    public $settings_id;
    public $settings_name;	
	public $settings_value;
	public $type;	
	
    public function exchangeArray($data)
    {
		$this->settings_id		= (isset($data['settings_id'])) ? $data['settings_id'] : null;
		$this->settings_name	= (isset($data['settings_name'])) ? $data['settings_name'] : null;
		$this->settings_value	= (isset($data['settings_value'])) ? $data['settings_value'] : null;
		$this->type				= (isset($data['type'])) ? $data['type'] : null;
    }
}