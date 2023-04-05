<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Started_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function insert_batch($data){
		$this->db->insert_batch('product',$data);
		// "insert into product product_code = $text , product_name = $text"
		if($this->db->affected_rows()>0)
		{
			return 1;
		}
		else{
			return 0;
		}
	}

	public function delete(){
		$this->db->from('product');
		$this->db->truncate();

		return 1;
	}

	public function insert($data){
		for ($i = 0; $i < count($data); $i++){
			$product_name = $data[$i]['product_name'];
			$quantity_in = $data[$i]['quantity_in'];
			$price = $data[$i]['price'];
			$quantity_out = $data[$i]['quantity_out'];

			$cek_data = $this->cek_data($data[$i]["product_name"]);
			if ($cek_data > 0){
				// do nothing
			}else{
				$this->db->query("insert into product (product_name, quantity_in, price, quantity_out) 
				values (
				'$product_name', $quantity_in, $price, $quantity_out)");
			}
		}

		return 1;
		
	}

	public function cek_data($code){
		$this->db->select('*');
		$this->db->from('product');
		$this->db->where('product_name', $code);
		$query=$this->db->get();

		if (count($query->result()) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	public function product_list(){
		$this->db->select('*');
		$this->db->from('product');
		$query=$this->db->get();
		return $query->result();
	}

	public function hitung_kmeans($all_stock_boundary, $data){
		// Mengambil data dari database

		// buat variabel untuk ekstrak product_code
		$param_prod_code = "";

		// fungsi untuk ekstrak product_code
		for($x = 0;$x < count($data); $x++){
			if($param_prod_code == ""){
				$param_prod_code = $data[$x]["product_code"];
			}else{
				$param_prod_code .= ", ";
				$param_prod_code .= $data[$x]["product_code"];
			}
		}

		// query ke db untuk product yg akan di preprocessing
		$get_data = $this->db->query("select a.*
		from product a where product_code in ($param_prod_code)")->result();

		// cek jika ada
		if(count($get_data) > 0){

		// masuk ke fungsi hitung_kmeans_2
		$proses = $this->hitung_kmeans_2($get_data, $all_stock_boundary);

		// jika data tidak ada
		}else{
			
			// variabel array untuk menampung data prodict_code
			$kode_arr = [];

			// fungsi untuk get product_code pada db
			for($x = 0;$x < count($data); $x++){
				$nama_arr = $data[$x]["product_name"];

				$get_datcode = $this->db->query("select product_code
				from product a where product_name = '$nama_arr'")->result_array()[0]["product_code"];

				// push data array pada variabel $kode_arr
				array_push($kode_arr, $get_datcode);
			}

			// fungsi untuk ekstrak product_code
			for($i = 0;$i < count($kode_arr); $i++){
				if($param_prod_code == ""){
					$param_prod_code = $kode_arr[$i];
				}else{
					$param_prod_code .= ", ";
					$param_prod_code .= $kode_arr[$i];
				}
			}

			// query ke db untuk product yg akan di preprocessing
			$get_data = $this->db->query("select a.*
			from product a where product_code in ($param_prod_code)")->result();

			// masuk ke fungsi hitung_kmeans_2
			$proses = $this->hitung_kmeans_2($get_data, $all_stock_boundary);

		}

		return $proses;
	}

	public function hitung_kmeans_2($get_data, $boundary){

		// penentuan variabel
		$data_hitung_kmeans = array();
		$i = 0;
		$a = 1;

		// penentuan plafond
		// $boundary = 10;

		// penentuan variabel
		$nilai_terkecil = [];
		$hasil_akhir = [];

		// lopping data dari db
		foreach($get_data as $key){

		$product = $key->product_code;

		// penentuan nilai random clustering kmeans
		$get_range_min = $this->get_range_min($boundary);
		$get_range_max = $this->get_range_max($boundary);

		$nm_stock = $key->quantity_in;

		// isi array product_code dan penentuan euclidian distance
		$data_hitung_kmeans['product_code'][$i] = $key->product_code;
		$data_hitung_kmeans['distance'][$i] = $this->euclide_distance($nm_stock, $get_range_min, $get_range_max);
		$distance = $this->euclide_distance($nm_stock, $get_range_min, $get_range_max);

		// penentuan clustering kmeans
		$qty = rand($get_range_min, $get_range_max);

		// insert pada db  dataset agar nanti bisa digunakan kembali
		$this->db->query("insert into hitung_temp (id_product, product, price_pcs, qty_recom, distance) 
		values (
				$key->product_code, '$key->product_name', $key->price, $qty, $distance)");

				// isi variabel array hasil_akhir dengan data2 setelah preprocessing kmeans
				$hasil_akhir[$i]['product_code'] = $data_hitung_kmeans['product_code'][$i];
				$hasil_akhir[$i]['product_name'] = $key->product_name;
				$hasil_akhir[$i]['distance'] = $data_hitung_kmeans['distance'][$i];
				$hasil_akhir[$i]['price_pcs'] = $key->price;
				$hasil_akhir[$i]['qty_recom'] = $qty;

				$i++;
		}
		
		// isi data variabel final dengan variabel hasil_akhir
		$final = $hasil_akhir;

		return $final;
	}

	public function hitung_cluster($k){

		// penentuan variabel array dan plafond
		$arr_data = [];
		$boundary = 10;

		// select data master product
		$get_data = $this->db->query("select a.*
		from product a")->result();

		// inisialisasi iterasi a
		$a = 0;
		// lopping data dari db
		foreach($get_data as $key){

			// penentuan nilai random clustering kmeans
			$get_range_min = $this->get_range_min($boundary);
			$get_range_max = $this->get_range_max($boundary);

			// get data dari db dan dimasukkan kedalam variabel
			$product_code = $key->product_code;
			$product_name = $key->product_name;
			$qty = $key->quantity_out;

			// looping u/ penentuan nilai k
			for($i = 0;$i < $k; $i++){

				// get euclidian distance
				$euclidian_distance = $this->euclide_distance($qty, $get_range_min, $get_range_max);
				$k = rand(1, 5);

			}

			// get k dari eclidian distance terkecil
			$ed = $euclidian_distance;
			$k_value = $k;

			// simpan data dalam array 2 dimensi
			$arr_data[$a]["product_code"] = $product_code;
			$arr_data[$a]["product_name"] = $product_name;
			$arr_data[$a]["euclidian_distance"] = $ed;
			$arr_data[$a]["k"] = $k_value;

			$a++;

		}

		// kembalikan hasil
		return $arr_data;
		
	}

	public function euclide_distance($nm_stock, $get_range_min, $get_range_max){

		$hasil = sqrt( pow( ($nm_stock - $get_range_min), 2 ) + pow( ($nm_stock - $get_range_max), 2 ) + pow( ($nm_stock - ($get_range_min - $get_range_max)), 2 ));

		return $hasil;
			
	}

	public function getDataUpload()
	{
		$get_data = $this->db->query("select a.*
		from product a")->result_array();

		return $get_data;
	}

	public function get_range_min($batas){

		if($batas >= 0 && $batas <= 10){
				$min_value = 1;
		}else if($batas >= 11 && $batas <= 20){
				$min_value = 2;
		}else if($batas >= 21 && $batas <= 30){
				$min_value = 3;
		}else if($batas >= 31 && $batas <= 40){
				$min_value = 4;
		}else if($batas >= 41 && $batas <= 50){
				$min_value = 5;
		}else if($batas >= 51 && $batas <= 60){
				$min_value = 6;
		}else if($batas >= 61 && $batas <= 70){
				$min_value = 7;
		}else if($batas >= 71 && $batas <= 80){
				$min_value = 8;
		}else if($batas >= 81 && $batas <= 90){
				$min_value = 9;
		}else if($batas >= 91 && $batas <= 100){
				$min_value = 10;
		}else{
				$min_value = 25;
		} 

		return $min_value;

	}

	public function get_range_max($batas){

		if($batas >= 0 && $batas <= 10){
				$max_value = 10;
		}else if($batas >= 11 && $batas <= 20){
				$max_value = 9;
		}else if($batas >= 21 && $batas <= 30){
				$max_value = 23;
		}else if($batas >= 31 && $batas <= 40){
				$max_value = 26;
		}else if($batas >= 41 && $batas <= 50){
				$max_value = 29;
		}else if($batas >= 51 && $batas <= 60){
				$max_value = 32;
		}else if($batas >= 61 && $batas <= 70){
				$max_value = 35;
		}else if($batas >= 71 && $batas <= 80){
				$max_value = 38;
		}else if($batas >= 81 && $batas <= 90){
				$max_value = 41;
		}else if($batas >= 91 && $batas <= 100){
				$max_value = 44;
		}else{
				$max_value = 30;
		} 

		return $max_value;
			
	}
	
}