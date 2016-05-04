<?php
namespace Testimonial\Form;
// File: SaveForm.php

use Zend\Form\Element;
use Zend\Form\Form;

class SaveForm extends Form
{
    public function __construct($name = null, $options = array(), $arrData = array())
    {
        parent::__construct($name, $options);
        $this->addElements($arrData);
    }

    public function addElements($arrData)
    {
        // File Input
        $file = new Element\File('image-file');
        $file->setLabel('Client Image')
             ->setAttribute('id', 'image-file');
        $this->add($file);
		$this->add(array(
            'name' => 'client_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Client Name',
            ),
			'attributes' => array(
                'id' => 'client_name',
				'value' => @$arrData['client_name'],
				'required' => true,
            ),
        ));
		$this->add(array(
            'name' => 'description',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Testimonial',
            ),
			'attributes' => array(
                'id' => 'description',
				'value' => @$arrData['description'],
				'rows' => "3",
				"cols" => "150",
				'required' => true,
            ),
        ));
		
		$this->add(array(
            'name' => 'status',
			'attributes' => array(
				'id'	 	=> 		'status',
				'value'		=>		@$arrData['status'],
            ),
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'status',
                'value_options' => array("Publish"=>"Publish","Unpublish"=>"Unpublish"),
				
            ),
        ));
		
    }
	
}
?>