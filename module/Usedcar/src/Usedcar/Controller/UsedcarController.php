<?php

namespace Usedcar\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Fixedvalues;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Session\Container as SessionContainer;

class UsedcarController extends AbstractActionController {

	protected $testimonialTable;
	protected $stockMasterTable;
	protected $enquiryCustomerTable;
	protected $buyerEnquiryTable;
	protected $sellerEnquiryTable;
	protected $mmvDetailTable;
	protected $showroomMasterTable;
	protected $settingsTable;

	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {
        return parent::onDispatch($e);
    }

	public function indexAction() {
		$this->session = new SessionContainer('post_search');
		$this->session->arrData = array();
		return $this->redirect()->toRoute('usedcar',array('action' => "search"));
	}
	
    public function searchAction() {
		$this->session = new SessionContainer('post_search');
		
		// Assign values to layout
		$arrWhere = array();
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster;
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		$strWhere = "";
		$strOrder = "";
		$arrData = array();
		$request = $this->getRequest();
		
		$keyword = $this->params('keyword');
		
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$search_type =  "";
		if($request->isPost() || isset($keyword))
		{
			$arrData = $request->getPost(); 
			if(isset($keyword))
			{
				$arrSearchMake = explode("-",$keyword);
				
				if(count($arrSearchMake) == 2) {
						$arrSearchMake[0] = ucwords(str_replace("+"," ",$arrSearchMake[0]));
						$arrData->make = $arrSearchMake[0];
						$search_type = "make";
				} else if(count($arrSearchMake) == 3) {
					if($arrSearchMake[1] == 'style')
					{
						$arrSearchMake[0] = ucwords(str_replace("+"," ",$arrSearchMake[0]));
						$arrData->body_style = $arrSearchMake[0]; 
						$search_type = "body";
					}
					else
					{
						$arrSearchMake[0] = ucwords(str_replace("+"," ",$arrSearchMake[0]));
						$arrSearchMake[1] = ucwords(str_replace("+"," ",$arrSearchMake[1]));
						$arrData->make = $arrSearchMake[0]; 
						$arrData->model = $arrSearchMake[1]; 
						$search_type = "model";
					}
				}
				else 
				{
					$arrSearchStyle = explode("rs.",$keyword);
					if($arrSearchStyle[1] == '0-100000') $arrData->budget = "0-1";
					else if($arrSearchStyle[1] == '0-100000') $arrData->budget = "0-1";
					else if($arrSearchStyle[1] == '100000-200000') $arrData->budget = "1-2";
					else if($arrSearchStyle[1] == '200000-300000') $arrData->budget = "2-3";
					else if($arrSearchStyle[1] == '300000-500000') $arrData->budget = "3-5";
					else if($arrSearchStyle[1] == '500000-1000000') $arrData->budget = "5-10";
					else if($arrSearchStyle[1] == '1000000') $arrData->budget = "10-0";
					else $arrData->budget = "";
					$search_type = "budget";
				}
			}
			$this->session->arrData = $arrData;
		}
		else
		{
			$setpage = $this->params()->fromRoute('page');
			if(isset($setpage))	$arrData = $this->session->arrData;
			else $this->session->arrData = $arrData;
		}
		if(!empty($arrData))	$strWhere = $this->createWhereCond($arrData);
			
		if(@$arrData->sort_by != "")
		{
			$strOrder = $arrData->sort_by;
		}
		else
		{
			$strOrder = "hot_deal desc, listing_date desc";
		}
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList();
		$arrBodyStyle = $this->getStockMasterTable()->getStockBodyStyle();
		$arrMake = $this->getStockMasterTable()->getStockMake();
		$arrStockList = $this->getStockMasterTable()->getStockList($strWhere,$strOrder);
		$totalCount = $arrStockList->count();
		$itemsPerPage = 25;
		$paginator = new Paginator(new paginatorIterator($arrStockList));

          $paginator->setCurrentPageNumber($arrParam['page'])
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);
		
		$current = $arrParam['page'];
		$previous = (int) ($arrParam['page'] == 1 ) ? null : $arrParam['page'] - 1;
		$next = (int) ($arrParam['page'] == $paginator->count()) ? null : $arrParam['page'] + 1;
		
		$range = (int)($arrParam['page'] / 7) ; 
		$lower_bound = (($range)*7) + ($range - 1);
		$upper_bound = (($range)*7) + 7;
		
		$arrModel = $this->getStockMasterTable()->getStockModel(@$arrData->make_name);
		$arrFuel = Fixedvalues::get_fuel_type();
		
		$title_page_ext = "";
		$pageno = $this->params()->fromRoute('page');
		if(isset($pageno))
		{
				$title_page_ext = " Page[".$this->params()->fromRoute('page')."] ";
		}
		
		if($search_type == "make")
		{
			$arrMeta = array('title' => 'Used '.$arrData->make.' cars in Gurgaon, Delhi/NCR-'.$totalCount.' Certified '.$arrData->make.' cars for sale'.$title_page_ext.' - Direct Cars',
							'description' => date('d M y').'-Choose from '.$totalCount.' Used '.$arrData->make.' Cars in Gurgaon, Delhi/NCR. Find wide range of Quality Certified Second Hand '.$arrData->make.' Cars for Sale. Pre-owned cars from Direct Cars are non-accidental, genuine mileage, certified and come with warranty.',
							'keywords' => 'Certified Used '.$arrData->make.' Cars in '.$arrShowroomMaster[0]['city'].' ');
			$this->layout()->breadcrum = '<a href="/">Home</a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search")).'">Buy Used Cars</a> > '.$arrData->make.' > Used '.$arrData->make.' Cars';
		}
		else if($search_type == "body")
		{
			$arrMeta = array('title' => 'Used '.$arrData->body_style.' cars in Gurgaon, Delhi/NCR-'.$totalCount.' Certified '.$arrData->body_style.' Cars for Sale'.$title_page_ext.'-Direct Cars',
							'description' => date('d M y').'-Choose from '.$totalCount.' Used '.$arrData->body_style.' Cars in Gurgaon, Delhi/NCR. Find wide range of Quality Certified Second Hand Cars for Sale. Pre-owned cars from Direct Cars are non-accidental, genuine mileage, certified and come with warranty.',
							'keywords' => 'Certified Used '.$arrData->body_style.' Cars in '.$arrShowroomMaster[0]['city'].'');
			$this->layout()->breadcrum = '<a href="/">Home</a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search")).'">Buy Used Cars</a> > '.$arrData->body_style.' > Used '.$arrData->body_style.' Cars';
		}
		else if($search_type == "budget")
		{
			if($arrData->max_price != 0)
				$budget = $arrData->min_price."-".$arrData->max_price;
			else
				$budget = $arrData->min_price." and above";
			$arrMeta = array('title' => $totalCount.' Used Cars in '.$budget.' for sale in Gurgaon, Delhi/NCR'.$title_page_ext.'-Direct Cars',
							'description' => date('d M y').'-Choose from '.$totalCount.' Used Cars in '.$budget.' in Gurgaon, Delhi/NCR. Find wide range of Quality Certified Second Hand Cars for Sale. Pre-owned cars from Direct Cars are non-accidental, genuine mileage, certified and come with warranty.',
							'keywords' => 'Certified Used Cars in Price Range - '.$arrData->min_price.', '.$arrData->max_price.' in '.$arrShowroomMaster[0]['city'].' ');
			$this->layout()->breadcrum = '<a href="/">Home</a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search")).'">Buy Used Cars</a> > Cars in Rs. '.$arrData->min_price.', '.$arrData->max_price.'';
		}
		else if($search_type == "model")
		{	
			$arrMeta = array('title' => 'Used '.$arrData->make.' '.$arrData->model.' cars in Gurgaon, Delhi/NCR-'.$totalCount.' Certified '.$arrData->make.' '.$arrData->model.' Cars for Sale'.$title_page_ext.'-Direct Cars',
							'description' => date('d M y').'-Choose from '.$totalCount.' Used '.$arrData->make.' '.$arrData->model.' '.$arrData->model.' Cars in Gurgaon, Delhi/NCR. Find wide range of Quality Certified Second Hand Cars for Sale. Pre-owned cars from Direct Cars are non-accidental, genuine mileage, certified and come with warranty.',
							'keywords' => 'direct cars, directcars.in');
			$this->layout()->breadcrum = '<a href="/">Home</a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search")).'">Buy Used Cars</a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search","keyword"=>strtolower(str_replace(" ","+",$arrData->make))."-cars")).'">'.$arrData->make.'</a> > Used '.$arrData->make.' '.$arrData->model.' Cars';
		}
		else
		{
			$arrMeta = array('title' => 'Buy Used Cars in Gurgaon, Delhi/NCR-'.$totalCount.' Certified Cars for Sale '.$title_page_ext.' | Direct Cars',
							'description' => 'Browse quality certified used cars in Gurgaon, Delhi/NCR in India, research car models and features, best deals & best prices on used cars from Direct Cars, all online at DirectCars.in',
							'keywords' => 'direct cars, directcars.in ');
			$this->layout()->breadcrum = '<a href="/">Home</a> > Buy Used Cars';
		}
		
		$this->layout()->arrMeta = $arrMeta;
		
		
		
		$config = $this->getServiceLocator()->get('Config');
		
		return new ViewModel(array(
            'arrTestimonial' => $arrTestimonial,
			'totalCount' => $totalCount,
			'arrBodyStyle' => $arrBodyStyle,
			'arrMake' => $arrMake,
			'arrStockList' => $paginator,
            'page' => $arrParam['page'],
			'current' => $current,
			'first' => 1,
			'last' => $paginator->count(),
			'next' => $next,
			'previous' => $previous,
			'pagesInRange' => $paginator->getPagesInRange($lower_bound,$upper_bound),
			'pageCount' => $paginator->count(),
			'arrModel'=>$arrModel,
			'objSearch' => $arrData,
			'arrFuel' => $arrFuel,
			'image_path' => $config['image_path'],
			'image_root_path' => $config['image_root_path'],
			));
    }
	
	public function viewCarAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		//print_r($config); die;
		// Assign values to layout
		$arrWhere = array();
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster;
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		//$this->sendSMS();
		$strkyword = $this->params('keyword');
		$arrKeyword = explode("-",$strkyword);
		$stockid = $arrKeyword[count($arrKeyword)-1];
		if(!is_numeric($stockid))	
		{
			return $this->redirect()->toRoute('usedcar',array("action"=>"search"));
		}
		$arrStockData = $this->getStockMasterTable()->getStockDetail($stockid);
		if(empty($arrStockData))
		{
			return $this->redirect()->toRoute('usedcar',array("action"=>"search"));
		}
		$arrStockImages = $this->getStockMasterTable()->getStockImages($stockid);
		$request = $this->getRequest();
		
		if($request->isPost() || isset($search_type))
		{
			$this->session = new SessionContainer('post_search');
			$sessionData = $this->session->arrData;
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
										"mobile" => @$arrData->customer_mobile);
				$customer_id = $this->getEnquiryCustomerTable()->insertEnquiryCustomer($arrInsertData);
			}
			$arrBuyer = array("customer_id" => $customer_id,
							  "stock_id" => $stockid,
							  "mmv_id" => $arrStockData[0]['mmv_id'],
							  "budget_range" => @$sessionData->budget,
							  "status" => "New");
			$this->getBuyerEnquiryTable()->insertBuyerEnquiry($arrBuyer);
			
			// Send email to showroom manager"...
			$message = "Buyer ".@$arrData->customer_email.", ".@$arrData->customer_mobile." is interested in ".$arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']." ".$arrStockData[0]['variant_name'];
			$subject = "Buyer enquiry";
			$result = Fixedvalues::sendmail($message,$subject,$arrStockData[0]['email']);
			Fixedvalues::sendSMS($message,$arrStockData[0]['phone_1']);
			
			// send customer email & SMS
			if(isset($arrData->customer_mobile))
			{
				$message = "Glad you chose ".$arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']." ".$arrStockData[0]['variant_name'].", Our Auto Expert ".$arrStockData[0]['phone_1'].",will call to assist.Team DIRECT CARS. www.directcars.in";
				Fixedvalues::sendSMS($message,$arrData->customer_mobile);
			}
			if(isset($arrData->customer_email))
			{
				if($arrStockData[0]['hot_deal']== '1') $price = $arrStockData[0]['hoat_deal_price']; 
				else $price = $arrStockData[0]['expected_price']; 
				
				$image_path = $config['image_path'];
				
				$certified_by = "";
				if($arrStockData[0]['certified_by'] != "") {
					$certified_by = "<img src='".$image_path."/".str_replace(" ","_",strtolower($arrStockData[0]['certified_by'])).".png' alt='".$arrStockData[0]['certified_by']."'>";
				}
				
				$address = $arrStockData[0]['land_mark'].",".$arrStockData[0]['locality'].",".$arrStockData[0]['city'].",".$arrStockData[0]['state'].",".$arrStockData[0]['pin_code'];
				
				$subject = "Details of ".$arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']." ".$arrStockData[0]['variant_name'].", Car ID ".$stockid.", Direct Cars";
				$message = "Dear Customer,<br><br>
				Glad you chose our certified car. <br><br>
				Our Auto Expert ".$arrStockData[0]['phone_1'].", ".$arrStockData[0]['email'].", will call to understand your needs and would be happy to provide assistance in your next car purchase.<br><br>
				The details of the car you chose are as follows:<br><br>
				Model: ".$arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']." ".$arrStockData[0]['variant_name'].", 
				Price: Rs.".$price."<br><br>
				Fuel Type: ".$arrStockData[0]["fuel_type"]."<br><br>
				Transmission: ".$arrStockData[0]["transmission_type"]." <br><br>
				KM Driven: ".$arrStockData[0]["kilometre"]." <br><br>
				Owners: ".$arrStockData[0]["owner"]." owner <br><br>
				Certified by: ".$certified_by." <br><br>
				Visit our ".$arrStockData[0]['showroom_name']." showroom: ".$address."<br><br>
				View Map: <a href='https://www.google.co.in/maps/place/DIRECT+CARS/@28.486347,77.052987,15z/data=!4m2!3m1!1s0x0:0xd42ff862ab16c092?sa=X&ei=u7obVeSfC4O6uASJ_oHoCg&ved=0CH0Q_BIwCw'>Click Here</a><br>
				Best Regards,<br>
				TEAM DIRECT CARS<br><br><br>
				<img src='".$config['site_image_path']."logo.png'><br><br>
				Visit us at: <a href='www.directcars.in'>www.directcars.in</a><br><br>
				We are on: <a href='https://plus.google.com/+DIRECTCARSGurgaon/about' target='NEW'><img src='".$config['site_image_path']."google_icon.png' width='20px'></a>
						<a  href='https://www.facebook.com/directcars.in' target='NEW'><img src='".$config['site_image_path']."facebook_icon.png' width='20px'></a>";
				$result = Fixedvalues::sendmail($message,$subject,$arrData->customer_email);
				//echo $message; die;
			}
			
			$keyword = $arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']." ".$arrStockData[0]['variant_name']."-".$arrStockData[0]['stock_id'];
			$keyword = str_replace(" ","-",$keyword);
			$keyword = strtolower($keyword);
			
			$this->flashMessenger()->addMessage('Your Enquiry has been placed.');
			return $this->redirect()->toRoute('usedcar',array("action"=>"view-car","keyword"=>$keyword));
		}
		
		// Get google maps coords
		$address = $arrStockData[0]['land_mark'].",".$arrStockData[0]['locality'].",".$arrStockData[0]['city'].",".$arrStockData[0]['state'].",".$arrStockData[0]['pin_code'];
		//$address = "Plot no 21, sector 17, vashi, Navi Mumbai, 400703";
		$data_arr = Fixedvalues::geocode($address);
		
		if($arrStockData[0]['certified_by'] != "") { $title = 'Certified ';  } else { $title = 'Used '; }
		$title .= $arrStockData[0]['make_year']." ".$arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']. 
		" ".$arrStockData[0]['variant_name'];
		
		$arrMeta = array('title' => ''.$title.'-ID '.$arrStockData[0]['stock_id'].' in Gurgaon, Delhi/NCR for Sale-directcars.in',
						'description' => 'Know more about Used Car for Sale in Gurgaon, Delhi/NCR:'.$title.'-ID '.$arrStockData[0]['stock_id'].' at directcars.in',
						'keywords' => 'used '.$title.', second-hand '.$title.', buy used '.$title.', '.$address.', '.$arrStockData[0]['stock_id'].', '.$arrStockData[0]['showroom_name'].', directcars.in, delhi, ncr');
		
		$this->layout()->arrMeta = $arrMeta;
		
		$this->layout()->breadcrum = ' <a href="/">Home</a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search")).'"> Buy Used Cars </a> > <a href="'.$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"search","keyword" => strtolower(str_replace(" ","+",$arrStockData[0]['make_name'])).'-'.strtolower(str_replace(" ","+",$arrStockData[0]['model_name']))."-cars")).'">'.$arrStockData[0]['make_name'].' '.$arrStockData[0]['model_name'].'</a> > '.$arrStockData[0]['make_name'].' '.$arrStockData[0]['model_name'].' '.$arrStockData[0]['variant_name'].'';
		
		$viewModel =  new ViewModel(array(
            'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrStockData' => $arrStockData[0],
			'arrStockImages' => $arrStockImages,
			'data_arr' => $data_arr,
			'image_path' => $config['image_path'],
			'image_root_path' => $config['image_root_path'],
			));
		
		return $viewModel;
	}
	
	public function createWhereCond($arrData)
	{
		$strWhere = "";
		//print_R($arrData);
		if(!empty($arrData->make))
		{
			$strWhere .= "mk.make_name = '".$arrData->make."' AND ";
		}
		if(!empty($arrData->model))
		{
			$strWhere .= "mo.model_name = '".$arrData->model."' AND ";
		}
		
		if(!empty($arrData->body_style))
		{
			$strWhere .= "mmv.bod_style = '".$arrData->body_style."' AND ";
		}
		if(!empty($arrData->fuel_type))
		{
			$strWhere .= "sm.fuel_type = '".$arrData->fuel_type."' AND ";
		}
		if(!empty($arrData->budget))
		{	
			$arrBudget=explode("-",$arrData->budget);
			if($arrBudget[0] != 0)	$arrData->min_price = $arrBudget[0]."00000";
			if($arrBudget[1] != 0)	$arrData->max_price = $arrBudget[1]."00000";
			if(!empty($arrData->min_price) and !empty($arrData->max_price))
			{
				$strWhere .= "((sm.expected_price >= ".$arrData->min_price." AND sm.expected_price <= ".$arrData->max_price." AND sm.hot_deal = '0' ) OR (sm.hoat_deal_price >= ".$arrData->min_price." AND sm.hoat_deal_price <= ".$arrData->max_price." AND sm.hot_deal = '1' ) ) AND";
			}
			else if(!empty($arrData->min_price))
			{
				$strWhere .= "((sm.expected_price >= ".$arrData->min_price." AND sm.hot_deal = '0') OR (sm.hoat_deal_price >= ".$arrData->min_price." AND sm.hot_deal = '1')) AND ";
			}
			else if(!empty($arrData->max_price))
			{
				$strWhere .= "((sm.expected_price <= ".$arrData->max_price." AND sm.hot_deal = '0') OR (sm.hoat_deal_price <= ".$arrData->max_price." AND sm.hot_deal = '1')) AND ";
			}
		}
		$strWhere = trim($strWhere," AND ");
		return $strWhere;
	}
	
	public function sellAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		// Assign values to layout
		$arrWhere = array();
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster;
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		$this->layout()->breadcrum = '<a href="/">Home</a> > Sell Your Car';
		
		$request = $this->getRequest();
		if($request->isPost() || isset($search_type))
		{
			$arrData = $request->getPost();
			
			// require_once( '../direct_Cars_website/vendor/recaptcha/recaptchalib.php');
			 //$privatekey = "6LdmEAUTAAAAAJYcUgm0YOqZnA9HK3ROjNQhc4D0";
			// $resp = recaptcha_check_answer ($privatekey,
											// $_SERVER["REMOTE_ADDR"],
										//	 $arrData->recaptcha_challenge_field,
											//$arrData->recaptcha_response_field);
			// if (!$resp->is_valid) {
				//$this->flashMessenger()->addMessage("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
				//	"(reCAPTCHA said: " . $resp->error . ")");
				//return $this->redirect()->toRoute('usedcar',array("action"=>"sell"));
			// }
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
				$arrInsertData = array( "name" 	 => @$arrData->customer_name, 
										"email"  => @$arrData->customer_email,
										"mobile" => @$arrData->customer_mobile,
										"customer_city" => @$arrData->customer_city);
				$customer_id = $this->getEnquiryCustomerTable()->insertEnquiryCustomer($arrInsertData);
			}
			$arrBuyer = array("customer_id" => $customer_id,
							  "make_year" => @$arrData->make_year,
							  "make_id" => @$arrData->car_make,
							  "model_id" => @$arrData->car_model,
							  "kilometre" => @$arrData->km_driven,
							  "registration_place" => @$arrData->reg_city,
							  "status" => "New");
			$this->getSellerEnquiryTable()->insertSellerEnquiry($arrBuyer);
			
			$message = "";
			if(@$arrData->customer_name != "") $message .= $arrData->customer_name." , ";
			if(@$arrData->customer_email != "") $message .= $arrData->customer_email." , ";
			if(@$arrData->customer_mobile != "") $message .= $arrData->customer_mobile." , ";
			
			$sell_make_name = $this->getMmvDetailTable()->get_make_name($arrData->car_make);
			$sell_model_name = $this->getMmvDetailTable()->get_model_name($arrData->car_model);
			//\\print_R($sell_make_name); die;
			$message = trim($message," , ");
			
			$message .= " customer city ".$arrData->customer_city." wants to sell ".$sell_make_name." ".$sell_model_name." drivern ".$arrData->km_driven." registration ".$arrData->reg_city."";
			$subject = "Seller enquiry";
			$result = Fixedvalues::sendmail($message,$subject,$arrShowroomMaster[0]['secondary_email']);
			
			$message = $arrData->customer_name.", ";
			$message .= $arrData->customer_email.", ".$arrData->customer_mobile." wants to sell ".$sell_make_name." ".$sell_model_name." ".$arrData->reg_city."";
			//echo $message;
			Fixedvalues::sendSMS($message,$arrShowroomMaster[0]['phone_2']);

			// send customer email & SMS
			if(isset($arrData->customer_mobile) && $arrData->customer_mobile != "")
			{	
				$message = "Glad you chose DIRECT CARS to sell your car.Our Auto Expert ".$arrShowroomMaster[0]['phone_2'].",will call to assist.Team DIRECT CARS. www.directcars.in";
				Fixedvalues::sendSMS($message,$arrData->customer_mobile);
			}
			if(isset($arrData->customer_email) && $arrData->customer_email != "")
			{
				$address = $arrShowroomMaster[0]['land_mark'].",".$arrShowroomMaster[0]['locality'].",".$arrShowroomMaster[0]['city'].",".$arrShowroomMaster[0]['state'].",".$arrShowroomMaster[0]['pin_code'];
				$subject = "Appointment for Appraisal of your car by Direct Cars";
				$message = "Dear Customer,<br><br>
				Glad you chose DIRECT CARS to sell your ".$sell_make_name." ".$sell_model_name." ".$arrData->make_year." model car.
				<br><br>
				Our Auto Expert ".$arrShowroomMaster[0]['phone_2'].", ".$arrShowroomMaster[0]['secondary_email'].", will call to fix an appointment for FREE Appraisal of our car.
				<br><br>
				You can also visit our ".$arrShowroomMaster[0]['showroom_name']." showroom: ".$address."<br><br>
				<br><br>
				View Map: <a href='https://www.google.co.in/maps/place/DIRECT+CARS/@28.486347,77.052987,15z/data=!4m2!3m1!1s0x0:0xd42ff862ab16c092?sa=X&ei=u7obVeSfC4O6uASJ_oHoCg&ved=0CH0Q_BIwCw'>Click Here</a>
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
			return $this->redirect()->toRoute('usedcar',array("action"=>"sell"));
		}
		
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList();
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		$regcity = Fixedvalues::get_reg_city();
		
		$arrMeta = array('title' => 'Sell Us Your Car - We buy all cars at Direct Cars',
						'description' => 'Sell your used car at Direct Cars. Get Best Price for Gurgaon, Delhi and Faridabad Registered Cars. Our Safe, Fast and Hassle-Free used car buying process assures complete peace of mind. Sell your car online now at DirectCars.in',
						'keywords' => 'direct cars, directcars.in ');
		
		$this->layout()->arrMeta = $arrMeta;
		
		$viewModel =  new ViewModel(array(
			'arrTestimonial' => $arrTestimonial,
			'sell_phone' => ($arrShowroomMaster[0]['phone_2'] != "")?$arrShowroomMaster[0]['phone_2']:$arrShowroomMaster[0]['phone_1'],
            'flashMessages' => $this->flashMessenger()->getMessages(),
			'image_path' => $config['image_path'],
			'regcity' => $regcity,
			'start_year' => $start_year
			));
		
		return $viewModel;
	}
	
	public function getMakeByYearAction()
	{
		 $request = $this->getRequest();
		 $arrData = $request->getPost(); 
		 $arrMMvDetail = $this->getMmvDetailTable()->getMmvMakeList($arrData->year);
		 echo json_encode($arrMMvDetail); die;
	}
	
	public function getModelByMakeAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrMMvDetail = $this->getMmvDetailTable()->getMmvModelList($arrData->make_id,$arrData->year);
		 echo json_encode($arrMMvDetail); die;
	}

	public function getTestimonialTable()
	{
		if (!$this->testimonialTable) {
            $sm = $this->getServiceLocator();
            $this->testimonialTable = $sm->get('Home\Model\TestimonialTable');
        }
        return $this->testimonialTable;
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
	public function getBuyerEnquiryTable()
	{
		if (!$this->buyerEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->buyerEnquiryTable = $sm->get('Usedcar\Model\BuyerEnquiryTable');
        }
        return $this->buyerEnquiryTable;
	}
	public function getSellerEnquiryTable()
	{
		if (!$this->sellerEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->sellerEnquiryTable = $sm->get('Usedcar\Model\SellerEnquiryTable');
        }
        return $this->sellerEnquiryTable;
	}
	public function getMmvDetailTable()
	{
		if (!$this->mmvDetailTable) {
            $sm = $this->getServiceLocator();
            $this->mmvDetailTable = $sm->get('Usedcar\Model\MmvDetailTable');
        }
        return $this->mmvDetailTable;
	}
	
	public function getShowroomMasterTable()
	{
		if (!$this->showroomMasterTable) {
            $sm = $this->getServiceLocator();
            $this->showroomMasterTable = $sm->get('Showroom\Model\ShowroomMasterTable');
        }
        return $this->showroomMasterTable;
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
