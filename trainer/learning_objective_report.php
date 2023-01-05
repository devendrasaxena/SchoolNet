<?php include_once('../header/trainerHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
$reportObj = new reportController();
if(isset($_REQUEST['student']) && $_REQUEST['student']!="" && isset($_REQUEST['batch']) && $_REQUEST['batch']!=""){
	$batch_id = trim($_REQUEST['batch']);
	//$country = trim($_REQUEST['country']);
	$course_id = trim($_REQUEST['course_id']);
	$student_id = trim($_REQUEST['student']);
	
	
	if($course_id!="" && $course_id!="All"){
		
		//Sorting
		$dir = "";
		$order = isset($_GET['sort']) ? $_GET['sort'] : 'cm.tree_node_id';
		$dir = isset($_GET['dir']) ? $_GET['dir'] : 'DESC';
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
		$topic_list_arr = $reportObj->getTopicOrAssessmentByCourseId($course_id,$order,$dir);
		
		
	}
	else{
		
		//Sorting
		$dir = "";
		$order = isset($_GET['sort']) ? $_GET['sort'] : 'LENGTH(title) ASC,title';
		
		$dir = isset($_GET['dir']) ? $_GET['dir'] : 'DESC';
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
		
		if($order=="title" && $dir == "ASC"){ $order = 'LENGTH(title) ASC,title';}
		if($order=="title" && $dir == "DESC"){ $order = 'LENGTH(title) DESC,title';}
		
		
		$topic_list_arr = $reportObj->getAllTopicOrAssessment($client_id,$order,$dir);
		
	}

	$users_arr = $adminObj->getUserList(2, $batch_id);	
	$stdInfo_arr=array();
	 foreach($users_arr  as $key => $value1){
		 $stdInfo_arr[]= userdetails($value1['user_id']); 
	 }
	
	//$users_arr = $reportObj->getUsersByCenter($center_id,'2');
	 
	


}else{
	$batch_id = "";
	//$country = "";
	$course_id = "";
	$student_id = "";
}


//$center_list_arr=$reportObj->getCenterListByClient($client_id,$center_id,$country);
//$center_list_arr=$reportObj->getCenterListByClient($client_id,'','');

//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);

$country_list_arr=$reportObj->getCountryList();

$course_list_arr=$reportObj->getCourseByClientId($client_id,'LENGTH(title) ASC,title','ASC');


//Export
if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
        $file = 'learning_objective_report'.time().'.xls';
        header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel");
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Level</th>';
        $export_data .= '<th>Learning Objective</th>';
        $export_data .= '<th>Skill</th>';
        $export_data .= '<th>Completed Lessons</th>';
        $export_data .= '<th>Score</th>';
		$export_data .= '</tr>';
        if (count($stdInfo_arr) > 0) {
            $i = 0;
           $courseId = "";$topicId = "";
			 foreach($topic_list_arr  as $key => $value){
				 
				if($value->assessment_type ==""){
					
				
				$skillArr =	$reportObj->getChapterSkillByTopicEdgeId($value->edge_id);
				
				foreach($skillArr as $skillKey=>$skillVal){
						
					if($courseId != $value->course_id )
					 {	
						$courseId = $value->course_id;
					}else{
						$value->course_title = ""; 
					 }
					
					if($topicId != $value->edge_id )
					 {	
						$topicId = $value->edge_id;
					}else{
						$value->description = ""; 
					 }
					
					$completionArr =	$reportObj->getTotalAndCompletedLesson($value->edge_id,$skillVal->skill_id,$student_id);
					$completionArr = json_decode($completionArr);
					$ttlChapter =$completionArr->cnt;
					$ttlComplChapter =$completionArr->cmplt;
					$chapter_list =$completionArr->chapter_list;
					
					$scorePer =	$reportObj->getSkillScoreByChapterEdgeId($chapter_list,$student_id); 
					
				$i++;	
					
				$export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' . $value->course_title. '</th>';
                $export_data .= '<th>' . $value->description . '</th>';
                $export_data .= '<th>' . $skillVal->competency. '</th>';
                $export_data .= '<th>' . $ttlComplChapter.'/'.$ttlChapter.'</th>';
                $export_data .= '<th>' . $scorePer. '</th>';
				$export_data .= '</tr>';
			
				
			}  
		    }
			}
           
			   
			
        }



        $export_data .= '</table>';
        echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
        echo $export_data;
        die;
    }
}


?>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <h3>Reports</h3>
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
 <ul class="nav nav-tabs" style="margin-bottom:40px;">
   <!-- <li class="textUpper"><a href="reports.php"><?php echo $centers; ?> Report</a></li>-->
	<li class="textUpper"><a href="learners_report.php"><?php echo $students; ?> Reports</a></li>
  <!--  <li class="textUpper"><a  href="trainers_report.php"><?php echo $teachers; ?> Reports</a></li>-->
    
	<li class="textUpper"><a  class="active" href="learning_objective_report.php">Performance Report</a></li>
  </ul>
	  <div class="tab-content">


	 <div id="insReport" class="tab-pane fade in active">
	 <?php // if(count($users_arr) > 0){?>
    <form id="serachform" name="serachform"  method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" action="learning_objective_report.php" >
	<section class="marginBottom5" style="height:80px;">

			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				<input name="center_id" id="center_id" value="<?php echo $center_id; ?>" type="hidden"/> 
				<select class="form-control parsley-validated" id="batch" name="batch" data-required="true">
					  <option value="">Select <?php echo $batch;?>*</option>
					  <?php  foreach($batchData as $key => $value){ 
					     $batchDataArr=$adminObj->getBatchNameByID($value['batch_id']);
						 $batch_name=$batchDataArr[0]['batch_name'];
						$sel = ($batch_id == $value['batch_id']) ? "selected" : "";
						?>
					  <option value="<?php echo $value['batch_id']; ?>" <?php echo $sel; ?> ><?php echo $batch_name; ?></option>
					  <?php } ?>
					</select>
		</div>			
		
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				<select name="student"  id="student"   class="form-control  parsley-validated" data-required="true">
                   <option value="">Select <?php echo $student; ?></option>
				   <?php  if(count($stdInfo_arr)>0){
							 foreach($stdInfo_arr  as $key => $value){
									
									$user_id = $value->user_id;
									
									$first_name = $value->first_name;
									$last_name = $value->last_name;
									$fullname = $first_name." ".$last_name;
							
									$optionSelected = ($student_id == $user_id) ? "selected" : "";
									echo '<option   value="'.$user_id.'" '.$optionSelected.' >'.$fullname.'</option>';
										
							 }
						 }
						?>
                   </select>
				
		</div>
		
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				<select name="course_id"  id="course_id"   class="form-control ">
                   <option value="">Select <?php echo $test;?></option>
                 <option value="All" <?php if($batch_id=='All'){ ?> selected <?php } ?>>All</option>
					<?php 
					 foreach ($course_list_arr as $key => $value) {	
						
						$title= $course_list_arr[$key]['title'];
						$courseId= $course_list_arr[$key]['course_id']; 
						
						if($course_id==$courseId){ 
						$selected ="selected"; }
						else{ $selected ="";} 
					   
					   ?>
					
						<option <?php echo $hide; ?> value="<?php echo $courseId; ?>" <?php echo $selected; ?> ><?php echo $title;?></option>	
					  <?php 
					   } ?>
				 </select>
				
		</div>
		
		
			
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave"style="margin-top:0px"> Search</button> <button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
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
		  <table class="table table-border dataTable table-fixed">
		    <?php if(count($stdInfo_arr) > 0 && !empty($stdInfo_arr)){?>
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2 text-left textUpper"><a href="learning_objective_report.php?center_id=<?php echo $center_id; ?>&student=<?php echo $student_id; ?>&course_id=<?php echo $course_id; ?>&sort=title&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $test;?>
		<span class="th-sort"> 
				<?php 
				if(isset($_GET['sort']) && $_GET['sort'] == 'title' && $_GET['dir']=='ASC'){ ?>
					<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
				<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'title' && $_GET['dir']=='DESC'){ ?>
					<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
				<?php }else{ ?> 
					<i class='fa fa-sort'></i>
				<?php } ?>
		</span>
		</a></th>
			   <th class="col-sm-4 text-left textUpper">Learning Objective
			  <!-- <a href="learning_objective_report.php?center_id=<?php echo $center_id; ?>&student=<?php echo $student_id; ?>&course_id=<?php echo $course_id; ?>&sort=cm.description&dir=<?php echo $dir; ?>" class="th-sortable">
			<span class="th-sort"> 
				<?php 
				if(isset($_GET['sort']) && $_GET['sort'] == 'cm.description' && $_GET['dir']=='ASC'){ ?>
					<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
				<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'cm.description' && $_GET['dir']=='DESC'){ ?>
					<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
				<?php }else{ ?> 
					<i class='fa fa-sort'></i>
				<?php } ?>
			</span>
			</a>-->
			</th>
			   <th class="col-sm-2 text-left textUpper">Skill</th>
			   <th class="col-sm-2 text-left textUpper">Completed Lessons</th>
			   <th class="col-sm-2 text-left textUpper">Score</th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php
		// echo "<pre>";print_r($center_list_arr);exit;	
		$courseId = "";$topicId = "";
			 foreach($topic_list_arr  as $key => $value){
				 
				 if($value->assessment_type ==""){
					
					/* $out = strlen($value->description) > 100 ? substr($value->description,0,100)."..." : $value->description; */
				
				$skillArr =	$reportObj->getChapterSkillByTopicEdgeId($value->edge_id);
				
				foreach($skillArr as $skillKey=>$skillVal){
						
					if($courseId != $value->course_id )
					 {	
						$courseId = $value->course_id;
					}else{
						$value->course_title = ""; 
					 }
					
					if($topicId != $value->edge_id )
					 {	
						$topicId = $value->edge_id;
					}else{
						$value->description = ""; 
					 }
					
					$completionArr =	$reportObj->getTotalAndCompletedLesson($value->edge_id,$skillVal->skill_id,$student_id);
					$completionArr = json_decode($completionArr);
					$ttlChapter =$completionArr->cnt;
					$ttlComplChapter =$completionArr->cmplt;
					$chapter_list =$completionArr->chapter_list;
					
					$scorePer =	$reportObj->getSkillScoreByChapterEdgeId($chapter_list,$student_id); 
					
					
					?>
						<tr class="col-sm-12 padd0" >
						   <td class="col-sm-2 text-left "><?php echo $value->course_title;?></td>
						   <td class="col-sm-4 text-left "><?php echo $value->description;?></td>
						   <td class="col-sm-2 text-left textUpper"><?php echo $skillVal->competency;?> </td>
						   <td class="col-sm-2 text-left ">
							<?php echo $ttlComplChapter.'/'.$ttlChapter;?>	 </td>
						   <td class="col-sm-2 text-left "> <?php echo $scorePer;?> %</td>
						</tr>
					<?php 
			
			}  
		    }
			}
			   ?>
			
			  <?php } else{   ?>
			<div class="col-xs-12 noRecord text-center">Records not available.<br>Please select <?php echo $batch; ?> and <?php echo $student; ?>.</div>
		  <?php }?>  
			</tbody>
		    </table>
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
<?php include_once('../footer/trainerFooter.php'); ?>
<script>
//On country chnage
/* $('#country').change(function(){
	
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
}); */
//On center chnage
$('#batch').change(function(){
	
	var batch_id = $('#batch option:selected').val();
	if(batch_id==''){
			$('#student').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getStudentByBatch.php', {batch_id: batch_id}, function(data){ 
			if(data!=''){
				  //$('#student').html();
				  $('#student').html(data);
				}else{
					$('#student').html('<option value="">Not Available</option>');
				}
		}); }
});

</script>

<script>

        $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'learning_objective_report.php?report_type=export';
                var student = $("#student").val();
                var batch_id = $("#batch").val();
                var course_id = $("#course_id").val();
                url += '&batch_id='+batch_id;
                url += '&student='+student;
                url += '&course_id='+course_id;

                 location.href = url;
                
            });
            
        });
      
 </script> 