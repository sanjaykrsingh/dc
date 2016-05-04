<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Inventory\Form\UploadForm;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Fixedvalues;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use FPDF;


class InventoryController extends AbstractActionController {

	protected $stockMasterTable;
	protected $stockImagesTable;
	protected $mmvDetailTable;
	protected $showroomMasterTable;
	protected $buyerEnquiryTable;
	protected $generalEnquiryTable;
	protected $sellerEnquiryTable;
	protected $userTable;

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
		
		$actionid = $this->params('id');
		$strWhere = "";
		$strWhereCount = "";
		$strOrder = "listing_date desc";
		$arrData = array();
		$arrmakemodel = array();
		$arrCnt = array();
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			$strWhere = $this->createWhereCond($arrData,$actionid);
			$strWhereCount = $this->createWhereCond($arrData,'0');
			if($arrData->sort_by!= "")
			{
				$strOrder = $arrData->sort_by;
			}
		}
		else
		{
			$strWhere = $this->createWhereCond(array(),$actionid);
		}
		
		$arr_fixed_values['month'] = Fixedvalues::get_month_array();
		$arrStock = $this->getStockMasterTable()->fetchCustomStockList($strWhere,$strOrder);
		$arrStockCount = $this->getStockMasterTable()->fetchStockCountByStatus($strWhereCount,$strOrder);
		foreach($arrStockCount as $stockcount)
		{
			$arrCnt[$stockcount['status']] =$stockcount['num'];
		}
		$strWhereModel = "";
		if($actionid == '1')
		{
			$strWhereModel .= "sm.status = '1'";
		}
		if($actionid == '2')
		{
			$strWhereModel .= "sm.status = '0'";
		}
		if(@$arrData->search_make != "")
			$arrmakemodel = $this->getStockMasterTable()->getStockModel("mmv.make_id = ".$arrData->search_make);
	
		$arrModel = $this->getStockMasterTable()->getStockMake($strWhereModel);
		$arrStatus = $this->getStockMasterTable()->getStockStatus($strWhereModel);
		
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		$arr_fixed_values['km_driven'] = Fixedvalues::get_km_driven();
		$arr_fixed_values['min_price'] = Fixedvalues::min_price_range();
		$arr_fixed_values['max_price'] = Fixedvalues::max_price_range();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
            'flashMessages' => $this->flashMessenger()->getMessages(),
			'arrStock' => $arrStock,
			'arrCnt' => $arrCnt,
			'arrModel' => $arrModel,
			'config'=>$config,
			'arrStatus' => $arrStatus,
			'arrmakemodel' => $arrmakemodel,
			'objSearch' => $arrData,
			'actionid' => $actionid,
			'start_year' => $start_year,
			'arr_fixed_values' => $arr_fixed_values
			));
		$viewModel->setTemplate('inventory/inventory/index.phtml');
		return $viewModel;
    }
	
	public function getStockModelByMakeAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$strwhere = "mmv.make_id = ".$arrData->make_id;
		$arrMMvDetail = $this->getStockMasterTable()->getStockModel($strwhere);
		echo json_encode($arrMMvDetail); die;
	}
	
	public function getVehicleDetailAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$strwhere = "stock_id = ".$arrData->stock_id."";
		$arrStockData = $this->getStockMasterTable()->fetchVehicleDetail($strwhere);
		echo json_encode($arrStockData); die;
	}
	
	public function checkRcExistAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$strwhere = "";
		if(isset($arrData->stock_id))
		{
			$strwhere = "stock_id != ".$arrData->stock_id." AND ";
		}
		$strwhere .= "sm.mmv_id != ".$arrData->mmv_id." AND registration_no = '".$arrData->reg_no."'";
		$arrStockData = $this->getStockMasterTable()->fetchVehicleDetail($strwhere);
		echo json_encode(count($arrStockData)); die;
	}
	
	public function exportPdfAction()
	{
		$stockid = $this->params('id');
		
		$arrStockData = $this->getStockMasterTable()->getStockExportDetail($stockid);
			//print_r($arrStockData); die;
			$pdf = new FPDF(); 
			$pdf->AddPage();

			$pdf->setleftmargin(10);
			$pdf->setX(0);
			$pdf->setY(30);
			
			$pdf->Ln(5);
			$pdf->setleftmargin(10);
			$pdf->Rect($pdf->GetX(), $pdf->GetY(), 190, 205, 'D');
			$actual_position_y = $pdf->GetY();

			$stockName = $arrStockData[0]['make_name']." ".$arrStockData[0]['model_name']." - ".$arrStockData[0]['make_year'];
			$pdf->SetFont('Arial','B',20);
			
			//Your actual content
			$pdf->SetXY(10, $actual_position_y);
			$pdf->Cell(190,15, $stockName, 0, 1, 'C');
			$x = $pdf->GetX();
			$y = $pdf->GetY();

			$col1=" REGN: ".$arrStockData[0]['registration_no'];
			$pdf->MultiCell(90, 15, $col1, 0,"C");

			$pdf->SetXY($x + 90, $y);

			$col2="STOCK ID: ".$arrStockData[0]['stock_id'];
			$pdf->MultiCell(100, 15, $col2,0,"C");
			
			
			
			$pdf->Cell(190,1, '', 0, 1, 'C',true);
			
			//$pdf->Rect($pdf->GetX(), $pdf->GetY(), 190, 55, 'D');
			$pdf->Ln(5);
			// Add Car Info
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			
			$pdf->MultiCell(30, 10, $pdf->Image("http://test.directcars.in/images/icon-fuel.png", $pdf->GetX()+20, $pdf->GetY()) ,0,"C");
			$pdf->SetXY($x + 30, $y);
			$pdf->MultiCell(60, 10, $arrStockData[0]["fuel_type"],0,"L");
			$pdf->SetXY($x + 90, $y);
			$pdf->MultiCell(30, 10, $pdf->Image("http://test.directcars.in/images/icon-transmission.png", $pdf->GetX()+20, $pdf->GetY()) ,0,"C");
			$pdf->SetXY($x + 120, $y);
			$pdf->MultiCell(70, 10, $arrStockData[0]["transmission_type"],0,"L");
			
			$pdf->Ln(5);
			
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->MultiCell(30, 10, $pdf->Image("http://test.directcars.in/images/icon-user.png", $pdf->GetX()+20, $pdf->GetY()) ,0,"C");
			$pdf->SetXY($x + 30, $y);
			$pdf->MultiCell(60, 10, $arrStockData[0]["owner"].' Owner ',0,"L");
			$pdf->SetXY($x + 90, $y);
			$pdf->MultiCell(30, 10, $pdf->Image("http://test.directcars.in/images/icon-meter.png", $pdf->GetX()+20, $pdf->GetY()) ,0,"C");
			$pdf->SetXY($x + 120, $y);
			$pdf->MultiCell(70, 10, $arrStockData[0]["kilometre"].' Kms ',0,"L");
			
			$pdf->Ln(5);
			
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->MultiCell(30, 10, $pdf->Image("http://test.directcars.in/images/icon-color.png", $pdf->GetX()+20, $pdf->GetY()) ,0,"C");
			$pdf->SetXY($x + 30, $y);
			$pdf->MultiCell(60, 10, $arrStockData[0]["color"],0,"L");
			$pdf->SetXY($x + 90, $y);
			$pdf->MultiCell(30, 10, $pdf->Image("http://test.directcars.in/images/icon-star.png", $pdf->GetX()+20, $pdf->GetY()) ,0,"C");
			$pdf->SetXY($x + 120, $y);
			$pdf->MultiCell(70, 10, $arrStockData[0]["certified_by"],0,"L");
			
			$pdf->Ln(5);
			// Arial 12
			$pdf->SetFont('Arial','',22);
			// Background color
			$pdf->SetFillColor(76,76,76);
			$pdf->SetTextColor(255);
			
			$pdf->Cell(190,10, 'WARRANTY & VALUE ADDED SERVICES', 0, 1, 'C',true);
			//$pdf->Rect($pdf->GetX(), $pdf->GetY(), 190, 45, 'D');
			
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetFont('Arial','',16);
			
			$pdf->Cell(180,15, 'warranty          : '.$arrStockData[0]['certified_warranty_details'], 0, 1, 'L',true);
			$pdf->Cell(180,15, 'Free Service      : '.$arrStockData[0]['free_services'], 0, 1, 'L',true);
			$pdf->Cell(180,15, 'On Road Assist : '.$arrStockData[0]['on_road_assistance'], 0, 1, 'L',true);
			$pdf->Ln(5);
			// Arial 12
			$pdf->SetFont('Arial','',22);
			// Background color
			$pdf->SetFillColor(76,76,76);
			$pdf->SetTextColor(255);
			
			$pdf->Cell(190,10, 'PRICING & OFFERS', 0, 1, 'C',true);
			//$pdf->Rect($pdf->GetX(), $pdf->GetY(), 190, 50, 'D');
			
			if($arrStockData[0]['hot_deal']== '1') $totprice = $arrStockData[0]['hoat_deal_price'];
			else $totprice = $arrStockData[0]['expected_price'];
			
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetFont('Arial','',24);
			$pdf->Cell(180,15, 'Price (Rs.) : '.number_format($totprice), 0, 1, 'L',true);
			$pdf->SetFont('Arial','',20);
			$pdf->MultiCell(180,15, 'Special Offer : '.wordwrap($arrStockData[0]['special_offer'],170," /n "), 0, 1, 'L',true);
			
			$pdf->Ln(5);
			// Arial 12
			$pdf->SetFont('Arial','',22);
			// Background color
			$pdf->SetFillColor(0,0,0);
			$pdf->SetTextColor(255);
			
			
			$x = $pdf->GetX();
			$y = $pdf->GetY();

			$col1=" Visit: directcars.in ";
			$pdf->MultiCell(90, 20, $col1, 0,"C",true);

			$pdf->SetXY($x + 90, $y);

			$col2="Call: 987 1872 513 ";
			$pdf->MultiCell(100, 20, $col2,0,"C",true);
			$pdf->Cell(900,20, $pdf->Image("http://directcars.in/images/logo_pdf.png", 10, 0), 0, 1, 'C');
			//$pdf->Ln(0);

			$filename = "stock-".$arrStockData[0]['stock_id'].".pdf";
			$pdf->Output();
			/**header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename='.basename($filename));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			ob_clean();
			flush();**/
				 
			//readfile($filename);
			//unlink($filename);
		die;
	}
	
	public function exportStockAction(){
		
		$actionid = $this->params('id');
		$strWhere = "";
		$strOrder = "";
		$arrData = array();
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			$strWhere = $this->createWhereCond($arrData,$actionid);
			if($arrData->sort_by!= "")
			{
				$strOrder = $arrData->sort_by;
			}
		}
		else
		{
			$strWhere = $this->createWhereCond(array(),$actionid);
		}
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX stock Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX stock Export");
		$objPHPExcel->getProperties()->setDescription("export stock on ".date("Y m d H:i:s").".");
		
		// Add header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'S.No.'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Listing Date'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Make');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Model/Varian');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Month');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Year'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Regn.No.'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Fuel Type'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Transmission'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Color'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Kms'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Ins. Validity');
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Owners'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Certfied By');
		$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Warranty'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Price'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Status'); 
		$arr_fixed_values['month'] = Fixedvalues::get_month_array();
		$arrStock = $this->getStockMasterTable()->fetchCustomStockList($strWhere,$strOrder);
		for($intS=0; $intS < count($arrStock); $intS++)
		{
			$rowid = $intS+2;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $rowid-1); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, date("d/m/Y",strtotime($arrStock[$intS]['listing_date']))); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $arrStock[$intS]['make_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, $arrStock[$intS]['model_name']." / ".$arrStock[$intS]['variant_name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $arr_fixed_values['month'][$arrStock[$intS]['make_month']]);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $arrStock[$intS]['make_year']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $arrStock[$intS]['registration_no']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $arrStock[$intS]['fuel_type']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, $arrStock[$intS]['transmission_type']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $arrStock[$intS]['color']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, number_format($arrStock[$intS]['kilometre'])); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $arrStock[$intS]['insurance_expiry']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowid, $arrStock[$intS]['owner']);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowid, $arrStock[$intS]['certified_by']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowid, $arrStock[$intS]['certified_warranty_details']);
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowid, number_format($arrStock[$intS]['expected_price'])); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowid, $arrStock[$intS]['car_status']); 
		}
		$objPHPExcel->getActiveSheet()->setTitle('Stock');
		$file_name = "Stock".date("Y_m_d_H_i_s").".xlsx";
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
	
	public function createWhereCond($arrData,$actionid)
	{
		$strWhere = "";
		if($actionid == '1')
		{
			$strWhere .= "sm.status = '1' AND ";
		}
		if($actionid == '2')
		{
			$strWhere .= "sm.status = '0' AND ";
		}
		
		if(!empty($arrData->carid))
		{
			$strWhere .= "(sm.registration_no = '".$arrData->carid."' OR sm.stock_id = '".$arrData->carid."') AND ";
		}
		if(!empty($arrData->search_make))
		{
			$strWhere .= "mmv.make_id = ".$arrData->search_make." AND ";
		}
		if(!empty($arrData->model))
		{
			$strWhere .= "mmv.model_id = ".$arrData->model." AND ";
		}
		if(!empty($arrData->min_km))
		{
			$strWhere .= "sm.kilometre >= ".$arrData->min_km." AND ";
		}
		if(!empty($arrData->max_km))
		{
			$strWhere .= "sm.kilometre <= ".$arrData->max_km." AND ";
		}
		if(!empty($arrData->min_year))
		{
			$strWhere .= "sm.make_year >= ".$arrData->min_year." AND ";
		}
		if(!empty($arrData->max_year))
		{
			$strWhere .= "sm.make_year <= ".$arrData->max_year." AND ";
		}
		if(!empty($arrData->min_price))
		{
			$strWhere .= "sm.expected_price >= ".$arrData->min_price." AND ";
		}
		if(!empty($arrData->max_price))
		{
			$strWhere .= "sm.expected_price <= ".$arrData->max_price." AND ";
		}
		if(!empty($arrData->car_status))
		{
			$strWhere .= "sm.car_status = '".$arrData->car_status."' AND ";
		}
		$strWhere = trim($strWhere," AND ");
		return $strWhere;
	}
	
	/**
	 * Notify user about inventory
	 * Created On 06th may 2015
	 * 
	 */
	public function notifyUserAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrStickId = $arrData->arrId;
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'str_stock_id' => implode(",",$arrStickId),
			));
		$viewModel->setTerminal(true);
		return $viewModel;
	}
	
	function sendNotificationUserAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		
		$arrStockId = explode(",",$arrData->str_stock_id);
		$arrStockDetail = $this->getStockMasterTable()->fetchSelectedStockList($arrStockId);
		for($intS=0; $intS < count($arrStockDetail); $intS++)
		{
			// Send SMS
			if($arrData->customer_mobile != "")
			{
				$this->notify_by_sms($arrStockDetail[$intS],$arrData->customer_mobile);
			}
			//Send Emaail
			if($arrData->customer_email != "")
			{
				$this->notify_by_email($arrStockDetail[$intS],$arrData);
			}
		}
		$this->flashMessenger()->addMessage('Notification has been sent!.');
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function notify_by_email($arrStockData,$arrData)
	{
		$config = $this->getServiceLocator()->get('Config');
		if($arrStockData['hot_deal']== '1') $price = $arrStockData['hoat_deal_price']; 
		else $price = $arrStockData['expected_price']; 
				
		$image_path = $config['image_path'];
		
		$certified_by = "";
		if($arrStockData['certified_by'] != "") {
			$certified_by = "<img src='".$image_path."/".str_replace(" ","_",strtolower($arrStockData['certified_by'])).".png' alt='".$arrStockData['certified_by']."'>";
		}
		
		$address = $arrStockData['land_mark'].",".$arrStockData['locality'].",".$arrStockData['city'].",".$arrStockData['state'].",".$arrStockData['pin_code'];
		
		$keyword = $arrStockData['make_name']." ".$arrStockData['model_name']." ".$arrStockData['variant_name']."-".$arrStockData['stock_id'];
		$keyword = str_replace(" ","-",$keyword);
		$keyword = strtolower($keyword);
		$longURL = $config['website_url']."/used-cars/view-car/".$keyword;
		
		$subject = "".$arrStockData['make_name']." ".$arrStockData['model_name']." ".$arrStockData['variant_name']."";
		$message = "Dear ".$arrData->customer_name.",<br><br>
		Please find below the details of the car you are interested in<br><br>
		Model: ".$arrStockData['make_name']." ".$arrStockData['model_name']." ".$arrStockData['variant_name']."<br><br> 
		Price: Rs.".$price."<br><br>
		Fuel Type: ".$arrStockData["fuel_type"]."<br><br>
		Transmission: ".$arrStockData["transmission_type"]." <br><br>
		KM Driven: ".$arrStockData["kilometre"]." <br><br>
		Owners: ".$arrStockData["owner"]." owner <br><br>
		Certified by: ".$certified_by." <br><br>

		View More Details Visit:  ".$longURL."<br><br>

		View Map to Our Showroom: <a href='https://www.google.co.in/maps/place/DIRECT+CARS/@28.486347,77.052987,15z/data=!4m2!3m1!1s0x0:0xd42ff862ab16c092?sa=X&ei=u7obVeSfC4O6uASJ_oHoCg&ved=0CH0Q_BIwCw'>Click Here</a><br>
		<br><br>

		Best Regards,
		<br><br>
		Team Direct Cars.
		<br><br>

		".$address."<br><br>

		Visit: https://directcars.in/   <br><br>Follow us:   <a href='https://plus.google.com/+DIRECTCARSGurgaon/about' target='NEW'><img src='".$config['image_path']."google_icon.png' width='20px'></a>
						<a  href='https://www.facebook.com/directcars.in' target='NEW'><img src='".$config['image_path']."facebook_icon.png' width='20px'></a>
						<a  href='https://twitter.com/directcarsindia' target='NEW'><img src='".$config['image_path']."twitter_icon.png' width='20px'></a>";
		$result = Fixedvalues::sendmail($message,$subject,$arrData->customer_email);
	}
	
	public function notify_by_sms($arrStockDetail,$customer_mobile)
	{
		$config = $this->getServiceLocator()->get('Config');
		$ACCESS_TOKEN = 'cfbe8735cf622268ca8cacaa487ca0051505b26f';
		$BITLY_URL = 'https://api-ssl.bitly.com/v3/user/link_save'; //SSL API Url
		$keyword = $arrStockDetail['make_name']." ".$arrStockDetail['model_name']." ".$arrStockDetail['variant_name']."-".$arrStockDetail['stock_id'];
		$keyword = str_replace(" ","-",$keyword);
		$keyword = strtolower($keyword);
		$longURL = $config['website_url']."/used-cars/view-car/".$keyword;
		
		$get_url=file_get_contents($BITLY_URL.'?access_token='.$ACCESS_TOKEN.'&longUrl='.$longURL);
		$url_content=json_decode($get_url);

		$datalink=$url_content->data->link_save->link;

		$message = "Please find the details of the ".$arrStockDetail['make_name']." ".$arrStockDetail['model_name']." ".$arrStockDetail['variant_name'].".  ".$datalink." .Visit https://directcars.in";
		Fixedvalues::sendSMS($message,$customer_mobile);
	
	}

	/**
	 * Function to import mak model variant xls in DB..
	 * Created On 18th Nov 2014
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function addAction()
	{
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			if(!isset($arrData->show_on_site))	$arrData->show_on_site =0;
			if(strtolower($arrData->regcity) == "other" && $arrData->otherregcity != "")
			{
				$arrData->regcity = $arrData->otherregcity;
			}
			if($arrData->financiers_name == 'other')
			{
				$arrData->financiers_name = $arrData->txt_financiers_name;
			}
			if($arrData->hypothecation == 0)
			{
				$arrData->financiers_name = '';
			}
			$arrStockData = array("mmv_id" => $arrData->mmv_id,
								  "showroom_id" => $arrData->showroom,
								  "color" => $arrData->color,
								  "fuel_type" => $arrData->fuel,
								  "owner" => $arrData->owner,
								  "expected_price" => $arrData->pricegaadi,
								  "make_year" => $arrData->year,
								  "make_month" => $arrData->month,
								  "kilometre" => $arrData->km,
								  "listing_date" => date("Y-m-d"),
								  "car_id" => rand(),
								  "certified_by" => $arrData->certified_by,
								  "registration_place" => $arrData->regcity,
								  "registration_no" => $arrData->reg,
								  "special_note" => "",
								  "transmission_type" => $arrData->tranmission,
								  "special_offer" => $arrData->offer,
								  "car_insurance" => $arrData->insurance,
								  "insurance_expiry" => $arrData->jimonth."-".$arrData->jiyear,
								  "free_services" => $arrData->free_services,
								  "certified_warranty_details" => $arrData->warranty,
								  "service_history" => $arrData->service_history,
								  "on_road_assistance" => $arrData->onraod_assistance,
								  "meta_description" => $arrData->meta_description,
								  "meta_keywords" => $arrData->meta_keywords,
								  "car_status" => $arrData->car_status,
								  "car_hignlights" => $arrData->car_hignlights,
								  "car_video" => $arrData->car_video,
								  "hypothecation" => $arrData->hypothecation,
								  "seller_financiers_name" => $arrData->financiers_name,
								  "purchase_date" => $arrData->purchase_date,
								  "seller_id" => $arrData->seller_id,
								  "show_on_site"  => $arrData->show_on_site,
								  "created_by" => "1");
				$stockid = $this->getStockMasterTable()->insertStock($arrStockData);
				$this->redirect()->toRoute('inventory', array("action" => "image-list",
					'id' => $stockid
				));
		}
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		$arr_fixed_values['month'] = Fixedvalues::get_month_array();
		$arr_fixed_values['owner'] = Fixedvalues::get_owner_array();
		$arr_fixed_values['color'] = Fixedvalues::get_color_array();
		$arr_fixed_values['fuel'] = Fixedvalues::get_fuel_type();
		$arr_fixed_values['tranmission'] = Fixedvalues::get_tranmission_type();
		$arr_fixed_values['regcity'] = Fixedvalues::get_reg_city();
		$arr_fixed_values['car_status'] = Fixedvalues::get_car_status();
		$arr_fixed_values['insurance'] = Fixedvalues::get_insurance();
		$arr_fixed_values['certified_by'] = Fixedvalues::get_certified_by();
		$arr_fixed_values['warranty'] = Fixedvalues::get_warranty();
		$arr_fixed_values['free_services'] = Fixedvalues::get_free_services();
		$arr_fixed_values['service_history'] = Fixedvalues::get_service_history();
		$arr_fixed_values['onraod_assistance'] = Fixedvalues::get_onraod_assistance();
		
		// Get Showroom list
		$arr_fixed_values['showroom'] = $this->getShowroomMasterTable()->fetchShowroomList();
		
		$viewModel =  new ViewModel(array(
            'arr_fixed_values' => $arr_fixed_values,
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'start_year' => $start_year
			));
		$viewModel->setTemplate('inventory/inventory/save.phtml');
		return $viewModel;
	}
	public function editAction()
	{
		$stockid = $this->params('id');
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			if(!isset($arrData->show_on_site))	$arrData->show_on_site =0;
			if(strtolower($arrData->regcity) == "other" && $arrData->otherregcity != "")
			{
				$arrData->regcity = $arrData->otherregcity;
			}
			if($arrData->financiers_name == 'other')
			{
				$arrData->financiers_name = $arrData->txt_financiers_name;
			}
			if($arrData->buyer_financiers_name == 'other')
			{
				$arrData->buyer_financiers_name = $arrData->txt_buyer_financiers_name;
			}
			if($arrData->hypothecation == 0)
			{
				$arrData->financiers_name = '';
			}
			if($arrData->hpa == 0)
			{
				$arrData->buyer_financiers_name = '';
			}
			$arrStockData = array("mmv_id" => $arrData->mmv_id,
								  "showroom_id" => $arrData->showroom,
								  "color" => $arrData->color,
								  "fuel_type" => $arrData->fuel,
								  "owner" => $arrData->owner,
								  "expected_price" => $arrData->pricegaadi,
								  "make_year" => $arrData->year,
								  "make_month" => $arrData->month,
								  "kilometre" => $arrData->km,
								  "listing_date" => date("Y-m-d"),
								  "car_id" => rand(),
								  "certified_by" => $arrData->certified_by,
								  "registration_place" => $arrData->regcity,
								  "registration_no" => $arrData->reg,
								  "special_note" => "",
								  "transmission_type" => $arrData->tranmission,
								  "special_offer" => $arrData->offer,
								  "car_insurance" => $arrData->insurance,
								  "insurance_expiry" => $arrData->jimonth."-".$arrData->jiyear,
								  "free_services" => $arrData->free_services,
								  "certified_warranty_details" => $arrData->warranty,
								  "service_history" => $arrData->service_history,
								  "on_road_assistance" => $arrData->onraod_assistance,
								  "meta_description" => $arrData->meta_description,
								  "meta_keywords" => $arrData->meta_keywords,
								  "car_status" => $arrData->car_status,
								  "car_hignlights" => $arrData->car_hignlights,
								  "car_video" => $arrData->car_video,
								  "hypothecation" => $arrData->hypothecation,
								  "seller_financiers_name" => $arrData->financiers_name,
								  "purchase_date" => ($arrData->purchase_date != '')?date("Y-m-d",strtotime($arrData->purchase_date)):'',
								  "seller_id" => $arrData->seller_id,
								  "show_on_site"  => $arrData->show_on_site,
								  "delivery_date" => ($arrData->delivery_date != '')?date("Y-m-d",strtotime($arrData->delivery_date)):'',
								  "delivered_by" => $arrData->delivered_by,
							      "sales_type" => $arrData->sales_type,
							      "buyer_id" => $arrData->buyer_id,
							      "hpa" => $arrData->hpa,
							      "buyer_financiers_name" => $arrData->buyer_financiers_name,
								  "created_by" => "1");
				$arrWhere = array("stock_id" => $stockid);
				$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);

		}
		$arrStockData = $this->getStockMasterTable()->getStockDetail($stockid);
		$arrUserData = $this->getUserTable()->fetchUserList(array("active"=>1));
		$start_year = $this->getMmvDetailTable()->get_min_start_year();
		$arr_fixed_values['month'] = Fixedvalues::get_month_array();
		$arr_fixed_values['owner'] = Fixedvalues::get_owner_array();
		$arr_fixed_values['color'] = Fixedvalues::get_color_array();
		$arr_fixed_values['fuel'] = Fixedvalues::get_fuel_type();
		$arr_fixed_values['tranmission'] = Fixedvalues::get_tranmission_type();
		$arr_fixed_values['regcity'] = Fixedvalues::get_reg_city();
		$arr_fixed_values['car_status'] = Fixedvalues::get_car_status();
		$arr_fixed_values['insurance'] = Fixedvalues::get_insurance();
		$arr_fixed_values['certified_by'] = Fixedvalues::get_certified_by();
		$arr_fixed_values['warranty'] = Fixedvalues::get_warranty();
		$arr_fixed_values['free_services'] = Fixedvalues::get_free_services();
		$arr_fixed_values['service_history'] = Fixedvalues::get_service_history();
		$arr_fixed_values['onraod_assistance'] = Fixedvalues::get_onraod_assistance();
		$arr_fixed_values['make_array'] = $this->getMmvDetailTable()->getMmvMakeList($arrStockData[0]['make_year']);
		$arr_fixed_values['model_array'] = $this->getMmvDetailTable()->getMmvModelList($arrStockData[0]['make_id'],$arrStockData[0]['make_year']);
		$arr_fixed_values['variant_array'] = $this->getMmvDetailTable()->getMmvVariantList($arrStockData[0]['model_id'],$arrStockData[0]['make_year']);
		
		// Get Showroom list
		$arr_fixed_values['showroom'] = $this->getShowroomMasterTable()->fetchShowroomList();
		
		$viewModel =  new ViewModel(array(
            'arr_fixed_values' => $arr_fixed_values,
			'arrUserData' => $arrUserData,
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'start_year' => $start_year,
			'arrStockData' => $arrStockData[0]
			));
		$viewModel->setTemplate('inventory/inventory/edit.phtml');
		return $viewModel;
	}
	
	public function stockSoldAction()
	{
		$stockid = $this->params('id');
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $stockid);
		$arrStockData = array("car_status" =>"Sold Out");
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function markHoatDealAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stock_id' => $arrData->id,
			));
		$viewModel->setTemplate('inventory/inventory/mark-hot-deal.phtml');
		$viewModel->setTerminal(true);
		return $viewModel;
	}
	
	public function saveMarkSoldAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $arrData->stock_id);
		if($arrData->buyer_financiers_name == 'other')
		{
			$arrData->buyer_financiers_name = $arrData->txt_buyer_financiers_name;
		}
		$arrStockData = array("car_status" =>"Sold Out",
							  "delivery_date" => $arrData->delivery_date,
							  "delivered_by" => $arrData->delivered_by,
							  "sales_type" => $arrData->sales_type,
							  "buyer_id" => $arrData->buyer_id,
							  "hpa" => $arrData->hpa,
							  "buyer_financiers_name" => $arrData->buyer_financiers_name);
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function markSoldAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrStockData = $this->getStockMasterTable()->getStockExportDetail($arrData->id);
		$arrUserData = $this->getUserTable()->fetchUserList(array("active"=>1));
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stock_id' => $arrData->id,
			'arrStockData'=>$arrStockData[0],
			'arrUserData'=> $arrUserData,
			));
		$viewModel->setTemplate('inventory/inventory/mark-sold.phtml');
		$viewModel->setTerminal(true);
		return $viewModel;
	}
	
	public function saveHotDealAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrWhere = array("stock_id" => $arrData->stock_id);
		$arrStockData = array(	"hot_deal" =>"1",
								"hoat_deal_price" => $arrData->hot_deal_price);
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		$this->flashMessenger()->addMessage('Hot deal has been set!.');
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function imageDeleteAction(){
		$image_id = $this->params('id');
		$config = $this->getServiceLocator()->get('Config');
		$arrWhere = array("stock_image_id" => $image_id);
		$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
		foreach($objStockImages as $stock)
		{
			$image_name = $stock->image_name;
			$stockid = $stock->stock_id;
		}
		
		unlink($config['public_folder_path']."images/stock/".$image_name);
		unlink($config['public_folder_path']."images/stock/crop/".$image_name);
		unlink($config['public_folder_path']."images/stock/src/".$image_name);
		unlink($config['public_folder_path']."images/stock/thumb/".$image_name);
		unlink($config['public_folder_path']."images/stock/carousel/".$image_name);
		$this->getStockImagesTable()->deleteStockImage($arrWhere);
		return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$stockid));
	}
	
	public function imageCropAction(){
		$image_id = $this->params('id');
		$config = $this->getServiceLocator()->get('Config');
		$arrWhere = array("stock_image_id" => $image_id);
		$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
		foreach($objStockImages as $stock)
		{
			$image_name = $stock->image_name;
			$stockid = $stock->stock_id;
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
			
			$thumb_src_w = 250;
			$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );

			$src = $config['public_folder_path']."images/stock/src/".$post['stock_img_src'];
			
			if(exif_imagetype ($src ) == 1){
				$img_r = imagecreatefromgif($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$targ_w,$targ_h,$_POST['w'],$_POST['h']);
				
				imagegif($dst_r,$config['public_folder_path']."images/stock/crop/".$post['stock_img_src']);
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
						$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
								
				imagegif($dst_r,$config['public_folder_path']."images/stock/thumb/".$post['stock_img_src']);
				
				$thumb_src_w = 100;
				$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
						$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
								
				imagegif($dst_r,$config['public_folder_path']."images/stock/carousel/".$post['stock_img_src']);
			}
			else if(exif_imagetype ($src ) == 2){
				$img_r = imagecreatefromjpeg($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$targ_w,$targ_h,$_POST['w'],$_POST['h']);

				imagejpeg($dst_r,$config['public_folder_path']."images/stock/crop/".$post['stock_img_src'],$jpeg_quality);
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
						$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);

				imagejpeg($dst_r,$config['public_folder_path']."images/stock/thumb/".$post['stock_img_src'],$jpeg_quality);
				
				$thumb_src_w = 100;
				$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
						$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);

				imagejpeg($dst_r,$config['public_folder_path']."images/stock/carousel/".$post['stock_img_src'],$jpeg_quality);
			}
			else if(exif_imagetype ($src ) == 3){
			//echo "hello"; echo exif_imagetype ($src ); die;
				
				$img_r = imagecreatefrompng($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
				$targ_w,$targ_h,$_POST['w'],$_POST['h']);
				//header('Content-Type: image/png');
				imagepng($dst_r,$config['public_folder_path']."images/stock/crop/".$post['stock_img_src']);
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
						$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				//header('Content-Type: image/png');
				imagepng($dst_r,$config['public_folder_path']."images/stock/thumb/".$post['stock_img_src']);
				
				$thumb_src_w = 100;
				$thumb_src_h = floor( $targ_h * ( $thumb_src_w / $targ_w ) );
				
				$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

				imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
						$thumb_src_w,$thumb_src_h,$_POST['w'],$_POST['h']);
				//header('Content-Type: image/png');
				imagepng($dst_r,$config['public_folder_path']."images/stock/carousel/".$post['stock_img_src']);
			}
					
			return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$stockid));
		
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stockid' => $stockid,
			'image_name'=> $image_name,
			'image_path' => $config['image_path'],
			));
		$viewModel->setTemplate('inventory/inventory/crop-image.phtml');
		return $viewModel;
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
		
					$arrWhere = array("stock_image_id" => $image_id);
					$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
					foreach($objStockImages as $stock)
					{
						$image_name = $stock->image_name;
						$stockid = $stock->stock_id;
					}
					unlink($config['public_folder_path']."images/stock/".$image_name);
					unlink($config['public_folder_path']."images/stock/crop/".$image_name);
					unlink($config['public_folder_path']."images/stock/src/".$image_name);
					unlink($config['public_folder_path']."images/stock/thumb/".$image_name);
					unlink($config['public_folder_path']."images/stock/carousel/".$image_name);
					$this->getStockImagesTable()->deleteStockImage($arrWhere);
		
				}
			}
		}
		return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$stockid));
	}
	
	public function setProfileImgAction()
	{
		$image_id = $this->params('id');
		
		$arrWhere = array("stock_image_id" => $image_id);
		$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
		foreach($objStockImages as $stock)
		{
			$stockid = $stock->stock_id;
		}
		
		/// unset profile image
		$arrWhere = array("stock_id" => $stockid);
		$arrImageData = array("is_profile_img" => "0");
		$this->getStockImagesTable()->updateStockImage($arrImageData,$arrWhere);
		
		// Set profile image
		$arrWhere = array("stock_image_id" => $image_id);
		$arrImageData = array("is_profile_img" => "1");
		$this->getStockImagesTable()->updateStockImage($arrImageData,$arrWhere);
		
		
		
		return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$stockid));
	}
	
	public function unmarkHoatDealAction()
	{
		$stockid = $this->params('id');
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $stockid);
		$arrStockData = array("hot_deal" =>"0");
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function notShowOnSiteAction()
	{
		$stockid = $this->params('id');
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $stockid);
		$arrStockData = array("show_on_site" =>"0");
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function showOnSiteAction()
	{
		$stockid = $this->params('id');
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $stockid);
		$arrStockData = array("show_on_site" =>"1");
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function deleteAction()
	{
		$stockid = $this->params('id');
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $stockid);
		$arrStockData = array("status" =>"0");
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'1'));
	}
	
	public function addStockAction() {
	
		$stockid = $this->params('id');
		
		//mark stock as deleted
		$arrWhere = array("stock_id" => $stockid);
		$arrStockData = array("status" =>"1");
		$this->getStockMasterTable()->updateStock($arrStockData,$arrWhere);
		
		return $this->redirect()->toRoute('inventory',array('action' => "index",'id'=>'2'));	
	}
	
	public function imageListAction()
	{
		$stockid = $this->params('id');
		$arrWhere = array("stock_id" => $stockid);
		$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
		$form = new UploadForm('upload-form');
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stockid' => $stockid,
			'config' => $config,
			'objStockImages'=> $objStockImages,
			'form' => $form
			));
		$viewModel->setTemplate('inventory/inventory/image-list.phtml');
		return $viewModel;
	}
	
	public function imageViewAction()
	{
		$stockid = $this->params('id');
		$arrWhere = array("stock_id" => $stockid);
		$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stockid' => $stockid,
			'config' => $config,
			'objStockImages'=> $objStockImages
			));
		$viewModel->setTemplate('inventory/inventory/image-view.phtml');
		return $viewModel;
	}
	
	public function imageSortAction()
	{
		$stockid = $this->params('id');
		
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			foreach($arrData as $key => $value)
			{
				$arrKey = explode("_",$key);
				$image_id = $arrKey[1];
				$arrWhere = array("stock_image_id" => $image_id);
				$arrImageData = array("dis_order" => $value);
				$this->getStockImagesTable()->updateStockImage($arrImageData,$arrWhere);
			}
			return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$stockid));
		}
		
		$arrWhere = array("stock_id" => $stockid);
		$objStockImages = $this->getStockImagesTable()->fetchStockImageList($arrWhere);
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stockid' => $stockid,
			'config'=>$config,
			'objStockImages'=> $objStockImages
			));
		$viewModel->setTemplate('inventory/inventory/image-sort.phtml');
		return $viewModel;
	}
	
	public function imageTagAction()
	{
		$request = $this->getRequest();
		if($request->isPost())
		{
			$arrData = $request->getPost(); 
			
			for($intI=0; $intI<count($arrData->image_id); $intI++)
			{
				$arrWhere = array("stock_image_id" => $arrData->image_id[$intI]);
				$arrImageData = array("image_title" => $arrData->image_title[$intI]);
				$this->getStockImagesTable()->updateStockImage($arrImageData,$arrWhere);
			}
			return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$arrData->stockid));
		}
	}
	
	public function addImageAction()
	{
		ini_set("memory_limit","1024M");
		$form = new UploadForm('upload-form');
		$stockid = $this->params('id');
		$config = $this->getServiceLocator()->get('Config');
		$request = $this->getRequest();
		if ($request->isPost()) {
			set_time_limit(0);
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
				//$validator = new \Zend\Validator\File\Extension(array('jpg', 'jpeg','gif','png'));
				$arrImageData = $data['image-file'];
				
				for($i=0; $i < count($arrImageData); $i++)
				{
					if($arrImageData[$i]['name'] != "")
					{
						$data['image-file'] = $arrImageData[$i];
						$data['image-file']['name'] = rand().$data['image-file']['name'];
						$data['image-file']['name'] = str_replace(" ","_",$data['image-file']['name']);
						copy($data['image-file']['tmp_name'],$config['public_folder_path']."images/stock/".$data['image-file']['name']);
						
						//if ($validator->isValid("public/images/stock/".$data['image-file']['name'])) 
						{
							
							$thumbWidth = 250;
							$jpeg_quality = 90;
							
							$thumb_src_width = 640;

							$src = $config['public_folder_path']."images/stock/".$data['image-file']['name'];
							
							if(exif_imagetype ($src ) == 1){
								$img_r = imagecreatefromgif($src);
								
								$width = imagesx( $img_r );
								$height = imagesy( $img_r );
								
								// calculate thumbnail size
								  $targ_w = $thumbWidth;
								  $targ_h = floor( $height * ( $thumbWidth / $width ) );
								  
								  $thumb_src_w = $thumb_src_width;
								  $thumb_src_h = floor( $height * ( $thumb_src_width / $width ) );
								  
								  
								 // $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$targ_w,$targ_h,$width,$height);
								
								imagegif($dst_r,$config['public_folder_path']."images/stock/thumb/".$data['image-file']['name']);
								
								$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$thumb_src_w,$thumb_src_h,$width,$height);
								
								imagegif($dst_r,$config['public_folder_path']."images/stock/src/".$data['image-file']['name']);
								
								$thumb_src_w = 100;
								$thumb_src_h = floor( $height * ( $thumb_src_w / $width ) );
								
								$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$thumb_src_w,$thumb_src_h,$width,$height);
												
								imagegif($dst_r,$config['public_folder_path']."images/stock/carousel/".$data['image-file']['name']);
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
								
								imagejpeg($dst_r,$config['public_folder_path']."images/stock/thumb/".$data['image-file']['name'],$jpeg_quality);
								
								$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$thumb_src_w,$thumb_src_h,$width,$height);
								
								imagejpeg($dst_r,$config['public_folder_path']."images/stock/src/".$data['image-file']['name'],$jpeg_quality);
								
								$thumb_src_w = 100;
								$thumb_src_h = floor( $height * ( $thumb_src_w / $width ) );
								
								$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$thumb_src_w,$thumb_src_h,$width,$height);
								
								imagejpeg($dst_r,$config['public_folder_path']."images/stock/carousel/".$data['image-file']['name'],$jpeg_quality);
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
								
								imagepng($dst_r,$config['public_folder_path']."images/stock/thumb/".$data['image-file']['name']);
								
								$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$thumb_src_w,$thumb_src_h,$width,$height);
								
								imagepng($dst_r,$config['public_folder_path']."images/stock/src/".$data['image-file']['name']);
								
								$thumb_src_w = 100;
								$thumb_src_h = floor( $height * ( $thumb_src_w / $width ) );
								
								$dst_r = ImageCreateTrueColor( $thumb_src_w, $thumb_src_h );

								imagecopyresampled($dst_r,$img_r,0,0,0,0,
								$thumb_src_w,$thumb_src_h,$width,$height);
								
								imagepng($dst_r,$config['public_folder_path']."images/stock/carousel/".$data['image-file']['name']);
							}
							
							// Insert Data in DB
							$arrData = array("stock_id" => $stockid,
											 "image_name" => $data['image-file']['name'],
											 "image_title" => "");
					
							$this->getStockImagesTable()->insertStockImage($arrData);
						}
						//else
						//{
							//$this->flashMessenger()->addMessage('Invalid file type.');
						//}
					}
				}
				return $this->redirect()->toRoute('inventory',array('action' => "image-list",'id'=>$stockid));
			}
		}
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'stockid' => $stockid,
			'form' => $form
			));
		$viewModel->setTemplate('inventory/inventory/add-image.phtml');
		return $viewModel;
	}
	
	public function dashboardAction()
	{
		$total_active_cars = $this->getStockMasterTable()->getStockCnt(array("status"=>"1"));
		$total_img_cars = $this->getStockMasterTable()->getStockWithImgCnt(array("status"=>"1"));
		$total_hot_deal_cars = $this->getStockMasterTable()->getStockCnt(array("status"=>"1","hot_deal"=>"1"));
		$arrBudget = array("0-1" => $this->getStockMasterTable()->getStockcnt('sm.expected_price <= 100000 AND status = "1"'),
						   "1-2" => $this->getStockMasterTable()->getStockcnt('sm.expected_price >= 100000 AND sm.expected_price <= 200000 AND status = "1"'),
						   "2-3" => $this->getStockMasterTable()->getStockcnt('sm.expected_price >= 200000 AND sm.expected_price <= 300000 AND status = "1"'),
						   "3-5" => $this->getStockMasterTable()->getStockcnt('sm.expected_price >= 300000 AND sm.expected_price <= 500000 AND status = "1"'),
						   "5-10" => $this->getStockMasterTable()->getStockcnt('sm.expected_price >= 500000 AND sm.expected_price <= 1000000 AND status = "1"'),
						   "10-0" => $this->getStockMasterTable()->getStockcnt('sm.expected_price >= 1000000 AND status = "1"'),
						   );
						   
		$arrFuelType = $this->getStockMasterTable()->getStockCntByFuel(array("status"=>"1"));
		$arrMM = $this->getStockMasterTable()->getStockCntByModel(array("status"=>"1"));
		
		date_default_timezone_set(date_default_timezone_get());
		
		$dt = strtotime(date("Y-m-d"));
		
		$res['start'] = date("Y-m-d");
		$buyer_enquiry['today'] = $this->getBuyerEnquiryTable()->countBuyerEnquery($res); 
		$seller_enquiry['today'] = $this->getSellerEnquiryTable()->countSellerEnquery($res); 
		$general_enquiry['today'] = $this->getGeneralEnquiryTable()->countGeneralEnquery($res); 
		$arrBFualType = $this->getBuyerEnquiryTable()->getStockCntByFuel($res);
		$arrBuyerFuelType['today'] = array();
		foreach($arrBFualType as $stock)
		{
			$arrBuyerFuelType['today'][$stock['fuel_type']] = $stock['cnt'];
		}
		
		$arrBMM = $this->getBuyerEnquiryTable()->getStockCntByModel($res);
		$arrBuyerMM['today'] = array();
		foreach($arrBMM as $stock)
		{
			$arrBuyerMM['today'][$stock['make_name']." ".$stock['model_name']] = $stock['cnt'];
		}
		
		$arrBuyerBudget['today'] = array("0-1" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price <= 100000'),
						   "1-2" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 100000 AND sm.expected_price <= 200000'),
						   "2-3" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 200000 AND sm.expected_price <= 300000'),
						   "3-5" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 300000 AND sm.expected_price <= 500000'),
						   "5-10" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 500000 AND sm.expected_price <= 1000000'),
						   "10-0" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 1000000'),
						   );
		
		$res['start'] = date('Y-m-d',strtotime("-1 days"));
		$buyer_enquiry['yesterday'] = $this->getBuyerEnquiryTable()->countBuyerEnquery($res); 
		$seller_enquiry['yesterday'] = $this->getSellerEnquiryTable()->countSellerEnquery($res);
		$general_enquiry['yesterday'] = $this->getGeneralEnquiryTable()->countGeneralEnquery($res);
		$arrBFualType = $this->getBuyerEnquiryTable()->getStockCntByFuel($res);
		$arrBuyerFuelType['yesterday'] = array();
		foreach($arrBFualType as $stock)
		{
			$arrBuyerFuelType['yesterday'][$stock['fuel_type']] = $stock['cnt'];
		}
		
		$arrBMM = $this->getBuyerEnquiryTable()->getStockCntByModel($res);
		$arrBuyerMM['yesterday'] = array();
		foreach($arrBMM as $stock)
		{
			$arrBuyerMM['yesterday'][$stock['make_name']." ".$stock['model_name']] = $stock['cnt'];
		}

		$arrBuyerBudget['yesterday'] = array("0-1" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price <= 100000'),
				   "1-2" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 100000 AND sm.expected_price <= 200000'),
				   "2-3" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 200000 AND sm.expected_price <= 300000'),
				   "3-5" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 300000 AND sm.expected_price <= 500000'),
				   "5-10" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 500000 AND sm.expected_price <= 1000000'),
				   "10-0" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 1000000'),
				   );
		
		$res['start'] = date('N', $dt)==1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
		$res['end'] = date('N', $dt)==7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));	
		$buyer_enquiry['week'] = $this->getBuyerEnquiryTable()->countBuyerEnquery($res); 
		$seller_enquiry['week'] = $this->getSellerEnquiryTable()->countSellerEnquery($res);
		$general_enquiry['week'] = $this->getGeneralEnquiryTable()->countGeneralEnquery($res);
		$arrBFualType = $this->getBuyerEnquiryTable()->getStockCntByFuel($res);
		$arrBuyerFuelType['week'] = array();
		foreach($arrBFualType as $stock)
		{
			$arrBuyerFuelType['week'][$stock['fuel_type']] = $stock['cnt'];
		}
		
		$arrBMM = $this->getBuyerEnquiryTable()->getStockCntByModel($res);
		$arrBuyerMM['week'] = array();
		foreach($arrBMM as $stock)
		{
			$arrBuyerMM['week'][$stock['make_name']." ".$stock['model_name']] = $stock['cnt'];
		}
		
		$arrBuyerBudget['week'] = array("0-1" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price <= 100000'),
				   "1-2" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 100000 AND sm.expected_price <= 200000'),
				   "2-3" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 200000 AND sm.expected_price <= 300000'),
				   "3-5" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 300000 AND sm.expected_price <= 500000'),
				   "5-10" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 500000 AND sm.expected_price <= 1000000'),
				   "10-0" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 1000000'),
				   );
		
		$res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
		$res['end'] = date('Y-m-d', strtotime('last day of this month', $dt));
		$buyer_enquiry['month'] = $this->getBuyerEnquiryTable()->countBuyerEnquery($res); 
		$seller_enquiry['month'] = $this->getSellerEnquiryTable()->countSellerEnquery($res);
		$general_enquiry['month'] = $this->getGeneralEnquiryTable()->countGeneralEnquery($res);
		$arrBuyerFuelType['month'] = $this->getBuyerEnquiryTable()->getStockCntByFuel($res);
		$arrBuyerMM['month'] = $this->getBuyerEnquiryTable()->getStockCntByModel($res);

		$arrBuyerBudget['month'] = array("0-1" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price <= 100000'),
				   "1-2" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 100000 AND sm.expected_price <= 200000'),
				   "2-3" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 200000 AND sm.expected_price <= 300000'),
				   "3-5" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 300000 AND sm.expected_price <= 500000'),
				   "5-10" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 500000 AND sm.expected_price <= 1000000'),
				   "10-0" => $this->getBuyerEnquiryTable()->getStockcnt($res,'sm.expected_price >= 1000000'),
				   );
		
		$buyer_enquiry['all'] = $this->getBuyerEnquiryTable()->countBuyerEnquery(array()); 
		$seller_enquiry['all'] = $this->getSellerEnquiryTable()->countSellerEnquery(array());
		$general_enquiry['all'] = $this->getGeneralEnquiryTable()->countGeneralEnquery(array()); 
		
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			'total_active_cars' => $total_active_cars,
			'total_img_cars' => $total_img_cars,
			'total_hot_deal_cars' => $total_hot_deal_cars,
			'arrBudget' => $arrBudget,
			'arrFuelType'=>$arrFuelType,
			'arrMM' => $arrMM,
			'buyer_enquiry' => $buyer_enquiry,
			'seller_enquiry' => $seller_enquiry,
			'general_enquiry' => $general_enquiry,
			'arrBuyerBudget' => $arrBuyerBudget,
			'arrBuyerFuelType' => $arrBuyerFuelType,
			'arrBuyerMM' =>$arrBuyerMM,
			));
		return $viewModel;
	}
	
	public function getMmvDetailTable()
	{
		if (!$this->mmvDetailTable) {
            $sm = $this->getServiceLocator();
            $this->mmvDetailTable = $sm->get('Mmv\Model\MmvDetailTable');
        }
        return $this->mmvDetailTable;
	}

	public function getStockMasterTable()
	{
		if (!$this->stockMasterTable) {
            $sm = $this->getServiceLocator();
            $this->stockMasterTable = $sm->get('Inventory\Model\StockMasterTable');
        }
        return $this->stockMasterTable;
	}
	public function getStockImagesTable()
	{
		if (!$this->stockImagesTable) {
            $sm = $this->getServiceLocator();
            $this->stockImagesTable = $sm->get('Inventory\Model\StockImagesTable');
        }
        return $this->stockImagesTable;
	}
	public function getShowroomMasterTable()
	{
		if (!$this->showroomMasterTable) {
            $sm = $this->getServiceLocator();
            $this->showroomMasterTable = $sm->get('Showroom\Model\ShowroomMasterTable');
        }
        return $this->showroomMasterTable;
	}
	
	public function getBuyerEnquiryTable()
	{
		if (!$this->buyerEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->buyerEnquiryTable = $sm->get('Enquiry\Model\BuyerEnquiryTable');
        }
        return $this->buyerEnquiryTable;
	}
	
	public function getGeneralEnquiryTable()
	{
		if (!$this->generalEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->generalEnquiryTable = $sm->get('Enquiry\Model\GeneralEnquiryTable');
        }
        return $this->generalEnquiryTable;
	}
	
	public function getSellerEnquiryTable()
	{
		if (!$this->sellerEnquiryTable) {
            $sm = $this->getServiceLocator();
            $this->sellerEnquiryTable = $sm->get('Enquiry\Model\SellerEnquiryTable');
        }
        return $this->sellerEnquiryTable;
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
