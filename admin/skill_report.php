<?php include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();
$center_id = trim($_REQUEST['center_id']);
$country = trim($_REQUEST['country']);
$course_id = trim($_REQUEST['course_id']);
$batch_id = trim($_REQUEST['batch_id']);
$student_id = trim($_REQUEST['student']);

$student_id = trim($_REQUEST['student']);
$student_id = trim($_REQUEST['student']);

if(!empty($_SESSION['region_id'])){
	$region_id=$_SESSION['region_id'];
}else if(!empty($_REQUEST['region_id'])){	
	$region_id = trim($_REQUEST['region_id']);
	$page_param .= "region_id=$region_id&";
}else{
	$region_id = '';
}
//Sorting
$dir = "";
$order = isset($_GET['sort']) ? $_GET['sort'] : 'course_id';
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'DESC';
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
$page_param .= "sort=".$_GET['sort']."&dir=".$_GET['dir']."&";


//Export
if(isset($_REQUEST['report_type'])) {

	if ($_REQUEST['report_type'] == 'export') {
		
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

			$request_param = "region_id=".$region_id_req."&country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&start=0&limit=100000&sort=".$order;
			//$request_param = "country_name=".$country."&batch_id=".$batch_id."&inst_id=".$center_id."&limit=100&sort=".$order;

			$url = $report_url."/report1.php"; 
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
			 //$objPage->_total = $response_result['total'];

			
			
			 ob_clean();
		   // $file = 'learning_objective_report'.time().'.xls';
			$file = 'skill_report'.time().'.csv';
			/*  header("Content-Disposition: attachment; filename=" . $file);
			header("Content-Type: application/vnd.ms-excel"); */

			$export_data = '<table>';
			$export_data .= '<tr>';
			$export_data .= '<th>S.No.</th>';
			$export_data .= '<th>Learner</th>';
			$export_data .= '<th>Level</th>';
			$export_data .= '<th>Module</th>';
			$export_data .= '<th>Reading</th>';
			$export_data .= '<th>Writing</th>';
			$export_data .= '<th>Speaking</th>';
			$export_data .= '<th>Listening</th>';
			$export_data .= '<th>Vocabulary</th>';
			$export_data .= '<th>Grammar</th>';
			$export_data .= '</tr>';
			
			$i = 1;
				
			foreach($datArr  as $key => $value){
					
							$export_data .= '<tr>';
							$export_data .= '<th>' . $i . '</th>';
							$export_data .= '<th>' . ltrim($value['user_name'],"@-+="). '</th>';
							$export_data .= '<th>' . ltrim($value['course_name'],"@-+="). '</th>';
							$export_data .= '<th>' . ltrim($value['topic_name'],"@-+=") . '</th>';
							$export_data .= '<th>' . ltrim($value['reading_per'],"@-+=").' %</th>';
							$export_data .= '<th>' . ltrim($value['writing_per'],"@-+=").' %</th>';
							$export_data .= '<th>' . ltrim($value['speaking_per'],"@-+=").' %</th>';
							$export_data .= '<th>' . ltrim($value['listening_per'],"@-+=").' %</th>';
							$export_data .= '<th>' . ltrim($value['vocabulary_per'],"@-+=").' %</th>';
							$export_data .= '<th>' . ltrim($value['grammar_per'],"@-+=").' %</th>';
							$export_data .= '</tr>';
							
						$i++;
					 
					
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
}
else{


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


$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

//$_limit = 20;
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

$request_param = "region_id=".$region_id_req."&country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&start=".$objPage->_db_start."&limit=".$_limit."&sort=".$order;
 
/* $request_param = "country_name=".$country_req."&batch_id=".$batch_id_req."&inst_id=".$center_id_req."&user_id=".$student_id_req."&limit=100000&sort=".$order;  */
//$request_param = "country_name=".$country."&batch_id=".$batch_id."&inst_id=".$center_id."&limit=100&sort=".$order;

$url = $report_url."/report1.php";  

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
	 <?php // if(count($users_arr) > 0){?>
    <form id="serachform" name="serachform"  method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" action="skill_report.php" >
	<section class="marginBottom5 serachformDiv">
		<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left paddRight0 paddLeft0 <?php if($_SESSION['role_id']==7){?> hide <?php }?>" >

				<select name="region_id" id="region" class="form-control "  >
				<option value=""><?php echo $language[$_SESSION['language']]['select_center']; ?></option>
				<option value="All" <?php if($region_id=='All'){ ?> selected <?php } ?>>All</option>
				<?php 
				foreach ($region_arr as $key => $value) {	
				$regionName= $value['region_name'];

				if($_SESSION['role_id']==7 && $_SESSION['region_id']==$value['id']){
				$selected ="selected";
				}
				elseif( $_REQUEST['region_id']==$value['id']){ $selected ="selected"; }
				else{ $selected ="";} 
				?>
				<option <?php echo $hide; ?> value="<?php echo $value['id']; ?>" <?php echo $selected; ?>><?php echo $regionName;?></option>	
				<?php 
				} ?>
				</select>

		</div>
		 
		 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left <?php if($_SESSION['role_id']==7){?> paddLeft0 <?php }?>">
		
			 <select name="country" id="country" class="form-control "  >
				<option value=""><?php echo $language[$_SESSION['language']]['select_country']; ?></option>
				 <option value="All" <?php if($country=='All'){ ?> selected <?php } ?>>All</option> 
				<?php 
				$country_list_arr=$reportObj->getCountryList($region_id);
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

		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3  text-left paddLeft0">
					<select name="center_id" id="center_id" class="form-control ">
					<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
					   if(count($center_list_arr_drop_down)>0){
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
		 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left paddLeft0 paddRight0" >
				
				<select class="form-control parsley-validated" id="batch_id" name="batch_id"  >
				<option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
					 <?php 
						$batchInfo = $reportObj->getBatchDeatils($center_id,$country,$region_id);
						if(count($batchInfo)>0){
							 
							$optionSelected = ($batch_id == 'All') ? "selected" : "";
							echo '<option value="All" '.$optionSelected.'>All</option>';
							 foreach($batchInfo  as $key => $value){
									
									$batchId = $value['batch_id'];
									
									$batch_name = $value['batch_name'];
							
									$optionSelected = ($batch_id == $batchId) ? "selected" : "";
									echo '<option   value="'.$batchId.'" '.$optionSelected.' >'.$batch_name.'</option>';
										
							 }
						 }
						?>
					</select>
		</div>	
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left ">
		
		<div class="searchboxCSS search-box col-xs-12 padd0 pull-right">
				<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['learners']; ?>..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student_txt'];?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student'];?>" <?php }?>/>
		<div class="result_list"></div>
		</div>
		
		</div>
		</div>
		
		 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave"style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search'] ?></button>
				
				<!--<button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>--> 
				<a class="btn btn-sm btn-red btnwidth40" href="skill_report.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
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
		    <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			 <thead  class="fixedHeader">
				<tr class="col-sm-12 padd0">
					<!--<th class="col-sm-1">S.No.</th>-->
					<th class="col-sm-2 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=user_name&dir=<?php echo $dir; ?>" class="th-sortable">Learner
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
					</a>
					</th>
					<th class="col-sm-2 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=course&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $test;?>
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
					</a>
					</th>
					<th class="col-sm-2 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=topic_name&dir=<?php echo $dir; ?>" class="th-sortable">Module
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
					<th class="col-sm-1 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=reading_per&dir=<?php echo $dir; ?>" class="th-sortable">Reading
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
					<th class="col-sm-1 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=writing_per&dir=<?php echo $dir; ?>" class="th-sortable">Writing
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
					<th class="col-sm-1 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=speaking_per&dir=<?php echo $dir; ?>" class="th-sortable">Speaking
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
					<th class="col-sm-1 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=listening_per&dir=<?php echo $dir; ?>" class="th-sortable">Listening
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
					<th class="col-sm-1 text-left textUpper" style="padding-left: 0;"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=vocabulary_per&dir=<?php echo $dir; ?>" class="th-sortable" >Vocab<span class="th-sort"><?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'title' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'title' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i>
							<?php } ?>
						</span>
					</a></th>
					<th class="col-sm-1 text-left textUpper"><a href="skill_report.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=grammer_per&dir=<?php echo $dir; ?>" class="th-sortable">Grammer 
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
			 </tr>
			</thead>
		   <tbody>	
		    <?php
		
			$courseId = "";$topicId = ""; $i = 1;
			
			foreach($datArr  as $key => $value){ ?>
						
						<tr class="col-sm-12 padd0  toggler normal rowId" id="rowId<?php echo $i; ?>" <?php //if($ttlComplChapter>0){?> onClick="" <?php //}?>>
						
						<!--<td class="col-sm-1"> 
								<?php //echo $i; ?>
						</td>-->
						 
						   <td class="col-sm-2 text-left ">
							<?php echo $value['user_name'];?></td>
						   <td class="col-sm-2 text-left "><?php echo $value['course'];?></td>
						   <td class="col-sm-2 text-left "><?php echo $value['topic_name'];?></td>
						   <td class="col-sm-1 text-left ">
							<?php echo $value['reading_per'];?> %</td>
							<td class="col-sm-1 text-left ">
							<?php echo $value['writing_per'];?> %</td>
							<td class="col-sm-1 text-left ">
							<?php echo $value['speaking_per'];?> %</td>
							<td class="col-sm-1 text-left ">
							<?php echo $value['listening_per'];?> %</td>
							<td class="col-sm-1 text-left ">
							<?php echo $value['vocabulary_per'];?> %</td>
							<td class="col-sm-1 text-left ">
							<?php echo $value['grammar_per'];?> %</td>
						 
						</tr>
						
						
						
					<?php 
								
					$i++;
					
					}
				?>
			
			 <tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			  <?php } else{   ?>
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_found.']; ?><br><?php echo $language[$_SESSION['language']]['please_select_country_and_state_and_learner']; ?>.</div>
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
	$('#center_id').html('<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>');
	$('#batch_id').html('<option value="">Select <?php echo $batch;?></option>');
	if(region==''){
			$('#country').find('option').remove().end().append('<option value=""><?php echo $language[$_SESSION['language']]['select_country']; ?></option>');
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
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'skill_report.php?report_type=export';
				var region_id = $("#region").val();
                var student = $("#student").val();
                var center_id = $("#center_id").val();
                var country = $("#country").val();
              //  var course_id = $("#course_id").val();
                var batch_id = $("#batch_id").val();
				var student_txt = $("#student_txt").val();
                var student_hidden = $("#student_hidden").val();
				//var sort = "<?php echo $order; ?>";
				//var dir = "<?php echo $dir; ?>";
				url += '&region_id='+region_id;
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
  

</script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		var region_id = $('#region option:selected').val();
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