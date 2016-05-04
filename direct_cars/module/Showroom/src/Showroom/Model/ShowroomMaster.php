<?php
namespace Showroom\Model;

class ShowroomMaster
{
	public $showroom_id;
    public $showroom_name;	
	public $address_line_1;
    public $address_line_2;	
	public $land_mark;
    public $locality;	
	public $city;
    public $state;	
	public $country;
    public $pin_code;	
	public $google_link;
    public $google_map_code;
	public $manager_name;	
	public $phone_1;
    public $phone_2;	
	public $email;
    public $secondary_email;	
	public $services;	
	public $description;
    public $virtual_tour_video;	
	public $showroom_timings_days;
	public $showroom_timings_start;
	public $showroom_timings_end;
	public $created_by;
    public $createed_on;

    public function exchangeArray($data)
    {
		$this->showroom_id		= (isset($data['showroom_id'])) ? $data['showroom_id'] : null;
		$this->showroom_name	= (isset($data['showroom_name'])) ? $data['showroom_name'] : null;
		$this->address_line_1		= (isset($data['address_line_1'])) ? $data['address_line_1'] : null;
		$this->address_line_2	= (isset($data['address_line_2'])) ? $data['address_line_2'] : null;
		$this->land_mark		= (isset($data['land_mark'])) ? $data['land_mark'] : null;
		$this->locality	= (isset($data['locality'])) ? $data['locality'] : null;
		$this->city		= (isset($data['city'])) ? $data['city'] : null;
		$this->state	= (isset($data['state'])) ? $data['state'] : null;
		$this->country		= (isset($data['country'])) ? $data['country'] : null;
		$this->pin_code	= (isset($data['pin_code'])) ? $data['pin_code'] : null;
		$this->google_link		= (isset($data['google_link'])) ? $data['google_link'] : null;
		$this->google_map_code	= (isset($data['google_map_code'])) ? $data['google_map_code'] : null;
		$this->manager_name		= (isset($data['manager_name'])) ? $data['manager_name'] : null;
		$this->phone_1		= (isset($data['phone_1'])) ? $data['phone_1'] : null;
		$this->phone_2	= (isset($data['phone_2'])) ? $data['phone_2'] : null;
		$this->email		= (isset($data['email'])) ? $data['email'] : null;
		$this->secondary_email	= (isset($data['secondary_email'])) ? $data['secondary_email'] : null;
		$this->services		= (isset($data['services'])) ? $data['services'] : null;
		$this->description	= (isset($data['description'])) ? $data['description'] : null;
		$this->virtual_tour_video	= (isset($data['virtual_tour_video'])) ? $data['virtual_tour_video'] : null;
		$this->showroom_timings_days	= (isset($data['showroom_timings_days'])) ? $data['showroom_timings_days'] : null;
		$this->showroom_timings_start	= (isset($data['showroom_timings_start'])) ? $data['showroom_timings_start'] : null;
		$this->showroom_timings_end	= (isset($data['showroom_timings_end'])) ? $data['showroom_timings_end'] : null;
		$this->created_by		= (isset($data['created_by'])) ? $data['created_by'] : null;
		$this->createed_on	= (isset($data['createed_on'])) ? $data['createed_on'] : null;
    }
}