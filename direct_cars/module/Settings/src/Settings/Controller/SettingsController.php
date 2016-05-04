<?php

namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class SettingsController extends AbstractActionController {

	protected $settingsTable;

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
	
		$arrWhere = array("type" => "Page");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		
		return new ViewModel(array(
            'arrSettings' => $arrSettings,
			'flashMessages' => $this->flashMessenger()->getMessages(),
			));
    }
	
	/**
	 * Function to edit existing page content.
	 * Created On 19th march  2015
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function editAction()
	{
		$id = $this->params('id');
		$arrWhere = array("settings_id" => $id);
		$arrSettings = $this->getSettingsTable()->fetchSettingsDetail($arrWhere);
		
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			$file_url = '/var/www/html'.$arrSettings[0]['settings_value'];
			chmod($file_url, 7777);
			unlink($file_url);
			copy($post['file_upload']['tmp_name'],$file_url);
			return $this->redirect()->toRoute('settings');
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrSettings' => $arrSettings
			));
		$viewModel->setTemplate('settings/settings/save.phtml');
		return $viewModel;
	}
	
	public function downloadAction()
	{
		$id = $this->params('id');
		$arrWhere = array("settings_id" => $id);
		$arrSettings = $this->getSettingsTable()->fetchSettingsDetail($arrWhere);
		$file_url = '/var/www/html'.$arrSettings[0]['settings_value'];
	
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
		readfile($file_url); // do the double-download-dance (dirty but worky)
		exit;
	}
	
	public function variableEditAction()
	{
		$arrWhere = array("type" => "Variable");
		$arrSettings = $this->getSettingsTable()->fetchSettingsList($arrWhere);
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			
			for($intP=0; $intP<count($post['settings_value']); $intP++)
			{
				$arrWhere = array("settings_id" => $post['settings_id'][$intP]);
				$arrData = array("settings_value" => $post['settings_value'][$intP]);
				
				$this->getSettingsTable()->updateSettings($arrData,$arrWhere);
			}
			
			return $this->redirect()->toRoute('settings');
		}
		
		return new ViewModel(array(
            'arrSettings' => $arrSettings,
			'flashMessages' => $this->flashMessenger()->getMessages(),
			));
	}

	public function getSettingsTable()
	{
		if (!$this->settingsTable) {
            $sm = $this->getServiceLocator();
            $this->settingsTable = $sm->get('Settings\Model\SettingsTable');
        }
        return $this->settingsTable;
	}
}
