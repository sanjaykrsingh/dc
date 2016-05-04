<?php
namespace Testimonial\Model;

class Testimonial
{
    public $testimonial_id;
    public $client_name;	
	public $client_image;
	public $description;	
	public $created_on;	
	public $modified_on;
	public $status;		

    public function exchangeArray($data)
    {
		$this->testimonial_id		= (isset($data['testimonial_id'])) ? $data['testimonial_id'] : null;
		$this->client_name	= (isset($data['client_name'])) ? $data['client_name'] : null;
		$this->client_image	= (isset($data['client_image'])) ? $data['client_image'] : null;
		$this->description		= (isset($data['description'])) ? $data['description'] : null;
		$this->created_on	= (isset($data['created_on'])) ? $data['created_on'] : null;
		$this->modified_on		= (isset($data['modified_on'])) ? $data['modified_on'] : null;
		$this->status		= (isset($data['status'])) ? $data['status'] : null;
    }
}