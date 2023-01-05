<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

$tehsilObj = new tehsilController();
$tid =filter_query($_POST['tid']);
//echo "<pre>";print_r( $_POST);exit;
if(isset($tid) && $tid!=''){
	$obj = new stdClass();
	$name = addslashes(trim(filter_string($_POST["name"])));
	$district_dropdown = addslashes(trim(filter_string($_POST["district_dropdown"])));
	$obj->name = $name;
	$obj->district_dropdown = $district_dropdown;
	
	$isExist = $tehsilObj->chkTehsilByName($name);
	if($isExist!==false && $isExist!=$tid){
		$_SESSION['error'] = 2;
		$_SESSION['msg'] ="$tehsil is already exist. Please try another.";
		$tid = base64_encode($tid);
		header("Location: ../createTehsil.php?tid=".$tid);
		exit;
		
	}
	
	$chk_tehsil = $tehsilObj->updateTehsil($obj,$tid);
	if($chk_tehsil){
		$_SESSION['succ'] = 1;
		$_SESSION['msg'] ="$tehsil updated successfully.";
		header("Location: ../tehsilList.php");
		exit;
		}else{
		$_SESSION['error'] = 1;
		$_SESSION['msg'] ="$tehsil not saved. Please try again.";
		header("location:../createTehsil.php");
		exit;
		} 
	}else{
    	$name = addslashes(trim(filter_string($_POST["name"])));
		$district_dropdown = addslashes(trim(filter_string($_POST["district_dropdown"]))); 
		$obj = new stdClass();
		$obj->name = $name;
		$obj->district_dropdown = $district_dropdown;
		$obj2 =  $obj;
		$obj2 = (array) $obj2;
		$isExist = $tehsilObj->chkTehsilByName($name);
		if($isExist!==false){
			$obj2 =  $obj;
			$obj2 = (array) $obj2;
			$_SESSION['tehsil_details'] = $obj2;
			$_SESSION['error'] = 2;
			$_SESSION['msg'] ="$tehsil  is already exist. Please try another.";

			header("Location: ../createTehsil.php");
			exit;
			
		}
		$chk_tehsil = $tehsilObj->createTehsil($obj);
		if($chk_tehsil){
			$_SESSION['succ'] = 1;
			$_SESSION['msg'] = "$tehsil updated successfully.";
			header("Location: ../tehsilList.php");
			exit;
		}else{
			$obj2 =  $obj;
			$obj2 = (array) $obj2;
			$_SESSION['tehsil_details'] = $obj2;
			$_SESSION['error'] = 1;
			$_SESSION['msg'] ="$tehsil not saved. Please try again.";
			header("location:../createTehsil.php");
			  exit;
			}
		

	 }



?>