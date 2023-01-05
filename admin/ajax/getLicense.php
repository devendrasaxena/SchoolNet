<?php 
 error_reporting(E_ALL);
ini_set('display_errors','1'); 
include_once('../../header/lib.php');
$centerObj = new centerController(); 
$commonObj = new commonController();
$customer_id=$lic_customer_id;
$centerDetail=$centerObj->getCenterByClient($client_id);
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];

$arr= array();
$con = createConnection();
$whr=" where 1=1  and lic_req_client_id=$customer_id and issued_to_customer=$center_id";

if(isset($_POST['issuedDate']) && $_POST['issuedDate']!=''){
	$issuedDate=trim($_POST['issuedDate']);
	$issuedDate=date('Y-m-d', strtotime($issuedDate));
	$whr.=" AND DATE(issued_date)='$issuedDate'";
}


if(isset($_POST['usedType']) && $_POST['usedType']!=''){
	$usedType=trim($_POST['usedType']);
	if($usedType==1){
	$whr.=" AND (license_used_by!='' and license_used_by IS NOT NULL)";
	}
	elseif($usedType==2){
	$whr.=" AND (license_used_by='' or license_used_by IS NULL)";
	}
}

if(isset($_POST['usedDate']) && $_POST['usedDate']!=''){
	$usedDate=trim($_POST['usedDate']);
	$usedDate=date('Y-m-d', strtotime($usedDate));
	$whr.=" AND DATE(used_date)='$usedDate'";
}

		$stmt = $con->prepare("SELECT license_id,license_value,license_status,license_used_by,used_date,issued_to_customer,issued_date FROM tbl_client_licenses $whr");
		$stmt->execute();
		$stmt->bind_result($license_id,$license_value,$license_status,$license_used_by,$used_date,$issued_to_customer,$issued_date);
		$license_arr = array();
		while($stmt->fetch()) {
		if($issued_to_customer!=''){
			$centerDtl=$commonObj->getDatabyId('tblx_center','center_id',$issued_to_customer);
			$issued_to_customer=$centerDtl['name'];
		}
		else{$issued_to_customer='-';}
		
		if($issued_date!=''){
				$issued_date=date('m-d-Y',strtotime($issued_date));
			}
		else{$issued_date='-';}
			
		
		if($license_used_by!=''){
			$usedByDtl=$commonObj->getDatabyId('user','user_id',$license_used_by);
			$license_used_by=$usedByDtl['first_name'].' '.$usedByDtl['last_name'];
		}
		else{$license_used_by='-';}
		
		if($used_date!=''){
				$used_date=date('m-d-Y',strtotime($used_date));
			}
			else{$used_date='-';}
		
		$bcm = new stdClass();
		$bcm->license_id = $license_id;
		$bcm->license_value = $license_value;
		$bcm->license_status = $license_status;
		$bcm->license_used_by = $license_used_by;
		$bcm->used_date = $used_date;
		$bcm->issued_date = $issued_date;
			
		array_push($license_arr,$bcm);
		}
		$stmt->close();	
		

	echo json_encode(array('result'=>$license_arr));


/* if($_POST['batchId']==1){
	echo json_encode(array('show'=>1));
}
else{
	echo json_encode(array('show'=>0));	
} */
 
	 
	

?>
