<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Started extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('started_model');
    }

	public function index()
	{
		$data['page_title'] = "Simulation";
		$this->load->view('template/header', $data); // Header File
		$this->load->view('started/content_started'); // Main File
		$this->load->view('template/footer'); // Footer File

		// $this->kmeans_cluster();
		
	}

	public function Report()
	{
		$data['page_title'] = "Simulation";
		$this->load->view('template/header', $data); // Header File
		$this->load->view('started/content_started'); // Main File
		$this->load->view('template/footer'); // Footer File

		// $this->kmeans_cluster();
	}

	public function DownloadDataset()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="REPORT_Dataset.xlsx"');

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'PRODUCT_CODE');
		$sheet->setCellValue('B1', 'PRODUCT');
		$sheet->setCellValue('C1', 'QTY_IN');
		$sheet->setCellValue('D1', 'PRICE / PCS');
		$sheet->setCellValue('E1', 'QTY_OUT');

		$writer = new Xlsx($spreadsheet);
		$writer->save("php://output");
	}

	public function UploadDataset()
	{
		$upload_file=$_FILES['upload_file_dataset']['name'];
		$extension=pathinfo($upload_file,PATHINFO_EXTENSION);

		$reader= new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

		$spreadsheet=$reader->load($_FILES['upload_file']['tmp_name']);
		$sheetdata=$spreadsheet->getActiveSheet()->toArray();
		$sheetcount=count($sheetdata);
		if($sheetcount>1)
		{
			$data=array();
			for ($i=1; $i < $sheetcount; $i++) {
				$product_code=$sheetdata[$i][0]; 
				$product_name=$sheetdata[$i][1];
				$quantity_in=$sheetdata[$i][2];
				$price=$sheetdata[$i][3];
				$quantity_out=$sheetdata[$i][4];
				$data[]=array(
					'product_code'=>$product_code,
					'product_name'=>$product_name,
					'quantity_in'=>$quantity_in,
					'price'=>$price,
					'quantity_out'=>$quantity_out
				);
			}

			$inserdata=$this->started_model->insert_batch($data);

			if($inserdata)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success">Successfully Added.</div>');
				redirect('started');
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Data Not uploaded. Please Try Again.</div>');
				redirect('started');
			}
		}
	}

	public function DownloadTemplate()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="REPORT_TEMPLATE.xlsx"');

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'PRODUCT_CODE');
		$sheet->setCellValue('B1', 'PRODUCT');
		$sheet->setCellValue('C1', 'PRICE / PCS');
		$sheet->setCellValue('D1', 'QTY_IN');
		$sheet->setCellValue('E1', 'QTY_OUT');

		$writer = new Xlsx($spreadsheet);
		$writer->save("php://output");
	}

	public function UploadReport()
	{

		// $this->kmeans_cluster();

		$PLAFOND = $_POST['plafond'];

		$deletedata=$this->started_model->delete();

		$upload_file = $_FILES['upload_file_report']['name']; //GET FROM VIEW upload_file_report
		$extension=pathinfo($upload_file,PATHINFO_EXTENSION);
		$reader= new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet=$reader->load($_FILES['upload_file_report']['tmp_name']);
		$sheetdata=$spreadsheet->getActiveSheet()->toArray();
		$sheetcount=count($sheetdata);
		if($sheetcount>1)
		{
			$data=array();
			for ($i=1; $i < $sheetcount; $i++) { 
				$product_code=$sheetdata[$i][0];
				$product_name=$sheetdata[$i][1];
				$quantity_in=$sheetdata[$i][2];
				$price=$sheetdata[$i][3];
				$quantity_out=$sheetdata[$i][4];
				$data[]=array(
					'product_code'=>$product_code,
					'product_name'=>$product_name,
					'quantity_in'=>$quantity_in,
					'price'=>$price,
					'quantity_out'=>$quantity_out
				);
			}

			// fungsi insert data
			$inserdata=$this->started_model->insert($data);

			// set plafond
			$all_stock_boundary = $PLAFOND;
			$result['plafond'] = $PLAFOND;

			// process algortima kmeans
			$result['hasil'] = $this->kmeans_preprocessing($all_stock_boundary, $data);

			// get kmeans cluster
			$result['data_cluster'] = $this->kmeans_cluster();

			// get data upload
			$result['data_upload'] =  $this->started_model->getDataUpload();

			// print_r($result['data_upload']);die();
			
			$data['page_title'] = "Output";
			$this->load->view('template/header', $data); // Header File
			$this->load->view('started/content_output', $result); // Main File
			$this->load->view('template/footer'); // Footer File
		}
	}

	public function spreadsheet_export()
	{
		//fetch my data
		$productlist=$this->started_model->product_list();
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="product.xlsx"');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'S.No');
		$sheet->setCellValue('B1', 'Product Name');
		$sheet->setCellValue('C1', 'Quantity');
		$sheet->setCellValue('D1', 'Price');
		$sheet->setCellValue('E1', 'Subtotal');

		$sn=2;
		foreach ($productlist as $prod) {
			//echo $prod->product_name;
			$sheet->setCellValue('A'.$sn,$prod->product_id);
			$sheet->setCellValue('B'.$sn,$prod->product_name);
			$sheet->setCellValue('C'.$sn,$prod->product_quantity);
			$sheet->setCellValue('D'.$sn,$prod->product_price);
			$sheet->setCellValue('E'.$sn,'=C'.$sn.'*D'.$sn);
			$sn++;
		}
		//TOTAL
		$sheet->setCellValue('D8','Total');
		$sheet->setCellValue('E8','=SUM(E2:E'.($sn-1).')');

		$writer = new Xlsx($spreadsheet);
		$writer->save("php://output");
	}

	function kmeans_preprocessing($all_stock_boundary, $data){

		// fungsi preprocessing kmeans pada model started_model
        $data['hasil'] = $this->started_model->hitung_kmeans($all_stock_boundary, $data);

		return $data;
    }

	function kmeans_cluster(){
		$k = 5;

		$data_cluster = $this->started_model->hitung_cluster($k);

		// echo json_encode($data_cluster); die();

		return $data_cluster;
	}
}
