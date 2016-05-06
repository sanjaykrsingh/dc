<?php

namespace Showroom\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Fixedvalues;

class showroomController extends AbstractActionController {
	
	
	protected $showroomMasterTable;
	protected $testimonialTable;
	protected $showroomImageTable;
	protected $stockMasterTable;
	protected $enquiryCustomerTable;
	protected $generalEnquiryTable;
	protected $settingsTable;
	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {
        return parent::onDispatch($e);
    }

	
    public function indexAction() {
	
		$config = $this->getServiceLocator()->get('Config');
		// Assign values to layout
		$arrWhere = array();
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster;
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		$id = $this->params('id');
		$arrWhere = array("showroom_id" => $id);
		$arrShowroom = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		
		$request = $this->getRequest();
		
		if($request->isPost())
		{
			$arrData = $request->getPost();
			$strWhere = "";
			if(isset($arrData->customer_mobile) && $arrData->customer_mobile != "")
			{
				$strWhere .= "mobile = ".$arrData->customer_mobile;
			}
			if(isset($arrData->customer_email) && $arrData->customer_email != "")
			{
				if($strWhere != "")	$strWhere .=" AND ";
				$strWhere .= "email = '".$arrData->customer_email."'";
			}
			
			$customer_id = $this->getEnquiryCustomerTable()->getCustomerId($strWhere);
			if($customer_id == 0)
			{
				$arrInsertData = array("email"   => @$arrData->customer_email,
										"mobile" => @$arrData->customer_mobile,
										"name" => @$arrData->customer_name);
				$customer_id = $this->getEnquiryCustomerTable()->insertEnquiryCustomer($arrInsertData);
			}
			$arrGeneral = array("customer_id" => $customer_id,
								"customer_message" => @$arrData->message,
							  "status" => "New");
			$this->getGeneralEnquiryTable()->insertGeneralEnquiry($arrGeneral);
			
			// Send email and sms to manager
			$message = "Hi <br><br> 
						General Customer Name: ".@$arrData->customer_name."<br><br>".
						"General Customer Email: ".@$arrData->customer_email."<br><br>".
						"General Customer Mobile: ".@$arrData->customer_mobile."<br><br>".
						"Message: ".@$arrData->message."<br><br>";
			
			$message = "Message from customer ";
			if(@$arrData->customer_name != "")	$message .= @$arrData->customer_name." , ";
			if(@$arrData->customer_email != "")	$message .= @$arrData->customer_email." , ";
			if(@$arrData->customer_mobile != "")	$message .= @$arrData->customer_mobile." , ";
			if(@$arrData->message != "")	$message .= @$arrData->message." , ";
			
			$message = trim($message," , ");
			
			$subject = "General enquiry";
			$result = Fixedvalues::sendmail($message,$subject,$arrSettings[0]['settings_value']);
			Fixedvalues::sendSMS($message,$arrSettings[1]['settings_value']);
			// Send email and sms to customer
			if(isset($arrData->customer_mobile) && $arrData->customer_mobile != "")
			{
				$message = "Thank you for contacting us, we will get in touch with you soon.For assistance call ".$arrSettings[1]['settings_value']." Team DIRECT CARS. www.directcars.in";
				Fixedvalues::sendSMS($message,$arrData->customer_mobile);
			}
			if(isset($arrData->customer_email) && $arrData->customer_email != "")
			{
				$address = $arrShowroom[0]['showroom_name'].",".$arrShowroom[0]['address_line_1'].",".$arrShowroom[0]['land_mark'].",".$arrShowroom[0]['city'].",".$arrShowroom[0]['pin_code'];
				$subject = "Your query with Direct Cars";
				$message = "Dear Customer,
				<br><br>
				Thank you for contacting us, we will get in touch with you soon.
				<br><br>
				For assistance call ".$arrSettings[1]['settings_value']." ".$arrSettings[0]['settings_value']."
				<br><br>

				You can also visit our ".$arrShowroomMaster[0]['showroom_name']." showroom: ".$address."<br><br>
				<br><br>
				View Map: <a href='https://www.google.co.in/maps/place/DIRECT+CARS/@28.486347,77.052987,15z/data=!4m2!3m1!1s0x0:0xd42ff862ab16c092?sa=X&ei=u7obVeSfC4O6uASJ_oHoCg&ved=0CH0Q_BIwCw'>Click Here</a><br>
				<br><br>

				Best Regards,
				<br><br>
				TEAM DIRECT CARS
				<br><br>
				<img src='".$config['site_image_path']."logo.png'><br><br>
				<br><br>
				Visit us at: www.directcars.in
				<br><br>
				We are on: <a href='https://plus.google.com/+DIRECTCARSGurgaon/about' target='NEW'><img src='".$config['site_image_path']."google_icon.png' width='20px'></a>
						<a  href='https://www.facebook.com/directcars.in' target='NEW'><img src='".$config['site_image_path']."facebook_icon.png' width='20px'></a>";
				$result = Fixedvalues::sendmail($message,$subject,$arrData->customer_email);
				//echo $message; die;
			}
			
			$this->flashMessenger()->addMessage('Your Enquiry has been placed.');
			return $this->redirect()->toRoute('showroom',array("action"=>"index","id"=>$id,"name"=>strtolower(str_replace(" ","+",$arrShowroom[0]['showroom_name'])),"city"=>strtolower($arrShowroom[0]['city'])));
		}
		
		// Get google maps coords
		$address = $arrShowroom[0]['showroom_name'].",".$arrShowroom[0]['address_line_1'].",".$arrShowroom[0]['land_mark'].",".$arrShowroom[0]['city'].",".$arrShowroom[0]['pin_code'];
		//$address = "Plot no 21, sector 17, vashi, Navi Mumbai, 400703";
		$data_arr = Fixedvalues::geocode($address);

		$arrMeta = array('title' => 'Used Car Showroom - '.$arrShowroom[0]['showroom_name'].' - '.$arrShowroom[0]['address_line_1'].' - '.$arrShowroom[0]['city'].' - '.$arrShowroom[0]['pin_code'].' - '.$arrShowroom[0]['land_mark'].'',
						'description' => ' Direct Cars used car showroom at '.$arrShowroom[0]['locality'].' near '.$arrShowroom[0]['land_mark'].' in '.$arrShowroom[0]['city'].' pin code '.$arrShowroom[0]['pin_code'].'. Search for used cars near you. Our facilities include buying & selling of used cars, used car loans, car insurance, used car exchange and free Wi-Fi',
						'keywords' => $arrShowroom[0]['pin_code'].', used car dealers in '.$arrShowroom[0]['city'].','.$arrShowroom[0]['showroom_name']);
		
		$this->layout()->arrMeta = $arrMeta;
		
		$this->layout()->breadcrum = '<a href="/">Home</a> > Showroom > '.$arrShowroom[0]['showroom_name'].'';
		
		$arrShowroomImage = $this->getShowroomImageTable()->fetchCustomShoroomImageList($arrWhere);
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList();
		$arrStock = $this->getStockMasterTable()->getShowroomStock($id);
		
		return new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
            'arrTestimonial' => $arrTestimonial,
			'data_arr' => $data_arr,
			'arrShowroom' => $arrShowroom[0],
			'arrShowroomImage'=>$arrShowroomImage,
			'arrStock' =>$arrStock,
			'image_path' => $config['image_path'],
			'image_root_path' => $config['image_root_path'],
			));
	}
	
	public function getTestimonialTable()
	{
		if (!$this->testimonialTable) {
            $sm = $this->getServiceLocator();
            $this->testimonialTable = $sm->get('Home\Model\TestimonialTable');
        }
        return $this->testimonialTable;
	}
	
	public function getShowroomMasterTable()
	{
		if (!$this->showroomMasterTable) {
            $sm = $this->getServiceLocator();
            $this->showroomMasterTable = $sm->get('Showroom\Model\ShowroomMasterTable');
        }
        return $this->showroomMasterTable;
	}
	public function getShowroomImageTable()
	{
		if (!$this->showroomImageTable) {
            $sm = $this->getServiceLocator();
            $this->showroomImageTable = $sm->get('Showroom\Model\ShowroomImageTable');
        }
        return $this->showroomImageTable;
	}
	public function getStockMasterTable()
	{
		if (!$this->stockMasterTable) {
            $sm = $this->getServiceLocator();
            $this->stockMasterTable = $sm->get('Home\Model\StockMasterTable');
        }
        return $this->stockMasterTable;
	}
	
	public function getEnquiryCustomerTable()
	{
		if (!$this->enquiryCustomerTable) {
            $sm = $this->getServiceLocator();
            $this->enquiryCustomerTable = $sm->get('Usedcar\Model\EnquiryCustomerTable');
        }
        return $this->enquiryCustomerTable;
	}
	public function getGeneralEnquiryTable()
	{
		if (!$this->generalEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->generalEnquiryTable = $sm->get('Usedcar\Model\GeneralEnquiryTable');
        }
        return $this->generalEnquiryTable;
	}
	
	public function getSettingsTable()
	{
		if (!$this->settingsTable) {
            $sm = $this->getServiceLocator();
            $this->settingsTable = $sm->get('Home\Model\SettingsTable');
        }
        return $this->settingsTable;
	}
}
