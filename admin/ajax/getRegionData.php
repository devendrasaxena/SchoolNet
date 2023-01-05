<?php
include_once('../../header/lib.php');
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;

$options = array();
$options['client_id'] = $client_id;
$center_id=$_POST['centerId'];
$customerId=$_POST['customerId'];
$region_id=$_POST['region_id'];
//echo $center_id;exit;
$commonObj = new commonController();
$clientObj = new clientController();
$centerObj = new centerController(); 
$landingObj = new landingController();

$options['region_id'] = $region_id;

$response_result=$centerObj->getRegionAdminCount($options);
$total_region_admins = $response_result['total'];


$region_result=$centerObj->getRegionDataByID($region_id);
$region_name = $region_result[0]['region_name'];

	 
$res2 = $centerObj->getCenterListCountByRegion($options);

$total_centers = $res2['total'];


$res = $centerObj->getSignedUpUserCountByRegion($client_id,$region_id);
 $res = (object) $res;

$batchCount=$clientObj->getBatchCount($center_id);	
$batchCount=!empty($batchCount)?$batchCount:0;	


	
 echo json_encode(array('center_detail'=>$res,'region_name'=>$region_name,'cDetail'=>$cenRes,'batch_count'=>$batchCount,'totalStudent'=>$totalStudent ,'total_region_admins'=>$total_region_admins ,'total_centers'=>$total_centers ));die;
?>