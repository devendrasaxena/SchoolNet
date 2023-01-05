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

$options['role_id'] = isset($_GET['role']) ? filter_query($_GET['role']) :'';

$options['centre'] = isset($_GET['centre']) ? filter_query($_GET['centre']) :'';

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'u1.created_date';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';

$centres = $reportObj->getCentresList();



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

$center_id='';
$country='';
$batch_id='';
if (!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $region_id;
	$country_list_arr=$reportObj->getCountryList($region_id);
	$page_param .= "region_id=$region_id&";
}else{
	$options['region_id'] = $rid;
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
    $tehsil_id = trim(filter_query($_REQUEST['tehsil_id']));
	$options['tehsil_id'] = $tehsil_id;
	$page_param .= "tehsil_id=$tehsil_id&";
}

$centres = $reportObj->getCentresList($rid);

$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

//$_limit = 20;
$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $reportObj->getUsersByStateRanking($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $reportObj->getUsersByStateRanking($options,$objPage->_db_start, $_limit,$order,$dir);
}
$objPage->_total = $response_result['total'];
//$state_ranking_arr = $response_result['result'];

$userCount = $reportObj->getUsersByStateCountRanking($options,$objPage->_db_start, $_limit,$order,$dir);
//$userCountArr = $userCount['result'];

$mandatoryModules = $reportObj->getStateRankingMandatoryModules($options,$objPage->_db_start, $_limit,$order,$dir);
//$mandatoryModulesArr = $mandatoryModules['result'];

/*ob_clean();
echo "<pre>";
print_r($state_ranking_arr);
 */
//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);


$state_ranking_arr =  $reportObj->getFinalStateRanking($options,$objPage->_db_start, $_limit,$order,$dir);

//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
       // $file = 'learners_report_'.time().'.xls';
        $file = 'state_ranking_report_'.time().'.csv';
        /* header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel"); */
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>State</th>';
        $export_data .= '<th>Total</th>';
        $export_data .= '<th>Rank</th>';
		$export_data .= '</tr>';
		$rankArr = array();
        if (count($state_ranking_arr) > 0) {
            $i = 0;
             foreach($state_ranking_arr  as $key => $value){
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
				$state=$value->state;
				$lastLogin =getLastLogin($value->firstTime_login);
				if($state=='')
				{
				$state='-';
				}

                $i++;

                $total = round($value['total'],2);
				if(!in_array($total,$rankArr)){
					array_push($rankArr, $total);
					$rank = count($rankArr);
					
				}else{
					$rank  = array_search($total,$rankArr)+1;

				}
                
           
			    $export_data .= '<tr>';
                $export_data .= '<th>' . ltrim($value['name'],"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($total,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($rank,"@-+=") . '</th>';

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
	<?php include_once('reports_menu.php');?>
	<div class="tab-content">
	<div id="insReport" class="tab-pane fade in active">
	 <?php // if(count($all_center_list_arr) > 0){?>
		<form id="serachform" name="serachform" method="GET" 
		 class="form-horizontal form-centerReg"
		  action="state_ranking_report.php" >
	<section class="marginBottom5 serachformDiv" >
		 <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
   		
		 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-6 text-left paddLeft0 <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>">
		
		 <select name="region_id" id="region_id" class="form-control " onchange="setState(this)">
				
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

		


				   
				
		</div>
			


			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-right paddRight0">
				<button type="submit"  class="btn btn-red <?php if($_SESSION['role_id']==7){?> <?php echo 'hide'; ?> <?php }?>" id="btnSave"
				 style="margin-top:0px" title="<?php echo $language[$_SESSION['language']]['search'] ?> <?php echo $language[$_SESSION['language']]['state_ranking_report'] ?>  "> 
				 <?php echo $language[$_SESSION['language']]['search']; ?></button>
				  <button type="submit" class="btn btn-sm btn-red btnwidth40 search-export export-report"
				   name="report_type" value="export" title=" <?php echo $language[$_SESSION['language']]['export']; ?> "
				    style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
				<a class="btn btn-sm btn-red btnwidth40" href="state_ranking_report.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?> " style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
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
			 
			   
			  
			 
			  <th class="col-sm-4 text-left textUpper">
			  	
			  	<?php echo $language[$_SESSION['language']]['state']; ?>
			  </th>
			   <th class="col-sm-4 text-left textUpper">
			   	<?php echo $language[$_SESSION['language']]['total']; ?>
			   </th>
			   <th class="col-sm-4 text-left textUpper"><?php echo $language[$_SESSION['language']]['rank']; ?></th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php
		
			
			
			$rankArr = array();

			 foreach($state_ranking_arr  as $key => $value){
				$total = round($value['total'],2);
				if(!in_array($total,$rankArr)){
					array_push($rankArr, $total);
					$rank = count($rankArr);
					
				}else{
					$rank  = array_search($total,$rankArr)+1;

				}
			?>

			 
				<tr class="col-sm-12 padd0" >

				   <td class="col-sm-4 text-left textUpper"><?php echo $value['name'];?></td>
				   <td class="col-sm-4 text-left textUpper"><?php echo $total;?></td>
				   <td class="col-sm-4 text-left"><?php echo $rank ?></td>
				</tr>
			<?php //}  
			   }
			   
			 
			   
			 ?>

			 
			
				<tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			  <?php } else{   ?>
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?><br>
		</div> 
		  <?php }?>  
			</tbody>
		    </table>
			</div>

			<b> State Ranking is measured based on following parameters and their respective weightage:</b>
			 <ul> <li>Average module score of the users in the State (20%) </li>
			 <li>Percentage (%) of users who completed all mandatory modules in the State (40%), </li>
			 <li>Percentage (%) of users who completed all modules in the State (40%) </li>
			 </ul>
					

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

     /*  $(document).ready(function () {
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
            
        });*/
      
 </script> 




<script src="./js/sb-report-script.js"></script>

