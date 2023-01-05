<?php 
include_once('../header/adminHeader.php');

$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$student not saved. Please try again.";
	}
	if($_SESSION['error'] == '2'){
		$msg = "$student login is already exist. Please try another.";
	}
	if($_SESSION['error'] == '3'){
		$msg = " You have reached maximum limit of $student allowed. You can not register more $student.";
	}
}

if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "$student created successfully.";
	}
	if($_SESSION['succ'] == '2'){
		$msg = "$student updated successfully.";
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

if(isset($_GET['uid'])){
  $uId = trim(base64_decode($_GET['uid']) );
	if(is_numeric($uId)==true){
	  $studentData = $centerObj->getUserDataByID($uId, 2); // student role 2
	  $batchId=$studentData->batch_id[0]['batch_id'];
	  $status=$studentData->is_active;
	}else{
		header('Location: dashboard.php');
		die;
	}
}
 //echo "<pre>";print_r($studentData);exit;
 $getSignedUpUser =  $centerObj->getSignedUpUserCountByCenter($client_id,$studentData->center_id);

$adminObj = new centerAdminController();
//$checkEmailExits = $adminObj->checkEmailExits();
$batchInfo = $adminObj->getBatchDeatils($studentData->center_id);
$centerDetails=$clientObj->getCenterDetailsById($studentData->center_id);	

$student_limit=(!empty($centerDetails[0]['student_limit'])?$centerDetails[0]['student_limit']:0);
//total user limit
if($student_limit != 0){	
	$remaining = $student_limit - $getSignedUpUser->totalCenterStudent;
	
	if($remaining<0){
		$remaining=0;
	}
	if($remaining < 1){
		$errDiv = "<div class='col-sm-12  marginTop40 paddLeft40  paddRight40 '> <div class='alert alert-danger height60 fontSize16 paddTop20'>
					
					<i class='fa fa-ok-sign'></i> You have reached maximum limit of $student allowed. You can not register more $student. Please contact administrator.
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
	  $pageType ="Edit ".$student;
	  $disabled='disabled';
	  $emailHide='';
      //$countryData= $resCountry[0]['id'];
	  $countryData= $resCountry[0]['country_name'];
      $stateData=$studentData->state;
      $cityData=$studentData->city;
	  $expiryDate= $studentData->expiry_date;
      $expiryDate = date('d-m-Y',strtotime($expiryDate));
	   $default_batch_id=$studentData->default_batch_id;
	   $userType= $studentData->userType;
    if($default_batch_id==$studentData->batch_id){
	    $ActiveStatus=(($status==0)?'':'disabledInput');
	  }else{
		  $ActiveStatus=(($status==1)?'':'disabledInput');  
	  }
	if($userType!='b2b'){
		//$disabledBatch  ='readonly';
		 $disabledBatch  =''; 
	  }else{
		 $disabledBatch  =''; 
	  } 
	  
  }else{
	  $pageType =$language[$_SESSION['language']]['add_learner'];
      //$countClass ="";
	  $disabled="";
     $emailHide='';
	  //$resCountry=$commonObj->getCountry();	
	 // $countryData= $resCountry[0]['id'];
       $countryData= $resCountry[0]['country_name'];
	  $stateData='';
      $cityData=''; 
	  $ActiveStatus='';
	}

?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="studentList.php" title=" <?php echo $language[$_SESSION['language']]['learners'] ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['learners']; ?> </a></li>
 <span class="pull-right"> <a title=" <?php echo $language[$_SESSION['language']]['bulk_upload_learners'] ?>" href='bulkStudentUpload.php' class="btn btn-primary "><?php echo $language[$_SESSION['language']]['bulk_upload_learners']; ?></a>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
       <section class="panel panel-default  marginBottom5">
	   <div class="row m-l-none m-r-none bg-light lter">
	  <div class="col-sm-6 col-md-6 padder-v b-light" title=" <?php echo $language[$_SESSION['language']]['maximum_learner_limit']?>">
		<div class="col-sm-4 padd0 text-right">
		<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-info"></i>
		  <i class="fa fa-users fa-stack-1x text-white"></i>
		  </span>
		</div>
		<div class="col-sm-8 padd0">
		 <a class="">
			<div class="h3  m-t-xs"><strong id="totalLimit"><?php echo $student_limit; ?></strong></div>
			<div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['maximum_learner_limit']; ?> </small></div>
		 </a>
		</div>
	  </div>
	 <div class="col-sm-6 col-md-6 padder-v b-l b-light lt" title=" <?php echo $language[$_SESSION['language']]['remaining_learner_limit']?>" >                     
		<div class="col-sm-4 padd0 text-right">
			<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-success"></i>
		  <i class="fa fa-user fa-stack-1x text-white"></i>
		   </span> 
		</div>
		 <div class="col-sm-8 padd0">
			<a class="clear">
			  <div class="h3  m-t-xs"><strong id="remainLimit"><?php echo $remaining; ?></strong></div>
			  <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['remaining_learner_limit']; ?></small></div>
			</a>
		 </div>
		</div>
	</div>
      </section>
    </br>  <div class="clear"></div>
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
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?> 
		    <div class="clear"></div>
   <form action="ajax/userFormSubmit.php" id="createStudentForm" class="createStudentForm" method="post"  data-validate="parsley" autocomplete="nope">
	  <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		 	<h3 class="panel-header"><?php echo $pageType; ?></h3>
		   <div>
		    <div class="col-sm-4 col-xs-4">
                <div><label class="control-label" for="logo"><?php echo $language[$_SESSION['language']]['photo']; ?> </label>
				  </div>       
				<div class="profile text-left profileBg profileBgLogo marginTop20" style="padding-top:20px" title=" <?php echo $language[$_SESSION['language']]['edit'].' '.$language[$_SESSION['language']]['photo'] ?>" > 
				<?php if($studentData->system_name != ''){ ?>
							<div class="thumb-md thumb-md-logo text-left  fileInputs buttonImg relative" id="logoImg" >
							<img id="viewImgProfile"  class="viewImgProfile imgBorder dataImg bdrCircle" src="<?php echo $profile_img_hosting_url.$studentData->system_name; ?>" /> 
							<span class="defaultImgShow uploadIcon" style="display:none">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="uploadProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');"> <i class="fa fa-edit"></i></a> </span>
						<span class="dataImgShow uploadIcon">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="editProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');" style="cursor:pointer"> <i class="fa fa-edit"></i> </a></span>
							
							</div>
							<input type="file" id="fileProfile" name="fileImgProfile" style="display: none;"accept="image/gif, image/jpeg, image/png"/>
							
							 <input type="hidden" name="fileImgNamePro" id="fileImgNamePro" value="<?php echo $studentData->system_name; ?>"  readonly=""/> 
							 
							  
								<?php }else{ ?>
								<div class="thumb-md thumb-md-logo fileInputs buttonImg relative" id="editProfileImg">
								<img id="viewImgProfile"  class="viewImgProfile bdrCircle"  src="<?php echo $profileImgDefault; ?>"/> 

								<span class="defaultImgShow uploadIcon">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="uploadProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');" style="cursor:pointer"> <i class="fa fa-edit"></i></a>
								</span>	 </div>
	                         <div class="clear"></div>
							  
								<input type="file" id="fileProfile" name="fileImgProfile" style="display: none;"accept="image/gif, image/jpeg, image/png"/>
									 <input type="hidden" name="fileImgNamePro" id="fileImgNamePro" value=""  readonly=""/> 
								<?php  }?>
								<div class="clear"></div>
							<label class="" id="profile_picError" style="margint-top:10px;font-size:12px;display:none">	(File Support- png, jpg, jpeg, gif)</label>
						</div>
                    <label class="required showErr" id="profile_picError"><?php echo $msgpic; ?></label>
				  </div>
		    <div class="form-group col-sm-8">
		   
               <h4 class="text-left fontSize14 bold"><?php echo $language[$_SESSION['language']]['account_information']; ?></h4>
		   <div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['first_name']; ?> <span class="required">*</span></label>
			  <input type="text" name="name" id="name" placeholder="<?php echo $language[$_SESSION['language']]['first_name']; ?>" class="form-control input-lg "  data-required="true" value="<?php echo $studentData->first_name; ?>" maxlength = "30" autocomplete="nope" pattern="^[a-zA-Z]*$" title="Enter valid name"/>
			</div>
			 <div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['last_name']; ?><span class="required">&nbsp;</span></label>
			  <input type="text" name="lastname" id="lastname" placeholder="<?php echo $language[$_SESSION['language']]['last_name']; ?>" class="form-control input-lg "  value="<?php echo $studentData->last_name; ?>" maxlength = "30" autocomplete="nope" pattern="^[a-zA-Z]*$"  title="Enter valid name"/>
			</div>
			<div class="clear"></div>
		   <div class="form-group col-sm-6 <?php echo $emailHide; ?>">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['login_id']; ?> <span class="required"> <?php echo (($studentData->email_id=='')?'*':'');?></span></label>
			  <input name="email" id="email" placeholder="abc@example.com" class="form-control input-lg inputText <?php echo (($studentData->email_id=='')?'':'disabledInput');?>" value="<?php echo $studentData->email_id; ?>"  data-type="email" <?php echo $disabled; ?>  data-required="true" maxlength = "50" autocomplete="email-12" onkeypress="checkEmailExistFn(this.id,'emailErr')" onblur="checkEmailExistFn(this.id,'emailErr')"/>
			  <div class="required error" id="emailErr"></div>
			</div>
			<div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['mobile_number']; ?> <span class="required"></span></label>
			    <input name="mobile" id="mobile" placeholder="<?php echo $language[$_SESSION['language']]['mobile_number']; ?>" class="form-control input-lg" value="<?php echo $studentData->phone; ?>" data-type="phone"  data-minlength="[10]"  maxlength="10" data-regexp="^[1-9]\d*$" data-regexp-message="Mobile number should not be 0" autocomplete="nope"/>
			</div><div class="clear"></div>
			  <?php if(isset($_GET['uid'])){
				  if($studentData->userType!='b2b'){?>
			<div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['expiry_date']; ?>  <span class="required">*</span></label>
			   <div id="expiryDate" class="input-append date form-control input-lg">
				<input  data-date-format="DD-MM-YYYY" readonly="true"  name="expiry" id="expiry" placeholder="DD-MM-YYYY" class=" width100per bdrNone" autocomplete="nope" tabindex="4" value='<?php echo $expiryDate; ?>' style="width: 120px;"/>
					<span class="calendarBg add-on top30"  style="top: 30px;"> <i class="fa fa-calendar"></i>
					</span></div> 
				</div>
				
				  <?php } }?>
			<div class="form-group col-sm-6"> 
			 <label class="control-label"><?php echo $language[$_SESSION['language']]['status']; ?> <span class="required">*</span></label>
			<select id="status" name="status" class="form-control"  data-required="true">
			
				<option value=""><?php echo $language[$_SESSION['language']]['status']." ".$language[$_SESSION['language']]['select']; ?></option>
				<option  value="1" <?php echo $selectedStatus =($status=='1')?'selected' : '';?>>Active</option>	
				<option  value="0" <?php echo $selectedStatus =($status=='0')?'selected' : '';?>>Inactive</option>
				</select>
			</div>
			</fieldset>
			</div>
			<div class="clear"></div>
			<div class="form-group col-sm-6 <?php if($studentData->password!=''){echo "hide";}?>"  >
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['password']; ?> <span class="required">*</span></label>
			<input type = "password" name="password" id="password" placeholder="<?php echo $language[$_SESSION['language']]['password']; ?>" class="form-control input-lg " value=""  <?php if($studentData->password!=''){echo "readonly";}else{?>  data-required="true" data-regexp="<?php echo $passRegexp;?>" data-regexp-message="<?php echo $passRegexpMsg;?>"<?php }?> maxlength="15"   autocomplete="pwd"/>
			 <label class="" style="font-size: 12px;margin-top:5px" id="login_pass"><?php echo $language[$_SESSION['language']]['the_password_must_have_8_or_more_characters,_at_least_one_uppercase_letter,_and_one_number']; ?></label>
		
			</div>
			<div class="form-group col-sm-6 <?php if($studentData->password!=''){echo "hide";}?>" >
		   <label class="control-label"><?php echo $language[$_SESSION['language']]['confirm_password']; ?> <span class="required">*</span></label>
			  <input type = "password" name="cpassword" id="cpassword" placeholder="<?php echo $language[$_SESSION['language']]['confirm_password']; ?>" class= "form-control input-lg"  value=""  data-equalto="#password"  maxlength="15"  autocomplete="nope"  <?php if($studentData->password!=''){echo "readonly";}else{?>data-required="true" <?php }?> />
			</div> 
			<div  class="col-sm-12 <?php if($studentData->password==''){echo "hide";}?>">
				<div >
				<a href="javascript:void(0)"  onclick="return showHideChangePassword('ajax/changePassword.php','shcpDiv','createStudentForm', 'oldPassword', 'newPassword', 'cnfPassword');"  class="btn btn-s-md btn-primary chpassword pointer marginTop20"><i class="passwordIcon fa fa-plus"></i> <?php echo $language[$_SESSION['language']]['change_password']; ?></a>
				 <input type='hidden' name='user_session_id' id='user_session_id' value='<?php echo base64_encode($studentData->loginid);?>' />
				</div>
				<div class="clear"></div>
				</br>
				<div id="shcpDiv">
				</div>
			  
			</div>
             <div class="clear"></div>
				 <div class="form-group col-sm-6">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['state_name']; ?> <span class="required"> <?php echo (($teacherData->email_id=='')?'*':'');?></span></label>
				 <select class="form-control input-lg parsley-validated fld_class <?php echo (($status==0)?'':'disabledInput');?>" name="center_id" id="center_id" data-required="true" onchange="selectCenter(this);" >
				  <option  value="" ><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
				  <?php $dfpdVar='';
					 foreach ($centers_arr as $key => $value) {	
					   $centerName= $centers_arr[$key]['name'];
					   $centerId= $centers_arr[$key]['center_id']; 
					 
					  $selectedCenter =  (  $centerId == $studentData->center_id ) ?  'selected ="selected"' : '';
					  if( $centerId == $studentData->center_id && $centerName=='DFPD'){
						$dfpdVar=  $centerName;
					  }
					 ?>
					<option  value="<?php echo $centerId; ?>" <?php echo $selectedCenter; ?> ><?php echo $centerName;?></option>	
					 <?php }?>
				</select>
				<input type = "hidden" name="cCenter" id="cCenter" value="<?php echo $studentData->center_id; ?>" />
				<input type = "hidden" name="default_batch_id" id="default_batch_id" value="<?php echo $studentData->default_batch_id; ?>" />
			 </div>
			 
			<div  class="form-group col-sm-6"  >
				<div id="classSectionDtl1" class="clear">
				<label class="control-label"><?php echo $language[$_SESSION['language']]['class']; ?> <span class="required">*</span></label>
		<select class="form-control input-lg parsley-validated fld_class <?php echo $ActiveStatus;?>" name="batch" id="batch" data-required="true" <?php echo $disabledBatch;?>>
				  <option value="" ><?php echo $language[$_SESSION['language']]['select_class'];?></option>
				  <?php  
				   foreach($batchInfo as $key => $bValue){
					 $selectedBtch = '';
					 $batchList =$studentData->batch_id;
					for ($i=0; $i < count( $batchList); $i++) { 
						 $selectedBtch .=  (  $batchList[$i]['batch_id'] == $bValue['batch_id'] ) ?  'selected ="selected"' : '';
					  } 
					$batch_name = explode('-',$bValue['batch_name']);
					if(isset($batch_name[1]) && $batch_name[1]!="")
					{
						$batch_name = $batch_name[1];
					}else{
						$batch_name = $bValue['batch_name']; 
					}
					
					?>
				  <option value="<?php echo $bValue['batch_id']; ?>"  <?php echo $selectedBtch; ?> ><?php echo $batch_name; ?></option>
				  <?php }?>					
				</select>
				<input type = "hidden" name="cBatch" id="cBatch" value="<?php echo $batchId; ?>" />
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
					if($studentData->gender==$genderValue['id']){$selectedGender="selected";};
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
					 
						 $ageStatus =  ($studentData->age_range == $ageValue['id'] ) ?  'selected ="selected"' : '';
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
					 
						 $selectedCountryStatus =  ($studentData->country == $countryValue['country_name'] ) ?  'selected ="selected"' : '';
					  
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
					 
						 $selectedMotherTongue =  (  $studentData->mother_tongue == $motherTongueValue['id'] ) ?  'selected ="selected"' : '';
					
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
				
						 $selectedEnglishExp =  (  $studentData->englishexp == $englishExpValue['id'] ) ?  'selected ="selected"' : '';
					 
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

						 $selectedEmpStatus =  (  $studentData->employment_status == $empStatusValue['id'] ) ?  'selected ="selected"' : '';
					 
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
				
						 $selectedEducation =  (  $studentData->education == $educationValue['id'] ) ?  'selected ="selected"' : '';
					 
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

						 $selectedPurJoining =  (  $studentData->joining_purpose == $purJoiningValue['id'] ) ?  'selected ="selected"' : '';
					 
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
			  <a href='studentList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary"><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			   <input id="profile_id" type="hidden" name="profile_id" value="<?php echo $studentData->profile_id; ?>"/>
			   <input id="userIdVal" type="hidden" name="userIdVal" value="<?php echo $uId; ?>"/>
			    <input id="client_id" type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
		   <input id="userType" type="hidden" name="userType" value="<?php echo $studentData->userType; ?>"/>
				<input type="hidden" id="cpFlag" value="0" />
			   <button type="submit"  title="<?php echo $language[$_SESSION['language']]['submit']; ?>"  name="uSignUp" value='studentReg'  class="btn btn-s-md btn-primary  pre-loader"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
	      </div>
		 </div> 
     </form>
   </section> 
  </div>
 </div>
</section>

<?php include_once('../footer/adminFooter.php');?>
<script>
// Validation for select course
/* 
var countryData='<?php echo $countryData; ?>';
var stateData='<?php echo $stateData; ?>';
var cityData='<?php echo $cityData; ?>';

 selectCity(countryData);
 selectState(stateData);


function selectCity(country_id){
	
	if(country_id!="-1"){
		loadData('state',country_id);
		$("#city_dropdown").html("<option value=''>Select city</option>");	
	}else{
		$("#state_dropdown").html("<option value=''>Select state</option>");
		$("#city_dropdown").html("<option value=''>Select city</option>");		
	}
}

function selectState(state_id){
	if(state_id!="-1"){
		loadData('city',state_id);
	}else{
		$("#city_dropdown").html("<option value=''>Select city</option>");		
	}
}

function loadData(loadType,loadId){
	showLoader();
	var dataString = 'loadType='+ loadType +'&loadId='+ loadId + '&stateData='+ stateData +'&cityData='+ cityData;
	$("#"+loadType+"_loader").show();
    //$("#"+loadType+"_loader").fadeIn(400).html('Please wait... <img src="image/loading.gif" />');
	$.ajax({
		type: "POST",
		url: "ajax/loadData.php",
		data: dataString,
		cache: false,
		success: function(result){
			$("#"+loadType+"_loader").hide();
			$("#"+loadType+"_dropdown").html("<option value=''>Select "+loadType+"</option>");
			$("#"+loadType+"_dropdown").append(result);  
			hideLoader();
		}
	});
}
 */
var countryData='<?php echo $countryData; ?>';
var type='<?php echo $studentData->type; ?>';
var defaultProfilePath='<?php echo $profileImgDefault; ?>';
var expiry_date='<?php echo $expiryDate; ?>';

function checkNumber(curVal, curId,targetId){
	//$("#errorCenterName").html('');
	if (isNaN( curVal )) {
    // It isn't a number
		//alert('not');
		$("#"+targetId).html('');
	} else {
		//alert('yes');
		// It is a number
		$("#"+curId).val("");
		$("#"+targetId).html('This field accepts alpha-numeric value');
	}
}

function loadCountry(countryData){
	var cValue=$("#country_dropdown :selected").val();
	var dataString = 'country='+ countryData;
	//$("#"+loadType+"_loader").show();
    $.ajax({
		type: "POST",
		url: "ajax/allCountry.php",
		data: dataString,
		cache: false,
		success: function(result){
			//console.log(result)
			$("#country_dropdown").html("<option value=''>--Select--</option>"+result);
			//$("#country_dropdown").html("<option value=''>"+result+"</option>");
			
		}
	});
}

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
			  // alertPopup("<?php echo $student; ?> login is already exist. Please try another.");
			   return false;
			}else{
				
			}
			
		}
	});
  }
}

$(document).ready(function(){
	
	$("#profile-pic-remove").click(function(){
	   var filevalue = $("#fileImgNamePro").val('');
	   $("#viewImgProfile").attr("src",defaultProfilePath);
	   $("#viewImgProfile").removeClass("imgBorder");
	   $(".defaultImgShow").show();
	   $(".dataImgShow").hide();
	  
		});
		
  $(".inputText").keypress(function() {
	   $(".error").html('');
	});		
		
 })
$(function () {
    $("#divBirthDate").datepicker({
        autoclose: true, 
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        startDate: '1-1-1950',
        endDate:'-10y',//new Date(),
    })
    //}).datepicker('update', new Date()); //// current date auto show
});

$(function () {
    $("#expiryDate").datepicker({
        autoclose: true, 
        todayHighlight: true,
        format: 'dd-mm-yyyy',
        startDate: expiry_date

    })
    //}).datepicker('update', new Date()); //// current date auto show
});


window.setTimeout(function() {
    $(".alertHide").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 4000);

function uploadFile(id,inputId,input,textName,viewId,typeMode){

	 // $("#loaderDiv").show();
	
	 $("#"+inputId).click();
	$("#"+inputId).on('change', function(){
		 // alert(this)
	  var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileName = this.files[0].name;
		 //alert(fileName);
		var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
		 if ($.inArray(fileNameExt, validExtensions) == -1) {
			  this.type = '';
			  this.type = 'file';
			 // $('#'+viewId).attr('src',"");  
			  alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
			  //$('#'+viewId).attr('src',defaultProfilePath); 
			 return false;
			 
		 } else{
			
				if (this.files && this.files[0]) { 
				
					var filerdr = new FileReader();
					filerdr.onload = function (e) {
					//	alert(e);
					$('#'+viewId).attr('src', e.target.result);
					$("#"+textName).val(fileName);
					//alert(e.target.result);
					var myformData = new FormData(); 
					var file_data = $("#"+inputId).prop('files')[0];   
					//alert(file_data);
					myformData.append('file', file_data);
					myformData.append('mode', 'fileUpload');
					myformData.append('uId', <?php echo json_encode($uId); ?>);
					
						 $.ajax({
						 //  url : <?php echo json_encode($profile_img_upload_url); ?>,
						   url : 'ajax/upload_profile.php',
						   type : 'POST',
						   data : myformData,
						   async: false,
						   cache: false,
						   processData: false,  // tell jQuery not to process the data
						   contentType: false,  // tell jQuery not to set contentType
						   enctype: 'multipart/form-data',
						   dataType:'json',
						   success : function(data) {
							   console.log(data);
							   console.log(data.uploadOk);
							   if(data.uploadOk==1){
							     //alertPopup(data.msg);
							   }else{
								 alertPopup(data.msg);  
							   }
							   //$("#loaderDiv").hide();
						    }
							
						}); 
					}
					filerdr.readAsDataURL(this.files[0]);
				}
		 }
	});
}

function removeImg(fileImgName,imgId){

	   var fileValue = $("#"+fileImgName).val('');
	   $("#"+imgId).attr("src",defaultProfilePath);
	   $(".defaultImgShow").show();
	   $(".dataImgShow").hide();
	  
		
}



function selectCenter(e){
     $("#batch").html("");

	 if(e.value!=''){
		  var cName=$("#"+e.id+" option:selected" ).text();
		 showLoader();
			$.ajax({
			  type: 'POST',
			  url: "ajax/getCenterDetailsRegistration.php",
			data: {centerId:e.value,customerId:<?php echo $customer_id;?>,roleId:2},
			  dataType: "text",
			  success: function(res) { 
			 // console.log(res);
			 var data = JSON.parse(res);
			 //console.log(data);
			   $("#totalLimit").html(data['center_detail']["student_limit"]);
			   $("#totalSignUp").html(data['signup_detail']["totalCenterStudent"]);
			   var remain=data['center_detail']["student_limit"]-data['signup_detail']["totalCenterStudent"];
			   $("#remainLimit").html(remain);
			   $("#default_batch_id").val(data['center_detail']["default_batch_id"]);
			   $("#country").html(data['center_detail']["country"]);
			   var batch="";
			   //console.log(data['batch']);
			   for(var i=0;i<data['batch'].length;i++){
				 var batch_id=data['batch'][i]['batch_id'];
				 var batch_name=data['batch'][i]['batch_name'];
				 batch+="<option value="+batch_id+">"+batch_name+"</option>";
			   }
			   $("#batch").html("<option value=''><?php echo  $language[$_SESSION['language']]['class'];?></option>"+batch);
			  
			   hideLoader();
			   
			  }
		  });

	
   }else{
	 $("#batch").html("<option value=''><?php echo  $language[$_SESSION['language']]['class'];?></option>");  
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

</script>
