<?php 
include_once('../header/trainerHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

$reportObj = new reportController();
$country_list_arr=$reportObj->getCountryList();
$options = array();
$options['client_id'] = $client_id;



$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'u.first_name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';




function getLastLogin($datetime, $full = false) {
	if($datetime == "")
		return '-';
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}




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
$region_id = '';
$center_id='';
$country='';
$district_id='';
$tehsil_id='';
$role_id = '';
if (!empty($_SESSION['region_id'])) { 
    $region_id = trim($_SESSION['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}elseif (!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $region_id;
	$country_list_arr=$reportObj->getCountryList($region_id);
	$page_param .= "region_id=$region_id&";
}else{
	$options['region_id'] = $rid;
}
if (!empty($_SESSION['center_id'])) {
    $center_id = trim(filter_query($_SESSION['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}

if (!empty($_SESSION['batch_id'])) {
    $batch_id = trim(filter_query($_SESSION['batch_id']));
	$options['batch_id'] = $batch_id;
	$page_param .= "batch_id=$batch_id&";
}

if (!empty($_REQUEST['district_id'])) {
    $district_id = trim(filter_query($_REQUEST['district_id']));
	$options['district_id'] = $district_id;
	$page_param .= "district_id=$district_id&";
}

if (!empty($_REQUEST['tehsil_id'])) {
    $tehsil_id  = trim(filter_query($_REQUEST['tehsil_id']));
	$options['tehsil_id'] = $tehsil_id ;
	$page_param .= "tehsil_id=$tehsil_id&";
}


	$role_id = 2;
	$options['role_id'] = $role_id ;
	$page_param .= "role_id=$role_id&";

if (!empty($_REQUEST['student'])) {
    $student1 = trim(filter_query($_REQUEST['student']));
	$options['user_id'] = $student1 ;
	$page_param .= "student=$student1&";
}
if (!empty($_REQUEST['student_txt'])) {
    $student_txt = trim(filter_query($_REQUEST['student_txt']));
	$options['student_txt'] = $student_txt ;
	$page_param .= "student_txt=$student_txt&";
}
/* ob_clean();
echo "<pre>";
print_r($_SESSION);
exit; */

$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;

$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $reportObj->getUsersByCenterAndCountryAndBatch($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $reportObj->getUsersByCenterAndCountryAndBatch($options,$objPage->_db_start, $_limit,$order,$dir);
}

//$dir = $dir == 'ASC' ? 'DESC' : 'ASC';

$centres = $reportObj->getCentresList($rid);

$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];
/*ob_clean();
echo "<pre>";
print_r($users_arr);
 */
//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);




//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
       // $file = 'learners_report_'.time().'.xls';
        $file = 'users_report_'.time().'.csv';
        /* header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); */
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Name</th>';
        $export_data .= '<th>Login Id</th>';
        $export_data .= '<th>Joined</th>';
        $export_data .= '<th>Centre</th>';
        $export_data .= '<th>Last Login</th>';
        $export_data .= '<th>Status</th>';
		$export_data .= '</tr>';

        if (count($users_arr) > 0) {
            $i = 0;
             foreach($users_arr  as $key => $value){
				$first_name=$value['first_name'];
				$last_name=$value['last_name'];
				$fullname=$first_name." ".$last_name;
				
				$email_id=$value['email_id'];
				$mother_tongue=$value['mother_tongue'];
				$status=$value['is_active'];
				if($status=='1')
				{
				$status='Active';
				}
				$created_date=$value['created_date'];
				$created_date = date('d-m-Y',strtotime($created_date));
				$state=$value['state'];
				if($value['last_visit']!= ""){
		
				$lastLogin = date('d-m-Y',strtotime($value['last_visit']));
				}else{
					$lastLogin =  '-';
				}
				
				if($state=='')
				{
				$state='-';
				}

                $i++;
                
           
			    $export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' . ltrim($fullname,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($email_id,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($created_date,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($state,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($lastLogin,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($status,"@-+="). '</th>';

                $export_data .= '</tr>';
			
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
	<?php include_once('reports_menu_dseu.php');?>
	<div class="tab-content">
	<div id="insReport" class="tab-pane fade in active">
	 <?php // if(count($all_center_list_arr) > 0){?>
		<form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="users_report_dseu.php" >
	<section class="marginBottom5 serachformDiv" >
		 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left text-left paddLeft0 mb-1">
   		
		 <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>">
		
		 <!--<select name="region_id" id="region_id" class="form-control " onchange="setState(this)">
				
				<?php 
				if(count($centres)>1)
					echo '<option value="" selected disabled>'.$language[$_SESSION['language']]['select_centre'].'</option>';
				 foreach ($centres  as $key => $value) {	
				  	$centre = $value->centre;
					  $id = $value->id;
					  if($id == $region_id)
						  echo "<option value='$id' selected>$region</option>";
					  else  
					      echo "<option value='$id'>$region</option>";
				   } 
				   ?>
			   </select>-->
			
		</div>

		


			


		 </div>
		    <div class="clear" style="margin-top:10px;">&nbsp;</div>
		
		  <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 text-right paddLeft0 paddRight0">
		 
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left paddLeft0">
			 
			 <select name="role_id" id="role_id" class="form-control" style='width:128px;' >
			 		<?php  $optiondisabled = ($tehsil_id == 'All') ? "disabled" : ""; ?>
			   <!--<option value="" <?php  echo $optiondisabled ;?>><?php echo $language[$_SESSION['language']]['user_type']; ?> </option>
								<?php
					$optionSelected = ($role_id == 'All') ? "selected" : "";
						  echo '<option value="All" '.$optionSelected.'>All</option>';
			   ?>
                     <option class="usertype_options" value="7" <?php   echo $options['role_id'] == 7?'selected':'' ?> ><?php echo "Admins"; ?> </option>
					
					<option class="usertype_options" value="4" <?php   echo $options['role_id'] == 4?'selected':'' ?> ><?php echo "Senior Teachers"; ?> </option>
					<option class="usertype_options" value="1" <?php   echo $options['role_id'] == 1?'selected':'' ?> ><?php echo "Teachers"; ?></option>-->
					<option class="usertype_options" value="2" <?php echo  $options['role_id'] == 2?'selected':'selected' ?> ><?php echo $language[$_SESSION['language']]['learners']; ?></option>
				
				</select>
				</div>
				   
	  <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-left paddLeft0">
		
		<div class="searchboxCSS search-box col-xs-12 padd0 pull-right" style='padding-left:30px;'>
				<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['users'].' '.$language[$_SESSION['language']]['name_or_email'].' '.$language[$_SESSION['language']]['search']; ?>..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student_txt'];?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student'];?>" <?php }?>/>
		<div class="result_list"></div>
		</div>
		
		</div>
	
		</div>
		
		
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit"  class="btn btn-red" id="btnSave"
				 style="margin-top:0px" title="<?php echo $language[$_SESSION['language']]['search'].' ' .$language[$_SESSION['language']]['users_report'];  ?>"> 
				 <?php echo $language[$_SESSION['language']]['search']; ?></button>
				  <button type="submit" class="btn btn-sm btn-red btnwidth40 search-export export-report"
				   name="report_type" value="export" title=" <?php echo $language[$_SESSION['language']]['export']; ?> "
				    style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
				<a class="btn btn-sm btn-red btnwidth40" href="users_report_dseu.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?> " style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			</div>
			 <div class="clear" style="margin-top:10px;">&nbsp;</div>
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
			  <th class="col-sm-2 text-left textUpper">
				  <a href="users_report_dseu.php?region_id=<?php echo $region_id;?>&center_id=<?php echo $center_id;?>&district_id=<?php echo $district_id;?>&tehsil_id=<?php echo $tehsil_id;?>&role_id=<?php echo $role_id;?>&sort=u.first_name&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['name']; ?>
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'u.first_name' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'u.first_name' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span>
					</a>
				</th>
			   
			   <th class="col-sm-3 text-left textUpper"><a href="users_report_dseu.php?region_id=<?php echo $region_id;?>&center_id=<?php echo $center_id;?>&district_id=<?php echo $district_id;?>&tehsil_id=<?php echo $tehsil_id;?>&role_id=<?php echo $role_id;?>&sort=u.email_id&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['login_id']; ?>
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'u.email_id' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'u.email_id' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span>
					</a>
				</th>
			   <th class="col-sm-2 text-left textUpper"><a href="users_report_dseu.php_dseu?region_id=<?php echo $region_id;?>&center_id=<?php echo $center_id;?>&district_id=<?php echo $district_id;?>&tehsil_id=<?php echo $tehsil_id;?>&role_id=<?php echo $role_id;?>&sort=u.created_date&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['joined']; ?>
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'u.created_date' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'u.created_date' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span>
					</a>
				</th>
			  <th class="col-sm-2 text-left textUpper">
			  	<a href="users_report_dseu.php?region_id=<?php echo $region_id;?>&center_id=<?php echo $center_id;?>&district_id=<?php echo $district_id;?>&tehsil_id=<?php echo $tehsil_id;?>&role_id=<?php echo $role_id;?>&sort=state&dir=<?php echo $dir?>" class="th-sortable">
			  	<?php echo $language[$_SESSION['language']]['state_name']; ?>
			  	<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'state' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'state' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span></a>
			  </th>
			   <th class="col-sm-2 text-left textUpper"><a href="users_report_dseu.php?region_id=<?php echo $region_id;?>&center_id=<?php echo $center_id;?>&district_id=<?php echo $district_id;?>&tehsil_id=<?php echo $tehsil_id;?>&role_id=<?php echo $role_id;?>&sort=last_visit&dir=<?php echo $dir?>" class="th-sortable">
			   	  	<?php echo $language[$_SESSION['language']]['last_login']; ?>
			   	<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'last_visit' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'last_visit' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span></a>

			   </th>
			   <th class="col-sm-1 text-left textUpper"><a href="users_report_dseu.php?region_id=<?php echo $region_id;?>&center_id=<?php echo $center_id;?>&district_id=<?php echo $district_id;?>&tehsil_id=<?php echo $tehsil_id;?>&role_id=<?php echo $role_id;?>&sort=u.is_active&dir=<?php echo $dir?>" class="th-sortable">
			   	<?php echo $language[$_SESSION['language']]['status']; ?>
			   		
			   		<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'u.is_active' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'u.is_active' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span></a>
			   	</th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php
	

			 foreach($users_arr  as $key => $value){
				$first_name=$value['first_name'];
				$last_name=$value['last_name'];
				$fullname=$first_name." ".$last_name;
				
				$email_id=$value['email_id'];
				$mother_tongue=$value['mother_tongue'];
				$status=$value['is_active'];
				if($status=='1')
				{
				$status='Active';
				}else{
					$status='Inactive';
				}
				$created_date=$value['created_date'];
				$created_date = date('d-m-Y',strtotime($created_date));
				$country=$value['country'];
				$state=$value['state'];
				if($state=='')
				{
				$state='-';
				}
				if($value['last_visit']!= ""){
		
				$lastLogin = date('d-m-Y',strtotime($value['last_visit']));
				}else{
					$lastLogin =  '-';
				}
				
				

				
				if($country=='')
				{
				$country='-';
				}
				
			
			?>

			 
				<tr class="col-sm-12 padd0" >
				   <td class="col-sm-2 text-left"><?php echo $fullname;?></td>
				   
				   <td class="col-sm-3 text-left "><?php echo $email_id;?></td>
				   <td class="col-sm-2 text-left "><?php echo $created_date;?></td>
				 
				   <td class="col-sm-2 text-left textUpper"><?php echo $state;?></td>
				   <td class="col-sm-2 text-left textUpper"><?php echo $lastLogin;?></td>
				   <td class="col-sm-1 text-left"><?php echo $status;?></td>
				</tr>
			<?php //}  
		       }?>
				<tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			  <?php } else{   ?>
				<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?><br>
		</div>  <?php }?>  
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

//On center chnage
 $('#center_id').change(function(){
	
	/*var center_id = $('#center_id option:selected').val();
	if(center_id==''){
			$('#district_id').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getBatchByCenter.php', {center_id: center_id}, function(data){ 
			if(data!=''){
				  $('#district_id').html(data);
				}else{
					$('#district_id').html('<option value="">Not Available</option>');
				}
		}); }*/
});

</script> 
<script>

     /*  $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'users_report_dseu.php?report_type=export';
                var country = $("#country").val();
                var center_id = $("#center_id").val();
                var district_id = $("#district_id").val();
                url += '&center_id='+center_id;
                url += '&country='+country;
                url += '&district_id='+district_id;

                 location.href = url;
                
            });
            
        });*/
      
 </script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		var region_id = $('#region_id option:selected').val();
		var district_id = $('#district_id option:selected').val();
		var tehsil_id = $('#tehsil_id option:selected').val();
		var batch_id = "<?php echo $batch_id ?>";
		var center_id = "<?php echo $center_id ?>";
		var role_id = $('#role_id option:selected').val();
        var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_student.php", {uname: inputVal,batch_id: batch_id,center_id: center_id,role_id: role_id,region_id: region_id,district_id: district_id,tehsil_id: tehsil_id}).done(function(data){
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

var colors = ['#4b7fbb','#ec859a','#be4b48','#4b7fbb','#ec859a','#be4b48','#4b7fbb','#ec859a','#be4b48','#4b7fbb'];

$(function(){


	$.get('./report-helper.php?getUserGraph',res=>{
		if(res.err)
			return;

			var graphLabels = [];
			var tmpGraphData = [];
			for(key in res.data){
				tmpObj = res.data[key];
				tmpGraphData.push([key,tmpObj.users]);
				graphLabels.push([key,tmpObj.month+'-'+tmpObj.year]);
			}
	
			var graphData = [
		{
			data: tmpGraphData,
			label: "Users"
		}];

		$("#flot-chart").length && $.plot($("#flot-chart"),graphData,
			{
				
			series: {
			lines: {
			show: true,
			lineWidth: 1,
			fill: false,
			fillColor: {
			colors: [{
			opacity: 0
			}, {
			opacity: 0
			}]
			}
			},
			points: {
			show: true
			},
			shadowSize: 2
			},
			grid: {
			hoverable: true,
			clickable: true,
			tickColor: "#f0f0f0",
			borderWidth: 0
			},
			colors: colors,
			xaxis: {
			ticks: graphLabels  
			},
			yaxis: {
			position: 'left', axisLabel: 'Y Axis', showTickLabels: 'none' ,
			tickDecimals: 0,
			labelWidth: 30
			},
			legend: {
				show: false  
			},
			tooltip: true,
			tooltipOpts: {
			content: "%s %y.4 Registered",
			defaultTheme: false,
			shifts: {
			x: 0,
			y: 20
			}
			}
			});
	});
	


});

</script>