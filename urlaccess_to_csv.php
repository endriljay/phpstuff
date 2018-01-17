<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class urlaccess_to_csv extends CI_Controller{
	
	function __construct(){
		date_default_timezone_set('Asia/Manila');
		parent::__construct();
		$this->load->helper('download');
		$this->load->library('file_processor');
	}
	

	function proj_mdp($meter_id='', $datetime='', $power='', $energy='', $voltAB='', $voltBC='', $voltAC='', $voltNA='', $voltNB='', $voltNC='', $currA='', $currB='', $currC='', $freq='', $pf=''){
		$this->load->helper('file');
		$this->load->helper('date');
		
		//Column Names
		$col_n1 = 'METER_ID';
		$col_n2 = 'DATETIME_MISC_DATA';
		$col_n3 = 'SYS_AC_POWER';
		$col_n4 = 'SYS_AC_ENERGY';
		$col_n5 = 'SYS_VOLTAGE_AB';
		$col_n6 = 'SYS_VOLTAGE_BC';
		$col_n7 = 'SYS_VOLTAGE_AC';
		$col_n8 = 'SYS_VOLTAGE_NA';
		$col_n9 = 'SYS_VOLTAGE_NB';
		$col_n10 = 'SYS_VOLTAGE_NC';
		$col_n11 = 'SYS_CURRENT_A';
		$col_n12 = 'SYS_CURRENT_B';
		$col_n13 = 'SYS_CURRENT_C';
		$col_n14 = 'FREQUENCY';
		$col_n15 = 'POWER_FACTOR';
		
		$column_names = $col_n1.','.$col_n2.','.$col_n3.','.$col_n4.','.$col_n5.','.$col_n6.','.$col_n7.','.$col_n8.','.$col_n9.','.$col_n10.','.$col_n11.','.$col_n12.','.$col_n13.','.$col_n14.','.$col_n15;
		//Get date today
		$dateformat = '%Y%m%d';
		$time=time();
		//Path to file
		$path='./proj_data/';
		//File name
		$file_name = mdate($dateformat,$time);
		$system_misc_file = $file_name.'_proj_mdp.csv';
		//Reset string variable
		$s = '';
		
		if(file_exists('./proj_data/'.$system_misc_file)){
			/* File exists */
			echo 'File already exists. only append data to last row.';
			$STRdata = $meter_id.','.$datetime.','.$power.','.$energy.','.$voltAB.','.$voltBC.','.$voltAC.','.$voltNA.','.$voltNB.','.$voltNC.','.$currA.','.$currB.','.$currC.','.$freq.','.$pf;
			write_file('./proj_data/'.$system_misc_file,"\n".$STRdata,'a+');
		}else{
			/* File does not exist */
			echo 'File does not exist. creating new file.';
			$STRcol_name = $column_names;
			$STRdata = $meter_id.','.$datetime.','.$power.','.$energy.','.$voltAB.','.$voltBC.','.$voltAC.','.$voltNA.','.$voltNB.','.$voltNC.','.$currA.','.$currB.','.$currC.','.$freq.','.$pf;
			write_file('./proj_data/'.$system_misc_file,$STRcol_name."\n".$STRdata);
		}
		echo '<br/>';
		$STRdata = $meter_id.','.$datetime.','.$power.','.$energy.','.$voltAB.','.$voltBC.','.$voltAC.','.$voltNA.','.$voltNB.','.$voltNC.','.$currA.','.$currB.','.$currC.','.$freq.','.$pf;
		$s = $STRdata;
		$data['datavalue'] = $s;
		$this->load->view('data_view',$data);
	}

	function proj_statb2p3($datetime_data='', $inv1_status='', $inv2_status='', $inv3_status='', $inv4_status='', $inv5_status='', $inv6_status='', $inv7_status='', $inv8_status='', $inv9_status='', $inv10_status='', $inv11_status='', $inv12_status='', $inv13_status='', $inv14_status='', $inv15_status='', $inv16_status=''){
		$this->load->helper('file');
		$this->load->helper('date');
		//Column Names
		$col_n1 = 'Datetime_Data';
		$col_n2 = 'Inv121_Status';
		$col_n3 = 'Inv122_Status';
		$col_n4 = 'Inv123_Status';
		$col_n5 = 'Inv124_Status';
		$col_n6 = 'Inv125_Status';
		$col_n7 = 'Inv126_Status';
		$col_n8 = 'Inv127_Status';
		$col_n9 = 'Inv128_Status';
		$col_n10 = 'Inv129_Status';
		$col_n11 = 'Inv130_Status';
		$col_n12 = 'Inv131_Status';
		$col_n13 = 'Inv132_Status';
		$col_n14 = 'Inv133_Status';
		$col_n15 = 'Inv134_Status';
		$col_n16 = 'Inv135_Status';
		$col_n17 = 'Inv136_Status';

		$column_names = $col_n1.','.$col_n2.','.$col_n3.','.$col_n4.','.$col_n5.','.$col_n6.','.$col_n7.','.$col_n8.','.$col_n9.','.$col_n10.','.$col_n11.','.$col_n12.','.$col_n13.','.$col_n14.','.$col_n15.','.$col_n16.','.$col_n17;
		//Get date today
		$dateformat = '%Y%m%d';
		$time = time();
		//Path to file
		$path_name = './proj_data/';
		//File name
		$file_name = mdate($dateformat,$time);
		$status_file = $file_name.'_proj_statb2p3.csv';
		//Reset string variable
		$s = '';
		if(file_exists('./proj_data/'.$status_file)){
			/* File exists */
			echo 'File already exists. only append data to last row.';
			$STRdata = $datetime_data.','.$inv1_status.','.$inv2_status.','.$inv3_status.','.$inv4_status.','.$inv5_status.','.$inv6_status.','.$inv7_status.','.$inv8_status.','.$inv9_status.','.$inv10_status.','.$inv11_status.','.$inv12_status.','.$inv13_status.','.$inv14_status.','.$inv15_status.','.$inv16_status;
			write_file('./proj_data/'.$status_file,"\n".$STRdata,'a+');
		}else{
			/* File does not exist */
			echo 'File does not exit. create new file.';
			$STRcol =$column_names;
			$STRdata = $datetime_data.','.$inv1_status.','.$inv2_status.','.$inv3_status.','.$inv4_status.','.$inv5_status.','.$inv6_status.','.$inv7_status.','.$inv8_status.','.$inv9_status.','.$inv10_status.','.$inv11_status.','.$inv12_status.','.$inv13_status.','.$inv14_status.','.$inv15_status.','.$inv16_status;
			write_file('./proj_data/'.$status_file,$STRcol."\n".$STRdata);
		}
		echo '<br/>';
		$STRdata = $datetime_data.','.$inv1_status.','.$inv2_status.','.$inv3_status.','.$inv4_status.','.$inv5_status.','.$inv6_status.','.$inv7_status.','.$inv8_status.','.$inv9_status.','.$inv10_status.','.$inv11_status.','.$inv12_status.','.$inv13_status.','.$inv14_status.','.$inv15_status.','.$inv16_status;
		$s = $STRdata;
		$data['datavalue'] = $s;
		$this->load->view('data_view', $data);
	}
	
	function proj_errb2p1($inv_id='', $datetime_data='', $error_code1='', $error_code2='', $error_code3='', $error_code4=''){
		$this->load->helper('file');
		$this->load->helper('date');
		
		//Column Names
		$col_n1 = 'Inverter_ID';
		$col_n2 = 'Datetime_Data';
		$col_n3 = 'Error_Code1';
		$col_n4 = 'Error_Code2';
		$col_n5 = 'Error_Code3';
		$col_n6 = 'Error_Code4';
		$column_names = $col_n1.','.$col_n2.','.$col_n3.','.$col_n4.','.$col_n5.','.$col_n6;
		
		//Get date today
		$dateformat = '%Y%m%d';
		$time = time();
		
		//Path to file
		$path_name='./proj_data/';
		//File name
		$file_name = mdate($dateformat,$time);
		$error_file = $file_name.'_proj_errb2p1.csv';
		//Reset string variable
		$s = '';
		if(file_exists('./proj_data/'.$error_file)){
			/* File exists */
			echo 'File already exists. only append data to last row.';
			$STRdata = $inv_id.','.$datetime_data.','.$error_code1.','.$error_code2.','.$error_code3.','.$error_code4;
			write_file('./proj_data/'.$error_file,"\n".$STRdata,'a+');
		}else{
			/* File does not exist */
			echo 'File does not exist. creating new file.';
			$STRcol = $column_names;
			$STRdata = $inv_id.','.$datetime_data.','.$error_code1.','.$error_code2.','.$error_code3.','.$error_code4;
			write_file('./proj_data/'.$error_file,$STRcol."\n".$STRdata);
		}
		echo '<br/>';
		$STRdata = $inv_id.','.$datetime_data.','.$error_code1.','.$error_code2.','.$error_code3.','.$error_code4;
		$s = $STRdata;
		$data['datavalue'] = $s;
		$this->load->view('data_view', $data);
	}

	function proj_invb1p3($inverter_id='',$datetime_data='',$acPower='',$acEnergy='',$acIa='',$acIb='',$acIc='',$acItot='',$acVAB='',$acVBC='',$acVAC='',$acVAN='',$acVBN='',$acVCN='',$acFreq='',$dcCurrent='',$dcVoltage='',$dcPower=''){
		$this->load->helper('file');
		$this->load->helper('date');
		//Column Names
		$col_n1='Inverter_ID';
		$col_n2='Datetime_Data';
		$col_n3='Inverter_acPower';
		$col_n4='Inverter_acEnergy';
		$col_n5='Inverter_acIa';
		$col_n6='Inverter_acIb';
		$col_n7='Inverter_acIc';
		$col_n8='Inverter_acItot';
		$col_n9='Inverter_acVoltAB';
		$col_n10='Inverter_acVoltBC';
		$col_n11='Inverter_acVoltAC';
		$col_n12='Inverter_acVoltAN';
		$col_n13='Inverter_acVoltBN';
		$col_n14='Inverter_acVoltCN';
		$col_n15='Inverter_acFreq';
		$col_n16='Inverter_dcCurrent';
		$col_n17='Inverter_dcVoltage';
		$col_n18='Inverter_dcPower';
		$column_names=$col_n1.','.$col_n2.','.$col_n3.','.$col_n4.','.$col_n5.','.$col_n6.','.$col_n7.','.$col_n8.','.$col_n9.','.$col_n10.','.$col_n11.','.$col_n12.','.$col_n13.','.$col_n14.','.$col_n15.','.$col_n16.','.$col_n17.','.$col_n18;
		//Get date today
		$dateformat='%Y%m%d';
		$time=time();
		//Path to file
		$path_name = './proj_data/';
		//File name
		$file_name = mdate($dateformat, $time);
		$inverter_file = $file_name.'_proj_invb1p3.csv';
		//Reset string variable
		$s = '';
		if(file_exists('./proj_data/'.$inverter_file)){
			/* File exists */
			echo 'File already exists. only append data to last row.';
			$STRdata=$inverter_id.','.$datetime_data.','.$acPower.','.$acEnergy.','.$acIa.','.$acIb.','.$acIc.','.$acItot.','.$acVAB.','.$acVBC.','.$acVAC.','.$acVAN.','.$acVBN.','.$acVCN.','.$acFreq.','.$dcCurrent.','.	$dcVoltage.','.	$dcPower;
			write_file('./proj_data/'.$inverter_file,"\n".$STRdata,'a+');		
		}else{
			/* File does not exist */
			echo 'File does not exist. creating new file.';
			$STRcol_name=$column_names;
			$STRdata=$inverter_id.','.$datetime_data.','.$acPower.','.$acEnergy.','.$acIa.','.$acIb.','.$acIc.','.$acItot.','.$acVAB.','.$acVBC.','.$acVAC.','.$acVAN.','.$acVBN.','.$acVCN.','.$acFreq.','.$dcCurrent.','.	$dcVoltage.','.	$dcPower;
			write_file('./proj_data/'.$inverter_file,$STRcol_name."\n".$STRdata);	
		}
		echo '<br/>';
		$STRdata=$inverter_id.','.$datetime_data.','.$acPower.','.$acEnergy.','.$acIa.','.$acIb.','.$acIc.','.$acItot.','.$acVAB.','.$acVBC.','.$acVAC.','.$acVAN.','.$acVBN.','.$acVCN.','.$acFreq.','.$dcCurrent.','.	$dcVoltage.','.	$dcPower;
		$s=$STRdata;
		$data['datavalue']=$s;
		$this->load->view('data_view',$data);
	}
}
?>