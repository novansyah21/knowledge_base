<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$data['page_title'] = "Home Page";
		$this->load->view('template/header', $data); // Header File
		$this->load->view('main/content_landing'); // Main File
		$this->load->view('template/footer'); // Footer File
	}
}
