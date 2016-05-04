<?php

namespace Adminuser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Adminuser\Form\UpdateForm;
use ZfcUser\Service\User as UserService;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;


class AdminuserController extends AbstractActionController {

	protected $userTable;
	protected $userService;

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
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'user_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'ASC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrWhere = array();
		$arrUser = $this->getUserTable()->fetchUserList($arrWhere,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrUser));

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
            'arrUser' => $paginator,
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
	
	public function editAction()
	{
		$userid = $this->params('id');
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$arrPost = $request->getPost()->toArray();
			
			$arrData = array("first_name" => $arrPost['first_name'],
							 "last_name" => $arrPost['last_name'],
							 "display_name" => $arrPost['display_name'],
							 "is_admin" => $arrPost['is_admin']);	
			if($arrPost['newCredential'] != "")
			{
				$enc_pass = $this->getUserService()->getAdminPassword($arrPost);
				$arrData = array_merge($arrData,array("password" => $enc_pass));
			}
			$arrWhere = array("user_id" => $arrPost['user_id']);
			$this->getUserTable()->updateUser($arrData,$arrWhere);
			return $this->redirect()->toRoute('adminuser');
			
		}
		
		$arrUser = $this->getUserTable()->fetchUserDetail(array("user_id"=>$userid));
		
		$form = new UpdateForm('update-form');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'userid' => $userid,
			'arrUser' => $arrUser[0],
			'form' => $form
			));
		
		return $viewModel;
	}
	
	 public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }
        return $this->userService;
    }

	public function deactivateAction()
	{
		$user_id = $this->params()->fromRoute('id');
		$arrWhere = array("user_id" => $user_id);
		$arrData = array("active" => 0); 
		$this->getUserTable()->updateUser($arrData,$arrWhere);
		return $this->redirect()->toRoute('adminuser');
	}
	
	public function activateAction()
	{
		$user_id = $this->params()->fromRoute('id');
		$arrWhere = array("user_id" => $user_id);
		$arrData = array("active" => 1); 
		$this->getUserTable()->updateUser($arrData,$arrWhere);
		return $this->redirect()->toRoute('adminuser');
	}
	
	public function getUserTable()
	{
		if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Adminuser\Model\UserTable');
        }
        return $this->userTable;
	}
}
