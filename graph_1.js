Highcharts.setOptions({global:{useUTC:false},chart:{style:{fontFamily:'OpenSans-light'}},});
function xyz123(){
	area_data=new Highcharts.Chart({
		chart:{
			alignTicks:false,renderTo:'areachart',borderWidth:0,spacingRight:30,plotBackgroundColor:null,backgroundColor:null,style:{fontFamily:'OpenSans-light'},
			events:{
				load:function(){
					inverter1_data=this.series[0];
					daily_graph=setInterval(function(){
						prbox_r();lengthy1=area_data.series[0].data.length;
						
						jQuery.ajax(
							{url:'pioneer_monitoring/r_dAVG',type:'POST',data:'data',dataType:'json',}
						).done(function(data){
							pAvolt=data.voltaAVG;pBvolt=data.voltbAVG;pCvolt=data.voltcAVG;pAcurr=data.curraAVG;pBcurr=data.currbAVG;pCcurr=data.currcAVG;pABCFreq=data.freqAVG;
							setTimeout(function(){
								if(parseFloat(pAvolt)>0){$('#pA_volt').text(parseFloat(pAvolt).toFixed(1));}else{$('#pA_volt').text('-');}
								if(parseFloat(pBvolt)>0){$('#pB_volt').text(parseFloat(pBvolt).toFixed(1));}else{$('#pB_volt').text('-');}
								if(parseFloat(pCvolt)>0){$('#pC_volt').text(parseFloat(pCvolt).toFixed(1));}else{$('#pC_volt').text('-');}
								if(parseFloat(pAcurr)>0){$('#pA_curr').text(parseFloat(pAcurr).toFixed(1));}else{$('#pA_curr').text('-');}
								if(parseFloat(pBcurr)>0){$('#pB_curr').text(parseFloat(pBcurr).toFixed(1));}else{$('#pB_curr').text('-');}
								if(parseFloat(pCcurr)>0){$('#pC_curr').text(parseFloat(pCcurr).toFixed(1));}else{$('#pC_curr').text('-');}
								if(parseFloat(pABCFreq)>0){$('#pABC_freq').text(parseFloat(pABCFreq).toFixed(1));}else{$('#pABC_freq').text('-');}}
							,35000);
						});
						
						jQuery.ajax(
							{url:'pioneer_dashboard/r_dl',type:'POST',data:'data',dataType:'json',}
						).done(function(data){
							lTime=data.latest_time;lData=parseFloat(data.latest_ac);acbox=parseFloat(data.latest_ac);
							yibox=data.latest_yi;tempbox=data.latest_temp;avgpowbox=data.latest_avgpow;irrbox=data.latest_irr;
							
							debug_mode&&console.log(data.latest_time+" X "+data.latest_ac+" X "+data.latest_yi+" X "+data.latest_temp+" X "+data.latest_avgpow+" X "+data.latest_irr);
					
							if(area_data.series[0].data.length>1){lengthy1=area_data.series[0].data.length;if((area_data.series[0].data[lengthy1-1].x==data.latest_time)){
								debug_mode&&console.log(area_data.series[0].data[lengthy1-1].x);debug_mode&&console.log(lTime);debug_mode && console.log("Same data");
							}else{
									debug_mode&&console.log(area_data.series[0].data[lengthy1-1].x);debug_mode&&console.log(lTime);debug_mode && console.log("Different data");
									
									setTimeout(function(){inverter1_data.addPoint([data.latest_time,lData],false);},5000);
									setTimeout(function(){if(area_data.series[0].data[lengthy1-1].y!=data.latest_ac){
											console.log(area_data.series[0].data[lengthy1-1].y+" "+data.latest_ac);
											setTimeout(function(){Ref_dCtr();debug_mode && console.log("Data count: "+yctr);
												if((yctr-lengthy1)>5){
													Ref_dRdAll();
													setTimeout(function(){
														area_data.series[0].setData(y1,true);
														//Box
														$('#curr_yield').text(parseFloat(acbox).toFixed(1));
														$('#curr_irrad').text(parseFloat(irrbox).toFixed(1));
														if(yibox>10000){
															yi_box=(yibox/1000);
															$('#yield_unit').text('MWh');
														}else{
															yi_box=yibox;
															$('#yield_unit').text('kWh');
														}
														$('#life_yield').text(parseFloat(yi_box).toPrecision(5));
														$('#perf_ratio').text(parseFloat(prbox).toFixed(2)+"%");
														$('#temp_mod').text(parseFloat(tempbox).toFixed(2));
														$('#avg_pow').text(parseFloat(avgpowbox).toFixed(2));
														area_data.redraw();
													},5000);
												}else{
													console.log(area_data.series[0].data[lengthy1-1].y+" "+lData+"b");
													setTimeout(function(){
														//Box
														$('#curr_yield').text(parseFloat(acbox).toFixed(1));
														$('#curr_irrad').text(parseFloat(irrbox).toFixed(1));
														if(yibox>10000){
															yi_box=(yibox/1000);
															$('#yield_unit').text('MWh');
														}else{
															yi_box=yibox;
															$('#yield_unit').text('kWh');
														}
														$('#life_yield').text(parseFloat(yi_box).toPrecision(5));
														$('#perf_ratio').text(parseFloat(prbox).toFixed(2)+"%");
														$('#temp_mod').text(parseFloat(tempbox).toFixed(2));
														$('#avg_pow').text(parseFloat(avgpowbox).toFixed(2));
														area_data.redraw();
													},5000);
												}
											},5000);
										}else{
											if((yctr-lengthy1)>5){
											console.log(area_data.series[0].data[lengthy1-1].y+" "+lData+"c");
												Ref_dRdAll();
												setTimeout(function(){
													area_data.series[0].setData(y1,true);
													//Box
													$('#curr_yield').text(parseFloat(acbox).toFixed(1));
													$('#curr_irrad').text(parseFloat(irrbox).toFixed(1));
													if(yibox>10000){
														yi_box=(yibox/1000);
														$('#yield_unit').text('MWh');
													}else{
														yi_box=yibox;
														$('#yield_unit').text('kWh');
													}
													$('#life_yield').text(parseFloat(yi_box).toPrecision(5));
													$('#perf_ratio').text(parseFloat(prbox).toFixed(2)+"%");
													$('#temp_mod').text(parseFloat(tempbox).toFixed(2));
													$('#avg_pow').text(parseFloat(avgpowbox).toFixed(2));
													area_data.redraw();
												},5000);
											}else{
												console.log(area_data.series[0].data[lengthy1-1].y+" "+lData+"d");
												setTimeout(function(){
													//Box
													$('#curr_yield').text(parseFloat(acbox).toFixed(1));
													$('#curr_irrad').text(parseFloat(irrbox).toFixed(1));
													if(yibox>10000){
														yi_box=(yibox/1000);
														$('#yield_unit').text('MWh');
													}else{
														yi_box=yibox;
														$('#yield_unit').text('kWh');
													}
													$('#life_yield').text(parseFloat(yi_box).toPrecision(5));
													$('#perf_ratio').text(parseFloat(prbox).toFixed(2)+"%");	
													$('#temp_mod').text(parseFloat(tempbox).toFixed(2));
													$('#avg_pow').text(parseFloat(avgpowbox).toFixed(2));
													area_data.redraw();
													debug_mode&&console.log("Do nothing");
												},5000);
											}
										}
									},30000);							
								}
							}else{debug_mode&&console.log("No data...");Ref_dRdAll();setTimeout(function(){area_data.series[0].setData(y1,true);area_data.redraw();},30000);}	

						});
					},timer_daily);
				}
			}
		},
		exporting:{enabled:false},legend:{enabled:true,itemStyle:{color:'#000000',}},credits:{enabled:false},title:{text:'Yield',style:{color:'#000000'}},
		xAxis:{type:'datetime',tickPixelInterval:150,labels:{enabled:true,style:{color:'#000000'}},style:{color:'#000000'},tickWidth:1,lineWidth:1,lineColor:'#000000',plotBands:[],min:ambnd,max:pmbnd,minRange:900*1000},
	
	
		yAxis:[{endOnTick:true,showFirstLabel:true,startOnTick:true,title:{text:'Power',rotation:-90,style:{color:'#000000',fontFamily:'OpenSans-light'}},labels:{enabled:true,style:{color:'#000000',fontFamily:'OpenSans-light'},},plotBands:[],min:0,minTickInterval:1,minorGridLineWidth:0}],
	
		plotOptions:{series:{marker:{enabled:false,states:{hover:{enabled:true}}}}},
		tooltip:{useHTML:true,shared:true,formatter:function(){var s="Date: <b>"+Highcharts.dateFormat('%b %e, %Y %H:%M',this.x)+"</b>";sum = 0;$.each(this.points,function(i,point){s+="<br /><span style=\"color: "+point.series.color+"\"> "+point.series.name+": </span> <b>"+Highcharts.numberFormat(point.y,1)+"</b>";s+=" <b>"+this.series.tooltipOptions.valueSuffix+"</b>";});return s;}},
		
		series:[{name:'Power',type:'area',yAxis:0,color:'#F4AD14',data:y1,lineWidth:1.0,lineColor:'#000000',marker:{enabled:false},tooltip:{valueSuffix:' kW',},}]
	})
}