<?php
namespace Customer\Form;
// File: SaveForm.php

use Zend\Form\Element;
use Zend\Form\Form;
use Showroom\Model\CitiesTable;

class SaveForm extends Form
{
    public function __construct($name = null, $options = array(), $arrData = array())
    {
        parent::__construct($name, $options);
        $this->addElements($arrData);
    }

    public function addElements($arrData)
    {
		$this->add(array(
            'name' => 'cust_id',
            'type'  => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'Cust Id',
            ),
			'attributes' => array(
                'id' => 'cust_id',
				'value' => @$arrData['cust_id'],
				'required' => true,
            ),
        ));
		$this->add(array(
            'name' => 'customer_id',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'customer Id',
            ),
			'attributes' => array(
                'id' => 'customer_id',
				'value' => (!empty($arrData['customer_id']))?$arrData['customer_id']:'CUS'.date("Ymd").rand(),
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'customer_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name',
            ),
			'attributes' => array(
                'id' => 'customer_name',
				'value' => @$arrData['customer_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_email',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email',
            ),
			'attributes' => array(
                'id' => 'customer_email',
				'value' => @$arrData['customer_email'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile',
            ),
			'attributes' => array(
                'id' => 'customer_mobile',
				'value' => @$arrData['customer_mobile'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_alt_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Alternate Mobile',
            ),
			'attributes' => array(
                'id' => 'customer_alt_mobile',
				'value' => @$arrData['customer_alt_mobile'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_dob',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'D.O.B.',
            ),
			'attributes' => array(
                'id' => 'customer_dob',
				'value' => @$arrData['customer_dob'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		
		$this->add(array(
            'name' => 'customer_contact',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Contact',
            ),
			'attributes' => array(
                'id' => 'customer_contact',
				'value' => @$arrData['customer_contact'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_company',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Company',
            ),
			'attributes' => array(
                'id' => 'customer_company',
				'value' => @$arrData['customer_company'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_pin',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'customer_pin',
            ),
			'attributes' => array(
                'id' => 'customer_pin',
				'value' => @$arrData['customer_pin'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_address',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Address',
            ),
			'attributes' => array(
                'id' => 'customer_address',
				'value' => @$arrData['customer_address'],
				'rows' => "3",
				"cols" => "100",
				'required' => true,
				'class' => "basic-car-detail-textarea-small add-description",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_state',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Customer State',
				'value_options' => $this->getStateList(),
            ),
			'attributes' => array(
                'id' => 'customer_state',
				'value' => @$arrData['customer_state'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_city',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Customer City',
				'value_options' => $this->getCityList(@$arrData['customer_state'],@$arrData['customer_city']),
            ),
			'attributes' => array(
                'id' => 'customer_city',
				'value' => @$arrData['customer_city'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'other_customer_source',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'other_customer_source',
            ),
			'attributes' => array(
                'id' => 'other_customer_source',
				'value' => @$arrData['customer_source'],
				'required' => false,
				'style' => "display:none",
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_source',
			'attributes' => array(
				'id'	 	=> 		'customer_source',
				'value'		=>		@$arrData['customer_source'],
				'class' => "basic-car-detail-textbox",
            ),
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Source',
                'value_options' => array(""=> "Select",
										 "Walkin"=>"Walkin",
										 "Tele"=>"Tele",
										 "DC Website"=>"DC Website",
										 "Reference" => "Reference",
										 "Carwale"=> "Carwale",
										 "Gaadi" => "Gaadi",
										 "JustDial" => "JustDial",
										 "CommnAgent" => "CommnAgent",
										 "Other" => "Other"),
				
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_type',
			'attributes' => array(
				'id'	 	=> 		'customer_type',
				'value'		=>		@$arrData['customer_type'],
				'class' => "basic-car-detail-textbox",
				'required' => true,
            ),
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(""=> "Select",
										 "Retail"=>"Retail",
										 "Corporate"=>"Corporate",
										 "Dealer"=>"Dealer"),
				
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_initial',
			'attributes' => array(
				'id'	 	=> 		'customer_initial',
				'value'		=>		@$arrData['customer_initial'],
				'class' => "",
            ),
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => '',
                'value_options' => array("Mr"=> "Mr.",
										 "Mrs"=>"Mrs.",
										 "Ms"=>"Ms.",
										 "Dr"=>"Dr."),
				
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_status',
			'attributes' => array(
				'id'	 	=> 		'customer_status',
				'value'		=>		@$arrData['customer_status'],
				'class' => "basic-car-detail-textbox",
            ),
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'status',
                'value_options' => array("Publish"=>"Publish","Unpublish"=>"Unpublish"),
				
            ),
        ));
		
    }
	
	public function getCityList($state,$city)
	{
		$objCities = new CitiesTable();
        $resCities = $objCities->getCitiesList($state);
		$arrState = array("" => "Select");
		foreach($resCities as $cities)
		{
			$city_name=trim($cities['city_name']);
			$arrState[$cities['city_name']]  = $city_name;
		}
		return $arrState;
	}
	
	public function getStateList()
	{
		$objCities = new CitiesTable();
        $resCities = $objCities->fetchStateList();
		$arrState = array("" => "Select");
		foreach($resCities as $cities)
		{
			$city_name=trim($cities['city_state']);
			$arrState[$cities['city_state']]  = $city_name;
		}
		return $arrState;
	}
	
}
?>