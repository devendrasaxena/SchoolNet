<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';

$assignmentObj = new assignmentController();

if($_GET['action'] == 'search_student'){

	$batch_id = $status = $title = '';
	if(isset($_POST['batch_id']) && $_POST['batch_id'] != ''){
		$batch_id = $_POST['batch_id'];
	}

	if(isset($_POST['status']) && $_POST['status'] != ''){
		$status = $_POST['status'];
	}
	if(isset($_POST['uname']) && $_POST['uname'] != ''){
		$title = $_POST['uname'];
	}

	$stdRowsData = $assignmentObj->getAllUserDetails(2, '', '',$batch_id, $status, $title);
	if(count($stdRowsData)>0){
			 
		$optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		foreach($stdRowsData  as $key => $value){
				
			$user_id = $value['user_id'];
			
			$first_name = $value['first_name'];
			$last_name = $value['last_name'];
			$fullname = $first_name." ".$last_name;

			$optionSelected = ($valSelected == $user_id) ? "selected" : "";
			echo '<option   value="'.$user_id.'" '.$optionSelected.' >'.$fullname.'</option>';
					
		 }
	}
	 else{
		echo '<option value="">Not Available</option>';
	}
}
if($_GET['action'] == 'search_assignment'){

	$batch_id = $status = $title = '';
	if(isset($_POST['batch_id']) && $_POST['batch_id'] != ''){
		$batch_id = $_POST['batch_id'];
	}

	if(isset($_POST['status']) && $_POST['status'] != ''){
		$status = $_POST['status'];
	}
	if(isset($_POST['uname']) && $_POST['uname'] != ''){
		$title = $_POST['uname'];
	}

	$stdRowsData = $assignmentObj->getAssignmentsByTeacher($_SESSION['user_id'], '', '',$batch_id,$status,$title);

	if($stdRowsData != false && count($stdRowsData)>0){
			 
		$optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		foreach($stdRowsData  as $key => $value){		

			echo '<option  value="'.$value['id'].'" '.$optionSelected.' >'.$value['assignment_name'].'</option>';
					
		 }
	}
	 else{
		echo '<option value="">Not Available</option>';
	}
}


?>