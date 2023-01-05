<?php 
include_once('../header/adminHeader.php');

//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();


 

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'user_name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';

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

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";
$rid = isset($_SESSION['region_id'])?$_SESSION['region_id']:'';

$center_id='';
$country='';
$batch_id='';
$student_id='';
if(!empty($_SESSION['region_id'])){
	$region_id=$_SESSION['region_id'];
}else if(!empty($_REQUEST['region_id'])){	
	$region_id = trim(filter_query($_REQUEST['region_id']));
	$page_param .= "region_id=$region_id&";
}else{
	$region_id = '';
}

if (!empty($_REQUEST['center_id'])) {
    $center_id = trim(filter_query($_REQUEST['center_id']));
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['country'])) {
    $country = trim(filter_query($_REQUEST['country']));
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['batch_id'])) {
    $batch_id = trim(filter_query($_REQUEST['batch_id']));
	$page_param .= "batch_id=$batch_id&";
}
if (!empty($_REQUEST['student'])) {
    $student_id = trim(filter_query($_REQUEST['student']));
	$page_param .= "student=$student_id&";
}
if (!empty($_REQUEST['student_txt'])) {
    $student_txt = trim(filter_query($_REQUEST['student_txt']));
	$page_param .= "student_txt=$student_txt&";
}

$centres = $reportObj->getCentresList($rid);



if($region_id=='All' || $region_id=='0'){
	$region_id_req = "";
	}
else{
	$region_id_req = $region_id;
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
if($student_txt=='All' || $student_txt=='0'){
		$student_txt = "";
}else{
		$student_txt = $student_txt;
}  
if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;	
	
	$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

	$objPage = new Pagination($_page, $_limit);
	
    $start_page = $objPage->_db_start; 
	
	//$request_param = "country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&start=".$objPage->_db_start."&limit=".$_limit."&sort=user_name%20asc";
	
	 if(isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export') {
		
		$request_param = "region_id=".$region_id_req."&country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&student_txt=".$student_txt."&topic_type=1&record_type=chapter&limit=1000000&sort=".$order."%20".$dir; 
				 
	 }else{
		
		$request_param = "region_id=".$region_id_req."&country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&student_txt=".$student_txt."&topic_type=1&record_type=chapter&start=".$start_page."&limit=".$_limit."&sort=".$order."%20".$dir;
		 
	 }	 
	
	
	//$url = SOLR_URL."api/time_spent.php?".$request_param;
	$url = SOLR_URL."api/session_tracking.php?".$request_param;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$result=curl_exec($ch);
	curl_close($ch);
	$res = json_decode($result, true);

	$total_response = $res['response']['numFound']; 

	$objPage->_total = $total_response;


$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);



//Export
if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();	
 
		   $file = 'time_spent_report'.time().'.csv';
        header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); 

        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
		$export_data .= '<th>Learner</th>';
        $export_data .= '<th>Level</th>';
        $export_data .= '<th>Module</th>';
        $export_data .= '<th>Lesson</th>';   
        $export_data .= '<th>Time Spent</th>';
        $export_data .= '<th>Last Activity</th>';
		$export_data .= '</tr>';
		
	    $i = 0;	
	//	if($total_response > 0){
			foreach($res['response']['docs']  as $key => $value){
									
				$quiz_time_sp = round($value['quiz_time_sp']/60);
				if($quiz_time_sp==0){
					$quiz_time_sp = 0;					
				}
							
				$last_attempted_date = date("d-M-Y H:i", strtotime($value['quiz_last_attempted_date']));
				if($last_attempted_date=='01-Jan-1970 05:30'){
					$last_attempted_date = '-';					
				}	
					
				if($value['topic_type'] == 1){
					
					$i++;

					$export_data .= '<tr>';
					$export_data .= '<th>' . $i . '</th>';
					$export_data .= '<th>' . ltrim($value['user_name'],"@-+=").'</th>';
					$export_data .= '<th>' . ltrim($value['course'],"@-+="). '</th>';
					$export_data .= '<th>' . ltrim($value['topic_name'],"@-+=") . '</th>';
					$export_data .= '<th>' . ltrim($value['chapter_name'],"@-+="). '</th>';		
					$export_data .= '<th>' . ltrim($quiz_time_sp,"@-+="). '</th>';
					$export_data .= '<th>' . ltrim($last_attempted_date,"@-+="). '</th>';
					
					$export_data .= '</tr>';
			
				} 
		    }
	//	}
         $export_data .= '</table>';
		
        // echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
        //echo $export_data;
        //die; 
		
		$html = str_get_html($export_data); 

		///header('Content-type: application/ms-excel');
		//header('Content-Disposition: attachment; filename='.$file);

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
	  <h3><?php echo $language[$_SESSION['language']]['reports']; ?></h3>
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
 <?php include_once('reports_menu.php');?> 
	  <div class="tab-content">


	 <div id="insReport" class="tab-pane fade in active">
	 <?php // if(count($users_arr) > 0){?>
    <form id="serachform" name="serachform"  method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" action="time_spent_report.php" >
	<section class="marginBottom5 serachformDiv">
	  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left text-left paddLeft0">
		 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>">
		
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

		<div class="<?php if($_SESSION['role_id']==7){?>col-xs-4 col-sm-4 col-md-4 col-lg-4 <?php }else{?> col-xs-3 col-sm-3 col-md-3 col-lg-3 <?php }?> text-left paddLeft0">
		   <select name="center_id" id="center_id" class="form-control" onchange="setDistrict(this)">
		   	<?php  $optiondisabled = ($center_id == 'All') ? "disabled" : ""; ?>
			 <option value=""  <?php  echo $optiondisabled ;?>><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
				  <?php 
				  
				  $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
				   if(count($center_list_arr_drop_down)>0 ){
					$optionSelected = ($center_id == 'All') ? "selected" : "";
					 echo '<option value="All" '.$optionSelected.'>All</option>';
					foreach($center_list_arr_drop_down  as $key => $value){
						   $centerId=$center_list_arr_drop_down[$key]['center_id'];
						   $center_name=$center_list_arr_drop_down[$key]['name'];
						   $optionSelected = ($center_id == $centerId) ? "selected" : "";
						   echo '<option   value="'.$centerId.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$center_name.'</option>';
							   
					}
				   }
				   
							  
				  
				  ?>	   
			 
			  
			  
			  
			  </select>
		   
		</div>
	<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0">
		
		<div class="searchboxCSS search-box col-xs-12 padd0 pull-right">
				<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['users'].' '.$language[$_SESSION['language']]['name_or_email']; ?>..." class="form-control  parsley-validated"  <?php if( (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student_txt'];?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student'];?>" <?php }?>/>
		<div class="result_list"></div>
		</div>
		
		</div>
	

		<!--<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
		   <select name="district_id" id="district_id" class="form-control" onchange="setTehsil(this)" >
			 <option value=""><?php echo $language[$_SESSION['language']]['select_district']; ?></option>
			 <?php	
						 $district_slected = isset($_GET['district_id']) ? filter_query($_GET['district_id']):0;
					 $districts =  $reportObj->session('districts');
					   foreach ($districts  as $key => $district) {
						   if($district_slected  == $district['district_id'])
						   echo "<option value='".$district['district_id']."' selected>".$district['district_name']."</option>";
						 else  
							echo "<option value='".$district['district_id']."' >".$district['district_name']."</option>";

					   }
				 ?>
			  </select>
		   
		</div>

		 <div class="col-xs-3 col-sm-3 col-md-2 col-lg-3 text-left paddLeft0">
		
		   <select name="tehsil_id" id="tehsil_id" class="form-control" >
			 <option value="">Select Tehsil </option>
				 <?php	
						 $tehsil_slected = isset($_GET['tehsil_id']) ? filter_query($_GET['tehsil_id']):0;
					 $tehsils =  $reportObj->session('tehsils');
					   foreach ($tehsils  as $key => $tehsil) {
						   if($tehsil_slected  == $tehsil['tehsil_id'])
						   echo "<option value='".$tehsil['tehsil_id']."' selected>".$tehsil['tehsil_name']."</option>";
						 else  
							echo "<option value='".$tehsil['tehsil_id']."' >".$tehsil['tehsil_name']."</option>";

					   }
				 ?>
			  
			  </select>
			  </div>-->

			
			
		</div>
		 <div class="clear" style="margin-top:10px;">&nbsp;</div>
		
		 <div class="pull-right paddRight0">
				<button type="submit" name="Submit" title="<?php echo $language[$_SESSION['language']]['search'].' ' .$language[$_SESSION['language']]['time_spent_report'];  ?>" class="btn btn-red" id="btnSave"style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']?> </button>
				
				<button type="submit" class="btn btn-sm btn-red btnwidth40 search-export export-report" name="report_type" 
				title="<?php echo $language[$_SESSION['language']]['export'];  ?>" style="margin-top:0px" value="export"> <i class="fa fa-file-excel-o"></i></button>
							
				<a class="btn btn-sm btn-red btnwidth40" href="time_spent_report.php" name="refresh" title="<?php echo $language[$_SESSION['language']]['refresh']  ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			 </div>
			 <label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
			 	<option value="10" <?php echo $_limit==10? 'selected':'' ?> >10 </option>
			 	<option value="25"<?php echo $_limit==25? 'selected':'' ?> >25 </option>
			 	<option value="50" <?php echo $_limit==50? 'selected':'' ?> >50 </option>
			 	<option value="100" <?php echo $_limit==100? 'selected':'' ?> >100 </option>
			 </select>
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
			<!--   <th class="col-sm-1">S.No.</th> --> 
			
			        <th class="col-sm-2 text-left textUpper"><a href="time_spent_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=user_name&dir=<?php echo $dir; ?>" class="th-sortable">
					<?php echo $language[$_SESSION['language']]['user']; ?>
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
			
					<th class="col-sm-2 text-left textUpper"><a href="time_spent_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=course&dir=<?php echo $dir; ?>" class="th-sortable">
					<?php echo $language[$_SESSION['language']]['level']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'course' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'course' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>


					<th class="col-sm-3 text-left textUpper"><a href="time_spent_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=topic_name&dir=<?php echo $dir; ?>" class="th-sortable">
					 <?php echo $language[$_SESSION['language']]['module']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'topic_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'topic_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>

					<th class="col-sm-1 text-center textUpper"><a href="time_spent_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=chapter_name&dir=<?php echo $dir; ?>" class="th-sortable">
					<?php echo $language[$_SESSION['language']]['lesson']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'chapter_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'chapter_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>
					
					<th class="col-sm-2 text-center textUpper">
					<a href="time_spent_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=quiz_time_sp&dir=<?php echo $dir; ?>" class="th-sortable">
					<?php echo $language[$_SESSION['language']]['time_spent_mins']; ?>
					
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'quiz_time_sp' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'quiz_time_sp' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>
					</th>

					<th class="col-sm-2 text-center textUpper">
					<a href="time_spent_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=quiz_last_attempted_date&dir=<?php echo $dir; ?>" class="th-sortable">
					
					<?php echo $language[$_SESSION['language']]['last_activity']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'quiz_last_attempted_date' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'quiz_last_attempted_date' && $_GET['dir']=='DESC'){ ?>
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
		
			   foreach($res['response']['docs']  as $key => $value){
				
				$quiz_time_sp = round($value['quiz_time_sp']/60);
				if($quiz_time_sp=='0'){
					$quiz_time_sp = 0;					
				}
							
				$last_attempted_date = date("d-M-Y H:i", strtotime($value['quiz_last_attempted_date']));
				if($last_attempted_date=='01-Jan-1970 05:30'){
					$last_attempted_date = '-';					
				}
				
				
				
			
			?>
			
		 
					
						<tr class="col-sm-12 padd0  toggler normal rowId" id="rowId<?php echo $i; ?>">
						
						
						
						   <td class="col-sm-2 text-left "><?php echo  $value['user_name']; ?> </td> 
						   <td class="col-sm-2 text-left "><?php echo $value['course']; ?></td>
						   <td class="col-sm-3 text-left "><?php echo $value['topic_name'];?></td>
						   <td class="col-sm-1 text-center "><?php echo $value['chapter_name'];?> </td>
						  

						   <td class="col-sm-2 text-center "> <?php echo $quiz_time_sp; ?> </td>
						   <td class="col-sm-2 text-center"> <?php echo $last_attempted_date; ?> </td>
						 
						</tr>
											
					<?php 
					$i++;
				
				}
			   ?>
					<tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr> 
			
			  <?php } else{   ?>
				<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?><br>
		</div>
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
//On region chnage
 $('#region').change(function(){
	var region = $('#region option:selected').val();  
	$('#student_txt').val('');
	$('#student_hidden').val('');
	$('#center_id').html('<option value="">Select <?php echo $center;?></option>');
	$('#batch_id').html('<option value="">Select <?php echo $batch;?></option>');
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
	var region_id = $('#region').val();
	var country = $('#country option:selected').val();
	$('#student_txt').val('');
	$('#student_hidden').val('');
	$('#center_id').html('<option value="">Select <?php echo $center;?></option>');
	$('#batch_id').html('<option value="">Select <?php echo $batch;?></option>');
	$.post('ajax/getCenterByCountry.php', {region_id: region_id,country: country}, function(data){ 
			if(data!=''){
				  $('#center_id').html(data);
				}else{
					$('#center_id').html('<option value="">Not Available</option>');
				}
		}); 
}); 


//On center chnage
 $('#center_id').change(function(){
	var region_id = $('#region').val();
	var country = $('#country').val();
	$('#student_txt').val('');
	$('#student_hidden').val('');
	var center_id = $('#center_id option:selected').val();
	if(center_id==''){
			$('#batch_id').html('<option value="">Select <?php echo $batch;?></option>');
		}else{
	$.post('ajax/getBatchByCenter.php', {region_id: region_id,country: country,center_id: center_id}, function(data){ 
			if(data!=''){
				  $('#batch_id').html(data);
				}else{
					$('#batch_id').html('<option value="">Not Available</option>');
				}
		}); }
});

///On center chnage
$('#batch_id').change(function(){
	
	$('#student_txt').val('');
	$('#student_hidden').val('');
	
});

</script>

<script>

        $(document).ready(function () {
             /*$(".export-report").click(function(e){
                e.preventDefault();
                var url = 'time_spent_report.php?report_type=export';
				//var region_id = $("#region").val();
               // var student = $("#student").val();
                //var center_id = $("#center_id").val();
                //var country = $("#country").val();
              //  var course_id = $("#course_id").val();
                //var batch_id = $("#batch_id").val();
				//var student_txt = $("#student_txt").val();
                // student_hidden = $("#student_hidden").val();
				//var sort = "<?php echo $order; ?>";
				//var dir = "<?php echo $dir; ?>";
				//url += '&region_id='+region_id;
                //url += '&center_id='+center_id;
                //url += '&student='+student;
               // url += '&country='+country;
               // url += '&course_id='+course_id;
               // url += '&batch_id='+batch_id;
				//url += '&student_txt='+student_txt;
                //url += '&student='+student_hidden;
				//url += '&sort='+sort;
				//url += '&dirt='+dir;

                 location.href = url;
                
            });*/
            
        });
  

</script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		var region_id = $('#region_id option:selected').val();
		var batch_id = $('#batch_id option:selected').val();
		var batch_id = $('#batch_id option:selected').val();
		var center_id = $('#center_id option:selected').val();
		var country = $('#country option:selected').val();
        var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_student_curl.php", {uname: inputVal,batch_id: batch_id,center_id: center_id,country: country,region_id: region_id}).done(function(data){
				// Display the returned data in browser
				resultDropdown.addClass("resultserchDiv");
				resultDropdown.html(data);
				
			});
		} else{
			resultDropdown.removeClass("resultserchDiv");
			resultDropdown.empty();
		}
	   
    });
    
     // Set search input value on click of result_list item
    $(document).on("click", ".result_list option", function(){
		
        $(this).parents(".search-box").find('input[type="hidden"]').val($(this).val());
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
		$(this).parent(".result_list").removeClass("resultserchDiv");
        $(this).parent(".result_list").empty();
		
    });
});
</script> 



<script src="./js/sb-report-script.js"></script>

