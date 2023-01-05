<?php
include_once('../../header/lib.php');

$batch_id= trim($_POST['batch_id']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$adminObj = new centerAdminController();
$optionSelected = "";
$users_arr = $adminObj->getUserList(2, $batch_id);	
$stdInfo_arr=array();
 foreach($users_arr  as $key => $value1){
	 $stdInfo_arr[]= userdetails($value1['user_id']); 
 }
//echo '<pre>';print_r($stdInfo_arr);
	 if(count($stdInfo_arr)>0){
		 
		 echo '<option value="" selected>Select Student</option>';
		 foreach($stdInfo_arr  as $key => $value){
				
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