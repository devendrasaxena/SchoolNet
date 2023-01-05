<?php 
include_once('../header/adminHeader.php');

//error_reporting(E_ALL);ini_set('display_erros',1);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? $_GET['sort'] : 'user_name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? $_GET['dir'] : 'ASC';

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
$student_id='';

if (!empty($_REQUEST['center_id'])) {
    $center_id = trim($_REQUEST['center_id']);
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['country'])) {
    $country = trim($_REQUEST['country']);
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['batch_id'])) {
    $batch_id = trim($_REQUEST['batch_id']);
	$page_param .= "batch_id=$batch_id&";
}
if (!empty($_REQUEST['student'])) {
    $student_id = trim($_REQUEST['student']);
	$page_param .= "student=$student_id&";
}
if (!empty($_REQUEST['student_txt'])) {
    $student_txt = trim($_REQUEST['student_txt']);
	$page_param .= "student_txt=$student_txt&";
}


if($country=='All' || $country=='0'){
	$country_req = "";
}else{
	 $country_req = $country;
}
if($center_id=='All' || $center_id=='0'){
	$center_id_req = "";
}else{
	$center_id_req = $center_id;
}
if($batch_id=='All' || $batch_id=='0'){
	$batch_id_req = "";
}else{
	$batch_id_req = $batch_id;
}
if($student_id=='All' || $student_id=='0' || $student_id=='' || $student_id=='undefined'){
	$student_id_req = "";
}else{
	$student_id_req = $student_id;
}
   
	$_limit = PAGINATION_LIMIT;	
	
	$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

	$objPage = new Pagination($_page, $_limit);
	
    $start_page = $objPage->_db_start; 

	//$request_param = "country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&start=".$objPage->_db_start."&limit=".$_limit."&sort=user_name%20asc";
	

	if(isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export') {

		$request_param = "country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&topic_type=1&record_type=course&limit=1000&sort=".$order."%20".$dir;
	 
	}else{
		$request_param = "country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&topic_type=1&record_type=course&start=".$start_page."&limit=".$_limit."&sort=".$order."%20".$dir;
	 
	}	
    	 

	$url = "http://3.7.157.39/report.php?".$request_param;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$result=curl_exec($ch);
	curl_close($ch);
	$res = json_decode($result, true);

	$total_response = $res['response']['numFound']; 

	$objPage->_total = $total_response;

$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);

$country_list_arr=$reportObj->getCountryList();

//Export
if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();	
 
		$file = 'time_spent_report2'.time().'.csv';
		
        /*  header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); */

        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Learner</th>';
        $export_data .= '<th>Current Level</th>';
        $export_data .= '<th>Lessons Completed</th>';
        $export_data .= '<th>Time Spent</th>';
        $export_data .= '<th>Last Activity</th>';
       
		$export_data .= '</tr>';
	
	    $i = 0;	
	//	if($total_response > 0){
			foreach($res['response']['docs']  as $key => $value){
					
					$current_level = $value['current_level'];
					if($value['current_level']==''){
						$current_level = '-';   
					} 				
					$lessons_completed = $value['completed_chapter'].' of '.$value['ttl_chapter'];		
						
					$course_time = gmdate("H:i:s",$value['course_time']);
					if($course_time=='00:00:00'){
						$course_time = '-';					
					}
								
					$last_attempted_date = date("d-M-Y H:i", strtotime($value['last_attempted_date']));
					if($last_attempted_date=='01-Jan-1970 05:30'){
						$last_attempted_date = '-';					
					}					
					
				if($value['topic_type'] == 1){
					
					$i++;

					$export_data .= '<tr>';
					$export_data .= '<th>' . $i . '</th>';
					$export_data .= '<th>' . $value['user_name'].'</th>';
					$export_data .= '<th>' . $current_level. '</th>';
					$export_data .= '<th>' . $lessons_completed . '</th>';
					$export_data .= '<th>' . $course_time. '</th>';			
					$export_data .= '<th>' . $last_attempted_date.'</th>';
					
					$export_data .= '</tr>';
			
				} 
		    }
	//	}
        $export_data .= '</table>';
		
        // echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
        //echo $export_data;
       // die; 
		
		$html = str_get_html($export_data); 

		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$file);

		$fp = fopen("php://output", "w");

		$html_tr = $html->find('tr');

		$trCount = count($html_tr);
	
		foreach($html_tr as $element)
		{

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
		
		fclose($fp);
		exit;
		
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
    <li class="textUpper"><a href="reports.php"><?php echo $centers; ?> Report</a></li>
	<li class="textUpper"><a href="learners_report.php"><?php echo $students; ?> Reports</a></li>
    <li class="textUpper"><a  href="trainers_report.php"><?php echo $teachers; ?> Reports</a></li>
    
	<li class="textUpper"><a  class="" href="learning_objective_report.php">Performance Report</a></li>
	<li class="textUpper"><a  class="active" href="lesson_report.php">Lesson Report</a></li>
	<li class="textUpper"><a  class="" href="skill_report.php">Skill Report</a></li>
  </ul>
	  <div class="tab-content">


	 <div id="insReport" class="tab-pane fade in active">
	 <?php // if(count($users_arr) > 0){?>
    <form id="serachform" name="serachform"  method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" action="time_spent_report2.php" >
	<section class="marginBottom5" style="height:80px;">
		 <div class="col-md-2 text-left paddLeft0">
		
			 <select name="country" id="country" class="form-control "  >
				<option value="">Select Country</option>
				 <option value="All" <?php if($country=='All'){ ?> selected <?php } ?>>All</option>
				<?php 
				 foreach ($country_list_arr as $key => $value) {	
				  $countryName= $country_list_arr[$key]['country_name'];
				  
				   if($country==$countryName){ $selected ="selected"; }
					else{ $selected ="";} 
				?>
					<option <?php echo $hide; ?> value="<?php echo $countryName; ?>" <?php echo $selected; ?>><?php echo $countryName;?></option>	
				  <?php 
				   } ?>
			   </select>
			
			</div>

			<div class="col-md-2 text-left paddLeft0">
					<select name="center_id" id="center_id" class="form-control " style="width: 173px;">
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country);
					   
						if(count($center_list_arr_drop_down)>0 && $country!=""){
						 $optionSelected = ($center_id == 'All') ? "selected" : "";
						  echo '<option value="0" '.$optionSelected.'>All</option>';
						  
						 foreach($center_list_arr_drop_down  as $key => $value){
							 
								$centerId=$center_list_arr_drop_down[$key]['center_id'];
								$center_name=$center_list_arr_drop_down[$key]['name'];
								$optionSelected = ($center_id == $centerId) ? "selected" : "";
								echo '<option   value="'.$centerId.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$center_name.'</option>';
									
						 }
						}
						else{
						echo '<option value="">Select '.$center.'</option>';
						}								   
					   
					   ?>				   
					   
					</select>
					
		</div>
		 <div class="col-md-2 text-left paddLeft0" >
				
				<select class="form-control parsley-validated" id="batch_id" name="batch_id"  style="width: 157px;" >
					 <?php 
						$batchInfo = $reportObj->getBatchDeatils($center_id);
						if(count($batchInfo)>0){
							 
							$optionSelected = ($batch_id == 'All') ? "selected" : "";
							echo '<option value="0" '.$optionSelected.'>All</option>';
							 foreach($batchInfo  as $key => $value){
									
									$batchId = $value['batch_id'];
									
									$batch_name = $value['batch_name'];
							
									$optionSelected = ($batch_id == $batchId) ? "selected" : "";
									echo '<option   value="'.$batchId.'" '.$optionSelected.' >'.$batch_name.'</option>';
										
							 }
						 }
						 else{
							echo '<option value="">Select '.$batch.'</option>';
						}?>
					</select>
		</div>	
		<div class="col-md-2 text-left paddLeft0">
		
		<div class="search-box" style="position: absolute;background: #fff; z-index: 999;">
				<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="Search <?php echo $student; ?>..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student_txt'];?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student'];?>" <?php }?>/>
		<div class="result_list"></div>
		</div>
		
		</div>
		
		<!--
		<div class="col-md-1 text-left paddLeft0">
				<select name="course_id"  id="course_id"   class="form-control " style="width: 125px;">
                   <option value="">Select <?php echo $test;?></option>
                 <option value="All" <?php if($course_id=='All'){ ?> selected <?php } ?>>All</option>
					<?php 
					 foreach ($course_list_arr as $key => $value) {	
						
						$title= $course_list_arr[$key]['title'];
						$courseId= $course_list_arr[$key]['course_id']; 
						
						if($course_id==$courseId){ $selected ="selected"; }
						else{ $selected ="";} 
					   
					   ?>
					
						<option <?php echo $hide; ?> value="<?php echo $courseId; ?>" <?php echo $selected; ?> ><?php echo $title;?></option>	
					  <?php 
					   } ?>
				 </select>
				
				
		</div>
		-->

			
			 <div class="col-md-3 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave"style="margin-top:0px"> Search</button>
				
				<button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button> 
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
		    <?php //if(count($topic_list_arr) > 0 && !empty($topic_list_arr)){?>
			 <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			<thead  class="fixedHeader">
				<tr class="col-sm-12 padd0">
			 <!-- <th class="col-sm-1">S.No.</th> -->
			
			        <th class="col-sm-3 text-left textUpper"><a href="time_spent_report2.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=user_name&dir=<?php echo $dir; ?>" class="th-sortable">Learner
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'user_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'user_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th> 
			
					<th class="col-sm-2 text-left textUpper"><a href="time_spent_report2.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=current_level&dir=<?php echo $dir; ?>" class="th-sortable">Current Level
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'current_level' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'current_level' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>

					<th class="col-sm-2 text-left textUpper"><a href="time_spent_report2.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=completed_chapter&dir=<?php echo $dir; ?>" class="th-sortable">Lessons Completed 
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'completed_chapter' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'completed_chapter' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>
					
					<th class="col-sm-2 text-left textUpper"><a href="time_spent_report2.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=course_time&dir=<?php echo $dir; ?>" class="th-sortable">Time Spent 
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'course_time' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'course_time' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>
					
					<th class="col-sm-3 text-left textUpper"><a href="time_spent_report2.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=last_attempted_date&dir=<?php echo $dir; ?>" class="th-sortable">Last Activity  
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'last_attempted_date' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'last_attempted_date' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>

				</tr>
			</thead>
		   <tbody>	
		    <?php
			$courseId = "";$topicId = ""; $i = 1;
			//echo "<pre>";print_r($res['response']['docs']);
			
			    foreach($res['response']['docs']  as $key => $value){
				   
						$current_level = $value['current_level'];
						if($value['current_level']==''){
							$current_level = '-';   
						} 				
						$lessons_completed = $value['completed_chapter'].' of '.$value['ttl_chapter'];		
							
						$course_time = gmdate("H:i:s",$value['course_time']);
						if($course_time=='00:00:00'){
							$course_time = '-';					
						}
									
						$last_attempted_date = date("d-M-Y H:i", strtotime($value['last_attempted_date']));
						if($last_attempted_date=='01-Jan-1970 05:30'){
							$last_attempted_date = '-';					
						}
				
					if($value['topic_type'] == 1){
			
			?>
						
						<tr class="col-sm-12 padd0  toggler normal rowId" id="rowId<?php echo $i; ?>">
						
						<!--<td class="col-sm-1"> <span>
							</span><?php echo $i; ?>
						</td> -->
						   <td class="col-sm-3 text-left "><?php echo  $value['user_name']; ?> </td> 
						   <td class="col-sm-2 text-left "><?php echo $current_level; ?></td>
						   <td class="col-sm-2 text-left "><?php echo $lessons_completed;?> </td>
						   <td class="col-sm-2 text-left "> <?php echo $course_time; ?> </td>
					   <td class="col-sm-3 text-left "> <?php echo $last_attempted_date; ?> </td>
						  <!-- <td class="col-sm-1 text-left "> 
						  <?php //echo $answer_exp_submit; ?> </td> -->
						</tr>
											
					<?php 
						$i++;
				//}  
				//}
					}
			// }
				}
			   ?>
			   
					<tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr> 
					
			
			  <?php } else{   ?>
			<div class="col-xs-12 noRecord text-center">Please select <?php echo $center; ?> and <?php echo $student; ?>.</div>
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
<?php include_once('../footer/adminFooter.php'); ?>
<script>
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
	$('#student_txt').val('');
	$('#student_hidden').val('');
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

//On center chnage
$('#batch_id').change(function(){
	
	$('#student_txt').val('');
	$('#student_hidden').val('');
	var batch_id = $('#batch_id option:selected').val();
	var center_id = $('#center_id option:selected').val();
	if(batch_id==''){
			$('#student').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getStudentByCenterAndBatch.php', {batch_id: batch_id,center_id: center_id}, function(data){ 
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
                var url = 'time_spent_report2.php?report_type=export';
                var student = $("#student").val();
                var center_id = $("#center_id").val();
                var country = $("#country").val();
              //  var course_id = $("#course_id").val();
                var batch_id = $("#batch_id").val();
				var student_txt = $("#student_txt").val();
                var student_hidden = $("#student_hidden").val();
				//var sort = "<?php echo $order; ?>";
				//var dir = "<?php echo $dir; ?>";
                url += '&center_id='+center_id;
                url += '&student='+student;
                url += '&country='+country;
               // url += '&course_id='+course_id;
                url += '&batch_id='+batch_id;
				url += '&student_txt='+student_txt;
                url += '&student='+student_hidden;
				//url += '&sort='+sort;
				//url += '&dirt='+dir;

                 location.href = url;
                
            });
            
        });
  

 var prviousId;
function showPanel(curId, iconId, panelId, targetId, chap_id, topic_edge_id, skill_id, student_id) {

	if ($('#' + curId).hasClass('activeTable')) {
		//alert("has")
		$('#' + iconId).removeClass("fa-minus");
		$('#' + curId).removeClass("activeTable");



	} else {
		$(".panelShow").fadeOut();
		$('span > i').addClass("fa-plus").removeClass("fa-minus");
		$('.toggler').addClass("normal").removeClass("activeTable");
		$('#' + iconId).addClass("fa-minus");
		$('#' + curId).addClass("activeTable");
		showLoader();
		
		var c_date_from = $("#date_of_start").val();
		var c_date_to = $("#date_of_end").val();
		var city = $("#city").val();
		
		$.ajax({url: 'ajax/chapter-ajax.php', type: 'post', dataType: 'html', 
			data: {topic_edge_id: topic_edge_id, skill_id: skill_id,student_id: student_id}, async: true,
			success: function (data) {
				$('#' + panelId).find(".childData").html(data);
				hideLoader();
			},
			error: function () {
				hideLoader();
			}
		});


	}
	$("#" + panelId).toggle();
	$('html, body').animate({
		scrollTop: $("#" + targetId).offset().top - 410}, 'slow');



}
</script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		var batch_id = $('#batch_id option:selected').val();
		var center_id = $('#center_id option:selected').val();
        var resultDropdown = $(this).siblings(".result_list");
       if(batch_id!="" &&  center_id!=""){
	   if(inputVal.length && inputVal.length>0){
            $.post("ajax/search_student.php", {uname: inputVal,batch_id: batch_id,center_id: center_id}).done(function(data){
                // Display the returned data in browser
                resultDropdown.html(data);
                resultDropdown.css({"border":"1px solid #ccc","border-top":"0px"});
            });
        } else{
            resultDropdown.empty();
        }
	   }else{
		    $('#serachform').parsley().validate("required");
		    $('#student_txt').focus();
		}
    });
    
    // Set search input value on click of result_list item
    $(document).on("click", ".result_list option", function(){
		
        $(this).parents(".search-box").find('input[type="hidden"]').val($(this).val());
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result_list").empty();
		$(this).parent(".result_list").css({"border":"none"});
    });
});
</script> 