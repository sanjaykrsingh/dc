<?php
namespace Inventory\Model;

class StockMaster
{
    public $stock_id;
    public $mmv_id;	
	public $showroom_id;
    public $color;	
	public $fuel_type;
    public $owner;	
	public $expected_price;
	public $make_month;
    public $make_year;	
	public $kilometre;
    public $listing_date;	
	public $car_id;
    public $certified_by;	
	public $registration_place;
    public $registration_no;	
	public $special_note;
    public $transmission_type;	
	public $special_offer;
    public $car_insurance;	
	public $insurance_expiry;
    public $free_services;	
	public $certified_warranty_details;
    public $service_history;	
	public $on_road_assistance;
    public $meta_description;	
	public $meta_keywords;
    public $car_status;	
	public $car_hignlights;	
	public $car_video;
    public $hot_deal;	
	public $hoat_deal_price;
    public $hoat_deal_offer;	
	public $created_by;
    public $createed_on;

    public function exchangeArray($data)
    {
		$this->stock_id		= (isset($data['stock_id'])) ? $data['stock_id'] : null;
		$this->mmv_id	= (isset($data['mmv_id'])) ? $data['mmv_id'] : null;
		$this->showroom_id		= (isset($data['showroom_id'])) ? $data['showroom_id'] : null;
		$this->color	= (isset($data['color'])) ? $data['color'] : null;
		$this->fuel_type		= (isset($data['fuel_type'])) ? $data['fuel_type'] : null;
		$this->owner	= (isset($data['owner'])) ? $data['owner'] : null;
		$this->expected_price		= (isset($data['expected_price'])) ? $data['expected_price'] : null;
		$this->make_month	= (isset($data['make_month'])) ? $data['make_month'] : null;
		$this->make_year	= (isset($data['make_year'])) ? $data['make_year'] : null;
		$this->kilometre		= (isset($data['kilometre'])) ? $data['kilometre'] : null;
		$this->listing_date	= (isset($data['listing_date'])) ? $data['listing_date'] : null;
		$this->car_id		= (isset($data['car_id'])) ? $data['car_id'] : null;
		$this->certified_by	= (isset($data['certified_by'])) ? $data['certified_by'] : null;
		$this->registration_place		= (isset($data['registration_place'])) ? $data['registration_place'] : null;
		$this->registration_no	= (isset($data['registration_no'])) ? $data['registration_no'] : null;
		$this->special_note		= (isset($data['special_note'])) ? $data['special_note'] : null;
		$this->transmission_type	= (isset($data['transmission_type'])) ? $data['transmission_type'] : null;
		$this->special_offer		= (isset($data['special_offer'])) ? $data['special_offer'] : null;
		$this->car_insurance	= (isset($data['car_insurance'])) ? $data['car_insurance'] : null;
		$this->insurance_expiry		= (isset($data['insurance_expiry'])) ? $data['insurance_expiry'] : null;		
		$this->free_services		= (isset($data['free_services'])) ? $data['free_services'] : null;
		$this->certified_warranty_details	= (isset($data['certified_warranty_details'])) ? $data['certified_warranty_details'] : null;
		$this->service_history		= (isset($data['service_history'])) ? $data['service_history'] : null;
		$this->on_road_assistance	= (isset($data['on_road_assistance'])) ? $data['on_road_assistance'] : null;
		$this->meta_description		= (isset($data['meta_description'])) ? $data['meta_description'] : null;
		$this->meta_keywords	= (isset($data['meta_keywords'])) ? $data['meta_keywords'] : null;
		$this->car_status		= (isset($data['car_status'])) ? $data['car_status'] : null;
		$this->car_hignlights	= (isset($data['car_hignlights'])) ? $data['car_hignlights'] : null;
		$this->car_video		= (isset($data['car_video'])) ? $data['car_video'] : null;
		$this->hot_deal	= (isset($data['hot_deal'])) ? $data['hot_deal'] : null;
		$this->hoat_deal_price		= (isset($data['hoat_deal_price'])) ? $data['hoat_deal_price'] : null;
		$this->hoat_deal_offer	= (isset($data['hoat_deal_offer'])) ? $data['hoat_deal_offer'] : null;
		$this->created_by		= (isset($data['created_by'])) ? $data['created_by'] : null;
		$this->createed_on	= (isset($data['createed_on'])) ? $data['createed_on'] : null;
    }
}