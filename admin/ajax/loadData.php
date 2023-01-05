<?php
include_once('../../header/lib.php');

$loadType=$_POST['loadType'];
$loadId=$_POST['loadId'];
$stateData=$_POST['stateData'];
$cityData=$_POST['cityData'];

$commonObj = new commonController();
//echo $loadId;exit;
	
if($loadType=="state"){
	$resState=$commonObj->getState($loadId);	
	//echo "<pre>";print_r($resState);exit;
	if($resState > 0){
		 $HTML="";
		 $selected="";
		foreach ($resState as $key => $value) {
				$state_id =$resState[$key]['id'];
				$state =$resState[$key]['state_name'];
	  	         $selected=($stateData==$state)?'selected':'';
				//echo "<option $selected value='".$state."'>".$state."</option>";
			    $HTML.="<option $selected value='".$state_id."'>".$state."</option>";  
				  
		}
		echo $HTML;
   } 
	
 } else{
	    
	$resCity=$commonObj->getCityName1($loadId);
		//echo "<pre>";print_r($res);exit;
	if($resCity > 0){
		$HTML="";
		foreach ($resCity as $key => $value) {
				$city =$resCity[$key]['city_name'];
				$selected=($cityData==$city)?'selected':'';
				//echo "<option $selected value='".$city."'>".$city."</option>";
			$HTML.="<option $selected value='".$city."'>".$city."</option>";  
					  
		}
		echo $HTML;
   } 
}


?>