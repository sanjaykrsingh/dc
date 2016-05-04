<?php
namespace Showroom\Form;
// File: SaveImageForm.php

use Zend\Form\Element;
use Zend\Form\Form;

class SaveImageForm extends Form
{
    public function __construct($name = null, $options = array(), $arrData = array())
    {
        parent::__construct($name, $options);
        $this->addElements($arrData);
    }

    public function addElements($arrData)
    {
		$file = new Element\File('image-file');
        $file->setLabel('Upload Image')
             ->setAttribute('id', 'image-file')
			  ->setAttribute('multiple', 'multiple')
			->setAttribute('accept', 'image/*');
        $this->add($file);
		
		
    }
	
}
?>