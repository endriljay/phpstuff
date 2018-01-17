<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class dashboard_controller extends CI_Controller{
	//System
	public $systemID, $sysName, $sysSize, $sysCommissioned, $sysWeather, $inverter_id, $inverter_num, $number_of_inverters;
	//Daily
	public $all_daily_data, $dailyMorning_bound, $dailyEvening_bound, $daily_eac_today, $daily_eac_yesterday;
	public $daily_client, $daily_client_total, $daily_client_latest, $daily_client_sum;
	public $misc_data, $misc_each_data, $daily_irradiance_data, $irri_total_daily;
	public $lifetime_total, $lifetime_total_sum, $sum_eac_today, $sum_eac_yesterday, $eac_diff;
	public $DateParam_Curr;
	//Weekly
	public $weekly_sun_date, $weekly_mon_date, $weekly_tue_date, $weekly_wed_date, $weekly_ths_date, $weekly_fri_date, $weekly_sat_date;
	public $weekly_sun, $weekly_mon, $weekly_tue, $weekly_wed, $weekly_ths, $weekly_fri, $weekly_sat;
	public $weekly_sun_total, $weekly_mon_total, $weekly_tue_total, $weekly_wed_total, $weekly_ths_total, $weekly_fri_total, $weekly_sat_total;
	public $all_weekly_data, $weekly_client_total;
	public $irr_weekly_array;
	//Monthly
	public $monthly_jan_data, $monthly_feb_data, $monthly_mar_data, $monthly_apr_data, $monthly_may_data, $monthly_jun_data, $monthly_jul_data, $monthly_aug_data, $monthly_sep_data, $monthly_oct_data, $monthly_nov_data, $monthly_dec_data;
	public $monthly_jan, $monthly_feb, $monthly_mar, $monthly_apr, $monthly_may, $monthly_jun, $monthly_jul, $monthly_aug, $monthly_sep, $monthly_oct, $monthly_nov, $monthly_dec;
	public $month_jan_total, $month_feb_total, $month_mar_total, $month_apr_total, $month_may_total, $month_jun_total, $month_jul_total, $month_aug_total, $month_sep_total, $month_oct_total, $month_nov_total, $month_dec_total;
	public $all_monthly_data, $monthly_client_total;
	//Yearly
	public $all_yearly_date, $all_yearly_data, $yearly_client, $yearly_client_total;
	public $string_time, $DateToday, $temp_DateToday, $DateYear;
	public $dtimeMin_data, $dtimeMax_data, $daily_bounds;
	public $number_of_data;
	public $todayspac_data;
	public $column_data_week, $column_data_month;
	public $Sun_date, $Mon_date, $Tue_date, $Wed_date, $Ths_date, $Fri_date, $Sat_date;
	public $SunEac_val, $MonEac_val, $TueEac_val, $WedEac_val, $ThsEac_val, $FriEac_val, $SatEac_val;
	public $Lastyear_data, $January_data, $February_data, $March_data, $April_data, $May_data, $June_data, $July_data, $August_data, $September_data, $October_data, $November_data, $December_data;
	public $YearData, $YearDateName;
	function __construct(){
		date_default_timezone_set('Asia/Manila');
		parent::__construct();
		$this->load->library('system_info');
		$this->load->library('daily_data');
		$this->load->library('weekly_data');
		$this->load->library('monthly_data');
		$this->load->library('yearly_data');
	}

	function index(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		if($this->session->userdata('logged_in')&&$class_name==$company_name){
			$class_name=$this->uri->rsegment(1);
			$company_name=$session_data['company'];
			$data['username']=$session_data['username'];
			$data['company']=$session_data['company'];
			$data['comp_id']=$session_data['comp_id'];
			//Load necessary functions/helpers
			$this->load->helper('date');
			$this->load->model('solenergy_db');
			//System Information
			$this->SysInfo();
			$data['sysName']=json_encode($this->sysName);
			$data['sysSize']=$this->sysSize;
			$data['sysCommissioned']=json_encode($this->sysCommissioned);
			$data['sysWeather']=json_encode($this->sysWeather);
			//Date and Time functions
			$this->timeFunctions();
			$dtoken = $this->daily_data->date_token;
			$data['DateToday']=$this->DateParam_Today;
			$data['DateParam_Curr']=$this->DateParam_Curr;
			//Inverter Assigned
			$this->InvAssigned();
			$data['number_of_inverters']=$this->inverter_num;
			$invparam_min=$this->inverter_id[0];
			$invparam_max=$this->inverter_id[count($this->inverter_id)-1];
			//Data Count
			$this->daily_data->getNumberOfData($this->DateParam_Today,$this->systemID);
			$data['daily_data_count']=$this->daily_data->daily_count;
			//Morning and Evening Bounds
			$this->getDailyBounds($this->DateParam_Today,$this->systemID);
			$data['dailyMorning_bound']=$this->dailyMorning_bound;
			$data['dailyEvening_bound']=$this->dailyEvening_bound;
			//Graph Data
			$this->get_dData();
			if($dtoken>0){$data['ddatactr']=$this->ddatactr;}
			else{$data['ddatactr']=0;}
			$sysEnergyColumn = $this->SystemData_Energy($this->DateParam_Today,$this->systemID);
			$data['ddatasysencol']=json_encode($sysEnergyColumn);
			$data['ddataall']=json_encode($this->ddataall);
			$data['ddatairr']=json_encode($this->ddatairr);
			//Latest Data
			$this->get_dL();
			$data['ddatairrL']=floatval($this->dataL['latest_irr']);
			$data['ddataACL']=floatval($this->dataL['latest_ac']);
			$data['ddataYiL']=floatval($this->dataL['latest_yi']);
			$data['ddataTemp']=floatval($this->dataL['latest_temp']);
			$data['ddataAVGpow']=floatval($this->dataL['latest_avgpow']);
			$data['lTime']=$this->dataL['latest_time'];
			$data['lData']=$this->dataL['latest_ac'];
				//AVG
			$this->get_dAVG($this->DateParam_Today,$invparam_min,$invparam_max);
			$data['voltA']=floatval($this->dataAVG['voltaAVG']);$data['currA']=floatval($this->dataAVG['curraAVG']);
			$data['voltB']=floatval($this->dataAVG['voltbAVG']);$data['currB']=floatval($this->dataAVG['currbAVG']);
			$data['voltC']=floatval($this->dataAVG['voltcAVG']);$data['currC']=floatval($this->dataAVG['currcAVG']);
			$data['freq']=floatval($this->dataAVG['freqAVG']);
				//PR
			$this->get_compPR();
			$data['ddataprL']=floatval($this->prd);
			//Weekly
			$this->get_sysdWk();
			$data['sunday_date']=json_encode($this->weekly_sun_date);
			$data['monday_date']=json_encode($this->weekly_mon_date);
			$data['tuesday_date']=json_encode($this->weekly_tue_date);
			$data['wednesday_date']=json_encode($this->weekly_wed_date);
			$data['thursday_date']=json_encode($this->weekly_ths_date);
			$data['friday_date']=json_encode($this->weekly_fri_date);
			$data['saturday_date']=json_encode($this->weekly_sat_date);
			$data['weekly_client_total']=json_encode($this->all_weekly_data);
			$data['weekly_power_total']=json_encode($this->weekly_power_total);
			$data['weekly_client_pr']=json_encode($this->all_weekly_pr);
			//Monthly
			$this->get_sysdMt();
			$data['monthly_client_total']=json_encode($this->all_monthly_data);
			$data['monthly_power_total']=json_encode($this->monthly_power_total);
			$data['monthly_client_pr']=json_encode($this->all_monthly_pr);
			//Yearly
			$this->get_sysdYr();
			$this->get_sysdYrPr();
			$data['all_yearly_date']=json_encode($this->all_yearly_date);
			$data['all_yearly_data']=json_encode($this->all_yearly_data);
			$data['yearly_client_pr']=json_encode($this->all_yearly_pr);
			//Load all to view
			$this->load->view('project_view', $data);
		}else{
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	function timeFunctions(){
		//call library function
		$this->daily_data->getDate($this->systemID);
		//assign variables	
		$this->DateParam_Today=$this->daily_data->DateParam_Today;
		$this->DateParam_Curr=$this->daily_data->DateParam_Today;
		$this->DateParam_Yesterday=$this->daily_data->DateParam_Yesterday;
		$this->DateParam_Year=$this->daily_data->DateParam_Year;
		$this->time_string=$this->daily_data->time_string;
	}
	
	function SysInfo(){
		//call function from library
		$this->system_info->get_system_info();
		//assign variables
		$this->systemID=$this->system_info->systemID;
		$this->sysName=$this->system_info->sysName;
		$this->sysSize=$this->system_info->sysSize;
		$this->sysCommissioned=$this->system_info->sysCommissioned;
		$this->sysWeather=$this->system_info->sysWeather;
	}
	function InvAssigned(){
		$this->inverter_num=0;
		//call function from library
		$this->system_info->get_assigned_inv();
		//assign variables - get inverter ID's
		foreach($this->system_info->invID as $var){$this->inverter_id[]=$var;}
		$this->inverter_num=$this->system_info->num_inv;
	}
	function getDailyBounds($dateparam,$systemparam){
		$this->daily_data->getNumberOfData($dateparam,$systemparam);
		//daily morning/evening bounds
		$this->dailyMorning_bound=$this->daily_data->dailyMorning_bound;
		$this->dailyEvening_bound=$this->daily_data->dailyEvening_bound;
	}
	function refreshDailyBounds(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->InvAssigned();
		$this->getDailyData();
		$this->getDailyBounds($this->DateParam_Today,$this->systemID);
		$this->daily_bounds=array("min_bound"=>$this->dailyMorning_bound,"max_bound"=>$this->dailyEvening_bound,);
		$data['datavalue']=json_encode($this->daily_bounds);
		$this->load->view('data_view', $data);
	}
	function getDailyData(){
		//Daily - Get data for the day
		$this->daily_client_total=array();
		$this->daily_data->getNumberOfData($this->DateParam_Today,$this->systemID);
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){$this->all_daily_data[$ctr]=$this->daily_data->get_daily_all($this->DateParam_Today,$this->inverter_id[$ctr]);}
		$this->daily_client_total=array();
		for($ctr2=0;$ctr2<($this->daily_data->daily_count)/$this->inverter_num;$ctr2++){
			$this->daily_client=0;
			for($ctr1=0;$ctr1<$this->inverter_num;$ctr1++){$this->daily_client+=$this->all_daily_data[$ctr1][$ctr2][1];}
			array_push($this->daily_client_total,array($this->all_daily_data[0][$ctr2][0],$this->daily_client));
		}
	}
	function pre_get_TotalEacDaily(){
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){$this->sum_eac_today+=$this->daily_data->get_daily_eac($this->DateParam_Today,$this->inverter_id[$ctr]);}
		$this->eac_diff=number_format($this->sum_eac_today,2,'.','');
	}
	function refresh_TotalEacDaily(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->pre_get_TotalEacDaily();
		$data['datavalue']=json_encode($this->eac_diff);
		$this->load->view('data_view', $data);
	}
	function pre_getDailyLatest(){
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){$this->latest_daily_data[$ctr]=$this->daily_data->get_daily_latest($this->DateParam_Today,$this->inverter_id[$ctr]);}
		$this->daily_client_latest=array();
		$this->daily_client_sum=0;
		$this->lifetime_total=0;
		for($ctr2=0;$ctr2<$this->inverter_num;$ctr2++){$this->daily_client_sum+=$this->latest_daily_data[$ctr2]['latest_data'];$this->lifetime_total+=$this->latest_daily_data[$ctr2]['lifetime_total'];}
		$this->daily_client_latest=array(
			"latest_time"=>$this->latest_daily_data[0]['latest_time'],
			"latest_data"=>floatval($this->daily_client_sum),
			"lifetime_total"=>floatval($this->lifetime_total)
		);
		$this->lifetime_total_sum=floatval($this->lifetime_total);
	}
	function getDailyLatest(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->pre_getDailyLatest();
		$data['datavalue']=json_encode($this->daily_client_latest);
		$this->load->view('data_view',$data);
	}
	function refreshDailyData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->InvAssigned();
		$this->getDailyData();
		$data['datavalue']=json_encode($this->daily_client_total);
		$this->load->view('data_view',$data);
	}
	function pre_Irr_SumDaily(){
		$this->irri_total_daily=$this->daily_data->get_miscdata_total($this->systemID,$this->DateParam_Today);
	}
	function Irr_SumDaily(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->InvAssigned();
		$this->pre_Irr_SumDaily();
		$data['datavalue']=json_encode($this->irri_total_daily['irradiance_total']);
		$this->load->view('data_view',$data);
	}
	function pre_Power_SumDaily(){
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){$this->ac_power_total[$ctr]=$this->daily_data->get_power_sum($this->DateParam_Today,$this->inverter_id[$ctr]);}
		$this->power_total=0;
		for($ctr2=0;$ctr2<$this->inverter_num;$ctr2++){$this->power_total+=$this->ac_power_total[$ctr2]['ac_power_sum'];}
	}
	function Power_SumDaily(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->InvAssigned();
		$this->pre_Power_SumDaily();
		$data['datavalue']=json_encode($this->power_total);
		$this->load->view('data_view',$data);
	}
	function refreshDailyCount(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->InvAssigned();
		$this->daily_data->getNumberOfData($this->DateParam_Today,$this->systemID);
		$data['datavalue']=$this->daily_data->daily_count;
		$this->load->view('data_view', $data);
	}
	function getSystemData(){
		$this->daily_irradiance_data=array();
		$this->misc_data=$this->daily_data->get_miscdata_all($this->systemID,$this->DateParam_Today);
		for($ctr=0;$ctr<count($this->misc_data);$ctr++){array_push($this->daily_irradiance_data,array($this->misc_data[$ctr]['latest_time'],$this->misc_data[$ctr]['irradiance']));}
	}
	function pre_getSystemData_Latest(){
		$this->misc_each_data=$this->daily_data->get_miscdata_latest($this->systemID,$this->DateParam_Today);
	}
	function refreshSystemData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->InvAssigned();
		$this->getSystemData();
		$data['datavalue']=json_encode($this->daily_irradiance_data);
		$this->load->view('data_view',$data);
	}
	function getSystemData_Latest(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->SysInfo();
		$this->pre_getSystemData_Latest();
		$data['datavalue']=json_encode($this->misc_each_data);
		$this->load->view('data_view',$data);
	}
	function getWeeklyData(){
		//assign default values
		$this->weekly_sun_date='';$this->weekly_mon_date='';$this->weekly_tue_date='';$this->weekly_wed_date='';$this->weekly_ths_date='';$this->weekly_fri_date='';$this->weekly_sat_date='';
		$this->weekly_sun='';$this->weekly_mon='';$this->weekly_tue='';$this->weekly_wed='';$this->weekly_ths='';$this->weekly_fri='';$this->weekly_sat='';
		$this->weekly_sun_total=0;$this->weekly_mon_total=0;$this->weekly_tue_total=0;$this->weekly_wed_total=0;$this->weekly_ths_total=0;$this->weekly_fri_total=0;$this->weekly_sat_total=0;
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){
			//Sunday
			$this->weekly_sun[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],1,2),2,'.','');
			$this->sun_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_sun_date[0]='';
			}else{
				$this->weekly_sun_date[0]=$this->weekly_data->date_of_week;
			}
			//Monday
			$this->weekly_mon[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],2,3),2,'.','');
			$this->mon_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_mon_date[0]='';
			}else{
				$this->weekly_mon_date[0]=$this->weekly_data->date_of_week;
			}
			//Tuesday
			$this->weekly_tue[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],3,4),2,'.','');
			$this->tue_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_tue_date[0]='';
			}else{
				$this->weekly_tue_date[0]=$this->weekly_data->date_of_week;
			}
			//Wednesday
			$this->weekly_wed[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],4,5),2,'.','');
			$this->wed_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_wed_date[0]='';
			}else{
				$this->weekly_wed_date[0]=$this->weekly_data->date_of_week;
			}
			//Thursday
			$this->weekly_ths[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],5,6),2,'.','');
			$this->ths_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_ths_date[0]='';
			}else{
				$this->weekly_ths_date[0]=$this->weekly_data->date_of_week;
			}
			//Friday
			$this->weekly_fri[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],6,7),2,'.','');
			$this->fri_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_fri_date[0]='';
			}else{
				$this->weekly_fri_date[0]=$this->weekly_data->date_of_week;
			}
			//Saturday
			$this->weekly_sat[$ctr]=number_format($this->weekly_data->get_weekly_all($this->DateParam_Today,$this->inverter_id[$ctr],7,8),2,'.','');
			$this->sat_power_total[$ctr]=$this->weekly_data->daily_power_total;
			if(is_null($this->weekly_data->date_of_week)){
				$this->weekly_sat_date[0]='';
			}else{
				$this->weekly_sat_date[0]=$this->weekly_data->date_of_week;
			}
			//Group into array
			$this->all_weekly_data[$ctr]=array(floatval($this->weekly_sun[$ctr]),floatval($this->weekly_mon[$ctr]),floatval($this->weekly_tue[$ctr]),floatval($this->weekly_wed[$ctr]),floatval($this->weekly_ths[$ctr]),floatval($this->weekly_fri[$ctr]),floatval($this->weekly_sat[$ctr]));
		}
		$this->week_sun_total=0;$this->mon_total=0;$this->tue_total=0;$this->wed_total=0;$this->ths_total=0;$this->fri_total=0;$this->sat_total=0;
		for($i=0;$i<$this->inverter_num;$i++){
			$this->week_sun_total+=$this->weekly_sun[$i];$this->week_mon_total+=$this->weekly_mon[$i];$this->week_tue_total+=$this->weekly_tue[$i];$this->week_wed_total+=$this->weekly_wed[$i];$this->week_ths_total+=$this->weekly_ths[$i];$this->week_fri_total+=$this->weekly_fri[$i];$this->week_sat_total+=$this->weekly_sat[$i];
			$this->sun_power_sum+=$this->sun_power_total[$i];$this->mon_power_sum += $this->mon_power_total[$i];$this->tue_power_sum+=$this->tue_power_total[$i];$this->wed_power_sum += $this->wed_power_total[$i];$this->ths_power_sum+=$this->ths_power_total[$i];$this->fri_power_sum += $this->fri_power_total[$i];$this->sat_power_sum+=$this->sat_power_total[$i];
		}
		$this->weekly_client_total=array($this->week_sun_total,$this->week_mon_total,$this->week_tue_total,$this->week_wed_total,$this->week_ths_total,$this->week_fri_total,$this->week_sat_total);
		$this->weekly_power_total=array($this->sun_power_sum,$this->mon_power_sum,$this->tue_power_sum,$this->wed_power_sum,$this->ths_power_sum,$this->fri_power_sum,$this->sat_power_sum);
	}
	function refreshWeeklyData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->getWeeklyData();
		$weekly_data=array(
			"weeks_data"=>$this->weekly_client_total,
			"weeks_date"=>array($this->weekly_sun_date[0],$this->weekly_mon_date[0],$this->weekly_tue_date[0],$this->weekly_wed_date[0],$this->weekly_ths_date[0],$this->weekly_fri_date[0],$this->weekly_sat_date[0]),
			"weeks_power"=>$this->weekly_power_total
		);
		$data['datavalue']=json_encode($weekly_data);
		$this->load->view('data_view', $data);
	}
	function Irr_WeeklyData(){
		//SUMMATION OF IRRADIANCE
		$this->irr_weekly_array=array();
		for($ctr=1;$ctr<8;$ctr++){
			array_push($this->irr_weekly_array,$this->weekly_data->get_weekly_misc($this->DateParam_Today,3,$ctr,$ctr+1));
		}
	}
	function refreshIrr_WeeklyData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->Irr_WeeklyData();
		$data['datavalue']=json_encode($this->irr_weekly_array);
		$this->load->view('data_view',$data);
	}
	function getMonthlyData(){
		$this->monthly_jan='';$this->monthly_feb='';$this->monthly_mar='';$this->monthly_apr='';$this->monthly_may='';$this->monthly_jun='';$this->monthly_jul='';$this->monthly_aug='';$this->monthly_sep='';$this->monthly_oct='';$this->monthly_nov='';$this->monthly_dec='';
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){
			//January
			$this->monthly_jan[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'01',$this->inverter_id[$ctr]),2,'.','');
			$this->jan_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//February
			$this->monthly_feb[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'02',$this->inverter_id[$ctr]),2,'.','');
			$this->feb_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//March
			$this->monthly_mar[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'03',$this->inverter_id[$ctr]),2,'.','');
			$this->mar_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//April
			$this->monthly_apr[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'04',$this->inverter_id[$ctr]),2,'.','');
			$this->apr_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//May
			$this->monthly_may[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'05',$this->inverter_id[$ctr]),2,'.','');
			$this->may_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//June
			$this->monthly_jun[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'06',$this->inverter_id[$ctr]),2,'.','');
			$this->jun_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//July
			$this->monthly_jul[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'07',$this->inverter_id[$ctr]),2,'.','');
			$this->jul_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//August
			$this->monthly_aug[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'08',$this->inverter_id[$ctr]),2,'.','');
			$this->aug_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//September
			$this->monthly_sep[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'09',$this->inverter_id[$ctr]),2,'.','');
			$this->sep_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//October
			$this->monthly_oct[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'10',$this->inverter_id[$ctr]),2,'.','');
			$this->oct_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//November
			$this->monthly_nov[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'11',$this->inverter_id[$ctr]),2,'.','');
			$this->nov_power_total[$ctr]=$this->monthly_data->ac_power_total;
			//December
			$this->monthly_dec[$ctr]=number_format($this->monthly_data->get_monthly_all($this->DateParam_Year,'12',$this->inverter_id[$ctr]),2,'.','');
			$this->dec_power_total[$ctr]=$this->monthly_data->ac_power_total;
			$this->all_monthly_data[$ctr] = array(floatval($this->monthly_jan[$ctr]),floatval($this->monthly_feb[$ctr]),floatval($this->monthly_mar[$ctr]),floatval($this->monthly_apr[$ctr]),floatval($this->monthly_may[$ctr]),floatval($this->monthly_jun[$ctr]),floatval($this->monthly_jul[$ctr]),floatval($this->monthly_aug[$ctr]),floatval($this->monthly_sep[$ctr]),floatval($this->monthly_oct[$ctr]),floatval($this->monthly_nov[$ctr]),floatval($this->monthly_dec[$ctr]));
		}
		$this->month_jan_total=0;$this->month_feb_total=0;$this->month_mar_total=0;$this->month_apr_total=0;$this->month_may_total= 0;$this->month_jun_total=0;$this->month_jul_total=0;$this->month_aug_total=0;$this->month_sep_total=0;$this->month_oct_total=0;$this->month_nov_total=0;$this->month_dec_total=0;
		$this->jan_power_sum=0;$this->feb_power_sum=0;$this->mar_power_sum=0;$this->apr_power_sum=0;$this->may_power_sum=0;$this->jun_power_sum=0;$this->jul_power_sum=0;$this->aug_power_sum=0;$this->sep_power_sum=0;$this->oct_power_sum=0;$this->nov_power_sum=0;$this->dec_power_sum=0;
		for($i=0;$i<$this->inverter_num;$i++){
			$this->month_jan_total+=$this->monthly_jan[$i];$this->month_feb_total+=$this->monthly_feb[$i];$this->month_mar_total+=$this->monthly_mar[$i];$this->month_apr_total+=$this->monthly_apr[$i];$this->month_may_total+=$this->monthly_may[$i];$this->month_jun_total+=$this->monthly_jun[$i];$this->month_jul_total+=$this->monthly_jul[$i];$this->month_aug_total+=$this->monthly_aug[$i];$this->month_sep_total+=$this->monthly_sep[$i];$this->month_oct_total+=$this->monthly_oct[$i];$this->month_nov_total+=$this->monthly_nov[$i];$this->month_dec_total+=$this->monthly_dec[$i];
			$this->jan_power_sum+=$this->jan_power_total[$i];$this->feb_power_sum+=$this->feb_power_total[$i];$this->mar_power_sum+=$this->mar_power_total[$i];$this->apr_power_sum+=$this->apr_power_total[$i];$this->may_power_sum+=$this->may_power_total[$i];$this->jun_power_sum+=$this->jun_power_total[$i];$this->jul_power_sum+=$this->jul_power_total[$i];$this->aug_power_sum+=$this->aug_power_total[$i];$this->sep_power_sum+=$this->sep_power_total[$i];$this->oct_power_sum+=$this->oct_power_total[$i];$this->nov_power_sum+=$this->nov_power_total[$i];$this->dec_power_sum+=$this->dec_power_total[$i];
		}
		$this->monthly_client_total=array($this->month_jan_total,$this->month_feb_total,$this->month_mar_total,$this->month_apr_total,$this->month_may_total,$this->month_jun_total,$this->month_jul_total,$this->month_aug_total,$this->month_sep_total,$this->month_oct_total,$this->month_nov_total,$this->month_dec_total);
		$this->monthly_power_total=array($this->jan_power_sum,$this->feb_power_sum,$this->mar_power_sum,$this->apr_power_sum,$this->may_power_sum,$this->jun_power_sum,$this->jul_power_sum,$this->aug_power_sum,$this->sep_power_sum,$this->oct_power_sum,$this->nov_power_sum,$this->dec_power_sum);
	}
	function refreshMonthlyData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->getMonthlyData();
		$monthly_data=array(
			"month_data"=>$this->monthly_client_total,
			"month_power"=>$this->monthly_power_total
		);
		$data['datavalue']=json_encode($monthly_data);
		$this->load->view('data_view',$data);
	}
	function Irr_MonthlyData(){
		//SUMMATION OF IRRADIANCE
		$this->irr_monthly_array=array();
		$month_array=array('01','02','03','04','05','06','07','08','09','10','11','12');
		for($ctr=0;$ctr<12;$ctr++){
			array_push($this->irr_monthly_array,$this->monthly_data->get_monthly_misc($this->DateParam_Year,$month_array[$ctr],3));
		}
	}
	function refreshIrr_MonthlyData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->Irr_MonthlyData();
		$data['datavalue']=json_encode($this->irr_monthly_array);
		$this->load->view('data_view',$data);
	}
	function getYearlyData(){
		$this->all_yearly_date=array();$this->all_yearly_data=array();$this->yearly_client_total=array();
		for($ctr=0;$ctr<$this->inverter_num;$ctr++){
			$this->all_yearly_date[$ctr]=$this->yearly_data->get_yearly_date($this->inverter_id[$ctr]);
			$this->all_yearly_data[$ctr]=$this->yearly_data->get_yearly_data($this->inverter_id[$ctr]);
		}
		for($col_number=0;$col_number<$this->yearly_data->year_count;$col_number++){
			$this->yearly_client=0;
			for($inv_number=0;$inv_number<$this->inverter_num;$inv_number++){
				$this->yearly_client+=$this->all_yearly_data[$inv_number][$col_number];
			}
			array_push($this->yearly_client_total,$this->yearly_client);
		}
	}
	function refreshYearlyDate(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');

		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->getYearlyData();
		$data['datavalue']=json_encode($this->all_yearly_date);
		$this->load->view('data_view', $data);
	}
	function refreshYearlyData(){
		$sess_array=array('username'=>'project','company'=>'project','comp_id'=>2);
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->getYearlyData();
		$data['datavalue']=json_encode($this->yearly_client_total);
		$this->load->view('data_view',$data);
	}
	function Irr_YearlyData(){
		for($ctr=0;$ctr<count($this->yearly_data->get_yearly_misc($this->systemID));$ctr++){
			$this->irr_yearly_array=$this->yearly_data->get_yearly_misc($this->systemID);
		}
	}
	function refreshIrr_YearlyData(){
		$sess_array=array('username'=>'username','company'=>'company name','comp_id'=>'company_id');
		$this->session->set_userdata('logged_in',$sess_array);
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->timeFunctions();
		$this->InvAssigned();
		$this->Irr_YearlyData();
		$data['datavalue']=json_encode($this->irr_yearly_array);
		$this->load->view('data_view',$data);
	}
	/*************
		project
	*************/
	function r_dDataCtr(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->sysInfo();		
		$this->timeFunctions();
		$this->get_dData();
		$data['datavalue']=json_encode($this->ddatactr);
		$this->load->view('data_view',$data);
	}
	function r_dDataAll(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->sysInfo();		
		$this->timeFunctions();
		$this->get_dData();
		$data['datavalue']=json_encode($this->ddataall);
		$this->load->view('data_view',$data);
	}
	function r_dDataIrr(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->sysInfo();		
		$this->timeFunctions();
		$this->get_dData();
		$data['datavalue']=json_encode($this->ddatairr);
		$this->load->view('data_view',$data);
	}
	function get_dData(){
		//Count
		$this->ddatactr=$this->daily_data->getNData($this->DateParam_Today,$this->systemID);
		//System Misc
		$this->ddataall=$this->daily_data->dall($this->DateParam_Today,$this->systemID);
		$this->ddatairr=$this->daily_data->dirr($this->DateParam_Today,$this->systemID);
	}
	function r_dl(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_dL();
		$data['datavalue']=json_encode($this->dataL);
		$this->load->view('data_view',$data);
	}
	function get_dL(){
		$this->dataL=$this->daily_data->Ldata($this->DateParam_Today,$this->systemID);
	}
	function get_dAVG($dateparam,$invparam1,$invparam2){
		$this->dataAVG=$this->daily_data->dACVoltCurrFreq($dateparam,$invparam1,$invparam2);
	}
	function r_dAVG(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();
		$this->timeFunctions();
		$this->InvAssigned();
		$invparam1=$this->inverter_id[0];$invparam2=$this->inverter_id[count($this->inverter_id)-1];
		$this->get_dAVG($this->DateParam_Today,$invparam1,$invparam2);
		$data['datavalue']=json_encode($this->dataAVG);
		$this->load->view('data_view',$data);
	}
	function r_pr(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_compPR();
		$data['datavalue']=$this->prd;
		$this->load->view('data_view',$data);	
	}
	
	function get_compPR(){
		$this->dataPR=$this->daily_data->dCmpPr($this->DateParam_Today,$this->systemID);
		$this->ddataall=$this->daily_data->dall($this->DateParam_Today,$this->systemID);
		
		$this->MinusData=0;
		$this->MinusData=$this->daily_data->EnergyMinus($this->DateParam_Today,$this->systemID);
		
		if($this->dataPR['sEac']>0 && $this->dataPR['sIrr']>0){
			//$this->cmppr=(((($this->dataPR['sEac']-$this->MinusData)-$this->daily_data->x)/$this->sysSize)/(($this->dataPR['sIrr']*(1/12))/1000))*100;
			$this->cmppr=(((($this->dataPR['sEac']-$this->MinusData)-$this->daily_data->x)/$this->sysSize)/(($this->dataPR['sIrr']*(1/12))/1000))*100;
			//$this->cmppr=(((($this->dataPR['sEac']))/$this->sysSize)/(($this->dataPR['sIrr']*(1/12))/1000))*100;
			
			if($this->cmppr >= 100){
				$this->cmppr = 99.99;
			}
			
		}else{
			$this->cmppr=0;
		}
		$this->prd=number_format($this->cmppr,2,'.','');
	}
	
	function r_sysdWk(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdWk();
		$data['datavalue']=json_encode($this->all_weekly_data);
		$this->load->view('data_view',$data);
	}
	
	function r_sysdWkPR(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdWk();
		$data['datavalue']=json_encode($this->all_weekly_pr);
		$this->load->view('data_view',$data);	
	}
	
	function get_sysdWk(){
		//assign default values
		$energy_minus=0;
		
		$this->weekly_sun_date='';$this->weekly_mon_date='';$this->weekly_tue_date='';$this->weekly_wed_date='';$this->weekly_ths_date='';$this->weekly_fri_date='';$this->weekly_sat_date='';
		$this->weekly_sun='';$this->weekly_mon='';$this->weekly_tue='';$this->weekly_wed='';$this->weekly_ths='';$this->weekly_fri='';$this->weekly_sat='';
		$this->weekly_sun_total=0;$this->weekly_mon_total=0;$this->weekly_tue_total=0;$this->weekly_wed_total=0;$this->weekly_ths_total=0;$this->weekly_fri_total=0;$this->weekly_sat_total=0;
		$this->sunirr=0;$this->cmppr_sun=0;
		$this->monirr=0;$this->cmppr_mon=0;
		$this->tueirr=0;$this->cmppr_tue=0;
		$this->wedirr=0;$this->cmppr_wed=0;
		$this->thsirr=0;$this->cmppr_ths=0;
		$this->friirr=0;$this->cmppr_fri=0;
		$this->satirr=0;$this->cmppr_sat=0;
		//Sunday
		$this->weekly_sun=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,1,2),2,'.','');
		$this->sun_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_sun_date='';
		}else{
			$this->weekly_sun_date=$this->weekly_data->date_of_week;
		}
		$this->sunirr = $this->weekly_data->avgIrr;
		
		if($this->sunirr>0){
			$qxc=$this->daily_data->dall($this->weekly_sun_date,$this->systemID);
			
			if($this->weekly_sun_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_sun_date,$this->systemID);
				//echo $energy_minus." x1 <br/>";
			}
			
			$this->cmppr_sun=((( ($this->weekly_sun-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->sunirr*(1/12))/1000))*100;
			if($this->cmppr_sun >= 100){
				$this->cmppr_sun = 99.99;
			}
		}
		
		//Monday
		$this->weekly_mon=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,2,3),2,'.','');
		$this->mon_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_mon_date='';
		}else{
			$this->weekly_mon_date=$this->weekly_data->date_of_week;
		}
		$this->monirr = $this->weekly_data->avgIrr;
		if($this->monirr>0){
			$qxc=$this->daily_data->dall($this->weekly_mon_date,$this->systemID);

			if($this->weekly_mon_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_mon_date,$this->systemID);
				//echo $energy_minus." x2 <br/>";
			}
			
			$this->cmppr_mon=((( ($this->weekly_mon-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->monirr*(1/12))/1000))*100;
			if($this->cmppr_mon >= 100){
				$this->cmppr_mon = 99.99;
			}
		}
		//Tuesday
		$this->weekly_tue=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,3,4),2,'.','');
		$this->tue_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_tue_date='';
		}else{
			$this->weekly_tue_date=$this->weekly_data->date_of_week;
		}
		$this->tueirr = $this->weekly_data->avgIrr;
		if($this->tueirr>0){
			$qxc=$this->daily_data->dall($this->weekly_tue_date,$this->systemID);		
			
			if($this->weekly_tue_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_tue_date,$this->systemID);
				//echo $energy_minus." x3 <br/>";
			}
			
			$this->cmppr_tue=((( ($this->weekly_tue-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->tueirr*(1/12))/1000))*100;
			if($this->cmppr_tue >= 100){
				$this->cmppr_tue = 99.99;
			}
		}
		//Wednesday
		$this->weekly_wed=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,4,5),2,'.','');
		$this->wed_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_wed_date='';
		}else{
			$this->weekly_wed_date=$this->weekly_data->date_of_week;
		}
		$this->wedirr = $this->weekly_data->avgIrr;
		if($this->wedirr>0){
			$qxc=$this->daily_data->dall($this->weekly_wed_date,$this->systemID);
			
			if($this->weekly_wed_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_wed_date,$this->systemID);
				//echo $energy_minus." x4 <br/>";
			}
			
			$this->cmppr_wed=((( ($this->weekly_wed-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->wedirr*(1/12))/1000))*100;	
			if($this->cmppr_wed >= 100){
				$this->cmppr_wed = 99.99;
			}
		}
		//Thursday
		$this->weekly_ths=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,5,6),2,'.','');
		$this->ths_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_ths_date='';
		}else{
			$this->weekly_ths_date=$this->weekly_data->date_of_week;
		}
		$this->thsirr=$this->weekly_data->avgIrr;
		if($this->thsirr>0){	
			$qxc=$this->daily_data->dall($this->weekly_ths_date,$this->systemID);
			
			if($this->weekly_ths_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_ths_date,$this->systemID);
				//echo $energy_minus." x5 <br/>";
			}
			
			$this->cmppr_ths=((( ($this->weekly_ths-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->thsirr*(1/12))/1000))*100;
			if($this->cmppr_ths >= 100){
				$this->cmppr_ths = 99.99;
			}
		}
		//Friday
		$this->weekly_fri=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,6,7),2,'.','');
		$this->fri_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_fri_date='';
		}else{
			$this->weekly_fri_date=$this->weekly_data->date_of_week;
		}
		$this->friirr=$this->weekly_data->avgIrr;
		if($this->friirr>0){
			$qxc=$this->daily_data->dall($this->weekly_fri_date,$this->systemID);
			
			if($this->weekly_fri_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_fri_date,$this->systemID);
				//echo $energy_minus." x6 <br/>";
			}
			
			$this->cmppr_fri=((( ($this->weekly_fri-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->friirr*(1/12))/1000))*100;	
			if($this->cmppr_fri >= 100){
				$this->cmppr_fri = 99.99;
			}
		}
		//Saturday
		$this->weekly_sat=number_format($this->weekly_data->Wdata($this->DateParam_Today,$this->systemID,7,8),2,'.','');
		$this->sat_power_total=$this->weekly_data->daily_power_total;
		if(is_null($this->weekly_data->date_of_week)){
			$this->weekly_sat_date='';
		}else{
			$this->weekly_sat_date=$this->weekly_data->date_of_week;
		}
		$this->satirr=$this->weekly_data->avgIrr;
		if($this->satirr>0){
			$qxc=$this->daily_data->dall($this->weekly_sat_date,$this->systemID);
			
			if($this->weekly_sat_date==''){
				$energy_minus=0;
			}else{
				$energy_minus=0;
				$energy_minus=$this->daily_data->EnergyMinus($this->weekly_sat_date,$this->systemID);
				//echo $energy_minus." x7 <br/>";
			}
			
			$this->cmppr_sat=((( ($this->weekly_sat-$energy_minus)-$this->daily_data->x)/$this->sysSize)/(($this->satirr*(1/12))/1000))*100;
			if($this->cmppr_sat >= 100){
				$this->cmppr_sat = 99.99;
			}
		}
		//Group into array
		$this->all_weekly_data=array(floatval($this->weekly_sun),floatval($this->weekly_mon),floatval($this->weekly_tue),floatval($this->weekly_wed),floatval($this->weekly_ths),floatval($this->weekly_fri),floatval($this->weekly_sat));
		$this->all_weekly_pr=array(floatval($this->cmppr_sun),floatval($this->cmppr_mon),floatval($this->cmppr_tue),floatval($this->cmppr_wed),floatval($this->cmppr_ths),floatval($this->cmppr_fri),floatval($this->cmppr_sat));
		$this->week_sun_total=0;$this->mon_total=0;$this->tue_total=0;$this->wed_total=0;$this->ths_total=0;$this->fri_total=0;$this->sat_total=0;
		$this->week_sun_total=$this->weekly_sun;$this->week_mon_total=$this->weekly_mon;$this->week_tue_total=$this->weekly_tue;$this->week_wed_total=$this->weekly_wed;
		$this->week_ths_total=$this->weekly_ths;$this->week_fri_total=$this->weekly_fri;$this->week_sat_total=$this->weekly_sat;
		$this->sun_power_sum=$this->sun_power_total;$this->mon_power_sum=$this->mon_power_total;$this->tue_power_sum=$this->tue_power_total;$this->wed_power_sum=$this->wed_power_total;
		$this->ths_power_sum=$this->ths_power_total;$this->fri_power_sum=$this->fri_power_total;$this->sat_power_sum=$this->sat_power_total;
		$this->weekly_client_total=array($this->week_sun_total,$this->week_mon_total,$this->week_tue_total,$this->week_wed_total,$this->week_ths_total,$this->week_fri_total,$this->week_sat_total);
		$this->weekly_power_total=array($this->sun_power_sum,$this->mon_power_sum,$this->tue_power_sum,$this->wed_power_sum,$this->ths_power_sum,$this->fri_power_sum,$this->sat_power_sum);
	}
	function r_sysdMt(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdMt();
		$data['datavalue']=json_encode($this->all_monthly_data);
		$this->load->view('data_view',$data);
	}
	function r_sysdMtPR(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdMt();
		$data['datavalue']=json_encode($this->all_monthly_data);
		$this->load->view('data_view',$data);	
	}
	function get_sysdMt(){
		$this->monthly_jan=0;$this->monthly_feb=0;$this->monthly_mar=0;$this->monthly_apr=0;$this->monthly_may=0;$this->monthly_jun=0;
		$this->monthly_jul=0;$this->monthly_aug=0;$this->monthly_sep=0;$this->monthly_oct=0;$this->monthly_nov=0;$this->monthly_dec=0;	
		$this->janirr=0;$this->cmppr_jan=0;$this->febirr=0;$this->cmppr_feb=0;
		$this->marirr=0;$this->cmppr_mar=0;$this->aprirr=0;$this->cmppr_apr=0;
		$this->mayirr=0;$this->cmppr_may=0;$this->junirr=0;$this->cmppr_jun=0;
		$this->julirr=0;$this->cmppr_jul=0;$this->augirr=0;$this->cmppr_aug=0;
		$this->sepirr=0;$this->cmppr_sep=0;$this->octirr=0;$this->cmppr_oct=0;
		$this->novirr=0;$this->cmppr_nov=0;$this->decirr=0;$this->cmppr_dec=0;
		//January
		$this->monthly_jan=number_format($this->monthly_data->Mdata($this->DateParam_Year,'01',$this->systemID),2,'.','');
		$this->jan_power_total=$this->monthly_data->ac_power_total;
		$this->janirr=$this->monthly_data->avgIrr;
		if($this->janirr>0){
			$this->cmppr_jan=(($this->monthly_jan/$this->sysSize)/(($this->janirr*(1/12))/1000))*100;	
		}
		//February
		$this->monthly_feb=number_format($this->monthly_data->Mdata($this->DateParam_Year,'02',$this->systemID),2,'.','');
		$this->feb_power_total=$this->monthly_data->ac_power_total;
		$this->febirr=$this->monthly_data->avgIrr;
		if($this->febirr>0){
			$this->cmppr_feb=(($this->monthly_feb/$this->sysSize)/(($this->febirr*(1/12))/1000))*100;	
		}
		//March
		$this->monthly_mar=number_format($this->monthly_data->Mdata($this->DateParam_Year,'03',$this->systemID),2,'.','');
		$this->mar_power_total=$this->monthly_data->ac_power_total;
		$this->marirr=$this->monthly_data->avgIrr;
		if($this->marirr>0){
			$this->cmppr_mar=(($this->monthly_mar/$this->sysSize)/(($this->marirr*(1/12))/1000))*100;	
		}
		//April
		$this->monthly_apr=number_format($this->monthly_data->Mdata($this->DateParam_Year,'04',$this->systemID),2,'.','');
		$this->apr_power_total=$this->monthly_data->ac_power_total;
		$this->aprirr=$this->monthly_data->avgIrr;
		if($this->aprirr>0){
			$this->cmppr_apr=(($this->monthly_apr/$this->sysSize)/(($this->aprirr*(1/12))/1000))*100;	
		}
		//May
		$this->monthly_may=number_format($this->monthly_data->Mdata($this->DateParam_Year,'05',$this->systemID),2,'.','');
		$this->may_power_total=$this->monthly_data->ac_power_total;
		$this->mayirr=$this->monthly_data->avgIrr;
		if($this->mayirr>0){
			$this->cmppr_may=(($this->monthly_may/$this->sysSize)/(($this->mayirr*(1/12))/1000))*100;	
		}
		//June
		$this->monthly_jun=number_format($this->monthly_data->Mdata($this->DateParam_Year,'06',$this->systemID),2,'.','');
		$this->jun_power_total=$this->monthly_data->ac_power_total;
		$this->junirr=$this->monthly_data->avgIrr;
		if($this->junirr>0){
			$this->cmppr_jun=(($this->monthly_jun/$this->sysSize)/(($this->junirr*(1/12))/1000))*100;	
		}
		//July
		$this->monthly_jul=number_format($this->monthly_data->Mdata($this->DateParam_Year,'07',$this->systemID),2,'.','');
		$this->jul_power_total=$this->monthly_data->ac_power_total;
		$this->julirr=$this->monthly_data->avgIrr;
		if($this->julirr>0){
			$this->cmppr_jul=(($this->monthly_jul/$this->sysSize)/(($this->julirr*(1/12))/1000))*100;	
		}
		//August
		$this->monthly_aug=number_format($this->monthly_data->Mdata($this->DateParam_Year,'08',$this->systemID),2,'.','');
		$this->aug_power_total=$this->monthly_data->ac_power_total;
		$this->augirr=$this->monthly_data->avgIrr;
		if($this->augirr>0){
			$this->cmppr_aug=(($this->monthly_aug/$this->sysSize)/(($this->augirr*(1/12))/1000))*100;	
		}
		//September
		$this->monthly_sep=number_format($this->monthly_data->Mdata($this->DateParam_Year,'09',$this->systemID),2,'.','');
		$this->sep_power_total=$this->monthly_data->ac_power_total;
		$this->sepirr=$this->monthly_data->avgIrr;
		if($this->sepirr>0){
			$this->cmppr_sep=(($this->monthly_sep/$this->sysSize)/(($this->sepirr*(1/12))/1000))*100;	
		}
		//October
		$this->monthly_oct=number_format($this->monthly_data->Mdata($this->DateParam_Year,'10',$this->systemID),2,'.','');
		$this->oct_power_total=$this->monthly_data->ac_power_total;
		$this->octirr=$this->monthly_data->avgIrr;
		if($this->octirr>0){
			$this->cmppr_oct=(($this->monthly_oct/$this->sysSize)/(($this->octirr*(1/12))/1000))*100;	
		}
		//November
		$this->monthly_nov=number_format($this->monthly_data->Mdata($this->DateParam_Year,'11',$this->systemID),2,'.','');
		$this->nov_power_total=$this->monthly_data->ac_power_total;
		$this->novirr=$this->monthly_data->avgIrr;
		if($this->novirr>0){
			$this->cmppr_nov=(($this->monthly_nov/$this->sysSize)/(($this->novirr*(1/12))/1000))*100;	
		}
		//December
		$this->monthly_dec=number_format($this->monthly_data->Mdata($this->DateParam_Year,'12',$this->systemID),2,'.','');
		$this->dec_power_total=$this->monthly_data->ac_power_total;
		$this->decirr=$this->monthly_data->avgIrr;
		if($this->decirr>0){
			$this->cmppr_dec=(($this->monthly_dec/$this->sysSize)/(($this->decirr*(1/12))/1000))*100;	
		}
		$this->all_monthly_data=array(floatval($this->monthly_jan),floatval($this->monthly_feb),floatval($this->monthly_mar),floatval($this->monthly_apr),floatval($this->monthly_may),floatval($this->monthly_jun),floatval($this->monthly_jul),floatval($this->monthly_aug),floatval($this->monthly_sep),floatval($this->monthly_oct),floatval($this->monthly_nov),floatval($this->monthly_dec));
		$this->all_monthly_pr=array(floatval($this->cmppr_jan),floatval($this->cmppr_feb),floatval($this->cmppr_mar),floatval($this->cmppr_apr),floatval($this->cmppr_may),floatval($this->cmppr_jun),floatval($this->cmppr_jul),floatval($this->cmppr_aug),floatval($this->cmppr_sep),floatval($this->cmppr_oct),floatval($this->cmppr_nov),floatval($this->cmppr_dec));
		$this->month_jan_total=0;$this->month_feb_total=0;$this->month_mar_total=0;$this->month_apr_total=0;
		$this->month_may_total=0;$this->month_jun_total=0;$this->month_jul_total=0;$this->month_aug_total=0;
		$this->month_sep_total=0;$this->month_oct_total=0;$this->month_nov_total=0;$this->month_dec_total=0;
		$this->jan_power_sum=0;$this->feb_power_sum=0;$this->mar_power_sum=0;$this->apr_power_sum=0;
		$this->may_power_sum=0;$this->jun_power_sum=0;$this->jul_power_sum=0;$this->aug_power_sum=0;
		$this->sep_power_sum=0;$this->oct_power_sum=0;$this->nov_power_sum=0;$this->dec_power_sum=0;
		$this->month_jan_total=$this->monthly_jan;$this->month_feb_total=$this->monthly_feb;$this->month_mar_total=$this->monthly_mar;$this->month_apr_total=$this->monthly_apr;
		$this->month_may_total=$this->monthly_may;$this->month_jun_total=$this->monthly_jun;$this->month_jul_total=$this->monthly_jul;$this->month_aug_total=$this->monthly_aug;
		$this->month_sep_total=$this->monthly_sep;$this->month_oct_total=$this->monthly_oct;$this->month_nov_total=$this->monthly_nov;$this->month_dec_total=$this->monthly_dec;
		$this->jan_power_sum=$this->jan_power_total;$this->feb_power_sum=$this->feb_power_total;$this->mar_power_sum=$this->mar_power_total;$this->apr_power_sum=$this->apr_power_total;
		$this->may_power_sum=$this->may_power_total;$this->jun_power_sum=$this->jun_power_total;$this->jul_power_sum=$this->jul_power_total;$this->aug_power_sum=$this->aug_power_total;
		$this->sep_power_sum=$this->sep_power_total;$this->oct_power_sum=$this->oct_power_total;$this->nov_power_sum=$this->nov_power_total;$this->dec_power_sum=$this->dec_power_total;
		$this->monthly_client_total=array($this->month_jan_total,$this->month_feb_total,$this->month_mar_total,$this->month_apr_total,$this->month_may_total,$this->month_jun_total,$this->month_jul_total,$this->month_aug_total,$this->month_sep_total,$this->month_oct_total,$this->month_nov_total,$this->month_dec_total);
		$this->monthly_power_total=array($this->jan_power_sum,$this->feb_power_sum,$this->mar_power_sum,$this->apr_power_sum,$this->may_power_sum,$this->jun_power_sum,$this->jul_power_sum,$this->aug_power_sum,$this->sep_power_sum,$this->oct_power_sum,$this->nov_power_sum,$this->dec_power_sum);		
	}
	function r_sysdYrDate(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdYr();
		$data['datavalue']=json_encode($this->all_yearly_date);
		$this->load->view('data_view',$data);
	}
	function r_sysdYrData(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdYr();
		$data['datavalue']=json_encode($this->all_yearly_data);
		$this->load->view('data_view',$data);
	}
	function get_sysdYr(){
		$this->all_yearly_date=array();$this->all_yearly_data=array();$this->yearly_client_total=array();
		$this->all_yearly_date=$this->yearly_data->Ydate($this->systemID);
		$this->all_yearly_data=$this->yearly_data->Ydata($this->systemID);	
		for($col_number=0;$col_number<$this->yearly_data->year_count;$col_number++){
			$this->yearly_client=$this->all_yearly_data[$col_number];
			array_push($this->yearly_client_total,$this->yearly_client);
		}
	}
	function r_sysdYrPr(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->get_sysdYrPr();
		$data['datavalue']=json_encode($this->all_yearly_pr);
		$this->load->view('data_view',$data);	
	}
	function get_sysdYrPr(){
		$this->all_yearly_pr=$this->yearly_data->YdataPR($this->systemID,$this->sysSize);
	}
	function rdBounds(){
		$session_data=$this->session->userdata('logged_in');
		$class_name=$this->uri->rsegment(1);
		$company_name=$session_data['company'];
		$this->SysInfo();		
		$this->timeFunctions();
		$this->getDailyBounds($this->DateParam_Today,$this->systemID);
		$this->daily_bounds=array("min_bound"=>$this->dailyMorning_bound,"max_bound"=>$this->dailyEvening_bound);
		$data['datavalue']=json_encode($this->daily_bounds);
		$this->load->view('data_view', $data);
	}
/**
	extra func
**/
	public function project_data($datetime='',$power='',$energy='',$temp_module='',$w_irrad='',$w_irrad1='',$w_irrad2=''){
		$this->load->helper('file');
		$this->load->helper('date');
		
		//column names
		$col_n1='DATETIME_MISC_DATA';
		$col_n2='SYS_AC_POWER';
		$col_n3='SYS_AC_ENERGY';
		$col_n4='TEMP_MODULE';
		$col_n5='W_IRRADIANCE';
		$col_n6='W_IRRADIANCE_POA_1';
		$col_n7='W_IRRADIANCE_POA_2';
		
		$column_names=$col_n1.','.$col_n2.','.$col_n3.','.$col_n4.','.$col_n5.','.$col_n6.','.$col_n7;
		$dateformat = '%Y%m%d';
		$time = time();
		$path_name='./project_data/';
		$file_name=mdate($dateformat,$time);
		$system_misc_file=$file_name.'_misc.csv';
		$s='';
		
		echo $system_misc_file;
		
		if(file_exists('./project_data/'.$system_misc_file)){
			/* file exists */
			echo 'file exists';
			$STRdata=$datetime.','.$power.','.$energy.','.$temp_module.','.$w_irrad.','.$w_irrad1.','.$w_irrad2;
			write_file('./project_data/'.$system_misc_file,"\n".$STRdata,'a+');
		}else{
			echo 'does not exist';
			$STRcol_name=$column_names;
			$STRdata=$datetime.','.$power.','.$energy.','.$temp_module.','.$w_irrad.','.$w_irrad1.','.$w_irrad2;
			write_file('./project_data/'.$system_misc_file,$STRcol_name."\n".$STRdata);
		}
		echo '<br/>';
		$s=$STRdata;
		$data['datavalue']=$s;
		$this->load->view('data_view',$data);
	}
	
	public function project_rec($datetime='',$proj_key='',$status_str=''){
		$this->load->helper('file');
		$this->load->helper('date');
		
		$col_n1='DATETIME';
		$col_n2='PROJ_NAME';
		$col_n3='STATUS';
		
		$column_names=$col_n1.','.$col_n2.','.$col_n3;
		
		//Get date today
		$dateformat='%Y%m%d';
		$time=time();
		//Path to file
		$path_name='./project_data/';
		//File name
		$file_name=mdate($dateformat,$time);
		$sys_stat_file = $file_name.'_sys_stat.csv';
		//Reset string variable
		$s='';
		
		echo $sys_stat_file;
		if(file_exists('./project_data/'.$sys_stat_file)){
			/* file exists */
			echo 'file already exists, only append to last row';
			$STRdata=$datetime.','.$proj_key.','.$status_str;
			write_file('./project_data/'.$sys_stat_file,"\n".$STRdata,'a+');
		}else{
			/* file does not exist */
			echo 'file does not exist, create new file';
			$STRcol_name=$column_names;
			$STRdata=$datetime.','.$proj_key.','.$status_str;
			write_file('./project_data/'.$sys_stat_file,$STRcol_name."\n".$STRdata);
		}
		echo '<br/>';
		$s=$STRdata;
		$data['datavalue']=$s;
		$this->load->view('data_view',$data);
	}
/** end **/
	public function SystemData_Energy($date_param,$system_param){
		$this->ci = & get_instance();
		$this->ci->load->library('session');
		$this->ci->load->model('daily_db','',TRUE);
		$sysEnData=array();
		$resultSysEn=$this->ci->daily_db->SysDataEnergy($date_param,$system_param);
		$ctr=0;
		foreach($resultSysEn as $row){
			if($ctr==0){
				//time
				$a=$row->datdata;
				//system energy
				$x=floatval($row->sysen);
				$y=floatval($row->sysen);
				$z=$x-$y;
				array_push($sysEnData,array($row->datdata*1000,floatval(number_format($z,1,'.',''))));				
				$temp=floatval($row->sysen);
			}else{
				//time
				$dvctr=2;
				$b=$row->datdata;
		
		/*
				do{
					$x=$b-$a;				
					if(($x)>900){
						$v1=floatval($row->sysen);
						$z1=($v1-$temp)/$dvctr;
						array_push($sysEnData,array((($row->datdata*1000)-900000),floatval(number_format($z1,1,'.',''))));
						$temp=$v1-floatval($z1);
						$a=$b;
						$dvctr++;
					}
				}while(($b-$a)>900);
		*/
		
				$j=$b;
				$k=$a;
				$l=$j-$k;
				$divictr=0;
				do{
					if($l>900){
						$divictr++;
						$l=$l-900;
					}
				}while($l>900);
				if(($b-$a)>900){
					$v1=floatval($row->sysen);
					if($divictr==1){
						$divisor=2;
					}else{
						$divisor=$divictr+1;
					}
					$z1=($v1-$temp)/$divisor;
					if($divictr==1){
						for($ctr=0;$ctr<$divictr;$ctr++){
							array_push($sysEnData,array( (($row->datdata*1000)-(900000*($ctr+1))),floatval(number_format($z1,1,'.',''))) );
						}
					}else{
						for($ctr=0;$ctr<$divictr+1;$ctr++){
							array_push($sysEnData,array( (($row->datdata*1000)-(900000*($ctr+1))),floatval(number_format($z1,1,'.',''))) );
						}							
					}
					$temp=$v1-floatval($z1);
					$a=$row->datdata;
				}
				
				//system energy
				$v=floatval($row->sysen);
				$z=$v-$temp;
				$temp=floatval($row->sysen);
				array_push($sysEnData,array($row->datdata*1000,floatval(number_format($z,1,'.',''))));
				$a=$row->datdata;
				
				
			}
			$ctr++;
		}
		return $sysEnData;
	}	
}
?>