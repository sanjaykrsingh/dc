<?php

namespace Mmv\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

class MmvController extends AbstractActionController {

	protected $mmvDetailTable;
	protected $modelMasterTable;
	protected $makeMasterTable;
	protected $variantMasterTable;
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

	public function getMakeByYearAction()
	{
		 $request = $this->getRequest();
		 $arrData = $request->getPost(); 
		 $arrMMvDetail = $this->getMmvDetailTable()->getMmvMakeList($arrData->year);
		 echo json_encode($arrMMvDetail); die;
	}
	
	public function getModelByMakeAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrMMvDetail = $this->getMmvDetailTable()->getMmvModelList($arrData->make_id,$arrData->year);
		 echo json_encode($arrMMvDetail); die;
	}
	public function getVariantByModelAction()
	{
		$request = $this->getRequest();
		$arrData = $request->getPost(); 
		$arrMMvDetail = $this->getMmvDetailTable()->getMmvVariantList($arrData->model_id,$arrData->year);
		 echo json_encode($arrMMvDetail); die;
	}
	
    public function indexAction() {
		$viewModel =  new ViewModel(array(
			'flashMessages' => $this->flashMessenger()->getMessages(),
			));
		$viewModel->setTemplate('mmv/mmv/index.phtml');
		return $viewModel;
    }

	/**
	 * Function to import mak model variant xls in DB..
	 * Created On 18th Nov 2014
	 * Created By Hemita (Hoodalytics.com)
	 **/
	public function addAction()
	{
		ini_set('memory_limit', '512M');
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('Config');
		if ($request->isPost()) {
			set_time_limit(0);
			// Make certain to merge the files info!
			$data = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			$validator = new \Zend\Validator\File\Extension(array('xlsx'));
			$data['upload_mmv']['name'] = date("YmdHis").$data['upload_mmv']['name'];
			copy($data['upload_mmv']['tmp_name'],$config['public_folder_path']."images/mmv/".$data['upload_mmv']['name']);
			//echo "<pre>"; print_r($_SERVER); die;
			//die;
			
			$inputFileName = $config['image_absolute_path']."mmv/".$data['upload_mmv']['name'];
			//$inputFileName = str_replace("/","\\",$inputFileName);
			if ($validator->isValid($inputFileName)) 
			{
				set_time_limit(0);
				
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				
				//  Get worksheet dimensions
				$sheet = $objPHPExcel->getSheet(0); 
				$highestRow = $sheet->getHighestRow(); 
				$highestColumn = $sheet->getHighestColumn();
				
				//  Loop through each row of the worksheet in turn
				for ($row = 2; $row <= $highestRow; $row++){ 
					//  Read a row of data into an array
					$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
													NULL,
													TRUE,
													FALSE);
						if($rowData[0][0] != "") {
						
							// Get Make Id and Insert Data in make Master
							$makeId = 0;
							$arrData = array("make_name" => $rowData[0][0]);
							if($rowData[0][4] != "" && $rowData[0][4] > 0) {
								$makeId = $rowData[0][4];
								$arrWhere = array("make_id" => $rowData[0][4]);
								$this->getMakeMasterTable()->updateMake($arrData,$arrWhere);
							}
							else
							{
								$arrMake = $this->getMakeMasterTable()->fetchMakeList($arrData);
								
								foreach($arrMake as $value)
								{
									$makeId = $value->make_id;
								}
								if($makeId == 0)
								{
									//insert Make master value
									$makeId = $this->getMakeMasterTable()->insertMake($arrData);
								}
							}
					
						
						// Get Model Id and Insert Data in Model Master..
						$arrData = array("model_name" => $rowData[0][1]);
						$modelId = 0;
						if($rowData[0][5] != "" && $rowData[0][5] > 0) {
							$modelId = $rowData[0][5];
							$arrWhere = array("model_id" => $rowData[0][5]);
							$this->getModelMasterTable()->updateModel($arrData,$arrWhere);
						}
						else
						{
							$arrModel = $this->getModelMasterTable()->fetchModelList($arrData);
							
							foreach($arrModel as $value)
							{
								$modelId = $value->model_id;
							}
							if($modelId == 0)
							{
								//insert Model master value
								$modelId = $this->getModelMasterTable()->insertModel($arrData);
							}
						}
						
						// Get Variant Id and Insert Data in Variant Master..
						$arrData = array("variant_name" => $rowData[0][2]);
						$variantId = 0;
						if($rowData[0][6] != "" && $rowData[0][6] > 0) {
							$variantId = $rowData[0][6];
							$arrWhere = array("variant_id" => $rowData[0][6]);
							$this->getVariantMasterTable()->updateVariant($arrData,$arrWhere);
						}
						else
						{
							$arrVariant = $this->getVariantMasterTable()->fetchVariantList($arrData);
							
							foreach($arrVariant as $value)
							{
								$variantId = $value->variant_id;
							}
							if($variantId == 0)
							{
								//insert Model master value
								$variantId = $this->getVariantMasterTable()->insertVariant($arrData);
							}
						}
						
						// Insert Data in MMV master
						$arrData = array("make_id" => $makeId,
										 "model_id" => $modelId,
										 "variant_id" => $variantId);
						$arrMmv = $this->getMmvDetailTable()->fetchMmvList($arrData);
						
						$arrInsertData = array(	"make_id" => $makeId,
													"model_id" => $modelId,
													"variant_id"=> $variantId,
													"segment" => $rowData[0][7],
													"type" => $rowData[0][8],
													"bod_style" => $rowData[0][9],
													"start_year" => $rowData[0][10], 
													"end_year" => $rowData[0][11],
													"model_discontunued" => $rowData[0][12],
													"noofcylinders" =>  $rowData[0][15],
													"engine_cc" => $rowData[0][16],
													"power" => $rowData[0][17],
													"fuel_type" => $rowData[0][18],
													"transmission_type" => $rowData[0][19],
													"ground_clearance" => $rowData[0][20],
													"seating_capacity" => $rowData[0][21],
													"boot_space_ltrs"  => $rowData[0][22],
													"alloy_wheels" => $rowData[0][23],
													"rear_wash_wiper" => $rowData[0][24],
													"front_fog_lights" => $rowData[0][25],
													"electric_mirrors" => $rowData[0][26],
													"electric_foldable_mirrors" => $rowData[0][27],
													"auto_headlamps" => $rowData[0][28],
													"rain_sensing_wipers" => $rowData[0][29],
													"aircondition" => $rowData[0][30],
													"rear_ac_vents" => $rowData[0][31],
													"power_window" => $rowData[0][32],
													"auto_down_power_windows" => $rowData[0][33],
													"anti_pinch_power_windows" => $rowData[0][34],
													"defogger_front" => $rowData[0][35],
													"leather_seats" => $rowData[0][36],
													"power_seats" => $rowData[0][37],
													"driver_seat_height_adjust" => $rowData[0][38],
													"sun_roof" => $rowData[0][39],
													"keyless_entry" => $rowData[0][40],
													"keyless_start" => $rowData[0][41],
													"cruise_control" => $rowData[0][42],
													"chilled_glovebox" => $rowData[0][43],
													"central_locking" => $rowData[0][44],
													"abs" => $rowData[0][45],
													"traction_control" => $rowData[0][46],
													"electronic_stability_control" => $rowData[0][47],
													"airbags" => $rowData[0][48],
													"immobilizer" => $rowData[0][49],
													"Childsafetylocks" => $rowData[0][50],
													"reversing_camera" => $rowData[0][51],
													"cupholders" => $rowData[0][52],
													"remotefuellid" => $rowData[0][53],
													"tachometer" => $rowData[0][54],
													"gear_shift_indicators" => $rowData[0][55],
													"power_steering" => $rowData[0][56],
													"bluetooth_connectivity" => $rowData[0][57],
													"audio_system" => $rowData[0][58],
													"front_speakers" => $rowData[0][59],
													"rear_speakers" => $rowData[0][60],
													"steeringmountedaudiocontrols" => $rowData[0][61],
													"default_image" => $rowData[0][13],
													"created_by" => $this->login_user_id);
						if($arrMmv->count() == 0)
						{	
							$this->getMmvDetailTable()->insertMMV($arrInsertData);
						}
						else
						{
							foreach($arrMmv as $mmv)
							{
								$update_mmv_id = $mmv->mmv_id;
							}
							$arrWhere = array("mmv_id" => $update_mmv_id);
							$this->getMmvDetailTable()->updateMMV($arrInsertData,$arrWhere);
						}
					} 
					
				}
				$this->flashMessenger()->addMessage('File Uploaded Successfully.');
				
			}
			else
			{
				$this->flashMessenger()->addMessage('Invalid file type.');
			}
		}
		return $this->redirect()->toRoute('mmv');
	}
	
	public  function exportAction()
	{
	
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("Direct Cars");
		$objPHPExcel->getProperties()->setLastModifiedBy("Direct Cars");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX MMV Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX MMV Export");
		$objPHPExcel->getProperties()->setDescription("export mmv on ".date("Y m d H:i:s").".");


		// Add header
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Make'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Model'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Variant'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'MMV Id');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Make Id');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Model Id');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Version Id'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Segment'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Type'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Bodystyle'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Start Year'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'End Year'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Discontunued');
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Default Image'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Review');
		$objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Noofcylinders'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Engine Cc'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Power');
		$objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Fueltype');
		$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Transmissiontype');
		$objPHPExcel->getActiveSheet()->SetCellValue('U1', 'GroundClearance MM');
		$objPHPExcel->getActiveSheet()->SetCellValue('V1', 'SeatingCapacity');
		$objPHPExcel->getActiveSheet()->SetCellValue('W1', 'BootSpace Ltrs');
		$objPHPExcel->getActiveSheet()->SetCellValue('X1', 'Alloywheels');
		$objPHPExcel->getActiveSheet()->SetCellValue('Y1', 'Rear Wash wiper');
		$objPHPExcel->getActiveSheet()->SetCellValue('Z1', 'Front fog lights');
		$objPHPExcel->getActiveSheet()->SetCellValue('AA1', 'Electric Mirrors');
		$objPHPExcel->getActiveSheet()->SetCellValue('AB1', 'Electric Foldable Mirrors');
		$objPHPExcel->getActiveSheet()->SetCellValue('AC1', 'Auto Headlamps');
		$objPHPExcel->getActiveSheet()->SetCellValue('AD1', 'Rain Sensing Wipers');
		$objPHPExcel->getActiveSheet()->SetCellValue('AE1', 'Aircondition');
		$objPHPExcel->getActiveSheet()->SetCellValue('AF1', 'Rear Ac Vents');
		$objPHPExcel->getActiveSheet()->SetCellValue('AG1', 'Powerwindows');
		$objPHPExcel->getActiveSheet()->SetCellValue('AH1', 'Auto Down Power Windows');
		$objPHPExcel->getActiveSheet()->SetCellValue('AI1', 'Anti Pinch Power Windows');
		$objPHPExcel->getActiveSheet()->SetCellValue('AJ1', 'Defogger');
		$objPHPExcel->getActiveSheet()->SetCellValue('AK1', 'Leatherseats');
		$objPHPExcel->getActiveSheet()->SetCellValue('AL1', 'Powerseats');
		$objPHPExcel->getActiveSheet()->SetCellValue('AM1', 'Driver Seat Height Adjust');
		$objPHPExcel->getActiveSheet()->SetCellValue('AN1', 'Sunroof');
		$objPHPExcel->getActiveSheet()->SetCellValue('AO1', 'Keyless Entry');
		$objPHPExcel->getActiveSheet()->SetCellValue('AP1', 'Keyless Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('AQ1', 'Cruise Control');
		$objPHPExcel->getActiveSheet()->SetCellValue('AR1', 'Chilled Glovebox');
		$objPHPExcel->getActiveSheet()->SetCellValue('AS1', 'Central Locking');
		$objPHPExcel->getActiveSheet()->SetCellValue('AT1', 'ABS');
		$objPHPExcel->getActiveSheet()->SetCellValue('AU1', 'Traction control');
		$objPHPExcel->getActiveSheet()->SetCellValue('AV1', 'Electronic Stability control');
		$objPHPExcel->getActiveSheet()->SetCellValue('AW1', 'Airbags');
		$objPHPExcel->getActiveSheet()->SetCellValue('AX1', 'Immobilizer');
		$objPHPExcel->getActiveSheet()->SetCellValue('AY1', 'Childsafetylocks');
		$objPHPExcel->getActiveSheet()->SetCellValue('AZ1', 'Reversing Camera');
		$objPHPExcel->getActiveSheet()->SetCellValue('BA1', 'Cupholders');
		$objPHPExcel->getActiveSheet()->SetCellValue('BB1', 'Remotefuellid');
		$objPHPExcel->getActiveSheet()->SetCellValue('BC1', 'Tachometer');
		$objPHPExcel->getActiveSheet()->SetCellValue('BD1', 'Gear Shift Indicators');
		$objPHPExcel->getActiveSheet()->SetCellValue('BE1', ' Power Steering');
		$objPHPExcel->getActiveSheet()->SetCellValue('BF1', 'Bluetooth Connectivity');
		$objPHPExcel->getActiveSheet()->SetCellValue('BG1', 'Audio System');
		$objPHPExcel->getActiveSheet()->SetCellValue('BH1', 'Front speakers');
		$objPHPExcel->getActiveSheet()->SetCellValue('BI1', 'Rear speakers');
		$objPHPExcel->getActiveSheet()->SetCellValue('BJ1', 'Steeringmountedaudiocontrols');
		ini_set('memory_limit', '512M');
		//Fetch Data from DB
		//echo "<pre>";
		$arrMMV = $this->getMmvDetailTable()->fetchMmvExport();
		$rowid = 2;
		foreach($arrMMV as $mmv)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowid, $mmv['make_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowid, $mmv['model_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowid, $mmv['variant_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowid, $mmv['mmv_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowid, $mmv['make_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowid, $mmv['model_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowid, $mmv['variant_id']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowid, $mmv['segment']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowid, $mmv['type']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowid, $mmv['bod_style']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowid, $mmv['start_year']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowid, $mmv['end_year']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowid, $mmv['model_discontunued']);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowid, $mmv['default_image']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowid, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowid, $mmv['noofcylinders']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowid, $mmv['engine_cc']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowid, $mmv['power']);
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowid, $mmv['fuel_type']);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowid, $mmv['transmission_type']);
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowid, $mmv['ground_clearance']);
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowid, $mmv['seating_capacity']);
			$objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowid, $mmv['boot_space_ltrs']);
			$objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowid, $mmv['alloy_wheels']);
			$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowid, $mmv['rear_wash_wiper']);
			$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowid, $mmv['front_fog_lights']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowid, $mmv['electric_mirrors']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowid, $mmv['electric_foldable_mirrors']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowid, $mmv['auto_headlamps']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowid, $mmv['rain_sensing_wipers']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowid, $mmv['aircondition']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowid, $mmv['rear_ac_vents']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowid, $mmv['power_window']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowid, $mmv['auto_down_power_windows']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowid, $mmv['anti_pinch_power_windows']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowid, $mmv['defogger_front']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowid, $mmv['leather_seats']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowid, $mmv['power_seats']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AM'.$rowid, $mmv['driver_seat_height_adjust']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AN'.$rowid, $mmv['sun_roof']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AO'.$rowid, $mmv['keyless_entry']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AP'.$rowid, $mmv['keyless_start']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$rowid, $mmv['cruise_control']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AR'.$rowid, $mmv['chilled_glovebox']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AS'.$rowid, $mmv['central_locking']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AT'.$rowid, $mmv['abs']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AU'.$rowid, $mmv['traction_control']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AV'.$rowid, $mmv['electronic_stability_control']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AW'.$rowid, $mmv['airbags']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AX'.$rowid, $mmv['immobilizer']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AY'.$rowid, $mmv['Childsafetylocks']);
			$objPHPExcel->getActiveSheet()->SetCellValue('AZ'.$rowid, $mmv['reversing_camera']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BA'.$rowid, $mmv['cupholders']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BB'.$rowid, $mmv['remotefuellid']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BC'.$rowid, $mmv['tachometer']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BD'.$rowid, $mmv['gear_shift_indicators']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BE'.$rowid, $mmv['power_steering']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BF'.$rowid, $mmv['bluetooth_connectivity']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BG'.$rowid, $mmv['audio_system']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BH'.$rowid, $mmv['front_speakers']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BI'.$rowid, $mmv['rear_speakers']);
			$objPHPExcel->getActiveSheet()->SetCellValue('BJ'.$rowid, $mmv['steeringmountedaudiocontrols']);
			$rowid++;
		}
		
		// Rename sheet
		echo date('H:i:s') . " Rename sheet\n"; 
		$objPHPExcel->getActiveSheet()->setTitle('MMV');
		$file_name = 'export_mmv_'.date("YmdHis").".xlsx";
				
		//// Save Excel 2007 file
		echo date('H:i:s') . " Write to Excel2007 format\n";
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

	public function getMmvDetailTable()
	{
		if (!$this->mmvDetailTable) {
            $sm = $this->getServiceLocator();
            $this->mmvDetailTable = $sm->get('Mmv\Model\MmvDetailTable');
        }
        return $this->mmvDetailTable;
	}
	public function getModelMasterTable()
	{
		if (!$this->modelMasterTable) {
            $sm = $this->getServiceLocator();
            $this->modelMasterTable = $sm->get('Mmv\Model\ModelMasterTable');
        }
        return $this->modelMasterTable;
	}
	public function getMakeMasterTable()
	{
		if (!$this->makeMasterTable) {
            $sm = $this->getServiceLocator();
            $this->makeMasterTable = $sm->get('Mmv\Model\MakeMasterTable');
        }
        return $this->makeMasterTable;
	}
	public function getVariantMasterTable()
	{
		if (!$this->variantMasterTable) {
            $sm = $this->getServiceLocator();
            $this->variantMasterTable = $sm->get('Mmv\Model\VariantMasterTable');
        }
        return $this->variantMasterTable;
	}
}
