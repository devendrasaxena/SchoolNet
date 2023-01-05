<?php
include_once('../../header/lib.php');

$center_id= trim($_POST['center_id']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController();
$optionSelected = "";
$users_arr = $reportObj->getUsersByCenter($center_id,'2');
	 
	 if(count($users_arr)>0){
		 
		 echo '<option value="" selected>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
				$user_id = $value->user_id;
				
				$first_name = $value->first_name;
				$last_name = $value->last_name;
				$fullname = $first_name." ".$last_name;
		
				if($valSelected!=""){$optionSelected = ($valSelected == $user_id) ? "selected" : "";}
				echo '<option   value="'.$user_id.'" '.$optionSelected.' >'.$fullname.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}
?>