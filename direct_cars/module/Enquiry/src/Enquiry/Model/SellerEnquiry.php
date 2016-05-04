<?php
namespace Enquiry\Model;

class SellerEnquiry
{
	public $enq_id;	
    public $customer_id;	
	public $make_year;
    public $make_id;
	public $model_id;
	public $kilometre;
	public $registration_place;
    public $comments;
	public $enquiry_on;
	public $status;

    public function exchangeArray($data)
    {
		$this->enq_id				= (isset($data['enq_id'])) ? $data['enq_id'] : null;
		$this->customer_id			= (isset($data['customer_id'])) ? $data['customer_id'] : null;
		$this->make_year			= (isset($data['make_year'])) ? $data['make_year'] : null;
		$this->make_id				= (isset($data['make_id'])) ? $data['make_id'] : null;
		$this->model_id				= (isset($data['model_id'])) ? $data['model_id'] : null;
		$this->kilometre			= (isset($data['kilometre'])) ? $data['kilometre'] : null;
		$this->registration_place	= (isset($data['registration_place'])) ? $data['registration_place'] : null;
		$this->comments				= (isset($data['comments'])) ? $data['comments'] : null;
		$this->enquiry_on			= (isset($data['enquiry_on'])) ? $data['enquiry_on'] : null;
		$this->status				= (isset($data['status'])) ? $data['status'] : null;
    }
}