<?php
namespace Enquiry\Model;

class GeneralEnquiry
{
	public $enq_id;	
    public $customer_id;	
	public $customer_message;
    public $comments;
	public $enquiry_on;
	public $status;

    public function exchangeArray($data)
    {
		$this->enq_id			= (isset($data['enq_id'])) ? $data['enq_id'] : null;
		$this->customer_id		= (isset($data['customer_id'])) ? $data['customer_id'] : null;
		$this->customer_message	= (isset($data['customer_message'])) ? $data['customer_message'] : null;
		$this->comments			= (isset($data['comments'])) ? $data['comments'] : null;
		$this->enquiry_on		= (isset($data['enquiry_on'])) ? $data['enquiry_on'] : null;
		$this->status			= (isset($data['status'])) ? $data['status'] : null;
    }
}