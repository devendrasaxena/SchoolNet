<?php
include_once('../header/adminHeader.php');

/* error_reporting(E_ALL);
ini_set('display_errors',1); */
if(isset($_GET['uid'])){ 
  $uId = trim( base64_decode($_GET['uid']) ); 
   $studentData = $centerObj->getUserDataByID($uId, 2); // student role 2
  $batchId=$studentData->batch_id[0]['batch_id'];
  $userStatus= $centerObj->getUserDetailsById($uId,2);
  $status=$userStatus[0]['is_active'];
}
 $getSignedUpUser =  $centerObj->getSignedUpUserCountByCenter($client_id,$studentData->center_id);
 // echo "<pre>";print_r($getSignedUpUser);exit;
$adminObj = new centerAdminController();
$batchInfo = $adminObj->getBatchDeatils($studentData->center_id);
$centerDetails=$clientObj->getCenterDetailsById($studentData->center_id);	

$student_limit=(!empty($centerDetails[0]['student_limit'])?$centerDetails[0]['student_limit']:0);
//total user limit
if($student_limit != 0){	
	$remaining = $student_limit - $getSignedUpUser->totalCenterStudent;
	if($remaining > 0){
		$uploadMsg = "Upload was not successful. You can upload only $remaining more $students. Please make corrections in the Excel file and upload it again.";
		$regClass ="";
		$customerrDiv ="";
	}else{

			$uploadMsg = "You have reached maximum limit of $students allowed. You can not upload more $students. Please contact administrator.";
			
				if($_GET["err"] == '' && $remaining == 0){ 
				 $regClass ="cursorDefault";
				 $customerrDiv = "<div class='col-sm-12  marginTop20  marginBottom40 paddLeft40  paddRight40 '> <div class='alert alert-danger height60 fontSize16 paddTop20'>
								
								<i class='fa fa-ok-sign'></i> You have reached maximum limit of $students allowed. You can not upload more $students. Please contact administrator.
								</div> </div>";
				}				
		
		
	}
}else{
	$remaining = 0;

}

//echo "=============".$remaining;exit;
if(isset($_POST['batchReportButton']) && $_FILES['file']['name'] != ""){
	$uploads_dir = 'uploads';
	 $tmp_name = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];
	//print_r($_FILES['file']['name']);exit;
	if (file_exists("$uploads_dir/$name")) {
		@unlink ("$uploads_dir/$name");
	} 
	$pathtomove=$uploads_dir.'/'.$name;
	//print_r($pathtomove);
	if(move_uploaded_file($tmp_name,$pathtomove)){
		 $userfile_extn = substr($name, strrpos($name, '.')+1);
			if($userfile_extn=="xls"){
				require_once ("excel/excel_reader2.php");
				$data = new Spreadsheet_Excel_Reader();

				//echo "<pre>"; print_r($data); die;
				$data->setOutputEncoding('utf8');
				$data->read('uploads/'.$name);
				$k=0;
				$num=0;
				$grid=array();
				
				$cnt = 0;
				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

					for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
						echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
						$grid[$k] = $data->sheets[0]['cells'][$i][$j];
						$k++;
					}
					
					//print_r($grid);exit;
					$itemNo = $grid[0];
					$fname = $grid[1];
					$lname = $grid[2];
					$email = $grid[3];
					$is_email_verified =0;
					$password = $grid[4];
					$mobile = $grid[5];
					$is_phone_verified = 0;
					
					
					
					
					/*
					$gender = $grid[6];
					$age = $grid[7];
					$age=explode('-',$age);
					$age=array_map("trim",$age);
					$age=implode('-',$age);
					$marital_status = $grid[8];
					$country = $grid[9];
					$state = $grid[10];
					$city = $grid[11];
					$pincode = $grid[12];
					$nationality = $grid[13];
					$mother_tongue = $grid[14];
					$education = $grid[15];
					$emp_status = $grid[16];
					$perpose_join = $grid[17];
					$discover_app = $grid[18];
					
					//Get age range  id
					$age = trim($age);
					$res_age=$commonObj->getDatabyId('tblx_age_range','age_range',$age);
					if($res_age){
						$age_id=$res_age['id'];
					}
					else{$age_id='';}
					
					
					
					//Get marital status id
					$marital_status = trim($marital_status);
					$res_marital_status=$commonObj->getDatabyId('tblx_marital_status','name',$marital_status);
					if($res_marital_status){
						$marital_id=$res_marital_status['id'];
					}
					else{$marital_id='';}
					
					//Get country id
					$country = trim($country);
					$res_country=$commonObj->getDatabyId('country','country_name',$country);
					if($res_country){
						$country_id=$res_country['id'];
					}
					else{$country_id='';}
					
					
					//Get state id
					$state = trim($state);
					$res_state=$commonObj->getDatabyId('state','state_name',$state);
					if($res_state){
						$state_id=$res_state['id'];
					}
					else{$state_id='';}
					
					
					//Get city id
					$city = trim($city);
					$res_city=$commonObj->getDatabyId('city','city_name',$city);
					if($res_city){
						$city_id=$res_city['id'];
					}
					else{$city_id='';}
					
					
					//Get mother tongue id
					$mother_tongue = trim($mother_tongue);
					$res_mother_tongue=$commonObj->getDatabyId('tblx_mother_tongue','name',$mother_tongue);
					if($res_mother_tongue){
						$mother_tongue_id=$res_mother_tongue['id'];
					}
					else{$mother_tongue_id='';}
					
					//Get education id
					$education = trim($education);
					$res_education=$commonObj->getDatabyId('tblx_education','name',$education);
					if($res_education){
						$education_id=$res_education['id'];
					}
					else{$education_id='';}
					
					//Get emp status id
					$emp_status = trim($emp_status);
					$res_emp_status=$commonObj->getDatabyId('tblx_employment_status','name',$emp_status);
					if($res_emp_status){
						$emp_status_id=$res_emp_status['id'];
					}
					else{$emp_status_id='';}
					
					
					//Get join purpose id
					$perpose_join = trim($perpose_join);
					$res_perpose_join=$commonObj->getDatabyId('tblx_joining_purpose','name',$perpose_join);
					if($res_perpose_join){
						$perpose_join_id=$res_perpose_join['id'];
					}
					else{$perpose_join_id='';}
					
					//Get discover app id
					$discover_app = trim($discover_app);
					$res_discover_app=$commonObj->getDatabyId('tblx_app_discovered','name',$discover_app);
					if($res_discover_app){
						$discover_app_id=$res_discover_app['id'];
					}
					else{$discover_app_id='';}*/
					
					
					$obj = array();
					//$obj = new stdClass();
					$obj['first_name'] = trim(addslashes(filter_string($fname)));
					$obj['last_name'] = trim(addslashes(filter_string($lname)));
					$obj['email_id'] = trim(addslashes(filter_string($email)));
					$obj['is_email_verified'] = trim($is_email_verified);
					$obj['batch'] = $_POST['batch'];
					$obj['center_id'] = $_POST['center_id'];
					$obj['password'] = trim(addslashes(filter_string($password)));
					$obj['mobile'] = trim(filter_string($mobile));
					$obj['is_phone_verified'] = $is_phone_verified;
				    $obj['country_id'] = $_POST['country'];
					$obj['mother_tongue_id'] = $_POST['motherTongue'];
					
				/* 	$obj['gender'] = trim($gender);
					$obj['age_id'] = $age_id;
					$obj['marital_id'] =$marital_id;
					$obj['country_id'] = $country_id;
					$obj['state_id'] = $state_id;
					$obj['city_id'] = $city_id;
					$obj['pincode'] = $pincode;
					$obj['nationality'] = trim($nationality);
					$obj['mother_tongue_id'] = $mother_tongue_id;
					$obj['education_id'] = $education_id;
					$obj['emp_status_id'] = $emp_status_id;
					$obj['perpose_join_id'] = $perpose_join_id;
					$obj['discover_app_id'] = $discover_app_id; */
					$msg = "";
					if($itemNo != "" && $password != "" &&  $fname != "" &&  $lname != "" &&   $email!= ""){
						
						if($email!=""){
								if(filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email)) {
                                    $resEmail = $adminObj->checkEmailExits($email);
									if(isset($resEmail['user_id']) && $resEmail['user_id']!='' && $resEmail['loginid']!=''){
									
									   $_SESSION['msg']='Email id already exist.';
									   $_SESSION['duplicate_email']=$email;
									   $status=1;
									  //$cnt++;
										
									}else{
										if(in_array($email,$email_arr)){
												
												$_SESSION['msg']='Duplicate Email id in XLS.';
												
												$_SESSION['duplicate_email']=$email;
												$status=1;
												
										}else{
											
											$email_arr[]=$email;
										}
										
									}   
								}else{
									$_SESSION['msg']='Login id should be a valid.';
									$cnt++;
								}
							}
						if($mobile!=""){
								if(preg_match('/^[0-9]{10}+$/', $mobile)) {

								}else{
									$msg.='Phone number should be a valid phone.<br>';
									$cnt++;
								}
							}
				
						$containsLetter  = preg_match('/[a-zA-Z]/', $password);
						$containsUpper  = preg_match('/[A-Z]/', $password);
						$containsLower  = preg_match('/[a-z]/', $password);
						$containsDigit   = preg_match('/\d/',$password);
						//$containsSpecial = preg_match('/[\_\@\.\-]/',$password);
						$containsSpecial = preg_match('/[\_\@\.\-]/',$password);
						if(!$containsLetter || !$containsUpper || !$containsLower|| !$containsDigit || !$containsSpecial || strlen($password) < 8 || strlen($password) > 15) {
							$msg.=$passValidMsg;
							$cnt++;
						} 	
							
							
						$tempArr[] = $obj;
						if($msg!=""){
							$_SESSION['msg'][] = array('item_no'=>$itemNo,'msg'=>$msg);
						
						}
						}else{
							
								if($fname == ""){
									$msg.='First name rquired.<br>';
								}
								elseif($lname == ""){
									$msg.='Last name required.<br>';
								}
								elseif($email == ""){
									$msg.='Login id  required.<br>';
								}
								elseif($password == ""){
									$msg.='Password required.<br>';
								}
								/* elseif($mobile == ""){
									$_SESSION['msg']='Phone required.';
								} */
							
							$_SESSION['msg'][] = array('item_no'=>$itemNo,'msg'=>$msg); 
							
							$cnt++;
						
						}

				
					$k = 0;
				}
				
				
				
				//echo "<pre>"; print_r($tempArr); die;
				if(count($tempArr) > $remaining  && $student_limit != 0){
					@unlink ("$uploads_dir/$name");
					header('location:bulkStudentUpload.php?err=3');
					exit;
				}
				if($cntExit){
					header('location:bulkStudentUpload.php?err=5&num='.$cntExit);
					exit;
				}
				if($cnt){
					header('location:bulkStudentUpload.php?err=4&num='.$cnt);
					exit;
				}else{
					
					if(count($tempArr) > 0){
						foreach($tempArr as $key => $value){
						$obj1 = array();
						$obj1['first_name'] = $value['first_name'];
						$obj1['last_name'] = $value['last_name'];
						$obj1['email_id'] = $value['email_id'];
						$obj1['is_email_verified'] = $value['is_email_verified'];
						$obj1['mobile'] = $value['mobile'];
						$obj1['is_phone_verified'] = $value['is_phone_verified'];
						$obj1['batch'] = $value['batch'];
						$obj1['password'] = $value['password'];	
						$obj1['center_id'] = $value['center_id'];
						$obj1['country_id'] =$value['country_id'];
						$obj1['mother_tongue_id'] =$value['mother_tongue_id'];		
						/* $obj1['gender'] = $value['gender'];	
						$obj1['age_id'] = $value['age_id'];	
						
						$obj1['marital_id'] =$value['marital_id'];
						$obj1['country_id'] =$value['country_id'];
						$obj1['state_id'] =$value['state_id'];
						$obj1['city_id'] =$value['city_id'];
						$obj1['pincode'] =$value['pincode'];
						$obj1['nationality'] =$value['nationality'];
						$obj1['mother_tongue_id'] =$value['mother_tongue_id'];
						$obj1['education_id'] =$value['education_id'];
						$obj1['emp_status_id'] =$value['emp_status_id'];
						$obj1['perpose_join_id'] =$value['perpose_join_id'];
						$obj1['discover_app_id'] =$value['discover_app_id']; */
						//echo "<pre>";print_r($obj1);exit;
						$res = $adminObj->bulkDataInsert($obj1);
						
						$centerAdminName=$value['first_name'].' '.$value['last_name'];
						$email_id=$value['email_id'];
						$password=$value['password'];	
						require_once('../mail_send_template.php');
							
						
						//studentBulkSignUp($obj1);
						}
					}
				
					@unlink ("$uploads_dir/$name");
					header('location:bulkStudentUpload.php?err=2');
					exit;
				}
				
				
				
				
					
			}else{
				@unlink ("$uploads_dir/$name");
				header('location:bulkStudentUpload.php?err=0');
				exit;
			}
				
	}else{
		header('location:bulkStudentUpload.php?err=1');
		exit;
	}
}
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="studentList.php"   title="<?php echo $language[$_SESSION['language']]['learners']; ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['learners']; ?> </a></li>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
      <section class="panel panel-default  marginBottom5">
            <div class="row m-l-none m-r-none bg-light lter">
                  <div class="col-sm-6 col-md-4 padder-v b-light"  title="<?php echo $language[$_SESSION['language']]['maximum_learner_limit']; ?>">
                    <div class="col-sm-4 padd0 text-right">
					<span class="fa-stack fa-2x m-r-sm  iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-info"></i>
                      <i class="fa fa-users fa-stack-1x text-white"></i>
					  </span>
                    </div>
					<div class="col-sm-8 padd0">
                    <a class="">
                      <div class="h3  m-t-xs"><strong id="totalLimit"><?php echo $student_limit; ?></strong></div>
                      <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['maximum_learner_limit']; ?></small></div>
                    </a>
					</div>
                  </div>
                 
                <div class="col-sm-6 col-md-4 padder-v b-l b-light lt"  title="<?php echo $language[$_SESSION['language']]['remaining_learner_limit']; ?>">                     
                   <div class="col-sm-4 padd0 text-right">
				    	<span class="fa-stack fa-2x m-r-sm  iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-success"></i>
                      <i class="fa fa-user fa-stack-1x text-white"></i>
                       </span> 
					</div>
					<div class="col-sm-8 padd0">
					
                    <a class="clear">
                      <div class="h3  m-t-xs"><strong id="remainLimit"><?php echo $remaining; ?></strong></div>
                      <div><small class="text-muted text-uc"> <?php echo $language[$_SESSION['language']]['remaining_learner_limit']; ?></small></div>
                    </a>
                  </div>
                 	</div>
					 <div class="col-sm-6 col-md-4 padder-v b-l b-light "  title="<?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?>">                 
                   <div class="col-sm-4  padd0 text-right">
				      <a href="excel/Bulk_Student_Upload.xls" download> <span class="fa-stack fa-2x  m-r-sm iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-danger"></i>
                      <i class="fa fa-download fa-stack-1x text-white"></i>
                       </span>  </a>
					</div>
					<div class="col-sm-8 padd0">
					
                    <a class="clear" href="excel/Bulk_Student_Upload.xls" download>
                      <div class="m-t-xs"> <?php echo $language[$_SESSION['language']]['download']; ?></div>
                      <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?></small></div>
                    </a>
                  </div>
                 	</div>
					</div>
          </section>
     <?php echo $customerrDiv;
		if(isset($_GET["err"])){
			if($_GET["err"] == 0){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Please check uploaded file.
				</div>";
			}else if($_GET["err"] == 2){
				$errDiv = " <div class='alert alert-success'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Uploaded Successfully please go to  $students list Report to view registered $students.
				</div>";
			}else if($_GET["err"] == 3){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> $uploadMsg.
				</div>";
			}else if($_GET["err"] == 1){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> There is something wrong.
				</div>";
			}	else if(($_GET["err"] == 4) && isset($_SESSION['msg']) && isset($_SESSION['duplicate_email'])){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Duplicate email address found: ".$_SESSION['duplicate_email']."<br>Please remove all duplicate email addresses before uploading again.</div>";
			unset($_SESSION['msg']);
			unset($_SESSION['duplicate_email']);
			
			}else if(($_GET["err"] == 4) && isset($_SESSION['msg'])){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> ".$_GET['num']." records are not uploaded. ".$_SESSION['msg']."</div>";
				unset($_SESSION['msg']);
			}else if(($_GET["err"] == 5) && isset($_SESSION['msg']) && isset($_SESSION['invalid_state'])){
				
				
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Center not exist : ".$_SESSION['invalid_state']."<br>Please correct state name before uploading again.</div>";
			unset($_SESSION['msg']);
			unset($_SESSION['invalid_state']);
			
			}else if(($_GET["err"] == 6) && isset($_SESSION['msg']) && isset($_SESSION['invalid_designation'])){
				
				
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Batch not exist : ".$_SESSION['invalid_designation']."<br>Please correct designation name before uploading again.</div>";
			unset($_SESSION['msg']);
			unset($_SESSION['invalid_designation']);
			
			}
			echo $errDiv;
		}
   ?>
	<form action="" id="bulkUploadForm" name="bulkUploadForm" class="<?php echo $regClass;?>" method="post"  data-validate="parsley" enctype="multipart/form-data" onsubmit="return fileValidation();" >
	 <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		 	<h3 class="panel-header"><?php echo $language[$_SESSION['language']]['bulk_upload_learners']; ?></h3>
		   
           <div class="form-group pull-in clearfix" id="classSectionDtl1">
         <div class="col-sm-6">
		  <label class="control-label"><?php echo $language[$_SESSION['language']]['state_name']; ?> <span class="required">*</span></label>
		 <select class="form-control input-lg parsley-validated fld_class " name="center_id" id="center_id" data-required="true" onchange="selectCenter(this);">
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
		</select></div>
				
				
       <div class="col-sm-4">
	  <label class="control-label"><?php echo $language[$_SESSION['language']]['class']; ?> <span class="required">*</span></label>
                    <select class="form-control " name="batch" id="batch" data-required="true" onchange="return  checkUploadFile(this.id,'file','fileName');">
                      <option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
                     <?php foreach($batchInfo as $key => $value){ 
						$sel = ($batchID == $value['batch_id']) ? "selected" : ""; 
						?>
					  <option value="<?php echo $value['batch_id']; ?>" <?php echo $sel; ?> ><?php echo $value['batch_name']; ?></option>
					  <?php } ?>
                    </select>
                  </div>
			 <div class="col-sm-2">
			 <label class="control-label"><?php echo $language[$_SESSION['language']]['file']; ?> <span class="required">*</span></label>
			 <!-- <div class="uploadClass" onclick="document.getElementById('file').click();" id="uploadDiv"><i class="fa fa-upload"></i> -->
			  <div class="uploadClass disabled" onclick="uploadFile(this.id,'file','fileName');" style="cursor:pointer" id="uploadDiv"><i class="fa fa-upload"></i> <?php echo $language[$_SESSION['language']]['upload']; ?>
			   <input type="file" class="fileHidden" name="file" id="file" style="width: 100px;
    height: 35px;"><br />
			  </div> <div id="fileName" style="color:#111;clear:both;"></div>
			  <label class="required" id="fileError"></label>
			  </div>
			  
		    </div>
		   </div>
		</div>
		  <div class="clear"></div>
		   <div class="text-right"> 
			  <a href='studentList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			   <input id="profile_id" type="hidden" name="profile_id" value="<?php echo $studentData->profile_id; ?>"/>
			  <input id="client_id" type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
			    <input type = "hidden" name="country" id="country" value="" />
			  <input type = "hidden" name="motherTongue" id="motherTongue" value="38" />
			 
			   
			   <button type="submit" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" name="batchReportButton"  class="btn btn-s-md btn-primary  pre-loader"  onclick="showLoaderOrNot('bulkUploadForm');" ondblclick="showLoaderOrNot('bulkUploadForm');"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
	      </div>
		 </div> 
     </form>
   </section> 
  </div>
 </div>
</section>
<style>

.fileHidden{font-size: 0px;
position: absolute;
top: 0px;
left: 0px;
opacity: 0;
cursor: pointer;}
.uploadClass {
    width: 100px;
    height: 35px;
    position: relative;
  
    left: 12px;
    background: rgb(1, 137, 197) none repeat scroll 0% 0%;
    color: rgb(255, 255, 255);
    text-align: center;
    padding: 8px;border-radius:5px;cursor:pointer}
	</style>
<?php include_once('../footer/adminFooter.php');?>
<script>
/* $("#uploadDiv").on('change', function(){
	
	var filevalue = $("#file").val();
	$("#fileName").text(filevalue);
	
}); */


function uploadFile(id,inputId,fileName){

	 // $("#loaderDiv").show();
	$("#"+inputId).on('change', function(){
	  var filevalue = this.files[0].name;
	   // var filevalue =  $("#"+inputId).val();
	    $("#"+fileName).text(filevalue);
		if( $("#"+fileName).text()!=''){
			 $("#fileError").text("");
	       }
	
		 // alert(this)
	 /*  var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileName = this.files[0].name;
		 //alert(fileName);
		var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
		 if ($.inArray(fileNameExt, validExtensions) == -1) {
			  this.type = '';
			  this.type = 'file';
			  alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
			 return false;
			 
		 } else{
			var filevalue =  $("#"+inputId).val();
	        $("#"+fileName).text(filevalue);

		 }  */
	});
}

function checkUploadFile(cId, UploadId,uploadName){
	var cValue = $("#"+cId).val();
	var center_id = $('#center_id').val();
	var district_id = $('#district_id').val();
	var tehsil_id = $('#tehsil_id').val();
	if(center_id!='' && district_id!='' &&  tehsil_id!='' && cValue!=''){
		$("#uploadDiv").removeClass('disabled');
	}else{$("#uploadDiv").addClass('disabled');}
	 //$("#"+UploadId).val('');
	  //$("#"+uploadName).text('');
}
function fileValidation(){
	var fileText= $("#fileName").text();
	if(fileText==''){
		 $("#fileError").text("Please upload file")
	}

}

function selectCenter(e){
    $("#batch").html(""); 
	 if(e.value!=''){
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
			   $("#batch").html("<option value=''>Select <?php echo $batch; ?></option>"+batch);
			  
			   hideLoader();
			   
			  }
		  });
		 
   }else{
	 $("#batch").html("<option value=''>Select <?php echo $batch; ?></option>");  
	
   }
}
</script>
