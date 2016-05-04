<?php
namespace Customer\Model;

class CustomerMaster
{
    public $cust_id;
    public $customer_id;	
	public $customer_name;
	public $customer_initial;	
	public $customer_email;	
	public $customer_mobile;
	public $customer_alt_mobile;	
	public $customer_dob;
    public $customer_source;	
	public $customer_type;
	public $customer_address;	
	public $customer_state;	
	public $customer_city;
	public $customer_pin;
	public $customer_contact;	
	public $customer_company;	
	public $customer_status;
	public $created_date;
	
    public function exchangeArray($data)
    {
		$this->cust_id		= (isset($data['cust_id'])) ? $data['cust_id'] : null;
		$this->customer_id	= (isset($data['customer_id'])) ? $data['customer_id'] : null;
		$this->customer_name	= (isset($data['customer_name'])) ? $data['customer_name'] : null;
		$this->customer_initial		= (isset($data['customer_initial'])) ? $data['customer_initial'] : null;
		$this->customer_email	= (isset($data['customer_email'])) ? $data['customer_email'] : null;
		$this->customer_mobile		= (isset($data['customer_mobile'])) ? $data['customer_mobile'] : null;
		$this->customer_alt_mobile		= (isset($data['customer_alt_mobile'])) ? $data['customer_alt_mobile'] : null;
		$this->customer_dob		= (isset($data['customer_dob'])) ? $data['customer_dob'] : null;
		$this->customer_source	= (isset($data['customer_source'])) ? $data['customer_source'] : null;
		$this->customer_type	= (isset($data['customer_type'])) ? $data['customer_type'] : null;
		$this->customer_address		= (isset($data['customer_address'])) ? $data['customer_address'] : null;
		$this->customer_state	= (isset($data['customer_state'])) ? $data['customer_state'] : null;
		$this->customer_city		= (isset($data['customer_city'])) ? $data['customer_city'] : null;
		$this->customer_pin		= (isset($data['customer_pin'])) ? $data['customer_pin'] : null;
		$this->customer_contact		= (isset($data['customer_contact'])) ? $data['customer_contact'] : null;
		$this->customer_company	= (isset($data['customer_company'])) ? $data['customer_company'] : null;
		$this->customer_status		= (isset($data['customer_status'])) ? $data['customer_status'] : null;
		$this->created_date		= (isset($data['created_date'])) ? $data['created_date'] : null;
    }
}