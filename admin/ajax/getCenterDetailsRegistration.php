<?php
include_once('../../header/lib.php');
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;
$center_id=$_POST['centerId'];
$roleId=$_POST['roleId'];
$clientObj = new clientController();
$adminObj = new centerAdminController();
$centerObj = new centerController(); 
	$cenRes=$clientObj->getCenterDetailsById($center_id);	
    $cenRes = (object) $cenRes[0];
	
    $signUpRes = $centerObj->getSignedUpUserCountByCenter($client_id,$center_id);
	$signUpRes = (object) $signUpRes;
	
	$batchInfo = $adminObj->getBatchDeatilsAsDesignation($center_id); 
	echo json_encode( array('center_detail'=>$cenRes,'signup_detail'=>$signUpRes,'batch'=>$batchInfo));	
    die;
?>