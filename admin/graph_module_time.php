<?php 
include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

$reportObj = new reportController();
$graphObj = new graphController();
$country_list_arr=$reportObj->getCountryList();
$options = array();
$options['client_id'] = $client_id;

$options['role_id'] = isset($_GET['role']) ? $_GET['role'] :'';

$options['centre'] = isset($_GET['centre']) ? $_GET['centre'] :'';

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? $_GET['sort'] : 'u1.created_date';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? $_GET['dir'] : 'ASC';

$centres = $reportObj->getCentresList();




function getLastLogin($datetime, $full = false) {
	if($datetime == "")
		return '-';
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}




switch(strtoupper($dir)){
	case 'DESC': 
		$dir = 'ASC'; 
		break;
	case 'ASC': 
		$dir = 'DESC'; 
		break;
	default: 
		$dir = 'DESC'; 
		break;
}


$page_param='';

$page_param .= "sort=".$_GET['sort']."&dir=".$_GET['dir']."&";


$center_id='';
$country='';
$batch_id='';

$rid = isset($_SESSION['region_id'])?$_SESSION['region_id']:'';


if (!empty($_REQUEST['region_id'])) {
    $region_id = trim($_REQUEST['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}else{
	$region_id = $rid;
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}
if (!empty($_REQUEST['date'])) {
    $selectedDate = trim($_REQUEST['date']);
	$options['date'] = $selectedDate;
	$page_param .= "date=$selectedDate&";
}else{
	$selectedDate = date('Y-m-d');
	$options['date'] = $selectedDate;
}

$centres = $reportObj->getCentresList($rid);
$graphRowData = $graphObj->getModuleTime($options);

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
	<div class="tab-content">
	<div id="insReport" class="tab-pane fade in active">
	 <?php // if(count($all_center_list_arr) > 0){?>
		<form id="serachform" name="serachform" method="GET" 
		 class="form-horizontal form-centerReg"
		  action="graph_module_time.php" >
	<section class="marginBottom5 serachformDiv <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>" >
		 <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
   		
		 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-6 text-left paddLeft0">
		
		 <select name="region_id" id="region_id" class="form-control " onchange="setState(this)">
				
				<?php 
				if(count($centres)>1)
					echo '<option value="" selected disabled>'.$language[$_SESSION['language']]['select_centre'].'</option>';
				 foreach ($centres  as $key => $value) {	
				  	$centre = $value->centre;
					  $id = $value->id;
					  if($id == $region_id)
						  echo "<option value='$id' selected>$centre</option>";
					  else  
					      echo "<option value='$id'>$centre</option>";
				   } 
				   ?>
			   </select>
			
		</div>

		


				   
				
		</div>
			


			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit"  class="btn btn-red" id="btnSave" title=" <?php echo $language[$_SESSION['language']]['search'] ?> <?php echo $language[$_SESSION['language']]['time_spent'] ?>"
				 style="margin-top:0px"> 
				 <?php echo $language[$_SESSION['language']]['search']; ?></button>
				
				<a class="btn btn-sm btn-red btnwidth40" href="graph_module_time.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?> " style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			</div>
			
			</form>
			 <!-- <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>-->
	</section>	
   <div class="clear"></div>	
   <?php  //}?>

	 
  
       <section class="panel panel-default">
	    <div class="panel-body">
		
		<div class="scoreList">
			<ul class="scoreUl">
		<li><span class="circle span1">&nbsp;</span> <span class="text">
		 Time Spent (in minutes)
			</span> 
		</li>
		
			</ul>
		</div>

			<?php if(!count($graphRowData))
				echo '<div class="col-xs-12 noRecord text-center">Records not available.</div>';
			?>

		


	     <div class="table-responsive">
		 <div style="height: 400px">
		<canvas id="stateChart" width="400" height="300" style="height:300px"></canvas>
				   </div>
		    <?php
			
		echo '<script>var tickslabel = []</script>'; 
		echo '<script>var graphData = []</script>';  
		

			 foreach($graphRowData  as $key => $value){
				
			$time = isset($value['total'])? floor($value['total']/60) : 0;
			?>
				<script>
			var lavel = 'M'+<?php echo $key+1?>;
			tickslabel.push(lavel);
			graphData.push(<?php echo $time;?>);
			
				</script>
			<?php 
			  
			   
			}
			   
			 ?>

			 
			
				
			</div>


					

		   </div>
		 </section>
	   </div>

	  </div>
</section>
<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>
<?php include_once('../footer/adminFooter.php'); ?>
<script>
//On region chnage
 $('#region').change(function(){
	var region = $('#region option:selected').val();
	$('#center_id').html('<option value="">Select Organization</option>');
	if(region==''){
			$('#country').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getCountryByRegion.php', {region_id: region}, function(data){ 
			if(data!=''){
				  $('#country').html(data);
				}else{
					$('#country').html('<option value="">Not Available</option>');
				}
		}); }
}); 
//On country chnage
 $('#country').change(function(){
	
	var country = $('#country option:selected').val();
	if(country==''){
			$('#center_id').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getCenterByCountry.php', {country: country}, function(data){ 
			if(data!=''){
				  $('#center_id').html(data);
				}else{
					$('#center_id').html('<option value="">Not Available</option>');
				}
		}); }
}); 

//On center chnage
 $('#center_id').change(function(){
	
	var center_id = $('#center_id option:selected').val();
	if(center_id==''){
			$('#batch_id').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getBatchByCenter.php', {center_id: center_id}, function(data){ 
			if(data!=''){
				  $('#batch_id').html(data);
				}else{
					$('#batch_id').html('<option value="">Not Available</option>');
				}
		}); }
});

</script> 
<script>

     /*  $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'learners_report.php?report_type=export';
                var country = $("#country").val();
                var center_id = $("#center_id").val();
                var batch_id = $("#batch_id").val();
                url += '&center_id='+center_id;
                url += '&country='+country;
                url += '&batch_id='+batch_id;

                 location.href = url;
                
            });
            
        });*/
      
 </script> 



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
 .scoreList ul.scoreUl li span.span1{background-color: #086a80;}
 .scoreList ul.scoreUl li span.span2{background-color: #CD1604;}
 .scoreList ul.scoreUl li span.span3{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span4{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span5{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span6{background-color: #4b7fbb;}
 .scoreList ul.scoreUl li span.span7{background-color: #99ba55;}
 .scoreList ul.scoreUl li span.span8{background-color: #ec859a;}
 .scoreList ul.scoreUl li span.span9{background-color: #be4b48;}
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

.axisLabel {
    position: absolute;
    text-align: center;
    font-size: 12px;
}

.xaxisLabel {
    bottom: 3px;
    left: 0;
    right: 0;
}

.y1Label { 
        fill: #772211;
        font-size: 18px;
    }
 </style>
 

<script src="./js/sb-report-script.js"></script>

<script src="js/charts/chart/Chart.min.js"></script>
<script src="js/charts/flot/jquery.flot.min.js"></script>
  <script src="js/charts/flot/jquery.flot.tooltip.min.js"></script>
  <!--<script src="js/charts/flot/jquery.flot.resize.js"></script>-->
  <script src="js/charts/flot/jquery.flot.orderBars.js"></script>
  <script src="js/charts/flot/jquery.flot.pie.min.js"></script>
  <script src="js/charts/flot/jquery.flot.grow.js"></script>
  <script src="js/charts/flot/jquery.flot.time.js"></script>
<script>
$(function(){

var colors = ['#4b7fbb','#ec859a','#be4b48'];





var ctx = document.getElementById("stateChart");
ctx.height = 500;

var myChart = new Chart(ctx, {
type: 'bar',
responsive: true,
data: {
labels: tickslabel,
datasets: [{
data: graphData,
backgroundColor: "#086a80"
}
]
},
options: {
	scaleShowValues: true,
	maintainAspectRatio: false,
	
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
      ticks: {
        beginAtZero: true
      },gridLines: {
		display : false
		}
    }],
    xAxes: [{
      ticks: {
        autoSkip: false
      },gridLines: {
		display : false
		}
    }]
  }
}


}

); 

});


	</script>
