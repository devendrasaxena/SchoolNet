<?php
include_once('../../header/lib.php');
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;
$center_id=$_POST['centerId'];
$customerId=$_POST['customerId'];
//echo $center_id;exit;
$commonObj = new commonController();
$clientObj = new clientController();
$centerObj = new centerController(); 
$landingObj = new landingController();

	$cenRes=$clientObj->getCenterDetailsById($center_id);	
     $cenRes = (object) $cenRes[0];
	 
	 $res = $centerObj->getSignedUpUserCountByCenter($client_id,$center_id);
	 $res = (object) $res;

	$batchCount=$clientObj->getBatchCount($center_id);	
	$batchCount=!empty($batchCount)?$batchCount:0;	
	
	$res2=$clientObj->getLicenseCenterMap($customerId,$center_id);	

	
 echo json_encode(array('center_detail'=>$res,'license_detail'=>$res2,'cDetail'=>$cenRes,'batch_count'=>$batchCount));die;
?>