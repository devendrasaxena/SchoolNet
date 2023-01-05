<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$adminObj = new centerAdminController();

$name = addslashes(trim(filter_string($_POST["name"])));
$fld_password = addslashes(trim(filter_string($_POST["fld_password"])));
if(strlen($name)>30 || strlen($fld_password)>15){
	header("Location: ../dashboard.php");
	exit;	
}

if(isset($name) && $name!='' && isset($_SESSION['user_id'])){
		$userIdVal = trim($_SESSION['user_id']);
		$data = $adminObj->getAdminDataByID($userIdVal, $_SESSION['role_id']); // Admin role 3 

		$oldPassword=$data->password;
		
        $name = addslashes(trim(filter_string($_POST["name"])));
		//$email = addslashes(trim($_POST["email"]));
		$mobile = addslashes(trim(filter_string($_POST["mobile"])));
		$userIdVal = addslashes(trim(filter_query($_POST['userIdVal'])));
		$fld_password = addslashes(trim(filter_string($_POST["fld_password"])));
		$cnfPassword = addslashes(trim(filter_string($_POST["cnfPassword"])));
		$profile_id = addslashes(trim(filter_query($_POST["profile_id"])));
		$fileImgNamePro = addslashes(trim(filter_string($_POST["fileImgNamePro"])));
		
		
		
		$obj = new stdClass();
		$obj->name = $name;
		$obj->mobile =$mobile;
		$obj->userIdVal = $userIdVal;
		$obj->profile_id = $profile_id;
		$obj->fileImgNamePro = $fileImgNamePro;
		
		
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

		if($fld_password!= ''){
			$obj->fld_password = $fld_password;
			$adminUpdate =$adminObj->updateCenterAdmin($obj);
		}else{
			$adminUpdate =$adminObj->updateCenterAdmin($obj);	
		}
		
			
		if($adminUpdate){
			$_SESSION['succ'] = 2;
			$_SESSION['msg'] ='Profile updated successfully';
			header("location:../profile.php");
			exit;
			}
		else{
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
	/* else{
		$_SESSION['error'] = 0;
		header("location:../profile.php");
		exit;
		}
 */
?>