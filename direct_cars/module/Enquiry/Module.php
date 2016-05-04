<?php
namespace Enquiry;

// Add these import statements:
use Enquiry\Model\EnquiryCustomer;
use Enquiry\Model\EnquiryCustomerTable;
use Enquiry\Model\BuyerEnquiry;
use Enquiry\Model\BuyerEnquiryTable;
use Enquiry\Model\GeneralEnquiry;
use Enquiry\Model\GeneralEnquiryTable;
use Enquiry\Model\SellerEnquiry;
use Enquiry\Model\SellerEnquiryTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getServiceConfig()
    {
        return array('factories' => array(
					'Enquiry\Model\EnquiryCustomerTable' =>  function($sm) {
						$tableGateway = $sm->get('EnquiryCustomerTableGateway');
						$table = new EnquiryCustomerTable($tableGateway);
						return $table;
					},
					'EnquiryCustomerTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new EnquiryCustomer());
						return new TableGateway('enquiry_customer', $dbAdapter, null, $resultSetPrototype);
					},
					'Enquiry\Model\BuyerEnquiryTable' =>  function($sm) {
						$tableGateway = $sm->get('BuyerEnquiryTableGateway');
						$table = new BuyerEnquiryTable($tableGateway);
						return $table;
					},
					'BuyerEnquiryTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new BuyerEnquiry());
						return new TableGateway('buyer_enquiry', $dbAdapter, null, $resultSetPrototype);
					},
					'Enquiry\Model\GeneralEnquiryTable' =>  function($sm) {
						$tableGateway = $sm->get('GeneralEnquiryTableGateway');
						$table = new GeneralEnquiryTable($tableGateway);
						return $table;
					},
					'GeneralEnquiryTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new GeneralEnquiry());
						return new TableGateway('general_enquiry', $dbAdapter, null, $resultSetPrototype);
					},
					'Enquiry\Model\SellerEnquiryTable' =>  function($sm) {
						$tableGateway = $sm->get('SellerEnquiryTableGateway');
						$table = new SellerEnquiryTable($tableGateway);
						return $table;
					},
					'SellerEnquiryTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new SellerEnquiry());
						return new TableGateway('seller_enquiry', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}