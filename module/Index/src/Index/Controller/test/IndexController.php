<?php

namespace Index\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container as SessionContainer;

class indexController extends AbstractActionController {
	
	protected $testimonialTable;
	protected $showroomMasterTable;
	protected $stockMasterTable;
	protected $settingsTable;
	
	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {
        return parent::onDispatch($e);
    }

	
    public function whyUsAction() {
		// Assign values to layout
		$arrWhere = array("showroom_id" => 2);
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster[0];
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		$arrMeta = array('title' => 'Direct Cars - We make used cars a joyful experience',
						'description' => 'At Direct Cars our commitment to deliver great quality, great value, great experience in making buying and selling used cars a joyful experience. We are one stop shop for all your used car needs.',
						'keywords' => 'We make used cars a joyful experience, direct cars');
		
		$this->layout()->arrMeta = $arrMeta;
		$this->layout()->breadcrum = '<a href="/">Home</a> > Why Direct Cars';
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
			'arrTestimonial' => $arrTestimonial,
            'flashMessages' => $this->flashMessenger()->getMessages(),
			'image_path' => $config['image_path']
			));
		
		return $viewModel;
    }
	
	public function privacyAction()
	{
		// Assign values to layout
		$arrWhere = array("showroom_id" => 2);
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster[0];
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		$arrMeta = array('title' => 'directcars.in privacy policy',
						'description' => 'directcars.in privacy policy',
						'keywords' => 'directcars.in privacy policy');
		
		$this->layout()->arrMeta = $arrMeta;
		
		$this->layout()->breadcrum = '<a href="/">Home</a> > Privacy Policy';
		
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList();
		
		$config = $this->getServiceLocator()->get('Config');
		
		$viewModel =  new ViewModel(array(
			'arrTestimonial' => $arrTestimonial,
            'flashMessages' => $this->flashMessenger()->getMessages(),
			'image_path' => $config['image_path']
			));
		
		return $viewModel;
	}
	
	public function usedCarsAction()
	{
		return $this->redirect()->toRoute('usedcar',array("action"=>"search"));
	}
	
	public function generateSitemapAction()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		$sitemap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
			<!-- created with Free Online Sitemap Generator www.xml-sitemaps.com -->

			<url>
			  <loc>https://directcars.in</loc>
			  <changefreq>always</changefreq>
			</url>
			<url>
			  <loc>https://directcars.in/used-cars/sell</loc>
			  <changefreq>always</changefreq>
			</url>
			<url>
			  <loc>https://directcars.in/used-cars/search</loc>
			  <changefreq>always</changefreq>
			</url>
			<url>
			  <loc>https://directcars.in/showroom/direct+cars:+old+delhi+gurgaon+road-in-gurgaon-2</loc>
			  <changefreq>always</changefreq>
			</url>
			<url>
			  <loc>https://directcars.in/why-us</loc>
			  <changefreq>always</changefreq>
			</url>
			<url>
			  <loc>https://directcars.in/privacy</loc>
			  <changefreq>always</changefreq>
			</url>
			
			';
			
		$arrMake = $this->getStockMasterTable()->getStockMake();
		foreach($arrMake as $make)
		{
			$sitemap .= '<url>
			  <loc>https://directcars.in/used-cars/search/'.strtolower(str_replace(" ","+",$make['make_name'])).'-cars</loc>
			  <changefreq>always</changefreq>
			</url>';
		}
		
		$arrBodyStyle = $this->getStockMasterTable()->getStockBodyStyle();
		foreach($arrBodyStyle as $body)
		{
			$sitemap .= '<url>
			  <loc>https://directcars.in/used-cars/search/'.strtolower(str_replace(" ","+",$body['bod_style'])).'-style-cars</loc>
			  <changefreq>always</changefreq>
			</url>';
		}
		
		$arrMakeModel = $this->getStockMasterTable()->getStockMakeModel();
		
		foreach($arrMakeModel as $mm)
		{
			$sitemap .= '<url>
			  <loc>https://directcars.in/used-cars/search/'.strtolower(str_replace(" ","+",$mm['make_name']))."-".strtolower(str_replace(" ","+",$mm['model_name'])).'-cars</loc>
			  <changefreq>always</changefreq>
			</url>';
		}
		$arrStock = $this->getStockMasterTable()->getStockList();
		
		foreach($arrStock as $stock)
		{
			
			$keyword = $stock['make_name']." ".$stock['model_name']." ".$stock['variant_name']."-".$stock['stock_id'];
			$keyword = str_replace(" ","-",$keyword);
			$keyword = strtolower($keyword);
		
			$dest_url = "https://directcars.in".$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"view-car","keyword"=>$keyword));
			$sitemap .= '<url>
			  <loc>'.$dest_url.'</loc>
			  <changefreq>always</changefreq>
			</url>';
			
		}
		$sitemap .= '</urlset>';
		
		$myFile = $_SERVER['DOCUMENT_ROOT']."/sitemap.xml";
		$fh = fopen($myFile, 'w');	
		fwrite($fh, $sitemap);
		fclose($fh);
		echo $sitemap; die;
	}
	public function directcarsDynamicxReportAction()
	{
		$arrStock = $this->getStockMasterTable()->getStockList();
		$config = $this->getServiceLocator()->get('Config');
		// output headers so that the file is downloaded rather than displayed
		//header('Content-Type: text/csv; charset=utf-8');
		//header('Content-Disposition: attachment; filename=data.csv');

		// create a file pointer connected to the output stream
		//$fp = fopen('php://output', 'w');
		//echo "<pre>";print_r($_SERVER);
		// output the column headings
		
		$strDisplay= implode(",",array('ID','Item Title','Item Subtitle','Price','Image Url','Destination Url','Item Category'));
		$strDisplay .= "\n";
		foreach($arrStock as $stock)
		{
			//print_r($stock);
			//die;
			if($stock['hot_deal']== '1') $price = $stock['hoat_deal_price'];
			else $price = $stock['expected_price'];
			
			$keyword = $stock['make_name']." ".$stock['model_name']." ".$stock['variant_name']."-".$stock['stock_id'];
			$keyword = str_replace(" ","-",$keyword);
			$keyword = strtolower($keyword);
			
			$arrImage = array();
			$image_name = "";
			if($stock['is_profile_img'] != "")
			{
				$arrImage = explode(",",$stock['image_name']); 
				$arrProfile = explode(",",$stock['is_profile_img']); 
				for($i=0; $i < count($arrProfile); $i++)
				{
					if($i==0 || $arrProfile[$i] == 1)
					{
						$image_name = $arrImage[$i];
					}
				}
			}
			$image_url = "";
			if($image_name !=  "")
			{
				if(file_exists($config['image_root_path']."stock/crop/".$image_name))
					$file_path = "stock/crop/";
				else 
					$file_path = "stock/";
				$image_url = $config['image_path'].$file_path.$image_name;
			}				
			
			$dest_url = "https://directcars.in".$this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('usedcar',array("action"=>"view-car","keyword"=>$keyword))."?utm_source=google_dynx&utm_medium=cpc&utm_campaign=usedcars&source=google_dynx";
			
			
			if($stock['certified_by'] != "") { $title = "Certified ";
			} else { $title = "Used ";
			} 
			$title .= $stock['make_year']." ".$stock['make_name']." ".$stock['model_name']." ".$stock['variant_name'];
			
			$strDisplay .= implode(",", array($stock['stock_id'],
								   $title,
								   '',
								   $price." INR",
								   $image_url,
								   $dest_url,
								   $stock['bod_style']));
								   $strDisplay .= "\n";
		}
		//fclose($fp);
		//die;
		//header("location: http://localhost/direct_cars_website/data.csv");
	
		
	
		$viewModel = new ViewModel(array("strDisplay"=>$strDisplay));
        $viewModel->setTerminal(true);
        return $viewModel;
		
	}
	
	public function getShowroomMasterTable()
	{
		if (!$this->showroomMasterTable) {
            $sm = $this->getServiceLocator();
            $this->showroomMasterTable = $sm->get('Showroom\Model\ShowroomMasterTable');
        }
        return $this->showroomMasterTable;
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
	
	public function getSettingsTable()
	{
		if (!$this->settingsTable) {
            $sm = $this->getServiceLocator();
            $this->settingsTable = $sm->get('Home\Model\SettingsTable');
        }
        return $this->settingsTable;
	}

}
