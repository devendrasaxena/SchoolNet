<?php include_once('../header/adminHeader.php');

/*  $serviceURL =$license_data_url; // path define in config 
$request = curl_init($serviceURL);
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'getCurrentCustomerLicenses', 'customer_id' => $customer_id));
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($request);
curl_close($request);
$res = json_decode($res); 
//print_r($res);exit;
$ret=addUpdateLicenses($customer_id,$res); */
////////////////
$_html_relative_path='../../';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
$region_arr=$centerObj->getRegionDetails();
$reportObj = new reportController();
$dir = "";

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
$status='';
$license='';

$options = array();
$options['customer_id'] = $lic_customer_id;


$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'tcl.license_created_date';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'DESC';


/* if (!empty($_SESSION['region_id'])) { 
    $region_id = trim($_SESSION['region_id']);
	$options['region_id'] = $region_id;
}
else */
if (!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
 }
if (!empty($_REQUEST['status'])) {
    $status = trim(filter_query($_REQUEST['status']));
	$options['status'] = $status;
	$page_param .= "status=$status&";
}
if (!empty($_REQUEST['license_type'])) {
    $license_type = trim(filter_query($_REQUEST['license_type']));
	$options['license_type'] = $license_type;
	$page_param .= "license_type=$license_type&";
}

if (!empty($_REQUEST['center_id'])) {
    $center_id = trim(filter_query($_REQUEST['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['license'])) { 
    $license = trim(filter_query($_REQUEST['license']));
	$options['license'] = $license;
	$page_param .= "license=$license&";
}

$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

//$_limit = 20;
$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $clientObj->getLicenseListByCenterAndStatus($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $clientObj->getLicenseListByCenterAndStatus($options,$objPage->_db_start, $_limit,$order,$dir);
}
$objPage->_total = $response_result['total'];
$licenseList = $response_result['result'];

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


//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
       // $file = 'learners_report_'.time().'.xls';
        $file = 'license_list_'.time().'.csv';
        /* header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); */
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Licenses</th>';
        $export_data .= '<th>'. ltrim($teacher,"@-+=").' Limit</th>';
        $export_data .= '<th>'.ltrim($student,"@-+=").' Limit</th>';
        $export_data .= '<th>Expiry Date</th>';
        $export_data .= '<th>Created Date</th>';
        $export_data .= '<th>License Type</th>';
        $export_data .= '<th>'.ltrim($center,"@-+=").'</th>';
        $export_data .= '<th>Requested By</th>';
        $export_data .= '<th>Status</th>';
        $export_data .= '<th>Used Date</th>';
		$export_data .= '</tr>';

        if (count($licenseList) > 0) {
            $i = 0;
             foreach($licenseList as $key=>$licenseDetail){

				if($licenseDetail->issued_date!=''){
					$issued_date=date('d-m-Y',strtotime($licenseDetail->issued_date));
				}
				else{$issued_date='-';}
				
				if($licenseDetail->license_used_by){
					$license_used_by=$licenseDetail->license_used_by;
				}
				else{$license_used_by='-';
				   $usedBgcolor="";
				}
				if($licenseDetail->license_type!=''){
					$license_type=$licenseDetail->license_type;
					}
				else{
					$license_type='-';
					}
				
				$used_date = $licenseDetail->used_date;
				$expiry_days = $licenseDetail->lic_exp_day_af_reg_lan;
				$expiry_date = $licenseDetail->lic_req_license_expiry_lan;
				
				if($used_date!="" &&  $used_date != '0000-00-00 00:00:00'){
					
					if($expiry_date!="" && $expiry_date != '0000-00-00'){

						$expiry_date = date('d-m-Y',strtotime($expiry_date));

					}else{
						$expiry_date = date('d-m-Y',strtotime($used_date . "+".$expiry_days." days"));

					}
					$used_date=date('d-m-Y',strtotime($licenseDetail->used_date));
					
				}else{
					$expiry_date = '-';
					$used_date='-';
				}
				
				if($licenseDetail->license_created_date!=''){
					$license_created_date=date('d-m-Y',strtotime($licenseDetail->license_created_date));
				}
				else{$license_created_date='-';}

				if($licenseDetail->license_used_by_name!=''){
					$license_used_by=$licenseDetail->license_used_by_name;
				}
				else{
					$license_used_by='-';
					}
					
				if($licenseDetail->license_status==1 && $licenseDetail->used_date!='' && $licenseDetail->used_date!='0000-00-00 00:00:00'){
						$status='Active';$bgcolor="style='color:Green'";
					}
					elseif($licenseDetail->license_status==1 && ($licenseDetail->used_date=='' ||  $licenseDetail->used_date=='0000-00-00 00:00:00')){
						$status='Available';
						$bgcolor="style='color:#a96500'";
					}
					elseif($licenseDetail->license_status==1 && $licenseDetail->used_date!='' && $licenseDetail->used_date!=''){
						$status='Available';
						$bgcolor="style='color:#a96500'";
					} 
					elseif($licenseDetail->license_status==4){
						
						$status='Active';$bgcolor="style='color:Green'";
						if($expiry_date!='-'){
							$current_date = date('Y-m-d');
							$dateTimestamp1 = strtotime($current_date); 
							$dateTimestamp2 = strtotime($expiry_date); 
							if($dateTimestamp1 > $dateTimestamp2) {
								$status='Expired';
								$bgcolor="style='color:Red'";
							}
						}
						
					} 
						
					if($licenseDetail->lic_req_by_user!="") {
									$requested_by = $licenseDetail->full_name;
					}else{
						$requested_by = '-';
					}
                $i++;
                
           
			    $export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' .  ltrim($licenseDetail->license_value,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($licenseDetail->trainer_limit,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($licenseDetail->student_limit,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($expiry_date,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($license_created_date,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($license_type,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($license_used_by,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($requested_by,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($status,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($used_date,"@-+="). '</th>';
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

		foreach($html->find('tr') as $element)
		{
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
		fclose($fp);
		exit;
		
		}
		
    
}

?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">Licenses</div>
	<div class="col-md-6 col-sm-6 text-right"><?php if($_SESSION['role_id']==7){?><a href='requestLicense.php' class="btn btn-primary "><?php echo "Request Licenses"; ?></a><?php }?></div>
 </div>
 <div class="clear"></div>
 <section class="padder"> 
 
  <form id="serachform" name="serachform"  method = "get"  class="form-horizontal form-centerReg" data-validate="parsley" action="license_list.php" >
	<section class="marginBottom5 serachformDiv">
	
       <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 pull-left text-left paddLeft0">
       	       		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> hide <?php }?>" >

					 <select name="region_id" id="region" class="form-control "  >
						<option value=""><?php echo $language[$_SESSION['language']]['select_centre'];?></option>
						 <option value="All" <?php if($region_id=='All'){ ?> selected <?php } ?>>All</option>
						<?php 
						 foreach ($region_arr as $key => $value){	
						  $regionName= $value['region_name'];
						  
						  /* if($_SESSION['role_id']==7 && $_SESSION['region_id']==$value['id']){
							  $selected ="selected";
						  }
						  else */
							  
						  if( $_REQUEST['region_id']==$value['id']){ 
						  $selected ="selected"; }
						  else{ 
						  $selected ="";
						  } 
						?>
							<option <?php echo $hide; ?> value="<?php echo $value['id']; ?>" <?php echo $selected; ?>><?php echo $regionName;?></option>	
						  <?php 
						   } ?>
					   </select>
					
				</div>
		
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				
				<div class="searchboxCSS search-box1 col-xs-12 padd0">
				<input name="center_txt"  id="center_txt"  type="text" autocomplete="off" placeholder="Search <?php echo $language[$_SESSION['language']]['centre'];?>..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['center_id']) && $_REQUEST['center_id']!="") && (isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="")){?> value="<?php echo filter_query($_REQUEST['center_txt']);?>" <?php }?> />
				<input name="center_id"  id="center_hidden"  type="hidden" class="form-control  parsley-validated"   <?php if((isset($_REQUEST['center_id']) && $_REQUEST['center_id']!="") && (isset($_REQUEST['center_txt']) && $_REQUEST['center_txt']!="")){?> value="<?php echo filter_query($_REQUEST['center_id']);?>" <?php }?> />
				<div class="result_list1"></div>
				</div>
				
				
			</div>

			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  text-left">
				<select id="status" name="status" onchange="selectStatus(this);" class="form-control " >
							<option value="">Select</option>
								<option  value="active" <?php if($status == 'active') {echo  "selected";} ?>>Used</option>	
								<option  value="expired" <?php if($status == 'expired') {echo  "selected";} ?>>Expired</option>
								<option  value="available" <?php if($status == 'available') {echo  "selected";} ?>>Available</option>
						   </select>
					
			</div>
			
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  text-left paddLeft0">
				<select id="license_type" name="license_type" onchange="selectStatus(this);" class="form-control " >
							<option value="">Select Type</option>
								<option  value="Demo" <?php if($license_type == 'Demo') {echo  "selected";} ?>>Demo</option>	
								<option  value="Paid" <?php if($license_type == 'Paid') {echo  "selected";} ?>>Paid</option>
							
						   </select>		
			</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  text-left paddLeft0">
				 <div class="searchboxCSS search-box col-xs-12 padd0 pull-right">
					<input name="license"  id="license"  type="text" autocomplete="off" placeholder="Search License..." class="form-control  parsley-validated"  <?php if((isset($_REQUEST['license']) && $_REQUEST['license']!="")){?> value="<?php echo filter_query($_REQUEST['license']);?>" <?php }?>/>
					
				  <div class="result_list"></div>
			   </div>
			 </div>
			</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 pull-right text-right padd0">
					<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> Search</button> <button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
					<!--<a class="btn btn-sm btn-red btnwidth40" href="license_list.php"  name="refresh" title=" Refresh" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>-->
			</div>		
		
			 <!-- <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>-->
	</section>	
	</form>
   <div class="clear"></div>	
<section class="panel panel-default ">
	<div class="panel-body marginBottom10">
    <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
			 <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-1 text-left">Licenses</th>
			  <th class="col-sm-1 text-center"><?php echo $teacher;?> Limit</th>
			  <th class="col-sm-1 text-center"><?php echo $student;?> Limit</th>
			  <th class="col-sm-1 text-center">Expiry Date</th>
			  <th class="col-sm-1 text-center">Created Date</th>
			  <th class="col-sm-1 text-center">License Type</th>
			  <th class="col-sm-2 text-center"><?php echo $center;?></th>
			  <th class="col-sm-2 text-center">Requested By</th>
			  <th class="col-sm-1 text-center">Status</th>
			  <th class="col-sm-1 text-center">Used Date</th>
			
			  </tr>
			</thead>


			<tbody id="tbodyRecord">
			<?php 
				foreach($licenseList as $key=>$licenseDetail){

						$used_date = $licenseDetail->used_date;
						$expiry_days = $licenseDetail->lic_exp_day_af_reg_lan;
						$expiry_date = $licenseDetail->lic_req_license_expiry_lan;
						
						if($used_date!="" &&  $used_date != '0000-00-00 00:00:00'){
							
							if($expiry_date!="" && $expiry_date != '0000-00-00'){

								$expiry_date = date('d-m-Y',strtotime($expiry_date));

							}else{
								$expiry_date = date('d-m-Y',strtotime($used_date . "+".$expiry_days." days"));

							}
						$used_date=date('d-m-Y',strtotime($licenseDetail->used_date));
							
						}else{
							$expiry_date = '-';
							$used_date='-';
						}
						
						if($licenseDetail->issued_date!=''){
							$issued_date=date('d-m-Y',strtotime($licenseDetail->issued_date));
						}
						else{$issued_date='-';}
						
						if($licenseDetail->license_used_by){
							/*$usedByDtl=$commonObj->getDatabyId('user','user_id',$licenseDetail->license_used_by);
							$license_used_by=$usedByDtl['first_name'].' '.$usedByDtl['last_name'];
							$usedBgcolor='style="background: #f5f1f1;"';*/
							$license_used_by=$licenseDetail->license_used_by;
						}
						else{$license_used_by='-';
						   $usedBgcolor="";
						}
						
						
						
						if($licenseDetail->license_created_date!=''){
							$license_created_date=date('d-m-Y',strtotime($licenseDetail->license_created_date));
						}
						else{$license_created_date='-';}


						if($licenseDetail->lic_exp_day_af_reg_lan!='')//Days
						{
							$expiryDateDay=$licenseDetail->lic_exp_day_af_reg_lan;
							$cDate = date('Y-m-d');
							$date1 = str_replace('-', '/', $cDate);
							$expiryDateDay1 = date('m-d-Y',strtotime($date1 . "+".$expiryDateDay." days"));
							$parts = explode('-', $expiryDateDay1);
							$expiryDateDay = $parts[1] . '-' . $parts[0] . '-' . $parts[2];
							
						}else{//Date
						
							$expiryDateDay=$licenseDetail->lic_req_license_expiry_lan;
							$parts = explode('-', $expiryDateDay);
							$expiryDateDay = $parts[1] . '-' . $parts[2] . '-' . $parts[0];
						} 

						if($licenseDetail->license_used_by_name!=''){
							$license_used_by=$licenseDetail->license_used_by_name;
						}
						else{
							$license_used_by='-';
							}
						if($licenseDetail->license_type!=''){
							$license_type=$licenseDetail->license_type;
						}
						else{
							$license_type='-';
							}
						
						if($licenseDetail->license_status==1 && ($licenseDetail->used_date=='' ||  $licenseDetail->used_date=='0000-00-00 00:00:00')){
							$status='Available';
							$bgcolor="style='color:Green'";
						}
						elseif($licenseDetail->license_status==1 && $licenseDetail->used_date!='' && $licenseDetail->used_date!=''){
							$status='Available';
							$bgcolor="style='color:Green'";
						} 
						elseif($licenseDetail->license_status==4){
							
							$status='Used';$bgcolor="style='color:Red'";
							if($expiry_date!='-'){
								$current_date = date('Y-m-d');
								$dateTimestamp1 = strtotime($current_date); 
								$dateTimestamp2 = strtotime($expiry_date); 
								if($dateTimestamp1 > $dateTimestamp2) {
									$status='Expired';
									$bgcolor="style='color:Red'";
								}
							}
							
						} 
						elseif($licenseDetail->license_status==0){
							$status='Expired';
							$bgcolor="style='color:Red'";
						}
						
						if($licenseDetail->lic_req_by_user!="") {
									$requested_by = $licenseDetail->full_name;
							}else{
								$requested_by = '-';
							}
						
					
					?>
					<tr class="col-sm-12 padd0" >
							
							  <td class="col-sm-1 text-left" <?php echo $usedBgcolor; ?> id="<?php echo $licenseDetail->license_status;?>"> <?php echo $licenseDetail->license_value;?></td>

							  <td class="col-sm-1 text-center"><?php echo $licenseDetail->trainer_limit;?></td>
							  <td class="col-sm-1 text-center"><?php echo $licenseDetail->student_limit;?></td>
							  <td class="col-sm-1 text-center"><?php echo $expiry_date;?></td>
							  <td class="col-sm-1 text-center" <?php echo $usedBgcolor; ?>> <?php echo $license_created_date;?></td>
							  <td class="col-sm-1 text-center" <?php echo $usedBgcolor; ?>> <?php echo $license_type;?></td>
							  <td class="col-sm-2 text-center" <?php echo $usedBgcolor; ?>><?php echo $license_used_by;?> </td>
							    <td class="col-sm-2 text-center" > <?php echo $requested_by; ?> </td> 
							   <td class="col-sm-1 text-center" <?php echo $bgcolor; ?>><?php echo $status;?> </td>
								<td class="col-sm-1 text-center" <?php echo $usedBgcolor; ?>> <?php echo $used_date;?></td>
							  
							
							 </tr>
				<?php } ?>
					<tr>
						<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
					<?php } else{   ?>
						<div class="col-xs-12 noRecord text-center">Records not available.</div>
					<?php }?>  
			</tbody>
		</table>
	</div>
</div>
	
</section>
</div>	
<?php include_once('../footer/adminFooter.php'); ?>

<script>
$(document).ready(function () {
	 $(".export-report").click(function(e){
		e.preventDefault();
		var url = 'license_list.php?report_type=export';
		var region_id = $('#region').val();
		var license = $("#license").val();
		var center_id = $("#center_hidden").val();
		var status = $("#status").val();
		var license = $("#license").val();
		url += '&center_id='+center_id;
		url += '&region_id='+region_id;
		url += '&status='+status;
		url += '&license='+license;
		location.href = url;
		
	});
	
	$('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		//$('#license').val('');
		var region_id = $('#region').val();
		var center_id = $('#center_hidden').val();
		var status = $('#status').val();
        var resultDropdown = $(this).siblings(".result_list");
	   if(inputVal!=''){
            $.post("ajax/search_license.php", {license: inputVal,center_id:center_id,status:status,region_id:region_id}).done(function(data){
                // Display the returned data in browser
				if(data.trim()=="No"){
					resultDropdown.html('Not Available');
					resultDropdown.addClass("resultserchDiv");
				}else{
					resultDropdown.addClass("resultserchDiv");
					resultDropdown.html(data);
				 }
				
            });
        } else{
			console.log("empty")
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


$('.search-box1 input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
		var inputVal = $(this).val();
		$('#center_hidden').val('');
		var region_id = $('#region').val();
		var resultDropdown = $(this).siblings(".result_list1");
	   if(inputVal.length && inputVal.length>0){
			$.post("ajax/search_center.php", {client_id: <?php echo $client_id;?>,cname: inputVal, <?php if($region_id !=''){ echo "region_id: $region_id"; }else{ ?>region_id: region_id<?php }?> }).done(function(data){
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
});
  
</script>
