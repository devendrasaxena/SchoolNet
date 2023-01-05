<?php include_once('../header/adminHeader.php');
$centerObj = new centerController();
$reportObj = new reportController();
$region_arr=$centerObj->getRegionDetails();
$tchRowsData = '';
$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$student not saved. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "$student created successfully.";
	}
	if($_SESSION['succ'] == '2'){
		$msg = "$student updated successfully.";
	}
}
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		//$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);

	
}
$options = array();
$options['client_id'] = $client_id;
$options['role_id'] = 2;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? $_GET['sort'] : 'u1.created_date';
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

if(!empty($_SESSION['region_id'])){
	$options['region_id'] = $_SESSION['region_id'];
	$region_id=$_SESSION['region_id'];
	$country_list_arr=$reportObj->getCountryList($region_id);
	$options['region_id'] = $region_id;
	$country_list_arr=$reportObj->getCountryList($region_id);
}else if(!empty($_REQUEST['region_id'])){	
	$region_id = trim($_REQUEST['region_id']);
	$country_list_arr=$reportObj->getCountryList($region_id);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
	$country_list_arr=$reportObj->getCountryList($region_id);
}else{
	$options['region_id'] = $region_id;
	$country_list_arr=$reportObj->getCountryList();
	$region_id='';
}


if (!empty($_REQUEST['center_id'])) {
    $center_id = trim($_REQUEST['center_id']);
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['country'])) {
    $country = trim($_REQUEST['country']);
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['batch_id'])) {
    $batch_id = trim($_REQUEST['batch_id']);
	$options['batch_id'] = $batch_id;
	$page_param .= "batch_id=$batch_id&";
}
if (!empty($_REQUEST['student'])) {
    $student_id = trim($_REQUEST['student']);
	$options['student_id'] = $student_id;
	$page_param .= "student_id=$student_id&";
}
if (!empty($_REQUEST['status']) || $_REQUEST['status'] == '0') {
    $status = trim($_REQUEST['status']);
	$options['status'] = $status;
	$page_param .= "status=$status&";
}




$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

//$_limit = 20;
$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $centerObj->getUsersByCenterAndCountry($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $centerObj->getUsersByCenterAndCountry($options,$objPage->_db_start, $_limit,$order,$dir);
}

$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];

?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"><?php echo $students; ?></div>
	<div class="col-md-6 col-sm-6 text-right"><span class="pull-right"><a href='createStudent.php' class="btn btn-primary marginTop0">Add <?php echo $student;?></a> <a href='bulkStudentUpload.php' class="btn btn-primary marginTop0">Bulk Upload <?php echo $students;?></a>
</span>	</div>
 </div>
 <div class="clear"></div>
 <section class="padder">

 <form id="serachform" name="serachform"  method = "get"  class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" action="studentList.php" >
	<section class="marginBottom5 serachformDiv">
	
       <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 pull-left text-left paddLeft0">
       <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> hide <?php }?>" >

		 <select name="region_id" id="region" class="form-control "  >
			<option value="">Select Centre</option>
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
		
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left paddLeft0">

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

				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left paddLeft0 paddRight0">
							<select name="center_id" id="center_id" class="form-control" >
							<option value="">Select <?php echo $center;?></option>
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
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-left ">
						
						<select class="form-control parsley-validated" id="batch_id" name="batch_id"  >
						<option value="">Select <?php echo $batch; ?></option>
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
								 else{
									echo '<option value="">Select '.$batch.'</option>';
								}?>
							</select>
				</div>	
			
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  text-left paddLeft0">
					<select id="status" name="status" onchange="selectStatus(this);" class="form-control " >
								<option value="">Status</option>
									<option <?php if($status=='1'){ echo 'selected';}?> value="1">Active</option>	
									<option <?php if($status=='0'){ echo 'selected';}?> value="0">Inactive</option>
							   </select>
			   </div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  text-left paddLeft0">
					 <div class="searchboxCSS search-box col-xs-10 padd0 pull-right">
							<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="Search <?php echo $student; ?>..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student_txt'];?>" <?php }?>/>
							<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo $_REQUEST['student'];?>" <?php }?>/>
					<div class="result_list"></div>
					</div>
				
				</div>
			
			
			</div>

			 <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 pull-right text-right padd0">
					<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> Search</button>
				 </div>
				</form>
  <?php if($succ=='1'){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
      <?php } ?>
	<?php if($succ=='2'){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i> <?php echo $msg;?> </div>
      <?php } ?>
	    <?php if($err == '1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
 </section>
 <div class="clear"></div>	
  <section class="panel panel-default">
   
     <div class="panel-body">
		   <?php if($objPage->_total>0){	
		   $no = ($_page - 1) * $_limit + 1;?>
			<div class="table-responsive">
			<table class="table table-border dataTable table-fixed">
		   <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=first_name&dir=<?php echo $dir; ?>" class="th-sortable">First Name
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'first_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'first_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			  <th class="col-sm-1 text-left"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=last_name&dir=<?php echo $dir; ?>" class="th-sortable">Last Name
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'last_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'last_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			 <th class="col-sm-3 text-left"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=email_id&dir=<?php echo $dir; ?>" class="th-sortable">Login Id
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			 <th class="col-sm-2 text-left paddLeft0 paddRight0"><?php echo $center;?><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=name&dir=<?php echo $dir; ?>" class="th-sortable hide">
					<span class="th-sort"> <?php echo $center;?>
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			 <th class="col-sm-1 text-left">User Type</th>
			  <th class="col-sm-1 text-left">Status</th>
			  <th class="col-sm-1 text-left" >Expiry Date<!--<a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo $_REQUEST['student_txt']; ?>&course_id=<?php echo $course_id; ?>&sort=expiry_date&dir=<?php echo $dir; ?>" class="th-sortable">Expiry Date<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'expiry_date' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'expiry_date' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a>--></th>
			  <th class="col-sm-1 text-center">Action</th>
			  
			  <!-- <th class="col-sm-1 text-center">Progress</th>-->
			  </tr>
			</thead>
		   <tbody>
			<?php
			 foreach($users_arr  as $key => $value){
				$first_name=$value->first_name;
				$last_name=$value->last_name;
				   $stdInfo= userdetails($values['user_id']);
				  
				   if($value->is_active==1){
					   $status="Active";
					   $activeClass="style='color:Green'";
					 }else{
						 $status="Inactive";
						 $activeClass="style='color:Red'";
					  }
				  

				  if(!isset($value->expiry_date)){
				   $expiryDate="NA";
				  }else if($value->expiry_date=='0000-00-00 00:00:00'){
				     $expiryDate="NA";
				  }
				  else {
				  
				    $expiryDate=$value->expiry_date;
				    $expiryDate = date('d-m-Y',strtotime($expiryDate));
				  }
				  //echo "<pre>";print_r($users_arr);
					
				?>
				<tr class="col-sm-12 padd0" uid="<?php echo $values['user_id'];?>">
				  <td class="col-sm-2"><?php echo $value->first_name; ?></td>
				  <td class="col-sm-1 text-left"><?php echo $value->last_name;?></td>
				<td class="col-sm-3 text-left"><?php echo $value->email_id; ?></td>
				  <td class="col-sm-2 text-left paddLeft0">
				  <?php echo $value->center_name; ?></td> 
				  <td class="col-sm-1 text-left"><?php echo strtoupper($value->user_from); ?></td>
				   <td class="col-sm-1 text-left" <?php echo $activeClass;?>><?php echo $status; ?></td>
				   <td class="col-sm-1 text-left"><?php echo $expiryDate; ?></td>
				   <td class="col-sm-1 text-center"><a class="edit" href="<?php echo "createStudent.php?uid=".base64_encode($value->user_id); ?>"> <i class="fa fa-edit"> </i> Edit</a></td>
				   <td class="col-sm-1 text-center hide"><a href="javascript:void(0);" onclick="return updateStudentStatusFn('ajax/updateStudentStatus.php','<?php echo $value->user_id;?>','<?php echo $value->is_active; ?>');">
						<button type="button" id="btnAction"><?php if($value->is_active==1){?>Deactivate<?php }else{?>
						Activate
					<?php }?></button>
					</a></td>
				   <!-- <td class="col-sm-1 text-center"><a class="view" href="javascript:void(0)" onclick="stdProgress(<?php echo $values['user_id'];?>)"> View</a></td>-->
				 </tr>
			  <?php } ?>
			   <tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			  </tbody>
		     </table>
			 </div>
			 <?php }else{ ?>
                  
                 <div class="col-sm-12 noRecord text-center">Records not available.</div>
				
                   <?php } ?>
            
	 </div>
</section>

<?php include_once('../footer/adminFooter.php');?>
<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>
<script>



function updateStudentStatusFn(filePath,userId,uStatus){	
/* 		var r = confirm("Are you sure you want to change status");
		if (r == true) {
			if(uStatus==1){
				status=0;
			}else if(uStatus==0){
				status=1;
			}
			var selectBatch=$("#fld_batch").val();
			console.log(selectBatch)
			//alert("You've clicked Ok");
			$.post(filePath, {userId: userId,uStatus:status}, 
			function(data){ 
				if(data == 'nO'){ 
					alert('Error');
				}else{ 
				    window.location.href= window.location.href; 
				  
			});
		} else {
		  //alert("You've clicked Cancel");
		} */

}

</script>
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
	if(country==''){
			$('#center_id').find('option').remove().end().append('<option value="">Select <?php echo $center;?> </option>');
		}else{
	$.post('ajax/getCenterByCountry.php', {region_id: region_id,country: country}, function(data){ 
			if(data!=''){
				  $('#center_id').html(data);
				}else{
					$('#center_id').html('<option value="">Not Available</option>');
				}
		}); }
}); 


//On center chnage
 $('#center_id').change(function(){
	var region_id = $('#region').val();
	$('#student_txt').val('');
	$('#student_hidden').val('');
	var center_id = $('#center_id option:selected').val();
	if(center_id==''){
			$('#batch_id').find('option').remove().end().append('<option value="">Select Class </option>');
		}else{
	$.post('ajax/getBatchByCenter.php', {region_id: region_id,center_id: center_id}, function(data){ 
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

<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		var region_id = $('#region').val();
		var batch_id = $('#batch_id option:selected').val();
		var center_id = $('#center_id option:selected').val();
		var country = $('#country option:selected').val();
		var status = $('#status').val();
        var resultDropdown = $(this).siblings(".result_list");
		if(inputVal.length && inputVal.length>0){
            $.post("ajax/search_student.php", {uname: inputVal,batch_id: batch_id,center_id: center_id,country: country,status: status,region_id: region_id}).done(function(data){ 
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