<?php 
include_once('../header/adminHeader.php');
$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$teacher not saved. Please try again.";
	}
	if($_SESSION['error'] == '2'){
		$msg = "$teacher login is already exist. Please try another.";
	}
	if($_SESSION['error'] == '3'){
		$msg = " You have reached maximum limit of $teacher allowed. You can not register more $teacher.";
	}
}

if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "$teacher created successfully.";
	}
	if($_SESSION['succ'] == '2'){
		$msg = "$teacher updated successfully.";
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

if(isset($_GET['uid'])){
  $uId = trim( base64_decode($_GET['uid']) );  
    if(is_numeric($uId)==true){
		$teacherData = $centerObj->getUserDataByID($uId, 1);// teacher role 1
    }else{
		header('Location: dashboard.php');
		die;
	}
  $selectBatchList=array();
 
 foreach($teacherData->batch_id as $key => $sbValue){
      $selectBatchList[]=  $sbValue['batch_id'];              
  }
   //echo "<pre>";print_r($selectBatchList);exit;
  $batchId=$teacherData->batch_id[0]['batch_id'];
  $status=$teacherData->is_active;
}
 $getSignedUpUser =  $centerObj->getSignedUpUserCountByCenter($client_id,$teacherData->center_id);
$adminObj = new centerAdminController();
//$checkEmailExits = $adminObj->checkEmailExits();
$batchInfo = $adminObj->getBatchDeatils($teacherData->center_id);
$centerDetails=$clientObj->getCenterDetailsById($teacherData->center_id);


$trainer_limit=(!empty($centerDetails[0]['trainer_limit'])?$centerDetails[0]['trainer_limit']:0);
//total user limit
if($trainer_limit != 0){	
	$remaining = $trainer_limit - $getSignedUpUser->totalCenterTeacher;
	
	if($remaining < 1){
		$errDiv = "<div class='col-sm-12  marginTop40 paddLeft40  paddRight40 '> <div class='alert alert-danger height60 fontSize16 paddTop20'>
					
					<i class='fa fa-ok-sign'></i> You have reached maximum limit of $teacher allowed. You can not register more $teacher. Please contact administrator.
					</div> </div>";
		$regClass ="displayNone";

	}else{
		$errDiv = "";
		$regClass ="";
	}
}else{
	$remaining = 0;
}
 $resCountry=$commonObj->getCountries(); 
    //$resCountry=$commonObj->getCountry();	
	$gender=$commonObj->getGender();	
	$age=$commonObj->getAge();
	$maritalStatus=$commonObj->getMaritalStatus();
	$motherTongue=$commonObj->getMotherTongue();
	$education=$commonObj->getEducation();
	$empStatus=$commonObj->getEmpStatus();  
	$purJoining=$commonObj->getPurJoining();
	$englishExp=$commonObj->getEnglishExp();
	$usersDicover=$commonObj->getUsersDicover();
if(!empty($_GET['uid'])){
	//  $countClass ="displayNone";
	  $regClass ="";
	  $errDiv = "";
	  $pageType ="Edit";
	  $userType=$teacherData->userType;
	  if($userType!='b2b'){
		$disabledBatch  ='readonly';
	  }else{
		 $disabledBatch  =''; 
	  }
	  
  }else{
	  $pageType ="Create";
      //$countClass ="";
	 }
 if($is_teacher=='Yes'){
	 $disabled='disabled';
 }else{
	 $disabled='';
	 }
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="teacherList.php" title=" <?php echo $language[$_SESSION['language']]['district_admin'] ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['district_admins']; ?> </a></li>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
	 
       <div class="panel panel-default marginBottom5">
	    <div class="row m-l-none m-r-none bg-light lter">
	    <div class="col-sm-6 col-md-6 padder-v b-light" title=" <?php echo $language[$_SESSION['language']]['maximum_district_admin_limit'] ?>">
		<div class="col-sm-4 padd0 text-right">
		<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-info"></i>
		  <i class="fa fa-users fa-stack-1x text-white"></i>
		  </span>
		</div>
		<div class="col-sm-8 padd0">
		 <a class="">
			<div class="h3  m-t-xs"><strong id="totalLimit"><?php echo $trainer_limit; ?></strong></div>
			<div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['maximum_district_admin_limit']; ?></small></div>
		 </a>
		</div>
	  </div>
	 <div class="col-sm-6 col-md-6 padder-v b-l b-light lt" title=" <?php echo $language[$_SESSION['language']]['remaining_district_admin_limit'] ?>">                     
		<div class="col-sm-4 padd0 text-right">
			<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-success"></i>
		  <i class="fa fa-user fa-stack-1x text-white"></i>
		   </span> 
		</div>
		 <div class="col-sm-8 padd0">
			<a class="clear">
			  <div class="h3  m-t-xs"><strong id="remainLimit"><?php echo $remaining; ?></strong></div>
			  <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['remaining_district_admin_limit']; ?></small></div>
			</a>
		 </div>
		</div>
	 </div>
   </div> <div class="clear"></div>
       <?php if($errDiv!=''){?>
      <div class="alert alert-danger col-sm-12">
      
        <i class="fa fa-ban-circle"></i><?php echo $errDiv;?> </div>
      <?php } ?>
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
		<?php if($err == '2'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>  
		  <?php if($err == '3'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo "You have reached maximum limit of $teacher allowed. You can not register more $teacher.";?> </div>
		  <?php } ?>  <div class="clear"></div>
    <form action="ajax/userFormSubmit.php" id="createTeacherForm" name="createTeacherForm" class="createTeacherForm" method="post"  data-validate="parsley" autocomplete="nope">
	  <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		<h3 class="panel-header"><?php echo $language[$_SESSION['language']]['create_district_admin']; ?></h3>
		 
             <div>
				<div class="form-group col-sm-6">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['first_name']; ?><span class="required">*</span></label>
				  <input type="text" name="name" id="name" placeholder="<?php echo $language[$_SESSION['language']]['first_name']; ?>" class="form-control input-lg "  data-required="true" value="<?php echo $teacherData->first_name; ?>" maxlength = "30" autocomplete="nope"/>
				</div>
				<div class="form-group col-sm-6">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['last_name']; ?><span class="required">&nbsp;</span></label>
				  <input type="text" name="lastname" id="lastname" placeholder="<?php echo $language[$_SESSION['language']]['last_name']; ?>" class="form-control input-lg " value="<?php echo $teacherData->last_name; ?>" maxlength = "30" autocomplete="nope"/>
				</div>
				<div class="clear"></div>
			   <div class="form-group col-sm-6">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['login_id']; ?><span class="required"> <?php echo (($teacherData->email_id=='')?'*':'');?></span></label>
				  <input name="email" id="email" placeholder="abc@example.com" class="form-control input-lg <?php echo (($teacherData->email_id=='')?'':'disabledInput');?>" value="<?php echo $teacherData->email_id; ?>"  data-type="email" <?php echo isset($teacherData->email_id)? "readonly" : ""; ?>  data-required="true" maxlength = "50" autocomplete="email-12" onkeypress="checkEmailExistFn(this.id,'emailErr')" onblur="checkEmailExistFn(this.id,'emailErr')"/>
			  <div class="required error" id="emailErr"></div>
				</div>
				<div class="form-group col-sm-6">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['mobile_number']; ?> <span class="required"></span></label>
					<input name="mobile" id="mobile" placeholder="<?php echo $language[$_SESSION['language']]['mobile_number']; ?> " class="form-control input-lg"   data-type="phone"  data-minlength="[10]"  maxlength="10" data-regexp="^[1-9]\d*$" data-regexp-message="Mobile number should not be 0" value="<?php echo $teacherData->phone; ?>" autocomplete="nope"/>
				</div>
				<div class="clear"></div>
				<div class="form-group col-sm-6 <?php if($teacherData->password!=''){echo "hide";}?>">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['password']; ?>  <span class="required">*</span></label>
				<input type = "password" name="password" id="password" placeholder="" class="form-control input-lg " maxlength = "15" value="" autocomplete="pwd" <?php if($teacherData->password!=''){echo "readonly";}else{?>  data-required="true" data-regexp="<?php echo $passRegexp;?>" data-regexp-message="<?php echo $passRegexpMsg;?>"<?php }?>/>
			 <label class="" style="font-size: 12px;margin-top:5px" id="login_pass"><?php echo$passValidMsg; ?></label>
				</div>
				<div class="form-group col-sm-6 <?php if($teacherData->password!=''){echo "hide";}?>">
				 <label class="control-label"><?php echo $language[$_SESSION['language']]['confirm_password']; ?>  <span class="required">*</span></label>
				  <input type = "password" name="cpassword" id="cpassword" placeholder="" class= "form-control input-lg " maxlength = "15"  autocomplete="nope" value=""  data-equalto="#password"  <?php if($teacherData->password!=''){echo "readonly";}else{?>data-required="true" <?php }?>/>
				</div> 
				<div  class="col-sm-12 <?php if($teacherData->password==''){echo "hide";}?>">
				<div >
				<a href="javascript:void(0)" title=" <?php echo $language[$_SESSION['language']]['change_password'] ?>" onclick="return showHideChangePassword('ajax/changePassword.php','shcpDiv','createTeacherForm', 'oldPassword', 'newPassword', 'cnfPassword');"  class="btn btn-s-md btn-primary chpassword pointer marginTop20"><i class="passwordIcon fa fa-plus"></i> <?php echo $language[$_SESSION['language']]['change_password'] ?></a>
				 <input type='hidden' name='user_session_id' id='user_session_id' value='<?php echo base64_encode($teacherData->loginid);?>' />
				</div>
				<div class="clear"></div>
				</br>
				<div id="shcpDiv">
				</div>
			  
			</div>
			 <div class="clear"></div>
				 <div class="form-group col-sm-6">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['state_name']; ?>  <span class="required">*</span></label>
				 <select class="form-control input-lg parsley-validated fld_class  <?php echo (($status==0)?'':'disabledInput');?>" name="center_id" id="center_id" data-required="true" onchange="selectCenter(this);">
				 <option  value="" ><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
				  <?php 
					 foreach ($centers_arr as $key => $value) {	
					   $centerName= $centers_arr[$key]['name'];
					   $centerId= $centers_arr[$key]['center_id']; 
					
					  $selectedCenter =  (  $centerId == $teacherData->center_id ) ?  'selected ="selected"' : '';
					  
					
					 ?>
					<option  value="<?php echo $centerId; ?>" <?php echo $selectedCenter; ?> <?php echo $dfpdVar;?>><?php echo $centerName;?></option>	
					 <?php }?>
				</select>
			 </div>
			
			
			<div  class="form-group col-sm-6"  >
				<div id="classSectionDtl1" class="clear">
				<label class="control-label"><?php echo $language[$_SESSION['language']]['classes']; ?>  <span class="required">*</span></label> 
		<select class="form-control input-lg parsley-validated fld_class " name="batch[]" id="batch" data-required="true" multiple <?php echo $disabledBatch;?>>

				  <!--<option value="" ><?php echo $language[$_SESSION['language']]['select_class'];?></option>-->
				  <?php  
				   foreach($batchInfo as $key => $bValue){
					 $selectedBtch = '';
					 
					  if (in_array($bValue['batch_id'], $selectBatchList)){
						$selectedBtch='selected';
					} 
					//$batchList =$teacherData->batch_id;
					//for ($i=0; $i < count( $batchList); $i++) { 
						// $selectedBtch .=  (  $batchList[$i]['batch_id'] == $bValue['batch_id'] ) ?  'selected ="selected"' : '';
					 // }  
					?>
				  <option value="<?php echo $bValue['batch_id']; ?>"  <?php echo $selectedBtch; ?> ><?php echo $bValue['batch_name']; ?></option>
				  <?php }?>					
				</select>
				</div>
			  </div>
			  
			</div>
			
		   </div>
		   
		   </div>
		  <div class="clear"></div>
		<div class="panel panel-default bdrNone hide">
			<div class="panel-body padd20">
		 	   <h3 class="panel-header">Personal Information</h3>
		     <div>
			  <div  class="form-group col-sm-6">
				<label class="control-label">Gender </label>
				 <select class="form-control input-lg parsley-validated fld_class" name="gender" id="gender" >
				  <option value="" >Select</option>
				 <?php foreach($gender as $key => $genderValue){
					 $selectedGender = '';
					if($teacherData->gender==$genderValue['id']){$selectedGender="selected";};
						?>
				  <option value="<?php echo $genderValue['id']; ?>"  <?php echo $selectedGender; ?> ><?php echo $genderValue['description']; ?></option>
				  <?php }  ?>						
				</select>				
				</select>
			  </div>
			  
			  <div class="form-group col-sm-6">
			 
          <label class="control-label">Age Group  </label>
			 <select class="form-control input-lg parsley-validated fld_class" name="age" id="age">
			  <option value="" >Select</option>
				 <?php foreach($age as $key => $ageValue){
					    $ageStatus = '';
					 
						 $ageStatus =  ($teacherData->age_range == $ageValue['id'] ) ?  'selected ="selected"' : '';
					?>
				  <option value="<?php echo $ageValue['id']; ?>"  <?php echo $ageStatus; ?> ><?php echo $ageValue['age_range']; ?></option>
				  <?php }  ?>						
				</select>
			</div>
			
			  <div class="form-group col-sm-6">
			  <label class="control-label">Country </label>
			   <?php if(isset($_GET['uid'])){?>
			   
			     <select class="form-control input-lg parsley-validated fld_class" name="country" id="country" >
			   <option value="" >Select</option>
			  <?php foreach($resCountry as $key => $countryValue){
					 
						 $selectedCountryStatus =  ($teacherData->country == $countryValue['country_name'] ) ?  'selected ="selected"' : '';
					  
					?>
				  <option value="<?php echo $countryValue['country_name']; ?>"  <?php echo $selectedCountryStatus; ?> ><?php echo $countryValue['country_name']; ?></option>
				  <?php }  ?>
			  
				
				</select>
                <?php }else{?>
			      <input type = "hidden" name="country" id="country" value="<?php echo $defaultCountry; ?>" />
			    <?php }?>
			
			</div>
			
			  <div class="form-group col-sm-6">
				<label class="control-label">Native Language </label>
				  <?php if(isset($_GET['uid'])){?>
					<select class="form-control input-lg parsley-validated fld_class" name="motherTongue" id="motherTongue">
				 <option value="" >Select</option>
				  <?php foreach($motherTongue as $key => $motherTongueValue){
					 
						 $selectedMotherTongue =  (  $teacherData->mother_tongue == $motherTongueValue['id'] ) ?  'selected ="selected"' : '';
					
					?>
				  <option value="<?php echo $motherTongueValue['id']; ?>"  <?php echo $selectedMotherTongue; ?> ><?php echo $motherTongueValue['name']; ?></option>
				  <?php }  ?>					
				</select>
			     <?php }else{?>
			      <input type = "hidden" name="motherTongue" id="motherTongue" value="38" />
			    <?php }?>
						
				
			</div>
			<div class="form-group col-sm-6">
			  <label class="control-label">Years of English Education  </label>
			 <select class="form-control input-lg parsley-validated fld_class" name="englishExp" id="englishExp" >
				 <option value="" >Select </option>
				  <?php  foreach($englishExp as $key => $englishExpValue){
				
						 $selectedEnglishExp =  (  $teacherData->englishexp == $englishExpValue['id'] ) ?  'selected ="selected"' : '';
					 
					?>
				  <option value="<?php echo $englishExpValue['id']; ?>"  <?php echo $selectedEnglishExp; ?> ><?php echo $englishExpValue['name']; ?></option>
				  <?php }  ?>					
				</select>	
			</div>
			
			
			
			  <div class="form-group col-sm-6">
			  <label class="control-label">Employment Status </label>
			<select class="form-control input-lg parsley-validated fld_class" name="empStatus" id="empStatus" >
				  <option value="" >Select</option>
				  <?php foreach($empStatus as $key => $empStatusValue){

						 $selectedEmpStatus =  (  $teacherData->employment_status == $empStatusValue['id'] ) ?  'selected ="selected"' : '';
					 
					?>
				  <option value="<?php echo $empStatusValue['id']; ?>"  <?php echo $selectedEmpStatus; ?> ><?php echo $empStatusValue['name']; ?></option>
				  <?php }  ?>			
				</select>				
				
			</div>
			 <div class="form-group col-sm-6">
			  <label class="control-label">Education Qualification  </label>
			 <select class="form-control input-lg parsley-validated fld_class" name="education" id="education" >
				 <option value="" >Select </option>
				  <?php  foreach($education as $key => $educationValue){
				
						 $selectedEducation =  (  $teacherData->education == $educationValue['id'] ) ?  'selected ="selected"' : '';
					 
					?>
				  <option value="<?php echo $educationValue['id']; ?>"  <?php echo $selectedEducation; ?> ><?php echo $educationValue['name']; ?></option>
				  <?php }  ?>					
				</select>	
			</div>
			
			<div class="clear"></div>
			
			<div class="form-group col-sm-6">
			  <label class="control-label">Purpose for Joining </label>
			 <select class="form-control input-lg parsley-validated fld_class" name="purJoining" id="purJoining" >
				  <option value="" >Select</option>
				  <?php foreach($purJoining as $key => $purJoiningValue){

						 $selectedPurJoining =  (  $teacherData->joining_purpose == $purJoiningValue['id'] ) ?  'selected ="selected"' : '';
					 
					?>
				  <option value="<?php echo $purJoiningValue['id']; ?>"  <?php echo $selectedPurJoining; ?> ><?php echo $purJoiningValue['name']; ?></option>
				  <?php }  ?>				
				</select>
			</div>
			
			  
			 </div>
		    </div>
	        </div>
		  <div class="clear"></div>
		   <div class="text-right"> 
		   <?php if(!empty($teacherData)){?>
		   <a href='javascript:void(0)' onclick="deleteUserConfirm(<?php echo $uId; ?>);" class="btn btn-primary "  title="<?php echo $language[$_SESSION['language']]['delete']; ?>"><?php echo $language[$_SESSION['language']]['delete']; ?></a>&nbsp;&nbsp; <?php }?>
			  <a href='teacherList.php' class="btn btn-primary " title=" <?php echo $language[$_SESSION['language']]['cancel'] ?>"><?php echo $language[$_SESSION['language']]['cancel']; ?> </a>&nbsp;&nbsp;
			   <input id="profile_id" type="hidden" name="profile_id" value="<?php echo $teacherData->profile_id; ?>"/>
			  <input id="userIdVal" type="hidden" name="userIdVal" value="<?php echo $uId; ?>"/>
			    <input id="client_id" type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
				 <input id="userType" type="hidden" name="userType" value="<?php echo $teacherData->userType; ?>"/>
				<input type="hidden" id="cpFlag" value="0" />
			 <button type="submit" title=" <?php echo $language[$_SESSION['language']]['submit'] ?>" class="btn btn-s-md btn-primary pre-loader" name="uSignUp" value='teacherReg' id="uSignUp"  onclick="showLoaderOrNot('createTeacherForm');" ondblclick="showLoaderOrNot('createTeacherForm');"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
	        </div>
		 </div>  
     </form>
   </section> 
  </div>
 </div>
</section>
<?php include_once('../footer/adminFooter.php');?>
<script>
function checkEmailExistFn(id,errid){
	$("#"+errid).text("");
	var cValue=$("#"+id).val();
	cValue=cValue.trim();
	//alert(cValue)
	var dataString = 'email='+ cValue;
  if(cValue!=''){
    $.ajax({
		type: "POST",
		url: "ajax/checkEmail.php",
		data: dataString,
		cache: false,
		success: function(result){
			console.log(result);
			if(result==1){
				$("#"+id).val(' ');
			   $("#"+errid).text("Login id is already exist. Please try another.");
				cValue=cValue.trim();
			  // alertPopup("<?php echo $student; ?> login id is already exist. Please try another.");
			   return false;
			}else{
				
			}
			
		}
	});
  }
}
/*function selectCenter(e){

	 if(e.value!=''){
		 showLoader();
			$.ajax({
			  type: 'POST',
			  url: "ajax/getCenterDetailsRegistration.php",
			data: {centerId:e.value,customerId:<?php echo $customer_id;?>,roleId:1},
			  dataType: "text",
			  success: function(res) { 
			 // console.log(res);
			 var data = JSON.parse(res);
			 //console.log(data);
			   $("#totalLimit").html(data['center_detail']["teacher_limit"]);
			   $("#totalSignUp").html(data['signup_detail']["totalCenterTeacher"]);
			   var remain=data['center_detail']["teacher_limit"]-data['signup_detail']["totalCenterTeacher"];
			   $("#remainLimit").html(remain);
			   var batch="";
			  // console.log(data['batch']);
			   for(var i=0;i<data['batch'].length;i++){
				 var batch_id=data['batch'][i]['batch_id'];
				 var batch_name=data['batch'][i]['batch_name'];
				 batch+="<option value="+batch_id+">"+batch_name+"</option>";
			   }
			   $("#batch").html("<option value=''>Select <?php echo $batch; ?></option>"+batch);
			   hideLoader();
			   
			  }
		  });
	
   }
}*/

function selectCenter(e){

	 if(e.value!=''){
		  var cName=$("#"+e.id+" option:selected" ).text();
		 showLoader();
			$.ajax({
			  type: 'POST',
			  url: "ajax/getCenterDetailsRegistration.php",
			data: {centerId:e.value,customerId:<?php echo $customer_id;?>,roleId:1},
			  dataType: "text",
			  success: function(res) { 
			 // console.log(res);
			 var data = JSON.parse(res);
			 console.log(data);
			   $("#totalLimit").html(data['center_detail']["trainer_limit"]);
			   $("#totalSignUp").html(data['signup_detail']["totalCenterTeacher"]);
			   var remain=data['center_detail']["trainer_limit"]-data['signup_detail']["totalCenterTeacher"];
			   $("#remainLimit").html(remain);
			    $("#country").html(data['center_detail']["country"]);
			  
			    var batch="";
			   //console.log(data['batch']);
			   for(var i=0;i<data['batch'].length;i++){
				 var batch_id=data['batch'][i]['batch_id'];
				// if(data['center_detail']["default_batch_id"]!=batch_id){
				    var batch_name=data['batch'][i]['batch_name'];
				    batch+="<option value="+batch_id+">"+batch_name+"</option>";
				// }
			   }
			   $("#batch").html(batch);
			   hideLoader();
			   
			  }
		  });

		
	
   }
}

function addParsely(formId,old_password,pwd, cpwd){
	//$('#'+formId).parsley('addItem', '#'+old_password);
	$('#'+formId).parsley('addItem', '#'+pwd);
	$('#'+formId).parsley('addItem', '#'+cpwd);
}

function removeParsely(formId,old_password,pwd, cpwd){
	$('#'+formId).parsley('destroy');
	$('#'+formId).parsley();
	//$('#'+formId).parsley('removeItem',  '#'+old_password);
	$('#'+formId).parsley('removeItem', '#'+pwd);
	$('#'+formId).parsley('removeItem', '#'+cpwd);
	
}

function showHideChangePassword(path,targetDiv,formId,old_password,pwd, cpwd){	
	showLoader();
	$('#'+formId).parsley();
	var cpFlag = $("#cpFlag").val();
	if(cpFlag == 0){
		$.post(path, {shpass: cpFlag}, function(data){
			$("#"+targetDiv).html(data);
			$("#cpFlag").attr('value','1');
			addParsely(formId,old_password,pwd, cpwd);
		});
		hideLoader();
	}else{	
		removeParsely(formId,old_password,pwd, cpwd);			
		$("#"+targetDiv).html('');
		$("#cpFlag").attr('value','0');		
		hideLoader();
	}
}


$(document).ready(function(){
	$(".chpassword").click(function(){
	   $(".passwordIcon").toggleClass('fa-plus fa-minus')
	});
 })


function deleteUserConfirm(userId) {
	if(userId!=''){
		  alertify.confirm("Are you sure you want to delete?", function (e) {
				 if (e) {
					 showLoader();
					 var dataString ='userId='+ userId;
						$.ajax({
							url : 'ajax/deleteUser.php',
							type : 'POST',
							data : dataString,
							success: function(result){
								console.log(result);
								hideLoader();
								if(result==1){
									 alertify.alert("<?php echo $teacher;?> deleted successfully")
									document.location.href='teacherList.php';
								 
									
								}else{
								alertify.alert("<?php echo $teacher;?> not deleted. Please try again.")	
								}
							}
						});

					} else {
						
						hideLoader();
					}
			});
	}
	
}



</script>
