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
	
		//$msg = $_SESSION['msg'];
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

	$usersDicover=$commonObj->getUsersDicover();
	  
if(!empty($_GET['uid'])){
	//  $countClass ="displayNone";
	  $regClass ="";
	  $errDiv = "";
	  $pageType ="View ".$student;
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
	  $pageType =$language[$_SESSION['language']]['learners_details'];
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
                <div><label class="control-label" for="logo" title="<?php echo $language[$_SESSION['language']]['photo']; ?>"><?php echo $language[$_SESSION['language']]['photo']; ?>  </label>
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
			  <label class="control-label"><b><?php echo $language[$_SESSION['language']]['first_name']; ?></b> <span class="required"></span></label>
             <br>
              <?php echo $studentData->first_name; ?>
             </div>
			 <div class="form-group col-sm-6">
			  <label class="control-label"><b><?php echo $language[$_SESSION['language']]['last_name']; ?></b> <span class="required">&nbsp;</span></label>
              <br>
              <?php echo $studentData->last_name; ?>
			</div>
			<div class="clear"></div>
		   <div class="form-group col-sm-6 <?php echo $emailHide; ?>">
			  <label class="control-label"><b><?php echo $language[$_SESSION['language']]['email_id']; ?> </b><span class="required"></span></label>
			  <br>
              <?php echo $studentData->email_id; ?>
			  <div class="required error" id="emailErr"></div>
			</div>
			<div class="form-group col-sm-6">
			  <label class="control-label"><b><?php echo $language[$_SESSION['language']]['mobile']; ?></b> <span class="required"></span></label>
              <br>
              <?php echo $studentData->phone==""?'NA':$studentData->phone; ?>
			</div>
			<div class="clear"></div>
			 <?php if(isset($_GET['uid'])){
				 if($studentData->userType!='b2b'){?>
			<div class="form-group col-sm-6 hide">
			  <label class="control-label"><b><?php echo $language[$_SESSION['language']]['expiry_date']; ?>  </b><span class="required"></span></label>
			   <div id="expiryDate" class="input-append date form-control input-lg">
				<br>
                <?php echo $expiryDate; ?>
                	<span class="calendarBg add-on top30"  style="top: 30px;"> <i class="fa fa-calendar"></i>
					</span></div> 
				</div>
				 <?php } }?>
				
			<div class="form-group col-sm-6"> 
			 <label class="control-label"><b><?php echo $language[$_SESSION['language']]['status']; ?></b> <span class="required"></span></label>
			<br>
				 <?php echo $selectedStatus =($status=='1')? $language[$_SESSION['language']]['active'] : $language[$_SESSION['language']]['inactive'];?>

			</div>

				<div class="form-group col-sm-6">
				 <label class="control-label"><b> <?php echo $language[$_SESSION['language']]['state_name']; ?> </b></label>
					 <br>
					 <label class="" name="" id="">
					  <?php
						$dfpdVar='';
					  
					  	$sql = "SELECT tt.name FROM tblx_center as tt where  tt.center_id=:center_id ";
						  $stmt = $adminObj->dbConn->prepare($sql);
						  $stmt->bindValue(':center_id', $studentData->center_id, PDO::PARAM_INT);
						  $stmt->execute();
						  $res = $stmt->fetch(PDO::FETCH_OBJ);
						  $stmt->closeCursor();
						  
						  $dfpdVar=$res->name;
						  echo $res->name;
					  ?>
					</label>
				</div>
				
			
				<div class="form-group col-sm-6">
				<div id="classSectionDtl1" class="clear">
				<label class="control-label">
				<b><?php echo $language[$_SESSION['language']]['classes']; ?></b>
				<br>
				<?php
						 foreach($batchInfo as $key => $bValue){
							 $selectedBtch = '';
							 $batchList =$studentData->batch_id;
						
							$batch_name = $bValue['batch_name']; 
							
							if($batchList[0]['batch_id'] == $bValue['batch_id'])
							{	echo $batch_name;
								break;
							}

						}

					?>
					
				</label>
				
				</div>
			  </div>
			  <?php  if($region_id=='5'){?>
		  <div class="form-group col-sm-6 <?php echo $emailHide; ?>">
			  <label class="control-label"><b><?php echo $language[$_SESSION['language']]['roll_no']; ?> </b><span class="required"></span></label>
			  <br>
              <?php echo $studentData->loginid; ?>
			  <div class="required error" id="emailErr"></div>
			</div>
			  <?php  }?>
			</fieldset>
			</div>
			 </div>
			</div>
		    </div>
			
		
		 </div> 

   </section> 
  </div>
 </div>
</section>

<?php include_once('../footer/adminFooter.php');?>
<script>

</script>
