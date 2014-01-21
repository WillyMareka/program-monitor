<?php
/**
 * @author Maestro
 */
class Upload extends MY_Controller {
	var $actualTables;
	function __construct() {
		parent::__construct();
		//$this -> load -> model('models_sugar/M_Sugar_ExternalFort_B3');
		$this -> load -> library('PHPexcel');
		ini_set('memory_size', '2048M');
	}

	function index() {
		$dataArr['contentView'] = 'upload/upload_v';

		$dataArr['uploaded'] = '';
		$dataArr['posted'] = 0;
		$this -> load -> view('template_v', $dataArr);
	}

	public function data_upload($activesheet = 0, $activity_id) {//convert .slk file to xlsx for upload

		//get activity ID

		$type = "";
		$start = 1;
		$config['upload_path'] = '././uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size'] = '1000000000';
		$this -> load -> library('upload', $config);

		//die();
		$file_1 = "upload_button";

		if ($type == 'slk') {
			//$edata = new Spreadsheet_Excel_Reader();

			// Set output Encoding.
			//$edata -> setOutputEncoding("CP1251");

			if ($_FILES['file_1']['tmp_name']) {
				$excelReader = PHPExcel_IOFactory::createReader('Excel2007');
				$excelReader -> setReadDataOnly(true);
				$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file_1']['tmp_name']);

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter -> save(str_replace('.php', '.xlsx', __FILE__));

			}

			$objPHPExcel = PHPExcel_IOFactory::load(str_replace('.php', '.xlsx', __FILE__));
		} else {
			$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file_1']['tmp_name']);
		}
		$objReader = new PHPExcel_Reader_Excel5();
		$arr = $objPHPExcel -> setActiveSheetIndex($activesheet) -> toArray(null, true, true, true);
		$highestColumm = $objPHPExcel -> setActiveSheetIndex($activesheet) -> getHighestColumn();
		$highestRow = $objPHPExcel -> setActiveSheetIndex($activesheet) -> getHighestRow();
		$data = array();
		$mytab = "";

		//echo $highestColumm;
		$data = $this -> getData($arr, $start, $highestColumm, $highestRow);
		//$data =json_encode($data);
		//echo($data);die;
		$data = $this -> formatData($data);
		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";
		//die ;
		//$this -> createTables();

		$this -> createAndSetProperties($data, $activity_id);
		//echo $activity_id;die;
		$data = $this -> makeTable($data);

		$dataArr['uploaded'] = $data;

		$dataArr['posted'] = 1;
		$dataArr['contentView'] = 'upload/upload_v';
		$this -> load -> module('home');
		$this -> home -> index();

	}

	public function data_uploadSpecific() {
		//convert .slk file to xlsx for upload
		$type = "";
		$start = 1;
		$config['upload_path'] = '././uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size'] = '1000000000';
		$this -> load -> library('upload', $config);

		//die();
		$file_1 = "upload_button";
		$activesheet = 0;
		if ($type == 'slk') {
			//$edata = new Spreadsheet_Excel_Reader();

			// Set output Encoding.
			//$edata -> setOutputEncoding("CP1251");

			if ($_FILES['file_1']['tmp_name']) {
				$excelReader = PHPExcel_IOFactory::createReader('Excel2007');
				$excelReader -> setReadDataOnly(true);
				$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file_1']['tmp_name']);

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter -> save(str_replace('.php', '.xlsx', __FILE__));

			}

			$objPHPExcel = PHPExcel_IOFactory::load(str_replace('.php', '.xlsx', __FILE__));
		} else {
			$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file_1']['tmp_name']);
		}
		$objReader = new PHPExcel_Reader_Excel5();
		$arr = $objPHPExcel -> setActiveSheetIndex($activesheet) -> toArray(null, true, true, true);
		$highestColumm = $objPHPExcel -> setActiveSheetIndex($activesheet) -> getHighestColumn();
		$highestRow = $objPHPExcel -> setActiveSheetIndex($activesheet) -> getHighestRow();
		$data = array();
		$mytab = "";

		//echo $highestColumm;
		$data = $this -> getDataSpecific($arr, '23', '149', 'C');

		echo "<pre>";
		print_r($data);
		echo "</pre>";
		//die;
		//$this -> createTables();
		//$this -> createAndSetProperties($data);
		//$data = $this -> makeTable($data);
		$this -> db -> insert_batch('activities', $data);
		$dataArr['uploaded'] = $data;

		$dataArr['posted'] = 1;
		$dataArr['contentView'] = 'upload/upload_v';
		$this -> load -> view('template_v', $dataArr);

	}

	public function upload_commit() {

		$size = $this -> input -> post('size');
		for ($i = 1; $i <= $size; $i++) {
			$data['testNO'][$i] = $this -> input -> post('testNO' . $i);
			$data['deviceID'][$i] = $this -> input -> post('deviceID' . $i);
			$data['asayID'][$i] = $this -> input -> post('asayID' . $i);
			$data['sampleNumber'][$i] = $this -> input -> post('sampleNumber' . $i);
			$data['cdCount'][$i] = $this -> input -> post('cdCount' . $i);
			$data['resultDate'][$i] = $this -> input -> post('resultDate' . $i);
			$data['operatorId'][$i] = $this -> input -> post('operatorId' . $i);

		}
		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";
		//save to DB
		//$this->db->insert_batch("test",$data);

	}

	public function getData($arr, $start, $highestColumn, $highestRow) {

		//possible columns
		for ($col = $start; $col < PHPExcel_Cell::columnIndexFromString($highestColumn) + 1; $col++) {

			for ($row = $start; $row < $highestRow; $row++) {
				$colString = PHPExcel_Cell::stringFromColumnIndex($col - 1);
				$title = $arr[$start][$colString];
				if ($title != "") {					
					//fields you want to save in DB
					$data[$title][] = $arr[$row][$colString];
				}
			}
		}

		return $data;
	}

	public function getDataSpecific($arr, $start, $end, $colString) {
		$data = array();
		//possible columns
		for ($row = $start; $row < $end; $row++) {

			if ($arr[$row][$colString] != "") {
				$data[] = array('activity_name' => $arr[$row][$colString], 'activity_start' => strtotime('2013-09-01'), 'activity_end' => strtotime('2013-12-01'));
			}
		}

		return $data;
	}

	public function formatData($data) {
		$rows = array();
		//var_dump($data);
		foreach ($data as $key => $value) {
			$title[] = $key;
			//$rowCounter = 0;
			for ($rowCounter = 1; $rowCounter < sizeof($value); $rowCounter++) {
				$rows['data'][$rowCounter][$key] = $value[$rowCounter];
			}

		}
		$rows['title'] = $title;
		return $rows;

	}

	public function makeTable($data) {
		$tableTitle = "<thead>";
		$tableTitle .= '<tr>';
		foreach ($data['title'] as $title) {
			$tableTitle .= '<th width="100px">' . $title . '</th>';

		}
		$tableTitle .= '</tr>';
		$tableTitle .= '</thead>';

		$tableData = '<tbody>';

		$j = 0;
		foreach ($data['data'] as $key => $data) {
			$tableData .= '<tr>';
			foreach ($data as $dataKey => $dataVal) {
				$tableData .= '<td>' . $dataVal . '</td>';
			}
			$tableData .= '</tr>';

		}
		$tableData .= '</tbody>';

		$table = $tableTitle . $tableData;
		return $table;
	}

	/**
	 * Initializes Tables in the Database
	 */
	public function createAndSetProperties($data, $activity_id) {
		$dataTables = array('subprogramlog');
		$title = $data['title'];
		//add to title
		$title[] = 'UPLOAD DATE';
		$title[] = 'ACTIVITY ID';
		$rowCounter = 0;
		$tableObj = array();
		foreach ($dataTables as $table) {

			foreach ($data['data'] as $data1) {
				//	echo'<pre>';print_r($data1);echo'</pre>';
				$currentTable = R::dispense($table);

				//link Cadre Name to Cadre ID
				$results = $this -> db -> get_where('cadre', array('cadre_name' => $data1['CADRE']));
				foreach ($results->result() as $cadre) {
					$data1['CADRE'] = $cadre -> cadre_id;
				}

				//convert date to timestamp
				$newDate = str_replace('/', '-', $data1['DATES']);
				$newDate = strtotime($newDate);
				$data1['DATES'] = $newDate;

				//set update time
				$data1['UPLOAD DATE'] = time();

				//set activity id
				$data1['ACTIVITY ID'] = $activity_id;
				//link FacilityName to MFC
				$results = $this -> db -> get_where('facility', array('facilityName' => $data1['WORK STATION']));
				foreach ($results->result() as $facility) {
					$data1['WORK STATION'] = $facility -> facilityMFC;
				}

				//remove excess columns
				unset($data1['county']);
				unset($data1['district']);
				foreach ($title as $val) {
					$valN = strtolower($val);
					$valN = str_replace(" ", "_", $valN);

					$currentTable -> setAttr($valN, $data1[$val]);

				}

				R::store($currentTable);
			}
		}

	}

	/**
	 * Reading the contents of a CSV
	 */
	public function readCSV() {
		$row = 1;
		if (($handle = fopen(base_url() . 'test.csv', "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";

				for ($c = 0; $c < $num; $c++) {

					$oldData[$row][] = $data[$c];

				}
				$row++;
			}
			fclose($handle);
		}
		$newData = array();
		foreach ($oldData as $key => $value) {
			if ($value[2] != "") {
				//exit ;
			} else {
				if ($value[0] == "" || $value[1] == "") {

				} else {
					$newData[] = $value;

				}

			}
			//echo '<pre>';
			//print_r($newData);
			//echo '</pre>';

		}

	}

	public function loadExcel() {
		$objPHPExcel = new PHPExcel();
		$data = $this -> db -> get('facility');
		$data = $data -> result_array();
		// Set properties
		echo date('H:i:s') . " Set properties\n";
		$objPHPExcel -> getProperties() -> setCreator("Maarten Balliauw");
		$objPHPExcel -> getProperties() -> setLastModifiedBy("Maarten Balliauw");
		$objPHPExcel -> getProperties() -> setTitle("Office 2007 XLSX Test Document");
		$objPHPExcel -> getProperties() -> setSubject("Office 2007 XLSX Test Document");
		$objPHPExcel -> getProperties() -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

		// Add some data
		echo date('H:i:s') . " Add some data\n";
		$objPHPExcel -> setActiveSheetIndex(0);

		$rowExec = 1;
		//Looping through the Counties
		//Looping Through a County
		foreach ($data as $row) {
			var_dump($row);
			foreach ($row as $facility) {

				//Looping through the cells per facility
				$column = 0;
				foreach ($facility as $cell) {
					$objPHPExcel -> getActiveSheet() -> setCellValueByColumnAndRow($column, $rowExec, $cell);
					$column++;
				}
				$rowExec++;
			}
		}

		//die ;

		// Rename sheet
		echo date('H:i:s') . " Rename sheet\n";
		$objPHPExcel -> getActiveSheet() -> setTitle('Simple');

		// Save Excel 2007 file
		echo date('H:i:s') . " Write to Excel2007 format\n";
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

		// We'll be outputting an excel file
		//		header('Content-type: application/vnd.ms-excel');

		// It will be called file.xls
		//		header('Content-Disposition: attachment; filename="file.xls"');

		// Write file to the browser
		$objWriter -> save('php://output');
		// Echo done
		echo date('H:i:s') . " Done writing file.\r\n";
	}

}
