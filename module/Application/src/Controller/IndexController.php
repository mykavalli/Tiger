<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;

use Laminas\View\Model\JsonModel;
use Laminas\Json\Json;

use Application\Model\HRMTable;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Ramsey\Uuid\Uuid;

class IndexController extends AbstractActionController
{
	private $hrmTable;
	
	public function __construct(HrmTable $hrmTable)
	{
		$this->hrmTable = $hrmTable;
		
		$this->pathDownload = getcwd().'/htdocs/public/downloads/';
		$this->pathReader = getcwd().'/htdocs/public/form/';
	}

    public function indexAction()
    {
        return $this->redirect()->toRoute('manage-attendance-v2');
		// $container = new Container('HeaderContent');
		// $container['HeaderTitle'] = 'Home';

        // $request = $this->getRequest();
        // if ($request->isPost()) {
        //     $data = $request->getPost();
        //     $data = $data->toArray(); // Convert to array if needed
        //     // Process the data as needed
        //     // For example, save it to the database or perform some action
            
        // }
        // return new ViewModel();
    }

    public function ManageAttendanceV2Action() {
		/* Check auth */
		// $auth = new AuthenticationService();
		// $containerUser = new Container('user');
		// $containerUser['Link'] = $_SERVER['REQUEST_URI'] != '/roles/check' ? $_SERVER['REQUEST_URI'] : '';
		// if (!$auth->hasIdentity()){
		// 	return $this->redirect()->toRoute('login');
		// }
		
        /* Check role */
        $routeMatch = $this->getEvent()->getRouteMatch();
		$routeName = $routeMatch->getMatchedRouteName();
		// $containerUser = new Container('user');
		// $checkAccess = $this->rolesTable->checkRole($containerUser['Role'], $routeName);
		
		// if (!$checkAccess)
		// {
		// 	$this->flashMessenger()->addErrorMessage($containerUser['Translate'][$containerUser['language']]['content_not_allow_access']);
		// 	return $this->redirect()->toRoute('users');
        // }

		$container = new Container('HeaderContent');
		$container['HeaderTitle'] = 'Chấm công tổng hợp';

		
		$dataSearch = [];
		$data = [];

		if (isset($_GET['user']) && $_GET['user'] != '') {
			$dataSearch['user'] = $_GET['user'];
		}
		if (isset($_GET['jobs']) && $_GET['jobs'] != '') {
			$dataSearch['jobs'] = $_GET['jobs'];
		}
		if (isset($_GET['date_report']) && $_GET['date_report'] != '') {
			$date = explode(" - ", $_GET['date_report']);
			$dataSearch['from'] = date("Y-m-d", strtotime(str_replace('/','-', $date[0]))).' 00:00:00';
			$dataSearch['to'] = date("Y-m-d", strtotime(str_replace('/','-', $date[1]))).' 23:59:59';
		} else {
			$dataSearch['from'] = date("Y-m-01").' 00:00:00';
			$dataSearch['to'] = date("Y-m-d", strtotime("last day of this month")).' 23:59:59';
		}
		$dataSearch['att_ver'] = 'v2';

		if ($dataSearch != null) {
			$data = $this->hrmTable->getAllAttendanceV2($dataSearch);
		}

		$request = $this->getRequest();
		if ($request->isPost()){
			$dataPost = $request->getPost()->toArray();
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'get_form_upload') {
				$this->layout('layout/layout_blank');
				
				$view = new ViewModel([
					'typePost' => 'upload_data',
					'idTag' => 'action_js'
				]);
				$view->setTemplate('application/upload-file');
				return $view;
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'upload_data') {
				$this->layout('layout/layout_blank');
				
				$allow_ext = ['xls', 'xlsx'];
				$file_aray = explode(".", $_FILES['file']['name']);
				$file_ext = end($file_aray);
				$dataImport = [];
						
				$linkMattTable = [];
				$linkAutoTable = [];
				
				if (in_array($file_ext, $allow_ext)){
					$reader = IOFactory::createReader('Xlsx');
					$spreadsheet = $reader->load($_FILES['file']['tmp_name']);
					
					// $thisSheet = $spreadsheet->getSheetByName('Dan-D-Pak');
					$thisSheet = $spreadsheet->getActiveSheet();

					if (isset($thisSheet) && $thisSheet != ''){
						/** Get table save from group worker */
						
						$i = 10;

						$oldUser = '';
						while ($i > 0) {
							$i++;
							
							if (trim($thisSheet->getCell('B'.$i)->getFormattedValue()) == '') {
								break;
							} else {
								$fullname = trim(str_replace(["'", "\""], "", $thisSheet->getCell('B'.$i)->getFormattedValue()));
								$position = trim(str_replace(["'", "\""], "", $thisSheet->getCell('C'.$i)->getFormattedValue()));
								$jobs = trim(str_replace(["'", "\""], "", $thisSheet->getCell('D'.$i)->getFormattedValue()));

								$dataImport['fullname'] = $fullname;
								$dataImport['position'] = $position;
								$dataImport['jobs'] = $jobs;
								$dataImport['time_create'] = date("Y-m-d H:i:s");

								for ($j=8; $j <= 38; $j++) {

									$dataImport['date_report'] = trim($thisSheet->getCell('E7')->getFormattedValue()).'-'.trim($thisSheet->getCell('C7')->getFormattedValue()).'-'.($j - 7);

									$columnLetter = Coordinate::stringFromColumnIndex($j);
									$cellValue = trim($thisSheet->getCell($columnLetter.$i)->getFormattedValue());

									if (trim($cellValue) == 'NP') {
										$dataImport['type_work'] = 2;
									} else {
										$dataImport['type_work'] = $jobs == 'Giao heo' || $jobs == 'Giao gà phạm tôn' ? 0 : 1; //0 is night; 1 is day
									}

									if ($cellValue != '') {

										//Start import list 
										$this->hrmTable->saveDataAttendance($dataImport);
									}
									$cellValue = '';
								}
							}
						}

						$this->flashMessenger()->addSuccessMessage("Cập nhật dữ liệu thành công");
						
						if ($containerUser['Link'] != ''){
							$link = $containerUser['Link'];
							$containerUser['Link'] = '';
							return $this->redirect()->toUrl((isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$link);
						} else {
							return $this->redirect()->refresh();
						}
					}
					else {
						$this->flashMessenger()->addErrorMessage('File không tồn tại dữ liệu/vui lòng kiểm tra lại tên sheet và ngày upload !');
						return $this->redirect()->refresh();
					}
				}
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'export_summary') {
				if ($data != null) {
					/** Export total data */
						$form = [];
						if (isset($dataPost['formData']) && $dataPost['formData'] != null) {
							foreach ($dataPost['formData'] as $key => $value) {
								$form[$value['name']] = $value['value'];
							}
						}
						
						$filename = 'Template_Attendance_Summary_V2.xlsx';

						// $fileDownload = 'BC_KPI_Total_'.$form['branch'].'_'.date("His").date("Ymd").'.xlsx';
						$fileDownload = 'Cham_cong_'.(str_replace("/", "_", $form['date_report'])).'.xlsx';
						
						$fileDownload = mb_convert_encoding($fileDownload, 'UTF-8', mb_detect_encoding($fileDownload, 'UTF-8, ISO-8859-1, ISO-8859-15', true));

						/** @var BaseReader $reader */
						$reader = IOFactory::createReaderForFile(($this->pathReader).$filename);
						$reader->setReadDataOnly(false);
						$spreadsheet = $reader->load(($this->pathReader).$filename);

						$worksheet = $spreadsheet->getSheetByName('Data');

						$reportData = $data;

						$worksheet->setCellValue('A4', 'Khoảng thời gian: '.$form['date_report']);
						
						$i = 6;
						if ($reportData != null) {
							
							foreach ($reportData as $key => $value) {
								if ($i > 7) {
									$worksheet->insertNewRowBefore($i, 1);
								}
								$worksheet->setCellValue('A'.$i, $i - 5);
								$worksheet->setCellValue('B'.$i, $value['fullname']);
								$worksheet->setCellValue('C'.$i, $value['position']);
								$worksheet->setCellValue('D'.$i, $value['sum_bhx']);
								$worksheet->setCellValue('E'.$i, $value['sum_bhx_pt']);
								$worksheet->setCellValue('F'.$i, $value['sum_bhx_vt']);
								$worksheet->setCellValue('G'.$i, $value['sum_ga_cho']);
								$worksheet->setCellValue('H'.$i, $value['sum_ga_nm']);
								$worksheet->setCellValue('I'.$i, $value['sum_heo']);
								$worksheet->setCellValue('J'.$i, $value['sum_trung']);
								$worksheet->setCellValue('K'.$i, $value['sum_px']);
								$worksheet->setCellValue('L'.$i, $value['sum_off']);
								$worksheet->setCellValue('M'.$i, "=SUM(D".$i.":K".$i.")");

								$i++;
							}
						}

						/**Create and save */
						$worksheet->setSelectedCell('A1');
						$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
						$writer->save($this->pathDownload . $fileDownload);
						$groupDownload['daily-report'] = ['filename' => $fileDownload, 'group' => $container['HeaderTitle'] = 'Download'];
					/** Export total kpi */
			
					return new JsonModel([
						'fileName' => $fileDownload
					]);
				}
			}
		}

        $view = new ViewModel([
			'data' => isset($data) ? $data : null,
			'listJob' => $this->hrmTable->getAllJob(null),
			'listUser' => $this->hrmTable->getAllUser(null)
		]);
		$view->setTemplate('application/index/manage-attendance-v2');
		return $view;
    }

    public function DetailAttendanceV2Action() {
		/* Check auth */
		// $auth = new AuthenticationService();
		// $containerUser = new Container('user');
		// $containerUser['Link'] = $_SERVER['REQUEST_URI'] != '/roles/check' ? $_SERVER['REQUEST_URI'] : '';
		// if (!$auth->hasIdentity()){
		// 	return $this->redirect()->toRoute('login');
		// }
		
        /* Check role */
        $routeMatch = $this->getEvent()->getRouteMatch();
		$routeName = $routeMatch->getMatchedRouteName();
		// $containerUser = new Container('user');
		// $checkAccess = $this->rolesTable->checkRole($containerUser['Role'], $routeName);
		
		// if (!$checkAccess)
		// {
		// 	$this->flashMessenger()->addErrorMessage($containerUser['Translate'][$containerUser['language']]['content_not_allow_access']);
		// 	return $this->redirect()->toRoute('users');
        // }

		$container = new Container('HeaderContent');
		$container['HeaderTitle'] = 'Chấm công chi tiết';

		
		$dataSearch = [];
		$data = [];

		if (isset($_GET['user']) && $_GET['user'] != '') {
			$dataSearch['user'] = $_GET['user'];
		}
		if (isset($_GET['jobs']) && $_GET['jobs'] != '') {
			$dataSearch['jobs'] = $_GET['jobs'];
		}
		if (isset($_GET['date_report']) && $_GET['date_report'] != '') {
			$date = explode(" - ", $_GET['date_report']);
			$dataSearch['from'] = date("Y-m-d", strtotime(str_replace('/','-', $date[0]))).' 00:00:00';
			$dataSearch['to'] = date("Y-m-d", strtotime(str_replace('/','-', $date[1]))).' 23:59:59';
		} else {
			$dataSearch = null;
		}
		if ($dataSearch != null) {
			$dataSearch['att_ver'] = 'v2';
		}
		
		// if (isset($_GET['month']) && $_GET['month'] != '' && isset($_GET['year']) && $_GET['year'] != '') {
		// 		$dataSearch['from'] = $_GET['year'].'-'.$_GET['month'].'-01 00:00:00';
		// 		$dataSearch['to'] = date("Y-m-d", strtotime("last day of this month ".$dataSearch['from'])).' 23:59:59';
		// } else {
		// 	$dataSearch = null;
		// }
		

		if ($dataSearch != null) {
			$data = $this->hrmTable->getAllDetailAttendance($dataSearch);

			if ($data != null) {
				 $new = [];
				foreach ($data as $key => $value) {
					if (!isset($new[$value['driver']][$value['jobs']][$value['date_report']])) {
						$new[$value['driver']][$value['jobs']][$value['date_report']] = [];
					}

					$new[$value['driver']][$value['jobs']][$value['date_report']] = $value;
				}

				$data = $new;
				// echo '<pre>';
				// print_r($data);
				// echo '<pre>';
				// die;
			}
		}

		$listJob = $this->hrmTable->getAllJob(null);
		if ($listJob != null) {
			$n = [];
			foreach ($listJob as $key => $value) {
				$n[$value['id']] = $value;
			}
			$listJob = $n;
		}

		$listUser = $this->hrmTable->getAllUser(['status' => 'active']);
		if ($listUser != null) {
			$n = [];
			foreach ($listUser as $key => $value) {
				$n[$value['id']] = $value;
			}
			$listUser = $n;
		}

		$request = $this->getRequest();
		if ($request->isPost()){
			$dataPost = $request->getPost()->toArray();
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'get_form_upload') {
				$this->layout('layout/layout_blank');
				
				$view = new ViewModel([
					'typePost' => 'upload_data',
					'templateFile' => 'Template_Upload_Attendance.xlsx',
					'idTag' => 'action_js'
				]);
				$view->setTemplate('application/upload-file');
				return $view;
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'upload_data') {
				$this->layout('layout/layout_blank');
				
				$allow_ext = ['xls', 'xlsx'];
				$file_aray = explode(".", $_FILES['file']['name']);
				$file_ext = end($file_aray);
				$dataImport = [];
						
				$linkMattTable = [];
				$linkAutoTable = [];
				
				if (in_array($file_ext, $allow_ext)){
					$reader = IOFactory::createReader('Xlsx');
					$spreadsheet = $reader->load($_FILES['file']['tmp_name']);
					
					$thisSheet = $spreadsheet->getSheetByName('Import');

					if (isset($thisSheet) && $thisSheet != ''){
						/** Get table save from group worker */
						
						$i = 4;
						$x = 0;

						while ($i > 0) {
							$i++;
							
							if (trim($thisSheet->getCell('AI'.$i)->getFormattedValue()) == '' || trim($thisSheet->getCell('AJ'.$i)->getFormattedValue()) == '') {
								break;
							} else {
								$driver = trim(str_replace(["'", "\""], "", $thisSheet->getCell('AI'.$i)->getFormattedValue()));
								$jobs = trim(str_replace(["'", "\""], "", $thisSheet->getCell('AJ'.$i)->getFormattedValue()));

								$dataImport['driver'] = $driver;
								$dataImport['jobs'] = $jobs;
								$dataImport['time_create'] = date("Y-m-d H:i:s");
								$dataImport['att_ver'] = 'v2';

								for ($j=4; $j <= 34; $j++) {

									$dataImport['date_report'] = trim($thisSheet->getCell('M3')->getFormattedValue()).'-'.sprintf("%02d", trim($thisSheet->getCell('G3')->getFormattedValue())).'-'.sprintf("%02d", ($j - 3));

									$columnLetter = Coordinate::stringFromColumnIndex($j);
									
									$cellValue = trim($thisSheet->getCell($columnLetter.$i)->getFormattedValue());

									if (strtoupper($cellValue) == 'NP') {
										$dataImport['type_work'] = 2;
									}elseif(strtoupper($cellValue) == 'PX'){
										$dataImport['type_work'] = 4;
									}elseif ($cellValue != '' && (strtolower($cellValue) == 'x' || strtolower($cellValue) == 'px') && isset($listJob[$jobs])) {
										$dataImport['type_work'] = isset($listJob[$jobs]) ? $listJob[$jobs]['type_works'] : 1; //0 is night; 1 is day; 2 is dayoff; 3 is special day to Phan Thiet
									}

									if ($cellValue != '') {

										//Start import list 
										$x ++;
										$this->hrmTable->saveDataAttendance($dataImport);
									}
									$cellValue = '';
								}
							}
						}

						$this->flashMessenger()->addSuccessMessage("Cập nhật dữ liệu thành công");
						return $this->redirect()->refresh();
					}
					else {
						$this->flashMessenger()->addErrorMessage('File không tồn tại dữ liệu/vui lòng kiểm tra lại tên sheet và ngày upload !');
						return $this->redirect()->refresh();
					}
				}
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'export_summary') {
				if ($data != null) {
					/** Export total data */
						$form = [];
						if (isset($dataPost['formData']) && $dataPost['formData'] != null) {
							foreach ($dataPost['formData'] as $key => $value) {
								$form[$value['name']] = $value['value'];
							}
						}
						
						$filename = 'Template_Attendance_Summary_V2.xlsx';

						// $fileDownload = 'BC_KPI_Total_'.$form['branch'].'_'.date("His").date("Ymd").'.xlsx';
						$fileDownload = 'Chấm_công_'.(str_replace("/", "_", $form['date_report'])).'.xlsx';
						
						$fileDownload = mb_convert_encoding($fileDownload, 'UTF-8', mb_detect_encoding($fileDownload, 'UTF-8, ISO-8859-1, ISO-8859-15', true));

						/** @var BaseReader $reader */
						$reader = IOFactory::createReaderForFile(($this->pathReader).$filename);
						$reader->setReadDataOnly(false);
						$spreadsheet = $reader->load(($this->pathReader).$filename);

						$worksheet = $spreadsheet->getSheetByName('Data');

						$reportData = $data;

						$worksheet->setCellValue('A4', 'Khoảng thời gian: '.$form['date_report']);
						
						$i = 6;
						if ($reportData != null) {
							
							foreach ($reportData as $key => $value) {
								if ($i > 7) {
									$worksheet->insertNewRowBefore($i, 1);
								}
								// $worksheet->setCellValue('A'.$i, $i - 5);
								// $worksheet->setCellValue('B'.$i, $value['fullname']);
								// $worksheet->setCellValue('C'.$i, $value['position']);
								// $worksheet->setCellValue('D'.$i, $value['sum_bhx']);
								// $worksheet->setCellValue('E'.$i, $value['sum_bhx_pt']);
								// $worksheet->setCellValue('F'.$i, $value['sum_ga_cho']);
								// $worksheet->setCellValue('G'.$i, $value['sum_ga_nm']);
								// $worksheet->setCellValue('H'.$i, $value['sum_trung']);
								// $worksheet->setCellValue('I'.$i, $value['sum_px']);
								// $worksheet->setCellValue('J'.$i, $value['sum_off']);
								// $worksheet->setCellValue('K'.$i, "=SUM(D".$i.":I".$i.")");

								$i++;
							}
						}

						/**Create and save */
						$worksheet->setSelectedCell('A1');
						$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
						$writer->save($this->pathDownload . $fileDownload);
						$groupDownload['daily-report'] = ['filename' => $fileDownload, 'group' => $container['HeaderTitle'] = 'Download'];
					/** Export total kpi */
			
					return new JsonModel([
						'fileName' => $fileDownload
					]);
				}
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'export_template') {
				/** Export template */
					
					$filename = $dataPost['arg_2'];

					// $fileDownload = 'BC_KPI_Total_'.$form['branch'].'_'.date("His").date("Ymd").'.xlsx';
					$fileDownload = $dataPost['arg_2'];
					
					$fileDownload = mb_convert_encoding($fileDownload, 'UTF-8', mb_detect_encoding($fileDownload, 'UTF-8, ISO-8859-1, ISO-8859-15', true));

					/** @var BaseReader $reader */
					$reader = IOFactory::createReaderForFile(($this->pathReader).$filename);
					$reader->setReadDataOnly(false);
					$spreadsheet = $reader->load(($this->pathReader).$filename);

					$worksheet = $spreadsheet->getSheetByName('Data');

					$styleArray = [
						'borders' => [
								'allBorders' => [
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
										'color' => ['argb' => '00000000'],
								],
						],
					];
					
					$i = 2;
					if ($listUser != null) {
						
						foreach ($listUser as $key => $value) {
							$worksheet->setCellValue('A'.$i, $value['id']);
							$worksheet->setCellValue('B'.$i, $value['fullname']);

							$i++;
						}
					}
					$worksheet->getStyle('A2:B'.($i - 1))->applyFromArray($styleArray);
					
					$j = 2;
					if ($listJob != null) {
						
						foreach ($listJob as $key => $value) {
							$worksheet->setCellValue('D'.$j, $value['id']);
							$worksheet->setCellValue('E'.$j, $value['job_name']);
							$worksheet->setCellValue('F'.$j, $value['job_ver']);

							$j++;
						}
					}
					$worksheet->getStyle('D2:F'.($j - 1))->applyFromArray($styleArray);

					$spreadsheet->setActiveSheetIndex(0);

					/**Create and save */
					$worksheet->setSelectedCell('A1');
					$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
					$writer->save($this->pathDownload . $fileDownload);
					$groupDownload['daily-report'] = ['filename' => $fileDownload, 'group' => $container['HeaderTitle'] = 'Download'];
				/** Export template */
		
				return new JsonModel([
					'fileName' => $fileDownload
				]);
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'delete_attendance') {
				if (isset($dataPost['date_report']) && $dataPost['date_report'] != '' && isset($dataPost['driver']) && $dataPost['driver'] != '') {
					$date = explode(" - ", $dataPost['date_report']);
					$dataDel['from'] = date("Y-m-d", strtotime(str_replace('/','-', $date[0]))).' 00:00:00';
					$dataDel['to'] = date("Y-m-d", strtotime(str_replace('/','-', $date[1]))).' 23:59:59';
					$dataDel['driver'] = $dataPost['driver'];
					$dataDel['att_ver'] = 'v2';
	
					$this->hrmTable->deleteAttendanceByDriver($dataDel);
				
					$this->flashMessenger()->addSuccessMessage("Xoá dữ liệu thành công");
					return $this->redirect()->refresh();
				}
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'get_form_del') {
				$this->layout('layout/layout_blank');
				
				$view = new ViewModel();
				$view->setTemplate('application/index/delete-attendance');
				return $view;
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'delete_attendance_month') {
				if (isset($dataPost['month']) && $dataPost['month'] != '' && isset($dataPost['year']) && $dataPost['year'] != '') {
					
					$dataDel['from'] = $dataPost['year'].'-'.$dataPost['month'].'-01 00:00:00';
					$dataDel['to'] = date("Y-m-d", strtotime($dataDel['from']." last day of this month")).' 23:59:59';
					$dataDel['att_ver'] = 'v2';
	
					$this->hrmTable->deleteAttendanceByMonth($dataDel);
				
					$this->flashMessenger()->addSuccessMessage("Xoá dữ liệu thành công");
					return $this->redirect()->refresh();
				}
			}
		}

        $view = new ViewModel([
			'data' => isset($data) ? $data : null,
			'listJob' => $listJob,
			'listUser' => $listUser
		]);
		$view->setTemplate('application/index/detail-attendance-v2');
		return $view;
    }

    public function ManageJobV2Action() {
		/* Check auth */
		// $auth = new AuthenticationService();
		// $containerUser = new Container('user');
		// $containerUser['Link'] = $_SERVER['REQUEST_URI'] != '/roles/check' ? $_SERVER['REQUEST_URI'] : '';
		// if (!$auth->hasIdentity()){
		// 	return $this->redirect()->toRoute('login');
		// }
		
        /* Check role */
        $routeMatch = $this->getEvent()->getRouteMatch();
		$routeName = $routeMatch->getMatchedRouteName();
		// $containerUser = new Container('user');
		// $checkAccess = $this->rolesTable->checkRole($containerUser['Role'], $routeName);
		
		// if (!$checkAccess)
		// {
		// 	$this->flashMessenger()->addErrorMessage($containerUser['Translate'][$containerUser['language']]['content_not_allow_access']);
		// 	return $this->redirect()->toRoute('users');
        // }

		$container = new Container('HeaderContent');
		$container['HeaderTitle'] = 'Công việc';

		
		$dataSearch = [];
		$data = [];

		if (isset($_GET['fullname']) && $_GET['fullname'] != '') {
			$dataSearch['fullname'] = $_GET['fullname'];
		}
		if (isset($_GET['jobs']) && $_GET['jobs'] != '') {
			$dataSearch['jobs'] = $_GET['jobs'];
		}
		// if (isset($_GET['date_report']) && $_GET['date_report'] != '') {
		// 	$date = explode(" - ", $_GET['date_report']);
		// 	$dataSearch['from'] = date("Y-m-d", strtotime(str_replace('/','-', $date[0]))).' 00:00:00';
		// 	$dataSearch['to'] = date("Y-m-d", strtotime(str_replace('/','-', $date[1]))).' 23:59:59';
		// } else {
		// 	$dataSearch['from'] = date("Y-m-d").' 00:00:00';
		// 	$dataSearch['to'] = date("Y-m-d").' 23:59:59';
		// }
		$dataSearch['att_ver'] = 'v2';

		$data = $this->hrmTable->getAllJob($dataSearch);
		// if ($dataSearch != null) {
		// }

		$request = $this->getRequest();
		if ($request->isPost()){
			$dataPost = $request->getPost()->toArray();
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'view_job') {
				$this->layout('layout/layout_blank');
				
				if (isset($dataPost['id'])) {
					$job = $this->hrmTable->getOneJob($dataPost['id']);
				}
				$view = new ViewModel([
					'job' => $job,
					'typePost' => 'upload_data',
					'idTag' => 'action_js'
				]);
				$view->setTemplate('application/index/manage-job-edit');
				return $view;
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'modify_job') {
				if (!isset($dataPost['id']) || isset($dataPost['id']) && $dataPost['id'] == '') {
					$idGen = Uuid::uuid4();
					$dataPost['id'] = $idGen->toString();
				}
				$dataPost['job_ver'] = 'v2';
				$this->hrmTable->saveDataJob($dataPost);
				
				$this->flashMessenger()->addSuccessMessage('Cập nhật thành công');
				return $this->redirect()->refresh();
			}
		}

        $view = new ViewModel([
			'data' => isset($data) ? $data : null,
			'listJob' => $this->hrmTable->getAllJob(null)
		]);
		$view->setTemplate('application/index/manage-job');
		return $view;
    }

    public function ManageUserAction() {
		/* Check auth */
		// $auth = new AuthenticationService();
		// $containerUser = new Container('user');
		// $containerUser['Link'] = $_SERVER['REQUEST_URI'] != '/roles/check' ? $_SERVER['REQUEST_URI'] : '';
		// if (!$auth->hasIdentity()){
		// 	return $this->redirect()->toRoute('login');
		// }
		
        /* Check role */
        $routeMatch = $this->getEvent()->getRouteMatch();
		$routeName = $routeMatch->getMatchedRouteName();
		// $containerUser = new Container('user');
		// $checkAccess = $this->rolesTable->checkRole($containerUser['Role'], $routeName);
		
		// if (!$checkAccess)
		// {
		// 	$this->flashMessenger()->addErrorMessage($containerUser['Translate'][$containerUser['language']]['content_not_allow_access']);
		// 	return $this->redirect()->toRoute('users');
        // }

		$container = new Container('HeaderContent');
		$container['HeaderTitle'] = 'Nhân sự';

		
		$dataSearch = [];
		$data = [];

		if (isset($_GET['user']) && $_GET['user'] != '') {
			$dataSearch['user'] = $_GET['user'];
		}
		if (isset($_GET['status']) && $_GET['status'] != '') {
			$dataSearch['status'] = $_GET['status'];
		} else {
			$dataSearch['status'] = 'active';
		}
		// if (isset($_GET['jobs']) && $_GET['jobs'] != '') {
		// 	$dataSearch['jobs'] = $_GET['jobs'];
		// }
		// if (isset($_GET['date_report']) && $_GET['date_report'] != '') {
		// 	$date = explode(" - ", $_GET['date_report']);
		// 	$dataSearch['from'] = date("Y-m-d", strtotime(str_replace('/','-', $date[0]))).' 00:00:00';
		// 	$dataSearch['to'] = date("Y-m-d", strtotime(str_replace('/','-', $date[1]))).' 23:59:59';
		// } else {
		// 	$dataSearch['from'] = date("Y-m-d").' 00:00:00';
		// 	$dataSearch['to'] = date("Y-m-d").' 23:59:59';
		// }

		$data = $this->hrmTable->getAllUser($dataSearch);
		// if ($dataSearch != null) {
		// }

		$request = $this->getRequest();
		if ($request->isPost()){
			$dataPost = $request->getPost()->toArray();
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'view_user') {
				$this->layout('layout/layout_blank');
				
				if (isset($dataPost['id'])) {
					$user = $this->hrmTable->getOneUser($dataPost['id']);
				}
				$view = new ViewModel([
					'user' => $user,
					'typePost' => 'upload_data',
					'idTag' => 'action_js'
				]);
				$view->setTemplate('application/index/manage-user-edit');
				return $view;
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'modify_user') {
				if (!isset($dataPost['id']) || isset($dataPost['id']) && $dataPost['id'] == '') {
					$idGen = Uuid::uuid4();
					$dataPost['id'] = $idGen->toString();
				}
				$this->hrmTable->saveDataUser($dataPost);
				
				$this->flashMessenger()->addSuccessMessage('Cập nhật thành công');
				return $this->redirect()->refresh();
			}
			
			if (isset($dataPost['type_post']) && $dataPost['type_post'] == 'delete_user') {
				if (isset($dataPost['id']) && $dataPost['id'] != '') {
					$this->hrmTable->deleteDataUser($dataPost['id']);
				}
				
				$this->flashMessenger()->addSuccessMessage('Cập nhật thành công');
				return $this->redirect()->refresh();
			}
		}

        return new ViewModel([
			'data' => isset($data) ? $data : null,
			'listUser' => $this->hrmTable->getAllUser(null)
		]);
    }
}
