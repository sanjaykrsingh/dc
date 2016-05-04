<?php
namespace Rctransfer\Form;
// File: SaveForm.php

use Zend\Form\Element;
use Zend\Form\Form;
use Showroom\Model\CitiesTable;
use Adminuser\Model\UserTable;

class RCTLoginForm extends Form
{
    public function __construct($name = null, $options = array(), $arrData = array())
    {
		
        parent::__construct($name, $options);
        $this->addElements($arrData);
    }

    public function addElements($arrData)
    {
		$this->add(array(
            'name' => 'stock_id',
            'type'  => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'stock_id',
            ),
			'attributes' => array(
                'id' => 'stock_id',
				'value' => @$arrData['stock_id'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'reg_no',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Reg. No',
            ),
			'attributes' => array(
                'id' => 'reg_no',
				'value' => @$arrData['reg_no'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'make_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'make_name',
            ),
			'attributes' => array(
                'id' => 'make_name',
				'value' => @$arrData['make_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'model_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'model_name',
            ),
			'attributes' => array(
                'id' => 'model_name',
				'value' => @$arrData['model_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'variant_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'variant_name',
            ),
			'attributes' => array(
                'id' => 'variant_name',
				'value' => @$arrData['variant_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'make_year',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'make_year',
            ),
			'attributes' => array(
                'id' => 'make_year',
				'value' => @$arrData['make_year'],
				'required' => true,
				'style' => "width: 50px;",
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'make_month',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'make_month',
            ),
			'attributes' => array(
                'id' => 'make_month',
				'value' => @$arrData['make_month'],
				'style' => "width: 50px;",
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'fuel_type',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'fuel_type',
            ),
			'attributes' => array(
                'id' => 'fuel_type',
				'value' => @$arrData['fuel_type'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'hypothecation',
            'type'  => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'hypothecation',
				'value_options' => array("1"=>"Yes","0"=>"No"),
            ),
			'attributes' => array(
                'id' => 'hypothecation',
				'value' => @$arrData['hypothecation'],
				'required' => true,
				'class' => "reg-no-label"
            ),
        ));
		
		$this->add(array(
            'name' => 'financier_name',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'financier_name',
				'value_options' => $this->getFinancerName(),
            ),
			'attributes' => array(
                'id' => 'financier_name',
				'value' => @$arrData['financier_name'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'purchase_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'purchase_date',
            ),
			'attributes' => array(
                'id' => 'purchase_date',
				'value' => @$arrData['purchase_date'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'delivery_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'delivery_date',
            ),
			'attributes' => array(
                'id' => 'delivery_date',
				'value' => @$arrData['delivery_date'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'delivered_by',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'delivered_by',
				'value_options' => $this->getuserList(),
            ),
			'attributes' => array(
                'id' => 'delivered_by',
				'value' => @$arrData['delivered_by'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'seller_id',
            'type'  => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'seller_id',
            ),
			'attributes' => array(
                'id' => 'seller_id',
				'value' => @$arrData['seller_id'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'seller_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'seller_mobile',
            ),
			'attributes' => array(
                'id' => 'seller_mobile',
				'value' => @$arrData['seller_mobile'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'seller_number',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'seller_number',
            ),
			'attributes' => array(
                'id' => 'seller_number',
				'value' => @$arrData['seller_number'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'seller_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'seller_name',
            ),
			'attributes' => array(
                'id' => 'seller_name',
				'value' => @$arrData['seller_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_id',
            'type'  => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'buyer_id',
            ),
			'attributes' => array(
                'id' => 'buyer_id',
				'value' => @$arrData['buyer_id'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'buyer_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_mobile',
            ),
			'attributes' => array(
                'id' => 'buyer_mobile',
				'value' => @$arrData['buyer_mobile'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_number',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_number',
            ),
			'attributes' => array(
                'id' => 'buyer_number',
				'value' => @$arrData['buyer_number'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_name',
            ),
			'attributes' => array(
                'id' => 'buyer_name',
				'value' => @$arrData['buyer_name'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_address',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'buyer_address',
            ),
			'attributes' => array(
                'id' => 'buyer_address',
				'value' => @$arrData['buyer_address'],
				'rows' => "3",
				"cols" => "50",
				'required' => true,
				'class' => "basic-car-detail-textarea-small add-description",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_city',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_city',
            ),
			'attributes' => array(
                'id' => 'buyer_city',
				'value' => @$arrData['buyer_city'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_state',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_state',
            ),
			'attributes' => array(
                'id' => 'buyer_state',
				'value' => @$arrData['buyer_state'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_pin',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_pin',
            ),
			'attributes' => array(
                'id' => 'buyer_pin',
				'value' => @$arrData['buyer_pin'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'transfer_by',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'transfer_by',
				'value_options' => array(""=>"Select","DC"=>"DC","Dealer"=>"Dealer","CNG Vendor"=>"CNG Vendor","Customer"=>"Customer"),
            ),
			'attributes' => array(
                'id' => 'transfer_by',
				'value' => @$arrData['transfer_by'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'transfer_type',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'transfer_type',
				'value_options' => array(""=>"Select","Normal"=>"Normal","NAP"=>"NAP","NOC"=>"NOC"),
            ),
			'attributes' => array(
                'id' => 'transfer_type',
				'value' => @$arrData['transfer_type'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'transfer_from',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'transfer_from',
				'value_options' => $this->getCityList(),
            ),
			'attributes' => array(
                'id' => 'transfer_from',
				'value' => @$arrData['transfer_from'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'transfer_to',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'transfer_to',
				'value_options' => $this->getCityList(),
            ),
			'attributes' => array(
                'id' => 'transfer_to',
				'value' => @$arrData['transfer_to'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$this->add(array(
            'name' => 'duplicate_rc',
            'type'  => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'duplicate_rc',
				'value_options' => array("Yes"=>"Yes","No"=>"No"),
            ),
			'attributes' => array(
                'id' => 'duplicate_rc',
				'value' => @isset($arrData['duplicate_rc'])?$arrData['duplicate_rc']:'No',
				'required' => false,
				'class' => "reg-no-label"
            ),
        ));
		$this->add(array(
            'name' => 'hpa',
            'type'  => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'hpa',
				'value_options' => array("Yes"=>"Yes","No"=>"No"),
            ),
			'attributes' => array(
                'id' => 'hpa',
				'value' => @isset($arrData['hpa'])?$arrData['hpa']:'No',
				'required' => false,
				'class' => "reg-no-label"
            ),
        ));
		$this->add(array(
            'name' => 'buyer_financiers_name',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'buyer_financiers_name',
				'value_options' =>$this->getFinancerName()
            ),
			'attributes' => array(
                'id' => 'buyer_financiers_name',
				'value' => @$arrData['buyer_financiers_name'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'cng_addition',
            'type'  => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'cng_addition',
				'value_options' => array("Yes"=>"Yes","No"=>"No"),
            ),
			'attributes' => array(
                'id' => 'cng_addition',
				'value' => @isset($arrData['cng_addition'])?$arrData['cng_addition']:'No',
				'required' => false,
				'class' => "reg-no-label"
            ),
        ));
		$this->add(array(
            'name' => 'cng_vendor',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'cng_vendor',
            ),
			'attributes' => array(
                'id' => 'cng_vendor',
				'value' => @$arrData['cng_vendor'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'agent_id',
            'type'  => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'agent_id',
            ),
			'attributes' => array(
                'id' => 'agent_id',
				'value' => @$arrData['agent_id'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'agent_mobile',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'agent_mobile',
            ),
			'attributes' => array(
                'id' => 'agent_mobile',
				'value' => @$arrData['agent_mobile'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'agent_number',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'agent_number',
            ),
			'attributes' => array(
                'id' => 'agent_number',
				'value' => @$arrData['agent_number'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'agent_name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'agent_name',
            ),
			'attributes' => array(
                'id' => 'agent_name',
				'value' => @$arrData['agent_name'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'file_status',
				'value_options' => array(""=>"Select","Ready for Log-in"=>"Ready for Log-in","Incomplete"=>"Incomplete","Logged-in"=>"Logged-in"),
            ),
			'attributes' => array(
                'id' => 'file_status',
				'value' => @$arrData['file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'login_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'login_date',
            ),
			'attributes' => array(
                'id' => 'login_date',
				'value' => @$arrData['login_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
				'disabled' => true,
            ),
        ));
		$this->add(array(
            'name' => 'login_remarks',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'login_remarks',
            ),
			'attributes' => array(
                'id' => 'login_remarks',
				'value' => @$arrData['login_remarks'],
				'rows' => "3",
				"cols" => "50",
				'required' => false,
				'class' => "basic-car-detail-textarea-small add-description",
            ),
        ));
		$this->add(array(
            'name' => 'transfer_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'transfer_status',
				'value_options' => array(""=>"Select","In Process"=>"In Process","Descrepency"=>"Descrepency","Resubmitted"=>"Resubmitted","Transferred"=>"Transferred"),
            ),
			'attributes' => array(
                'id' => 'transfer_status',
				'value' => @$arrData['transfer_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
				'disabled' => true,
            ),
        ));
		$this->add(array(
            'name' => 'transfer_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'transfer_date',
            ),
			'attributes' => array(
                'id' => 'transfer_date',
				'value' => @$arrData['transfer_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
				'disabled' => true,
            ),
        ));
		$this->add(array(
            'name' => 'discrepency_details',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'discrepency_details',
            ),
			'attributes' => array(
                'id' => 'discrepency_details',
				'value' => @$arrData['discrepency_details'],
				'rows' => "3",
				"cols" => "50",
				'required' => false,
				'class' => "basic-car-detail-textarea-small add-description",
            ),
        ));
		$this->add(array(
            'name' => 'resubmission_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'resubmission_date',
            ),
			'attributes' => array(
                'id' => 'resubmission_date',
				'value' => @$arrData['resubmission_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
				'disabled' => true,
            ),
        ));
		$this->add(array(
            'name' => 'resubmission_remarks',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'resubmission_remarks',
            ),
			'attributes' => array(
                'id' => 'resubmission_remarks',
				'value' => @$arrData['resubmission_remarks'],
				'rows' => "3",
				"cols" => "50",
				'required' => false,
				'class' => "basic-car-detail-textarea-small add-description",
            ),
        ));
		$this->add(array(
            'name' => 'original_rc_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'original_rc_file_status',
				'value_options' => array(""=>"Select","Received"=>"Received","Pending"=>"Pending","Not Available"=>"Not Available"),
            ),
			'attributes' => array(
                'id' => 'original_rc_file_status',
				'value' => @$arrData['original_rc_file_status'],
				'required' => true,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('original_rc_file');
			$file->setLabel('original_rc_file')
				 ->setAttribute('id', 'original_rc_file')
				  ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'original_rc_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'original_rc_date',
            ),
			'attributes' => array(
                'id' => 'original_rc_date',
				'value' => @$arrData['original_rc_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'insurance_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'insurance_file_status',
				'value_options' => array(""=>"Select","Received"=>"Received","Pending"=>"Pending","Expired"=>"Expired"),
            ),
			'attributes' => array(
                'id' => 'insurance_file_status',
				'value' => @$arrData['insurance_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('insurance_file');
			$file->setLabel('insurance_file')
				 ->setAttribute('id', 'insurance_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'insurance_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'insurance_date',
            ),
			'attributes' => array(
                'id' => 'insurance_date',
				'value' => @$arrData['insurance_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'tto_set_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'tto_set_file_status',
				'value_options' => array(""=>"Select","Received"=>"Received","Pending"=>"Pending"),
            ),
			'attributes' => array(
                'id' => 'tto_set_file_status',
				'value' => @$arrData['tto_set_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('tto_set_file');
			$file->setLabel('tto_set_file')
				 ->setAttribute('id', 'tto_set_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'tto_set_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'tto_set_date',
            ),
			'attributes' => array(
                'id' => 'tto_set_date',
				'value' => @$arrData['tto_set_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'form35_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'form35_file_status',
				'value_options' => array(""=>"Select","Pending"=>"Pending","Yes"=>"Yes","No"=>"No","Not Applicable"=>"Not Applicable"),
            ),
			'attributes' => array(
                'id' => 'form35_file_status',
				'value' => @$arrData['form35_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('form35_file');
			$file->setLabel('form35_file')
				 ->setAttribute('id', 'form35_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'form35_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'form35_date',
            ),
			'attributes' => array(
                'id' => 'form35_date',
				'value' => @$arrData['form35_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'affidavit_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'affidavit_file_status',
				'value_options' => array(""=>"Select","Received"=>"Received","Pending"=>"Pending"),
            ),
			'attributes' => array(
                'id' => 'affidavit_file_status',
				'value' => @$arrData['affidavit_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('affidavit_file');
			$file->setLabel('affidavit_file')
				 ->setAttribute('id', 'affidavit_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'affidavit_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'affidavit_date',
            ),
			'attributes' => array(
                'id' => 'affidavit_date',
				'value' => @$arrData['affidavit_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'letter_head_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'letter_head_file_status',
				'value_options' => array(""=>"Select","Received"=>"Received","Pending"=>"Pending","Not Applicable"=>"Not Applicable"),
            ),
			'attributes' => array(
                'id' => 'letter_head_file_status',
				'value' => @$arrData['letter_head_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('letter_head_file');
			$file->setLabel('letter_head_file')
				 ->setAttribute('id', 'letter_head_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'letter_head_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'letter_head_date',
            ),
			'attributes' => array(
                'id' => 'letter_head_date',
				'value' => @$arrData['letter_head_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'id_proof_1_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'id_proof_1_file_status',
				'value_options' => $this->getIdProof(),
            ),
			'attributes' => array(
                'id' => 'id_proof_1_file_status',
				'value' => @$arrData['id_proof_1_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('id_proof_1_file');
			$file->setLabel('id_proof_1_file')
				 ->setAttribute('id', 'id_proof_1_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'id_proof_1_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'id_proof_1_date',
            ),
			'attributes' => array(
                'id' => 'id_proof_1_date',
				'value' => @$arrData['id_proof_1_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'id_proof_2_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'id_proof_2_file_status',
				'value_options' => $this->getIdProof(),
            ),
			'attributes' => array(
                'id' => 'id_proof_2_file_status',
				'value' => @$arrData['id_proof_2_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('id_proof_2_file');
			$file->setLabel('id_proof_2_file')
				 ->setAttribute('id', 'id_proof_2_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'id_proof_2_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'id_proof_2_date',
            ),
			'attributes' => array(
                'id' => 'id_proof_2_date',
				'value' => @$arrData['id_proof_2_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'address_proof_1_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'address_proof_1_file_status',
				'value_options' => $this->getAddressProof(),
            ),
			'attributes' => array(
                'id' => 'address_proof_1_file_status',
				'value' => @$arrData['address_proof_1_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('address_proof_1_file');
			$file->setLabel('address_proof_1_file')
				 ->setAttribute('id', 'address_proof_1_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'address_proof_1_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'address_proof_1_date',
            ),
			'attributes' => array(
                'id' => 'address_proof_1_date',
				'value' => @$arrData['address_proof_1_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'address_proof_2_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'address_proof_2_file_status',
				'value_options' => $this->getAddressProof(),
            ),
			'attributes' => array(
                'id' => 'address_proof_2_file_status',
				'value' => @$arrData['address_proof_2_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('address_proof_2_file');
			$file->setLabel('address_proof_2_file')
				 ->setAttribute('id', 'address_proof_2_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'address_proof_2_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'address_proof_2_date',
            ),
			'attributes' => array(
                'id' => 'address_proof_2_date',
				'value' => @$arrData['address_proof_2_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		// buyer id & address proof....
		$this->add(array(
            'name' => 'buyer_id_proof_1_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'buyer_id_proof_1_file_status',
				'value_options' => $this->getIdProof(),
            ),
			'attributes' => array(
                'id' => 'buyer_id_proof_1_file_status',
				'value' => @$arrData['buyer_id_proof_1_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('buyer_id_proof_1_file');
			$file->setLabel('buyer_id_proof_1_file')
				 ->setAttribute('id', 'buyer_id_proof_1_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'buyer_id_proof_1_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_id_proof_1_date',
            ),
			'attributes' => array(
                'id' => 'buyer_id_proof_1_date',
				'value' => @$arrData['buyer_id_proof_1_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_id_proof_2_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'buyer_id_proof_2_file_status',
				'value_options' => $this->getIdProof(),
            ),
			'attributes' => array(
                'id' => 'buyer_id_proof_2_file_status',
				'value' => @$arrData['buyer_id_proof_2_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('buyer_id_proof_2_file');
			$file->setLabel('buyer_id_proof_2_file')
				 ->setAttribute('id', 'buyer_id_proof_2_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'buyer_id_proof_2_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_id_proof_2_date',
            ),
			'attributes' => array(
                'id' => 'buyer_id_proof_2_date',
				'value' => @$arrData['buyer_id_proof_2_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_address_proof_1_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'buyer_address_proof_1_file_status',
				'value_options' => $this->getAddressProof(),
            ),
			'attributes' => array(
                'id' => 'buyer_address_proof_1_file_status',
				'value' => @$arrData['buyer_address_proof_1_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('buyer_address_proof_1_file');
			$file->setLabel('buyer_address_proof_1_file')
				 ->setAttribute('id', 'buyer_address_proof_1_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'buyer_address_proof_1_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_address_proof_1_date',
            ),
			'attributes' => array(
                'id' => 'buyer_address_proof_1_date',
				'value' => @$arrData['buyer_address_proof_1_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'buyer_address_proof_2_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'buyer_address_proof_2_file_status',
				'value_options' => $this->getAddressProof(),
            ),
			'attributes' => array(
                'id' => 'buyer_address_proof_2_file_status',
				'value' => @$arrData['buyer_address_proof_2_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('buyer_address_proof_2_file');
			$file->setLabel('buyer_address_proof_2_file')
				 ->setAttribute('id', 'buyer_address_proof_2_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'buyer_address_proof_2_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'buyer_address_proof_2_date',
            ),
			'attributes' => array(
                'id' => 'buyer_address_proof_2_date',
				'value' => @$arrData['buyer_address_proof_2_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$this->add(array(
            'name' => 'puc_file_status',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'puc_file_status',
				'value_options' => array(""=>"Select","Received"=>"Received","Pending"=>"Pending"),
            ),
			'attributes' => array(
                'id' => 'puc_file_status',
				'value' => @$arrData['puc_file_status'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		$file = new Element\File('puc_file');
			$file->setLabel('puc_file')
				 ->setAttribute('id', 'puc_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
		$this->add(array(
            'name' => 'puc_date',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'puc_date',
            ),
			'attributes' => array(
                'id' => 'puc_date',
				'value' => @$arrData['puc_date'],
				'required' => false,
				'class' => "basic-car-detail-textbox",
            ),
        ));
		
		$file = new Element\File('transfer_file');
			$file->setLabel('transfer_file')
				 ->setAttribute('id', 'transfer_file')
				 ->setAttribute('class', 'upload');
			$this->add($file);
    }
	
	public function getCityList($state="",$city="")
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
	
	public function getuserList()
	{
		$objUsers = new UserTable();
        $arrUserData = $objUsers->fetchUserList(array("active"=>1));
		$arrUser = array("" => "Select");
		foreach ($arrUserData as $adminuser)
		{
			$arrUser[$adminuser['user_id']] = $adminuser['first_name']." ".$adminuser['last_name']."(".$adminuser['email'].")";
		}
		return $arrUser;
	}
	
	public function getIdProof()
	{
		return array(""=>"Select","Pending"=>"Pending","Pancard"=>"Pancard","Voter ID"=>"Voter ID","Passport"=>"Passport","Driving License"=>"Driving License","Aadhar"=>"Aadhar");
	}
	
	public function getAddressProof()
	{
		return array(""=>"Select","Pending"=>"Pending","Voter ID"=>"Voter ID","Passport"=>"Passport","Aadhar"=>"Aadhar","Landline Bill"=>"Landline Bill","Electricity Bill"=>"Electricity Bill");
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
	
	public function getFinancerName()
	{
		return array(""=>"Select",
					"HDFC BANK"=>"HDFC BANK",
					"KOTAK MAHINDRA"=>"KOTAK MAHINDRA",
					"ICICI BANK"=>"ICICI BANK",
					"ARVAL INDIA"=>"ARVAL INDIA",
					"ALD AUTOMOTIVE"=>"ALD AUTOMOTIVE",
					"LEASEPLAN"=>"LEASEPLAN",
					"AXIS BANK"=>"AXIS BANK",
					"MAHINDRA FINANCE"=>"MAHINDRA FINANCE",
					"TATA CAPITAL"=>"TATA CAPITAL",
					"AU FINANCERS"=>"AU FINANCERS",
					"CITIBANK"=>"CITIBANK",
					"STATE BANK OF INDIA"=>"STATE BANK OF INDIA",
					"STATE BANK OF PATIALA"=>"STATE BANK OF PATIALA",
					"STATE BANK OF BIKANER & JAIPUR"=>"STATE BANK OF BIKANER & JAIPUR",
					"STATE BANK OF MAHARASHTRA"=>"STATE BANK OF MAHARASHTRA",
					"STATE BANK OF TRAVANCORE"=>"STATE BANK OF TRAVANCORE",
					"TRANSLEAZE"=>"TRANSLEAZE",
					"ROYAL BANK OF SCOTLAND"=>"ROYAL BANK OF SCOTLAND",
					"STANDARD CHARTED BANK"=>"STANDARD CHARTED BANK",
					"MAGMA"=>"MAGMA",
					"RELIANCE CAPITAL"=>"RELIANCE CAPITAL",
					"SHRIRAM"=>"SHRIRAM",
					"GE MONEY"=>"GE MONEY",
					"GE CAPITAL"=>"GE CAPITAL",
					"other"=>"OTHER");
	}
	
}
?>