<?php

namespace Testimonial\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Testimonial\Form\SaveForm;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;


class TestimonialController extends AbstractActionController {

	protected $testimonialTable;

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
		$arrParam['order_by'] = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'testimonial_id';
        $arrParam['order'] = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'ASC';
        $arrParam['page'] = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
		$arrWhere = array();
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialList($arrWhere,$arrParam);

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
            'arrTestimonial' => $paginator,
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
	
	/**
	 * Function to add new testimonial.
	 * Created On 18th Nov 2014
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function addAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		$form = new SaveForm('save-testimonial');
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
				if($data['image-file']['name'] != "" && $data['image-file']['size'] < 4194304)
				{
					$validator = new \Zend\Validator\File\Extension(array('jpg', 'jpeg','gif','png'));
					$data['image-file']['name'] = rand().$data['image-file']['name'];
					$data['image-file']['name'] = str_replace(" ","_",$data['image-file']['name']);
					copy($data['image-file']['tmp_name'],$config['public_folder_path']."images/testimonial/".$data['image-file']['name']);
					$image_name = "";
					if ($validator->isValid($config['public_folder_path']."images/testimonial/".$data['image-file']['name'])) {
					$thumbWidth = 150;
					$jpeg_quality = 90;
					// Create thumnail
						$image_name = $data['image-file']['name'];
						$src = $config['public_folder_path']."images/testimonial/".$data['image-file']['name'];
						if(exif_imagetype ($src ) == 1){
							$img_r = imagecreatefromgif($src);
							
							$width = imagesx( $img_r );
							$height = imagesy( $img_r );
							
							// calculate thumbnail size
							  $targ_w = $thumbWidth;
							  $targ_h = floor( $height * ( $thumbWidth / $width ) );
							  
							  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$img_r,0,0,0,0,
							$targ_w,$targ_h,$width,$height);
							
							imagegif($dst_r,$config['public_folder_path']."images/testimonial/thumb/".$data['image-file']['name']);
						}
						else if(exif_imagetype ($src ) == 2){
							
							$img_r = imagecreatefromjpeg($src);
							
							$width = imagesx( $img_r );
							$height = imagesy( $img_r );
							
							// calculate thumbnail size
							  $targ_w = $thumbWidth;
							  $targ_h = floor( $height * ( $thumbWidth / $width ) );
							  
							  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$img_r,0,0,0,0,
							$targ_w,$targ_h,$width,$height);
							
							imagejpeg($dst_r,$config['public_folder_path']."images/testimonial/thumb/".$data['image-file']['name'],$jpeg_quality);
						}
						else if(exif_imagetype ($src ) == 3){
						$img_r = imagecreatefrompng($src);
							
							$width = imagesx( $img_r );
							$height = imagesy( $img_r );
							
							// calculate thumbnail size
							  $targ_w = $thumbWidth;
							  $targ_h = floor( $height * ( $thumbWidth / $width ) );
							  
							  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$img_r,0,0,0,0,
							$targ_w,$targ_h,$width,$height);
							
							imagepng($dst_r,$config['public_folder_path']."images/testimonial/thumb/".$data['image-file']['name']);
						}
					
					}
				}
			}
			
			// Insert Data in DB
			$arrData = array("client_name" => $post['client_name'],
							 "client_image" => $image_name,
							 "description" => $post['description'],
							 "status" => $post['status']);
	
			$this->getTestimonialTable()->insertTestimonial($arrData);
			return $this->redirect()->toRoute('testimonial');
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('testimonial/testimonial/save.phtml');
		return $viewModel;
	}
	
	public function deleteAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		$id = $this->params('id');
		$arrWhere = array("testimonial_id" => $id);
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialDetail($arrWhere);
		if(!empty($arrTestimonial[0]['client_image']))
		{
			unlink($config['public_folder_path']."images/testimonial/".$arrTestimonial[0]['client_image']);
			unlink($config['public_folder_path']."images/testimonial/thumb/".$arrTestimonial[0]['client_image']);
		}
		$this->getTestimonialTable()->deleteTestimonial($arrWhere);
		return $this->redirect()->toRoute('testimonial');
	}
	
	/**
	 * Function to edit existing testimonial.
	 * Created On 30th Dec 2014
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function editAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		$id = $this->params('id');
		$arrWhere = array("testimonial_id" => $id);
		$arrTestimonial = $this->getTestimonialTable()->fetchTestimonialDetail($arrWhere);
		
		$form = new SaveForm('save-testimonial',array(), $arrTestimonial[0]);
		
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
				//echo "<pre>"; print_r($data); die;
				// ...or with array notation
				if($data['image-file']['name'] != "" && $data['image-file']['size'] < 4194304)
				{
					$validator = new \Zend\Validator\File\Extension(array('jpg', 'jpeg','gif','png'));
					$data['image-file']['name'] = rand().$data['image-file']['name'];
					copy($data['image-file']['tmp_name'],$config['public_folder_path']."images/testimonial/".$data['image-file']['name']);
					$image_name = "";
					if ($validator->isValid($config['public_folder_path']."images/testimonial/".$data['image-file']['name'])) {
					$thumbWidth = 150;
					$jpeg_quality = 90;
					// Create thumnail
						$image_name = $data['image-file']['name'];
						$src = $config['public_folder_path']."images/testimonial/".$data['image-file']['name'];
						if(exif_imagetype ($src ) == 1){
							$img_r = imagecreatefromgif($src);
							
							$width = imagesx( $img_r );
							$height = imagesy( $img_r );
							
							// calculate thumbnail size
							  $targ_w = $thumbWidth;
							  $targ_h = floor( $height * ( $thumbWidth / $width ) );
							  
							  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$img_r,0,0,0,0,
							$targ_w,$targ_h,$width,$height);
							
							imagegif($dst_r,$config['public_folder_path']."images/testimonial/thumb/".$data['image-file']['name']);
						}
						else if(exif_imagetype ($src ) == 2){
							
							$img_r = imagecreatefromjpeg($src);
							
							$width = imagesx( $img_r );
							$height = imagesy( $img_r );
							
							// calculate thumbnail size
							  $targ_w = $thumbWidth;
							  $targ_h = floor( $height * ( $thumbWidth / $width ) );
							  
							  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$img_r,0,0,0,0,
							$targ_w,$targ_h,$width,$height);
							
							imagejpeg($dst_r,$config['public_folder_path']."images/testimonial/thumb/".$data['image-file']['name'],$jpeg_quality);
						}
						else if(exif_imagetype ($src ) == 3){
						$img_r = imagecreatefrompng($src);
							
							$width = imagesx( $img_r );
							$height = imagesy( $img_r );
							
							// calculate thumbnail size
							  $targ_w = $thumbWidth;
							  $targ_h = floor( $height * ( $thumbWidth / $width ) );
							  
							  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$img_r,0,0,0,0,
							$targ_w,$targ_h,$width,$height);
							
							imagepng($dst_r,$config['public_folder_path']."images/testimonial/thumb/".$data['image-file']['name']);
						}
					
					}
				}
			}
			
			// Insert Data in DB
			$arrData = array("client_name" => $post['client_name'],
							 "description" => $post['description'],
							 "status" => $post['status']);
	
			if($image_name != "")
			{
				if(!empty($arrTestimonial[0]['client_image']))
				{
					unlink($config['public_folder_path']."images/testimonial/".$arrTestimonial[0]['client_image']);
					unlink($config['public_folder_path']."images/testimonial/thumb/".$arrTestimonial[0]['client_image']);
				}
				$arrData = array_merge($arrData, array("client_image"=>$image_name));
			}
			
			$this->getTestimonialTable()->updateTestimonial($arrData,$arrWhere);
			return $this->redirect()->toRoute('testimonial');
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'form' => $form
			));
		$viewModel->setTemplate('testimonial/testimonial/save.phtml');
		return $viewModel;
	}

	public function getTestimonialTable()
	{
		if (!$this->testimonialTable) {
            $sm = $this->getServiceLocator();
            $this->testimonialTable = $sm->get('Testimonial\Model\TestimonialTable');
        }
        return $this->testimonialTable;
	}
}
