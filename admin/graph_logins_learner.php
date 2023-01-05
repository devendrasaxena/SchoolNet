<?php
include_once('../header/adminHeader.php');
ini_set('max_execution_time', 60 * 15);
ini_set('display_errors',1);
error_reporting(1);

if(isset($curCustomer) && $curCustomer!=""){
	$curCustomer = trim($curCustomer);	
}

$graphObj = new graphController();
$limit = 12;

if(isset($_REQUEST['start_date']) && $_REQUEST['start_date']!=""){
	$start_date = trim($_REQUEST['start_date']);
	$start_date_qry = date('Y-m-d', strtotime($start_date));
	$year = date('Y', strtotime($start_date));

}else{
	$start_date =  date('d-m-Y', strtotime('-7 days'));
	$start_date_qry = date('Y-m-d', strtotime($start_date));
	$year = date('Y', strtotime($start_date));
}
if(isset($_REQUEST['end_date']) && $_REQUEST['end_date']!=""){
	$end_date = trim($_REQUEST['end_date']);
	$end_date_qry = date('Y-m-d', strtotime($end_date));
	$year = date('Y', strtotime($end_date));
}else{
	$end_date = date('d-m-Y');
	$end_date_qry = date('Y-m-d', strtotime($end_date));
	$year = date('Y', strtotime($end_date));
}

$datetoshow1=date('d-M-Y',strtotime($start_date));
$datetoshow2=date('d-M-Y',strtotime($end_date));



$date1=date_create(date('Y-m-d',strtotime($start_date)));
$date2=date_create(date('Y-m-d',strtotime($end_date)));
$diff=date_diff($date1,$date2);
$differnce_days =  $diff->format("%a");

$options = array();
$options['start_date_qry']=$start_date_qry;
$options['end_date_qry']=$end_date_qry;


	if($differnce_days<=60){
		$users = $graphObj->getLoginsUsers($options,'daily');
		$total = 0;
		$month = 0;
		echo '<script>var graphData = []</script>';
		echo '<script>var graphLabel = []</script>'; 
		$i=0;
		foreach($users as $key=>$user):
			$time_spent_in_mnt = $user->total;
			$total  += $user->time_spent_in_mnt;
			$day  = $user->day;
			$week  = $user->week;
			$date  = $user->date;
			
			$month_name  = $user->month_name; 
			
			$label_name  = $day.'-'.$month_name;

				
			?>
			<script>
				graphData[<?php echo $i?>]=<?php echo $time_spent_in_mnt?>;
			</script>
			<script>
				graphLabel[<?php echo $i?>]=<?php echo "'".$label_name. "'"?>;
			</script>
			<?php
		
		$month = $user->month;
		$i++;
		endforeach;
	}

	elseif( $differnce_days>60){
		
	$users = $graphObj->getLoginsUsers($options,'monthly');
	$total = 0;
	echo '<script>var graphData = []</script>';
	echo '<script>var graphLabel = []</script>'; 
	$i=0;
	foreach($users as $key=>$user):
		$time_spent_in_mnt = $user->total;
		$total  += $time_spent_in_mnt;
		$day  = $user->day;
		$week  = $user->week;
		$date  = $user->date;
		$month_name  = $user->month_name;
		
    
		$label_name  = $month_name.'-'.$user->year_number;


		?>
		<script>
			graphData[<?php echo $i?>]=<?php echo $time_spent_in_mnt?>;
		</script>
		<script>
			graphLabel[<?php echo $i?>]=<?php echo "'".$label_name. "'"?>;
		</script>
		<?php

			$i++;
			endforeach;
			$ddate = date('Y-m-d');
			$date = new DateTime($ddate);
			$current_week = $date->format("W");

		
	}
	
	

?>


<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  
	   <h3><?php echo  $language[$_SESSION['language']]['graphical_reports'] ?>  </h3>
	  
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>

 <div class="clear"></div>

<section class="padder reportList">
<?php include_once('graph_menu.php');?>
<section class="panel panel-default">

<header class="panel-heading b-light" style="overflow: auto;">
		<form id="serachform" name="serachform"  method = "get" 
		 class="form-horizontal form-centerReg" data-validate="parsley" 
		 onSubmit="return confSubmit();" action="graph_logins_learner.php" >
	<section class="marginBottom5 serachformDiv"> 

<div class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-left paddLeft0" style="padding-top: 20px;"><strong><?php echo $language[$_SESSION['language']]['learner_logins'] ?></strong></div>
  <div class="col-xs-9 col-sm-9 col-md-10 col-lg-10 pull-right text-right padd0"> 
	<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-left paddLeft0"  style="padding-top: 10px;">
	</div>
	<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  text-right  paddLeft0 paddRight0" style="padding-top: 10px;">
		<label class="control-label"> <?php echo $language[$_SESSION['language']]['start_date'] ?> </label>
		 <div id="startDate" class="input-append date form-control input-lg" style="position: relative;width: 150px;display: inline-block;">
				<input  data-date-format="YYYY-MM-DD" readonly="true"  name="start_date" id="start_date" placeholder="YYYY-MM-DD" class=" width100per bdrNone" autocomplete="off" tabindex="4" value='<?php echo $start_date; ?>' style="width: 120px;" />
					<span class="calendarBg add-on top30"  style="top: 3px;"> <i class="fa fa-calendar"></i>
					</span>
					
			</div> 
		
		</div>
		 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left   paddRight0" style="padding-top: 10px;">
			<label class="control-label"> <?php echo $language[$_SESSION['language']]['end_date'] ?>  </label>
			<div id="endDate" class="input-append date form-control input-lg"  style="position: relative;width: 150px;display: inline-block;">
				<input  data-date-format="YYYY-MM-DD" readonly="true"  name="end_date" id="end_date" placeholder="YYYY-MM-DD" class=" width100per bdrNone" autocomplete="off" tabindex="4" value='<?php echo $end_date; ?>' style="width: 120px;" />
					<span class="calendarBg add-on top30"  style="top: 3px;"> <i class="fa fa-calendar"></i>
					</span>
					
			</div> 
			
		</div>
	<div class="pull-right text-right padd0 " style="margin-top: 9px;">
		<button type="submit" class="btn btn-red" id="btnSave" style="padding: 10px 20px;
	   margin: 0px auto;" title=" <?php echo $language[$_SESSION['language']]['search'] ?> <?php echo $language[$_SESSION['language']]['learner_logins'] ?>"> <?php echo $language[$_SESSION['language']]['search'] ?> </button>
		
		<a class="btn btn-sm btn-red btnwidth40" href="graph_logins_learner.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh'] ?>" style="padding: 10px 20px;
	   margin: 0px auto;"> <i class="fa fa-refresh"></i></a>
	 </div>  
			</div> 

</form> 
</section>
</header> 

	<div class="panel-body">
	<div class="row"> 
		
		<div class="col-sm-12 bg-white pt-1">
		<div class="row">
		<div class="col-xs-12 pb-3">
			<div class="row">
				
					<div class="col-xs-4">
						Total Logins: <span id="totalRecord"><?php echo $total?></span> 
					</div>
					
					<div class="col-xs-4">
					&nbsp;
					</div>
					
					<div class="col-xs-4 text-right">
						<span id="recordFrom"> </span> to <span id="recordTo"> </span>
						
					</div>
					
			</div>
			<div class="row">
					<div class="col-xs-12">
					&nbsp;
					</div>
					</div>
			
			
			<?php if($report_by == 'weekly'){?>
				<div class="row">
					<div class="col-xs-12">
					<div class="scoreList">
	<ul class="scoreUl">
	<?php 
	$startday = array(1=>1,2=>8,3=>15,4=>22,5=>29);
	$endday = array(1=>7,2=>14,3=>21,4=>28,5=>">28");

	?>
	<?php for($j=1;$j<6;$j++){?>
<?php if($j==5){?>
	<li><span class="circle span<?php echo $j; ?>">&nbsp;</span> <span class="text">
	Week <?php echo $j;?> ( <?php echo $endday[$j];?>)
	</span> 

	</li>
<?php }else{?>
	<li><span class="circle span<?php echo $j; ?>">&nbsp;</span> <span class="text">
	Week <?php echo $j;?> (<?php echo $startday[$j];?> - <?php echo $endday[$j];?>)
	</span> 

	</li>
	
<?php	
}}?>
	</ul>
	</div>
	</div>
	</div>
			<?php }?>
	
	<div class="clear"></div>
			
			
			
			<?php 
			//if($total)
				echo '<canvas id="userChart"></canvas>'; 
			//else 
			//	echo "<h3 class='text-center'>No Record found!</h3>"; 
			?>
			</div>
			
		</div>
		
	</div>
		</div>

	</div>


 

</section>
</section>


<?php include_once('../footer/adminFooter.php'); ?>

 <style>
.scoreList{padding: 10px;}
.scoreList ul{list-style:none;padding: 0px;
text-align: center;margin: 0px; margin-top: 10px;}

 .scoreList ul.scoreUl li {display: inline-block;
width: auto;margin-bottom: 5px;margin-right: 10px;
line-height: 10px;}
 .scoreList ul.scoreUl li span.circle{ /* width: 11px;
height: 11px;*/
width: 16px;
height: 16px;
border-radius: 100%;
display: inline-block;
border: solid #fff;}
 .scoreList ul.scoreUl li span.span1{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span2{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span3{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span4{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span5{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span6{background-color: #00799e;}
 .scoreList ul.scoreUl li span.text {
font-family: Open Sans;
font-size: 12px;
font-weight: normal;
font-stretch: normal;
font-style: normal;
line-height: normal;
letter-spacing: normal;
color: #4e4e4e;
}
 </style>
<script src="js/charts/chart/Chart.min.js"></script>
<script>
var stdate = '<?php echo $start_date; ?>';
var date = new Date();
	date.setDate(date.getDate());
	var current_date='<?php echo date("d-m-Y"); ?>';	  
	$(function () {
		$("#startDate").datepicker({
			endDate: date,
			autoclose: true, 
			todayHighlight: true,
			format: 'dd-mm-yyyy',
			

		}).on('changeDate', function (selected) {
		var startDate = new Date(selected.date.valueOf());
		$('#endDate').datepicker('setStartDate', startDate);
		}).on('clearDate', function (selected) {
			$('#endDate').datepicker('setStartDate', null);
		});
		//}).datepicker('update', new Date()); //// current date auto show
	});
	$(function () {
		
		$("#endDate").datepicker({
			endDate: date,
			startDate:stdate,
			autoclose: true, 
			todayHighlight: true,
			format: 'dd-mm-yyyy',
			

		}).on('changeDate', function (selected) {
			var endDate = new Date(selected.date.valueOf());
				$('#startDate').datepicker('setEndDate', endDate);
			}).on('clearDate', function (selected) {
				$('#startDate').datepicker('setEndDate', date);
			});
		//}).datepicker('update', new Date()); //// current date auto show
	});	


   
      
 </script> 

<script>
			console.log(graphLabel);
			console.log(graphData);

	</script>
<script type="text/javascript">
$('#recordFrom').text('<?php echo $datetoshow1;?>');
$('#recordTo').text('<?php echo $datetoshow2;?>');


var totalRecord = 0;
for(x = 0; x<graphData.length;x++){
totalRecord+= graphData[x] != undefined ?graphData[x]:0;
}
jQuery('#totalRecord').text(totalRecord);
var ctx = document.getElementById("userChart");
var myChart = new Chart(ctx, {
type: 'bar',
responsive: true,
data: {
labels: graphLabel,
datasets: [{
data: graphData,
backgroundColor: "#086a80"
}]
},
options: {
	layout: {
            padding: {
                left: 20,
                right: 0,
                top: 15,
                bottom: 0
            }
        },
events: [],
"hover": {
	 mode: false
},
"animation": {
"duration": 1,
"onComplete": function () {
var chartInstance = this.chart,
ctx = chartInstance.ctx;

ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
ctx.textAlign = 'center';
ctx.textBaseline = 'bottom';



this.data.datasets.forEach(function (dataset, i) {
var meta = chartInstance.controller.getDatasetMeta(i);
meta.data.forEach(function (bar, index) {
var data = dataset.data[index];
ctx.fillText(data, bar._model.x, bar._model.y - 1);
});
});
}
},
legend: {
"display": false 

},
tooltips: {
"enabled": true
},
scales: {
yAxes: [{
display: true,
offset: true, 
gridLines: {
display : false
},ticks: {
        beginAtZero: true,
		display: true,
		precision: 0,
		  userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }
		  }
      }
}],
xAxes: [{
offset: true, 
gridLines: {
display : false
},
ticks: {
beginAtZero:true
}
}]
}
}
}); 

</script>

	
