<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';


$region_id=$_POST['region_id'];
 
$name= trim($_POST['cname']);

$reportObj = new reportController();
$optionSelected = "";
$users_arr =	$reportObj->searchCountryByCountryName($name,$region_id);
	 
	 if(count($users_arr)>0){
		 
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
				$id = $value->id;
				
				$country_name = $value->country_name;
				//$optionSelected = ($name == $country_name) ? "selected" : "";
				$optionSelected =  "";
				echo '<option   value="'.$country_name.'" '.$optionSelected.' >'.$country_name.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}

?>