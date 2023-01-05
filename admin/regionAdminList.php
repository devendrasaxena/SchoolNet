<?php include_once('../header/adminHeader.php');
	//echo "<pre>";print_r($centers_arr);exit;	
//echo count($centers_arr);	
$reportObj = new reportController();
$regionAdminRole=7;
$options = array();
$options['client_id'] = $client_id;
$options['role_id'] = 7;
$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'us.first_name';
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
$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

//$_limit = 20;
if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

$centerObj = new centerController();
$region_arr=$centerObj->getRegionDetails();




 if(!empty($_REQUEST['region_id'])){	
	$region_id = trim(filter_query($_REQUEST['region_id']));
	$country_list_arr=$reportObj->getCountryList($region_id);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}elseif(!empty($_SESSION['region_id'])){
	$options['region_id'] = $_SESSION['region_id'];
	$region_id=$_SESSION['region_id'];
	$country_list_arr=$reportObj->getCountryList($region_id);
	$options['region_id'] = 'All';//$region_id;
}else{
	$options['region_id'] = $region_id;
	$region_id='';
}
if (!empty($_REQUEST['student'])) {
    $student_id = trim(filter_query($_REQUEST['student']));
	$options['student_id'] = $student_id;
	$page_param .= "student_id=$student_id&";
}
if (!empty($_REQUEST['status']) || $_REQUEST['status'] == '0') {
    $status = trim(filter_query($_REQUEST['status']));
	$options['status'] = $status;
	$page_param .= "status=$status&";
}

if (!empty($_REQUEST['student'])) {
	$student_id = trim(filter_query($_REQUEST['student']));
	$options['student'] = $student_id;
	$page_param .= "student=$student_id&";
}
if (!empty($_REQUEST['student_txt'])) {
	$student_txt = trim(filter_query($_REQUEST['student_txt']));
	$options['student_txt'] = $student_txt;
	$page_param .= "student_txt=$student_txt&";
}

$response_result=$centerObj->getRegionAdminDetails($options,$objPage->_db_start, $_limit,$order,$dir);
$objPage->_total = $response_result['total'];
$regionAdminArr = $response_result['result'];
//echo "<pre>";print_r($regionAdminArr);exit;	
$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = $language[$_SESSION['language']]['centre']." Admin not saved. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = $language[$_SESSION['language']]['centre']." Admin created successfully.";
	}
	if($_SESSION['succ'] == '2'){
		$msg = $language[$_SESSION['language']]['centre']." Admin updated successfully.";
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
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'center_id';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'DESC';


$page_param='';

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";

$center_id='';
$country='';
if (!empty($_REQUEST['country'])) {
    $country = trim(filter_query($_REQUEST['country']));
	$options['country'] = $country;
	$page_param .= "country=$country&";
}



//$_limit = 20;


//switch order
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

?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"> <?php echo $language[$_SESSION['language']]['centre_admins']; ?></div>
	<div class="col-md-6 col-sm-6 text-right"><a href='createRegionAdmin.php' class="btn btn-primary "><?php echo "Add Region Admin" ; ?>  </a> </div>
 </div>
 <div class="clear"></div>
<section class="padder"> 
  <section class="marginBottom5 serachformDiv">
	<form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="regionAdminList.php" >

	  <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left paddLeft0">
			 <select name="region_id" id="region" class="form-control "  >
			<option value=""><?php echo $language[$_SESSION['language']]['select_centre']; ?> </option>
			 <option value="All" <?php if($region_id=='All'){ ?> selected <?php } ?>>All</option>
			<?php 
			 foreach ($region_arr as $key => $value) {	
			  $regionName= $value['region_name'];
			  
				if( $_REQUEST['region_id']==$value['id']){ $selected ="selected"; }
			  else{ $selected ="";} 
			?>
				<option <?php echo $hide; ?> value="<?php echo $value['id']; ?>" <?php echo $selected; ?>><?php echo $regionName;?></option>	
			  <?php 
			   } ?>
		   </select>
		</div>
		<!--<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  text-left ">
					<select id="status" name="status" onchange="selectStatus(this);" class="form-control " >
								<option value="">Status</option>
									<option <?php //if($status=='1'){ echo 'selected';}?> value="1">Active</option>	
									<option <?php //if($status=='0'){ echo 'selected';}?> value="0">Inactive</option>
							   </select>
			 </div>-->
		<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-left paddLeft0">
				
				<div class="searchboxCSS search-box col-xs-11 padd0 pull-right">
							<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['centre_admins'].' '.$language[$_SESSION['language']]['name_or_email'].' ' .$language[$_SESSION['language']]['search']; ?> ..." class="form-control  parsley-validated"  <?php if($_REQUEST['student_txt']!=""){?> value="<?php echo filter_query($_REQUEST['student_txt']);?>" <?php }?>/>
							<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo filter_query($_REQUEST['student']);?>" <?php }?>/>
					<div class="result_list"></div>
				</div>
				
				
				
			</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right paddRight0">
			</div>			 
		</div>
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button> 
				 <a class="btn btn-sm btn-red" href="regionAdminList.php" name="refresh" title=" Refresh" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			 </div> 
			 
			  <label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
			 	<option value="10" <?php echo $_limit==10? 'selected':'' ?> >10 </option>
			 	<option value="25"<?php echo $_limit==25? 'selected':'' ?> >25 </option>
			 	<option value="50" <?php echo $_limit==50? 'selected':'' ?> >50 </option>
			 	<option value="100" <?php echo $_limit==100? 'selected':'' ?> >100 </option>
			 </select>
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
  
  <div class="table-responsive">
	  <table class="table table-border dataTable table-fixed">
		 <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
		 <thead  class="fixedHeader">
		   <tr class="col-sm-12">
			
			 <th class="col-sm-3 col-xs-3 col-sm-3 text-left"><a href="regionAdminList.php?region_id=<?php echo $region_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&sort=first_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['name']; ?>
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
			 <th class="col-sm-3  col-xs-3 col-sm-3 text-left"><a href="regionAdminList.php?region_id=<?php echo $region_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&sort=region_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['centres']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'region_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'region_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a> </th>
			 <th class="col-sm-3 col-xs-3 col-sm-3 text-left"><a href="regionAdminList.php?region_id=<?php echo $region_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&sort=email_id&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['login_id']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a> </th>
			<th class="col-sm-3 col-xs-3 col-sm-3 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>

		   </tr>
		 </thead>
		 <tbody>
		 <?php
		    //echo "<pre>";print_r($regionAdminArr);exit;	
			 foreach($regionAdminArr  as $key => $value){
				 
				$regionData=$centerObj->getRegionDataByID($value->region_id);
				$region_name=$regionData[0]["region_name"];	
				$email_id=$value->email_id;	

			?>  
		   
			<tr class="col-sm-12" cid="<?php echo $value->user_id;?>">
				  
				<td class="col-sm-3 col-xs-3 col-sm-3 text-left fontSize12"><?php echo $value->first_name;?>
				 </td> 
				
				 <td class="col-sm-3 col-xs-3 col-sm-3 text-left fontSize12" title=""><?php echo $region_name;?></td>
				<td class="col-sm-3 text-left fontSize12" title=""><?php echo $email_id;?></td>
				
				  <td class="col-sm-3 col-xs-3 col-sm-3 text-center fontSize12">
				   <a href="<?php echo "createRegionAdmin.php?rid=".base64_encode($value->user_id);?>"> <i class="fa fa-edit <?php 
				  echo $hide;?>"></i> <?php echo $language[$_SESSION['language']]['edit']; ?></a> 
				  
				  </td>
				 </tr>		 
		 
		  <?php //}  
		       }?>
			
			  <tr>
				<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
				<?php } else{   ?>
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?> <br>
			Click <span class="capitalize">"<?php echo $language[$_SESSION['language']]['add_center_admin']; ?>"</span> to add <span class="textLower">"<?php echo $language[$_SESSION['language']]['centre_admins']; ?></span>.</div>
		  <?php }?>  
		</tbody>
	 </table>
   </div>
</section>

</section>
<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>
<?php include_once('../footer/adminFooter.php'); ?>
<script type="text/javascript">
//On region chnage
 $('#region').change(function(){
	$('#student_txt').val('');
	$('#student_hidden').val('');
	
}); 
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val(); 
		$('#student_hidden').val('');
		var region_id = $('#region option:selected').val();
		var status = $('#status').val();
        var resultDropdown = $(this).siblings(".result_list");
		if(inputVal.length && inputVal.length>0){
            $.post("ajax/search_region_admin.php", {uname: inputVal,region_id:  region_id,status: status}).done(function(data){  
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