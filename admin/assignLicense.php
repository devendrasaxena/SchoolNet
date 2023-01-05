<?php include_once('../header/adminHeader.php');
$licenseDetail=$adminObj->availLicense();
$availLicenses=$licenseDetail[0]['cnt'];
//Showing message after submit
if(isset($_POST['noOfLicense'])&& isset($_POST['assignLicense'])){

  $noOfLicense=trim($_POST['noOfLicense']);
  $customer=trim($_POST['customer']);

if($availLicenses>=$noOfLicense){

   $res = $adminObj->saveLicenseIssue($noOfLicense,$customer);

}

if($res){
   $_SESSION['succ'] = 1;
   $_SESSION['msg'] ='Licenses assigned successfully.';
	$batch = (object) $batch;
	header("Location:licenses.php");
	exit;
 }else{
	$_SESSION['error'] =1;
	$_SESSION['msg'] ='License not assigned. Please try again.';
	header("location:licenses.php");
	exit;
	} 
}
$pageType ="Assign";
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="licenses.php"><i class="fa fa-arrow-left"></i> Licenses</a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
   <form action="" id="assignBatchForm" class="assignBatchForm" method="post"  data-validate="parsley">
	 <div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20" style="padding-bottom:20px;">
			 <h3 class="panel-header"> <?php echo $pageType; ?> Licenses</h3>  
			  <div class="form-group">
			   <div class="col-sm-5"> 
				<label class="control-label">Select <?php echo $center; ?>  <span class="text-red">*</span></label>			 
						<div class="clear"></div>
						<select id="customer" name="customer" data-required="true" class="form-control " >
						 <option value="" >Select <?php echo $center; ?> </option>
						  <?php 

							if(count($centers_arr ) > 0 && !empty($centers_arr)){
							// echo "<pre>";print_r($centers_arr);exit;	
								 foreach($centers_arr  as $key => $value)
								{
									$center_id=$centers_arr[$key]['center_id'];
									$center_name=$centers_arr[$key]['name'];
							?>
						  <option value="<?php echo $center_id;?>" ><?php echo $center_name;?></option>
							<?php }}?>
						  
						</select>
				
			  </div>
			   <div class="col-sm-1"> 
			   </div>
			   <div class="col-sm-5"> 
				<label class="control-label">No of Licenses  <span class="text-red">*</span></label>			 
						<div class="clear"></div>
						<input type="text" id="noOfLicense" name="noOfLicense" data-required="true" data-regexp="^[1-9]\d*$" data-regexp-message="No of license should be valid number and not be 0" class="form-control "  data-max="[<?php echo $availLicenses;?>]"  data-max-message="Maximum license availlable is <?php echo $availLicenses;?>"/>
				
			  </div>
			  </div>
		  </div>
		 </div>
			<div class="text-right"> 
				<a href='licenses.php' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
				<input type="hidden" name="batchId" value="<?php echo $batchId;?>" />
			<button type="submit" name="assignLicense" id="assignLicense" class="btn btn-s-md btn-primary">Submit</button>
	    </div>
	   </div>
      </form>
	 </section>
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

</script>
