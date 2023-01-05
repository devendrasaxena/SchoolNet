<?php 
include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

$reportObj = new reportController();
$country_list_arr=$reportObj->getCountryList();
$options = array();
$options['client_id'] = $client_id;
$options['role_id'] = 2;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'u1.created_date';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';



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
if (!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $region_id;
	$country_list_arr=$reportObj->getCountryList($region_id);
	$page_param .= "region_id=$region_id&";
}else{
	$options['region_id'] = $region_id;
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


$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

//$_limit = 20;
$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $reportObj->getUsersByCenterAndCountry($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $reportObj->getUsersByCenterAndCountry($options,$objPage->_db_start, $_limit,$order,$dir);
}
$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];

 
//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);




//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
       // $file = 'learners_report_'.time().'.xls';
        $file = 'learners_report_'.time().'.csv';
        /* header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); */
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Name</th>';
        $export_data .= '<th>Login Id</th>';
        $export_data .= '<th>Joined</th>';
        $export_data .= '<th>Country</th>';
        $export_data .= '<th>Language</th>';
        $export_data .= '<th>Status</th>';
		$export_data .= '</tr>';

        if (count($users_arr) > 0) {
            $i = 0;
             foreach($users_arr  as $key => $value){
				$first_name=$value->first_name;
				$last_name=$value->last_name;
				$fullname=$first_name." ".$last_name;
				
				$email_id=$value->email_id;
				$mother_tongue=$value->mother_tongue;
				$status=$value->is_active;
				if($status=='1')
				{
				$status='Active';
				}
				$created_date=$value->created_date;
				$created_date = date('d-m-Y',strtotime($created_date));
				$country=$value->country;
				if($country=='')
				{
				$country='-';
				}

                $i++;
                
           
			    $export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' . ltrim($fullname,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($email_id,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($created_date,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($country,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($mother_tongue,"@-+=") . '</th>';
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
	 <?php // if(count($all_center_list_arr) > 0){?>
		<form id="serachform" name="serachform"  method = "GET"  class="form-horizontal form-centerReg" action="learners_report.php" >
			<section class="marginBottom5 serachformDiv">
			<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> hide <?php }?>" >

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
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3  text-left paddLeft0">
		
			 <select name="country" id="country" class="form-control " >
				<option value=""><?php echo $language[$_SESSION['language']]['country']; ?></option>
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

			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
				<select name="center_id" id="center_id" class="form-control" >
                  <option value=""><?php echo $language[$_SESSION['language']]['states']; ?></option>
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
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 paddLeft0 text-left " >
				
				<select class="form-control parsley-validated" id="batch_id" name="batch_id" data-required="true">
				<option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
					 <?php 
						$batchInfo = $reportObj->getBatchDeatils($center_id);
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
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button> <button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" <?php echo $language[$_SESSION['language']]['export']; ?> " style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
				<a class="btn btn-sm btn-red btnwidth40" href="learners_report.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?> " style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
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
			  <th class="col-sm-2 text-left textUpper">
				  <a href="learners_report.php?country=<?php echo $country;?>&center_id=<?php echo $center_id;?>&batch_id=<?php echo $batch_id;?>&sort=u1.first_name&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['name']; ?>
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'u1.first_name' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'u1.first_name' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span>
					</a>
				</th>
			   
			   <th class="col-sm-3 text-left textUpper"><a href="learners_report.php?country=<?php echo $country;?>&center_id=<?php echo $center_id;?>&batch_id=<?php echo $batch_id;?>&sort=u1.email_id&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['login_id']; ?>
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'u1.email_id' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'u1.email_id' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span>
					</a>
				</th>
			   <th class="col-sm-2 text-left textUpper"><a href="learners_report.php?country=<?php echo $country;?>&center_id=<?php echo $center_id;?>&batch_id=<?php echo $batch_id;?>&sort=uc1.created_date&dir=<?php echo $dir?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['joined']; ?>
						<span class="th-sort"> 
							<?php 
							if(isset($_GET['sort']) && $_GET['sort'] == 'uc1.created_date' && $_GET['dir']=='ASC'){ ?>
								<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
							<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'uc1.created_date' && $_GET['dir']=='DESC'){ ?>
								<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
							<?php }else{ ?> 
								<i class='fa fa-sort'></i><?php }?>
						</span>
					</a>
				</th>
			   <th class="col-sm-2 text-left textUpper"><?php echo $language[$_SESSION['language']]['country']; ?></th>
			   <th class="col-sm-2 text-left textUpper"><?php echo $language[$_SESSION['language']]['language']; ?></th>
			   <th class="col-sm-1 text-left textUpper"><?php echo $language[$_SESSION['language']]['status']; ?></th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php
		
			 foreach($users_arr  as $key => $value){
				$first_name=$value->first_name;
				$last_name=$value->last_name;
				$fullname=$first_name." ".$last_name;
				
				$email_id=$value->email_id;
				$mother_tongue=$value->mother_tongue;
				$status=$value->is_active;
				if($status=='1')
				{
				$status='Active';
				}
				$created_date=$value->created_date;
				$created_date = date('d-m-Y',strtotime($created_date));
				$country=$value->country;
				if($country=='')
				{
				$country='-';
				}
				
			
			?>
				<tr class="col-sm-12 padd0" >
				   <td class="col-sm-2 text-left"><?php echo $fullname;?></td>
				   
				   <td class="col-sm-3 text-left "><?php echo $email_id;?></td>
				   <td class="col-sm-2 text-left "><?php echo $created_date;?></td>
				   <td class="col-sm-2 text-left textUpper"><?php echo $country;?></td>
				   <td class="col-sm-2 text-left textUpper"><?php echo $mother_tongue;?></td>
				   <td class="col-sm-1 text-left"><?php echo $status;?></td>
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
</style>
<?php include_once('../footer/adminFooter.php'); ?>
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
	
	var center_id = $('#center_id option:selected').val();
	if(center_id==''){
			$('#batch_id').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getBatchByCenter.php', {center_id: center_id}, function(data){ 
			if(data!=''){
				  $('#batch_id').html(data);
				}else{
					$('#batch_id').html('<option value="">Not Available</option>');
				}
		}); }
});

</script> 
<script>

        $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'learners_report.php?report_type=export';
                var country = $("#country").val();
                var center_id = $("#center_id").val();
                var batch_id = $("#batch_id").val();
                url += '&center_id='+center_id;
                url += '&country='+country;
                url += '&batch_id='+batch_id;

                 location.href = url;
                
            });
            
        });
      
 </script> 