<?php
namespace Rctransfer\Form;
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
            'name' => 'agent_id',
            'type'  => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'Agent Id',
            ),
			'attributes' => array(
                'id' => 'agent_id',
				'value' => @$arrData['agent_id'],
				'required' => true,
            ),
        ));
		$this->add(array(
            'name' => 'agent_number',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Agent ID',
            ),
			'attributes' => array(
                'id' => 'agent_number',
				'value' => (!empty($arrData['agent_number']))?$arrData['agent_number']:'AGN'.date("Ymd").rand(),
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'agent_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Agent Name',
            ),
			'attributes' => array(
                'id' => 'agent_name',
				'value' => @$arrData['agent_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'representative',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Representative',
            ),
			'attributes' => array(
                'id' => 'representative',
				'value' => @$arrData['representative'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'agent_email',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email',
            ),
			'attributes' => array(
                'id' => 'agent_email',
				'value' => @$arrData['agent_email'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'agent_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile',
            ),
			'attributes' => array(
                'id' => 'agent_mobile',
				'value' => @$arrData['agent_mobile'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'agent_alt_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Alternate Mobile',
            ),
			'attributes' => array(
                'id' => 'agent_alt_mobile',
				'value' => @$arrData['agent_alt_mobile'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		
		$this->add(array(
            'name' => 'agent_pin_code',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'agent_pin_code',
            ),
			'attributes' => array(
                'id' => 'agent_pin_code',
				'value' => @$arrData['agent_pin_code'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'agent_address',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Address',
            ),
			'attributes' => array(
                'id' => 'agent_address',
				'value' => @$arrData['agent_address'],
				'rows' => "3",
				"cols" => "100",
				'required' => true,
				'class' => "basic-car-detail-textarea-small add-description",
            ),
        ));
		
		$this->add(array(
            'name' => 'agent_state',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
				'value_options' => $this->getStateList(),
            ),
			'attributes' => array(
                'id' => 'agent_state',
				'value' => @$arrData['agent_state'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'agent_city',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
				'value_options' => $this->getCityList(@$arrData['customer_state'],@$arrData['customer_city']),
            ),
			'attributes' => array(
                'id' => 'agent_city',
				'value' => @$arrData['agent_city'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
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