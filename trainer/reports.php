<?php include_once('../header/centerAdminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

$options = array();
$options['client_id'] = $client_id;
$page_param='';
$center_id='';
$country='';


$dir = "";
$order = isset($_GET['sort']) ? $_GET['sort'] : 'name';
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
$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page']; 

//$_limit = 50;
$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

$response_result= $reportObj->getCenterListByClientAndCountry($options,$objPage->_db_start, '',$order,$dir);

}else{
$response_result= $reportObj->getCenterListByClientAndCountry($options,$objPage->_db_start, $_limit,$order,$dir);
}

$objPage->_total = $response_result['total'];
$center_list_arr = $response_result['result'];


$all_center_list_arr = $reportObj->getAllCenterListByClient($client_id);

$country_list_arr=$reportObj->getCountryList();


//Export
 if (isset($_REQUEST['report_type'])) {

    if ($_REQUEST['report_type'] == 'export') {
        ob_clean();
        $file = 'report_'.time().'.xls';
        header("Content-Disposition: attachment; filename=" . $file);
        header("Content-Type: application/vnd.ms-excel");
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>S.No.</th>';
        $export_data .= '<th>Organization Name</th>';
        $export_data .= '<th>License</th>';
        $export_data .= '<th>Activation Date</th>';
        $export_data .= '<th>Expiry Date / Days</th>';
        $export_data .= '<th>Student Licenses</th>';
        $export_data .= '<th>Trainer Licenses</th>';
		$export_data .= '</tr>';


        if (count($center_list_arr) > 0) {
            $i = 0;
            foreach($center_list_arr  as $key => $value){
				$centerId=$center_list_arr[$key]['center_id'];
				$centerAddress=$center_list_arr[$key]['address1']."  ".$center_list_arr[$key]['city'].", ".$center_list_arr[$key]['state'].", ".$center_list_arr[$key]['country']." - ".$center_list_arr[$key]['pincode'];
				$center_name=$center_list_arr[$key]['name'];
				$center_code=$center_list_arr[$key]['code'];
				$email_id=$center_list_arr[$key]['email_id'];
				$mobile=$center_list_arr[$key]['mobile'];
				$license_key=$center_list_arr[$key]['license_key'];
				$created_date=$center_list_arr[$key]['created_date']; 
				$created_date = date('d-m-Y',strtotime($created_date));
				$expiry_days = $center_list_arr[$key]['expiry_days'];
				$student_limit = $center_list_arr[$key]['student_limit']; 
				$trainer_limit = $center_list_arr[$key]['trainer_limit'];

                $i++;
                
           
			    $export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' . $center_name . '</th>';
                $export_data .= '<th>' . $license_key . '</th>';
                $export_data .= '<th>' . $created_date . '</th>';
                $export_data .= '<th>' . $expiry_days . '</th>';
                $export_data .= '<th>' . $student_limit . '</th>';
				$export_data .= '<th>' . $trainer_limit . '</th>';

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
    <li class="textUpper"><a class="active" href="reports.php"><?php echo $centers; ?> Report</a></li>
	<li class="textUpper"><a href="learners_report.php"><?php echo $students; ?> Reports</a></li>
    <li class="textUpper"><a  href="trainers_report.php"><?php echo $teachers; ?> Reports</a></li>
    <li class="textUpper"><a  href="learning_objective_report.php">Performance Report</a></li>
  </ul>
	  <div class="tab-content">


	  <div id="insReport" class="tab-pane fade in active">
	 <?php  if(count($all_center_list_arr) > 0){?>
    <form id="serachform" name="serachform" method="GET"  class="form-horizontal form-centerReg" action="reports.php" >
	<section class="marginBottom5" style="height:80px;">
		  <div class="col-md-4 text-left paddLeft0">
				<select name="center_id" id="center_id" class="form-control " style="width:300px;">
                    <option value="">Select <?php echo $center; ?></option>
                   
				   <option value="All" <?php if($center_id=='All'){ ?> selected <?php } ?>>All</option>
					<?php 
					 foreach ($all_center_list_arr as $key => $value) {	
						
						$centerName= $all_center_list_arr[$key]['name'];
						$centerId= $all_center_list_arr[$key]['center_id']; 
						
						if($center_id==$centerId){ $selected ="selected"; }
						else{ $selected ="";} 
					   
					   ?>
					
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>" <?php echo $selected; ?> ><?php echo $centerName;?></option>	
					  <?php 
					   } ?>
                   </select>
				
			 </div>

			 <div class="col-md-4 text-left paddLeft0">
			
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
			 <div class="col-md-4 text-right paddRight0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> Search</button> 
				 <button class="btn btn-sm btn-red btnwidth40 search-export export-report" name="search" title=" Export" style="margin-top:0px"> <i class="fa fa-file-excel-o"></i></button>
			 </div>
			</form>
			 <!-- <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>-->
	</section>	
   <div class="clear"></div>	
   <?php  }?>
  
       <section class="panel panel-default">
	    <div class="panel-body">
	     <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
		    <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			<thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
				<th class="col-sm-2 text-left textUpper"><a href="reports.php?sort=name&dir=<?php echo $dir?>" class="th-sortable"><?php echo $center; ?> Name
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
					</a>
			    </th>
				<th class="col-sm-2 text-left textUpper"><a href="reports.php?sort=license_key&dir=<?php echo $dir?>" class="th-sortable">License
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'license_key' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'license_key' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a>
				</th>
			   <th class="col-sm-2 text-left textUpper">
				<a href="reports.php?sort=created_date&dir=<?php echo $dir?>" class="th-sortable">Activation Date
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'created_date' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'created_date' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a>
			   </th>
			   <th class="col-sm-2 text-left textUpper">
			   <a href="reports.php?sort=expiry_days&dir=<?php echo $dir?>" class="th-sortable">Expiry Date / Days
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
			   </th>
			   <th class="col-sm-2 text-left textUpper">
			    <a href="reports.php?sort=student_limit&dir=<?php echo $dir?>" class="th-sortable"><?php echo $student; ?> Licenses
					 <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'student_limit' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'student_limit' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a>
				</th>
			   <th class="col-sm-2 text-left textUpper"> <a href="reports.php?sort=trainer_limit&dir=<?php echo $dir?>" class="th-sortable"><?php echo $teacher; ?> Licenses
					  <span class="th-sort"> 
						<?php 
						if(isset($_GET['sort']) && $_GET['sort'] == 'trainer_limit' && $_GET['dir']=='ASC'){ ?>
							<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
						<?php }elseif(isset($_GET['sort']) && $_GET['sort'] == 'trainer_limit' && $_GET['dir']=='DESC'){ ?>
							<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
						<?php }else{ ?> 
							<i class='fa fa-sort'></i><?php }?>
					</span>
				</a></th>
			 </tr>
			</thead>
		   <tbody>	
		    <?php
		// echo "<pre>";print_r($center_list_arr);exit;	
			 foreach($center_list_arr  as $key => $value){
				$centerId=$center_list_arr[$key]['center_id'];
				$centerAddress=$center_list_arr[$key]['address1']."  ".$center_list_arr[$key]['city'].", ".$center_list_arr[$key]['state'].", ".$center_list_arr[$key]['country']." - ".$center_list_arr[$key]['pincode'];
				$center_name=$center_list_arr[$key]['name'];
				$center_code=$center_list_arr[$key]['code'];
				$email_id=$center_list_arr[$key]['email_id'];
				$mobile=$center_list_arr[$key]['mobile'];
				$license_key=$center_list_arr[$key]['license_key'];
				$created_date=$center_list_arr[$key]['created_date']; 
				$created_date = date('d-m-Y',strtotime($created_date));
				$expiry_days = $center_list_arr[$key]['expiry_days'];
				$student_limit = $center_list_arr[$key]['student_limit']; 
				$trainer_limit = $center_list_arr[$key]['trainer_limit'];
			if(B2C_CENTER!=$centerId){
				
				
			}
			?>
				<tr class="col-sm-12 padd0" >
				   <td class="col-sm-2 text-left "><?php echo $center_name;?></td>
				   <td class="col-sm-2 text-left textUpper"><?php echo $license_key;?></td>
				   <td class="col-sm-2 text-left "><?php echo $created_date;?></td>
				   <td class="col-sm-2 text-left "><?php echo $expiry_days;?></td>
				   <td class="col-sm-2 text-left "><?php echo $student_limit;?></td>
				   <td class="col-sm-2 text-left "><?php echo $trainer_limit;?></td>
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
<?php include_once('../footer/centerAdminFooter.php'); ?>
 <script>

        $(document).ready(function () {
             $(".export-report").click(function(e){
                e.preventDefault();
                var url = 'reports.php?report_type=export';
                var country = $("#country").val();
                var center_id = $("#center_id").val();
                url += '&center_id='+center_id;
                url += '&country='+country;

                 location.href = url;
                
            });
            
        });
      
 </script> 
