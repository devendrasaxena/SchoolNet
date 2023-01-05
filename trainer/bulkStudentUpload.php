<?php
include_once('../header/trainerHeader.php');
/* error_reporting(E_ALL);
ini_set('display_errors',1); */

$centerDetail=$adminObj->getCenterDetails();
//echo "<pre>";print_r($centerDetail);
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];
if(isset($_GET['uid'])){
  $uId = trim( base64_decode($_GET['uid']) ); 
  $studentData = $adminObj->getUserDataByID($uId, 2); // student role 2
   //echo "<pre>";print_r($studentData->password);exit;
   //echo "<pre>";print_r($studentData);exit;
} 
 $getSignedUpUser = $adminObj->getSignedUpUserCountByCenter($center_id);
  //echo "<pre>";print_r($getSignedUpUser->student);exit;

$student_limit=$centerDetails[0]['student_limit'];
//total user limit
if($student_limit != 0){	
	$remaining = $student_limit - $getSignedUpUser->student;
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

//print_r($_SESSION);
if ($region_id==5){
	$upload_file_path = 'excel/Bulk_Student_Upload_DSEU.xls';
}else{
	$upload_file_path = 'excel/Bulk_Student_Upload.xls';
}

//echo "=============".$remaining;exit;
if(isset($_POST['batchReportButton']) && $_FILES['file']['name'] != ""){
	$uploads_dir = 'uploads';
	$tmp_name = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];
	//print_r($_FILES['file']['name']);//exit;
	
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
					//_dd(array ('ab', $grid));
					$itemNo = $grid[0];
					$fname = $grid[1];
					$lname = $grid[2];
					$email = $grid[3];
					$is_email_verified =0;
					$password = $grid[4];
					$mobile = $grid[5];
					$is_phone_verified = 0;
					
					if($region_id==5){
					$roll_no = $grid[6];
					$fathers_name = $grid[7];
					$mothers_name = $grid[8];
					$section = $grid[13];
					$slot = $grid[12];
					}
					
					
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
					if($region_id==5){
					$obj['roll_no'] = $roll_no;
					$obj['fathers_name'] = trim(addslashes(filter_string($fathers_name)));
					$obj['mothers_name'] = trim(addslashes(filter_string($mothers_name)));
					$obj['section'] = $section;
					$obj['slot'] = $slot;
					}
					
					if($itemNo != "" && $password != "" &&  $fname != "" &&  $lname != "" &&  $email!= ""){
						if($email!=""){
								if(filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email)) {
                                     /*  $resEmail = $adminObj->checkEmailExits($email);
									if(isset($resEmail['user_id']) && $resEmail['user_id']!='' && $resEmail['loginid']!=''){
									
									 $_SESSION['msg']='Login id already exist.';
									  $cnt++;
										
									}else{
											
										}  */
								}else{
									$_SESSION['msg']='Login id should be a valid.';
									$cnt++;
								}
							}
						if($mobile!=""){
								if(preg_match('/^[0-9]{10}+$/', $mobile)) {

								}else{
									$_SESSION['msg']='Phone number should be a valid phone.';
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
							$_SESSION['msg']=$passValidMsg;
							$cnt++;
						} 	
							
							
						$tempArr[] = $obj;

						}else{
							
								if($fname == ""){
									$_SESSION['msg']='First name rquired.';
								}
								elseif($lname == ""){
									$_SESSION['msg']='Last name required.';
								}
								elseif($email == ""){
									$_SESSION['msg']='Login id  required.';
								}
								elseif($password == ""){
									$_SESSION['msg']='Password required.';
								}

							$cnt++;
						}

					$k = 0;
				}
				
				
				
				//echo "<pre>"; print_r($tempArr); die;
				if(count($tempArr) > $remaining  && $student_limit != 0){
					@unlink ("$uploads_dir/$name");
					header('location:bulkStudentUploadNew.php?err=3');
					exit;
				}
				if($cnt){
					header('location:bulkStudentUploadNew.php?err=4&num='.$cnt);
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
						$obj1['center_id'] = $value['center_id'];	
						$obj1['password'] = $value['password'];	
						
						if($region_id==5){
						$obj1['roll_no'] = $value['roll_no'];
						$obj1['fathers_name'] = $value['fathers_name'];
						$obj1['mothers_name'] = $value['mothers_name'];
						$obj1['section'] = $value['section'];
						$obj1['slot'] = $value['slot'];
						}
						
						$res = $adminObj->bulkDataInsert($obj1);
						
						$centerAdminName=$value['first_name'].' '.$value['last_name'];
						$email_id=$value['email_id'];
						$password=$value['password'];	
						require_once('../mail_send_template.php');
							
						
						//studentBulkSignUp($obj1);
						}
					}
				
					@unlink ("$uploads_dir/$name");
					header('location:bulkStudentUploadNew.php?err=2');
					exit;
				}
				
				
				
				
					
			}else{
				@unlink ("$uploads_dir/$name");
				header('location:bulkStudentUploadNew.php?err=0');
				exit;
			}
				
	}else{
		header('location:bulkStudentUploadNew.php?err=1');
		exit;
	}
}
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="studentList.php" title="<?php echo $language[$_SESSION['language']]['learners']; ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['learners']; ?> </a></li>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
      <section class="panel panel-default  marginBottom5">
            <div class="row m-l-none m-r-none bg-light lter" >
                  <div class="col-sm-6 col-md-4 padder-v b-light"  title="<?php echo $language[$_SESSION['language']]['maximum_learner_limit']; ?>">
                    <div class="col-sm-4 padd0 text-right">
					<span class="fa-stack fa-2x m-r-sm  iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-info"></i>
                      <i class="fa fa-users fa-stack-1x text-white"></i>
					  </span>
                    </div>
					<div class="col-sm-8 padd0">
                    <a class="">
                      <div class="h3  m-t-xs"><strong><?php echo $student_limit; ?></strong></div>
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
                      <div class="h3  m-t-xs"><strong><?php echo $remaining; ?></strong></div>
                      <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['remaining_learner_limit']; ?></small></div>
                    </a>
                  </div>
                 	</div>

					 <div class="col-sm-6 col-md-4 padder-v b-l b-light ">                 
                   <div class="col-sm-4  padd0 text-right">
				      <a href="<?php echo $upload_file_path ?>" download title="<?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?>"> <span class="fa-stack fa-2x  m-r-sm iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-danger"></i>
                      <i class="fa fa-download fa-stack-1x text-white"></i>
                       </span>  </a>
					</div>
					
					
					<div class="col-sm-8 padd0">
					
                    <a class="clear" title="<?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?>" href="<?php echo $upload_file_path ?>" download >
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
			}else if($_GET["err"] == 4){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> ".$_GET['num']." records are not uploaded. ".$_SESSION['msg']."</div>";
			unset($_SESSION['msg']);
			}
			echo $errDiv;
		}
   ?>
	<form action="" id="batchReport" class="<?php echo $regClass;?>" method="post"  data-validate="parsley" enctype="multipart/form-data" onsubmit="return fileValidation();" >
	 <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		 	<h3 class="panel-header"><?php echo $language[$_SESSION['language']]['bulk_upload_learners']; ?></h3>
		   
           <div class="form-group pull-in clearfix" id="classSectionDtl1">
		    <div class="col-sm-6 hide">
		  <label class="control-label"><?php echo $language[$_SESSION['language']]['state_name']; ?><span class="required"></span></label>
		<input type = "text" name="center" id="center" placeholder="" class= "form-control input-lg disabledInput"  value="<?php echo $centerName; ?>"  autocomplete="nope" /></div>
		
       <div class="col-sm-4">
	   <label class="control-label"><?php echo $language[$_SESSION['language']]['designation']; ?> <span class="required"> *</span></label>
                    <select class="form-control " name="batch" id="batch" data-required="true" onchange="return  checkUploadFile(this.id);">
                      <option value="" ><?php echo $language[$_SESSION['language']]['select_class']; ?> </option>
				  <?php  
				  
				     foreach($batchData as $key => $bValue){
						 $selectedBtch = '';
						 $batchList =$studentData->batch_id;
						 $batch_name_arr =$adminObj->getBatchNameByID($bValue['batch_id']);
						// echo "<pre>";print_r($batch_name_arr);
						 $batch_name=$batch_name_arr[0]['batch_name'];
						for ($i=0; $i < count( $batchList); $i++) { 
							 $selectedBtch .=  (  $batchList[$i]['batch_id'] == $bValue['batch_id'] ) ?  'selected ="selected"' : '';
						  } 

					?>
				  <option value="<?php echo $bValue['batch_id']; ?>"  <?php echo $selectedBtch; ?> ><?php echo $batch_name; ?></option>
				  <?php }  ?>	
                    </select>
                  </div>
		
			<div class="col-sm-2"></div>
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
			  <a href='studentList.php' class="btn btn-primary "  title="<?php echo $language[$_SESSION['language']]['cancel']; ?>"><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			   <input id="profile_id" type="hidden" name="profile_id" value="<?php echo $studentData->profile_id; ?>"/>
			 <input id="center_id" type="hidden" name="center_id" value="<?php echo $center_id; ?>"/>
			 
			   <button type="submit" name="batchReportButton"  class="btn btn-s-md btn-primary  pre-loader"  title="<?php echo $language[$_SESSION['language']]['submit']; ?>"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
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
<?php include_once('../footer/trainerFooter.php');?>
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
	
	
	});
}

function checkUploadFile(cId){
	var cValue = $("#"+cId).val();
	if(cValue!=''){
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

 $(document).ready(function () {

});
</script>
