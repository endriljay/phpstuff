<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Wu, Jayson">
	<title>Monitoring Dashboard</title>
	<link rel="shortcut icon" href="css/images/logo/favicon.ico">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	<link type="text/css" href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link type="text/css" href="css/project_monitoring.css" rel="stylesheet" />
	<link type="text/css" href="css/default_chart.css" rel="stylesheet" />
</head>
<body>
	<div class="container">
		<div class="top-dashboard">
			<div class="row">
				<div class="col-md-6 system-information">
					<div id="sysSize" class="sys-space"></div>
					<div id="sysCommissioned" class="sys-space"></div>
					<div id="sysDateToday" class="sys-space"></div>
				</div>
				<div class="col-md-3 text-right"><div id="zWeather" class="items"></div></div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="misc-box">
					<div class="misc-text">Current Power:</div>
					<div id="curr_yield" class="misc-data"></div>
					<div class="misc-text2">kW</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="misc-box">
					<div class="misc-text">Irradiance:</div>
					<div id="curr_irrad" class="misc-data"></div>
					<div class="misc-text2"><sup>W</sup>&frasl;<sub>m&sup2;</sub></div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="misc-box">
					<div class="misc-text">Lifetime Yield:</div>
					<div id="life_yield" class="misc-data"></div>
					<div id="yield_unit" class="misc-text2"></div>
				</div>
			</div>
			
			<!--
			<div class="col-md-3">
				<div class="misc-box">
					<div class="misc-text">Performance Ratio:</div>
					<div id="perf_ratio" class="misc-data"></div>
				</div>
			</div>
			-->
			
		</div>
		
		<!--
		<div class="row">
			<div class="col-md-12"><div id="areachart"></div></div>
		</div>
		-->
		
		<div class="row">
			<div class="col-md-12"><div id="colchart"></div></div>
		</div>
		
		<div class="row">
			<div class="col-md-4"><div id="weeklychart"></div></div>
			<div class="col-md-4"><div id="monthlychart"></div></div>
			<div class="col-md-4"><div id="yearlychart"></div></div>
		</div>
		<div class="row">
			<div class="col-md-12" style="height:200px;">
				<div class="table-wrapper-div" style="overflow-y:scroll; height:90%; width:100%;">
					<table class="table" id="tblErr">
						<thead>
							<tr>
								<th>Time</th>
								<th>Type</th>
								<th>Inverter ID</th>
								<th>Message</th>
							</tr>
						</thead>
						<tbody>					
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script type="text/javascript" src="plugins/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="plugins/highcharts/js/highcharts.js"></script>
	<script type="text/javascript" src="plugins/highcharts/js/highcharts-more.js"></script>
	<script type="text/javascript" src="js/datetime.js"></script>
	<script>
		<!-- Dashboard Settings -->
		var debug_mode=true,timer_daily=90000,timer_weekly=120000,timer_monthly=120000,timer_yearly=120000,timer_system=300000;var datetoday="<?php echo $DateToday;?>",dateparam_chk="<?php echo $DateParam_Curr;?>";var area_data,daily_graph,lTime=<?php echo $lTime;?>,lData=<?php echo $lData;?>;var lengthy1 =0;
		<!-- System Information -->
		var sysEnCol_Ener=<?php echo $sysEnCol_Ener;?>;
		
		var err_message='';
		var sysName=<?php echo $sysName;?>,sysSize=<?php echo $sysSize;?>,sysCommissioned=<?php echo $sysCommissioned;?>,sysWeather=<?php echo $sysWeather;?>;var numinv=<?php echo $number_of_inverters;?>;var ambnd=<?php echo $dailyMorning_bound;?>,pmbnd=<?php echo $dailyEvening_bound;?>;var yctr=<?php echo $ddatactr;?>;var y1=<?php echo $ddataall;?>;var y2=<?php echo $ddatairr;?>;
		<!-- Weekly -->
		var sundt=<?php echo $sunday_date;?>,mondt=<?php echo $monday_date;?>,tuedt=<?php echo $tuesday_date;?>,weddt=<?php echo $wednesday_date;?>,thsdt=<?php echo $thursday_date;?>,fridt=<?php echo $friday_date;?>,satdt=<?php echo $saturday_date;?>;var wkcltot=<?php echo $weekly_client_total;?>;var wkclpr=<?php echo $weekly_client_pr;?>;var wkpwtot=<?php echo $weekly_power_total;?>;
		<!-- Monthly -->
		var mtcltot=<?php echo $monthly_client_total;?>;var mtclpr=<?php echo $monthly_client_pr;?>;var mtpwtot=<?php echo $monthly_power_total;?>;
		<!-- Yearly -->
		var yrcltot_dt=<?php echo $all_yearly_date;?>;var yrcltot_pr=<?php echo $yearly_client_pr;?>;var yrcltot_da=<?php echo $all_yearly_data;?>;
		<!-- Boxes -->
		var irrbox=<?php echo $ddatairrL;?>;var acbox=<?php echo $ddataACL;?>;var yibox=<?php echo $ddataYiL;?>;var prbox=<?php echo $ddataprL;?>;
	</script>
	<!-- WEATHER JAVASCRIPTS -->
	<script type="text/javascript" src="js/weather-widget/jquery.zweatherfeed.js"></script><script type="text/javascript" src="js/weather-widget/zweatherJS.js"></script>
	<!-- GRAPH JAVASCRIPTS -->
	<script type="text/javascript" src="js/graphs/pioneer_monitoring/graph_daily.js"></script><script type="text/javascript" src="js/graphs/pioneer_monitoring/graph_weekly.js"></script><script type="text/javascript" src="js/graphs/pioneer_monitoring/graph_monthly.js"></script><script type="text/javascript" src="js/graphs/pioneer_monitoring/graph_yearly.js"></script><script type="text/javascript" src="js/data/pioneer_monitoring/refresh_data.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			/*System*/
			$('#sysSize').text('System Size: '+sysSize+' kW');$('#sysCommissioned').text('Commissioned: '+sysCommissioned);$('#sysDateToday').text('Date Today: '+datetoday);
			if(yctr <= 0){var xSeconds = 20;$( "#dashboard_err" ).dialog();setTimeout(function() { $( "#dashboard_err" ).dialog("close"); }, 5000);}
			/*Box*/
			$('#curr_yield').text(acbox.toFixed(1));$('#curr_irrad').text(irrbox.toFixed(1));if(yibox>10000){yi_box=(yibox/1000);$('#yield_unit').text('MWh');}else{yi_box=yibox;$('#yield_unit').text('kWh');}$('#life_yield').text(yi_box.toFixed(2));$('#perf_ratio').text(prbox.toFixed(1)+"%");
			/*Weather*/
			pioneerWeather();
			/*Graph*/
			xyz456();
			/*xyz123();*/ /*area*/
			abc123();/*week*/fgh123();/*month*/qwe123();/*year*/
/*		
			debug_mode&&console.log("START");setTimeout(function(){setInterval(function(){r_graph();},timer_system);},15000);
			setInterval(function(){System_Data_Check();},timer_daily);
*/		
			jQuery.ajax({
				url:'project_monitoring/get_errorcodes',
				type:'POST',
				data:'data',
				dataType:'json',
			}).done(function(data){
				setTimeout(function(){
					var errTable=document.getElementById("tblErr"); 
					while(errTable.rows.length>1) 
						errTable.deleteRow(errTable.rows.length-1);
					for(ctr1=0;ctr1<10;ctr1++){
						for(ctr2=0;ctr2<2;ctr2++){
							if(data[ctr1][ctr2].err_code > 0){
								//var x = getErrorMessage( data[ctr1][ctr2].err_type , data[ctr1][ctr2].err_inv_id , data[ctr1][ctr2].err_code );
								//var err_msg = getErrorMessage( data[ctr1][ctr2].err_type , data[ctr1][ctr2].err_code );
								var x = getErrorMessage(data[ctr1][ctr2].err_date,data[ctr1][ctr2].err_inv_id,data[ctr1][ctr2].err_type,data[ctr1][ctr2].err_code);
							}
						}
					}
				},5000);			
			});
			
		});
		
		//setInterval(function(){
			jQuery.ajax({
				url:'error_controller/mail_errorcodes',
				type:'POST',
				data:'data',
				dataType:'json',
			}).done(function(data){
			});
		//},20000);
		
		function getErrorMessage(err_date,err_invid,err_type,err_code){
			jQuery.ajax({
				url:'project_monitoring/get_errormsgs/'+err_type+'/'+err_code,
				type:'POST',
				data:'data',
				dataType:'json',
			}).done(function(data){
				setTimeout(function(){
					y1=err_date;
					y2=err_invid;
					y3=err_type;
					addRow( y1 , y3 , y2 , data);					
				},5000);
			});
		}
		
		function addRow(content,morecontent,evenmorecontent,somuchmorecontent){
			if(!document.getElementsByTagName) return;
			tabBody=document.getElementsByTagName("TBODY").item(0);
			row=document.createElement("TR");
			cell1=document.createElement("TD");cell2=document.createElement("TD");cell3=document.createElement("TD");cell4=document.createElement("TD");
			textnode1=document.createTextNode(content);textnode2=document.createTextNode(morecontent);textnode3=document.createTextNode(evenmorecontent);textnode4=document.createTextNode(somuchmorecontent);
			cell1.appendChild(textnode1);cell2.appendChild(textnode2);cell3.appendChild(textnode3);cell4.appendChild(textnode4);
			row.appendChild(cell1);row.appendChild(cell2);row.appendChild(cell3);row.appendChild(cell4);
			tabBody.appendChild(row);
		}
		
		
	</script>
	<div id="dashboard_err" title="Dashboard Error" class="ui-dialog-content ui-widget-content" style="display: none;"><p>Error with displaying the dashboard. Please contact support.</p></div>	
</body>
</html>

