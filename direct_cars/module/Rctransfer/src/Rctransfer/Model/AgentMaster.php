<?php
namespace Rctransfer\Model;

class AgentMaster
{
    public $agent_id;
    public $agent_name;	
	public $agent_number;
	public $representative;	
	public $agent_mobile;	
	public $agent_alt_mobile;
	public $agent_email;	
	public $agent_address;
    public $agent_state;	
	public $agent_city;
	public $agent_pin_code;	
	public $created_date;	
	
    public function exchangeArray($data)
    {
		$this->agent_id		= (isset($data['agent_id'])) ? $data['agent_id'] : null;
		$this->agent_name	= (isset($data['agent_name'])) ? $data['agent_name'] : null;
		$this->agent_number	= (isset($data['agent_number'])) ? $data['agent_number'] : null;
		$this->representative		= (isset($data['representative'])) ? $data['representative'] : null;
		$this->agent_mobile	= (isset($data['agent_mobile'])) ? $data['agent_mobile'] : null;
		$this->agent_alt_mobile		= (isset($data['agent_alt_mobile'])) ? $data['agent_alt_mobile'] : null;
		$this->agent_email		= (isset($data['agent_email'])) ? $data['agent_email'] : null;
		$this->agent_address		= (isset($data['agent_address'])) ? $data['agent_address'] : null;
		$this->agent_state	= (isset($data['agent_state'])) ? $data['agent_state'] : null;
		$this->agent_city	= (isset($data['agent_city'])) ? $data['agent_city'] : null;
		$this->agent_pin_code		= (isset($data['agent_pin_code'])) ? $data['agent_pin_code'] : null;
		$this->created_date	= (isset($data['created_date'])) ? $data['created_date'] : null;
    }
}