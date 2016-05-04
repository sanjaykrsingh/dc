<?php

namespace Rctransfer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Rctransfer\Form\SaveForm;
use Rctransfer\Form\RCTLoginForm;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Session\Container as SessionContainer;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Fixedvalues;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;

class RctransferController extends AbstractActionController {

	protected $agentMasterTable;
	protected $rcTransferTable;
	protected $rcTransferLogTable;
	protected $customerMasterTable;
	protected $stockMasterTable;

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


	public function createAgentAction() {
		$form = new SaveForm('save-agent');
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			// Make certain to merge the files info!
			$post = $request->getPost()->toArray();
			
			$arrAgent = $this->getAgentMasterTable()->fetchAgentDetail("agent_email = '".$post['agent_email']."' OR agent_mobile = '".$post['agent_mobile']."'");
			
			if(count($arrAgent) > 0)
			{
				$this->flashMessenger()->addMessage('Email or mobile no  already exist.');
				return $this->redirect()->toRoute('rctransfer',array('action'=>"create-agent"));
			}
			else
			{
				// Insert Data in DB
				$arrData = array("agent_name" => $post['agent_name'],
								 "agent_number" => $post['agent_number'],
								 "representative" => $post['representative'],
								 "agent_mobile" => $post['agent_mobile'],
								 "agent_alt_mobile" => $post['agent_alt_mobile'],
								 "agent_email" => $post['agent_email'],
								 "agent_address" => $post['agent_address'],
								 "agent_state" => $post['agent_state'],
								 "agent_city" => $post['agent_city'],
								 "agent_pin_code" => $post['agent_pin_code']);

				$this->getAgentMasterTable()->insertAgent($arrData);
				$this->flashMessenger()->addMessage('Agent Added.');
				return $this->redirect()->toRoute('rctransfer',array("action"=>"create-agent"));
			}
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('rctransfer/rctransfer/save.phtml');
		return  $viewModel;
    }
	
	public function getAgentAction()
	{
		 $request = $this->getRequest();
		 $arrData = $request->getPost(); 
		 if(isset($arrData->agent_mobile))
		 {
			$arrWhere = array("agent_mobile" => $arrData->agent_mobile);
		 } else if(isset($arrData->agent_number))
 		 {
			$arrWhere = array("agent_number" => $arrData->agent_number);
		 }
		
		 $arrAgent = $this->getAgentMasterTable()->fetchAgentDetail($arrWhere);
		 if(!empty($arrAgent))
		 {
			 $arrAgentDetail[0] = $arrAgent[0];
		 }
		 else
		 {
			 $arrAgentDetail[0]['agent_id'] = 0;
		 }
		 echo json_encode($arrAgentDetail); die;
		
	}
	
	public function loginAction() {
		$config = $this->getServiceLocator()->get('Config');
		$form = new SaveForm('save-agent');
		$request = $this->getRequest();
		if ($request->isPost()) {
			 $post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			ini_set("memory_limit","1024M");
			
			foreach($_FILES as $fieldname=>$file)
			{
				if($post[$fieldname]['name'] != "") {	
					$post[$fieldname]['name'] = rand().$post[$fieldname]['name'];
					$post[$fieldname]['name'] = str_replace(" ","_",$post[$fieldname]['name']);
					copy($post[$fieldname]['tmp_name'],$config['public_folder_path']."images/rctransfer/".$post[$fieldname]['name']);
				}
			}
			if($post['financier_name'] == 'other')	$post['financier_name'] = $post['txt_financier_name'];
			if($post['buyer_financiers_name'] == 'other')	$post['buyer_financiers_name'] = $post['txt_buyer_financiers_name'];
			if($post['hypothecation'] == 0)		 $post['financier_name'] = '';
			if($post['hpa'] == 'No')	$post['buyer_financiers_name'] = '';
			//update inventory data...
			$arrStockUpdate = array("hypothecation" => $post['hypothecation'],
									"seller_financiers_name" => $post['financier_name'],
									"purchase_date" => ($post['purchase_date'] != '')?date("Y-m-d",strtotime($post['purchase_date'])):'',
									"seller_id" => $post['seller_id'],
									"delivery_date" => ($post['delivery_date'] != '')?date("Y-m-d",strtotime($post['delivery_date'])):'',
									"delivered_by" => $post['delivered_by'],
									"buyer_id" => $post['buyer_id'],
									"hpa" => ($post['hpa'] == 'Yes')?1:0,
									"buyer_financiers_name" => $post['buyer_financiers_name']);
			$this->getStockMasterTable()->updateStock($arrStockUpdate,array("stock_id" => $post['stock_id']));
			
			//insert rc transfer data...
			$arrData = array("stock_id" => $post['stock_id'],
							 "seller_id" => $post['seller_id'],
							 "original_rc_file" => $post['original_rc_file']['name'],
							 "original_rc_file_status" => $post['original_rc_file_status'],
							 "original_rc_date" => ($post['original_rc_date'] != '')?date("Y-m-d",strtotime($post['original_rc_date'])):'',
							 "insurance_file" => $post['insurance_file']['name'],
							 "insurance_file_status" => $post['insurance_file_status'],
							 "insurance_date" => ($post['insurance_date'] != '')?date("Y-m-d",strtotime($post['insurance_date'])):'',
							 "tto_set_file" => $post['tto_set_file']['name'],
							 "tto_set_file_status" => $post['tto_set_file_status'],
							 "tto_set_date" => ($post['tto_set_date'] != '')?date("Y-m-d",strtotime($post['tto_set_date'])):'',
							 "form35_file" => $post['form35_file']['name'],
							 "form35_file_status" => $post['form35_file_status'],
							 "form35_date" => ($post['form35_date'] != '')?date("Y-m-d",strtotime($post['form35_date'])):'',
							 "affidavit_file" => $post['affidavit_file']['name'],
							 "affidavit_file_status" => $post['affidavit_file_status'],
							 "affidavit_date" => ($post['affidavit_date'] != '')?date("Y-m-d",strtotime($post['affidavit_date'])):'',
							 "letter_head_file" => $post['letter_head_file']['name'],
							 "letter_head_file_status" => $post['letter_head_file_status'],
							 "letter_head_date" => ($post['letter_head_date'] != '')?date("Y-m-d",strtotime($post['letter_head_date'])):'',
							 "id_proof_1_file" => $post['id_proof_1_file']['name'],
							 "id_proof_1_file_status" => $post['id_proof_1_file_status'],
							 "id_proof_1_date" => ($post['id_proof_1_date'] != '')?date("Y-m-d",strtotime($post['id_proof_1_date'])):'',
							 "id_proof_2_file" => $post['id_proof_2_file']['name'],
							 "id_proof_2_file_status" => $post['id_proof_2_file_status'],
							 "id_proof_2_date" => ($post['id_proof_2_date'] != '')?date("Y-m-d",strtotime($post['id_proof_2_date'])):'',
							 "address_proof_1_file" => $post['address_proof_1_file']['name'],
							 "address_proof_1_file_status" => $post['address_proof_1_file_status'],
							 "address_proof_1_date" => ($post['address_proof_1_date'] != '')?date("Y-m-d",strtotime($post['address_proof_1_date'])):'',
							 "address_proof_2_file" => $post['address_proof_2_file']['name'],
							 "address_proof_2_file_status" => $post['address_proof_2_file_status'],
							 "address_proof_2_date" => ($post['address_proof_2_date'] != '')?date("Y-m-d",strtotime($post['address_proof_2_date'])):'',
							 "buyer_id" => $post['buyer_id'],
							 "buyer_id_proof_1_file" => $post['buyer_id_proof_1_file']['name'],
							 "buyer_id_proof_1_file_status" => $post['buyer_id_proof_1_file_status'],
							 "buyer_id_proof_1_date" => ($post['buyer_id_proof_1_date'] != '')?date("Y-m-d",strtotime($post['buyer_id_proof_1_date'])):'',
							 "buyer_id_proof_2_file" => $post['buyer_id_proof_2_file']['name'],
							 "buyer_id_proof_2_file_status" => $post['buyer_id_proof_2_file_status'],
							 "buyer_id_proof_2_date" => ($post['buyer_id_proof_2_date'] != '')?date("Y-m-d",strtotime($post['buyer_id_proof_2_date'])):'',
							 "buyer_address_proof_1_file" => $post['buyer_address_proof_1_file']['name'],
							 "buyer_address_proof_1_file_status" => $post['buyer_address_proof_1_file_status'],
							 "buyer_address_proof_1_date" => ($post['buyer_address_proof_1_date'] != '')?date("Y-m-d",strtotime($post['buyer_address_proof_1_date'])):'',
							 "buyer_address_proof_2_file" => $post['buyer_address_proof_2_file']['name'],
							 "buyer_address_proof_2_file_status" => $post['buyer_address_proof_2_file_status'],
							 "buyer_address_proof_2_date" => ($post['buyer_address_proof_2_date'] != '')?date("Y-m-d",strtotime($post['buyer_address_proof_2_date'])):'',
							 "puc_file" => $post['puc_file']['name'],
							 "puc_file_status" => $post['puc_file_status'],
							 "puc_date" => ($post['puc_date'] != '')?date("Y-m-d",strtotime($post['puc_date'])):'',
							 "transfer_by" => $post['transfer_by'],
							 "transfer_type" => $post['transfer_type'],
							 "duplicate_rc" => $post['duplicate_rc'],
							 "hpa" => $post['hpa'],
							 "buyer_financiers_name" => $post['buyer_financiers_name'],
							 "transfer_from" => $post['transfer_from'],
							 "transfer_to" => $post['transfer_to'],
							 "cng_addition" => $post['cng_addition'],
							 "cng_vendor" => $post['cng_vendor'],
							 "agent_id" => $post['agent_id'],
							 "file_status" => $post['file_status'],
							 "login_date" => ($post['login_date'] != '')?date("Y-m-d",strtotime($post['login_date'])):'',
							 "login_remarks" => $post['login_remarks'],
							 "transfer_status" => $post['transfer_status'],
							 "transfer_date" => ($post['transfer_date'] != '')?date("Y-m-d",strtotime($post['transfer_date'])):'',
							 "discrepency_details" => $post['discrepency_details'],
							 "resubmission_date" => ($post['resubmission_date'] != '')?date("Y-m-d",strtotime($post['resubmission_date'])):'',
							 "resubmission_remarks" => $post['resubmission_remarks'],
							 "hypothecation" => $post['hypothecation'],
							 "seller_financiers_name" => $post['financier_name'],
							 "purchase_date" => ($post['purchase_date'] != '')?date("Y-m-d",strtotime($post['purchase_date'])):'',
							 "delivery_date" => ($post['delivery_date'] != '')?date("Y-m-d",strtotime($post['delivery_date'])):'',
							 "delivered_by" => $post['delivered_by'],
							 );
							 
			
			$rc_transfer_id = $this->getRcTransferTable()->insertRc($arrData);
			$this->flashMessenger()->addMessage('RC Login Data Added.');
			
			if($post['transfer_status'] == 'Resubmitted')
			{
				// Notify buyer
				$this->notify_relog_in_transfer($post,$post['buyer_id'],"Buyer",$rc_transfer_id);
			}
			else if($post['transfer_status'] == "Descrepency")
			{
				// notify buyer
				$this->notify_descripency($post,$post['buyer_id'],"Buyer",$rc_transfer_id);
			}
			else if($post['transfer_status'] == "Transferred")
			{
				// notify buyer
				$this->notify_transfered($post,$post['buyer_id'],"Buyer",$rc_transfer_id,$post['transfer_file']);
				//notify seller
				$this->notify_transfered($post,$post['seller_id'],"Seller",$rc_transfer_id,$post['transfer_file']);
			}
			else if($post['login_date'] != '')
			{
				//notify seller
				$this->notify_log_in_transfer($post,$post['seller_id'],"Seller",$rc_transfer_id);
				// Notify buyer
				$this->notify_log_in_transfer($post,$post['buyer_id'],"Buyer",$rc_transfer_id);
			}
			
			return $this->redirect()->toRoute('rctransfer',array("action"=>"login"));
		}
		$form = new RCTLoginForm('rct-login',array("enctype"=>"multipart/form-data"));
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('rctransfer/rctransfer/login.phtml');
		return  $viewModel;
    }
	
	public function notify_transfered($post,$customer_id,$user_type,$rc_transfer_id,$attachmentname)
	{
		$arrWhere = array("cust_id" => $customer_id);
		$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
		$subject = "Intimation of Transferred RC";
		if($user_type == 'Buyer') {
			$message = "Your Car[".$post['reg_no']."] is transfered. Download the Copy of Transferred RC in your Email. Pls Collect Original from Showroom. DIRECT CARS";
		} else {
			$message = "Your Car[".$post['reg_no']."] is transfered. Download the Copy of Transferred RC in your Email. DIRECT CARS";
		}
		Fixedvalues::sendSMS($message,$arrCustomer[0]['customer_mobile']);
		$result = Fixedvalues::sendmailattachment($message,$subject,$arrCustomer[0]['customer_email'],$attachmentname);
		
		$arrLogData = array("rc_transfer_id" =>  $rc_transfer_id,
							"user_type" => $user_type,
							"subject" => $subject,
							"message" => $message,
							"email" => $arrCustomer[0]['customer_email'],
							"mobile" => $arrCustomer[0]['customer_mobile']);
		$this->getRcTransferLogTable()->insertRcLog($arrLogData);
	}
	
	public  function notify_descripency($post,$customer_id,$user_type,$rc_transfer_id)
	{
		$arrWhere = array("cust_id" => $customer_id);
		$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
		$subject = "Intimation of Discrepency";
		$message = "Discrepency in RC Transfer of Car [".$post['reg_no']."].".$post['discrepency_details'].". <br><br>DIRECT CARS";
		Fixedvalues::sendSMS($message,$arrCustomer[0]['customer_mobile']);
		$result = Fixedvalues::sendmail($message,$subject,$arrCustomer[0]['customer_email']);
		
		$arrLogData = array("rc_transfer_id" =>  $rc_transfer_id,
							"user_type" => $user_type,
							"subject" => $subject,
							"message" => $message,
							"email" => $arrCustomer[0]['customer_email'],
							"mobile" => $arrCustomer[0]['customer_mobile']);
		$this->getRcTransferLogTable()->insertRcLog($arrLogData);
	}
	
	public function notify_log_in_transfer($post,$customer_id,$user_type,$rc_transfer_id)
	{
		$arrWhere = array("cust_id" => $customer_id);
		$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
		$subject = "Intimation of Log-in For Transfer";
		$message = "Your Car[".$post['reg_no']."] is logged in for transfer. Copy of Transferred RC shall be sent once it is done. <br><br>DIRECT CARS";
		Fixedvalues::sendSMS($message,$arrCustomer[0]['customer_mobile']);
		$result = Fixedvalues::sendmail($message,$subject,$arrCustomer[0]['customer_email']);
		
		$arrLogData = array("rc_transfer_id" =>  $rc_transfer_id,
							"user_type" => $user_type,
							"subject" => $subject,
							"message" => $message,
							"email" => $arrCustomer[0]['customer_email'],
							"mobile" => $arrCustomer[0]['customer_mobile']);
		$this->getRcTransferLogTable()->insertRcLog($arrLogData);
	}
	
	public function notify_relog_in_transfer($post,$customer_id,$user_type,$rc_transfer_id)
	{
		$arrWhere = array("cust_id" => $customer_id);
		$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
		$subject = "Intimation of Re-Login";
		$message = "Your Car[".$post['reg_no']."] is Re-logged in for transfer. Shall update status once RC is Transferred. <br><br>DIRECT CARS";
		Fixedvalues::sendSMS($message,$arrCustomer[0]['customer_mobile']);
		$result = Fixedvalues::sendmail($message,$subject,$arrCustomer[0]['customer_email']);
		
		$arrLogData = array("rc_transfer_id" =>  $rc_transfer_id,
							"user_type" => $user_type,
							"subject" => $subject,
							"message" => $message,
							"email" => $arrCustomer[0]['customer_email'],
							"mobile" => $arrCustomer[0]['customer_mobile']);
		$this->getRcTransferLogTable()->insertRcLog($arrLogData);
	}
	
	public function getRcDetailAction(){
		
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$strwhere = "rc_transfer_id  = '".$arrData->rct_id."'";
		$arrStockData = $this->getRcTransferTable()->fetchRctransferDetail($strwhere);
		echo json_encode($arrStockData); die;
	}
	
	public function getVehicleListAction(){
		$request = $this->getRequest();
		$arrData = $request->getPost();		
		$strWhere = "registration_no = ".$arrData->reg_no;
		$arrStockData = $this->getRcTransferTable()->getStockList($strWhere);
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrStockData'=>$arrStockData,
			
			));
		$viewModel->setTemplate('rctransfer/rctransfer/get-rct-list.phtml');
		$viewModel->setTerminal(true);
		return $viewModel;
	}
	
	public function getInventoryListAction() {
		$request = $this->getRequest();
		$arrData = $request->getPost();		
		$strWhere = "registration_no = ".$arrData->reg_no;
		
		$arrStockData = $this->getRcTransferTable()->getLoginStockList($strWhere);
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrStockData'=>$arrStockData,
			
			));
		$viewModel->setTemplate('rctransfer/rctransfer/get-inventory-list.phtml');
		$viewModel->setTerminal(true);
		return $viewModel;
	}
	
	public function viewAction() {
		$config = $this->getServiceLocator()->get('Config');
		$form = new SaveForm('save-agent');
		$request = $this->getRequest();
		if ($request->isPost()) {
			 $post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			ini_set("memory_limit","1024M");
			$arrFile = array();
			foreach($_FILES as $fieldname=>$file)
			{
				if($post[$fieldname]['name'] != "") {	
					$post[$fieldname]['name'] = rand().$post[$fieldname]['name'];
					$post[$fieldname]['name'] = str_replace(" ","_",$post[$fieldname]['name']);
					copy($post[$fieldname]['tmp_name'],$config['public_folder_path']."images/rctransfer/".$post[$fieldname]['name']);
					$arrFile = array_merge($arrFile,array($fieldname=>$post[$fieldname]['name']));
				}
			}
			
			if($post['financier_name'] == 'other')	$post['financier_name'] = $post['txt_financier_name'];
			if($post['buyer_financiers_name'] == 'other')	$post['buyer_financiers_name'] = $post['txt_buyer_financiers_name'];
			if($post['hypothecation'] == 0)		 $post['financier_name'] = '';
			if($post['hpa'] == 'No')	$post['buyer_financiers_name'] = '';
			//update inventory data...
			$arrStockUpdate = array("hypothecation" => $post['hypothecation'],
									"seller_financiers_name" => $post['financier_name'],
									"purchase_date" => ($post['purchase_date'] != '')?date("Y-m-d",strtotime($post['purchase_date'])):'',
									"seller_id" => $post['seller_id'],
									"delivery_date" => ($post['delivery_date'] != '')?date("Y-m-d",strtotime($post['delivery_date'])):'',
									"delivered_by" => $post['delivered_by'],
									"buyer_id" => $post['buyer_id'],
									"hpa" => ($post['hpa'] == 'Yes')?1:0,
									"buyer_financiers_name" => $post['buyer_financiers_name']);
			$this->getStockMasterTable()->updateStock($arrStockUpdate,array("stock_id" => $post['stock_id']));
			
			//Update RC transfer Data
			$arrData = array("stock_id" => $post['stock_id'],
							 "seller_id" => $post['seller_id'],
							 "original_rc_file_status" => $post['original_rc_file_status'],
							 "original_rc_date" => ($post['original_rc_date'] != '')?date("Y-m-d",strtotime($post['original_rc_date'])):'',
							 "insurance_file_status" => $post['insurance_file_status'],
							 "insurance_date" => ($post['insurance_date'] != '')?date("Y-m-d",strtotime($post['insurance_date'])):'',
							 "tto_set_file_status" => $post['tto_set_file_status'],
							 "tto_set_date" => ($post['tto_set_date'] != '')?date("Y-m-d",strtotime($post['tto_set_date'])):'',
							 "form35_file_status" => $post['form35_file_status'],
							 "form35_date" => ($post['form35_date'] != '')?date("Y-m-d",strtotime($post['form35_date'])):'',
							 "affidavit_file_status" => $post['affidavit_file_status'],
							 "affidavit_date" => ($post['affidavit_date'] != '')?date("Y-m-d",strtotime($post['affidavit_date'])):'',
							 "letter_head_file_status" => $post['letter_head_file_status'],
							 "letter_head_date" => ($post['letter_head_date'] != '')?date("Y-m-d",strtotime($post['letter_head_date'])):'',
							 "id_proof_1_file_status" => $post['id_proof_1_file_status'],
							 "id_proof_1_date" => ($post['id_proof_1_date'] != '')?date("Y-m-d",strtotime($post['id_proof_1_date'])):'',
							 "id_proof_2_file_status" => $post['id_proof_2_file_status'],
							 "id_proof_2_date" => ($post['id_proof_2_date'] != '')?date("Y-m-d",strtotime($post['id_proof_2_date'])):'',
							 "address_proof_1_file_status" => $post['address_proof_1_file_status'],
							 "address_proof_1_date" => ($post['address_proof_1_date'] != '')?date("Y-m-d",strtotime($post['address_proof_1_date'])):'',
							 "address_proof_2_file_status" => $post['address_proof_2_file_status'],
							 "address_proof_2_date" => ($post['address_proof_2_date'] != '')?date("Y-m-d",strtotime($post['address_proof_2_date'])):'',
							 "buyer_id" => $post['buyer_id'],
							 "buyer_id_proof_1_file_status" => $post['buyer_id_proof_1_file_status'],
							 "buyer_id_proof_1_date" => ($post['buyer_id_proof_1_date'] != '')?date("Y-m-d",strtotime($post['buyer_id_proof_1_date'])):'',
							 "buyer_id_proof_2_file_status" => $post['buyer_id_proof_2_file_status'],
							 "buyer_id_proof_2_date" => ($post['buyer_id_proof_2_date'] != '')?date("Y-m-d",strtotime($post['buyer_id_proof_2_date'])):'',
							 "buyer_address_proof_1_file_status" => $post['buyer_address_proof_1_file_status'],
							 "buyer_address_proof_1_date" => ($post['buyer_address_proof_1_date'] != '')?date("Y-m-d",strtotime($post['buyer_address_proof_1_date'])):'',
							 "buyer_address_proof_2_file_status" => $post['buyer_address_proof_2_file_status'],
							 "buyer_address_proof_2_date" => ($post['buyer_address_proof_2_date'] != '')?date("Y-m-d",strtotime($post['buyer_address_proof_2_date'])):'',
							 "puc_file_status" => $post['puc_file_status'],
							 "puc_date" => ($post['puc_date'] != '')?date("Y-m-d",strtotime($post['puc_date'])):'',
							 "transfer_by" => $post['transfer_by'],
							 "transfer_type" => $post['transfer_type'],
							 "duplicate_rc" => $post['duplicate_rc'],
							 "hpa" => $post['hpa'],
							 "buyer_financiers_name" => $post['buyer_financiers_name'],
							 "transfer_from" => $post['transfer_from'],
							 "transfer_to" => $post['transfer_to'],
							 "cng_addition" => $post['cng_addition'],
							 "cng_vendor" => $post['cng_vendor'],
							 "agent_id" => $post['agent_id'],
							 "file_status" => $post['file_status'],
							 "login_date" => ($post['login_date'] != '')?date("Y-m-d",strtotime($post['login_date'])):'',
							 "login_remarks" => $post['login_remarks'],
							 "transfer_status" => $post['transfer_status'],
							 "transfer_date" => ($post['transfer_date'] != '')?date("Y-m-d",strtotime($post['transfer_date'])):'',
							 "discrepency_details" => $post['discrepency_details'],
							 "resubmission_date" => ($post['resubmission_date'] != '')?date("Y-m-d",strtotime($post['resubmission_date'])):'',
							 "resubmission_remarks" => $post['resubmission_remarks'],
							 "hypothecation" => $post['hypothecation'],
							 "seller_financiers_name" => $post['financier_name'],
							 "purchase_date" => ($post['purchase_date'] != '')?date("Y-m-d",strtotime($post['purchase_date'])):'',
							 "delivery_date" => ($post['delivery_date'] != '')?date("Y-m-d",strtotime($post['delivery_date'])):'',
							 "delivered_by" => $post['delivered_by'],
							 );
				$arrData = array_merge($arrData,$arrFile);
			
			$arrWhere = array("rc_transfer_id"=>$post['rct_id']);
			$this->getRcTransferTable()->updateRc($arrData,$arrWhere);
			$this->flashMessenger()->addMessage('RC Login Data Updated.');
			$rc_transfer_id = $post['rct_id'];
			if($post['transfer_status'] == 'Resubmitted')
			{
				// Notify buyer
				$this->notify_relog_in_transfer($post,$post['buyer_id'],"Buyer",$rc_transfer_id);
			}
			else if($post['transfer_status'] == "Descrepency")
			{
				// notify buyer
				$this->notify_descripency($post,$post['buyer_id'],"Buyer",$rc_transfer_id);
			}
			else if($post['transfer_status'] == "Transferred")
			{
				// notify buyer
				$this->notify_transfered($post,$post['buyer_id'],"Buyer",$rc_transfer_id,$post['transfer_file']);
				//notify seller
				$this->notify_transfered($post,$post['seller_id'],"Seller",$rc_transfer_id,$post['transfer_file']);
			}
			else if($post['login_date'] != '')
			{
				//notify seller
				$this->notify_log_in_transfer($post,$post['seller_id'],"Seller",$rc_transfer_id);
				// Notify buyer
				$this->notify_log_in_transfer($post,$post['buyer_id'],"Buyer",$rc_transfer_id);
			}
			
			return $this->redirect()->toRoute('rctransfer',array("action"=>"view"));
		}
		$form = new RCTLoginForm('rct-login',array("enctype"=>"multipart/form-data"));
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('rctransfer/rctransfer/view.phtml');
		return  $viewModel;
    }
	
	public function checkRcExistAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 

		$strWhere = "rt.stock_id = ".$arrData->stock_id."";
		$arrStockData = $this->getRcTransferTable()->fetchRctransferDetail($strWhere);
		echo json_encode(count($arrStockData)); die;
	}
	
	public function exportAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$strwhere = "rt.purchase_date between '".date("Y-m-d",strtotime($arrData->from_date))."' AND '".date("Y-m-d",strtotime($arrData->to_date))."'";
		$arrStockData = $this->getRcTransferTable()->fetchRctransferDetail($strwhere);

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Consolidated Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX consolidated Export");
		$objPHPExcel->getProperties()->setDescription("export consolidated report on ".date("Y m d H:i:s").".");
		
		// Add header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells("A1:A2");   
		$objPHPExcel->getActiveSheet()->mergeCells("B1:B2");
		$objPHPExcel->getActiveSheet()->mergeCells("C1:C2");
		$objPHPExcel->getActiveSheet()->mergeCells("D1:D2");
		$objPHPExcel->getActiveSheet()->mergeCells("E1:E2");
		$objPHPExcel->getActiveSheet()->mergeCells("F1:F2");
		$objPHPExcel->getActiveSheet()->mergeCells("G1:G2");   
		$objPHPExcel->getActiveSheet()->mergeCells("H1:H2");
		$objPHPExcel->getActiveSheet()->mergeCells("I1:I2");
		$objPHPExcel->getActiveSheet()->mergeCells("J1:J2");
		$objPHPExcel->getActiveSheet()->mergeCells("K1:K2");
		$objPHPExcel->getActiveSheet()->mergeCells("L1:L2");
		$objPHPExcel->getActiveSheet()->mergeCells("M1:M2");
		$objPHPExcel->getActiveSheet()->mergeCells("N1:N2");
		$objPHPExcel->getActiveSheet()->mergeCells("O1:O2");
		$objPHPExcel->getActiveSheet()->mergeCells("AC1:AC2");
		$objPHPExcel->getActiveSheet()->mergeCells("AD1:AD2");
		$objPHPExcel->getActiveSheet()->mergeCells("AE1:AE2");
		$objPHPExcel->getActiveSheet()->mergeCells("AF1:AF2");
		$objPHPExcel->getActiveSheet()->mergeCells("AG1:AG2");   
		$objPHPExcel->getActiveSheet()->mergeCells("AH1:AH2");
		$objPHPExcel->getActiveSheet()->mergeCells("AI1:AI2");
		$objPHPExcel->getActiveSheet()->mergeCells("AJ1:AJ2");
		$objPHPExcel->getActiveSheet()->mergeCells("AK1:AK2");
		$objPHPExcel->getActiveSheet()->mergeCells("AL1:AL2");
		$objPHPExcel->getActiveSheet()->mergeCells("P1:X1");
		$objPHPExcel->getActiveSheet()->mergeCells("Y1:AB1");
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'S.No'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Reg No'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Model');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Fuel Type');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Existing HP Bank Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Seller Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Seller Email'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Seller Mob'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Buyer Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Buyer Email'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Buyer Mob'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Date of Delivery');
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Transfer Type'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'CNG Addition');
		$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'CNG Vendor Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Vehicle / Seller Docs'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('P2', 'Original RC'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('Q2', 'Insurance'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('R2', 'TTO Set'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('S2', 'Affidavit'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('T2', 'NOC & Form 35'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('U2', 'Letter Head'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('V2', 'ID Proof'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('W2', 'Address Proof'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('X2', 'Pendency Completion Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('Y1', 'Buyer Docs'); 		
		$objPHPExcel->getActiveSheet()->SetCellValue('Y2', 'ID Proof'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('Z2', 'Address Proof 1'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AA2', 'Address Proof 2'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AB2', 'PUC'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AC1', 'HPA Bank Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('AD1', 'Log in Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('AE1', 'Agent Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('AF1', 'Transfer Status'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AG1', 'Discrepency'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AH1', 'Transferred RC Uploaded'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AI1', 'Delivery to Log-in (Days)'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AJ1', 'Delivery to Today (Days)'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AK1', 'Log-in to Today (Days)'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AL1', 'Log-in to Transfer (Days)');
		$objPHPExcel->getActiveSheet()->getStyle('A1:AL2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->cellColor($objPHPExcel,'A1:E1', 'ffff47');
		$this->cellColor($objPHPExcel,'F1:H1', '3366ff');
		$this->cellColor($objPHPExcel,'F1:H1', '3366ff');
		$this->cellColor($objPHPExcel,'AD1:AF1', '3366ff');
		$this->cellColor($objPHPExcel,'I1:L1', 'c7d5ff');
		$this->cellColor($objPHPExcel,'M1', 'd4d4d4');
		$this->cellColor($objPHPExcel,'AG1', 'd4d4d4');
		$this->cellColor($objPHPExcel,'AG1', 'bd1900');
		$this->cellColor($objPHPExcel,'N1:O1', '00ad00');
		$this->cellColor($objPHPExcel,'Y1:AB2', 'dbffdb');
		$this->cellColor($objPHPExcel,'P1:X2', 'c7d5ff');
		
		for($intS=0; $intS < count($arrStockData); $intS++)
		{
			$rowid = $intS+3;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-2); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, $arrStockData[$intS]['registration_no']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $arrStockData[$intS]['make_name']." ".$arrStockData[$intS]['model_name']." ".$arrStockData[$intS]['variant_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, $arrStockData[$intS]['fuel_type']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, ($arrStockData[$intS]['hypothecation'])?$arrStockData[$intS]['seller_financiers_name']:''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $arrStockData[$intS]['seller_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $arrStockData[$intS]['seller_email']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $arrStockData[$intS]['seller_mobile']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, $arrStockData[$intS]['buyer_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $arrStockData[$intS]['buyer_email']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $arrStockData[$intS]['buyer_mobile']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, ($arrStockData[$intS]['delivery_date'] != '0000-00-00')?date("d-m-Y",strtotime($arrStockData[$intS]['delivery_date'])):''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowid, $arrStockData[$intS]['transfer_type']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowid, $arrStockData[$intS]['cng_addition']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowid, $arrStockData[$intS]['cng_vendor']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowid, $arrStockData[$intS]['original_rc_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowid, $arrStockData[$intS]['insurance_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowid, $arrStockData[$intS]['tto_set_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowid, $arrStockData[$intS]['affidavit_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowid, $arrStockData[$intS]['form35_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowid, $arrStockData[$intS]['letter_head_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowid, $arrStockData[$intS]['id_proof_1_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowid, $arrStockData[$intS]['address_proof_1_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowid, ($arrStockData[$intS]['login_date'] != '0000-00-00')?date("d-m-Y",strtotime($arrStockData[$intS]['login_date'])):''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowid, $arrStockData[$intS]['buyer_id_proof_1_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowid, $arrStockData[$intS]['buyer_address_proof_1_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowid, $arrStockData[$intS]['buyer_address_proof_2_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowid, $arrStockData[$intS]['puc_file_status']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowid, ($arrStockData[$intS]['hpa'])?$arrStockData[$intS]['buyer_financiers_name']:''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowid, ($arrStockData[$intS]['login_date'] != '0000-00-00')?date("d-m-Y",strtotime($arrStockData[$intS]['login_date'])):''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowid, $arrStockData[$intS]['agent_name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowid, $arrStockData[$intS]['transfer_status']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowid, $arrStockData[$intS]['discrepency_details']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowid, ($arrStockData[$intS]['transfer_date'] != '0000-00-00')?date("d-m-Y",strtotime($arrStockData[$intS]['transfer_date'])):'');
			if($arrStockData[$intS]['delivery_date'] != "0000-00-00" && $arrStockData[$intS]['login_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($arrStockData[$intS]['delivery_date']) - strtotime($arrStockData[$intS]['login_date'])) / (60 * 60 * 24));
			}
			else
			{
				$date_diff = 0;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowid, $date_diff);
			if($arrStockData[$intS]['delivery_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($arrStockData[$intS]['delivery_date']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
			}
			else
			{
				$date_diff = 0;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowid, $date_diff);
			if($arrStockData[$intS]['login_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($arrStockData[$intS]['login_date']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
			}
			else
			{
				$date_diff = 0;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowid, $date_diff);
			if($arrStockData[$intS]['delivery_date'] != "0000-00-00" && $arrStockData[$intS]['transfer_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($arrStockData[$intS]['delivery_date']) - strtotime($arrStockData[$intS]['transfer_date'])) / (60 * 60 * 24));
			}
			else
			{
				$date_diff = 0;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowid, $date_diff);
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('consolidated report');
		$file_name = "consolidated_report_".date("Y_m_d_H_i_s").".xlsx";
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
	
	public function exportPendencyAction()
	{
		$strwhere = "original_rc_file_status = 'Pending' OR insurance_file_status = 'Pending' OR tto_set_file_status = 'Pending' OR form35_file_status = 'Pending'
		OR affidavit_file_status = 'Pending' OR letter_head_file_status = 'Pending' OR id_proof_1_file_status = 'Pending' OR id_proof_2_file_status = 'Pending'
		OR address_proof_1_file_status = 'Pending' OR address_proof_2_file_status = 'Pending' OR  buyer_id_proof_1_file_status = 'Pending' 
		OR buyer_id_proof_2_file_status = 'Pending' OR buyer_address_proof_1_file_status = 'Pending' OR buyer_address_proof_2_file_status = 'Pending' OR
		puc_file_status  = 'Pending'";
		$arrPendencyData = $this->getRcTransferTable()->fetchPendencyRecord($strwhere);
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Pendency Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Pendency Export");
		$objPHPExcel->getProperties()->setDescription("export Docs Pendency report on ".date("Y m d H:i:s").".");
		
		// Add header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Document Pendency Report'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'S.No'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Reg No'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C2', 'MMV Model');
		$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Date of Purchase');
		$objPHPExcel->getActiveSheet()->SetCellValue('E2', 'Seller Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Seller Mob'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Pending Document'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H2', 'Pending Days'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Expected Date'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Date of Delivery'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K2', 'Buyer Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Buyer Mob');

		$rowid = 3;;
		for($intS=0; $intS < count($arrPendencyData); $intS++)
		{
			if($arrPendencyData[$intS]['original_rc_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Original RC File',$arrPendencyData[$intS]['original_rc_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['insurance_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Insurance',$arrPendencyData[$intS]['insurance_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['tto_set_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'TTO Set',$arrPendencyData[$intS]['tto_set_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['form35_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'NOC & Form 35',$arrPendencyData[$intS]['tto_set_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['affidavit_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Affidavit',$arrPendencyData[$intS]['affidavit_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['letter_head_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Letter Head',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['id_proof_1_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Seller ID Proof 1',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['id_proof_2_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Seller ID Proof 2',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['address_proof_1_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Seller Address Proof 1',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['address_proof_2_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Seller Address Proof 2',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['buyer_id_proof_1_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Buyer ID Proof 1',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['buyer_id_proof_2_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Buyer ID Proof 2',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['buyer_address_proof_1_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Buyer Address Proof 1',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['buyer_address_proof_2_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'Buyer Address Proof 2',$arrPendencyData[$intS]['letter_head_date'],$arrPendencyData[$intS]['purchase_date']);
				$rowid++;		
			}
			if($arrPendencyData[$intS]['puc_file_status'] == "Pending") {
				$this->addPendencyRow($objPHPExcel,$rowid,$arrPendencyData[$intS],'PUC',$arrPendencyData[$intS]['puc_date'],$arrPendencyData[$intS]['delivery_date']);
				$rowid++;		
			}
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('Pendency report');
		$file_name = "pendency_report_".date("Y_m_d_H_i_s").".xlsx";
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
	
	public function addPendencyRow($objPHPExcel,$rowid,$row,$doc_name,$expected_date,$date)
	{
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-2); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, $row['registration_no']); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $row['make_name']." ".$row['model_name']." ".$row['variant_name']); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, ($row['purchase_date'] != '0000-00-00')?date("d-m-Y",strtotime($row['purchase_date'])):''); 
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $row['seller_name']); 
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $row['seller_mobile']); 
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $doc_name);  
		
		if($date != "0000-00-00")
		{
			$date_diff = abs((strtotime($date) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
		}
		else
		{
			$date_diff = 0;
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $date_diff); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, ($expected_date != '0000-00-00')?date("d-m-Y",strtotime($expected_date)):''); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, ($row['delivery_date'] != '0000-00-00')?date("d-m-Y",strtotime($row['delivery_date'])):''); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $row['buyer_mobile']); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $row['buyer_name']); 
	}
	
	public function exportPendingAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$strwhere = "file_status = 'Logged-in' AND transfer_status != 'Transferred'";
		$arrStockData = $this->getRcTransferTable()->fetchRctransferDetail($strwhere);
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX RCT Pending Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX RCT Pending Export");
		$objPHPExcel->getProperties()->setDescription("export Docs RCT Pending report on ".date("Y m d H:i:s").".");
		
		// Add header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'RC Transfer Pending Status Update'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'S.No'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Reg No'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C2', 'MMV Model');
		$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Date of Delivery');
		$objPHPExcel->getActiveSheet()->SetCellValue('E2', 'Buyer Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Buyer Mob'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Agent Name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H2', 'Agent Mob'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Log-in Date'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Transfer Status'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K2', 'Days Pending'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Discrepency Remarks');
		$objPHPExcel->getActiveSheet()->SetCellValue('M2', 'Re Log-in Date');
		$objPHPExcel->getActiveSheet()->SetCellValue('N2', 'Re Log-in Remarks');
		
		$rowid = 3;;
		for($intS=0; $intS < count($arrStockData); $intS++)
		{
			$row = $arrStockData[$intS];
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-2); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, $row['registration_no']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $row['make_name']." ".$row['model_name']." ".$row['variant_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, ($row['delivery_date'] != '0000-00-00')?date("d-m-Y",strtotime($row['delivery_date'])):''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $row['buyer_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $row['buyer_mobile']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $row['agent_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $row['agent_mobile']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, ($row['login_date'] != '0000-00-00')?date("d-m-Y",strtotime($row['login_date'])):''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $row['transfer_status']); 
			if($row['login_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($row['login_date']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
			}
			else
			{
				$date_diff = 0;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $date_diff); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $row['discrepency_details']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowid, ($row['resubmission_date'] != '0000-00-00')?date("d-m-Y",strtotime($row['resubmission_date'])):''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowid, $row['resubmission_remarks']); 
			$rowid++;
		}
		$objPHPExcel->getActiveSheet()->setTitle('RCT Pending report');
		$file_name = "rct_pending_report_".date("Y_m_d_H_i_s").".xlsx";
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
	
	public function cellColor($objPHPExcel,$cells,$color){
		$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				 'rgb' => $color
			)
		));
	}
	
	public function uploadAction(){
		echo json_encode($_FILES);
	}
	
	public function getRcTransferTable()
	{
		if (!$this->rcTransferTable) {
            $sm = $this->getServiceLocator();
            $this->rcTransferTable = $sm->get('Rctransfer\Model\RcTransferTable');
        }
        return $this->rcTransferTable;
	}
	
	public function getRcTransferLogTable()
	{
		if (!$this->rcTransferLogTable) {
            $sm = $this->getServiceLocator();
            $this->rcTransferLogTable = $sm->get('Rctransfer\Model\RcTransferLogTable');
        }
        return $this->rcTransferLogTable;
	}
	
	public function getAgentMasterTable()
	{
		if (!$this->agentMasterTable) {
            $sm = $this->getServiceLocator();
            $this->agentMasterTable = $sm->get('Rctransfer\Model\AgentMasterTable');
        }
        return $this->agentMasterTable;
	}
	public function getCustomerMasterTable()
	{
		if (!$this->customerMasterTable) {
            $sm = $this->getServiceLocator();
            $this->customerMasterTable = $sm->get('Customer\Model\CustomerMasterTable');
        }
        return $this->customerMasterTable;
	}
	public function getStockMasterTable()
	{
		if (!$this->stockMasterTable) {
            $sm = $this->getServiceLocator();
            $this->stockMasterTable = $sm->get('Inventory\Model\StockMasterTable');
        }
        return $this->stockMasterTable;
	}
}
