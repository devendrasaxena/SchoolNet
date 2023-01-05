<?php 
include_once ('../header/lib.php'); 
include_once ('../header/global.php'); 
$_html_relative_path='../';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
$centerDetail=$adminObj->getCenterDetails();
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];


$uid=trim($_REQUEST['userId']);
?>
<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title></title>
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
	<div id="preLoaderPage" class="preloadBg" >
		<div id="overlayBlur"></div>
		<div class="loadDiv">
			<img src="<?php echo $_html_relative_path;?>images/default.svg" class="loadImg text-center"/>
			<div class="loadText">Please wait<span>.</span><span>.</span><span>.</span>
			</div>
		</div>
	</div>
	<div id="loaderDiv" class="loadBg">
	  <div id="overlayBlur"></div>
	  <div class="loadDiv"> <img src="<?php echo $_html_relative_path;?>images/default.svg" class="loadImg text-center"/>
		<div class="loadText">Please wait<span>.</span><span>.</span><span>.</span> </div>
	  </div>
	</div>
<ul class="breadcrumb no-border no-radius b-b b-light pull-in">
	<li> Student Progress</li>
</ul>
<div class="clear"></div> 
<section class="panel panel-default marginBottom5">
	<div id="loaderDiv" class="loadBg">
	  <div id="overlayBlur"></div>
	  <div class="loadDiv"> <img src="<?php echo $_html_relative_path; ?>images/default.svg" class="loadImg text-center"/>
		<div class="loadText">Please wait<span>.</span><span>.</span><span>.</span> </div>
	  </div>
	</div>
  <div class="panel-body marginBottom5">	
	<div class="col-sm-12 col-md-12 bdr padd0">  
    <header class="panel-heading font-bold">Student <?php echo $tests;?></header>
	  <div class="panel-body padd0 marginBottom5">	
	  <div class="table-responsive" style="min-height:60px;height:160px;overflow:auto;">
	  <?php	$testAttempted = getUserCompletedTest($uid,'1');
	  
	  if($testAttempted){?>
	 <table class="table table-border dataTable table-fixed">
		<thead  class="fixedHeader">
			 <tr class="col-sm-12 padd0">
			 <th class="col-sm-3"><?php echo $test;?> Name</th>
			 <th class="col-sm-3 text-center">Score</th>
			 <th class="col-sm-2 text-center">Percentage</th>
			 <th class="col-sm-3 text-center">Date</th>
			</tr>
		 </thead>
		 <tbody>    
			<?php 
				 $date = date( 'Y-m-d');
				 $graphPer=array();
				 $graphXAxixData=array();
				 $testScoreComparisionArr=array();
				 $firstKey = key($testAttempted);
				 $studentData = $adminObj->getUserDataByID($uid, 2);
				 $course_ids=$studentData->course_id;
				 $course_id_arr=explode(',',$course_ids);
				 $course_id_arr=array_filter($course_id_arr);
				 $course_count=count($course_id_arr);
				 foreach($testAttempted  as $key=>$testAttempt)
				 {
					 
					
					 
					$batt_id=$testAttempt->battery_id;
					if($batt_id!=""){
					$battery_arr = $batteryObj->getBatteryById($batt_id,$client_id);
					$batteryname= $battery_arr['battery_name'];
					$userResult= getBatteryResult($batt_id,$uid,$course_count);
					$userResult=$userResult[0];
					$cumScorearr=$userResult;
					$fld_datetime=(!empty($userResult->fld_datetime))?date('d-m-Y',strtotime($userResult->fld_datetime)):'-';
					$myPer=round(($userResult->ttlCorrect*100)/$userResult->qCount);
					
					//Getting batch data
					$studentData = $adminObj->getUserDataByID($uid, 2);
					$btch= $studentData->batch_id;
					$batch_id=$btch[0]['batch_id'];
					$resBatchPer=$userObj->getBatchPer2($batt_id,$batch_id,$_SESSION['center_id'],1);
					$batchPer=round(($resBatchPer['ttlCorrect']*100)/$resBatchPer['qCount']);
					
					$resCustomerPer=$userObj->getCustomerPer2($batt_id,$_SESSION['center_id'],'1');
					$customerPer=round(($resCustomerPer['ttlCorrect']*100)/$resCustomerPer['qCount']);
					
					$resTestAvgPer=$userObj->getTestAvgPer($batt_id,'1');
					$testAvgPer=round(($resTestAvgPer['ttlCorrect']*100)/$resTestAvgPer['qCount']);
					
					
					$testBatchRank=$userObj->getBatchRank($batt_id,$uid,$batch_id,$_SESSION['center_id'],'1');
					
					
					$testCustomerRank=$userObj->getCustomerRank($batt_id,$uid,$_SESSION['center_id'],'1');
			
					 $testOverAllRank=$userObj->getOverAllRank($batt_id,$uid,$_SESSION['client_id'],'1');
					 
					 $testBatchRankPercentile=$userObj->getBatchRankPercentile($batt_id,$uid,$batch_id,$_SESSION['center_id'],'1');
					 
					 $testCustomerRankPercentile=$userObj->getCustomerRankPercentile($batt_id,$uid,$_SESSION['center_id'],'1');
					
					$testOverAllRankPercentile=$userObj->getOverAllRankPercentile($batt_id,$uid,$_SESSION['client_id'],'1');
				
					 ?>
						<tr class="col-sm-12 padd0">
						<td class="col-sm-3 text-left" > 
						<input type="radio" <?php  if($firstKey==$key){?> checked <?php } ?> name="rdo1" value="<?php echo $batt_id;?>" onclick="showHide(<?php echo $testAttempt->test_id;?>,<?php echo $batt_id;?>)"/> <?php echo $batteryname;?> </td>
						<td class="col-sm-3 text-center "><?php echo $userResult->ttlCorrect.'/'.$userResult->qCount;?></td> 
						<td class="col-sm-2 text-center "><?php echo $myPer.'%';?></td> 	
						<td class="col-sm-3 text-center "> <?php echo $fld_datetime;?></td> 			
						</tr>
			   <?php
					 
					}
					else{
						
					$testInfo = getAssessmentNameByEdgeId($testAttempt->test_id);
					$userResult= getTestResult($testAttempt->test_id,$uid);
					$userResult=$userResult[0];
					$fld_datetime=date('d-m-Y',strtotime($userResult->fld_datetime));
					$myPer=round(($userResult->ttlCorrect*100)/$userResult->qCount);
					
					//Getting batch data
					$studentData = $adminObj->getUserDataByID($uid, 2);
					$btch= $studentData->batch_id;
					$batch_id=$btch[0]['batch_id'];
					$resBatchPer=$userObj->getBatchPer2($testAttempt->test_id,$batch_id,$_SESSION['center_id']);
					$batchPer=round(($resBatchPer['ttlCorrect']*100)/$resBatchPer['qCount']);
					
					$resCustomerPer=$userObj->getCustomerPer2($testAttempt->test_id,$_SESSION['center_id']);
					$customerPer=round(($resCustomerPer['ttlCorrect']*100)/$resCustomerPer['qCount']);
					
					$resTestAvgPer=$userObj->getTestAvgPer($testAttempt->test_id);
					$testAvgPer=round(($resTestAvgPer['ttlCorrect']*100)/$resTestAvgPer['qCount']);
					
					
					$testBatchRank=$userObj->getBatchRank($testAttempt->test_id,$uid,$batch_id,$_SESSION['center_id']);
					
					$testCustomerRank=$userObj->getCustomerRank($testAttempt->test_id,$uid,$_SESSION['center_id']);	
					
			

					 $testOverAllRank=$userObj->getOverAllRank($testAttempt->test_id,$uid,$_SESSION['client_id']);
					 
					 $testBatchRankPercentile=$userObj->getBatchRankPercentile($testAttempt->test_id,$uid,$batch_id,$_SESSION['center_id']);
					 
					 $testCustomerRankPercentile=$userObj->getCustomerRankPercentile($testAttempt->test_id,$uid,$_SESSION['center_id']);
					 $testOverAllRankPercentile=$userObj->getOverAllRankPercentile($testAttempt->test_id,$uid,$_SESSION['client_id']);
					 ?>
						<tr class="col-sm-12 padd0">
						<td class="col-sm-3 text-left" > <input type="radio" <?php  if($firstKey==$key){?> checked <?php } ?> name="rdo1" value="<?php echo $testAttempt->test_id;?>" onclick="showHide(<?php echo $testAttempt->test_id;?>,<?php echo $batt_id;?>)"/> <?php echo $testInfo->name;?> </td>
						<td class="col-sm-3 text-center "><?php echo $userResult->ttlCorrect.'/'.$userResult->qCount;?></td> 
						<td class="col-sm-2 text-center "><?php echo floor($myPer).'%';?></td> 	
						<td class="col-sm-3 text-center "> <?php echo $fld_datetime;?></td> 			
						</tr>

				<?php

					}
					
					$obj = new stdClass();
					$obj->testId = $testAttempt->test_id;
					$obj->battery_id = $testAttempt->battery_id;
					$obj->myPer = $myPer;
					$obj->batchPer = $batchPer;
					$obj->customerPer = $customerPer;
					$obj->testAvgPer = $testAvgPer;
					$obj->testBatchRank = $testBatchRank;
					$obj->testCustomerRank = $testCustomerRank;
					$obj->testOverAllRank = $testOverAllRank;
					$obj->testBatchRankPercentile = $testBatchRankPercentile;
					$obj->testCustomerRankPercentile = $testCustomerRankPercentile;
					$obj->testOverAllRankPercentile = $testOverAllRankPercentile;
					array_push($testScoreComparisionArr,$obj);
					
					
					
					
					?>
					
					
				
					 
				<?php }
				 
                  ?>
			 
		</tbody>
	</table>
  <?php }else{ ?>
			<div class="col-xs-12  text-center" style="min-height:60px;height:140px;line-height:100px;">Records not available. <br>
			</div>
		<?php }?>
	</div>
	 </div>
   </div>
  </div>
</section>
<div class="clear"></div> 

<?php //echo '<pre>';print_r($testScoreComparisionArr);?>
 <div class="col-md-6 col-sm-12 padd0 paddRight5 marginBottom40">  
 <section class="panel panel-default padd0 marginBottom5">
  <header class="panel-heading font-bold">Score Comparision <small></small></header>
	<div class="panel-body">
	<?php if(count($testScoreComparisionArr)>0){
			$firstKey = key($testScoreComparisionArr);  // fetches the key of the element  ?>
		<?php 
		foreach($testScoreComparisionArr  as $key=>$val){
		
			//Set Display property of Div

			if($firstKey==$key){ $style="";}else{$style="display:none";}
				?>
		
		<table class="table dataTable table-fixed myprogress marginBottom0 tblScoreComp" id="tblScoreComp-<?php echo $val->testId;?>-<?php echo $val->battery_id;?>" style="<?php echo $style;?>">
			 <tr class="col-sm-12 padd0">
			 <td class="col-sm-4">Student Percentage </td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                 <div class="progress-bar progress-bar-danger" data-toggle="tooltip" data-original-title="<?php echo $val->myPer;?>%" style="width: <?php echo $val->myPer;?>%"></div> 
                </div></td>
			<td class="col-sm-1"><?php echo $val->myPer;?>%</td>
			</tr>
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-4">Batch Percentage</td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                  <div class="progress-bar progress-bar-info" data-toggle="tooltip" data-original-title="<?php echo $val->batchPer;?>%" style="width: <?php echo $val->batchPer;?>%"></div>
                 </div></td>
			<td class="col-sm-1"><?php echo $val->batchPer;?>%</td>
			</tr>
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-4"><?php echo $center; ?> Percentage</td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                  <div class="progress-bar progress-bar-warning" data-toggle="tooltip" data-original-title="<?php echo $val->customerPer;?>%" style="width: <?php echo $val->customerPer;?>%"></div>
              </div></td>
			<td class="col-sm-1"><?php echo $val->customerPer;?>%</td>
			</tr>
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-4"><?php echo $test; ?> Avg. %</td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                 <div class="progress-bar progress-bar-success" data-toggle="tooltip" data-original-title="<?php echo $val->testAvgPer;?>%" style="width: <?php echo $val->testAvgPer;?>%"></div>
              </div></td>
			<td class="col-sm-1"><?php echo $val->testAvgPer;?>%</td>
			</tr>
		 </table>
	<?php } } else{?>
		
		
		<table class="table dataTable table-fixed myprogress marginBottom0 tblScoreComp" >
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-12 text-center padd0" colspan='3'>Records not available.</td>
			</tr>
		</table>
	
	<?php } ?>
	</div>  
</section> 
</div> 

<div class="col-md-6 col-sm-12 padd0 paddLeft5 marginBottom40"> 		
 <section class="panel panel-default padd0 marginBottom5">
<header class="panel-heading font-bold">Rank Comparison <small>(in percentile)</small></header>
	<div class="panel-body">
	 	<?php if(count($testScoreComparisionArr)>0){
			 $firstKey = key($testScoreComparisionArr);  // fetches the key of the element  ?>
		<?php foreach($testScoreComparisionArr  as $key=>$val){
		
				//Set Display property of Div
				if($firstKey==$key){ $style="";}else{$style="display:none";}
				?>
	 <table class="table dataTable table-fixed myprogress marginBottom0 tblRankComp" id="tblRankComp-<?php echo $val->testId;?>-<?php echo $val->battery_id;?>" style="<?php echo $style;?>">
			
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-3 text-left padd0">Rank in Batch</td>
			  <td class="col-sm-1 text-center  padd0"><?php echo $val->testBatchRank;?></td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                  <div class="progress-bar progress-bar-info" data-toggle="tooltip" data-original-title="<?php echo $val->testBatchRankPercentile;?>%" style="width:<?php echo $val->testBatchRankPercentile;?>%"></div>
                 </div></td>
			<td class="col-sm-1"><?php echo $val->testBatchRankPercentile;?>%</td>
			</tr>
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-3 text-left padd0">Rank in <?php echo $center; ?></td>
			  <td class="col-sm-1 text-center  padd0"><?php echo $val->testCustomerRank;?></td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                  <div class="progress-bar progress-bar-warning" data-toggle="tooltip" data-original-title="<?php echo $val->testCustomerRankPercentile;?>%" style="width: <?php echo $val->testCustomerRankPercentile;?>%"></div>
              </div></td>
			<td class="col-sm-1"><?php echo $val->testCustomerRankPercentile;?>%</td>
			</tr>
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-3 text-left padd0">Overall</td>
			  <td class="col-sm-1 text-center padd0"><?php echo $val->testOverAllRank;?></td>
			 <td class="col-sm-7 text-center">
			   <div class="progress progress-xs m-t-sm">
                 <div class="progress-bar progress-bar-success" data-toggle="tooltip" data-original-title="<?php echo $val->testOverAllRankPercentile;?>%" style="width:<?php echo $val->testOverAllRankPercentile;?>%"></div>
              </div></td>
			<td class="col-sm-1"><?php echo $val->testOverAllRankPercentile;?>%</td>
			</tr>
		 </table>
	<?php } } else{?>
	 <table class="table dataTable table-fixed myprogress marginBottom0">
			
			<tr class="col-sm-12 padd0">
			 <td class="col-sm-12 text-center padd0"  colspan='3'>Records not available.</td>
			</tr>
			
		 </table>
	<?php } ?>
	</div> 		
 </section> 
</div> 	







<script type="text/javascript">

     
		
		function showHide(test_id,batt_id)
		{
			if(!batt_id){batt_id='';};
			showLoader();
			$('.tblScoreComp').hide();
           	$('#tblScoreComp-'+test_id+'-'+batt_id).show();			
			$('.tblRankComp').hide();
           	$('#tblRankComp-'+test_id+'-'+batt_id).show();
		   
			hideLoader();
		}
        

</script>




<?php include('../footer/userFooter.php'); ?>
