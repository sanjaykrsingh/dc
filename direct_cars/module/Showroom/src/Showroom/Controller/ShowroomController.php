<?php

namespace Showroom\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Showroom\Form\SaveForm;
use Showroom\Form\SaveImageForm;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;


class ShowroomController extends AbstractActionController {

	protected $showroomMasterTable;
	protected $showroomImageTable;
	protected $citiesTable;
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
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'showroom_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'ASC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrWhere = array('status' => '1');
		$arrShowroom = $this->getShowroomMasterTable()->fetchCustomShoroomList($arrWhere,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrShowroom));

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
            'arrShowroom' => $paginator,
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
	
	/**
	 * Function to add new showroom.
	 * Created On 31 Dec 2014
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function addAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		$form = new SaveForm('save-showroom');
		$request = $this->getRequest();
		if ($request->isPost()) {
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			
			//$form->setData($post);
			//if ($form->isValid()) 
			{
				$data = $post;
				
				$data['showroom_timings_days'] = implode(",",$data['showroom_timings_days']);
				
				// Insert Data in DB
				$arrData = array("showroom_name" => $data['showroom_name'],
								 "address_line_1" => $data['address_line_1'],
								 "address_line_2" => $data['address_line_2'],
								 "land_mark" => $data['land_mark'],
								 "locality" => $data['locality'],
								 "city" => $data['city'],
								 "state" => $data['state'],
								 "country" => $data['country'],
								 "pin_code" => $data['pin_code'],
								 "google_link" => $data['google_link'],
								 "google_map_code" => $data['google_map_code'],
								 "manager_name" => $data['manager_name'],
								 "phone_1" => $data['phone_1'],
								 "phone_2" => $data['phone_2'],
								 "email" => $data['email'],
								 "secondary_email" => $data['secondary_email'],
								 "services" => $data['services'],
								 "description" => $data['description'],
								 "virtual_tour_video" => $data['virtual_tour_video'],
								 "showroom_timings_days" => $data['showroom_timings_days'],
								 "showroom_timings_start" => $data['showroom_timings_start'],
								 "showroom_timings_end" => $data['showroom_timings_end'],
								 "google_map_code" => $data['google_map_code'],
								 "created_by" => $this->login_user_id);
		
				$this->getShowroomMasterTable()->insertShowroom($arrData);
				return $this->redirect()->toRoute('showroom');
			}
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('showroom/showroom/save.phtml');
		return $viewModel;
	}
	
	/**
	 * Function to edit showroom detil.
	 * Created On 31 Dec 2014
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function editAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		$id = $this->params('id');
		$arrWhere = array("showroom_id" => $id);
		$arrShowroom = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$form = new SaveForm('save-showroom',array(), $arrShowroom[0]);
		$request = $this->getRequest();
		if ($request->isPost()) {
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			//print_r($post); die;
			//$form->setData($post);
			//if ($form->isValid()) 
			{
				$data = $post;
				
				//print_r($data); die;
				$data['showroom_timings_days'] = implode(",",$data['showroom_timings_days']);
				// Insert Data in DB
				$arrData = array("showroom_name" => $data['showroom_name'],
								 "address_line_1" => $data['address_line_1'],
								 "address_line_2" => $data['address_line_2'],
								 "land_mark" => $data['land_mark'],
								 "locality" => $data['locality'],
								 "city" => $data['city'],
								 "state" => $data['state'],
								 "country" => $data['country'],
								 "pin_code" => $data['pin_code'],
								 "google_link" => $data['google_link'],
								 "google_map_code" => $data['google_map_code'],
								 "manager_name" => $data['manager_name'],
								 "phone_1" => $data['phone_1'],
								 "phone_2" => $data['phone_2'],
								 "email" => $data['email'],
								 "secondary_email" => $data['secondary_email'],
								 "services" => $data['services'],
								 "description" => $data['description'],
								 "virtual_tour_video" => $data['virtual_tour_video'],
								 "showroom_timings_days" => $data['showroom_timings_days'],
								 "showroom_timings_start" => $data['showroom_timings_start'],
								 "showroom_timings_end" => $data['showroom_timings_end'],
								 "google_map_code" => $data['google_map_code'],
								 "created_by" => $this->login_user_id);
		
				$this->getShowroomMasterTable()->updateShowroom($arrData,$arrWhere);
				return $this->redirect()->toRoute('showroom');
			}
		}
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('showroom/showroom/save.phtml');
		return $viewModel;
	}
	
	public function deleteAction()
	{
		$id = $this->params('id');
		$arrWhere = array("showroom_id" => $id);
		$arrShowroom = $this->getShowroomMasterTable()->fetchShowroomDetail($arrWhere);
		$arrData = array("status" => "0");
		$this->getShowroomMasterTable()->updateShowroom($arrData,$arrWhere);
		return $this->redirect()->toRoute('showroom');
	}
	
	public function viewImagesAction() {
		$showroom_id = $this->params('id');
		
		$form = new SaveImageForm('save-showroom-image');
		
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'showroom_img_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'ASC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrWhere = array('showroom_id' => $showroom_id);
		$arrShowroom = $this->getShowroomImageTable()->fetchCustomShoroomImageList($arrWhere,$arrParam);

		$itemsPerPage = 10;

		$paginator = new Paginator(new paginatorIterator($arrShowroom));

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
            'arrShowroom' => $paginator,
			'showroom_id' => $showroom_id,
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
			'form' => $form,
			'config' => $config,
			));
	}
	
	public function imageTagAction()
	{
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			
			for($intI=0; $intI<count($arrData->image_id); $intI++)
			{
				$arrWhere = array("showroom_img_id" => $arrData->image_id[$intI]);
				$arrImageData = array("img_title" => $arrData->image_title[$intI]);
				$this->getShowroomImageTable()->updateShowroomImage($arrImageData,$arrWhere);
			}
			return $this->redirect()->toRoute('showroom',array('action' => "view-images",'id'=>$arrData->showroom_id));
		}
	}
	
	public function addImageAction() {
		$showroom_id = $this->params('id');
		$form = new SaveImageForm('save-showroom-image');
		$config = $this->getServiceLocator()->get('Config');
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			
			$form->setData($post);
			if ($form->isValid()) {
				$data = $form->getData();
				//print_r($_FILES);
				// ...or with array notation
				$arrImageData = $data['image-file'];
				for($i=0; $i < count($arrImageData); $i++)
				{
					if($arrImageData[$i]['name'] != "")
					{
							$data['image-file'] = $arrImageData[$i];
							$validator = new \Zend\Validator\File\Extension(array('jpg', 'jpeg','gif','png'));
							$data['image-file']['name'] = rand().$data['image-file']['name'];
							$data['image-file']['name'] = str_replace(" ","_",$data['image-file']['name']);
							copy($data['image-file']['tmp_name'],$config['public_folder_path']."images/showroom/".$data['image-file']['name']);
							$image_name = "";
							if ($validator->isValid($config['public_folder_path']."images/showroom/".$data['image-file']['name'])) {
							
							$thumbWidth = 640;
							$jpeg_quality = 90;
							$thumb_src_width = 250;
							
							// Create thumnail
								$image_name = $data['image-file']['name'];
								$src = $config['public_folder_path']."images/showroom/".$data['image-file']['name'];
								if(exif_imagetype ($src ) == 1){
									$img_r = imagecreatefromgif($src);
									
									$width = imagesx( $img_r );
									$height = imagesy( $img_r );
									
									// calculate thumbnail size
									  $targ_w = $thumbWidth;
									  $targ_h = floor( $height * ( $thumbWidth / $width ) );
									  
									  $thumb_src_w = $thumb_src_width;
									  $thumb_src_h = floor( $height * ( $thumb_src_width / $width ) );
									  
									  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$targ_w,$targ_h,$width,$height);
									
									imagegif($dst_r,$config['public_folder_path']."images/showroom/small/".$data['image-file']['name']);
									
									$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$thumb_src_w,$thumb_src_h,$width,$height);
									
									imagegif($dst_r,$config['public_folder_path']."images/showroom/src/".$data['image-file']['name']);
									
									$thumb_src_w = 132;
									$thumb_src_h = floor( $height * ( $thumb_src_w / $width ) );
									  
									$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$thumb_src_w,$thumb_src_h,$width,$height);
									
									imagegif($dst_r,$config['public_folder_path']."images/showroom/carousel/".$data['image-file']['name']);
								}
								else if(exif_imagetype ($src ) == 2){
									
									$img_r = imagecreatefromjpeg($src);
									
									$width = imagesx( $img_r );
									$height = imagesy( $img_r );
									
									// calculate thumbnail size
									  $targ_w = $thumbWidth;
									  $targ_h = floor( $height * ( $thumbWidth / $width ) );
									  
									  $thumb_src_w = $thumb_src_width;
									  $thumb_src_h = floor( $height * ( $thumb_src_width / $width ) );
									  
									  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$targ_w,$targ_h,$width,$height);
									
									imagejpeg($dst_r,$config['public_folder_path']."images/showroom/small/".$data['image-file']['name'],$jpeg_quality);
									
									$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$thumb_src_w,$thumb_src_h,$width,$height);
									
									imagejpeg($dst_r,$config['public_folder_path']."images/showroom/src/".$data['image-file']['name'],$jpeg_quality);
									
									$thumb_src_w = 132;
									$thumb_src_h = floor( $height * ( $thumb_src_w / $width ) );
									  
									$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$thumb_src_w,$thumb_src_h,$width,$height);
									
									imagejpeg($dst_r,$config['public_folder_path']."images/showroom/carousel/".$data['image-file']['name'],$jpeg_quality);
								}
								else if(exif_imagetype ($src ) == 3){
								$img_r = imagecreatefrompng($src);
									
									$width = imagesx( $img_r );
									$height = imagesy( $img_r );
									
									// calculate thumbnail size
									  $targ_w = $thumbWidth;
									  $targ_h = floor( $height * ( $thumbWidth / $width ) );
									  
									  $thumb_src_w = $thumb_src_width;
									  $thumb_src_h = floor( $height * ( $thumb_src_width / $width ) );
									  
									 $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$targ_w,$targ_h,$width,$height);
									
									imagepng($dst_r,$config['public_folder_path']."images/showroom/small/".$data['image-file']['name']);
									
									$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$thumb_src_w,$thumb_src_h,$width,$height);
									
									imagepng($dst_r,$config['public_folder_path']."images/showroom/src/".$data['image-file']['name']);
									
									$thumb_src_w = 132;
									$thumb_src_h = floor( $height * ( $thumb_src_w / $width ) );
									  
									$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

									imagecopyresampled($dst_r,$img_r,0,0,0,0,
									$thumb_src_w,$thumb_src_h,$width,$height);
									
									imagepng($dst_r,$config['public_folder_path']."images/showroom/carousel/".$data['image-file']['name']);
								}
								
								// Insert Data in DB
								$arrData = array("img_title" => '',
												 "img_name" => $image_name,
												 "showroom_id" => $showroom_id);
						
								$this->getShowroomImageTable()->insertShowroomImage($arrData);
								
							}
						else{
							$this->flashMessenger()->addMessage('Invalid file type.');
						}
					}	
				}
				return $this->redirect()->toRoute('showroom',array('action' => "view-images",'id'=>$showroom_id));
			}	
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form,
			'showroom_id' => $showroom_id,
			));
		$viewModel->setTemplate('showroom/showroom/save-image.phtml');
		return $viewModel;
	}
	
	public function imageCropAction(){
		$image_id = $this->params('id');
		$config = $this->getServiceLocator()->get('Config');
		$arrWhere = array("showroom_img_id" => $image_id);
		$objShowroomImages = $this->getShowroomImageTable()->fetchShowroomImageList($arrWhere);
		foreach($objShowroomImages as $showroom)
		{
			$image_name = $showroom->img_name;
			$showroom_id = $showroom->showroom_id;
		}
		
		$request = $this->getRequest();
		if($request->isPost())
		{
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			$targ_w = 640;
			$targ_h = 480;
			$jpeg_quality = 90;
			
			$thumb_src_w =250;
			$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );

			$src = $config['public_folder_path']."images/showroom/src/".$post['stock_img_src'];
			unlink($config['public_folder_path']."images/showroom/thumb/".$post['stock_img_src']);
			if(exif_imagetype ($src ) == 1){
				$img_r = imagecreatefromgif($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$targ_w,$targ_h,$_POST['w'],$_POST['h']);
				
				imagegif($dst_r,$config['public_folder_path']."images/showroom/thumb/".$post['stock_img_src']);
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				
				imagegif($dst_r,$config['public_folder_path']."images/showroom/small/".$post['stock_img_src']);
				
				$thumb_src_w =132;
				$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				
				imagegif($dst_r,$config['public_folder_path']."images/showroom/carousel/".$post['stock_img_src']);
			}
			else if(exif_imagetype ($src ) == 2){
				$img_r = imagecreatefromjpeg($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$targ_w,$targ_h,$_POST['w'],$_POST['h']);

				imagejpeg($dst_r,$config['public_folder_path']."images/showroom/thumb/".$post['stock_img_src'],$jpeg_quality);
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				
				imagejpeg($dst_r,$config['public_folder_path']."images/showroom/small/".$post['stock_img_src'],$jpeg_quality);
				
				$thumb_src_w =132;
				$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				
				imagejpeg($dst_r,$config['public_folder_path']."images/showroom/carousel/".$post['stock_img_src'],$jpeg_quality);
			}
			else if(exif_imagetype ($src ) == 3){
			//echo "hello"; echo exif_imagetype ($src ); die;
				
				$img_r = imagecreatefrompng($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$targ_w,$targ_h,$_POST['w'],$_POST['h']);
				//header('Content-Type: image/png');
				imagepng($dst_r,$config['public_folder_path']."images/showroom/thumb/".$post['stock_img_src']);
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				
				imagepng($dst_r,$config['public_folder_path']."images/showroom/small/".$post['stock_img_src']);
				
				$thumb_src_w =132;
				$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				
				imagepng($dst_r,$config['public_folder_path']."images/showroom/carousel/".$post['stock_img_src']);
			}
					
			return $this->redirect()->toRoute('showroom',array('action' => "view-images",'id'=>$showroom_id));
		
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'showroom_id' => $showroom_id,
			'image_name'=> $image_name,
			'image_path' => $config['image_path']
			));
		$viewModel->setTemplate('showroom/showroom/crop-image.phtml');
		return $viewModel;
	}
	public function deleteImageAction() {
		$id = $this->params('id');
		
		$config = $this->getServiceLocator()->get('Config');
		
		$arrWhere = array("showroom_img_id" => $id);
		$arrShowroomImage = $this->getShowroomImageTable()->fetchShowroomImageDetail($arrWhere);
		$showroom_id = $arrShowroomImage[0]['showroom_id'];
		if(!empty($arrShowroomImage[0]['img_name']))
		{
			unlink($config['public_folder_path']."images/showroom/".$arrShowroomImage[0]['img_name']);
			unlink($config['public_folder_path']."images/showroom/src/".$arrShowroomImage[0]['img_name']);
			unlink($config['public_folder_path']."images/showroom/thumb/".$arrShowroomImage[0]['img_name']);
			unlink($config['public_folder_path']."images/showroom/small/".$arrShowroomImage[0]['img_name']);
			unlink($config['public_folder_path']."images/showroom/carousel/".$arrShowroomImage[0]['img_name']);
		}
		$this->getShowroomImageTable()->deleteShowroomImage($arrWhere);
		return $this->redirect()->toRoute('showroom',array('action' => "view-images",'id'=>$showroom_id));
	}
	
	public function deleteMultiImagesAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			if(!empty($arrData->delete_image))
			{
				for($i=0; $i  < count($arrData->delete_image); $i++)
				{
					$image_id = $arrData->delete_image[$i];
					
					
					$arrWhere = array("showroom_img_id" => $image_id);
					$arrShowroomImage = $this->getShowroomImageTable()->fetchShowroomImageDetail($arrWhere);
					$showroom_id = $arrShowroomImage[0]['showroom_id'];
					if(!empty($arrShowroomImage[0]['img_name']))
					{
						unlink($config['public_folder_path']."images/showroom/".$arrShowroomImage[0]['img_name']);
						unlink($config['public_folder_path']."images/showroom/thumb/".$arrShowroomImage[0]['img_name']);
						unlink($config['public_folder_path']."images/showroom/src/".$arrShowroomImage[0]['img_name']);
						unlink($config['public_folder_path']."images/showroom/small/".$arrShowroomImage[0]['img_name']);
						unlink($config['public_folder_path']."images/showroom/carousel/".$arrShowroomImage[0]['img_name']);
					}
					$this->getShowroomImageTable()->deleteShowroomImage($arrWhere);
				}
			}
		}
		return $this->redirect()->toRoute('showroom',array('action' => "view-images",'id'=>$showroom_id));
	}
	
	public function imageViewAction()
	{
		$showroom_id = $this->params('id');
		$arrWhere = array("showroom_id" => $showroom_id);
		$objShowroomImages = $this->getShowroomImageTable()->fetchShowroomImageList($arrWhere);
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'showroom_id' => $showroom_id,
			'config' => $config,
			'objShowroomImages'=> $objShowroomImages
			));
		$viewModel->setTemplate('showroom/showroom/image-view.phtml');
		return $viewModel;
	}
	
	public function imageSortAction()
	{
		$showroom_id = $this->params('id');
		
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			foreach($arrData as $key => $value)
			{
				$arrKey = explode("_",$key);
				$image_id = $arrKey[1];
				$arrWhere = array("showroom_img_id" => $image_id);
				$arrImageData = array("dis_order" => $value);
				$this->getShowroomImageTable()->updateShowroomImage($arrImageData,$arrWhere);
			}
			return $this->redirect()->toRoute('showroom',array('action' => "view-images",'id'=>$showroom_id));
		}
		
		$arrWhere = array("showroom_id" => $showroom_id);
		$objShowroomImages = $this->getShowroomImageTable()->fetchShowroomImageList($arrWhere);
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'showroom_id' => $showroom_id,
			'config' => $config,
			'objShowroomImages'=> $objShowroomImages
			));
		$viewModel->setTemplate('showroom/showroom/image-sort.phtml');
		return $viewModel;
	}
	
	public function getCitiesAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrCities = $this->getCitiesTable()->getCitiesList($arrData->state);
		echo json_encode($arrCities);die;
	}
	
	public function getShowroomMasterTable()
	{
		if (!$this->showroomMasterTable) {
            $sm = $this->getServiceLocator();
            $this->showroomMasterTable = $sm->get('Showroom\Model\ShowroomMasterTable');
        }
        return $this->showroomMasterTable;
	}
	
	public function getShowroomImageTable()
	{
		if (!$this->showroomImageTable) {
            $sm = $this->getServiceLocator();
            $this->showroomImageTable = $sm->get('Showroom\Model\ShowroomImageTable');
        }
        return $this->showroomImageTable;
	}
	public function getCitiesTable()
	{
		if (!$this->citiesTable) {
            $sm = $this->getServiceLocator();
            $this->citiesTable = $sm->get('Showroom\Model\CitiesTable');
        }
        return $this->citiesTable;
	}
}
