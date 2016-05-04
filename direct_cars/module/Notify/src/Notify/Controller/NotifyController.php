<?php

namespace Notify\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Session\Container as SessionContainer;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Fixedvalues;

class NotifyController extends AbstractActionController {

	protected $rcTransferTable;
	protected $rcTransferLogTable;
	protected $agentMasterTable;
	protected $customerMasterTable;
	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {
        return parent::onDispatch($e);
    }
	
	public function indexAction() {
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'log_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrWhere = array();
		$arrLog = $this->getRcTransferLogTable()->fetchRctransferLogList($arrWhere,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrLog));

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
            'arrLog' => $paginator,
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
			));
    }
	
    public function agentAction() {
		$strwhere = "login_date != '0000-00-00' AND login_date != '' AND transfer_status != 'Transferred'";
		$arrStockData = $this->getRcTransferTable()->fetchRctransferDetail($strwhere);
		for($intS=0; $intS < count($arrStockData); $intS++)
		{
			$row = $arrStockData[$intS];
			$send_notification = false;
			if($row['login_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($row['login_date']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
				
				if($date_diff > 60)
				{
					$send_notification = true;
				}
				elseif($date_diff%3 == 0)
				{
					$send_notification = true;
				}
			}
			else
			{
				$date_diff = 0;
			}
			if($send_notification)
			{
				$arrAgent = $this->getAgentMasterTable()->fetchAgentDetail(array("agent_id"=>$row['agent_id']));
				$subject = "Pending for Transfer";
				$message = "File of [".$row['registration_no']."] Pending for Transfer since [".date("d-m-Y")."-".date("d-m-Y",strtotime($row['login_date']))." date] days.Update Status. <br><br>DIRECT CARS";
				Fixedvalues::sendSMS($message,$arrAgent[0]['agent_mobile']);
				$result = Fixedvalues::sendmail($message,$subject,$arrAgent[0]['agent_email']);
				
				$arrLogData = array("rc_transfer_id" =>  $row['rc_transfer_id'],
									"user_type" => "Agent",
									"subject" => $subject,
									"message" => $message,
									"email" => $arrAgent[0]['agent_email'],
									"mobile" => $arrAgent[0]['agent_mobile']);
				$this->getRcTransferLogTable()->insertRcLog($arrLogData);
			}
				
		}
		die;
    }
	
	public  function sellerAction()
	{
		$strwhere = "login_date = '0000-00-00' OR login_date = ''";
		$arrPendencyData = $this->getRcTransferTable()->fetchPendencyRecord($strwhere);
		
		for($intS=0; $intS < count($arrPendencyData); $intS++)
		{
			$row = $arrPendencyData[$intS];
			$send_notification = false;
			if($row['purchase_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($row['purchase_date']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
				
				if($date_diff == 7)
				{
					$send_notification = true;
				}
				if($date_diff > 7 && ($date_diff%3 == 0))
				{
					$send_notification = true;
				}
				if($row['car_status'] == "Sold Out")
				{
					$send_notification = true;
				}
				
			}
			else
			{
				$date_diff = 0;
			}
			if($send_notification){
				
				$file_name = "";
				if($arrPendencyData[$intS]['original_rc_file_status'] == "Pending") {
					$file_name .= "Original RC File, ";		
				}
				if($arrPendencyData[$intS]['insurance_file_status'] == "Pending") {
					$file_name .= "Insurance, ";			
				}
				if($arrPendencyData[$intS]['tto_set_file_status'] == "Pending") {
					$file_name .= "TTO Set, ";	
				}
				if($arrPendencyData[$intS]['form35_file_status'] == "Pending") {
					$file_name .= "NOC & Form 35, ";	
				}
				if($arrPendencyData[$intS]['affidavit_file_status'] == "Pending") {
					$file_name .= "Affidavit, ";	
				}
				if($arrPendencyData[$intS]['letter_head_file_status'] == "Pending") {
					$file_name .= "Letter Head, ";			
				}
				if($arrPendencyData[$intS]['id_proof_1_file_status'] == "Pending") {
					$file_name .= "Seller ID Proof 1, ";		
				}
				if($arrPendencyData[$intS]['id_proof_2_file_status'] == "Pending") {
					$file_name .= "Seller ID Proof 2, ";	
				}
				if($arrPendencyData[$intS]['address_proof_1_file_status'] == "Pending") {
					$file_name .= "Seller Address Proof 1, ";	
				}
				if($arrPendencyData[$intS]['address_proof_2_file_status'] == "Pending") {
					$file_name .= "Seller Address Proof 2, ";
				}
				if($arrPendencyData[$intS]['buyer_id_proof_1_file_status'] == "Pending") {
					$file_name .= "Buyer ID Proof 1, ";
				}
				if($arrPendencyData[$intS]['buyer_id_proof_2_file_status'] == "Pending") {
					$file_name .= "Buyer ID Proof 2, ";
				}
				if($arrPendencyData[$intS]['buyer_address_proof_1_file_status'] == "Pending") {
					$file_name .= "Buyer Address Proof 1, ";
				}
				if($arrPendencyData[$intS]['buyer_address_proof_2_file_status'] == "Pending") {
					$file_name .= "Buyer Address Proof 2, ";
				}
				if($arrPendencyData[$intS]['puc_file_status'] == "Pending") {
					$file_name .= "PUC, ";			
				}
				$file_name = trim($file_name,", ");
				$arrWhere = array("cust_id" => $row['seller_id']);
				$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
				$subject = "Pending Documents";
				$message = "Pending Docs [".$row['registration_no']."],".$file_name."... <br><br>DIRECT CARS";
				Fixedvalues::sendSMS($message,$arrCustomer[0]['customer_mobile']);
				$result = Fixedvalues::sendmail($message,$subject,$arrCustomer[0]['customer_email']);
				
				$arrLogData = array("rc_transfer_id" =>  $row['rc_transfer_id'],
									"user_type" => "Seller",
									"subject" => $subject,
									"message" => $message,
									"email" => $arrCustomer[0]['customer_email'],
									"mobile" => $arrCustomer[0]['customer_mobile']);
				$this->getRcTransferLogTable()->insertRcLog($arrLogData);
			}
		}
		die;
		
	}
	
	public  function buyerAction()
	{
		$strwhere = "login_date = '0000-00-00' OR login_date = ''";
		$arrPendencyData = $this->getRcTransferTable()->fetchPendencyRecord($strwhere);
		
		for($intS=0; $intS < count($arrPendencyData); $intS++)
		{
			$row = $arrPendencyData[$intS];
			$send_notification = false;
			if($row['delivery_date'] != "0000-00-00")
			{
				$date_diff = abs((strtotime($row['delivery_date']) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
				
				if($date_diff > 0 )
				{
					$send_notification = true;
				}
			}
			else
			{
				$date_diff = 0;
			}
			if($send_notification){
				
				$file_name = "";
				if($arrPendencyData[$intS]['original_rc_file_status'] == "Pending") {
					$file_name .= "Original RC File, ";		
				}
				if($arrPendencyData[$intS]['insurance_file_status'] == "Pending") {
					$file_name .= "Insurance, ";			
				}
				if($arrPendencyData[$intS]['tto_set_file_status'] == "Pending") {
					$file_name .= "TTO Set, ";	
				}
				if($arrPendencyData[$intS]['affidavit_file_status'] == "Pending") {
					$file_name .= "Affidavit, ";	
				}
				if($arrPendencyData[$intS]['letter_head_file_status'] == "Pending") {
					$file_name .= "Letter Head, ";			
				}
				if($arrPendencyData[$intS]['puc_file_status'] == "Pending") {
					$file_name .= "PUC, ";			
				}
				$file_name = trim($file_name,", ");
				$arrWhere = array("cust_id" => $row['buyer_id']);
				$arrCustomer = $this->getCustomerMasterTable()->fetchCustomerDetail($arrWhere);
				$subject = "Pending Documents";
				$message = "Pending Docs [".$row['registration_no']."],".$file_name."... <br><br>DIRECT CARS";
				Fixedvalues::sendSMS($message,$arrCustomer[0]['customer_mobile']);
				$result = Fixedvalues::sendmail($message,$subject,$arrCustomer[0]['customer_email']);
				
				$arrLogData = array("rc_transfer_id" =>  $row['rc_transfer_id'],
									"user_type" => "Seller",
									"subject" => $subject,
									"message" => $message,
									"email" => $arrCustomer[0]['customer_email'],
									"mobile" => $arrCustomer[0]['customer_mobile']);
				$this->getRcTransferLogTable()->insertRcLog($arrLogData);
			}
		}
		die;
		
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
}
