<?php 

include_once('../header/trainerHeader.php');
$centerDetail=$adminObj->getCenterDetails();
//echo "<pre>";print_r($centerDetail);
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];
 $msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$student not saved. Please try again.";
	}
	if($_SESSION['error'] == '2'){
		$msg = "$student email is already exist. Please try another.";
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
	
		//$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);
}

if(isset($_GET['uid'])){
  $uId = trim(base64_decode($_GET['uid']) ); 
    if(is_numeric($uId)==true){
	  $studentData = $adminObj->getUserDataByID($uId, $loginid, 2); // student role 2
	  $batchId=$studentData->batch_id[0]['batch_id'];
	  $status=$studentData->is_active;
	 // echo "<pre>";print_r($status);exit;
	}else{
		header('Location: dashboard.php');
		die;
	 }
}else{
	header('Location: studentList.php');
		die;
}
 $getSignedUpUser = $adminObj->getSignedUpUserCount();
  //echo "<pre>";print_r($getSignedUpUser->student);exit;
  $checkEmailExits = $adminObj->checkEmailExits($email=null);

$student_limit=$centerDetails[0]['student_limit'];
//total user limit
if($student_limit != 0){	
	$remaining = $student_limit - $getSignedUpUser->student;
	
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

	$usersDicover=$commonObj->getUsersDicover();
	  
if(!empty($_GET['uid'])){
	//  $countClass ="displayNone";
	  $emailHide='';
	  $regClass ="";
	  $errDiv = "";
	  $pageType =$language[$_SESSION['language']]['learners_details'];
	  $disabled='disabled';
	  $default_batch_id=$studentData->default_batch_id;
	  if($default_batch_id==$batchId){
	    $ActiveStatus=(($status==0)?'':'disabledInput');
	  }else{
		  $ActiveStatus=(($status==1)?'':'disabledInput');  
	  }
	  
      //$countryData= $resCountry[0]['id'];
	  $countryData= $resCountry[0]['country_name'];
      $stateData=$studentData->state;
      $cityData=$studentData->city;
  }else{
	   $pageType =$language[$_SESSION['language']]['learners_details'];
      //$countClass ="";
	  $disabled="";
	   $emailHide='';
      $ActiveStatus='';
	  //$resCountry=$commonObj->getCountry();	
	 // $countryData= $resCountry[0]['id'];
       $countryData= $resCountry[0]['country_name'];
	  $stateData='';
      $cityData=''; 
	}
$batchInfo = $adminObj->getBatchDeatils($_SESSION['center_id']);

?>
<style>.form-control{border:none;  -webkit-appearance: none;
    -moz-appearance: none;
    text-indent: 1px;
    text-overflow: '';}</style>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="studentList.php" title=" <?php echo $language[$_SESSION['language']]['learners'] ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['learners']; ?> </a></li>
 </ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
      

	  <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		 	<h3 class="panel-header"><?php echo $pageType; ?></h3>
		   <div>
		    <div class="col-sm-4 col-xs-4">
                <div><label class="control-label" for="logo"><?php echo $language[$_SESSION['language']]['photo']; ?>  </label>
				  </div>       
				<div class="profile text-left profileBg profileBgLogo marginTop20" style="padding-top:20px" > 
				<?php if($studentData->system_name != ''){ ?>
							<div class="thumb-md thumb-md-logo text-left  fileInputs buttonImg relative" id="logoImg" >
							<img id="viewImgProfile"  class="viewImgProfile imgBorder dataImg bdrCircle" src="<?php echo $profile_img_hosting_url.$studentData->system_name; ?>" /> 
							
							</div>
							 
							   <!--<div class="clear col-sm-12" style="margin-top:10px;"></div>
							<span class="dataImgShow uploadIcon">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="editProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');" style="cursor:pointer"> <i class="fa fa-edit"></i> Edit</a>
									 | 
									 <a href="javascript:void(0)" style="cursor:pointer" onclick="removeImg('fileImgNamePro','viewImgProfile');" id="profile-pic-remove" ref="edit"  class="remove pointer"> <i class="fa fa-trash-o"></i> Remove</a>
								</span>	 -->
								     
								<?php }else{ ?>
								<div class="thumb-md thumb-md-logo fileInputs buttonImg relative" id="editProfileImg">
								<img id="viewImgProfile"  class="viewImgProfile bdrCircle"  src="<?php echo $profileImgDefault; ?>"/> 
                             </div>
	                         <div class="clear"></div>
							  
								<?php  }?>
								<div class="clear"></div>
							<label class="" id="profile_picError" style="margint-top:10px;font-size:12px;display:none">	(File Support- png, jpg, jpeg, gif)</label>
						</div>
                    <label class="required showErr" id="profile_picError"><?php echo $msgpic; ?></label>
				  </div>
		    <div class="form-group col-sm-8">
		   
               <h4 class="text-left fontSize14 bold"><?php echo $language[$_SESSION['language']]['account_information']; ?></h4>
		   <div class="form-group col-sm-6">
			  <label class="control-label bold"><?php echo $language[$_SESSION['language']]['first_name']; ?> </label>
             <div class="control-label">
              <?php echo $studentData->first_name; ?>
             </div> </div>
			 <div class="form-group col-sm-6">
			   <label class="control-label bold"><?php echo $language[$_SESSION['language']]['last_name']; ?> </label>
               <div class="control-label">
              <?php echo $studentData->last_name; ?>
			   </div>
			</div>
			<div class="clear"></div>
		   <div class="form-group col-sm-6 <?php echo $emailHide; ?>">
			  <label class="control-label bold"><?php echo $language[$_SESSION['language']]['login_id']; ?> </label>
			 <div class="control-label">
              <?php echo $studentData->email_id; ?>
			   </div>
			  
			</div>
			<div class="form-group col-sm-6">
			  <label class="control-label bold"><?php echo $language[$_SESSION['language']]['mobile']; ?></label>
             <div class="control-label">
              <?php echo $studentData->phone==""?'NA':$studentData->phone; ?>
			   </div>
			</div>
			<div class="clear"></div>
			 <?php if(isset($_GET['uid'])){
				 if($studentData->userType!='b2b'){?>
			<div class="form-group col-sm-6">
			   <label class="control-label bold"><?php echo $language[$_SESSION['language']]['expiry_date']; ?> </label>
			   <div id="expiryDate" class="input-append date form-control input-lg">
				<div class="control-label">
                <?php echo $expiryDate; ?>
                	
					 </div>
				</div>
				 <?php } }?>
				
			<div class="form-group col-sm-6"> 
			 <label class="control-label bold"><?php echo $language[$_SESSION['language']]['status']; ?> </label>
			<div class="control-label">
				 <?php echo $selectedStatus =($status=='1')? $language[$_SESSION['language']]['active'] : $language[$_SESSION['language']]['inactive'];?>

			</div>
			</div>
			<div class="clear"></div>

				
			
				
				 <div class="form-group col-sm-6">
					 <label class="control-label bold"> <?php echo $language[$_SESSION['language']]['tehsil_name']; ?> </label>
					<div class="control-label">
					  <?php

					  
					  	$sql = "SELECT tt.* FROM tblx_tehsil tt where tt.district_id=:district_id AND tt.tehsil_id=:tehsil_id ";
						  $stmt = $adminObj->dbConn->prepare($sql);
						  $stmt->bindValue(':district_id', $studentData->district_id, PDO::PARAM_INT);
						  $stmt->bindValue(':tehsil_id', $studentData->tehsil_id, PDO::PARAM_INT);
						  $stmt->execute();
						  $res = $stmt->fetch(PDO::FETCH_OBJ);
						  $stmt->closeCursor();
			
						  echo $res->tehsil_name;
					  ?>
					</div>
				 </div>
				 
			<div  class="form-group col-sm-6"  >
				
				<label class="control-label bold">
				<?php echo $language[$_SESSION['language']]['designation']; ?>
				</label>
				<div class="control-label">
				<?php


				 foreach($batchInfo as $key => $bValue){
					 $selectedBtch = '';
					 $batchList =$studentData->batch_id;
				
					$batch_name = explode('-',$bValue['batch_name']);
					if(isset($batch_name[1]) && $batch_name[1]!="")
					{
						 $batch_name = $batch_name[1];
					}else{
						$batch_name = $bValue['batch_name']; 
					}

					if($batchList[0]['batch_id'] == $bValue['batch_id'])
					{	echo $batch_name;
						break;
					}

				}

					?>
					
				</div>
				
				</div>
				</div>
			  </div>
			</fieldset>
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
					if($studentData->gender==$genderValue['name']){$selectedGender="selected";};
						?>
				  <option value="<?php echo $genderValue['name']; ?>"  <?php echo $selectedGender; ?> ><?php echo $genderValue['description']; ?></option>
				  <?php }  ?>						
				</select>				
				</select>
			  </div>
			  
			  <div class="form-group col-sm-6">
			  <!--<label class="control-label">Date of Birth  <span class="required">*</span></label>
			   <div id="divBirthDate" class="input-append date form-control input-lg">
									  <input  data-date-format="DD-MM-YYYY" readonly="true"  name="age" id="dob" placeholder="DD-MM-YYYY" class=" width100per bdrNone" autocomplete="nope" tabindex="4" value='<?php //echo $studentData->dob; ?>'/>
										<span class="calendarBg add-on top30">
									   <i class="fa fa-calendar"></i>
									  </span></div>  -->
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
			  <!--<div class="clear"></div>
			<div class="form-group col-sm-6" style="diplsy:none">
			  <label class="control-label">Marital Status  <span class="required">*</span></label>
			 <select class="form-control input-lg parsley-validated fld_class" name="maritalStatus" id="maritalStatus" >
			  <option value="" >Select Marital Status</option>
				  <?php //foreach($maritalStatus as $key => $maritalStatusValue){
					 //$selectedMaritalStatus = '';
						 //$selectedMaritalStatus =  ($studentData->marital_status == $maritalStatusValue['id'] ) ?  'selected ="selected"' : '';
					  
					?>
				  <option value="<?php //echo $maritalStatusValue['id']; ?>"  <?php //echo $selectedMaritalStatus; ?> ><?php //echo $maritalStatusValue['name']; ?></option>
				  <?php //}  ?>					
				</select>
			</div>-->
			  <div class="form-group col-sm-6">
			  <label class="control-label">Country </label>
			 <select class="form-control input-lg parsley-validated fld_class" name="country" id="country" >
			   <option value="" >Select</option>
			  <?php foreach($resCountry as $key => $countryValue){
					 
						 $selectedCountryStatus =  ($studentData->country == $countryValue['country_name'] ) ?  'selected ="selected"' : '';
					  
					?>
				  <option value="<?php echo $countryValue['country_name']; ?>"  <?php echo $selectedCountryStatus; ?> ><?php echo $countryValue['country_name']; ?></option>
				  <?php }  ?>
			  
				
				</select>
			</div>
			 <!-- <div class="clear"></div>
			   <div class="form-group col-sm-6" style="display:none">
			  <label class="control-label">State  <span class="required">*</span></label>
			 <select id="state_dropdown" name = "state_dropdown" onclick="selectState(this.options[this.selectedIndex].value)" onChange="selectState(this.options[this.selectedIndex].value)" class="form-control input-lg"  style="padding-right:0px;text-transform: capitalize;">
                    <option value="">Select state</option>
                  </select>
                  <span id="state_loader"></span>
			</div>
			 <div class="form-group col-sm-6" style="display:none">
			  <label class="control-label">City  <span class="required">*</span></label>
			  <select id="city_dropdown" name = "city_dropdown" class="form-control input-lg"  style="padding-right:0px;text-transform: capitalize;">
                    <option value="">Select city</option>
                  </select>
                  <span id="city_loader"></span> 
			</div>
			<div class="clear"></div>
			  <div class="form-group col-sm-6"  style="display:none">
			  <label class="control-label">Pin Code <span class="required">*</span></label>
			
			<input type = "text" name="pincode" id="pincode" placeholder="Pincode" class= "form-control input-lg "  value="<?php //echo $studentData->postal_code;?>" data-minlength="[6]"  maxlength="6" data-regexp="^[1-9]\d*$" data-regexp-message="Pin code should be a valid pincode." data-minlength-message="This value is too short. It should have 6 characters." autocomplete="nope"/>					
				
			</div>
			 <div class="form-group col-sm-6"  style="display:none">
			  <label class="control-label">Nationality  <span class="required">*</span></label>
			  <input type = "text" name="nationality" id="nationality" placeholder="Nationality" class= "form-control input-lg "  value="<?php //echo $studentData->nationality;?>"  autocomplete="nope" />
			</div>-->
			
			  <div class="form-group col-sm-6">
				<label class="control-label">Native Language </label>
				<select class="form-control input-lg parsley-validated fld_class" name="motherTongue" id="motherTongue">
				 <option value="" >Select</option>
				  <?php foreach($motherTongue as $key => $motherTongueValue){
					 
						 $selectedMotherTongue =  (  $studentData->mother_tongue == $motherTongueValue['id'] ) ?  'selected ="selected"' : '';
					
					?>
				  <option value="<?php echo $motherTongueValue['id']; ?>"  <?php echo $selectedMotherTongue; ?> ><?php echo $motherTongueValue['name']; ?></option>
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
				
			</div><div class="clear"></div>
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
			 <!--<div class="clear"></div>
			 <div class="form-group col-sm-6">
			  <label class="control-label">How do users dicover <span class="required">*</span></label>
			<select class="form-control input-lg parsley-validated fld_class" name="usersDicover" id="usersDicover" data-required="true">
				  <option value="" >Select users dicover</option>
				  <?php/*  foreach($usersDicover as $key => $usersDicoverValue){

						 $selectedUsersDicover =  (  $studentData->app_discovered == $usersDicoverValue['id'] ) ?  'selected ="selected"' : ''; */
					 
					?>
				  <option value="<?php //echo $usersDicoverValue['id']; ?>"  <?php //echo $selectedUsersDicover; ?> ><?php //echo $usersDicoverValue['name']; ?></option>
				  <?php //}  ?>				
				</select>	
			</div>
			-->
			  
			 </div>
		    </div>
	      </div>
		  <div class="clear"></div>
		
		 </div> 

   </section> 
  </div>
 </div>
</section>


<?php include_once('../footer/trainerFooter.php');?>
<script>

</script>
