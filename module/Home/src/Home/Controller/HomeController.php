<?php

namespace Home\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container as SessionContainer;

class homeController extends AbstractActionController {

	/**
	  * Function to make user authentication on arrival of page
	  */
    public function onDispatch(MvcEvent $e) {
        return parent::onDispatch($e);
    }

	
    public function indexAction() {
		
    }
}
