<?php

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
error_reporting(E_ALL);
ini_set('display_errors','1');
$districtObj = new districtController();
$did =filter_query($_POST['did']);
//echo "<pre>";print_r( $_POST);exit;
if(isset($did) && $did!=''){
	$obj = new stdClass();
	$name = addslashes(trim(filter_string($_POST["name"])));
	$state_dropdown = addslashes(trim(filter_string($_POST["state_dropdown"])));
	$obj->name = $name;
	$obj->state_dropdown = $state_dropdown;
	$isExist = $districtObj->chkDistrictByName($name);
	if($isExist!==false && $isExist!=$did){
		 $_SESSION['error'] = 2;
		 $_SESSION['msg'] ="$district  is already exist. Please try another.";
		$did = base64_encode($did);
		header("Location: ../createDistrict.php?did=".$did);
		exit;
		
	}
	$chk_district = $districtObj->updateDistrict($obj,$did);
	if($chk_district){
		$_SESSION['succ'] = 2;
		$_SESSION['msg'] ="$district updated successfully.";
		header("Location: ../districtList.php");
		exit;
		}else{
		$_SESSION['error'] = 1;
		$_SESSION['msg'] ="$district not saved. Please try again.";
		header("location:../createDistrict.php");
		exit;
		} 
	
	
	}else{
    	$name = addslashes(trim(filter_string($_POST["name"])));
		$state_dropdown = addslashes(trim(filter_string($_POST["state_dropdown"])));
		$obj = new stdClass();
		$obj->name = $name;
		$obj->state_dropdown = $state_dropdown;
		
		$isExist = $districtObj->chkDistrictByName($name);
		if($isExist!==false){
			$obj2 =  $obj;
			$obj2 = (array) $obj2;
			$_SESSION['district_details'] = $obj2;
			$_SESSION['error'] = 2;
			$_SESSION['msg'] ="$district  is already exist. Please try another.";

			header("Location: ../createDistrict.php");
			exit;
			
		}
		
		
		$chk_district = $districtObj->createDistrict($obj);
		if($chk_district){
			$_SESSION['succ'] = 1;
			$_SESSION['msg'] = "$district created successfully.";
			header("Location: ../districtList.php");
			exit;
		}else{
			$obj2 =  $obj;
			$obj2 = (array) $obj2;
			$_SESSION['district_details'] = $obj2;
			$_SESSION['error'] = 1;
			$_SESSION['msg'] ="$district not saved. Please try again.";
			 header("location:../createDistrict.php");
			  exit;
			} 
		

	 }



?>