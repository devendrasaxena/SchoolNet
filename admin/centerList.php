<?php include_once('../header/adminHeader.php');
$reportObj = new reportController();
//get all region
$region_arr=$centerObj->getRegionDetails();
//$country_list_arr=$reportObj->getCountryList();
$msg='';	
$err='';	
$succ='';	
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = $language[$_SESSION['language']]['state']." not saved. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = $language[$_SESSION['language']]['state']." created successfully.";
	}
	if($_SESSION['succ'] == '2'){
		$msg = $language[$_SESSION['language']]['state']." updated successfully.";
	}
	if($_SESSION['succ'] == '3'){
		$msg = $language[$_SESSION['language']]['state']." deleted successfully.";
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
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';


$page_param='';

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";

$center_id='';
$country='';
$center_txt='';
if (!empty($_REQUEST['center_txt'])) {
    $center_txt = trim(filter_query($_REQUEST['center_txt']));
	$options['center_txt'] = $center_txt;
	$page_param .= "center_txt=$center_txt&";
}

if (!empty($_REQUEST['country'])) {
    $country = trim(filter_query($_REQUEST['country']));
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['center_id'])) {
    $center_id = trim(filter_query($_REQUEST['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}
if (!empty($_SESSION['region_id'])) {
    $region_id = trim(filter_query($_SESSION['region_id']));
	$options['region_id'] = $region_id;
}
elseif(!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}



$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

//$_limit = 20;
if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);
$response_result= $centerObj->getCenterListByClient($options,$objPage->_db_start, $_limit,$order,$dir);

$objPage->_total = $response_result['total'];
$centers_arr = $response_result['result'];

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
	<div class="col-md-6 col-sm-6 text-left paddLeft0"><?php echo $language[$_SESSION['language']]['manage_states']; ?></div>
	<div class="col-md-6 col-sm-6 text-right paddRight0">
		<?php if($region_id!=5){?>
		   <a href='createCenter.php' class="btn btn-primary " title="<?php echo $language[$_SESSION['language']]['add_state']; ?>"><?php echo $language[$_SESSION['language']]['add_state']; ?></a> 
		<?php }?>
	</div>
 </div>
 <div class="clear"></div>
<section class="padder"> 
  <section class="marginBottom5 serachformDiv">
	<form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="centerList.php" >

		<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>" >

					 <select name="region_id" id="region" class="form-control "  >
						<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
						 <option value="All" <?php if($region_id=='All'){ ?> selected <?php } ?>>All</option>
						<?php 
						 foreach ($region_arr as $key => $value) {	
						  $regionName= $value['region_name'];
						  
						  if($_SESSION['role_id']==7 && $_SESSION['region_id']==$value['id']){
							  $selected ="selected";
						  }
						  elseif( $region_id==$value['id']){ $selected ="selected"; }
						  else{ $selected ="";} 
						?>
							<option <?php echo 'hide'; ?> value="<?php echo $value['id']; ?>" <?php echo $selected; ?>><?php echo $regionName;?></option>	
						  <?php 
						   } ?>
					   </select>
					
				</div>
		
		
		<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-left paddLeft0 ">
			  <div class="searchboxCSS search-box col-xs-12 padd0">
				<input name="center_txt" id="country"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['state']; ?>..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="") && (isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="")){?> value="<?php echo filter_query($_REQUEST['center_txt']);?>" <?php }?>/>
				<input name="center_id"  id="center_id"  type="hidden" class="form-control  parsley-validated" onchange="selectCountry(this);" <?php if((isset($_REQUEST['center_id']) && $_REQUEST['center_id']!="") && (isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="")){?> value="<?php echo filter_query($_REQUEST['center_id']);?>" <?php }?> />
				<div class="result_list"></div>
			</div>
		</div>

		
		
		
		
		
		<!-- <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left hide">
				
				<select name="center_id" id="center_id" class="form-control ">
					   
					   <option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
						if(count($center_list_arr_drop_down)>0){
						 $optionSelected = ($center_id == 'All') ? "selected" : "";
						  //echo '<option value="All" '.$optionSelected.'>All</option>';
						 foreach($center_list_arr_drop_down  as $key => $value){
								$centerId=$center_list_arr_drop_down[$key]['center_id'];
								$center_name=$center_list_arr_drop_down[$key]['name'];
								$optionSelected = ($center_id == $centerId) ? "selected" : "";
								echo '<option   value="'.$centerId.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$center_name.'</option>';
									
						 }
						}
						

					   ?>
					</select>
			</div> -->
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
			</div>			 
		</div>
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit" title="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['state']; ?>" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button> 
				  <a class="btn btn-sm btn-red btnwidth40" href="centerList.php" name="refresh" title="<?php echo $language[$_SESSION['language']]['refresh']?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
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
			   <?php if($succ=='1' || $succ=='3'){?>
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
			 <!--<th class="col-sm-4 text-left textUpper"><?php echo $centers; ?></th>
			 <th class="col-sm-2 text-left">MASTER LEARNERS</th>
			
			 <th class="col-sm-6 text-right">&nbsp;</th>-->
			 
			 <th class="col-sm-4 text-left"><a href="centerList.php?center_id=<?php echo $_REQUEST['center_id'];?>&country=<?php echo $_REQUEST['country'];?>&sort=name&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['state_name']; ?>
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
					</a></th> 
				
			 <!--<th class="col-sm-2 text-left">
			<a href="centerList.php?center_id=<?php echo $_REQUEST['center_id'];?>&country=<?php echo $_REQUEST['country'];?>&sort=country&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['country']; ?>
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'country' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'country' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
					</a>
					
			</th>	-->
					
			 <!--<th class="col-sm-2 text-left"><a href="centerList.php?center_id=<?php echo $_REQUEST['center_id'];?>&country=<?php echo $_REQUEST['country'];?>&sort=license_key&dir=<?php echo $dir?>" class="th-sortable">License
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'license_key' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'license_key' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
					</a></th>-->
			 <th class="col-sm-3 text-left"><a href="centerList.php?center_id=<?php echo $_REQUEST['center_id'];?>&country=<?php echo $_REQUEST['country'];?>&sort=created_date&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['activation_date']; ?>
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'created_date' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'created_date' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
					</a></th>
			
			 <th class="col-sm-3 text-left"><a href="centerList.php?center_id=<?php echo $_REQUEST['center_id'];?>&country=<?php echo $_REQUEST['country'];?>&sort=expiry_days&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['expiry_date']; ?>
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
					<!--</th>
						<th class="col-sm-1 text-left">
					Short Code	
					</th>-->
			<th class="col-sm-2 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>

		   </tr>
		 </thead>
		 <tbody>
		 <?php
		 $i = 1;
		// echo "<pre>";print_r($centers_arr);exit;	
			 foreach($centers_arr  as $key => $value){
				$centerId=$centers_arr[$key]['center_id'];
				$centerAddress=$centers_arr[$key]['address1']."  ".$centers_arr[$key]['city'].", ".$centers_arr[$key]['state'].", ".$centers_arr[$key]['country']." - ".$centers_arr[$key]['pincode'];
				$center_name=$centers_arr[$key]['name'];
				$country_name=$centers_arr[$key]['country'];
				
				$center_code=$centers_arr[$key]['code'];
				$email_id=$centers_arr[$key]['email_id'];
				$mobile=$centers_arr[$key]['mobile'];
				$license_key=$centers_arr[$key]['license_key'];
				$org_short_code=$centers_arr[$key]['org_short_code'];
				$created_date=$centers_arr[$key]['created_date']; 
				$created_date = date('d-m-Y',strtotime($created_date));
				$expiry_date = $centers_arr[$key]['expiry_date'];
				$expiry_days = $centers_arr[$key]['expiry_days'];
				if($expiry_date!="" && $expiry_date != '0000-00-00 00:00:00'){
					
					$expiry_date = date('d-m-Y H:i',strtotime($expiry_date));
				
				}else{
					$res_used_date = $reportObj->getLicenseUsedDate($license_key);
					
					$expiry_date = date('d-m-Y H:i',strtotime($res_used_date . "+".$expiry_days." days"));
					$expiry_date = date('d-m-Y H:i',strtotime($expiry_date));
					
				}
				
				$student_limit = $centers_arr[$key]['student_limit']; 
				
			/* if(B2C_CENTER==$centerId){
				$b2cBg="style='background-color:#0085a21a'";
				
			}else{
				$b2cBg="";
			} */
						 
			   // echo $date_created;exit;
		   //if(B2C_CENTER!=$centerId){?>  
		   
			<tr id="rowId<?php echo $i; ?>" class="toggler normal col-sm-12  <?php echo $hide; ?>" cid="<?php echo $centerId;?>" <?php echo $b2cBg;?> >
				<td class="col-sm-4 text-left fontSize12"><?php /* ?> <span id="spanId<?php echo $i; ?>" onClick="showPanel('ajax/getCenterLicense.php','<?php echo $centerId; ?>',this.id,'icon<?php echo $i; ?>','panelrowId<?php echo $i; ?>','tableId_<?php echo $i; ?>','<?php echo $i; ?>')"><i class="fa fa-plus" id="icon<?php echo $i; ?>"></i></span> <?php */ ?>  <?php echo $center_name;?></td>
				 
				<!--<td class="col-sm-2 text-left fontSize12"><?php echo $country_name;?>
				 </td> -->
				
				<!-- <td class="col-sm-2 text-left fontSize12" title=""><?php echo $license_key;?></td>-->
				<td class="col-sm-3 text-left fontSize12"><?php echo $created_date;?></td>
				<td class="col-sm-3 text-left fontSize12"><?php echo $expiry_date;?></td>
				  <!--<td class="col-sm-1 text-left">
					<?php echo $org_short_code;?>
					</td>-->
				<td class="col-sm-2 text-center fontSize12">
				   <a title="<?php echo $language[$_SESSION['language']]['edit']; ?>" href="<?php echo "createCenter.php?cid=".base64_encode($centerId);?>"> <i class="fa fa-edit <?php 
				  echo $hide;?>"></i> <?php echo $language[$_SESSION['language']]['edit']; ?></a> 
				 	<?php if($region_id!=5){?>  | 
				  <a title="<?php echo $language[$_SESSION['language']]['delete']; ?>" onclick="return deleteState('<?php echo base64_encode($centerId)?>');" href="javascript:void(0)" > <i class="fa fa-trash <?php 
				  echo $hide;?>"></i> <?php echo $language[$_SESSION['language']]['delete']; ?></a> 
				  	<?php }?>
				  </td>
				 </tr>	
			<tr id="panelrowId<?php echo $i; ?>" class="panelShow" style="display:none;">
              <td colspan="5" class="padd0 col-xs-12"><div class="subtable">
                  <table border='0' cellpadding='0' cellspacing='0' width='100%' class="table-fixed" id="tableId_<?php echo $i; ?>">
                  </table>
                </div></td>
            </tr>				 
		  <!--<tr class="col-sm-12" centerid="<?php echo $centerId;?>">
			<td class="col-sm-4 text-left"><?php echo $center_name;?></td>
			 <td class="col-sm-2 text-left"><?php echo $student_limit;?></td>
			
			 <td class="col-sm-6 text-right">
		
			   <a href="<?php echo "createCenter.php?cid=".base64_encode(centerId);?>" class="edit"> <i class="fa fa-edit <?php 
			  echo $hide;?>"> Edit</i> </a>
			  </td>
			 </tr>-->
		  <?php //} 
					$i++;		  
		       }?>
			
			  <tr>
				<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
				<?php } else{   ?>
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?> <br>
			Click <span class="capitalize">"<?php echo $language[$_SESSION['language']]['add_state']; ?>"</span> to <span class="textLower"><?php echo $language[$_SESSION['language']]['add_state']; ?></span>.</div>
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
<script>
//Country search
 $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
		var inputVal = $(this).val();
		$('#country_hidden').val('');
		$('#center_id').val('');
		var region_id = $('#region').val();
		var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_center.php", {cname: inputVal,region_id: region_id}).done(function(data){
				// Display the returned data in browser
				resultDropdown.html(data);
				resultDropdown.addClass("resultserchDiv");
			});
		} else{
			    resultDropdown.removeClass("resultserchDiv");
				resultDropdown.empty();
				$(".search-box").find('input[type="hidden"]').trigger('change');
				
		}
	   
    });
   
// Set search input value on click of result_list item
$(document).on("click", ".result_list option", function(){
	var conutry_name = $(this).val();
	$(this).parents(".search-box").find('input[type="text"]').val($(this).text());
	$(this).parent(".result_list").removeClass("resultserchDiv");
	$(this).parent(".result_list").empty();
	$(".search-box").find('input[type="hidden"]').val(conutry_name).trigger('change');

});


$('.search-box1 input[type="text"]').on("keyup input", function(e){
		 // get keycode of current keypress event
		var code = (e.keyCode || e.which);

		// do nothing if it's an arrow key
		if(code == 37 || code == 38 || code == 39 || code == 40) {
			e.preventDefault();
		}
        /* Get input value on change */
		var inputVal = $(this).val();
		$('#center_hidden').val('');
		var region_id = $('#region').val();
		var country = $('#country').val();
		var resultDropdown = $(this).siblings(".result_list1");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_center.php", {client_id: <?php echo $client_id;?>,cname: inputVal,region_id: region_id,hide_b2c: 6}).done(function(data){
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

// Set search input value on click of result_list item
$(document).on("click", ".result_list1 option", function(){
	var center_id = $(this).val();
	$(this).parents(".search-box1").find('input[type="text"]').val($(this).text());
	$(this).parent(".result_list1").removeClass("resultserchDiv");
	$(this).parent(".result_list1").empty();
	$(".search-box1").find('input[type="hidden"]').val(center_id).trigger('change');

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

var prviousId;
function showPanel(filePath, center_id, curId, iconId, panelId, targetId,cid){
	
	if( typeof($(".fa-minus")[0]) == "undefined"){
		
		
		showLoader(); 
		
		$.post(filePath, {center_code: center_id}, function(data){ $("#"+panelId).fadeIn(); $("#"+targetId).html(data);
			// Handler for .ready() called.
			   //var panelgridBgHeight =$(".panelgrid").height();
				 //$(".DashBoradTable").css("height",panelgridBgHeight+30+'px');
					 $('html, body').animate({
					 scrollTop: $("#"+targetId).offset().top-200
				    }, 'slow');	
				 hideLoader();
			});
		
		   
		 if(prviousId != curId){
			prviousId =  curId ;
			
		 }else{
			   $(".panelShow").fadeOut();
			   $('span > i').addClass( "fa-plus" ).removeClass( "fa-minus" );
			   $('.toggler').addClass("normal").removeClass( "bold" );
			   
		 }
		  //$("#"+rowId).show();
		  $('#'+curId).find('i.fa').toggleClass("fa-plus fa-minus");
		  $('#rowId'+cid).toggleClass("normal bold ");
		
		  
	}else{
		   $(".panelShow").fadeOut();
		   $('span > i').addClass( "fa-plus" ).removeClass( "fa-minus" );
		   $('.toggler').addClass("normal").removeClass("bold");
		 
		   
	}
	if(prviousId != curId){
		showLoader();
		$.post(filePath, {center_code: center_id}, function(data){ 
			$("#"+panelId).fadeIn(); 
			$("#"+targetId).html(data);
			// Handler for .ready() called.
			  //var panelgridBgHeight =$(".panelgrid").height();
				 //$(".DashBoradTable").css("height",panelgridBgHeight+30+'px');
				 $('html, body').animate({
				scrollTop: $("#"+targetId).offset().top-200
				}, 'slow');				 
			 hideLoader();
		 });
		$('#'+curId).find('i.fa').toggleClass("fa-plus fa-minus");
		$('#rowId'+cid).toggleClass("normal bold "); 
	   
	}
		 prviousId =  curId;
}



function deleteState(stateId){
	    status=0;
		$.post("ajax/chk_state_class.php", {stateId:stateId, action:"updatestatus"}).done(function(data){
			var obj = JSON.parse(data);
				if(obj.hasBatch==1){
					alert("Please unlink or delete classes from this state.");
					return;
				}
				else{ 
					var r = confirm("Are you sure you want to delete this?");
					if (r == true) {
						$.post("ajax/update_state.php", {stateId:stateId, action:"updatestatus"}).done(function(data){
							console.log(data);
							window.location.href='centerList.php';

						  });
					}
	
				}
			}); 
			  
		return;
		
	}


</script>