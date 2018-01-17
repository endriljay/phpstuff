function graph_refresh(){
	now=new Date();
	datetoday=now.format("sqliteDate");
	Refresh_Daily_Count();
	debug_mode&&console.log("count is: "+daily_data_count);
	debug_mode&&console.log("#1: "+datetoday);
	debug_mode&&console.log("#2: "+dateparam_chk);
	if(datetoday==dateparam_chk){
		//Do nothing
	}else{
		//Do something

		if(daily_data_count>=1){
			Refresh_Daily_Data();
			debug_mode && console.log("power refreshed");
			Refresh_Irradiance_Data();
			debug_mode && console.log("irradiance refreshed");
			Refresh_Bounds();
			debug_mode && console.log("bounds refreshed");
			setTimeout(function(){
				area_data.xAxis[0].setExtremes(dailyMorning_bound,dailyEvening_bound,false);
				debug_mode && console.log("bound assigned");
				area_data.series[0].setData(daily_client_total,false);
				debug_mode && console.log("power assigned");
				area_data.series[1].setData(daily_irradiance_all,false);
				debug_mode && console.log("irradiance assigned");
				area_data.redraw();
			},15000);
		}else{
			debug_mode && console.log("do nothing");
		}
	}
}

function System_Data_Check(){
	//Dashboard - Status
	var var_graph_time=area_data.series[0].data[area_data.series[0].data.length-1].x;
	var var_function_time=Math.round((new Date()).getTime()/1000)*1000;
	var var_difference_time=var_function_time-var_graph_time;
	var tempDate=new Date();
	var tempHrs=tempDate.getHours();
	debug_mode&&console.log("Hour is: "+tempHrs+" Time Difference: "+var_function_time+" - "+var_graph_time+" = "+var_difference_time);
	if(tempHrs>=6&&tempHrs<17){
		if(var_difference_time<3599998){
		//1799999
		//150000
			debug_mode&&console.log("Pass By #1");
		}else{
			debug_mode&&console.log("System Error - Dashboard Down");
			//$( "#sysdata-check" ).dialog();
		}
	}else if(tempHrs>=19&&tempHrs<=23){
		debug_mode&&console.log("Pass By #2");
	}else if(tempHrs>=0&&tempHrs<=5){
		debug_mode&&console.log("Pass By #3");
	}
	/* End Dashboard - Status */
}

function Refresh_Daily_Count(){
	jQuery.ajax({
		url:'demo/refreshDailyCount',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		daily_data_count = data;
	});
}

function Refresh_TotalEac_Daily(){
	jQuery.ajax({
		url:'demo/refresh_TotalEacDaily',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		eac_diff = data;
	});
}

function Refresh_Power_Sum(){
	jQuery.ajax({
		url:'demo/Power_SumDaily',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		power_total = data;
	});
}

function Refresh_Irradiance_Latest(){
	jQuery.ajax({
		url:'demo/getSystemData_Latest',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		irradiance_latest = data.irradiance;
	});
}

function Refresh_Bounds(){
	jQuery.ajax({
		url:'demo/refreshDailyBounds',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		dailyMorning_bound = data.min_bound;
		dailyEvening_bound = data.max_bound;
	});
}

function Refresh_Daily_Data(){
	jQuery.ajax({
		url:'demo/refreshDailyData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		daily_client_total = data;
	});
}

function Refresh_Irradiance_Data(){
	jQuery.ajax({
		url:'demo/refreshSystemData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		daily_irradiance_all = data;
	});
}

function Refresh_Irradiance_Sum(){
	jQuery.ajax({
		url:'demo/Irr_SumDaily',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		irr_total_daily = data;
	});
}

function Refresh_Weekly_Data(){
	jQuery.ajax({
		url:'demo/refreshWeeklyData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		weekly_power_total = data.weeks_power;
		weekly_client_total = data.weeks_data;
		sunday_date = data.weeks_date[0]; monday_date = data.weeks_date[1]; tuesday_date = data.weeks_date[2];
		wednesday_date = data.weeks_date[3]; thursday_date = data.weeks_date[4]; friday_date = data.weeks_date[5]; saturday_date = data.weeks_date[6];
	});
}

function Refresh_Weekly_Irr(){
	jQuery.ajax({
		url:'demo/refreshIrr_WeeklyData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		irr_sunday = data[0];
		irr_monday = data[1];
		irr_tuesday = data[2];
		irr_wednesday = data[3];
		irr_thursday = data[4];
		irr_friday = data[5];
		irr_saturday = data[6];
	});
}

function Refresh_Monthly_Data(){
	jQuery.ajax({
		url:'demo/refreshMonthlyData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		monthly_client_total = data.month_data;
		monthly_power_total = data.month_power;
	});
}

function Refresh_Monthly_Irr(){
	jQuery.ajax({
		url:'demo/refreshIrr_MonthlyData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		irr_jan = data[0]; irr_feb = data[1]; irr_mar = data[2]; irr_apr = data[3];
		irr_may = data[4]; irr_jun = data[5]; irr_jul = data[6]; irr_aug = data[7];
		irr_sep = data[8]; irr_oct = data[9]; irr_nov = data[10]; irr_dec = data[11];
	});
}

function Refresh_Yearly_Date(){
	jQuery.ajax({
		url:'demo/refreshYearlyDate',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		all_yearly_date0 = data[0];
		all_yearly_date1 = data[1];
	});
}

function Refresh_Yearly_Data(){
	jQuery.ajax({
		url:'demo/refreshYearlyData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		yearly_client_total = data;
	});
}

/*******************
	Pioneer
******************/

function Ref_dRdAll(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_dDataAll',
		type:'POST',
		data:'data',
		dataType:'json'
	}).done(function(data){
		y1 = data;
	});
}
function Ref_dCtr(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_dDataCtr',
		type:'POST',
		data:'data',
		dataType:'json'
	}).done(function(data){
		yctr = data;
	});
}
function Ref_MtDPR(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdMtPR',
		type:'POST',
		data:'data',
		dataType:'json'
	}).done(function(data){
		mtclpr = data;
	});
}
function Ref_MtD(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdMt',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		mtcltot = data;
	});
}

function Ref_WkD(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdWk',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		wkcltot = data;
	});
}

function Ref_WkDPR(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdWkPR',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		wkclpr=data;
	});
}
function Ref_YrDate(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdYrDate',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		yrcltot_dt=data;
	});
}

function Ref_YrData(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdYrData',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		yrcltot_da=data;
	});
}

function Ref_YrPR(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_sysdYrPr',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		yrcltot_pr=data;
	});
}

function r_graph(){
	now=new Date();
	datetoday=now.format("sqliteDate");
	//datetoday='2014-02-11';
	//Refresh_Daily_Count();
	Ref_dCtr();
	debug_mode&&console.log("count is: "+yctr);
	debug_mode&&console.log("#1: "+datetoday);
	debug_mode&&console.log("#2: "+dateparam_chk);
	if(datetoday==dateparam_chk){
		//Do nothing
		debug_mode&&console.log("Do nothing");
	}else{
		//Do something
		if(yctr>=1){
			Ref_dRdAll()
			debug_mode && console.log("power refreshed");
			
			rf_bds();
			debug_mode && console.log("bounds refreshed");
			
			setTimeout(function(){
				area_data.xAxis[0].setExtremes(ambnd,pmbnd,false);
				debug_mode && console.log("bound assigned: "+ambnd+" "+pmbnd);
				area_data.series[0].setData(y1,false);
				debug_mode && console.log("power assigned");
				area_data.redraw();
			},20000);
		}else{
			debug_mode && console.log("do nothing");
		}
	}
}

function rf_bds(){
	jQuery.ajax({
		url:'pioneer_monitoring/rdBounds',
		type:'POST',
		data:'data',
		dataType:'json',		
	}).done(function(data){
		ambnd=min_bound;
		pmbnd=max_bound;
	});
}

function syschk(){
	//Dashboard - Status
	var var_graph_time=area_data.series[0].data[area_data.series[0].data.length-1].x;
	var var_function_time=Math.round((new Date()).getTime()/1000)*1000;
	var var_difference_time=var_function_time-var_graph_time;
	var tempDate=new Date();
	var tempHrs=tempDate.getHours();
	debug_mode&&console.log("Hour is: "+tempHrs+" Time Difference: "+var_function_time+" - "+var_graph_time+" = "+var_difference_time);
	if(tempHrs>=6&&tempHrs<17){
		if(var_difference_time<1799999){
		//1799999
		//150000
			debug_mode&&console.log("Pass By #1");
		}else{
			debug_mode&&console.log("System Error - Dashboard Down");
			//$( "#sysdata-check" ).dialog();
		}
	}else if(tempHrs>=19&&tempHrs<=23){
		debug_mode&&console.log("Pass By #2");
	}else if(tempHrs>=0&&tempHrs<=5){
		debug_mode&&console.log("Pass By #3");
	}
	/* End Dashboard - Status */
}

function prbox_r(){
	jQuery.ajax({
		url:'pioneer_monitoring/r_pr',
		type:'POST',
		data:'data',
		dataType:'json',
	}).done(function(data){
		prbox=data;
	});
}