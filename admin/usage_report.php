<?php include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
if($_SESSION['role_id']!=3){  header('location:dashboard.php');}

ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();

if(isset($_REQUEST['start_date']) && isset($_REQUEST['end_date'])){
if (!empty($_REQUEST['start_date'])) {
    $start_date = trim(filter_query($_REQUEST['start_date']));
	 $start_date_param = date('Y-m-d',strtotime($start_date));
}

if (!empty($_REQUEST['end_date'])) {
    $end_date = trim(filter_query($_REQUEST['end_date']));
	 $end_date_param = date('Y-m-d',strtotime($end_date)); 
}

//$total_user_uklearn= $reportObj->getTotalUserByUserFrom('uklearn',$start_date_param,$end_date_param);
$total_user_b2b= $reportObj->getTotalUserByUserFrom('b2b',$start_date_param,$end_date_param);
//$total_user_b2c= $reportObj->getTotalUserByUserFrom('b2c',$start_date_param,$end_date_param);

//Get total visiting users
//$total_visited_user_uklearn= $reportObj->getVisitedUserByUserFrom('uklearn',$start_date_param,$end_date_param);
$total_visited_user_b2b= $reportObj->getVisitedUserByUserFrom('b2b',$start_date_param,$end_date_param);
//$total_visited_user_b2c= $reportObj->getVisitedUserByUserFrom('b2c',$start_date_param,$end_date_param);

//Get users those given pretest
//$total_pretest_user_uklearn= $reportObj->getUserAttemptedPretestByUserFrom('uklearn',$start_date_param,$end_date_param);
$total_pretest_user_b2b= $reportObj->getUserAttemptedPretestByUserFrom('b2b',$start_date_param,$end_date_param);
//$total_pretest_user_b2c= $reportObj->getUserAttemptedPretestByUserFrom('b2c',$start_date_param,$end_date_param);

//Get users those Completed pretest
//$total_comple_pretest_user_uklearn= $reportObj->getUserCompletedPretestByUserFrom('uklearn',$start_date_param,$end_date_param);
$total_comple_pretest_user_b2b= $reportObj->getUserCompletedPretestByUserFrom('b2b',$start_date_param,$end_date_param);
//$total_comple_pretest_user_b2c= $reportObj->getUserCompletedPretestByUserFrom('b2c',$start_date_param,$end_date_param);

//Users those failed to complete pretest
//$total_failed_pretest_user_uklearn = $total_pretest_user_uklearn - $total_comple_pretest_user_uklearn;
$total_failed_pretest_user_b2b = $total_pretest_user_b2b - $total_comple_pretest_user_b2b;
//$total_failed_pretest_user_b2c=  $total_pretest_user_b2c - $total_comple_pretest_user_b2c;


//Get users those break level
//$total_break_level_user_uklearn= $reportObj->getBreakLevelUserByUserFrom('uklearn',$start_date_param,$end_date_param);
$total_break_level_user_b2b= $reportObj->getBreakLevelUserByUserFrom('b2b',$start_date_param,$end_date_param);
//$total_break_level_user_b2c= $reportObj->getBreakLevelUserByUserFrom('b2c',$start_date_param,$end_date_param);

//Get users those moved from one level to other 
//$total_move_level_user_uklearn= $reportObj->getMoveLevelUserByUserFrom('uklearn',$start_date_param,$end_date_param);
$total_move_level_user_b2b= $reportObj->getMoveLevelUserByUserFrom('b2b',$start_date_param,$end_date_param);
//$total_move_level_user_b2c= $reportObj->getMoveLevelUserByUserFrom('b2c',$start_date_param,$end_date_param);

//Module time spent
//$module_time_spent_uklearn= $reportObj->getModuleTimeSpent('uklearn',$start_date_param,$end_date_param);

//$module_time_spent_uklearn = ($module_time_spent_uklearn/60);
//$module_time_spent_uklearn = ($module_time_spent_uklearn/$total_pretest_user_uklearn);
//$module_time_spent_uklearn = round($module_time_spent_uklearn,1);
//$module_time_spent_uklearn = is_nan($module_time_spent_uklearn)?0:$module_time_spent_uklearn;

//$module_time_spent_b2c= $reportObj->getModuleTimeSpent('b2c',$start_date_param,$end_date_param);

//$module_time_spent_b2c = ($module_time_spent_b2c/60);
//$module_time_spent_b2c = ($module_time_spent_b2c/$total_pretest_user_b2c);
//$module_time_spent_b2c = round($module_time_spent_b2c,1);
//$module_time_spent_b2c = is_nan($module_time_spent_b2c)?0:$//module_time_spent_b2c;

$module_time_spent_b2b= $reportObj->getModuleTimeSpent('b2b',$start_date_param,$end_date_param);
$module_time_spent_b2b = ($module_time_spent_b2b/60);
$module_time_spent_b2b = ($module_time_spent_b2b/$total_pretest_user_b2b);
$module_time_spent_b2b = round($module_time_spent_b2b,1);
$module_time_spent_b2b = is_nan($module_time_spent_b2b)?0:$module_time_spent_b2b;

//Skill time spent
/* $skill_time_spent_uklearn= $reportObj->getSkillTimeSpent('uklearn',$start_date_param,$end_date_param);

$skill_time_spent_uklearn = ($skill_time_spent_uklearn/60);
$skill_time_spent_uklearn = ($skill_time_spent_uklearn/$total_pretest_user_uklearn);
$skill_time_spent_uklearn = round($skill_time_spent_uklearn,1);
$skill_time_spent_uklearn = is_nan($skill_time_spent_uklearn)?0:$skill_time_spent_uklearn;

$skill_time_spent_b2c= $reportObj->getSkillTimeSpent('b2c',$start_date_param,$end_date_param);
$skill_time_spent_b2c = ($skill_time_spent_b2c/60);
$skill_time_spent_b2c = ($skill_time_spent_b2c/$total_pretest_user_b2c);
$skill_time_spent_b2c = round($skill_time_spent_b2c,1);
$skill_time_spent_b2c = is_nan($skill_time_spent_b2c)?0:$skill_time_spent_b2c;

$skill_time_spent_b2b= $reportObj->getSkillTimeSpent('b2b',$start_date_param,$end_date_param); */

$skill_time_spent_b2b = ($skill_time_spent_b2b/60);
$skill_time_spent_b2b = ($skill_time_spent_b2b/$total_pretest_user_b2b);
$skill_time_spent_b2b = round($skill_time_spent_b2b,1);
$skill_time_spent_b2b = is_nan($skill_time_spent_b2b)?0:$skill_time_spent_b2b;
//Level wise reprot for all level
/* $level1_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,1);
$level2_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,2);
$level3_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,3);
$level4_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,4);
$level5_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,5);
$level6_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,6);
$level7_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,7);
$level8_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,8);
$level9_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,9);
$level10_user_uklearn = $reportObj->getTotalUserByUserFromAndByLevel('uklearn',$start_date_param,$end_date_param,10);



$level1_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,1);
$level2_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,2);
$level3_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,3);
$level4_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,4);
$level5_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,5);
$level6_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,6);
$level7_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,7);
$level8_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,8);
$level9_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,9);
$level10_user_b2c = $reportObj->getTotalUserByUserFromAndByLevel('b2c',$start_date_param,$end_date_param,10);
 */


$level1_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,1);
$level2_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,2);
$level3_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,3);
$level4_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,4);
$level5_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,5);
$level6_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,6);
$level7_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,7);
$level8_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,8);
$level9_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,9);
$level10_user_b2b = $reportObj->getTotalUserByUserFromAndByLevel('b2b',$start_date_param,$end_date_param,10); 
}
//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);

//Export
 if (isset($_REQUEST['report_type']) && isset($_REQUEST['start_date']) && isset($_REQUEST['end_date']))  {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
        $file = 'usage_report_'.time().'.csv';
        /* header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); */
        $export_data = '<table>';
        $export_data = '<thead>';
        $export_data .= '<tr>';
        $export_data .= '<th>Data from '.date('d-F-Y', strtotime($start_date)).' to '.date('d-F-Y', strtotime($end_date)).'</th>';
        //$export_data .= '<th>UK Learns </th>';
        //$export_data .= '<th>B2C</th>';
        $export_data .= '<th>B2B </th>';
        $export_data .= '<th> </th>';
        $export_data .= '<th>Level Wise User Count</th>';
       // $export_data .= '<th>UK Learn</th>';
       // $export_data .= '<th>B2C</th>';
        $export_data .= '<th>B2B</th>';
		$export_data .= '</tr>';
		$export_data .= '</thead>';

		$export_data .= '<tbody>';
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Visitors</td>';
		//$export_data .= '<td>NA</td>';
		//$export_data .= '<td>NA</td>';
		$export_data .= '<td>NA</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 1</td>';
		//$export_data .= '<td>' . ltrim($level1_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level1_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level1_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Registration</td>';
		//$export_data .= '<td>'.ltrim($total_user_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($total_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 2</td>';
		//$export_data .= '<td>' . ltrim($level2_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level2_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level2_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Registered Users Login</td>';
		//$export_data .= '<td>'.ltrim($total_visited_user_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($total_visited_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_visited_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 3</td>';
		//$export_data .= '<td>' . ltrim($level3_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level3_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level3_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Users taking Pre-test</td>';
		//$export_data .= '<td>'.ltrim($total_pretest_user_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($total_pretest_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_pretest_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 4</td>';
		//$export_data .= '<td>' . ltrim($level4_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level4_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level4_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Users successfully completing Pretest</td>';
		$export_data .= '<td>'.ltrim($total_comple_pretest_user_uklearn,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_comple_pretest_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_comple_pretest_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 5</td>';
		//$export_data .= '<td>' . ltrim($level5_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level5_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level5_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Users Failed Pre-test</td>';
		//$export_data .= '<td>'.ltrim($total_failed_pretest_user_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($total_failed_pretest_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_failed_pretest_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 6</td>';
		//$export_data .= '<td>' . ltrim($level6_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level6_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level6_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Level wise Count of Users post Pre-test (pass/fail)</td>';
		//$export_data .= '<td>'.ltrim($total_break_level_user_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($total_break_level_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_break_level_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 7</td>';
		//$export_data .= '<td>' . ltrim($level7_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level7_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level7_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Count of Users who Changed Level</td>';
		//$export_data .= '<td>'.ltrim($total_move_level_user_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($total_move_level_user_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($total_move_level_user_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 8</td>';
		//$export_data .= '<td>' . ltrim($level8_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level8_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level8_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Average time Spent on Skills</td>';
		//$export_data .= '<td>'.ltrim($skill_time_spent_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($skill_time_spent_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($skill_time_spent_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 9</td>';
		//$export_data .= '<td>' . ltrim($level9_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level9_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level9_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		
		$export_data .= '<tr>';
		$export_data .= '<td>Average time Spent on Modules</td>';
		//$export_data .= '<td>'.ltrim($module_time_spent_uklearn,"@-+=").'</td>';
		//$export_data .= '<td>'.ltrim($module_time_spent_b2c,"@-+=").'</td>';
		$export_data .= '<td>'.ltrim($module_time_spent_b2b,"@-+=").'</td>';
		$export_data .= '<td> </td>';
		$export_data .= '<td>Level 10</td>';
		//$export_data .= '<td>' . ltrim($level10_user_uklearn,"@-+=") . '</td>';
		//$export_data .= '<td>' . ltrim($level10_user_b2c,"@-+=") . '</td>';
		$export_data .= '<td>' . ltrim($level10_user_b2b,"@-+=") . '</td>';

		$export_data .= '</tr>';
		$export_data .= '</tbody>';
				
		 $export_data .= '</table>';
        /* echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
        echo $export_data;
        die; */
		
		$html = str_get_html($export_data);
	
		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$file);

		$fp = fopen("php://output", "w");

		if(!empty($html)){
			foreach($html->find('tr') as $element)
			{
				if(!empty($element)){
					$th = array();
					foreach( $element->find('th') as $row)  
					{
						$th [] = $row->plaintext;
					}

					$td = array();
					foreach( $element->find('td') as $row)  
					{
						$td [] = $row->plaintext;
					}
					!empty($th) ? fputcsv($fp, $th) : fputcsv($fp, $td);
			
				}
			
			}
		}
		fclose($fp);
		exit;
		
		}
		
    }




?>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <h3><?php echo $language[$_SESSION['language']]['report']; ?></h3>
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
<?php include_once('reports_menu.php');?>
	  <div class="tab-content">
	  <div id="insReport" class="tab-pane fade in active">
	 <?php  //if(count($all_center_list_arr) > 0){?>
    <form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="usage_report.php" data-validate="parsley" >
	<section class="marginBottom5 serachformDiv" >
		 <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
   		<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0 " >
		<label class="control-label"><?php echo $language[$_SESSION['language']]['start_date']; ?>  <span class="required">*</span></label>
		 <div id="startDate" class="input-append date form-control input-lg">
				<input  data-date-format="DD-MM-YYYY" readonly="true"  name="start_date" id="start_date" placeholder="DD-MM-YYYY" class=" width100per bdrNone" autocomplete="off" tabindex="4" value='<?php echo $start_date; ?>' style="width: 120px;" data-required="true"/>
					<span class="calendarBg add-on top30"  style="top: 30px;"> <i class="fa fa-calendar"></i>
					</span>
					
			</div> 
		
		</div>
		 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0">
			<label class="control-label"><?php echo $language[$_SESSION['language']]['end_date']; ?> <span class="required">*</span></label>
			<div id="endDate" class="input-append date form-control input-lg">
				<input  data-date-format="DD-MM-YYYY" readonly="true"  name="end_date" id="end_date" placeholder="DD-MM-YYYY" class=" width100per bdrNone" autocomplete="off" tabindex="4" value='<?php echo $end_date; ?>' style="width: 120px;" data-required="true"/>
					<span class="calendarBg add-on top30"  style="top: 30px;"> <i class="fa fa-calendar"></i>
					</span>
					
			</div> 
			
		</div>

		 		
		</div>
			


		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0 text-right paddRight0">
			<button type="submit" name="Submit" class="btn btn-red" id="btnSave" > <?php echo $language[$_SESSION['language']]['search']; ?></button> 
			 <button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" <?php echo $language[$_SESSION['language']]['export']; ?>" > <i class="fa fa-file-excel-o"></i></button>
			 <a class="btn btn-sm btn-red btnwidth40" href="usage_report.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?>" > <i class="fa fa-refresh"></i></a>
		 </div>
			
			
			</form>
			 <!-- <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>-->
	</section>	
   <div class="clear"></div>	
   <?php  //}?>
  
       
			<?php if(isset($_REQUEST['start_date']) && isset($_REQUEST['end_date'])){?>
			<section class="panel panel-default">
	    <div class="panel-body">
	     <div class="table-responsive">
			<table class="table table-border dataTable table-fixed">
		   
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
				<th class="col-sm-6 text-left textUpper">
				Data from <?php echo date('d-F-Y', strtotime($start_date));?> to <?php echo date('d-F-Y', strtotime($end_date));?>
			    </th>
				<th class="col-sm-6 text-center textUpper">
				B2B
			    </th>
				<!--<th class="col-sm-3 text-left textUpper">
				Comments
			    </th>-->
				
				
			</tr>
			</thead>
			<tbody>
			<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
				Count of Visitors</td>

				<td class="col-sm-6 text-center textUpper">
				NA
			    </td>
				
			</tr>
				
			<tr class="col-sm-12 padd0">	
			<td class="col-sm-6 text-left textUpper">
					Count of Registration
				</td>
				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_user_b2b;?>
			    </td>
				
				
			</tr>
			<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
					Count of Registered Users Login
			    </td>
				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_visited_user_b2b;?>
			    </td>
				
				
			</tr>
			<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
					Count of Users taking Pre-test
				</td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_pretest_user_b2b;?>
			    </td>
				
			</tr>
			<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
				Count of Users successfully completing Pretest</td>

				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_comple_pretest_user_b2b;?>
			    </td>
				
				
			    </tr>
				
				
				<tr class="col-sm-12 padd0">
				
				<td class="col-sm-6 text-left textUpper">
					Count of Users Failed Pre-test    </td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_failed_pretest_user_b2b;?>
			    </td>
				
			    </tr>
				
				<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
				Level wise Count of Users post Pre-test (pass/fail)
				</td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_break_level_user_b2b;?>
			    </td>
				
				</tr>
				
				<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
					Count of Users who Changed Level
				</td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $total_move_level_user_b2b;?>
			    </td>
				
				</tr>
				
				<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
				Average time Spent on Skills
				</td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $skill_time_spent_b2b;?>
			    </td>
				
				</tr>
				
				<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
				Average time Spent on Modules
				</td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $module_time_spent_b2b;?>
			    </td>
				
				</tr>
				
			</tr>
				
			 </tbody>		
			</table>		
			</div>
			</div>
			</section>
			
			<section class="panel panel-default">
			<div class="panel-body">
			<div class="table-responsive">
			<table class="table table-border dataTable table-fixed">
		   
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
				<th class="col-sm-6 text-left textUpper">
				Level Wise User Count
				</th>
				
				<th class="col-sm-6 text-center textUpper">
				B2B
			    </th>
				
			</tr>
			</thead>
			<tbody>
			<tr class="col-sm-12 padd0">
				<td class="col-sm-6 text-left textUpper">
				Level 1 </td>
				
				<td class="col-sm-6 text-center textUpper">
				<?php echo $level1_user_b2b;?>
			    </td>
				
			</tr>
			
				
			 </tbody>		
			</table>
			</div>
			</div>
			</section>
			<?php } else{?>
			<section class="panel panel-default">
			<div class="panel-body">
			<div class="table-responsive">
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['select_start_date_and_end_date_to_search_records.']; ?> </div>
			</div>
			</div>
			</section>
			<?php }?>
			
		  
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

</script>
 <script>

        $(document).ready(function () {
             $(".export-report").click(function(e){
				 $form = $('#serachform');
				 $form.parsley().validate();
				e.preventDefault();
                var url = 'usage_report.php?report_type=export';
                var start_date = $("#start_date").val();
                var end_date = $("#end_date").val();
                //var batch_id = $("#batch_id").val();
                url += '&start_date='+start_date;
                url += '&end_date='+end_date;
               // url += '&batch_id='+batch_id;
				if(start_date!="" && end_date!=""){
                 location.href = url;
				}
                
            });
            
        });

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
