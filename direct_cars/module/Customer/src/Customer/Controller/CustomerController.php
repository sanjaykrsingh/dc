<?php

namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Customer\Form\SaveForm;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Session\Container as SessionContainer;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;


class CustomerController extends AbstractActionController {

	protected $customerMasterTable;

	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {

        $sm = $this->getServiceLocator();
        $auth = $sm->get('zfcuserauthservice');
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser');
        }
        return parent::onDispatch($e);
    }

	
	
	
    public function indexAction() {
		$this->session = new SessionContainer('post_search');
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'cust_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'ASC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrWhere = array();
		
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
		
		$arrTestimonial = $this->getCustomerMasterTable()->fetchCustomerList($arrPost,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrTestimonial));

        $paginator->setCurrentPageNumber($arrParam['page'])
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

		$current = $arrParam['page'];
		$previous = (int) ($arrParam['page'] == 1 ) ? null : $arrParam['page'] - 1;
		$next = (int) ($arrParam['page'] == $paginator->count()) ? null : $arrParam['page'] + 1;
		
		$range = (int)($arrParam['page'] / 7) ; 
		$lower_bound = (($range)*7) + ($range - 1);
		$upper_bound = (($range)*7) + 7;
		$config = $this->getServiceLocator()->get('Config');
		return new ViewModel(array(
            'arrCustomer' => $paginator,
			'order_by' => $arrParam['order_by'],
            'order' => $arrParam['order'],
            'page' => $arrParam['page'],
			'current' => $current,
			'first' => 1,
			'config' => $config,
			'last' => $paginator->count(),
			'next' => $next,
			'previous' => $previous,
			'pagesInRange' => $paginator->getPagesInRange($lower_bound,$upper_bound),
			'pageCount' => $paginator->count(),
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrPost'=> $arrPost,
			));
    }

	public function addAction() {
		$form = new SaveForm('save-customer');
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			// Make certain to merge the files info!
			$post = $request->getPost()->toArray();
			
			$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail("customer_email = '".$post['customer_email']."' OR customer_mobile = '".$post['customer_mobile']."'");
			
			if(count($arrCustomer) > 0)
			{
				$this->flashMessenger()->addMessage('Email or mobile no  already exist.');
				return $this->redirect()->toRoute('customer',array('action'=>"add"));
			}
			else
			{
				if($post['customer_source'] == 'Other')		$post['customer_source'] = $post['other_customer_source'];
				// Insert Data in DB
				$arrData = array("customer_id" => $post['customer_id'],
								 "customer_name" => $post['customer_name'],
								 "customer_initial" => $post['customer_initial'],
								 "customer_email" => $post['customer_email'],
								 "customer_mobile" => $post['customer_mobile'],
								 "customer_alt_mobile" => $post['customer_alt_mobile'],
								 "customer_dob" => $post['customer_dob'],
								 "customer_source" => $post['customer_source'],
								 "customer_type" => $post['customer_type'],
								 "customer_address" => $post['customer_address'],
								 "customer_state" => $post['customer_state'],
								 "customer_city" => $post['customer_city'],
								 "customer_pin" => $post['customer_pin'],
								 "customer_contact" => $post['customer_contact'],
								 "customer_company" => $post['customer_company'],
								 "customer_status" => $post['customer_status']);

				$this->getCustomerMasterTable()->insertCustomer($arrData);
				return $this->redirect()->toRoute('customer');
			}
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('customer/customer/save.phtml');
		return  $viewModel;
    }
	
	public function editAction() {
		
		$id = $this->params('id');
		$arrWhere = array("cust_id" => $id);
		$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
		
		$form = new SaveForm('save-customer',array(), $arrCustomer[0]);
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			// Make certain to merge the files info!
			$post = $request->getPost()->toArray();
			
			$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail("(customer_email = '".$post['customer_email']."' OR customer_mobile = '".$post['customer_mobile']."') AND cust_id != ".$id);
			
			if(count($arrCustomer) > 0)
			{
				$this->flashMessenger()->addMessage('Email or mobile no  already exist.');
				return $this->redirect()->toRoute('customer',array("action" => "edit","id" => $id));
			}
			else
			{
				if($post['customer_source'] == 'Other')		$post['customer_source'] = $post['other_customer_source'];
				// Insert Data in DB
				$arrData = array("customer_id" => $post['customer_id'],
								 "customer_name" => $post['customer_name'],
								 "customer_initial" => $post['customer_initial'],
								 "customer_email" => $post['customer_email'],
								 "customer_mobile" => $post['customer_mobile'],
								 "customer_alt_mobile" => $post['customer_alt_mobile'],
								 "customer_dob" => $post['customer_dob'],
								 "customer_source" => $post['customer_source'],
								 "customer_type" => $post['customer_type'],
								 "customer_address" => $post['customer_address'],
								 "customer_state" => $post['customer_state'],
								 "customer_city" => $post['customer_city'],
								 "customer_pin" => $post['customer_pin'],
								 "customer_contact" => $post['customer_contact'],
								 "customer_company" => $post['customer_company'],
								 "customer_status" => $post['customer_status']);

				$this->getCustomerMasterTable()->updateCustomer($arrData,$arrWhere);
				return $this->redirect()->toRoute('customer');
			}
		
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('customer/customer/save.phtml');
		return  $viewModel;
    }
	
	public function getCustomerAction()
	{
		 $request = $this->getRequest();
		 $arrData = $request->getPost(); 
		
		 $arrCustomer = $this->getCustomerMasterTable()->getCustomerDetail($arrData);
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
	
	public function getCustomerMasterTable()
	{
		if (!$this->customerMasterTable) {
            $sm = $this->getServiceLocator();
            $this->customerMasterTable = $sm->get('Customer\Model\CustomerMasterTable');
        }
        return $this->customerMasterTable;
	}
}
