<?php
include_once('../../header/lib.php');
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;
$center_id=$_POST['centerId'];

$clientObj = new clientController();
$adminObj = new centerAdminController();
$centerObj = new centerController(); 
	$cenRes=$clientObj->getCenterDetailsById($center_id);	
    $cenRes = (object) $cenRes[0];
	
    $signUpRes = $centerObj->getSignedUpUserCountByCenter($client_id,$center_id);
	$signUpRes = (object) $signUpRes;
	
	$batchInfo = $adminObj->getBatchDeatils($center_id);
	/* $batch='';
	 foreach($batchInfo as $key => $bValue){
		$batch_id=$bValue['batch_id'];
		$batch_name=$bValue['batch_name'];
	
	   $batch.="<option value=".$batch_id.">".$batch_name."</option>";
	 }
	 */
    
 echo json_encode(array('center_detail'=>$cenRes,'signup_detail'=>$signUpRes,'batch'=>$batchInfo));die;
?>