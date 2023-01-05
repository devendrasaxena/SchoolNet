<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

$centerObj = new centerController();
if($_GET['action'] == 'getDist'){
	 $districts = $centerObj->getAllDistrictByState($_POST['state_id']);
	if($_POST['state_name']=='DFPD'){
		foreach ($districts as  $district) {
			$selectedDist = '';
			if($_POST['dist_id'] == $district['district_id']){
				$selectedDist = 'selected="1"';
			}
			$html .= '<option value="'.$district['district_id'].'" '.$selectedDist.'>'.$district['district_name'].'</option>';
		}	
	}else{
		
		$html = '<option value="">'.$language[$_SESSION['language']]['select_district'].'</option>';
		foreach ($districts as  $district) {
			$selectedDist = '';
			if($_POST['dist_id'] == $district['district_id']){
				$selectedDist = 'selected="1"';
			}
			$html .= '<option value="'.$district['district_id'].'" '.$selectedDist.'>'.$district['district_name'].'</option>';
		}
	}
	
	echo $html;
}
if($_GET['action'] == 'getTehsil'){
	$tehsils = $centerObj->getAllTehsilByDistrict($_POST['dist_id']);
	if($_POST['state_name']=='DFPD'){
		
		foreach ($tehsils as  $tehsil) {
			$selectedTehsil = '';
			if($_POST['teh_id'] == $tehsil['tehsil_id']){
				$selectedTehsil = 'selected="1"';
			}
			$html .= '<option value="'.$tehsil['tehsil_id'].'" '.$selectedTehsil.'>'.$tehsil['tehsil_name'].'</option>';
		}
	}else{
		$html = '<option value="">'.$language[$_SESSION['language']]['select_tehsil'].'</option>';
		foreach ($tehsils as  $tehsil) {
			$selectedTehsil = '';
			if($_POST['teh_id'] == $tehsil['tehsil_id']){
				$selectedTehsil = 'selected="1"';
			}
			$html .= '<option value="'.$tehsil['tehsil_id'].'" '.$selectedTehsil.'>'.$tehsil['tehsil_name'].'</option>';
		}
	}
	echo $html;
}
?>