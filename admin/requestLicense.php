<?php include_once('../header/adminHeader.php');
$adminObj = new centerAdminController();
$msg='';	
$err='';	
$succ='';	
//$customer=trim($_SESSION['client_id']);
//echo "-->".$customer;exit;

if(isset($_POST['noOfLicense'])&& isset($_POST['requestLicense']) && ($_POST['expiryDate']!="" || $_POST['expDay']!="")){
  $noOfLicense=trim(strip_tags($_POST['noOfLicense']));
  $customer=trim($_SESSION['client_id']);
  //echo "-->".$customer;exit;
  $expiryDate=trim(strip_tags($_POST['expiryDate']));
  $expDay=trim(strip_tags($_POST['expDay']));
  $no_of_trainer=trim(strip_tags($_POST['no_of_trainer']));
  $no_of_learner=trim(strip_tags($_POST['no_of_learner']));
  $licenseType=trim(strip_tags($_POST['licenseType']));
$res = $adminObj->saveLicenserequest($noOfLicense,$customer,$expiryDate,$expDay,$no_of_trainer,$no_of_learner,$licenseType);
if($res){ 
 
	    $licensedataurl=$license_data_url; //"http://courses.englishedge.in/celp/service.php";
		$request = curl_init($licensedataurl);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'licenseRequestFromCustomer', 'customer' => $customer,'customer_id_celp' => $customer_id,'lic_no_lan' => $noOfLicense,'no_of_trainer' => $no_of_trainer,'no_of_learner' => $no_of_learner,'lic_expiry_lan' => $expiryDate, 'lic_exp_day_af_reg_lan' => $expDay,'licenseType' => $licenseType,'lic_req_by_user' => $_SESSION['user_id']));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$resLic = curl_exec($request);
		curl_close($request);
		$resLic = json_decode($res);
		//echo "-->".$resLic;exit;
		//print_r($resLic);
    $_SESSION['succ'] = 1;
    $_SESSION['msg'] ='Licenses generated successfully.';
	header("Location:requestLicense.php");
	exit;
 }else{
	$_SESSION['error'] =1;
	$_SESSION['msg'] ='License request not submitted. Please try again.';
	header("location:requestLicense.php");
	exit;
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

$pageType ="Request";
$licdata = $adminObj->getReqLicenseDetails($client_id);
//echo "<pre>";print_r($licdata);exit;
?>

<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="licenses.php"><i class="fa fa-arrow-left"></i> Licenses</a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
	 
      <form action="" id="assignBatchForm" class="assignBatchForm" method="post"  data-validate="parsley">
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
          <div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header"> <?php echo $pageType; ?> Licenses</h3>
		  <div class="form-group">
			
		   <div class="col-sm-4"> 
		    <label class="control-label">Number  of Licenses  <span class="required text-red">*</span></label>			 
					<input type="text" id="noOfLicense" name="noOfLicense" maxlength='3' data-required="true" data-regexp="^[1-9]\d*$" data-regexp-message="License should be valid number and not be 0" class="form-control " />
			</div>
		 
		   <div class="col-sm-4"> 
		    <label class="control-label">Expiry Date  <span class="required text-red">*</span></label>			 
					<div id="divDate1" class="input-append date">
					  <input  data-date-format="DD-MM-YYYY" name="expiryDate" value=""  id="expiryDate" placeholder="DD-MM-YYYY" class="form-control" onchange="disableday(this)"  readonly="true" autocomplete="off" />
					  	<span class="calendarBg add-on">
					   <i class="fa fa-calendar"></i>
					  </span></div>   
				
		  </div>
		     <div class="col-sm-1"> <label class="control-label"> <span class="text-red">&nbsp;</span></label>	</br>
			 Or</div>
		   <div class="col-sm-3"> 
		   <label class="control-label">Expiry Days <span class="required text-red">*</span></label>			 
					  <input type="text" id="expDay" name="expDay"  data-regexp="^[1-9]\d*$" data-regexp-message="Expiry day should be valid number and not be 0" class="form-control "   />
			 <label class="required showErr" id="dayDateError"><?php echo $msgold; ?></label>
		  </div>
		    <div class="clear">&nbsp;</div>
			 <div class="col-sm-4"> 
		    <label class="control-label">Number of <?php echo $teachers;?> <span class="required text-red">*</span></label>			 
			<input type="text" id="no_of_trainer" name="no_of_trainer" data-required="true" data-regexp="^[1-9]\d*$" data-regexp-message="Number of trainer should be valid number and not be 0" class="form-control "   />
			 <label class="required showErr" id="noTrainerError"><?php echo $msgold; ?></label>
		  </div>
		   <div class="col-sm-4"> 
		    <label class="control-label">Number of <?php echo $students;?> <span class="required text-red">*</span></label>			 
				<input type="text" id="no_of_learner" name="no_of_learner" data-required="true" data-regexp="^[1-9]\d*$" data-regexp-message="Number of learner should be valid number and not be 0" class="form-control "   />
			 <label class="required showErr" id="noLearnerError"><?php echo $msgold; ?></label>
		  </div>
		  
		   <div class="col-sm-4"> 
		   <label class="control-label">Types of licenses <span class="required text-red">*</span></label>
		  <select class="form-control" id="licenseType" name="licenseType" data-required="true" >
					<option value=''>Select</option>
					<option value="Demo">Demo</option>
					<option value="Paid">Paid</option>

				   </select>
			</div>
		    </div>
		   </div>
		 </div>
		    <div class="clear">&nbsp;</div>
			
			<div class="text-right"> 
				<a href='requestLicense.php' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
			
			<input type="hidden" name="batchId" value="<?php echo $batchId;?>" />
			<button type="submit" name="requestLicense" id="requestLicense" class="btn btn-s-md btn-primary" onclick="return chkDateDay();" ondblclick="return chkDateDay();" >Submit</button>
	    </div>
	   </div>
     </form>
	 </section>
 
<?php if(count($licdata) > 0 && !empty($licdata)){?>
 <div class="clear"></div>	
   <section class="panel panel-default">
    <div class="table-responsive" style="min-height:180px;height:300px; overflow-y:auto">
	  <table class="table table-border dataTable table-fixed">
		 <thead  class="fixedHeader">
		   <tr class="col-sm-12">
			 <th class="col-sm-1 text-center">Number of Licenses</th>
			  
			 <th class="col-sm-2 text-center">Expiry Date</th>
            <th class="col-sm-2 text-center">Expiry Days</th>
			<th class="col-sm-1 text-center">Number of <?php echo $teachers;?> </th>
			<th class="col-sm-1 text-center">Number of <?php echo $students;?></th>
			 <th class="col-sm-2 text-center">Requested Date </th>
			   <th class="col-sm-2 text-center">Requested By</th>
			  <th class="col-sm-1 text-center">License Type </th>
			
			
		   </tr>
		 </thead>
		 <tbody>
		 <?php
		// echo "<pre>";print_r($licdata);exit;	
		  foreach($licdata  as $key => $value){
		   $licNo=$licdata[$key]['noOfLicense'];
		   $expiryDate=$licdata[$key]['expiry_date'];	
		 
			if($expiryDate=='0000-00-00 00:00:00'){
				$expiryDate='-';
			}else{
				$expiryDate=date('d-m-Y',strtotime($expiryDate));
				//$expiryDate=date('Y-m-d H:i:s',strtotime($expiryDate));
			}
			
		   
		   $expiryDays=$licdata[$key]['expiry_day'];
		   $trainerlimit=$licdata[$key]['no_of_trainers'];	
		   $studentlimit=$licdata[$key]['no_of_learners'];
          $reqDate=$licdata[$key]['requested_date'];	
		  $reqDateNew=date('d-m-Y',strtotime($reqDate));
		  $liceneType=$licdata[$key]['license_type'];	
		  if($licdata[$key]['lic_req_by_user']!="") {
			 $user = userdetails($licdata[$key]['lic_req_by_user']);
			 $requested_by = $user->first_name.' '.$user->last_name;
			}else{
				$requested_by = '-';
			}
		 ?>          
		  <tr class="col-sm-12">
			<td class="col-sm-1 text-center"><?php echo $licNo;?></td>
			 
			<td class="col-sm-2 text-center"><?php echo $expiryDate;?></td>
			<td class="col-sm-2 text-center"><?php echo $expiryDays;?></td>
			<td class="col-sm-1 text-center"><?php echo $trainerlimit;?></td>
			<td class="col-sm-1 text-center"><?php echo $studentlimit;?></td>
			 <td class="col-sm-2 text-center"><?php echo $reqDateNew;?></td>
			 <td class="col-sm-2 text-center"><?php echo $requested_by;?></td>
		    <td class="col-sm-1 text-center"><?php echo $liceneType;?> </td>
			
		
			
			 </tr>
			<?php }?>

		 </table>
	</div>
	</section>	
  <?php  } ?>
  </div>
 </div>	 
</section>	 

<?php include_once('../footer/adminFooter.php'); ?>
<script>
// Validation for select course
$(document).ready(function(){
 /* var sectorWise = "<?php  echo $sectionConfig; ?>";
  if(sectorWise=='Number'){
		for (i = 0; i < 100; i++) {
			var number =i+1;
			var opt ="<option value='"+number+"'>"+number+"</option>";
		  $("#section").append(opt);
		}
     }
  if(sectorWise=='Alphabet'){
		for (i = 0; i < 26; i++) {
			var number =i+1;
			var aplha =(i+10).toString(36);
			var opt ="<option value='"+aplha+"'>"+aplha+"</option>";
		  	$("#section").append(opt);
		}
     }
	if(sectorWise=='None'){
		  $("#section").val('');
     }  */
	 
	 
});



$(function () {
  $("#divDate1").datepicker({ 
  	startDate: new Date(),
    autoclose: true, 
    todayHighlight: true,
    format: 'dd-mm-yyyy',
  }); 
});

function chkDateDay(){
	
    $("#dayDateError").html('');
	var expiryDate = $("#expiryDate").val();
	var expDay = $("#expDay").val();

	if((expiryDate == "") && (expDay == "")){
		$("#dayDateError").html('Please enter either expiry date or day');
		return false;
	}
}

function disableday(e){
	if($('#expiryDate').val()){
		$('#expDay').val("");
		$('#expDay').prop("disabled", true);
	}
	else{
		$('#expDay').prop("disabled", false);
	}
}


$("#expDay").on('change keyup paste', function() {
	if($(this).val()){
	$('#expiryDate').prop("disabled", true);
	//$('#divDate1').addClass("patient");
	//$('#opaCiti').addClass("opaCiti");
	}
	else{
		$('#expiryDate').prop("disabled", false);
		//$('#divDate1').removeClass("patient");
		//$('#opaCiti').removeClass("opaCiti");
	}
});

</script>
