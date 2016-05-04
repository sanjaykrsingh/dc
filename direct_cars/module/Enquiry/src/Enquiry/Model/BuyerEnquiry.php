<?php
namespace Enquiry\Model;

class BuyerEnquiry
{
	public $enq_id;	
    public $customer_id;	
	public $stock_id;
    public $budget_range;	
    public $comments;
	public $enquiry_on;
	public $status;

    public function exchangeArray($data)
    {
		$this->enq_id		= (isset($data['enq_id'])) ? $data['enq_id'] : null;
		$this->customer_id	= (isset($data['customer_id'])) ? $data['customer_id'] : null;
		$this->stock_id		= (isset($data['stock_id'])) ? $data['stock_id'] : null;
		$this->budget_range	= (isset($data['budget_range'])) ? $data['budget_range'] : null;
		$this->comments		= (isset($data['comments'])) ? $data['comments'] : null;
		$this->enquiry_on	= (isset($data['enquiry_on'])) ? $data['enquiry_on'] : null;
		$this->status		= (isset($data['status'])) ? $data['status'] : null;
    }
}