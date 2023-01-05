<?php include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }
$reportObj = new reportController();
$centerObj = new centerController(); 
$options = array();
$options['client_id'] = $client_id;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';
$dirQry = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';

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
$page_param='';
$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";
$center_id='';
$country='';

$rid = isset($_SESSION['region_id'])?$_SESSION['region_id']:'';

if (!empty($_SESSION['region_id'])) { 
    $region_id = trim($_SESSION['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}elseif (!empty($_REQUEST['region_id'])) {
    $region_id = trim($_REQUEST['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}else{
	$region_id = $rid;
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}



if (!empty($_REQUEST['center_id'])) {
    $center_id = trim(filter_query($_REQUEST['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}

if (!empty($_REQUEST['district_id'])) {
    $district_id = trim(filter_query($_REQUEST['district_id']));
	$options['district_id'] = $district_id;
	$page_param .= "district_id=$district_id&";
}

if (!empty($_REQUEST['tehsil_id'])) {
    $district_id = trim(filter_query($_REQUEST['tehsil_id']));
	$options['tehsil_id'] = $district_id;
	$page_param .= "tehsil_id=$district_id&";
}



$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;

$objPage = new Pagination($_page, $_limit);


$centres = $reportObj->getCentresList($rid);


if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

  $response_result= $reportObj->getCenterListByClientAndCountryExportSuperadmin($options,$objPage->_db_start, '',$order,$dir);
	
   // $response_result= $reportObj->getCenterListByClientAndCountry($options);

// echo "<pre>";print_r($response_result);  exit();

}else{
$response_result= $reportObj->getCenterListByClientAndCountry($options,$objPage->_db_start, $_limit,$order,$dirQry);
}

// $response_result= $reportObj->getCenterListByClientAndCountry();
 // echo "<pre>";print_r($response_result);  exit();
$objPage->_total = $response_result['total'];
$center_list_arr = $response_result['result'];
// echo "<pre>";print_r(count($center_list_arr)); 
 // echo "<pre>";print_r($center_list_arr); 

//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);
// echo count($center_list_arr); exit();
//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
		ob_clean();
	
        $file = 'states_report_'.time().'.csv';
        // header("Content-Disposition: attachment; filename=" . $file);
        // header("Content-Type: application/vnd.ms-excel"); 
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Centre Name</th>';
        $export_data .= '<th>State</th>';
        $export_data .= '<th>District Name</th>';
        $export_data .= '<th>Tehsil Name</th>';
        // $export_data .= '<th>Class Name</th>';
		// $export_data .= '<th>License</th>';
        $export_data .= '<th>Created Date</th>';
		$export_data .= '<th>Expiry Date</th>';
	
        $export_data .= '<th>learners</th>';
        $export_data .= '<th>Trainer </th>';
		$export_data .= '</tr>';


        if (count($center_list_arr) > 0) {
			$i = 0;
            foreach($center_list_arr  as $key => $value){
				$centre=$center_list_arr[$key]['region'];
				$state=$center_list_arr[$key]['state'];
				$district=$center_list_arr[$key]['district'];
				$tehsil=$center_list_arr[$key]['tehsil'];
				$batch=$center_list_arr[$key]['batch_name'];

				$teacherReg=$center_list_arr[$key]['teacherReg'];
				$studentReg=$center_list_arr[$key]['studentReg'];

				
				$license_key=$center_list_arr[$key]['license_key'];
				$created_date=$center_list_arr[$key]['created_date']; 
				$created_date = date('d-m-Y',strtotime($created_date));
				$expiry_days = $center_list_arr[$key]['expiry_days'];
				$expiry_date = $center_list_arr[$key]['expiry_date'];
				if($expiry_date!="" && $expiry_date != '0000-00-00 00:00:00'){
					
					$expiry_date = date('d-m-Y H:i',strtotime($expiry_date));
				
				}else{
					$res_used_date = $reportObj->getLicenseUsedDate($license_key);
					
					$expiry_date = date('d-m-Y H:i',strtotime($res_used_date . "+".$expiry_days." days"));
					$expiry_date = date('d-m-Y H:i',strtotime($expiry_date));
					
				}
			

                $i++;
                
           
			    $export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' . ltrim($centre,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($state,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($district,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($tehsil,"@-+=") . '</th>';
                // $export_data .= '<th>' . ltrim($batch,"@-+=") . '</th>';
                // $export_data .= '<th>' . ltrim($license_key,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($created_date,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($expiry_date,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($studentReg,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($teacherReg,"@-+=") . '</th>';

                $export_data .= '</tr>';
				
				
            }
        }



       $export_data .= '</table>';


        /* echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
        echo $export_data;
        die; */
		
		$html = str_get_html($export_data);
		// echo "<pre>"; print_r($export_data); exit();
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

<style type="text/css">.tree-btn{
	background: none;
    border: none;
    font-size: 19px;
    color: #047a9c;
    font-weight: 700;
}</style>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <h3><?php echo $language[$_SESSION['language']]['reports']; ?> </h3>
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
	<?php 
	include_once('reports_menu_dseu.php');
	?>
	  <div class="tab-content"> 
	  <div id="insReport" class="tab-pane fade in active">
	 <?php  //if(count($all_center_list_arr) > 0){?>
    <form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="reports.php" >
	<section class="marginBottom5 serachformDiv" >
		 <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
   		
		 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>">
		
		 <select name="region_id" id="region_id" class="form-control " onchange="setState(this)" >
				
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

		 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0">
				<select name="center_id" id="center_id" class="form-control" onchange="setDistrict(this)">
                  <option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);

						if(count($center_list_arr_drop_down)>0 ){
						 $optionSelected = ($center_id == 'All') ? "selected" : "";
						  echo '<option value="0" '.$optionSelected.'>All</option>';
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
<!--

			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
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

			  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
			 
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
				   </div>
  -->
				   
				
		</div>
			


		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0 text-right paddRight0">
			<button type="submit"  title="<?php echo $language[$_SESSION['language']]['search'].' ' .$language[$_SESSION['language']]['state'];  ?>" class="btn btn-red" id="btnSave" style="margin-top:0px"> 
			<?php echo $language[$_SESSION['language']]['search']; ?></button> 
		 <button type="submit" name="report_type" value="export" class="btn btn-sm btn-red btnwidth40 search-export export-report" 
		  title=" <?php echo $language[$_SESSION['language']]['export']; ?> " style="margin-top:0px">
		   <i class="fa fa-file-excel-o"></i></button>
			 <a class="btn btn-sm btn-red btnwidth40" href="reports.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?> " style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
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
		  <table class="table table-border dataTable table-fixed" id="StateWiseReport">
		    <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
				<th class="col-sm-3 text-left textUpper"><a href="reports.php?center_id=<?php echo filter_query($_REQUEST['center_id']);?>&country=<?php echo filter_query($_REQUEST['country']);?>&sort=name&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['state_name']; ?>
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
					</a>
			    </th>
			<!-- 	<th class="col-sm-2 text-left textUpper"><a href="reports.php?center_id=<?php echo filter_query($_REQUEST['center_id']);?>&country=<?php echo filter_query($_REQUEST['country']);?>&sort=license_key&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['licence']; ?>
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'license_key' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'license_key' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a>
				</th> -->
			   <th class="col-sm-2 text-left textUpper">
				<a href="reports.php?center_id=<?php echo filter_query($_REQUEST['center_id']);?>&country=<?php echo filter_query($_REQUEST['country']);?>&sort=created_date&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['activation_date']; ?>
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'created_date' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'created_date' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a>
			   </th>
			   <th class="col-sm-2 text-left textUpper">
			   <a href="reports.php?center_id=<?php echo filter_query($_REQUEST['center_id']);?>&country=<?php echo filter_query($_REQUEST['country']);?>&sort=expiry_days&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['expiry_date']; ?>
					<span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'expiry_days' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'expiry_days' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a>
			   </th>
			   <th class="col-sm-2 text-left textUpper">
			    <?php echo $language[$_SESSION['language']]['learners']; ?>
					 
				</th>
			   <th class="col-sm-3 text-center textUpper"> <?php echo $language[$_SESSION['language']]['district_admins']; ?>
					 </th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php

echo '<script>var tickslabel = []</script>'; 
echo '<script>var totalCenterStudent = []</script>'; 
echo '<script>var totalCenterTeacher = []</script>'; 
		// echo "<pre>";print_r($center_list_arr);exit;	
			 foreach($center_list_arr  as $key => $value){
				$centerId=$center_list_arr[$key]['center_id'];
				$centerAddress=$center_list_arr[$key]['address1']."  ".$center_list_arr[$key]['city'].", ".$center_list_arr[$key]['state'].", ".$center_list_arr[$key]['country']." - ".$center_list_arr[$key]['pincode'];
				$center_name=$center_list_arr[$key]['name'];
				$center_code=$center_list_arr[$key]['code'];
				$email_id=$center_list_arr[$key]['email_id'];
				$mobile=$center_list_arr[$key]['mobile'];
				$license_key=$center_list_arr[$key]['license_key'];
				$created_date=$center_list_arr[$key]['created_date']; 
				$created_date = date('d-m-Y',strtotime($created_date));
				$expiry_date = $center_list_arr[$key]['expiry_date'];
				$expiry_days = $center_list_arr[$key]['expiry_days'];
				$student_limit = $center_list_arr[$key]['student_limit']; 
				$trainer_limit = $center_list_arr[$key]['trainer_limit'];
			
				if($expiry_date!="" && $expiry_date != '0000-00-00 00:00:00'){
					
					$expiry_date = date('d-m-Y H:i',strtotime($expiry_date));
				
				}else{
					$res_used_date = $reportObj->getLicenseUsedDate($license_key);
					
					$expiry_date = date('d-m-Y H:i',strtotime($res_used_date . "+".$expiry_days." days"));
					$expiry_date = date('d-m-Y H:i',strtotime($expiry_date));
					
				}
				
				
				$res = $centerObj->getSignedUpUserCountByCenter($client_id,$centerId);
				$res = (object) $res;
				$totalCenterTeacher = $res->totalCenterTeacher;
				$totalCenterStudent = $res->totalCenterStudent;
				$totalCenterTeacher = !empty($totalCenterTeacher)?$totalCenterTeacher:0;
				$totalCenterStudent = !empty($totalCenterStudent)?$totalCenterStudent:0;
			
				$b2cBg="";
			

				
			?>
			<script>
			tickslabel.push([<?php echo $key?>, <?php echo "'".$center_name. "'"?>]);
			totalCenterStudent.push([<?php echo $key?>,<?php echo $totalCenterStudent;?>]);
			totalCenterTeacher.push([<?php echo $key?>,<?php echo $totalCenterTeacher;?>]);
			</script>
				<tr class="col-sm-12 padd0 tree-toggler" <?php echo $b2cBg;?>>
				   <td class="col-sm-3 text-left "> 
				   	<button onclick="plusTree(this)" data-type="state" class="tree-btn" data-id="<?php echo $centerId ?>">+</button> 
				   	<?php echo $center_name;?> </td>
				   <!-- <td class="col-sm-2 text-left textUpper"><.?php echo $license_key;?></td> -->
				   <td class="col-sm-2 text-left "><?php echo $created_date;?></td>
				   <td class="col-sm-2 text-left "><?php echo $expiry_date;?></td>
				   <td class="col-sm-2 text-left "><?php echo $totalCenterStudent.'/'.$student_limit;?></td>
				   <td class="col-sm-3 text-center "><?php echo $totalCenterTeacher.'/'.$trainer_limit;?></td>
				</tr>
				<tr style="display: none;" class="state-child-<?php echo $centerId ?>">
					<td class="nav nav-list tree" style="padding-top: 0;">
                            <table class="table">
                            	
                                <tbody>
                                	
                                </tbody>
                            </table>
                      </td>
				</tr>
			<?php //}  
		       }?>
			   
			 <tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			  <?php } else{   ?>
			<div class="col-xs-12 noRecord text-center">Records not available.</div>
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
 
 

<script src="./js/sb-report-script.js"></script>



