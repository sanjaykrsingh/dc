<?php
namespace Enquiry\Form;
// File: SaveForm.php

use Zend\Form\Element;
use Zend\Form\Form;
use Showroom\Model\CitiesTable;

class SaveForm extends Form
{
    public function __construct($name = null, $options = array(),$arrData = array())
    {
        parent::__construct($name, $options);
        $this->addElements($name,$arrData);
		
    }

    public function addElements($name,$arrData)
    {
		$this->add(array(
            'name' => 'name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Customer Name',
            ),
			'attributes' => array(
                'id' => 'name',
				'value' => @$arrData['name'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'email',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Customer Email',
            ),
			'attributes' => array(
                'id' => 'email',
				'required' => true,
				'value' => @$arrData['email'],
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Customer Mobile',
            ),
			'attributes' => array(
                'id' => 'mobile',
				'value' => @$arrData['mobile'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'customer_address',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Customer Address',
            ),
			'attributes' => array(
                'id' => 'customer_address',
				'value' => @$arrData['customer_address'],
				'required' => true,
				'rows' => "2",
				"cols" => "70"
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
            'name' => 'customer_pin',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Pin Code',
            ),
			'attributes' => array(
                'id' => 'customer_pin',
				'value' => @$arrData['customer_pin'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'gender',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Gender',
				'value_options' => array("Male"=>"Male","Female"=>"Female"),
            ),
			'attributes' => array(
                'id' => 'gender',
				'value' => @$arrData['gender'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'profession',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Profession',
            ),
			'attributes' => array(
                'id' => 'profession',
				'value' => @$arrData['profession'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'annual_income',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Annual Income',
            ),
			'attributes' => array(
                'id' => 'annual_income',
				'value' => @$arrData['annual_income'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'comments',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Comments',
            ),
			'attributes' => array(
                'id' => 'comments',
				'value' => @$arrData['comments'],
				'required' => false,
				'rows' => "3",
				"cols" => "100"
            ),
        ));
		$this->add(array(
            'name' => 'status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
				'value_options' => $this->getStatus($name),
            ),
			'attributes' => array(
                'id' => 'status',
				'value' => @$arrData['status'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
    }
	
	public function getStatus($name)
	{
		if($name == "save-buyer")
			return array("New"=>"New","Hot"=>"Hot","Walked-in"=>"Walked-in","VNA"=>"VNA","Bought Car"=>"Bought Car","Closed"=>"Closed");
		else
			return array("New"=>"New","InProgress"=>"InProgress","Ignored"=>"Ignored","Closed"=>"Closed");
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
	public function getWeekDays()
	{
		return array("Monday"=>"Monday",
					 "Tuesday"=>"Tuesday",
					 "Wednesday"=>"Wednesday",
					 "Thrusday" => "Thrusday",
					 "Friday" => "Friday",
					 "Saturday" => "Saturday",
					 "Sunday"=> "Sunday");
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
}
?>