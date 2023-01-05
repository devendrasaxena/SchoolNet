<?php include_once('../header/adminHeader.php');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();




//$center_list_arr=$reportObj->getCenterListByClient($client_id,$center_id,$country);
//$center_list_arr=$reportObj->getCenterListByClient($client_id,'','');

$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);

$country_list_arr=$reportObj->getCountryList();

$course_list_arr=$reportObj->getCourseByClientId($client_id,'LENGTH(title) ASC,title','ASC');

//Sorting
$dir = "";
$order = isset($_GET['sort']) ? filter_query($_GET['sort']) : 'rpt_id';
$dir = isset($_GET['dir']) ? filter_query($_GET['dir']) : 'ASC';
$order = $order.' '.$dir;
$order = trim($order);
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
	

$page_param='';
$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";


$rid = isset($_SESSION['region_id'])?$_SESSION['region_id']:'';

$center_id ='';
$country ='';
$batch_id = '';
$student_id ='';
$region_id ='';
$student_txt ='';

if(!empty($_SESSION['region_id'])){
	$region_id=$_SESSION['region_id'];
}else if(!empty($_REQUEST['region_id'])){	
	$region_id = trim(filter_query($_REQUEST['region_id']));
	$page_param .= "region_id=$region_id&";
}
if (!empty($_REQUEST['center_id'])) {
	$center_id = trim(filter_query($_REQUEST['center_id']));
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['country'])) {
	$country = trim(filter_query($_REQUEST['country']));
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

if (!empty($_REQUEST['district_id'])) {
	$district_id = trim(filter_query($_REQUEST['district_id']));
	$page_param .= "district_id=$district_id&";
}
if (!empty($_REQUEST['tehsil_id'])) {
	$tehsil_id = trim(filter_query($_REQUEST['tehsil_id']));
	$page_param .= "tehsil_id=$tehsil_id&";
}


$centres = $reportObj->getCentresList($rid);

//Export
if (isset($_REQUEST['report_type']) && ($_REQUEST['report_type'] == 'export')) {
        if($region_id=='All' || $region_id=='0'){
			$region_id_req = "";
		}else{
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
		if($student_id=='All' || $student_id=='0'){
			$student_id_req = "";
		}else{
			$student_id_req = $student_id;
		}
		if($student_txt=='All' || $student_txt=='0'){
			$student_txt = "";
		}else{
			$student_txt = $student_txt;
		}

	  $request_param = "region_id=".$region_id_req."&country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&student_txt=".$student_txt."&start=".$objPage->_db_start."&limit=1000000&sort=".$order;
		//$request_param = "country_name=".$country."&batch_id=".$batch_id."&inst_id=".$center_id."&limit=100&sort=".$order;


		$url = SOLR_URL.'api/performance_tracking.php';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$request_param);
		//In real life you should use something like:
		//curl_setopt($ch, CURLOPT_POSTFIELDS, 

		//Receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);

		//Initiate curl
		$res = json_decode($server_output, true);

		
		$datArr = $res['response']['docs'];
		$objPage->_total = $res['response']['numFound'];
		ob_clean();
       // $file = 'learning_objective_report'.time().'.xls';
        $file = 'performance_'.time().'.csv';
       header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel");

        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Learner </th>';
        $export_data .= '<th>Level</th>';
        $export_data .= '<th>Completed Modules</th>';
        $export_data .= '<th>Total Modules</th>';
        $export_data .= '<th>Score</th>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Name</th>';
        $export_data .= '<th>Progress </th>';
        $export_data .= '<th>Score</th>';
		$export_data .= '</tr>';
       

		if (count($datArr) > 0) {
			$i = 1;
			foreach($datArr  as $key => $value){ 
	
			$chap_id = $value['topic_edge_id'].'_'. $value['skill_id'];
			$completed_chapter_list = $value['completed_chapter_list'];
			
			if($completed_chapter_list !='NA'){
				
				$completed_chapter_list = json_decode($completed_chapter_list);
				$completed_chapter_list = json_decode(json_encode($completed_chapter_list), true);
				$j = 1;
				$ttl_chapter = count($completed_chapter_list);
				foreach($completed_chapter_list as $key=>$val){
										
					$topic_name = $val['topic_detail']['name'];
					$score_per = round($val['score_per'],2);
					$complete = $val['complete'];
					$completion_per = $val['completion_per'];
					$show_topic = $val['show'];
					if($completion_per==""){$completion_per=0;}
					if($show_topic=='yes'){
					
					$export_data .= '<tr>';
					$export_data .= '<th>' . $i++ . '</th>';
					$export_data .= '<th>' .ltrim($reportObj->displayText($value['user_name']),"@-+="). '</th>';
					$export_data .= '<th>' . ltrim($reportObj->displayText($value['course']),"@-+=") . '</th>';
					$export_data .= '<th>' . ltrim($value['completed_chapter'],"@-+=") . '</th>';
					$export_data .= '<th>' . ltrim($ttl_chapter,"@-+=") . '</th>';
					$export_data .= '<th>' . ltrim($value['score'],"@-+="). '</th>';
				
					$export_data .= '<th>' . $j++. '</th>';
					$export_data .= '<th>' .ltrim($reportObj->displayText($topic_name),"@-+=") . '</th>';
					$export_data .= '<th>' .ltrim($reportObj->displayText($completion_per).'%',"@-+=") . '</th>';
					$export_data .= '<th>' .ltrim($reportObj->displayText($score_per).'%',"@-+=") . '</th>';
					
					$export_data .= '</tr>';
					}
				}
				
			}else{
			
				$export_data .= '<tr>';
				$export_data .= '<th>' . $i++ . '</th>';
				$export_data .= '<th>' .ltrim($reportObj->displayText($value['user_name']),"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($reportObj->displayText($value['course']),"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($value['completed_chapter'],"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($ttl_chapter,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($value['score'],"@-+="). '</th>';
				$export_data .= '<th> </th>';
				$export_data .= '<th> </th>';
				$export_data .= '<th> </th>';
				$export_data .= '<th> </th>';
				
				
				$export_data .= '</tr>';
			}
		}

			
		}
		
	$export_data .= '</table>';
		
       /*  echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
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
else{
	
	$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

	if(isset($_REQUEST['limit']))
		$_limit = intval($_REQUEST['limit']);
	else
		 $_limit = PAGINATION_LIMIT;
		 

	$objPage = new Pagination($_page, $_limit);


	if($region_id=='All' || $region_id=='0'){
		$region_id_req = "";
	}else{
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
	if($student_id=='All' || $student_id=='0'){
		$student_id_req = "";
	}else{
		$student_id_req = $student_id;
	}
	if($student_txt=='All' || $student_txt=='0'){
		$student_txt = "";
	}else{
		$student_txt = $student_txt;
	}

	 $request_param = "region_id=".$region_id_req."&country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&student_txt=".$student_txt."&start=".$objPage->_db_start."&limit=".$_limit."&sort=".$order;
	 //$request_param ='';

	$url =  SOLR_URL."api/performance_tracking.php";
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
			$request_param);
	// In real life you should use something like:
	// curl_setopt($ch, CURLOPT_POSTFIELDS, 
	//          http_build_query(array('postvar1' => 'value1')));

	// Receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close ($ch);

	// Initiate curl
	$res = json_decode($server_output, true);

	$datArr = $res['response']['docs'];
	
	$objPage->_total = $res['response']['numFound'];


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
    <form id="serachform" name="serachform"  method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley"  action="learning_objective_report.php" >
		<section class="marginBottom5 serachformDiv" >
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

	    <div class=" <?php if($_SESSION['role_id']==7){?>col-xs-4 col-sm-4 col-md-4 col-lg-4 <?php }else{?> col-xs-3 col-sm-3 col-md-3 col-lg-3 <?php }?> text-left paddLeft0">
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
				<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['users'].' '.$language[$_SESSION['language']]['name_or_email']; ?>..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student_txt'];?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student'];?>" <?php }?>/>
		<div class="result_list"></div>
		</div>
		
		</div>
	

<!--
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
		   <select name="district_id" id="district_id" class="form-control" onchange="setTehsil(this)" >
			 <option value=""><?php echo $language[$_SESSION['language']]['select_district']; ?></option>
			 <?php	
						 $district_slected = isset($district_id) ? $district_id:0;
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
						 $tehsil_slected = isset($tehsil_id) ? $tehsil_id:0;
					 $tehsils =  $reportObj->session('tehsils');
					   foreach ($tehsils  as $key => $tehsil) {
						   if($tehsil_slected  == $tehsil['tehsil_id'])
						   echo "<option value='".$tehsil['tehsil_id']."' selected>".$tehsil['tehsil_name']."</option>";
						 else  
							echo "<option value='".$tehsil['tehsil_id']."' >".$tehsil['tehsil_name']."</option>";

					   }
				 ?>
			  
			  </select>
			  </div>

			-->
			
			</div>
			 <div class="clear" style="margin-top:10px;">&nbsp;</div>
		
	
			
				
				<div class="pull-right padd0">
					<button type="submit" name="Submit" title="<?php echo $language[$_SESSION['language']]['search'].' ' .$language[$_SESSION['language']]['perfomance_report'];  ?>" class="btn btn-red" id="btnSave"style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button>
					
					<button type="submit" class="btn btn-sm btn-red btnwidth40 search-export export-report-" name="report_type" value="export" title=" <?php echo $language[$_SESSION['language']]['export']; ?>" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button> 
					<a class="btn btn-sm btn-red btnwidth40" href="learning_objective_report.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
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
		     <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2 text-left textUpper"><a href="learning_objective_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=user_name&dir=<?php echo $dir; ?>" class="th-sortable">
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
						</span>
					</a>
			</th>
			  <th class="col-sm-4 text-left textUpper">
			  <a href="learning_objective_report.php?country=<?php echo $country; ?>
			  &center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>
			  &student=<?php echo $student_id; ?>
			  &student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>
			  &course_id=<?php echo $course_id; ?>&sort=course&dir=
			  <?php echo $dir; ?>" class="th-sortable">
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
		</span>
		</a></th> 
			 
			   <th class="col-sm-3 text-left textUpper">  <?php echo $language[$_SESSION['language']]['completed_modules']; ?> </th>
			  
			   

		 <th class="col-sm-3 text-center textUpper"><a href="learning_objective_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=score&dir=<?php echo $dir; ?>" class="th-sortable">
		 <?php echo $language[$_SESSION['language']]['score']; ?> 
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'score' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'score' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i>
							<?php } ?>
						</span>
					</a>
			</th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php
			
			$courseId = "";$topicId = ""; $i = 1;
			echo '<script>var graphLabels = [];</script>';
			echo '<script>var ScoreData = [];</script>';
			foreach($datArr  as $key => $value){ 
			
					$chap_id = $value['topic_edge_id'].'_'. $value['skill_id'];
					$completed_chapter_list = $value['completed_chapter_list'];
					
					
			
			?>
				<script> graphLabels.push([<?php echo $key?>,'<?php echo  $reportObj->displayText($value['user_name']);?>']);</script>
				<script> ScoreData.push([<?php echo $key?>,'<?php echo $value['score']; ?>']);</script>
				 
						<tr class="col-sm-12 padd0  toggler normal rowId" id="rowId<?php echo $i; ?>" <?php if($completed_chapter_list!='NA'){?> onClick="showPanel(this.id, 'icon<?php echo $i; ?>', 'panelrowId<?php echo $i; ?>', 'tableId_<?php echo $chap_id; ?>', <?php echo $chap_id; ?>, <?php echo $value['topic_edge_id']; ?>, <?php echo $value['topic_edge_id']; ?>, <?php echo $value['user_id']; ?>)" <?php }?>>
						
						<td class="col-sm-2 text-left ">
							<?php if($completed_chapter_list!='NA'){?>
							<span><i class="faShow fa fa-plus" id="icon<?php echo $i; ?>" ></i>
							</span>
							<?php }?>
							<?php echo $reportObj->displayText($value['user_name']);?>
							
							</td>
					
						 
						   <td class="col-sm-4 text-left "><?php echo $value['course'];?></td>
						   <!--td class="col-sm-2 text-left textUpper"><?php echo $reportObj->displayText($value['skill_name']);?> </td-->
						   <td class="col-sm-3 text-left ">
							<?php echo $value['completed_chapter'].'/'.$value['ttl_chapter'];?>	 </td>
						 
						   <td class="col-sm-3 text-center "> <?php echo $value['score'];?>%</td>
						</tr>
						<tr id="panelrowId<?php echo $i; ?>" class="panelShow" style="display:none;">
							<td colspan="12" class="padd0 col-xs-12">
								<div class="subtable" style="padding:0px 10px;height:200px;overflow-y:auto;">
									<table border='0' cellpadding='0' cellspacing='0' width='100%' class="" id="tableId_<?php echo $chap_id ?>">
									<thead>
									<tr>
									<th class="col-sm-2 text-left">S.No.</th>
									<th class="col-sm-4 text-left">Name</th>
									<th class="col-sm-3 text-left">Progress</th>
									<th class="col-sm-3 text-center" style=" padding-left: 32px;">Score</th>
								
									</tr>
									</thead> <tbody id="chId_<?php echo $chap_id; ?>" class="childData" >

									<?php if($completed_chapter_list !='NA'){
										
										$completed_chapter_list = json_decode($completed_chapter_list);
										$completed_chapter_list = json_decode(json_encode($completed_chapter_list), true);
			
										$j = 1;
										
									foreach($completed_chapter_list as $key=>$val){
										
										$topic_name = $val['topic_detail']['name'];
										$score_per = round($val['score_per'],2);
										$complete = $val['complete'];
										$completion_per = $val['completion_per'];
										$show_topic = $val['show'];
										if($completion_per==""){$completion_per=0;}
							if($show_topic=='yes'){
										?>
									<tr>

										<td class="col-sm-2 text-left"><?php echo $j++;?></td>
										<td class="col-sm-4 text-left"><?php echo $reportObj->displayText($topic_name);?></td>
										<td class="col-sm-3 text-left"><?php echo $reportObj->displayText($completion_per).'%';?></td>
										<td class="col-sm-3 text-center" style=" padding-left: 32px;"><?php echo $reportObj->displayText($score_per).'%';?></td>



									</tr>

									<?php } } } ?>

									</tbody>
								</table>                                       </div>
							</td>

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
/* 
        $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'learning_objective_report.php?report_type=export';
				var region_id = $("#region").val();
				var student_txt = $("#student_txt").val();
				//var student_txt = $("#student_txt").val();
                //var student = $('#student_hidden').val();
                var center_id = $("#center_id").val();
                //var country = $("#country").val();
               // var batch_id = $("#batch_id").val();
				url += '&region_id='+region_id;
                url += '&center_id='+center_id;
                url += '&student_txt='+student_txt;
               // url += '&student='+student;
                //url += '&country='+country;
                //url += '&batch_id='+batch_id;

                 location.href = url;
                
            });
            
        }); */
  

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
		var region_id = $('#region_id option:selected').val();
		var batch_id = $('#batch_id option:selected').val();
		var batch_id = $('#batch_id option:selected').val();
		var center_id = $('#center_id option:selected').val();
		var country = $('#country option:selected').val();
        var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_student_curl.php", {uname: inputVal,batch_id: batch_id,center_id: center_id,country: country,region_id: region_id}).done(function(data){
				// Display the returned data in browser
				resultDropdown.html(data);
				resultDropdown.css({"border":"1px solid #ccc","border-top":"0px"});
			});
		} else{
			resultDropdown.empty();
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


 

<script src="./js/sb-report-script.js"></script>



