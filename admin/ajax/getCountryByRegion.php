<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');


$region_id= trim($_REQUEST['region_id']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController();

$center_list_arr=$reportObj->getCountryByRegion($region_id);
	 if(count($center_list_arr)>0){
		  $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		  echo '<option value="" '.$optionSelected.'>Select Country</option>';
		  echo '<option value="All" >All</option>'; 
		 foreach($center_list_arr  as $key => $value){
				$countryId=$value->id;
				$countryName=$value->country_name;
				$optionSelected = ($valSelected == $countryId) ? "selected" : "";
				echo '<option   value="'.$countryName.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$countryName.'</option>';		
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}
?>