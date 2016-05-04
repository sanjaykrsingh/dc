<?php
namespace Showroom\Form;
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
            'name' => 'showroom_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Showroom Name',
            ),
			'attributes' => array(
                'id' => 'showroom_name',
				'value' => @$arrData['showroom_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'address_line_1',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'address 1',
            ),
			'attributes' => array(
                'id' => 'address_line_1',
				'value' => @$arrData['address_line_1'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'address_line_2',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'address 2',
            ),
			'attributes' => array(
                'id' => 'address_line_2',
				'value' => @$arrData['address_line_2'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'land_mark',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Land Mark',
            ),
			'attributes' => array(
                'id' => 'land_mark',
				'value' => @$arrData['land_mark'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'locality',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Locality',
            ),
			'attributes' => array(
                'id' => 'locality',
				'value' => @$arrData['locality'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'country',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Country',
            ),
			'attributes' => array(
                'id' => 'country',
				'value' => isset($arrData['country'])?$arrData['country']:'india',
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'state',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
				'value_options' => $this->getStateList(),
            ),
			'attributes' => array(
                'id' => 'state',
				'value' => @$arrData['state'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'city',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
            ),
			'attributes' => array(
                'id' => 'city',
				'value' => @$arrData['city'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'pin_code',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Pin Code',
            ),
			'attributes' => array(
                'id' => 'pin_code',
				'value' => @$arrData['pin_code'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'google_link',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Google Link',
            ),
			'attributes' => array(
                'id' => 'google_link',
				'value' => @$arrData['google_link'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'google_map_code',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'google Map Code',
            ),
			'attributes' => array(
                'id' => 'google_map_code',
				'value' => @$arrData['google_map_code'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'phone_1',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Primary Phone',
            ),
			'attributes' => array(
                'id' => 'phone_1',
				'value' => @$arrData['phone_1'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'phone_2',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Secondary Phone',
            ),
			'attributes' => array(
                'id' => 'phone_2',
				'value' => @$arrData['phone_2'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'email',
            'type'  => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'Primary Email',
            ),
			'attributes' => array(
                'id' => 'email',
				'value' => @$arrData['email'],
				'required' => true,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'secondary_email',
            'type'  => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'Secondary Email',
            ),
			'attributes' => array(
                'id' => 'secondary_email',
				'value' => @$arrData['secondary_email'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'services',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Services',
            ),
			'attributes' => array(
                'id' => 'services',
				'value' => @$arrData['services'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
		
		$this->add(array(
            'name' => 'description',
            'type'  => 'Zend\Form\Element\TextArea',
            'options' => array(
                'label' => 'Description',
            ),
			'attributes' => array(
                'id' => 'description',
				'value' => @$arrData['description'],
				'required' => true,
				'rows' => "3",
				'cols' => "150",
            ),
        ));
		
		$file = new Element\File('tour-file');
        $file->setLabel('Tour Vedio')
             ->setAttribute('id', 'tour-file');
        $this->add($file);
		
		$this->add(array(
            'name' => 'showroom_timings',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Showroom Timings',
            ),
			'attributes' => array(
                'id' => 'showroom_timings',
				'value' => @$arrData['showroom_timings'],
				'required' => false,
				'class' => "basic-car-detail-textbox"
            ),
        ));
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