<?php
//error_reporting(E_ALL);
//ini_set('display_errors','1');
include_once('../../header/lib.php');
$adminObj = new centerAdminController();

$name = addslashes(trim(filter_string($_POST["name"])));
$fld_password = addslashes(trim(filter_string($_POST["fld_password"])));
if(strlen($name)>30 || strlen($fld_password)>15){
	header("Location: ../dashboard.php");
	exit;	
}
if(isset($name) && $name!='' && isset($_SESSION['user_id']) && $_SESSION['role_id']==1){
		$userIdVal = trim($_SESSION['user_id']);
		$data = $adminObj->getCenterAdminDataByID($userIdVal,1); // Customer tainer role 1 

		$oldPassword=$data->password;
		
        $name = addslashes(trim(filter_string($_POST["name"])));
		//$email = addslashes(trim($_POST["email"]));
		$mobile = addslashes(trim(filter_string($_POST["mobile"])));
		$qualification1 = isset($_POST["qualification"]) ? trim($_POST["qualification"]) : "";
		$qualification = filter_string($qualification1);
		$businessunit1 = isset($_POST["business_unit"]) ? trim($_POST["business_unit"]) : "";
		$businessunit = filter_string($businessunit1);
		$userIdVal = addslashes(trim(filter_query($_POST["userIdVal"])));
		$fld_password = addslashes(trim(filter_string($_POST["fld_password"])));
		$cnfPassword = addslashes(trim(filter_string($_POST["cnfPassword"])));
		$profile_id = addslashes(trim(filter_query($_POST["profile_id"])));
		$fileImgNamePro = addslashes(trim($_POST["fileImgNamePro"]));
		$cid = addslashes(trim(filter_query($_POST["cid"])));
		
		$obj = new stdClass();
		$obj->name = $name;
		$obj->mobile =$mobile;
		$obj->qualification = $qualification;
        $obj->business_unit = $businessunit;
		$obj->userIdVal = $userIdVal;
		$obj->profile_id = $profile_id;
		$obj->fileImgNamePro = $fileImgNamePro;
		$obj->center_id = $cid;

		
		/* if($fld_password != '' && ($fld_password == $oldPassword)){
			$_SESSION['error'] =1;
			$_SESSION['msg'] ='New password can not be same as old password.';
			header("location:../profile.php");
			exit;
		} */
		
		if($fld_password != '' && ($fld_password != $cnfPassword)){
			$_SESSION['error'] =1;
			$_SESSION['msg'] ='Password and confirm password must be same.';
			header("location:../profile.php");
			exit;
		}
		
		$fld_password =(isset($fld_password) && !empty($fld_password)) ? trim(addslashes($fld_password)) : $oldPassword; 

		
		//echo "<pre>";print_r($obj); exit;
		if($fld_password!= ''){
			$obj->fld_password = $fld_password;
			$adminUpdate =$adminObj->updateCenterTrainer($obj);	
		}else{
			$adminUpdate =$adminObj->updateCenterTrainer($obj);	
		}
		
		if($adminUpdate){
			$_SESSION['succ'] = 2;
			$_SESSION['msg'] ='Profile updated successfully';
			header("location:../profile.php");
			exit;
		}else{
			$_SESSION['error'] = 0;
			$_SESSION['msg'] ='Profile not updated. Please try again.';
			header("location:../profile.php");
			exit;
		}
	}
	else{
		 $_SESSION['error'] = 0;
		 header("location:../profile.php");
		 exit;
		}

?>