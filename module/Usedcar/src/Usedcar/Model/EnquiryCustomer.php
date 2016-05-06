<?php
namespace Usedcar\Model;

class EnquiryCustomer
{
	public $customer_id;	
    public $name;	
	public $email;
    public $mobile;	
    public $customer_address;
	public $customer_city;
	public $customer_state;
	public $customer_pin;	
    public $gender;
	public $profession;
	public $annual_income;

    public function exchangeArray($data)
    {
		$this->customer_id		= (isset($data['customer_id'])) ? $data['customer_id'] : null;
		$this->name				= (isset($data['name'])) ? $data['name'] : null;
		$this->email			= (isset($data['email'])) ? $data['email'] : null;
		$this->mobile			= (isset($data['mobile'])) ? $data['mobile'] : null;
		$this->customer_address	= (isset($data['customer_address'])) ? $data['customer_address'] : null;
		$this->customer_city	= (isset($data['customer_city'])) ? $data['customer_city'] : null;
		$this->customer_state	= (isset($data['customer_state'])) ? $data['customer_state'] : null;
		$this->customer_pin		= (isset($data['customer_pin'])) ? $data['customer_pin'] : null;
		$this->gender			= (isset($data['gender'])) ? $data['gender'] : null;
		$this->profession		= (isset($data['profession'])) ? $data['profession'] : null;
		$this->annual_income	= (isset($data['annual_income'])) ? $data['annual_income'] : null;
    }
}