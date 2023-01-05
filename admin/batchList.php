<?php 
error_reporting(1);
include_once('../header/adminHeader.php');
$adminObj = new centerAdminController();
$reportObj = new reportController();

// echo "<pre>";
// print_r($country_list_arr);exit;
//Showing message after submit
$region_arr=$centerObj->getRegionDetails();
$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$batch not saved. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "$batch created successfully.";
	}
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '2'){
		$msg = "$batch updated successfully.";
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


$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'date_created';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'DESC';

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
$region_id = trim(filter_query($_REQUEST['region_id']));
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
    $center_id = trim(filter_query($_REQUEST['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['country'])) {
    $country = trim(filter_query($_REQUEST['country']));
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['batch_id'])) {
    $batch_id = trim(filter_query($_REQUEST['batch_id']));
	$options['batch_id'] = $batch_id;
	$page_param .= "batch_id=$batch_id&";
}


$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);
$response_result = $adminObj->getBatchList($options,$objPage->_db_start, $_limit,$order,$dir);

$objPage->_total = $response_result['total']; 
$batchInfoArr = $response_result['result'];


?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left paddLeft0"><?php echo $language[$_SESSION['language']]['classes']; ?></div>
	<div class="col-md-6 col-sm-6 text-right paddRight0"><?php if($editRight=='1'){?><a href='createBatch.php' title="<?php echo $language[$_SESSION['language']]['add_class']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['add_class']; ?></a><?php }?></div>
 </div>
<section class="padder">
	<form id="serachform" name="serachform"  method = "GET"  class="form-horizontal form-centerReg" action="batchList.php" >
	<section class="marginBottom5 serachformDiv">
       <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
       		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> hide <?php }?>" >

					 <select name="region_id" id="region" class="form-control "  >
						<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
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
			<!--<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
			 <select name="country" id="country" class="form-control ">
				<option value=""><?php echo $language[$_SESSION['language']]['select_country']; ?></option>
				
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
			--><div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0">
					<select name="center_id" id="center_id" class="form-control ">
					   <?php  $optiondisabled = ($center_id == 'All') ? "disabled" : ""; ?>
					   <option value="" <?php echo  $optiondisabled ;?>><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
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
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				
				<select class="form-control parsley-validated" id="batch_id" name="batch_id">
					 <?php  $optiondisabled = ($batch_id == 'All') ? "disabled" : ""; ?>
				<option value="" <?php echo  $optiondisabled ;?>><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
					 <?php 
						$batchInfo = $reportObj->getBatchDeatils($center_id,$country,$region_id);
						if(count($batchInfo)>0 && $center_id!=""){
							 
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
		  </div>		
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right text-right padd0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave"  title="<?php echo $language[$_SESSION['language']]['classes']. ' '. $language[$_SESSION['language']]['search'] ; ?>" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button> <!--<button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>-->
				<a class="btn btn-sm btn-red" href="batchList.php" name="refresh" title="<?php echo $language[$_SESSION['language']]['refresh']; ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
		 </div>
		 <br>
				 <br>
		 <label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
			 	<option value="10" <?php echo $_limit==10? 'selected':'' ?> >10 </option>
			 	<option value="25"<?php echo $_limit==25? 'selected':'' ?> >25 </option>
			 	<option value="50" <?php echo $_limit==50? 'selected':'' ?> >50 </option>
			 	<option value="100" <?php echo $_limit==100? 'selected':'' ?> >100 </option>
			 </select>
	</form>
  </section>	

  <?php if($err!=''){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
	   
		 <?php if($succ!=''){?>
		  <div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
		  <?php } ?>
   <div class="clear"></div>

  <section class="panel panel-default">
  
		<div class="panel-body">
    
		<?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
	    <div class="table-responsive">
	    <table class="table table-border dataTable table-fixed">
	    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-6"><a href="batchList.php?sort=batch_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['class_name']; ?>
					<span class="th-sort"> 
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'batch_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'batch_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			   <th class="col-sm-3"><a href="batchList.php?sort=center_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['state_name']; ?>
					<span class="th-sort"> 
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'center_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'center_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>

			  <th class="col-sm-3 text-center"><?php if($editRight=='1'){?><?php echo $language[$_SESSION['language']]['action']; }?>
					<span class="th-sort"> </th>
			  </tr>
			</thead>
	  
		<tbody> 
		<?php $i=1;
		//$batchInfoArr=array_filter($batchInfoArr);
         // echo "<pre>";print_r($batchInfoArr);
		 
		foreach($batchInfoArr as $key => $value){
		  	$batch_id=$value['batch_id'];
			$center_id=$value['center_id'];
			$centerName=$value['center_name'];
		    $roleID=2;
		?>
			 <tr id="row<?php echo $i;?>" class="col-sm-12 padd0" bid="<?php echo $value['batch_id']; ?>">
			  <td class="col-sm-6"><?php echo $value['batch_name'];?></td>
			    <td class="col-sm-3"><?php echo $centerName;?></td>
			  <td class="col-sm-3 text-center">
				<?php if($editRight=='1'){?><a class="edit" title="<?php echo $language[$_SESSION['language']]['edit']?>" href="<?php echo "createBatch.php?bid=".base64_encode($value['batch_id'])."&cid=".base64_encode($value['center_id']); ?>"> <i class="fa fa-edit"></i> <?php echo $language[$_SESSION['language']]['edit']; ?>
				<span class="th-sort"> </a><?php }?></td>
			  </tr>
	   <?php  $i++; } ?>
	   <tr>
			<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td>
			</tr>
			<?php } else{   ?>
			 
			<div class="col-xs-12 noRecord text-center">Records is not available. <?php if($editRight=='1'){?><br>Click <span class="capitalize">"Add <?php echo $batch;?>"</span> to add <?php echo $batch;?>.<?php }?></div>
		</tbody>
		<?php 	} ?>
	   
	</table>
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
//On region chnage
 $('#region').change(function(){
	var region = $('#region option:selected').val();
	$('#center_id').html('<option value="">Select Organization</option>');
	$('#batch_id').html('<option value="">Select class</option>');
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
	var region_id = $('#region').val();
	var country = $('#country option:selected').val();
	$('#batch_id').html('<option value="">Select class</option>');
	if(country==''){
			$('#center_id').find('option').remove().end().append('<option value="">Select Organization </option>');
		}else{
	$.post('ajax/getCenterByCountry.php', {country: country ,region_id: region_id}, function(data){ 
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
	var country = $('#country').val();
	var center_id = $('#center_id option:selected').val();
	if(center_id==''){
			$('#batch_id').find('option').remove().end().append('<option value="">Select Class </option>');
		}else{
	$.post('ajax/getBatchByCenter.php', {region_id: region_id,country: country,center_id: center_id}, function(data){ 
			if(data!=''){
				  $('#batch_id').html(data);
				}else{
					$('#batch_id').html('<option value="">Not Available</option>');
				}
		}); }
});

</script>