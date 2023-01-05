<?php include_once('../header/adminHeader.php');
//$lic_customer_id=95;
$serviceURL =$license_data_url; // path define in config   "http://courses.englishedge.in/celp/service.php";
$request = curl_init($serviceURL);
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'getCurrentCustomerLicenses', 'customer_id' => $lic_customer_id));
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($request);
curl_close($request);
$res = json_decode($res);
//print_r($res);
$ret=addUpdateLicenses($lic_customer_id,$res);

$licenseDetails=getLicenseDeatails($lic_customer_id);
$licUseData=getUsedLicense($lic_customer_id);

//echo "-->".$licenseDetails->givenToCustomers;
$licenses=$licenseDetails->licenses;
//echo $licenses;exit;
$licensesStr=substr($licenses,1,strlen($licenses))."...";
$pageType ="Request";


$msg='';	
$err='';	
$succ='';	
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "License not assigned. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "License assigned successfully.";
	}
	
}
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);

	
}

?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">Licenses </div>
	<div class="col-md-6 col-sm-6 text-right"><a href='requestLicense.php' class="btn btn-primary "><?php echo $pageType." Licenses"; ?></a></div>
 </div>
 <div class="clear"></div>
 <section class="padder"> 
   <section class="panel panel-default">
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
	<div class="panel-body">
    <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-4 text-center">Total Licenses Issued</th>
			 <!-- <th class="col-sm-3 text-center">Assigned Licenses</th>
			  <th class="col-sm-2 text-center paddLeft0 paddRight0">Unassigned Licenses</th>-->
			  <th class="col-sm-4 text-center">Used Licenses</th>
			  <th class="col-sm-4 text-center">Action</th>
			 <!-- <th class="col-sm-2 text-center">Action</th>-->
			 </tr>
			</thead>
		   <tbody>
			
				<tr class="col-sm-12 padd0" >
				
				 <td class="col-sm-4 text-center"><?php echo $licenseDetails->totalIssued;?></td>
				  <!--<td class="col-sm-3 text-center"><?php echo $licenseDetails->givenToCustomers;?></td>
				  <td class="col-sm-2 text-center"><?php echo($licenseDetails->totalIssued-$licenseDetails->givenToCustomers) ;?></td>-->
				  <td class="col-sm-4 text-center"><?php echo $licUseData->totalUsed;?></td>
				  <td class="col-sm-4 text-center"> <a class="edit" href="license_list.php">View</a></td> 
				 <!--   <td class="col-sm-2 text-center"> <a class="edit" href="assignLicense.php"> Assign to Customers</a></td>-->
				</tr>
				 
			</tbody>
		    </table>
	</div>
	</div>
	
</section>
 
 </section> 
 <!-- Modal -->
  <div id="reportView" class="modal fade" role="dialog">
  <div class="modal-dialog width90per">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">View Report</h4>
      </div>
      <div class="modal-body" id="resultQ">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<?php include_once('../footer/adminFooter.php'); ?>
<script>
var licenseWin;
function viewLicense(){
	var fpath="ajax/licenseList.php";
	licenseWin=window.open(fpath, "verify", "fullscreen=yes, scrollbars=auto,toolbar=no,menubar=no,resizable=no,statusbar=no,location=no,directories=no,width="+screen.availWidth+",height="+screen.availHeight);
	licenseWin.focus();
}
</script>