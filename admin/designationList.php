<?php include_once('../header/adminHeader.php');
	//echo "<pre>";print_r($designation_arr);exit;	
//echo count($designation_arr);	
$reportObj = new reportController();
$designationObj = new designationController();

$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);

	
}

$options = array();
$options['client_id'] = $client_id;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'td.designation';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';


$page_param='';

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";


$center_id='';
$country='';
$designation='';
if (!empty($_REQUEST['designation'])) {
$designation = trim(filter_query($_REQUEST['designation']));
$options['designation'] =  $designation;
$page_param .= "designation=$designation&";
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



$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

//$_limit = 20;
if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);
$response_result= $designationObj->getDesignationList($options,$objPage->_db_start, $_limit,$order,$dir); 

$objPage->_total = $response_result['total'];
$designation_arr = $response_result['result']; 

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
	<div class="col-md-6 col-sm-6 text-left paddLeft0"><?php echo $language[$_SESSION['language']]['manage_designation']; ?> </div>
	<div class="col-md-6 col-sm-6 text-right paddRight0"><a href='createDesignation.php' title="<?php echo $language[$_SESSION['language']]['add_designation']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['add_designation']; ?></a> </div>
 </div>
 <div class="clear"></div>
<section class="padder"> 
  <section class="marginBottom5 serachformDiv">
<form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="designationList.php" >

		<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		
			<!-- <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>" >

					 <select name="region_id" id="region" class="form-control "  >
						<option value="">Select Centre</option>
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
					
				</div>  -->
		
		
	
		
		<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-left padd0">
				
				  <div class="searchboxCSS search-box1 col-xs-12 padd0">
				<input name="designation"  id="center_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['designation']; ?>..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['designation']) && $_REQUEST['designation']!="") && (isset($_REQUEST['designation']) && $_REQUEST['designation']!="")){?> value="<?php echo filter_query($_REQUEST['designation']);?>" <?php }?> />
				
				<div class="result_list1"></div>
				</div>
				
				
			</div>
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
			</div>			 
		</div>
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" title="<?php echo $language[$_SESSION['language']]['search_designation']; ?> " id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button> 
				
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
		
			 
			 <th class="col-sm-2 text-left"><a href="designationList.php?sort=desination_short_code&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['designation_short_code']; ?>
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
					 <th class="col-sm-3"> <?php echo $language[$_SESSION['language']]['designation']; ?>
					</th>
					<th class="col-sm-3"> <?php echo $language[$_SESSION['language']]['description']; ?>
					</th>
					<th class="col-sm-2"> <?php echo $language[$_SESSION['language']]['created_date']; ?>
					</th>
				
			<th class="col-sm-2 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>

		   </tr>
		 </thead>
		 <tbody>
		 <?php
		 $i = 1;
		// echo "<pre>";print_r($designation_arr);exit;	
			 foreach($designation_arr  as $key => $value){
				$designation_id=$designation_arr[$key]['id'];
				$desination_short_code=$designation_arr[$key]['desination_short_code'];
				$designation=$designation_arr[$key]['designation'];
				$description=$designation_arr[$key]['description'];
				$created_date=$designation_arr[$key]['created_date']; 
				$created_date = date('d-m-Y',strtotime($created_date));
				?>
		   
			<tr id="rowId<?php echo $i; ?>" class="toggler normal col-sm-12  <?php echo $hide; ?>" cid="<?php echo $district_id;?>"  >
				<td class="col-sm-2 text-left fontSize12">
				<?php echo $desination_short_code;?></td>
				 
				<td class="col-sm-3 text-left fontSize12"><?php echo $designation;?>
				 </td>
				
			
			
				<td class="col-sm-3 text-left fontSize12"><?php echo $description;?></td>
				<td class="col-sm-2 text-left fontSize12"><?php echo $created_date;?></td>
				
				<td class="col-sm-2 text-center fontSize12">
				   <a title="<?php echo $language[$_SESSION['language']]['edit']; ?>" href="<?php echo "createDesignation.php?did=".base64_encode($designation_id);?>"> <i class="fa fa-edit <?php 
				  echo $hide;?>"></i> <?php echo $language[$_SESSION['language']]['edit']; ?></a>
				 
				  
				  </td>
				 </tr>	
						 
		  <?php
					$i++;		  
		       }?>
			
			  <tr>
				<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
				<?php } else{   ?>
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?><!-- <br>
			Click <span class="capitalize">"Add <?php echo $tehsil; ?>"</span> to add <span class="textLower"><?php echo $tehsil; ?></span>. --></div>
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
		var region_id = $('#region').val();
		var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_country.php", {cname: inputVal,region_id: region_id}).done(function(data){
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
			$.post("ajax/search_designation.php", {cname: inputVal}).done(function(data){
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



</script>