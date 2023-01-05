<?php include_once('../header/adminHeader.php');
$region_arr=$centerObj->getRegionDetails(); 
$msg='';	
$err='';	
$succ='';
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "Access code not saved. Please try again.";
	}
	if($_SESSION['error'] == '2'){
		$msg = "Access code generation limit exceed.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "Access code  created successfully.";
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
 
$center_id='';
$accessCode='';
$status='';
$options = array();


$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? $_GET['sort'] : 'code_created_date';
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




if(!empty($_SESSION['region_id'])){
	$region_id=$_SESSION['region_id'];
	$options['region_id'] = $region_id;
}else if(!empty($_REQUEST['region_id'])){	
	$region_id = trim($_REQUEST['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}else{
	$options['region_id'] = '';
	$country_list_arr=$reportObj->getCountryList();
	$region_id='';
}	
	
	
	
	
 if (!empty($_REQUEST['center_id'])) {
    $center_id = trim($_REQUEST['center_id']);
    $center_txt = trim($_REQUEST['center_txt']);
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
	$page_param .= "center_txt=$center_txt&";
 }

 if(!empty($_REQUEST['acccodeoremail'])){
 	$accessCode=trim($_REQUEST['acccodeoremail']);
 	$options['accessCode'] = $accessCode;
 	$page_param .= "acccodeoremail=$accessCode&";
 }

if($_REQUEST['status']!=''){

 	$status=$_REQUEST['status']; 
 	$options['status'] = $status;
 	$page_param .= "status=$status&";
 }
	
	
$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

	$_limit = PAGINATION_LIMIT;
	$objPage = new Pagination($_page, $_limit);
	$response_result = $centerObj->searchAcessDataByCenterIdAndAccessCode($options,$objPage->_db_start, $_limit,$order,$dir);

	$objPage->_total = $response_result['total'];
	$accessData = $response_result['result'];

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
	<div class="col-md-6 col-sm-6 text-left">Access Codes</div>
	<div class="col-md-6 col-sm-6 text-right"><a href='generateAccessCode.php' class="btn btn-primary marginTop0">Generate Access Codes</a></div>
 </div>
 <div class="clear"></div>
 <section class="padder"> 
	<section class="marginBottom5 serachformDiv">
	<form id="serachform" name="serachform" method="GET" class="form-horizontal form-centerReg" action="user_access_list.php" >
       <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> hide <?php }?>" >

		<select name="region_id" id="region" class="form-control "  >
		<option value="">Select Centre</option>
		<option value="All" <?php if($region_id=='All'){ ?> selected <?php } ?>>All</option>
		<?php 
		foreach ($region_arr as $key => $value){	
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
		
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				
				<div class="searchboxCSS search-box1 col-xs-12 padd0">
				<input name="center_txt"  id="center_txt"  type="text" autocomplete="off" placeholder="Search <?php echo $center; ?>..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['center_id']) && $_REQUEST['center_id']!="") && (isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="")){?> value="<?php echo $_REQUEST['center_txt'];?>" <?php }?> />
				<input name="center_id"  id="center_hidden"  type="hidden" class="form-control  parsley-validated"   <?php if((isset($_REQUEST['center_id']) && $_REQUEST['center_id']!="") && (isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="")){?> value="<?php echo $_REQUEST['center_id'];?>" <?php }?> />
				<div class="result_list1"></div>
				</div>
				
				
			</div>

			
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3  text-left">
				<select id="status" name="status" class="form-control " >
					<option value="">Select Status</option>
					<option  value="1" <?php if($status == "1") {echo  "selected";} ?>>Used</option>	
					<option  value="0" <?php if($status == "0") {echo  "selected";} ?>>Not Used</option>
								
				</select>	
			</div>
			
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3  text-left paddLeft0">
				<div class="searchboxCSS search-box search-box col-xs-12 padd0 pull-right">
					<input name="acccodeoremail"  id="acccodeoremail"  type="text" autocomplete="off" placeholder="Search access code..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['acccodeoremail']) && $_REQUEST['acccodeoremail']!="")){?> value="<?php echo $_REQUEST['acccodeoremail'];?>" <?php }?>/>
					
				  <div class="result_list"></div>
			   </div>
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3  text-left paddLeft0">
				 
			 </div>
		</div>
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right text-right padd0">
					<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> Search</button> 
					<!--<a class="btn btn-sm btn-red btnwidth40" href="license_list.php"  name="refresh" title=" Refresh" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>-->
			</div>		
		
			 <!-- <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>-->
			  </form>
			  <div class="col-sm-12">
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
    </div>
	</section>	
	 
 	
  <section class="panel panel-default ">
	<div class="panel-body marginBottom10">
    <div class="table-responsive">
		  

	<?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
	<table class="table table-border dataTable table-fixed">
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2 text-left"><a href="user_access_list.php?center_id=<?php echo $center_id; ?>&status=<?php echo $status; ?>&acccodeoremail=<?php echo $accessCode; ?>&center_txt=<?php echo $_REQUEST['center_txt']; ?>&sort=access_code&dir=<?php echo $dir; ?>" class="th-sortable">Access Code
			  <span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'access_code' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'access_code' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			  <th class="col-sm-2 text-left"><a href="user_access_list.php?center_id=<?php echo $center_id; ?>&status=<?php echo $status; ?>&acccodeoremail=<?php echo $accessCode; ?>&center_txt=<?php echo $_REQUEST['center_txt']; ?>&sort=organization_id&dir=<?php echo $dir; ?>" class="th-sortable">
			 <?php echo $center; ?> Name<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'organization_id' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'organization_id' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
					<th class="col-sm-2 text-left">Used By</th>
			  <th class="col-sm-2 text-center"><a href="user_access_list.php?center_id=<?php echo $center_id; ?>&status=<?php echo $status; ?>&acccodeoremail=<?php echo $accessCode; ?>&center_txt=<?php echo $_REQUEST['center_txt']; ?>&sort=code_created_date&dir=<?php echo $dir; ?>" class="th-sortable">Created Date<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'code_created_date' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'code_created_date' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			  
			 <!-- <th class="col-sm-2 text-left">Login Id</th>
			   <th class="col-sm-1 text-center">Use Mode</th>-->
			  
			<th class="col-sm-2 text-center">Status</th>
			  <th class="col-sm-2 text-center">Used Date</th>
			
			  </tr>
			</thead>
	<tbody id="tbodyRecord">
	<?php 
		foreach($accessData as $key=>$accDetail){
			//echo "<pre>";print_r($accessData);exit; 
			if($accDetail['access_code']!=''){
				$access_code=$accDetail['access_code'];
			}else{
				$access_code='-';
			}
			
			$organization_id=$accDetail['organization_id'];
			 foreach ($centers_arr as $key => $value) {	
				 if($value['center_id']==$organization_id){
					 $centerName= $value['name'];
				 }
			 }
		  if($accDetail['code_used_by_name']!=''){
				$used_by_name=$accDetail['code_used_by_name'];
			}else{
				$used_by_name='-';
			}
			if($accDetail['code_used_by_email'] ){
				$code_used_by_email=$accDetail['code_used_by_email'];
			}else{
				$code_used_by_email='-';
			}
			if($accDetail['code_used_by_b2c']=='no' ){
				$code_used_by_b2c='B2B';
			}else{
				$code_used_by_b2c='B2C';
			}
			
			if($accDetail['code_created_date']!=''){
				$code_created_date=date('d-m-Y',strtotime($accDetail['code_created_date']));
			}else{$code_created_date='-';}
			
			if($accDetail['code_used_date']!=''){
				$code_used_date=date('d-m-Y',strtotime($accDetail['code_used_date']));
			
			}else{$code_used_date='-';
			
			}

		   if($accDetail['access_code_status']==1 && $accDetail['code_used_date']!=''){
				$status='Used';
				$bgcolor="style='color:Green'";
			}else{
				$status='Not Used';
				$bgcolor="style='color:Red'";
			}

		?>
		<tr class="col-sm-12 padd0" >
				
				  <td class="col-sm-2 text-left"> <?php echo $access_code;?></td>
                  <td class="col-sm-2 text-left"><?php echo $centerName; ?></td>
				  <td class="col-sm-2 text-left"><?php echo $used_by_name;?></td>
				   <td class="col-sm-2 text-center"><?php echo $code_created_date;?></td>
				  
				  <!-- <td class="col-sm-2 text-left"><?php echo $code_used_by_email;?></td>
				 <td class="col-sm-1 text-center"><?php echo $code_used_by_b2c;?></td>-->
				
				 <td class="col-sm-2 text-center" <?php echo $bgcolor;?>><?php echo $status;?> </td>
				  <td class="col-sm-2 text-center"> <?php echo $code_used_date;?></td>
				  
				
				 </tr>
		<?php } ?>
		<tr>
			<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td>
			</tr>
			</tbody>
			 </table>
			<?php } else{   ?>
			 <div class="col-xs-12 noRecord text-center"> Records not available.</div>
		
		<?php 	} ?>
	
	
	</div>
	</div>
	
</section>
</div>	
<?php include_once('../footer/adminFooter.php'); ?>
<script>
$(document).ready(function () {


 $('#region').change(function(){
	$('#center_hidden').val('');
	$('#center_txt').val('');
	$('#acccodeoremail').val('');
	$('#status').prop('selectedIndex',0);
	
}); 
 $('#status').change(function(){
	$('#acccodeoremail').val('');
	
}); 


$('.search-box1 input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
		var inputVal = $(this).val();
		$('#center_hidden').val('');
		var region_id = $('#region').val();
		var resultDropdown = $(this).siblings(".result_list1");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_center.php", {client_id: <?php echo $client_id;?>,cname: inputVal,region_id:region_id}).done(function(data){
				// Display the returned data in browser
				resultDropdown.addClass("resultserchDiv");
				resultDropdown.html(data);
			});
		} else{
				resultDropdown.removeClass("resultserchDiv");
				resultDropdown.empty();
				$(".search-box1").find('input[type="hidden"]').trigger('change');
				
				
		}
	   
    });

$('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
		
		var inputVal = $(this).val();
		var center_id = $('#center_hidden').val(); 
		var region_id = $('#region').val();
		var status = $('#status').val();
		var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_access_code.php", {accesscode: inputVal, center_id:center_id, region_id:region_id, status:status }).done(function(data){
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
  $(document).on("click", ".result_list1 option", function(){
	var center_id = $(this).val();
	$(this).parents(".search-box1").find('input[type="text"]').val($(this).text());
	$(this).parent(".result_list1").removeClass("resultserchDiv");
	$(this).parent(".result_list1").empty();
	$(".search-box1").find('input[type="hidden"]').val(center_id).trigger('change');

   });	

    $(document).on("click", ".result_list option", function(){
	var center_id = $(this).val();
	$(this).parents(".search-box").find('input[type="text"]').val($(this).text());
	$(this).parent(".result_list").removeClass("resultserchDiv");
	$(this).parent(".result_list").empty();
   });
	
});
  
</script>
