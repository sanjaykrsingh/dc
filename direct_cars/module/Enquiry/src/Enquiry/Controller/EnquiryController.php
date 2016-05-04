<?php

namespace Enquiry\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Enquiry\Form\SaveForm;
use Enquiry\Form\SaveAddForm;
use Zend\View\Model\ViewModel;
use Fixedvalues;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Session\Container as SessionContainer;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

class EnquiryController extends AbstractActionController {

	protected $enquiryCustomerTable;
	protected $buyerEnquiryTable;
	protected $generalEnquiryTable;
	protected $sellerEnquiryTable;
	protected $mmvDetailTable;
	public $login_user_id;

	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {

        $sm = $this->getServiceLocator();
        $auth = $sm->get('zfcuserauthservice');
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser');
        }
		$this->login_user_id = $auth->getIdentity()->getId();
        return parent::onDispatch($e);
    }

    public function indexAction() {
		$this->session = new SessionContainer('post_search');
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'enq_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrPost = array();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			$this->session->arrPost = $arrPost;
		}
		else
		{
			$arrPost = $this->session->arrPost;
		}
		
		
		$arrEnquiry = $this->getBuyerEnquiryTable()->fetchCustomEnquiryList($arrPost,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrEnquiry));

        $paginator->setCurrentPageNumber($arrParam['page'])
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

		$current = $arrParam['page'];
		$previous = (int) ($arrParam['page'] == 1 ) ? null : $arrParam['page'] - 1;
		$next = (int) ($arrParam['page'] == $paginator->count()) ? null : $arrParam['page'] + 1;
		
		$range = (int)($arrParam['page'] / 7) ; 
		$lower_bound = (($range)*7) + ($range - 1);
		$upper_bound = (($range)*7) + 7;

		return new ViewModel(array(
            'arrEnquiry' => $paginator,
			'arrPost' => $arrPost,
			'order_by' => $arrParam['order_by'],
            'order' => $arrParam['order'],
            'page' => $arrParam['page'],
			'current' => $current,
			'first' => 1,
			'last' => $paginator->count(),
			'next' => $next,
			'previous' => $previous,
			'pagesInRange' => $paginator->getPagesInRange($lower_bound,$upper_bound),
			'pageCount' => $paginator->count(),
			'flashMessages' => $this->flashMessenger()->getMessages(),
			));
    }
	
	public function exportBuyerAction()
	{
		$this->session = new SessionContainer('post_search');
		$file_name = "buyer_enquiry-".date("Y_m_d_H_i_s").".xlsx";
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX buyer enquiry Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX buyer enquiry Export");
		$objPHPExcel->getProperties()->setDescription("export buyer enquiry on ".date("Y m d H:i:s").".");
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Sr. No.'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Enquire On'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Customer Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Customer Email');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Customer Mobile');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Stock Id');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Car Detail'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Budget Range'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Address'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Gender'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Profession'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Anual Income'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Comments');
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Status');
		
		$arrPost = array();
		$arrParam = array();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			$this->session->arrPost = $arrPost;
		}
		else
		{
			$arrPost = $this->session->arrPost;
		}
		
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'enq_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
		$arrEnquiry = $this->getBuyerEnquiryTable()->fetchCustomEnquiryList($arrPost,$arrParam);
		$rowid = 2;
		
		foreach($arrEnquiry as $enquiry)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-1); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, date("d/m/Y",strtotime($enquiry['enquiry_on']))); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $enquiry['name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, $enquiry['email']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $enquiry['mobile']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $enquiry['stock_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $enquiry['make_name']." - ".$enquiry['model_name']." - ".$enquiry['variant_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $enquiry['budget_range']." LKH"); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, $enquiry['customer_address'].";".$enquiry['customer_city'].";".$enquiry['customer_state'].";".$enquiry['customer_pin']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $enquiry['gender']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $enquiry['profession']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $enquiry['annual_income']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowid, $enquiry['comments']);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowid, $enquiry['status']); 
			$rowid++;
		} 
		
		$objPHPExcel->getActiveSheet()->setTitle('Buyer Enquiry');
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($file_name);
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file_name));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_name));
		ob_clean();
		flush();
		
		readfile($file_name);
		unlink($file_name);
		
		exit();
	}
	
	
	public function generalAction() {
		$this->session = new SessionContainer('post_search');
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'enq_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		
		$arrPost = array();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			$this->session->arrPost = $arrPost;
		}
		else
		{
			$arrPost = $this->session->arrPost;
		}
		
		
		$arrEnquiry = $this->getGeneralEnquiryTable()->fetchCustomEnquiryList($arrPost,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrEnquiry));

        $paginator->setCurrentPageNumber($arrParam['page'])
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

		$current = $arrParam['page'];
		$previous = (int) ($arrParam['page'] == 1 ) ? null : $arrParam['page'] - 1;
		$next = (int) ($arrParam['page'] == $paginator->count()) ? null : $arrParam['page'] + 1;
		
		$range = (int)($arrParam['page'] / 7) ; 
		$lower_bound = (($range)*7) + ($range - 1);
		$upper_bound = (($range)*7) + 7;

		return new ViewModel(array(
            'arrEnquiry' => $paginator,
			'arrPost' => $arrPost,
			'order_by' => $arrParam['order_by'],
            'order' => $arrParam['order'],
            'page' => $arrParam['page'],
			'current' => $current,
			'first' => 1,
			'last' => $paginator->count(),
			'next' => $next,
			'previous' => $previous,
			'pagesInRange' => $paginator->getPagesInRange($lower_bound,$upper_bound),
			'pageCount' => $paginator->count(),
			'flashMessages' => $this->flashMessenger()->getMessages(),
			));
    }
	
	
	public function exportGeneralAction()
	{
		$this->session = new SessionContainer('post_search');
		$file_name = "general_enquiry-".date("Y_m_d_H_i_s").".xlsx";
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX general enquiry Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX general enquiry Export");
		$objPHPExcel->getProperties()->setDescription("export general enquiry on ".date("Y m d H:i:s").".");
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Sr. No.'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Enquire On'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Customer Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Customer Email');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Customer Mobile');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Customer Message');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Address'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Gender'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Profession'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Anual Income'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Comments');
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Status');
		
		$arrPost = array();
		$arrParam = array();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			$this->session->arrPost = $arrPost;
		}
		else
		{
			$arrPost = $this->session->arrPost;
		}
		
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'enq_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
		$arrEnquiry = $this->getGeneralEnquiryTable()->fetchCustomEnquiryList($arrPost,$arrParam);
		$rowid = 2;	
		
		foreach($arrEnquiry as $enquiry)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-1); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, date("d/m/Y",strtotime($enquiry['enquiry_on']))); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $enquiry['name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, $enquiry['email']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $enquiry['mobile']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $enquiry['customer_message']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $enquiry['customer_address'].";".$enquiry['customer_city'].";".$enquiry['customer_state'].";".$enquiry['customer_pin']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $enquiry['gender']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, $enquiry['profession']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $enquiry['annual_income']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $enquiry['comments']);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $enquiry['status']); 
			$rowid++;
		} 
		
		$objPHPExcel->getActiveSheet()->setTitle('General Enquiry');
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($file_name);
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file_name));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_name));
		ob_clean();
		flush();
		
		readfile($file_name);
		unlink($file_name);
		
		exit();
	}
	
	public function sellerAction() {
		$this->session = new SessionContainer('post_search');
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'enq_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrPost = array();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			$this->session->arrPost = $arrPost;
		}
		else
		{
			$arrPost = $this->session->arrPost;
		}
		$arrEnquiry = $this->getSellerEnquiryTable()->fetchCustomEnquiryList($arrPost,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrEnquiry));

        $paginator->setCurrentPageNumber($arrParam['page'])
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

		$current = $arrParam['page'];
		$previous = (int) ($arrParam['page'] == 1 ) ? null : $arrParam['page'] - 1;
		$next = (int) ($arrParam['page'] == $paginator->count()) ? null : $arrParam['page'] + 1;
		
		$range = (int)($arrParam['page'] / 7) ; 
		$lower_bound = (($range)*7) + ($range - 1);
		$upper_bound = (($range)*7) + 7;

		return new ViewModel(array(
            'arrEnquiry' => $paginator,
			'arrPost' => $arrPost,
			'order_by' => $arrParam['order_by'],
            'order' => $arrParam['order'],
            'page' => $arrParam['page'],
			'current' => $current,
			'first' => 1,
			'last' => $paginator->count(),
			'next' => $next,
			'previous' => $previous,
			'pagesInRange' => $paginator->getPagesInRange($lower_bound,$upper_bound),
			'pageCount' => $paginator->count(),
			'flashMessages' => $this->flashMessenger()->getMessages(),
			));
    }
	
	public function exportSellerAction()
	{
		$this->session = new SessionContainer('post_search');
		$file_name = "seller_enquiry-".date("Y_m_d_H_i_s").".xlsx";
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX seller enquiry Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX seller enquiry Export");
		$objPHPExcel->getProperties()->setDescription("export seller enquiry on ".date("Y m d H:i:s").".");
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Sr. No.'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Enquire On'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Customer Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Customer Email');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Customer Mobile');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Make Year');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Make - Model'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'KM Driven'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Registration Place'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Address'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Gender'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Profession'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Anual Income'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Comments');
		$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Status');
		
		$arrPost = array();
		$arrParam = array();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			$this->session->arrPost = $arrPost;
		}
		else
		{
			$arrPost = $this->session->arrPost;
		}
		
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'enq_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
		$arrEnquiry = $this->getSellerEnquiryTable()->fetchCustomEnquiryList($arrPost,$arrParam);
		$rowid = 2;		
		
		foreach($arrEnquiry as $enquiry)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-1); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, date("d/m/Y",strtotime($enquiry['enquiry_on']))); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $enquiry['name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, $enquiry['email']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $enquiry['mobile']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $enquiry['make_year']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $enquiry['make_name']." / ".$enquiry['model_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $enquiry['kilometre']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, $enquiry['registration_place']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $enquiry['customer_address'].";".$enquiry['customer_city'].";".$enquiry['customer_state'].";".$enquiry['customer_pin']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $enquiry['gender']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $enquiry['profession']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowid, $enquiry['annual_income']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowid, $enquiry['comments']);
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowid, $enquiry['status']); 
			$rowid++;
		} 
		
		$objPHPExcel->getActiveSheet()->setTitle('Seller Enquiry');
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($file_name);
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file_name));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_name));
		ob_clean();
		flush();
		
		readfile($file_name);
		unlink($file_name);
		
		exit();
	}
	
	public function buyerEditAction()
	{
		$id = $this->params('id');
		$arrWhere = array("enq_id" => $id);
		$arrData = $this->getBuyerEnquiryTable()->fetchBuyerEnquiryDetail($arrWhere);
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			
			$arrEnq = array("comments"=>$arrPost['comments'],
							"status"=>$arrPost['status']);
			$arrWhereEnq = array("enq_id" => $arrPost['enq_id']);
			$this->getBuyerEnquiryTable()->updateBuyerInquiry($arrEnq,$arrWhereEnq);
			
			$this->updateCustomerDetail($arrPost);
			return $this->redirect()->toRoute('enquiry',array("action"=>"index"));
		}
		$form = new SaveForm('save-buyer',array(),$arrData[0]);
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrData' => $arrData[0],
			'form' => $form
			));
		return $viewModel;
	}
	
	public function buyerAddAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			
			if($arrPost['customer_id'] > 0)
			{
				$this->updateCustomerDetail($arrPost);
			}
			else
			{
				$arrPost['customer_id'] = $this->insertCustomerDetail($arrPost);
			}
			
			$arrEnq = array("comments"=>$arrPost['comments'],
							"status"=>$arrPost['status'],
							"mmv_id"=>$arrPost['mmv_id'],
							"stock_id"  => 0,
							"customer_id"=>$arrPost['customer_id']);

			$this->getBuyerEnquiryTable()->insertBuyerInquiry($arrEnq);
			return $this->redirect()->toRoute('enquiry',array("action"=>"index"));
		}
		$form = new SaveAddForm('save-buyer');
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form,
			'start_year' =>$start_year
			));
		return $viewModel;
	}

	public function sellerEditAction()
	{
		$id = $this->params('id');
		$arrWhere = array("enq_id" => $id);
		$arrData = $this->getSellerEnquiryTable()->fetchSellerEnquiryDetail($arrWhere);
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			
			$arrEnq = array("make_year"=>$arrPost['year'],
							"make_id"=>$arrPost['make'],
							"model_id"=>$arrPost['model'],
							"kilometre"=>$arrPost['kilometre'],
							"registration_place"=>$arrPost['regcity'],
							"comments"=>$arrPost['comments'],
							"status"=>$arrPost['status']);
			$arrWhereEnq = array("enq_id" => $arrPost['enq_id']);
			$this->getSellerEnquiryTable()->updateSellerInquiry($arrEnq,$arrWhereEnq);
			
			$this->updateCustomerDetail($arrPost);
			return $this->redirect()->toRoute('enquiry',array("action"=>"seller"));
		}
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		
		$arr_fixed_values['make_array'] = $this->getMmvDetailTable()->getMmvMakeList($arrData[0]['make_year']);
		$arr_fixed_values['model_array'] = $this->getMmvDetailTable()->getMmvModelList($arrData[0]['make_id'],$arrData[0]['make_year']);
		$arr_fixed_values['regcity'] = Fixedvalues::get_reg_city();
		$form = new SaveForm('save-seller',array(),$arrData[0]);
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrData' => $arrData[0],
			'form' => $form,
			'start_year'=>$start_year,
			'arr_fixed_values' => $arr_fixed_values,
			));
		return $viewModel;
	}
	
	public function sellerAddAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			
			if($arrPost['customer_id'] > 0)
			{
				$this->updateCustomerDetail($arrPost);
			}
			else
			{
				$arrPost['customer_id'] = $this->insertCustomerDetail($arrPost);
			}
			
			$arrEnq = array("make_year"=>$arrPost['year'],
							"make_id"=>$arrPost['make'],
							"model_id"=>$arrPost['model'],
							"kilometre"=>$arrPost['kilometre'],
							"registration_place"=>$arrPost['regcity'],
							"comments"=>$arrPost['comments'],
							"status"=>$arrPost['status'],
							"customer_id"=>$arrPost['customer_id']);
			
			$this->getSellerEnquiryTable()->insertSellerInquiry($arrEnq);
			
			
			return $this->redirect()->toRoute('enquiry',array("action"=>"seller"));
		}
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		$arr_fixed_values['regcity'] = Fixedvalues::get_reg_city();
		$form = new SaveAddForm('save-seller');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			
			'form' => $form,
			'start_year'=>$start_year,
			'arr_fixed_values' => $arr_fixed_values,
			));
		return $viewModel;
	}
	
	public function getCustomerAction()
	{
		 $request = $this->getRequest();
		 $arrData = $request->getPost(); 
		
		 $arrCustomer = $this->getEnquiryCustomerTable()->getCustomerDetail($arrData);
		 if(!empty($arrCustomer))
		 {
			 $arrCustomerDetail[0] = $arrCustomer[0];
		 }
		 else
		 {
			 $arrCustomerDetail[0]['customer_id'] = 0;
		 }
		 echo json_encode($arrCustomerDetail); die;
		
	}
	
	
	public function generalEditAction()
	{
		$id = $this->params('id');
		$arrWhere = array("enq_id" => $id);
		$arrData = $this->getGeneralEnquiryTable()->fetchGeneralEnquiryDetail($arrWhere);
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			
			$arrEnq = array("comments"=>$arrPost['comments'],
							"status"=>$arrPost['status']);
			$arrWhereEnq = array("enq_id" => $arrPost['enq_id']);
			$this->getGeneralEnquiryTable()->updateGenralInquiry($arrEnq,$arrWhereEnq);
			$this->updateCustomerDetail($arrPost);
			return $this->redirect()->toRoute('enquiry',array("action"=>"general"));
		}
		$form = new SaveForm('save-general',array(),$arrData[0]);
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrData' => $arrData[0],
			'form' => $form
			));
		return $viewModel;
	}
	
	public function updateCustomerDetail($arrPost)
	{
		$arrCus = array("name"=>$arrPost['name'],
						"email"=>$arrPost['email'],
						"mobile"=>$arrPost['mobile'],
						"customer_address"=>$arrPost['customer_address'],
						"customer_city"=>$arrPost['customer_city'],
						"customer_state"=>$arrPost['customer_state'],
						"customer_pin"=>$arrPost['customer_pin'],
						"gender"=>$arrPost['gender'],
						"profession"=>$arrPost['profession'],
						"annual_income"=>$arrPost['annual_income']);
			
		$arrWhereCus = array("customer_id" => $arrPost['customer_id']);
		$this->getEnquiryCustomerTable()->updateCustomer($arrCus,$arrWhereCus);
	}
	
	public function insertCustomerDetail($arrPost)
	{
		$arrCus = array("name"=>$arrPost['name'],
						"email"=>$arrPost['email'],
						"mobile"=>$arrPost['mobile'],
						"customer_address"=>$arrPost['customer_address'],
						"customer_city"=>$arrPost['customer_city'],
						"customer_state"=>$arrPost['customer_state'],
						"customer_pin"=>$arrPost['customer_pin'],
						"gender"=>$arrPost['gender'],
						"profession"=>$arrPost['profession'],
						"annual_income"=>$arrPost['annual_income']);
			
		
		$customer_id= $this->getEnquiryCustomerTable()->insertCustomer($arrCus);
		return $customer_id;
	}
	
	public function getEnquiryCustomerTable()
	{
		if (!$this->enquiryCustomerTable) {
            $sm = $this->getServiceLocator();
            $this->enquiryCustomerTable = $sm->get('Enquiry\Model\EnquiryCustomerTable');
        }
        return $this->enquiryCustomerTable;
	}
	public function getBuyerEnquiryTable()
	{
		if (!$this->buyerEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->buyerEnquiryTable = $sm->get('Enquiry\Model\BuyerEnquiryTable');
        }
        return $this->buyerEnquiryTable;
	}
	
	public function getGeneralEnquiryTable()
	{
		if (!$this->generalEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->generalEnquiryTable = $sm->get('Enquiry\Model\GeneralEnquiryTable');
        }
        return $this->generalEnquiryTable;
	}
	
	public function getSellerEnquiryTable()
	{
		if (!$this->sellerEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->sellerEnquiryTable = $sm->get('Enquiry\Model\SellerEnquiryTable');
        }
        return $this->sellerEnquiryTable;
	}
	
	public function getMmvDetailTable()
	{
		if (!$this->mmvDetailTable) {
            $sm = $this->getServiceLocator();
            $this->mmvDetailTable = $sm->get('Mmv\Model\MmvDetailTable');
        }
        return $this->mmvDetailTable;
	}
	
}
