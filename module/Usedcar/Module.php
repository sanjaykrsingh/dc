<?php
namespace Usedcar;

use Usedcar\Model\EnquiryCustomer;
use Usedcar\Model\EnquiryCustomerTable;
use Usedcar\Model\BuyerEnquiry;
use Usedcar\Model\BuyerEnquiryTable;
use Usedcar\Model\GeneralEnquiry;
use Usedcar\Model\GeneralEnquiryTable;
use Usedcar\Model\SellerEnquiry;
use Usedcar\Model\SellerEnquiryTable;
use Usedcar\Model\MmvDetail;
use Usedcar\Model\MmvDetailTable;
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
					'Usedcar\Model\EnquiryCustomerTable' =>  function($sm) {
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
					'Usedcar\Model\BuyerEnquiryTable' =>  function($sm) {
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
					'Usedcar\Model\GeneralEnquiryTable' =>  function($sm) {
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
					'Usedcar\Model\SellerEnquiryTable' =>  function($sm) {
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
					'Usedcar\Model\MmvDetailTable' =>  function($sm) {
						$tableGateway = $sm->get('MmvDetailTableGateway');
						$table = new MmvDetailTable($tableGateway);
						return $table;
					},
					'MmvDetailTableGateway' => function ($sm) {
						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
						$resultSetPrototype = new ResultSet();
						$resultSetPrototype->setArrayObjectPrototype(new MmvDetail());
						return new TableGateway('make_model_variant', $dbAdapter, null, $resultSetPrototype);
					},
				),
			);
    }
    
}