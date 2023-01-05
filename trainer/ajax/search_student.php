<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';


$center_id= trim($_POST['center_id']);
$batch_id= trim($_POST['batch_id']);

$role_id = 2;
$name= trim($_POST['uname']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController();
$optionSelected = "";
$users_arr =	$reportObj->searchUsersByCenterAndBatchAndName($center_id,$batch_id,$role_id,'','',$name);
	 
	 if(count($users_arr)>0){
		 
		 $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
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

?>