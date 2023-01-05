<?php

error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');

$assignmentObj = new assignmentController();
$adminObj = new centerAdminController();
$assessmentObj = new assessmentController();



function uploadAssigemnet($file){
	$target_dir = "../assignment_files/";
$target_file = $target_dir . time() .'_'.basename($file["assignment_file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if (move_uploaded_file($file["assignment_file"]["tmp_name"], $target_file)) {
    return '../trainer/assignment_files/'.time() .'_'.basename($file["assignment_file"]["name"]);
  } else {
    return '-';
  }

}



/* Create Assingnment */
if(isset($_POST['createAssignment']) && isset($_POST['assignment_id']) && empty($_POST['assignment_id'])){
	$postArray = $_POST;
	$postArray['assignment_file'] = '';
	if($_FILES['assignment_file']['error'] != '4'){
		$postArray['assignment_file'] = uploadAssigemnet($_FILES);
	}
	$assignment = $assignmentObj->saveAssignment($postArray);
	if($assignment){
		$_SESSION['succ'] =1;
		$_SESSION['msg'] ='Assignment created successfully.';
		header('Location:../assignments.php');
		exit;
		
	} else {
		$_SESSION['error'] =1;
		$_SESSION['msg'] ='Assignment not saved. Please try again.';
		header('Location:../createAssignment.php');
		exit;
	}
	
} 

/* Update Assingnment */
if(isset($_POST['createAssignment']) && isset($_POST['assignment_id']) && !empty($_POST['assignment_id'])){
	$postArray = $_POST;
	$postArray['assignment_file'] = '';
	if($_FILES['assignment_file']['error'] != '4'){
		$postArray['assignment_file'] = uploadAssigemnet($_FILES);
	}
	$assignment = $assignmentObj->saveAssignment($postArray, true);
	if($assignment){
		$_SESSION['succ'] =2;
		$_SESSION['msg'] ='Assignment updated successfully.';
		header('Location:../assignments.php');
		exit;
	} else {
		$_SESSION['error'] =1;
		$_SESSION['msg'] ='Assignment not saved. Please try again.';
		header('Location:../createAssignment.php');
		exit;
	}
} 


?>