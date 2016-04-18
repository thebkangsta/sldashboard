<html><head>	<meta charset="utf-8" />	<title>Dashboard | Analytics</title>	<meta name="keywords" content="" />	<meta name="description" content="" />	<meta name="Author" content="Brandon Kang" />	<link rel="shortcut icon" href="" type="image/x-icon">	<link rel="icon" href="" type="image/x-icon">	<!-- mobile settings -->	<meta name="viewport" content="width=device-width, initial-scale=1.0" />	<!-- FONTS -->	<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css'>	<!-- CORE CSS -->	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">	<link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />	<link href="plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css" />	<link href="plugins/css/datepicker.css" rel="stylesheet" type="text/css" />	<link href="plugins/less/datepicker.less" rel="stylesheet" type="text/css" />    <link href="main.css" rel="stylesheet" type="text/css" />	<!-- JS -->	<script type="text/javascript" src="plugins/js/jquery-2.1.4.min.js"></script>	<script type="text/javascript" src="plugins/js/datepicker.js"></script>	<script src="plugins/js/moment.js" type="text/javascript"></script>	<script src="plugins/js/jquery.csv.js"></script>	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>	<script type="text/javascript">		google.charts.load('current', {packages: ['corechart']});		google.charts.setOnLoadCallback(drawChart);		google.charts.setOnLoadCallback(drawPie);		function drawChart() {			//Grabs csv file			$.get("data/analytics.csv", function(csvString) {				//Transforms csv to 2d array				var arrayData = $.csv.toArrays(csvString, {onParseValue: $.csv.hooks.castToScalar});				// Creates new object that holds data				var data = new google.visualization.arrayToDataTable(arrayData);				// column variables				var viewscolumn = 1;				var subscriberscolumn = 2;				var partnerscolumn = 3;				var videoscolumn = 4;				var earningscolumn = 5;				var totalviewscolumn = 6;				var totalsubscriberscolumn = 7;				var totalpartnerscolumn = 8;				var totalvideoscolumn = 9;				var totalearningscolumn = 10;				//default options (Views, last 30 days)				var currentdate = moment().format('M/D/YYYY');				var currentdateindex = findindex(currentdate,arrayData);				var newdateindex = currentdateindex-30;				var options = {					hAxis: {						viewWindow: {							min: newdateindex,							max: currentdateindex						},					},					backgroundColor: 'none',					chartArea: {						left: 100,						top: 120,						width: '90%',					},					legend: 'none',					height: 500,					pointSize: 5,					pointShape: 'circle'				};				//Generate all stats				findtotalstats(arrayData);				//default view (Views, last 30 days)				var chart = new google.visualization.LineChart(document.getElementById('mainchart'));				var dataview = new google.visualization.DataView(data);				dataview.setColumns([0,viewscolumn]);				chart.draw(dataview,options);				var text = null;				var all = findtotaldata(arrayData);				text = all;				document.getElementById('totaldata').innerHTML = text;				//Chart Selection				document.getElementById('chartselector').onchange = function() {					var chartselectedcolumn = eval(this.value+"column");					var dataview = new google.visualization.DataView(data);					dataview.setColumns([0,chartselectedcolumn]);					chart.draw(dataview, {						legend: 'none',						chartArea: {							left: 100,							top: 120,							width: '90%',						},						backgroundColor: 'none',						height: 500,						pointSize: 5,						pointShape: 'circle'					});					changedate();				};				document.getElementById('dateselector').onchange = function() {					changedate();				};				function changedate() {					var currentid = document.getElementById('dateselector');					var currentval = currentid.options[currentid.selectedIndex].value;					var currentdate = moment().format('M/D/YYYY');					if(currentval.localeCompare("last90")==0) {						var newdate = moment().subtract(89, 'days').format('M/D/YYYY');						setfromdate(newdate);						settodate(currentdate);						$('#dateselector').val(currentval);					}					if(currentval.localeCompare("last30")==0) {						var newdate = moment().subtract(29, 'days').format('M/D/YYYY');						setfromdate(newdate);						settodate(currentdate);						$('#dateselector').val(currentval);					}					if(currentval.localeCompare("lastmonth")==0) {						var lastmonthbeg = moment().subtract(1, 'months').date(1).format('M/D/YYYY');						var lastmonthend = moment().date(0).format('M/D/YYYY');						setfromdate(lastmonthbeg);						settodate(lastmonthend);						$('#dateselector').val(currentval);					}					if(currentval.localeCompare("lifetime")==0) {						var beg = moment().startOf('year').format('M/D/YYYY');						var end = moment().endOf('year').format('M/D/YYYY');						setfromdate(beg);						settodate(end);						$('#dateselector').val(currentval);					}					if(currentval.localeCompare("customrange")==0) {						var beg = $('.date-from').val();						var end = $('.date-to').val();						setfromdate(beg);						settodate(end);					}					var text = null;					var all = findtotaldata(arrayData);					text = all;					//for()					document.getElementById('totaldata').innerHTML = text;				};				function resizechart() {					var currentchartID = document.getElementById('chartselector');					var currentchartval = currentchartID.options[currentchartID.selectedIndex].value;					var chartselectedcolumn = eval(currentchartval+"column");					var dataview = new google.visualization.DataView(data);					dataview.setColumns([0,chartselectedcolumn]);				};				$(window).resize(function(){					resizechart();				});				function findtotalstats(array) {					var arrayofstats = ["earnings","views","subscribers","partners","videos"];					for(var k=0;k<arrayofstats.length;k++) {						var sum = 0;						for(var i=1;i<array.length;i++) {							sum += array[i][eval(arrayofstats[k]+"column")];						}						var parts = sum.toString().split(".");						if(parts[1]!==undefined) {							var decimal = parts[1];							parts[1] = decimal.slice(0,2);							parts[0] = "$"+parts[0];						}    					parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");    					parts = (parts.join("."));						document.getElementById(arrayofstats[k]).innerHTML = parts;					}				}				function findtotaldata(array) {					var currentchartID = document.getElementById('chartselector');					var currentchartval = currentchartID.options[currentchartID.selectedIndex].value;					if(currentchartval.indexOf("total")===0) {						currentchartval = currentchartval.split("total").pop();					}					var chartselectedcolumn = eval(currentchartval+"column");					var currentid = document.getElementById('dateselector');					var currentval = currentid.options[currentid.selectedIndex].value;					var currentdate = moment().format('M/D/YYYY');					var currentdateindex = findindex(currentdate,arrayData);					var date;					if(currentval.localeCompare("last90")==0) {						var newdateindex = currentdateindex-89;						date = "last 90 days";					}					if(currentval.localeCompare("last30")==0) {						var newdateindex = currentdateindex-29;						date = "last 30 days";					}					if(currentval.localeCompare("lastmonth")==0) {						var lastmonthbeg = moment().subtract(1, 'months').date(1).format('M/D/YYYY');						var newdateindex = findindex(lastmonthbeg,arrayData);						var lastmonthend = moment().date(0).format('M/D/YYYY');						var currentdateindex = findindex(lastmonthend,arrayData);						date = "last month";					}					if(currentval.localeCompare("lifetime")==0) {						var newdateindex = 1;						currentdateindex = array.length-1;						date = "lifetime";					}					if(currentval.localeCompare("customrange")==0) {						var beg = $('.date-from').val();						var newdateindex = findindex(beg,arrayData);						var end = $('.date-to').val();						var currentdateindex = findindex(end,arrayData);					}					var sum = 0;					for(var i=newdateindex;i<=currentdateindex;i++) {						sum += array[i][chartselectedcolumn];					}					var parts = sum.toString().split(".");					if(parts[1]!==undefined) {						var decimal = parts[1];						parts[1] = decimal.slice(0,2);						parts[0] = "$"+parts[0];					}    				parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");    				parts = (parts.join("."));					if(currentval.localeCompare("lifetime")==0) {						return parts+" "+date+" "+currentchartval;					}					if(currentval.localeCompare("customrange")==0) {						return parts+" total "+currentchartval;					}					return parts+" "+currentchartval+" "+date;				}				$(document).ready(function() {					$('.topofmainchart .input-daterange').datepicker({    					format: "m/d/yyyy",						startDate: new Date(2016,0,1),						endDate: new Date(2016,11,31),					});					var currentdate = moment().format('M/D/YYYY');					var monthago = moment().subtract(29, 'days').format('M/D/YYYY');					$('.date-from').datepicker('setDate', monthago);					$('.date-to').datepicker('setDate', currentdate);				});				$('.date-from').datepicker().on('changeDate',function() {					$('#dateselector').val("customrange");					var currenttodate = $('.date-to').val();					var currentfromdate = $('.date-from').val();					var fromindex = findindex(currentfromdate,arrayData)-1;					var toindex = findindex(currenttodate,arrayData)					var currentid = document.getElementById('chartselector');					var currentval = currentid.options[currentid.selectedIndex].value;					var chartselectedcolumn = eval(currentval+"column");					var dataview = new google.visualization.DataView(data);					dataview.setColumns([0,chartselectedcolumn]);					chart.draw(dataview,{						hAxis: {viewWindow: {min: fromindex, max: toindex}},						legend: 'none',						chartArea: {							left: 100,							top: 120,							width: '90%',						},						backgroundColor: 'none',						height: 500,						pointSize: 5,						pointShape: 'circle'					});					var text = null;					var all = findtotaldata(arrayData);					text = all;					document.getElementById('totaldata').innerHTML = text;				});				$('.date-to').datepicker().on('changeDate',function(){					$('#dateselector').val("customrange");					var currenttodate = $('.date-to').val();					var currentfromdate = $('.date-from').val();					var fromindex = findindex(currentfromdate,arrayData)-1;					var toindex = findindex(currenttodate,arrayData)					var currentid = document.getElementById('chartselector');					var currentval = currentid.options[currentid.selectedIndex].value;					var chartselectedcolumn = eval(currentval+"column");					var dataview = new google.visualization.DataView(data);					dataview.setColumns([0,chartselectedcolumn]);					chart.draw(dataview,{						hAxis: {viewWindow: {min: fromindex, max: toindex}},						legend: 'none',						chartArea: {							left: 100,							top: 120,							width: '90%',						},						backgroundColor: 'none',						height: 500,						pointSize: 5,						pointShape: 'circle'					});					var text = null;					var all = findtotaldata(arrayData);					text = all;					document.getElementById('totaldata').innerHTML = text;				});				function setfromdate(from) {					$('.date-from').datepicker({    					format: 'm/d/yyyy',					});					$('.date-from').datepicker('setDate', from);				}				function settodate(to) {					$('.date-to').datepicker({    					format: 'm/d/yyyy',					});					$('.date-to').datepicker('setDate', to);				}			}); // End of .get function			function findindex(date,array) {				for(var i = 1; i < array.length; i++) {					if(array[i][0] == date) {						return i;					}				}			}		}; // End of drawChart	function drawPie() {		//Grabs csv file		$.get("data/applications.csv", function(csvString) {			//Transforms csv to 2d array			var arrayData = $.csv.toArrays(csvString, {onParseValue: $.csv.hooks.castToScalar});			// Creates new object that holds data			var data = new google.visualization.arrayToDataTable(arrayData);			var options = {				chartArea: {					top: 50,					width: '90%',				},				backgroundColor: 'none',				legend: {					position: 'right',					alignment: 'center',				},				pieSliceText: 'value',				height: 500,				pieHole: 0.5,			};			//default view (Views, last 30 days)			var chart = new google.visualization.PieChart(document.getElementById('piechart'));			var dataview = new google.visualization.DataView(data);			chart.draw(dataview,options);			function resizechart() {				var chart = new google.visualization.PieChart(document.getElementById('piechart'));				var dataview = new google.visualization.DataView(data);				chart.draw(dataview,options);			};			$(document).ready(function() {				var text = null;				var linked = arrayData[1][1];				var all = findnumberofapplications(arrayData);				var conversion = Math.round(linked/all*100);				if(conversion > 70) {					text = conversion+"% conversion rate, Nice!";				}				if(conversion < 70 && conversion > 30) {					text = conversion+"% conversion rate, Not Bad.";				}				if(conversion<30) {					text = conversion+"% conversion rate, Keep Impmroving!";				}				//for()				document.getElementById('conversion').innerHTML = text;			});			function findnumberofapplications(array) {				var sum = 0;				for(var i=1; i <array.length;i++) {					sum += array[i][1];				}				return sum;			}			$(window).resize(function(){					resizechart();			});		});	};	</script></head><body>	<!-- NAVIGATION -->	<div class="topNav">	<img src="assets/images/BannerWhite.png" style="width:200px;"/>			<ul class="navButtons">				<li><a>Home <span class="fa fa-home"></a></span></li>				<li><a>Applications <span class="fa fa-wpforms"></a></li>				<li><a>Partners <span class="fa fa-user fa-1x"></a></li>				<li class="active"><a>Analytics <span class="fa fa-line-chart"></a></li>				<li class="last"><a>Earnings <span class="fa fa-dollar"></a></li>			</ul>	</div>	<!-- END OF NAV --><div class="dashboard">	<!-- MAIN CHART DATA -->	<div class="mainContent">		<div class="row">			<div class="wrapper">				<section class="overallcontainer">					<span class="charttop">Overall Statistics</span>					<div class="col-md-2 col-md-offset-1">Earnings						<p id="earnings"></p>					</div>    				<div class="col-md-2">Views						<p id="views"></p>					</div>    				<div class="col-md-2">Subscribers						<p id="subscribers"></p>					</div>    				<div class="col-md-2">Partners						<p id="partners"></p>					</div>    				<div class="col-md-2">Videos						<p id="videos"></p>					</div>				</section>			</div>		</div> <!-- END OF FIRST ROW -->    	<div class="row">    		<div class="wrapper">				<section class="mainchartcontainer">					<div class="charttop">Statistics						<div id="totaldata" style="float: right;padding-right:15px;"></div>					</div>					<div class="topofmainchart">						<select id="chartselector" >							<option value="views" selected>Views</option>							<option value="subscribers">Subscribers</option>							<option value="partners">Partners</option>							<option value="videos">Videos</option>							<option value="earnings">Earnings</option>							<option value="totalviews">Total Views</option>							<option value="totalsubscribers" >Total Subscribers</option>							<option value="totalpartners">Total Partners</option>							<option value="totalvideos">Total Videos</option>							<option value="totalearnings">Total Earnings</option>						</select>						<select id="dateselector">							<option value="customrange" disabled>Custom Range</option>							<option value="last30" selected>Last 30 Days</option>							<option value="lastmonth">Last Month</option>							<option value="last90">Last 90 Days</option>							<option value="lifetime">Lifetime</option>						</select>						<div class="input-daterange input-group" id="datepicker">    						<input type="text" class="date-from form-control" name="start"/>    						<span class="input-group-addon">to</span>    						<input type="text" class="date-to form-control" name="end"/>						</div>					</div>					<div id="mainchart"></div>				</section> <!-- END OF MAIN CHART DATA -->				<section class="piechartcontainer">					<span class="charttop">Applications</span>					<div id="piechart"></div>					<h1 class="pieinfo" id="conversion"></h1>				</section>			</div>    	</div>    </div> <!-- END OF SECOND ROW --></div>	<!-- JAVASCRIPT FILES -->	<script src="plugins/js/datepicker.js"></script></body></html>