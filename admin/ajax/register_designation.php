<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

$designationObj = new designationController();
$did =filter_query($_POST['did']);
//echo "<pre>";print_r( $_POST);exit;
if(isset($did) && $did!=''){
	$obj = new stdClass();
	$desination_short_code = addslashes(trim(filter_string($_POST["desination_short_code"])));
	$designation = addslashes(trim(filter_string($_POST["designation"])));
	$description = addslashes(trim(filter_string($_POST["description"])));
	$obj->desination_short_code = $desination_short_code;
	$obj->designation = $designation;
	$obj->description = $description;
	
	$isExist = $designationObj->chkDesignationByName($designation);
	if($isExist!==false && $isExist!=$did){
		$_SESSION['error'] = 2;
		$_SESSION['msg'] ="Designation is already exist. Please try another.";
		$did = base64_encode($did);
		header("Location: ../createDesignation.php?did=".$did);
		exit;
		
	}
	
	$chk_designation = $designationObj->updateDesignation($obj,$did);
	if($chk_designation){
		$_SESSION['succ'] = 2;
		$_SESSION['msg'] ="Designation updated successfully.";
		header("Location: ../designationList.php");
		exit;
		}else{
		$_SESSION['error'] = 1;
		$_SESSION['msg'] ="Designation not saved. Please try again.";
		header("location:../createDesignation.php");
		exit;
		} 
	}else{
		$obj = new stdClass();
		$desination_short_code = addslashes(trim(filter_string($_POST["desination_short_code"])));
		$designation = addslashes(trim(filter_string($_POST["designation"])));
		$description = addslashes(trim(filter_string($_POST["description"])));
		$obj->desination_short_code = $desination_short_code;
		$obj->designation = $designation;
		$obj->description = $description;
		$obj2 =  $obj;
		$obj2 = (array) $obj2;
		$isExist = $designationObj->chkDesignationByName($designation);
		if($isExist!==false){
			$obj2 =  $obj;
			$obj2 = (array) $obj2;
			$_SESSION['designation_details'] = $obj2;
			$_SESSION['error'] = 2;
			$_SESSION['msg'] ="Designation  is already exist. Please try another.";

			header("Location: ../createDesignation.php");
			exit;
			
		}
		$chk_designation = $designationObj->createDesignation($obj);
		if($chk_designation){
			$_SESSION['succ'] = 1;
			$_SESSION['msg'] = "Designation created successfully.";
			header("Location: ../designationList.php");
			exit;
		}else{
			$obj2 =  $obj;
			$obj2 = (array) $obj2;
			$_SESSION['designation_details'] = $obj2;
			$_SESSION['error'] = 1;
			$_SESSION['msg'] ="Designation not saved. Please try again.";
			header("location:../createDesignation.php");
			  exit;
			}
		

	 }



?>