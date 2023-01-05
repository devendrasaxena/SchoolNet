<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

if(isset($_SESSION['region_id'])){
    $region_id=$_SESSION['region_id'];
 }elseif(isset($_POST['region_id'])){
	 $region_id= trim($_POST['region_id']);
 }
 
$country= trim($_POST['country']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController(); 

$center_list_arr=$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
	 if(count($center_list_arr)>0){
		 $optionSelected = ($valSelected == 'All') ? "selected" : "";
		  echo '<option value="" '.$optionSelected.'> Select '.$center.'</option>';
			//echo '<option value="All" '.$optionSelected.'>All</option>';
		foreach($center_list_arr  as $key => $value){
				$centerId=$center_list_arr[$key]['center_id'];
				$center_name=$center_list_arr[$key]['name'];
				$optionSelected = ($valSelected == $centerId) ? "selected" : "";
				echo '<option   value="'.$centerId.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$center_name.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}
?>