<?php 
error_reporting(1);
ini_set('display_errors',1);
include_once ('../../header/lib.php'); 
$assessmentObj = new assessmentController();
$batteryObj = new batteryController();
$userObj = new userController();
$_html_relative_path='../../';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
$clientUserId=$assessmentObj->getSuperClientId($_SESSION['user_group_id']);
$course_arr=$assessmentObj->getCourseByClientId($clientUserId); 
$battery_arr1=$assessmentObj->getBatteryByClientId($_SESSION['client_id']); 

?>
<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title>Summary Report</title>
  <meta name="description" content="" />
    <link rel="shortcut icon" href="<?php echo $_html_relative_path; ?>images/favicon.ico" type="image/vnd.microsoft.icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/animate.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/font-awesome.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/font.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/app.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/common1.css" type="text/css" />
  <!--css theme -->
  <?php include_once($_html_relative_path.'css/theme.php');?>
  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
   <script src="<?php echo $_html_relative_path;?>js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="<?php echo $_html_relative_path;?>js/bootstrap.js"></script>
<body class="overFlowHidden">
	<div class="submitPopup" id="loaderDiv" >
   <div class="overlay"></div><div class="loaderImageDiv"></div>
</div>


  <section class="vbox">
    <header class="bg-dark dk header navbar navbar-fixed-top-xs">
      <div class="navbar-header">
        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
          <i class="fa fa-bars"></i>
        </a>
        <a class="navbar-brand cursorDeafult"><img src="<?php echo $_html_relative_path.applogo; ?>" class="m-r-sm">Summary Report</a>
        <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
          <i class="fa fa-cog"></i>
        </a>
      </div>
    </header>
    <section class="container padd15">
      <section class="hbox stretch">
<div class="col-sm-12">  
	
		
		 
		  <header class="panel-heading font-bold b-light" style="overflow: auto;">
		<div class="col-md-4 padd0">  </div>
		  <div class="col-md-8 text-right">
			 <form role="form" method = "POST"  id="centerForm" class="form-horizontal form-centerReg" data-validate="parsley" >
			 <label class="lineHeight30 paddRight10">Select <?php echo $center;?> : </label>
			  
				 <select id="center" name="center" onchange="selectCenter(this);" class="form-control pull-right" style="width:200px;">
				 <option value="">All</option>
                 <?php 
					 foreach ($centers_arr as $key => $value) {	
						$centerName= $centers_arr[$key]['name'];
						$centerId= $centers_arr[$key]['center_id'];
						//$firstCenterId=(isset($firstCenterId))?$firstCenterId:$centerId;
					  ?>
                      <option <?php //if($centerId==$firstCenterId){ echo 'selected';}?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>
                    <?php } ?>
						</select>
				</form>
			 </div>
		 </header> 
	
	
	
	<div class="clear"></div>
	
<section class="panel panel-default ">
	<div class="panel-body">
    <div class="table-responsive">
	<?php //$tests=getTests();
	if($centers_arr){?>
		  <table class="table table-border dataTable table-fixed">
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2 text-left">Sr. No.</th>
			  <th class="col-sm-3 text-left"><?php echo $test;?> Name</th>
			  <th class="col-sm-2 text-center">Attempted By</th>
			  <th class="col-sm-2 text-center">Average score</th>
			  <th class="col-sm-2 text-center">Last Attempted</th>
			  </tr>
			</thead>
		  
<tbody id="tbodyRecord">
<?php 
//$tests=getTests();
$arr= array();
$con = createConnection();



	$i=1;
//echo "<pre>";print_r($userids);
foreach($course_arr as $key=>$val){
	$testList=$assessmentObj->getTopicOrAssessmentByCourseId($val['course_id']);	
	
	foreach($testList as $testKey=>$testVal){
	$userCnt = 0;
	$per='-';
	$fld_datetime = '-';
	 $test_id=$testVal->edge_id;
	$test_name=$testVal->name;	
    //echo "<pre>";print_r($test_id);
	

		$sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT user_id FROM `tbl_test_complete_status` WHERE test_id =?  and (battery_id='' or battery_id IS NULL)) AS DerivedTableAlias";
		 //echo "<pre>";print_r($sql);
		
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$test_id);
		$stmt->execute();
		$stmt->bind_result($ttl_attempted);
		while($stmt->fetch()) {
			
			 if($ttl_attempted!=""){
				 $userCnt=$ttl_attempted;
			}
			else{$fld_datetime='-';}
			
		}
        $stmt->close();
	
		$quesCount= $userObj->getTestQuesCount($test_id);
		$quesCount=$quesCount['qCount'];
		
		
		$stmt = $con->prepare("SELECT SUM(`correct`) as ttlCorrect FROM temp_ans_push where  test_id = ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("i",$test_id);
		$stmt->execute();
		$stmt->bind_result($ttlCorrect);
		while($stmt->fetch()) {
		
		 if($ttlCorrect!=""){
			 $ttlCorrect=$ttlCorrect;
		}
		else{$ttlCorrect=0;}
		}
		$stmt->close();
		
		  
		if($userCnt!=0){
		$ttlqCount=$quesCount*$userCnt;
		}
		else{$ttlqCount=$quesCount;}
		
		
		
		if($ttlqCount>0){
			$per =round(($ttlCorrect*100)/$ttlqCount);
			$per = $per.'%';
		}
		else{
			$per ='-';
		}
		 
		$stmt = $con->prepare("SELECT MAX(attempt_date) FROM tbl_test_complete_status where test_id = ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("i",$test_id);
		$stmt->execute();
		$stmt->bind_result($maxDate);
		
		while($stmt->fetch()) {
		
		 if($maxDate!=""){
			 $fld_datetime= date('d-m-Y',strtotime($maxDate));
		}
		else{$fld_datetime='-';}
		}
		$stmt->close();
		
	 
		
?>
<tr class="col-sm-12 padd0" >
				
				  <td class="col-sm-2 text-left"> <?php echo $i;?> <?php ///echo $test_id;?></td>
				  <td class="col-sm-3 text-left"><?php echo $test_name;?></td>
				  <td class="col-sm-2 text-center"> <?php echo $userCnt;?></td>
				  <td class="col-sm-2 text-center"><?php echo $per;?></td>
				  <td class="col-sm-2 text-center"> <?php echo $fld_datetime;?></td>
				
				 </tr>				 

<?php 

	$i++;
	}
   }
 
if(count($battery_arr1 ) > 0 && !empty($battery_arr1)){
$userCnt = 0;
$per='-';
$fld_datetime = '-';
					
foreach($battery_arr1  as $key => $batteryValue){
$battery_arr = $batteryObj->getBatteryById($batteryValue,$_SESSION['client_id']);

$battery_id=  $battery_arr['id'];
$batteryname= $battery_arr['battery_name'];
 

$sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT user_id FROM `tbl_test_complete_status` WHERE battery_id =? group by user_id) AS DerivedTableAlias";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $battery_id);
        $stmt->execute();
        $stmt->bind_result($ttl_attempted);
        while($stmt->fetch()) {
			 $userCnt=$ttl_attempted;
		}



	$stmt = $con->prepare("SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,MAX(fld_datetime) FROM temp_ans_push WHERE battery_id = ? and (battery_id!='' and battery_id IS NOT NULL)");
    $stmt->bind_param("i",$battery_id);
    $stmt->execute();
    $stmt->bind_result($qCount,$ttlCorrect,$fld_datetime);
	$arr_tests = array();
	
	while($stmt->fetch()) {
		
		if($fld_datetime!=""){
			$fld_datetime = date('d-m-Y',strtotime($fld_datetime));
		}
		else{$fld_datetime='-';}
		
		if(!empty($qCount)){
			$per =round(($ttlCorrect*100)/$qCount);
			$per = $per.'%';
		}
		else{
			$per ='-';
		}
		?>
		
		<?php
	}
	


?>

 	<tr class="col-sm-12 padd0" >
				
				  <td class="col-sm-2 text-left"> <?php echo $i;?></td>
				  <td class="col-sm-3 text-left"><?php echo $batteryname;?></td>
				  <td class="col-sm-2 text-center"> <?php echo $userCnt;?></td>
				  <td class="col-sm-2 text-center"><?php echo $per;?> </td>
				  <td class="col-sm-2 text-center"> <?php echo $fld_datetime;?></td>
				
				 </tr>
 <?php  
 $i++;
          }
       }
							
     	

?>
</tbody>
</table>
<?php }else{
?>
<div class="col-sm-12 noRecord text-center">Records not available.</div>
	
<?php }
?>	
	
	</div>
	</div>
	
</section>
</div>	


<?php include('../../footer/playerFooter.php'); ?>

<script>

function selectCenter(e){
	showLoader();
	$("#tbodyRecord").html('');
	var html='';
	var j;
	$.ajax({
      type: 'POST',
      url: "getReport.php",
	data: {centerId:e.value},
      dataType: "text",
      success: function(res) { 
	  
	 // console.log(res);
	 
		var data = JSON.parse(res);
		 console.log(data);
		 var myArray=data.result;
		 if(myArray.length>0){
		 for(let i = 0; i < myArray.length; i++){
			j=i+1;
			html=html+'<tr class="col-sm-12 padd0" ><td class="col-sm-2 text-left">'+j+'</td><td class="col-sm-3 text-left">'+myArray[i].test_name+'</td><td class="col-sm-2 text-center"> '+myArray[i].ttl_attempted+'</td><td class="col-sm-2 text-center">'+myArray[i].avg_score+' </td><td class="col-sm-2 text-center">'+myArray[i].last_atmpt+'</td></tr>';

			}
		 }
		 else{
			html=html+'<tr class="col-sm-12 padd0" ><td class="col-sm-11 text-center"> Records not available. </td></tr>';
		 }
		 $("#tbodyRecord").append(html);
		
	   hideLoader();
	  }
  });

}
var e='';
//selectCenter(e);

</script>
