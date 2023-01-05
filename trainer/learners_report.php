<?php include_once('../header/trainerHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
$reportObj = new reportController();
$options = array();
$options['client_id'] = $client_id;
$options['role_id'] = 2;
$page_param='';
//$center_id='';
$batch_id='';
$country='';
$options['center_id'] = $center_id;

 if (!empty($_REQUEST['batch'])) {
    $batch_id = trim($_REQUEST['batch']);
	$options['batch_id'] = $batch_id;
	$page_param .= "batch_id=$batch_id&";
} 
if (!empty($_REQUEST['country'])) {
    $country = trim($_REQUEST['country']);
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

$dir = "";
$order = isset($_GET['sort']) ? $_GET['sort'] : 'u1.created_date';
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';

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


$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

//$_limit = 20;
$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $reportObj->getUsersByBatchAndCountry($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $reportObj->getUsersByBatchAndCountry($options,$objPage->_db_start, $_limit,$order,$dir);
}
$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];

 
//$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);

$country_list_arr=$reportObj->getCountryList();


//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
        $file = 'learners_report_'.time().'.xls';
        header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel");
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Name</th>';
        $export_data .= '<th>Email</th>';
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
                $export_data .= '<th>' . $fullname . '</th>';
                $export_data .= '<th>' . $email_id . '</th>';
                $export_data .= '<th>' . $created_date . '</th>';
                $export_data .= '<th>' . $country . '</th>';
                $export_data .= '<th>' . $mother_tongue . '</th>';
				$export_data .= '<th>' . $status . '</th>';

                $export_data .= '</tr>';
			
			 }
        }



        $export_data .= '</table>';
        echo '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
        echo $export_data;
        die;
    }
}

?>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <h3>Reports</h3>
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
 <ul class="nav nav-tabs" style="margin-bottom:40px;">
     <!--<li class="textUpper"><a href="reports.php"><?php echo $centers; ?> Report</a></li>-->
	<li class="textUpper"><a  class="active" href="learners_report.php"><?php echo $students; ?> Reports</a></li>
	 <!--<li class="textUpper"><a  href="trainers_report.php"><?php echo $teachers; ?> Reports</a></li>-->
    <li class="textUpper"><a  href="learning_objective_report.php">Performance Report</a></li>
  </ul>
	<div class="tab-content">
	<div id="insReport" class="tab-pane fade in active">
	 <?php  //if(count($all_center_list_arr) > 0){?>
		<form id="serachform" name="serachform"  method = "GET"  class="form-horizontal form-centerReg" action="learners_report.php" >
			<section class="marginBottom5" style="height:80px;">
			  <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0">
					<input name="center_id" id="center_id" value="<?php echo $center_id; ?>" type="hidden"/>
					<select class="form-control parsley-validated" id="batch" name="batch" data-required="true">
					  <option value="">Select <?php echo $batch;?>*</option>
					  <?php  foreach($batchData as $key => $value){ 
					     $batchDataArr=$adminObj->getBatchNameByID($value['batch_id']);
						 $batch_name=$batchDataArr[0]['batch_name'];
						$sel = ($batch_id == $value['batch_id']) ? "selected" : "";
						?>
					  <option value="<?php echo $value['batch_id']; ?>" <?php echo $sel; ?> ><?php echo $batch_name; ?></option>
					  <?php } ?>
					</select>
				 </div>

				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left paddLeft0">
			
					<select name="country" id="country" class="form-control " style="width:300px;">
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
			 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> Search</button> <button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
			 </div>
			</form>
			 <!-- <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>-->
	</section>	
   <div class="clear"></div>	
   <?php // }?>

	 
  
       <section class="panel panel-default">
	    <div class="panel-body">
	     <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
		 
		    <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			 
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2 text-left textUpper">
				  <a href="learners_report.php?sort=u1.first_name&dir=<?php echo $dir?>" class="th-sortable">Name
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
			   
			   <th class="col-sm-3 text-left textUpper"><a href="learners_report.php?sort=u1.email_id&dir=<?php echo $dir?>" class="th-sortable">Email
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
			   <th class="col-sm-2 text-left textUpper"><a href="learners_report.php?sort=uc1.created_date&dir=<?php echo $dir?>" class="th-sortable">Joined
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
			   <th class="col-sm-2 text-left textUpper">Country</th>
			   <th class="col-sm-2 text-left textUpper">Language</th>
			   <th class="col-sm-1 text-left textUpper">Status</th>
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
		       }?><tfoot>
				<tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr></tfoot>
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
<?php include_once('../footer/trainerFooter.php'); ?>
 <script>

        $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'learners_report.php?report_type=export';
                var country = $("#country").val();
                var batch_id = $("#batch").val();
                url += '&batch_id='+batch_id;
                url += '&country='+country;

                 location.href = url;
                
            });
            
        });
      
 </script> 