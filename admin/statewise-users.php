<?php include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();
$centerObj = new centerController(); 
$graphObj = new graphController(); 
$options = array();
$options['client_id'] = $client_id;

$options['role_id'] = isset($_GET['role']) ? $_GET['role'] :'';


$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? $_GET['sort'] : 'name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? $_GET['dir'] : 'ASC';
$dirQry = (isset($_GET['dir']) && $_GET['dir'] !="") ? $_GET['dir'] : 'ASC';

switch(strtoupper($dir)){
	case 'DESC': 
		$dir = 'ASC'; 
		break;
	case 'ASC': 
		$dir = 'DESC'; 
		break;
	default: 
		$dir = 'ASC'; 
		break;
}




$country_list_arr=$reportObj->getCountryList();

$rid = isset($_SESSION['region_id'])?$_SESSION['region_id']:'';

$page_param='';
$page_param .= "sort=".$_GET['sort']."&dir=".$_GET['dir']."&";
$center_id='';
$country='';

if (!empty($_REQUEST['region_id'])) {
    $region_id = trim($_REQUEST['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}else{
	$region_id = $rid;
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}








$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

//$_limit = 50;
$_limit = 50;
$objPage = new Pagination($_page, $_limit);

$centres = $reportObj->getCentresList($rid);

if(isset($_REQUEST['role']) && $_REQUEST['role']==7){
$response_result= $graphObj->getStateCentralAdmins($options);
}else{

$response_result= $graphObj->getStateUsers($options);
}


$objPage->_total = $response_result['total'];
$center_list_arr = $response_result['result'];



?>

<style type="text/css">.tree-btn{
	background: none;
    border: none;
    font-size: 19px;
    color: #047a9c;
    font-weight: 700;
}</style>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <h3><?php echo  $language[$_SESSION['language']]['graphical_reports'] ?>  </h3>
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
	<?php 
	include_once('graph_menu.php');
	?>
	  <div class="tab-content"> 
	  <div id="insReport" class="tab-pane fade in active">

    <form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="statewise-users.php" >
	<section class="marginBottom5 serachformDiv" >
		 <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
   		
		 <div class="col-xs-4 col-sm-3 col-md-6 col-lg-6 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>">
		 
				
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

		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-4 text-left paddLeft0">
				
		<select name="role" id="role" class="form-control" >
			<?php  $optiondisabled = ($options['role_id'] == 'All') ? "disabled" : ""; ?>
			   <option value=""  <?php  echo $optiondisabled ;?>><?php echo $language[$_SESSION['language']]['user_type']; ?> </option>
							<?php
					$optionSelected = ($options['role_id'] == 'All') ? "selected" : "";
						  echo '<option value="All" '.$optionSelected.'>All</option>';
			   ?>		
						<option class="usertype_options" value="7" <?php   echo $options['role_id'] == 7?'selected':'' ?> > 
						<?php echo 'CEO'; ?> </option>
					<option class="usertype_options" value="4" <?php   echo $options['role_id'] == 4?'selected':'' ?> ><?php echo $language[$_SESSION['language']]['state_admins']; ?> </option>
					<option class="usertype_options" value="1" <?php   echo $options['role_id'] == 1?'selected':'' ?> ><?php echo $language[$_SESSION['language']]['district_admins']; ?></option>
					<option class="usertype_options" value="2" <?php echo  $options['role_id'] == 2?'selected':'' ?> ><?php echo $language[$_SESSION['language']]['learners']; ?></option>
				
				
				</select>
				</div>




				   
				
		</div>
			


		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0 text-right paddRight0">
			<button type="submit"  class="btn btn-red" id="btnSave" style="margin-top:0px" title=" <?php echo $language[$_SESSION['language']]['search'] ?> <?php echo $language[$_SESSION['language']]['statewise_users'] ?>"> 
			<?php echo $language[$_SESSION['language']]['search']; ?></button> 
		

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
	     <div class="table-responsive">
		 
<?php

echo '<script>var tickslabel = []</script>'; 
echo '<script>var graphData = []</script>';  
 foreach($center_list_arr  as $key => $value){
				
				
			?>
			<script>
			var lavel = '<?php echo $value['name'] ?>';
			tickslabel.push(lavel);
			graphData.push(<?php echo $value['users'];?>);
				</script>
			
			<?php 
			   }
			   
			 ?>
			   
			
<?php  if(!count($center_list_arr)) {			   
		echo '<div class="col-xs-12 noRecord text-center">Records not available.</div>';
}else{
		?>

<hr>


			
			<canvas id="stateChart" width="400" height="300"></canvas>
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
.highlight{
 background:#0085a21a;
}
</style>
<?php include_once('../footer/adminFooter.php'); ?>
<script>
//On region chnage
 $('#region').change(function(){
	var region = $('#region option:selected').val();
	$('#center_id').html('<option value="">Select <?php echo $center;?></option>');
	
	if(region==''){
			$('#country').find('option').remove().end().append('<option value="">Select Country</option>');
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
			$('#center_id').find('option').remove().end().append('<option value="">Select <?php echo $center;?></option>');
		}else{
	$.post('ajax/getCenterByCountry.php', {country: country}, function(data){ 
			if(data!=''){
				  $('#center_id').html(data);
				}else{
					$('#center_id').html('<option value="">Not Available</option>');
				}
		}); }
}); 




</script>
 <script>

      /*  $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'reports.php?report_type=export';
                var country = $("#country").val();
                var center_id = $("#center_id").val();
                //var batch_id = $("#batch_id").val();
                url += '&center_id='+center_id;
                url += '&country='+country;
               // url += '&batch_id='+batch_id;

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
 .scoreList ul.scoreUl li span.span1{background-color: #00799e;}
 .scoreList ul.scoreUl li span.span2{background-color: #00799e;}
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
var myChart = new Chart(ctx, {
type: 'bar',
responsive: true,
data: {
labels: tickslabel,
datasets: [{
data: graphData,
backgroundColor: "#086a80"
}]
},
/*
options: {
  scaleShowValues: true,
  scales: {
    yAxes: [{
      ticks: {
        beginAtZero: true
      }
    }],
    xAxes: [{
      ticks: {
        autoSkip: false
      }
    }]
  }
}*/

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
        beginAtZero: true,
		  userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }
		  }
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

});	</script>


