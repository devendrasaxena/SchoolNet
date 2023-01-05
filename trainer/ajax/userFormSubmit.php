<?php 

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
require_once("../../library/phpMailer/mail.php");

$centerObj = new centerController();
$adminObj = new centerAdminController();		
						
// For register 

$userIdVal = filter_query($_POST['userIdVal']);
$roleID = ( $_POST['uSignUp'] == 'studentReg' ) ? 2 : 1;
$centerDetails = $adminObj->getCenterDetails();
$center_id = $centerDetails[0]['center_id'];

 $trainer_limit = $centerDetails[0]['trainer_limit'];
 $student_limit = $centerDetails[0]['student_limit'];
 $chkLimit= $adminObj->getUserLimit($center_id,$roleID);

  if((($roleID==1) && ($trainer_limit>$chkLimit)) || ($roleID==2) && ($student_limit>$chkLimit)){					
	// For register 

	if(empty($userIdVal) && ($_POST['uSignUp'] == 'studentReg') ) {
		
		$res=$adminObj->registerUser($_POST);
		//echo "<pre>";print_r($res);exit;
		  if($res) {
			if(isset($res['user_id']) && $res['user_id']!='' && $res['loginid']!=''){
				// case : user is already registered
				 $_SESSION['error'] = 2;
				 $_SESSION['msg'] ="$student login id is already exist. Please try another.";
				 header("Location: ../createStudent.php");
				 exit;
			 }else{

					$centerAdminName=filter_string($_POST['name']).' '.filter_string($_POST['lastname']);
					$email_id=filter_string($_POST['email']);
					$password=filter_string($_POST['password']);	
					require_once('../../mail_send_template.php');	//Sending crdential in email	
					$_SESSION['succ'] = 1;
					$_SESSION['msg'] ="$student created successfully.";
					header("Location: ../studentList.php"); 
					exit;
				}
				
		
		}else{
			$_SESSION['error'] = 1;
			$_SESSION['msg'] ="$student not saved. Please try again.";
			header("Location: ../createStudent.php");
			exit;
		}
	}

 }else if(empty($userIdVal)){
	
	if($roleID==2){
        $_SESSION['error'] = 3;
		$_SESSION['msg'] ="You have reached maximum limit of $student allowed. You can not register more $student.";
		header("Location: ../createStudent.php");
		exit;
	}
} 
// For Edit /update


if(isset($userIdVal) && $userIdVal != '' && $_POST['uSignUp'] == 'studentReg'){
	
	$userUpdate =$adminObj->updateUser($_POST);	
	
	if($userUpdate){
		 $_SESSION['succ'] = 2;
		 $_SESSION['msg'] ="$student updated successfully.";
		header("Location: ../studentList.php");
		exit;
	}else{
		$_SESSION['error'] = 0;
		$_SESSION['msg'] ="$student not updated. Please try again.";
		header("Location: ../studentList.php");
		exit;
	}
	
}

?>
