<?php
Class access_to_db extends CI_Model{
	/* DAILY */
	function getTodaysData($DateToday){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT UNIX_TIMESTAMP(DateTime) as todaystime, Pac as todayspac, Irradiance as todaysirr
		FROM inverter_data 
		WHERE DateTime 
		LIKE '".$DateToday."%' 
		ORDER BY DateTime");
		return $query->result();
	}

	function getLatestPac($DateToday){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT UNIX_TIMESTAMP(DateTime) as latesttime, Pac as latestpac, Ppv_A as latestvolt
			FROM inverter_data
			WHERE DateTime
			LIKE '".$DateToday."%'
			ORDER BY DateTime DESC LIMIT 1");
		return $query->result();
	}
	
	function getTodayCount($DateToday){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT COUNT(*) as totalctr_today
			FROM inverter_data
			WHERE DATE(datetime) = '".$DateToday."'");
		return $query->result();
	}
	
	/* WEEKLY */
	function getWeeksData($DateToday,$value1,$value2){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT date(DateTime) as thedate, Eac as weeklyeac
			FROM inverter_data
			WHERE DateTime
			BETWEEN AddDate('".$DateToday."',-DayOfweek('".$DateToday."')+".$value1.")
			AND AddDate('".$DateToday."',".$value2."-DayOfweek('".$DateToday."'))
			ORDER BY DateTime DESC
			LIMIT 1");
		return $query->result();
	}	
	
	/* MONTHLY */
	function getMonthsData($DateYear,$DateMonth){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT DateTime as timedate, Eac as monthlyeac
			FROM inverter_data
			WHERE DateTime
			LIKE '".$DateYear."-".$DateMonth."-%'
			ORDER BY DateTime DESC LIMIT 1;");
		return $query->result();
	}

	/* YEARLY */
	function getYearsData(){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT YEAR(datetime) as dateyear, MAX(eac) as yearlyeac
			FROM inverter_data
			GROUP BY YEAR(datetime);");
		return $query->result();
	}
	
	function getYearsDate(){
		$inv_data = $this->load->database('inverter_data',TRUE);
		$inv_data->query("SET time_zone='+8:00'");
		$query = $inv_data->query("SELECT DISTINCT YEAR(datetime) as dateyear
			FROM inverter_data;");
		return $query->result();	
	}
}
?>