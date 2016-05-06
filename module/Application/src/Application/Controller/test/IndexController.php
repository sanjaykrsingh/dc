<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container as SessionContainer;

class IndexController extends AbstractActionController
{
	protected $testimonialTable;
	protected $stockMasterTable;
	protected $showroomMasterTable;
	protected $settingsTable;
	
   public function onDispatch(MvcEvent $e) {
			//return $this->redirect()->toRoute('');
		
        return parent::onDispatch($e);
    }

    public function indexAction() {     
     $this->session = new SessionContainer('post_search');
		$this->session->arrData = array();
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList();
		$arrBodyStyle = $this->getStockMasterTable()->getStockBodyStyle();
		$arrMake = $this->getStockMasterTable()->getStockMake();
		$arrMakeModel = $this->getStockMasterTable()->getStockMakeModel();
		$arrHoatDeal = $this->getStockMasterTable()->getHotDeal();
		
		$arrBudget = array("0-1" => $this->getStockMasterTable()->getStockcnt('((sm.expected_price <= 100000 AND hot_deal = "0") OR (sm.hoat_deal_price <= 100000 AND hot_deal = "1"))'),
						   "1-2" => $this->getStockMasterTable()->getStockcnt('((sm.expected_price >= 100000 AND sm.expected_price <= 200000 AND hot_deal = "0") OR (sm.hoat_deal_price >= 100000 AND sm.hoat_deal_price <= 200000 AND hot_deal = "1"))'),
						   "2-3" => $this->getStockMasterTable()->getStockcnt('((sm.expected_price >= 200000 AND sm.expected_price <= 300000 AND hot_deal = "0") OR (sm.hoat_deal_price >= 200000 AND sm.hoat_deal_price <= 300000 AND hot_deal = "1"))'),
						   "3-5" => $this->getStockMasterTable()->getStockcnt('((sm.expected_price >= 300000 AND sm.expected_price <= 500000 AND hot_deal = "0") OR (sm.hoat_deal_price >= 300000 AND sm.hoat_deal_price <= 500000 AND hot_deal = "1"))'),
						   "5-10" => $this->getStockMasterTable()->getStockcnt('((sm.expected_price >= 500000 AND sm.expected_price <= 1000000 AND hot_deal = "0") OR (sm.hoat_deal_price >= 500000 AND sm.hoat_deal_price <= 1000000 AND hot_deal = "1"))'),
						   "10-0" => $this->getStockMasterTable()->getStockcnt('((sm.expected_price >= 1000000 AND hot_deal = "0") OR (sm.hoat_deal_price >= 1000000 AND hot_deal = "1"))'),
						   );
		
		// set layout variables
		$arrWhere = array("showroom_id" => 2);
		$arrShowroomMaster = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$this->layout()->arrShowroomMaster = $arrShowroomMaster[0];
		
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$this->layout()->arrSettings = $arrSettings;
		
		$this->layout()->breadcrum = 'Home';
		
		$arrMeta = array('title' => 'Direct Cars-Buy & Sell Used Cars in Gurgaon, Delhi/NCR |DirectCars.in',
						'description' => 'Direct Cars is a professional used car company in Gurgaon, Delhi/NCR in India, offering great way to buy and sell used cars. Know more at DirectCars.in',
						'keywords' => 'direct cars, directcars.in');
		
		$this->layout()->arrMeta = $arrMeta;
		$config = $this->getServiceLocator()->get('Config');
		return new ViewModel(array(
            'arrTestimonial' => $arrTestimonial,
			'arrBodyStyle' => $arrBodyStyle,
			'arrMake' => $arrMake,
			'arrMakeModel' => $arrMakeModel,
			'arrHoatDeal' => $arrHoatDeal[0],
			'arrBudget' => $arrBudget,
			'image_path' => $config['image_path'],
			'site_image_path' => $config['site_image_path'],
			'image_root_path'=>$config['image_root_path'],
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
	public function getStockMasterTable()
	{
		if (!$this->stockMasterTable) {
            $sm = $this->getServiceLocator();
            $this->stockMasterTable = $sm->get('Home\Model\StockMasterTable');
        }
        return $this->stockMasterTable;
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