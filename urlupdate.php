<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class urlupdate extends CI_Controller{
	
	function __construct(){
		date_default_timezone_set('Asia/Manila');
		parent::__construct();
		$this->load->helper('download');
		$this->load->library('file_processor');
	}
	
	function proj_mtr_version(){echo 'v2016-07-18_mtr1.0';}
	
	function proj_inv_version(){
		echo 'v2016-07-18_inv1.0';
	}
	
	function proj_pyr_version(){
		echo 'v2016-07-18_pyr1.0';
	}
	
	function proj_mtr_dl(){
		$data_path_mtr = '/path/to/c/application';
		$mtr_file = 'update_mtr.tar';
		$this->file_processor->downloads($data_path_mtr,$mtr_file);
		echo $data_path_mtr.'/'.$mtr_file;
	}
	
	function proj_inv_dl(){
		$data_path_inv = '/path/to/c/application';
		$inv_file = 'update_inv.tar';
		$this->file_processor->downloads($data_path_inv,$inv_file);
	}
	
	function proj_pyr_dl(){
		$data_path_pyr = '/path/to/c/application';
		$pyr_file = 'update_pyr.tar';
		$this->file_processor->downloads($data_path_pyr,$pyr_file);
	}
	
	function proj_dlt_dl(){
		$data_path_dlt = '/path/to/c/application';
		$dlt_file = '20160414_update.tar';
		$this->file_processor->downloads($data_path_dlt,$dlt_file);
	}
	
	function proj_gpio_dl(){
		$data_path_gpio = '/path/to/c/application';
		$gpio_file = '20160414_update.tar';
		$this->file_processor->downloads($data_path_gpio,$gpio_file);
	}
}
?>