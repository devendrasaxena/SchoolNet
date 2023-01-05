<?php
// Function for update Licenses
function addUpdateLicenses($customer_id,$res){

	//file_put_contents("lica.txt",$customer_id);
		$con = createConnection();
		$stmt = $con->prepare("SELECT COUNT(*) FROM tbl_client_licenses where lic_req_client_id=?");
		$stmt->bind_param("i", $customer_id);
		$stmt->execute();
		$stmt->bind_result($totalLicenses);
		$stmt->fetch();
		$stmt->close();
		//echo $totalLicenses;
		if(count($res) > $totalLicenses)
		{
			//file_put_contents("lic.txt",count($res)."-".$totalLicenses);
			for ($i=0;$i<count($res);$i++)
			{
		
			$licArray=array();
			$licArray=(array) $res[$i];
			//echo "SELECT license_id FROM tbl_client_licenses where license_value".$licArray['license_id'];
			$stmt = $con->prepare("SELECT license_id FROM tbl_client_licenses where license_value=? and lic_req_client_id=$customer_id");
			$stmt->bind_param("s", $licArray['license_value']);
			$stmt->execute();
			$stmt->bind_result($license_id);
			$stmt->fetch();
			$stmt->close();
				if(empty($license_id))
				{
			//	file_put_contents("licval.txt",$license_id);		
				$stmt = $con->prepare("INSERT INTO tbl_client_licenses SET license_id=?,license_value=?,trainer_limit=?,student_limit=?,license_status=?,license_batch_id=?,license_used_by=?,license_created_date=?,lic_req_client_id=?,lic_req_license_expiry_lan=?,lic_exp_day_af_reg_lan=?,lic_req_license_expiry_emp=?,lic_exp_day_af_reg_emp=?,lic_usage_count=?,lic_device_count=?,lic_platform=?,lic_req_modified_on=?,lic_modified_on=NOW(),license_type=?,lic_req_by_user=?");
				$stmt->bind_param("isiisissisisiiisssi", $licArray['license_id'],$licArray['license_value'],$licArray['trainer_limit'],$licArray['student_limit'],$licArray['license_status'],$licArray['license_batch_id'],$licArray['license_centre_code'],$licArray['created_date'],$licArray['lic_req_client_id'],$licArray['lic_req_license_expiry_lan'],$licArray['lic_exp_day_af_reg_lan'],$licArray['lic_req_license_expiry_emp'],$licArray['lic_exp_day_af_reg_emp'],$licArray['lic_usage_count'],$licArray['lic_device_count'],$licArray['lic_platform'],$licArray['lic_req_modified_on'],$licArray['license_type'],$licArray['lic_req_by_user']);
				$stmt->execute();
				$stmt->close();	
				}

			}
		
		}
   return true;

}

function getLicenseDeatails($customer_id){

		
		$licString="";
		$totalIssued=0;
		$con = createConnection();
		$whr = "where lic_req_client_id=$customer_id and license_value!='1EAA401523'";
		
		if($_SESSION['role_id']==7){
		   $whr.= " AND lic_req_by_user = '".$_SESSION['user_id']."'"; 
		}
		
		
		$stmt = $con->prepare("SELECT license_value FROM tbl_client_licenses $whr");
		$stmt->execute();
		$stmt->bind_result($license_value);
		$stmt->execute();
		$questions = array();
		while($stmt->fetch()) {
			
			$totalIssued=$totalIssued+1;
			$licString=$licString.",".$license_value;
		}
		$stmt->close();	

		//$stmt = $con->prepare("SELECT COUNT(*) FROM tbl_client_licenses where issued_to_customer='0'");
		$whr = "where issued_to_customer='0' and lic_req_client_id=? and license_value!='1EAA401523'";
		
		if($_SESSION['role_id']==7){
		   $whr.= " AND lic_req_by_user = '".$_SESSION['user_id']."'"; 
		}
		$stmt = $con->prepare("SELECT COUNT(*) FROM tbl_client_licenses $whr");
		$stmt->bind_param("i", $customer_id);
		$stmt->execute();
		$stmt->bind_result($notGivenToCustomers);
		$stmt->fetch();
		$stmt->close();

		$givenToCustomers=$totalIssued-$notGivenToCustomers;
		
		$bcm = new stdClass();
		$bcm->licenses = $licString;
		$bcm->totalIssued = $totalIssued;
		$bcm->givenToCustomers = $givenToCustomers;
		$bcm->notGiveToCustomers = $notGiveToCustomers;
		return $bcm;

}


function getUsedLicense($customer_id){
		$con = createConnection();
		
		$whr = "where license_used_by!='' and lic_req_client_id=? and license_value!='1EAA401523'";
		
		if($_SESSION['role_id']==7){
		   $whr.= " AND lic_req_by_user = '".$_SESSION['user_id']."'"; 
		}
		
		$stmt = $con->prepare("SELECT COUNT(*) FROM tbl_client_licenses $whr");
		
		$stmt->bind_param("i", $customer_id);
		
		$stmt->execute();
		$stmt->bind_result($Used);
		$stmt->fetch();
		$stmt->close();
		$bcm = new stdClass();
		$bcm->totalUsed = $Used;
		return $bcm;

}


function getLicenseList($customer_id){

		$con = createConnection();
		$stmt = $con->prepare("SELECT lic_req_id,license_id,license_value,license_status,license_used_by,used_date,issued_to_customer,issued_date FROM tbl_client_licenses where lic_req_client_id=$customer_id");
		$stmt->bind_param("i", $customer_id);
		$stmt->execute();
		$stmt->bind_result($lic_req_id,$license_id,$license_value,$license_status,$license_used_by,$used_date,$issued_to_customer,$issued_date);
		$license_arr = array();
		while($stmt->fetch()) {
			
		$bcm = new stdClass();
		$bcm->lic_req_id = $lic_req_id;
		$bcm->license_id = $license_id;
		$bcm->license_value = $license_value;
		$bcm->license_status = $license_status;
		$bcm->license_used_by = $license_used_by;
		$bcm->used_date = $used_date;
		$bcm->issued_to_customer = $issued_to_customer;
		$bcm->issued_date = $issued_date;
			
		array_push($license_arr,$bcm);
		}
		$stmt->close();	
		return $license_arr;

}

function updateLicenseStatus($lid,$status){
	
			$sql = "UPDATE tbl_client_licenses SET license_status =?  WHERE lic_req_id =?";
			$con = createConnection();
			$stmt = $con->prepare($sql);    
			$stmt->bind_param("si",$status,$lid);			
			if($stmt->execute()){
				return true;
			} 
			$stmt->closeCursor();
	
}

 function checkLicUsedByUser($customer_id,$center_id,$licValue){

		$con = createConnection();
		$sql="SELECT license_status,license_used_by FROM tbl_client_licenses where lic_req_client_id=? and issued_to_customer=? and license_value=?";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $con->prepare($sql);
		$stmt->bind_param("iis", $customer_id,$center_id,$licValue);
		$stmt->execute();
		$stmt->bind_result($license_status,$license_used_by);
		$stmt->fetch();
		$stmt->close();
		$bcm = new stdClass();
		$bcm->licStatus =$license_status;
		$bcm->used_by=$license_used_by;
		//echo "<pre>";print_r($bcm);exit;
		return $bcm; 
	
}

function updateLicUsedByUser($customer_id,$center_id,$licKeyVal,$userId,$email,$lic_test_battery,$licCourse){
		$con = createConnection();
		$sql="update tbl_client_licenses SET license_used_by=?,license_used_by_email=? ,lic_test_battery=? ,lic_course=?,used_date=Now() where lic_req_client_id=$customer_id and issued_to_customer=$center_id and license_value='$licKeyVal' and license_status=1";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $con->prepare($sql);    
		$stmt->bind_param("isss",$userId,$email,$lic_test_battery,$licCourse);	
        $stmt->execute();		
		$stmt->close();
        return true; 
	
}

function getLicenseBasedTestBatteryId($customer_id,$center_id,$userId,$email){
		$con = createConnection();
		$sql="SELECT lic_test_battery ,lic_course from tbl_client_licenses where lic_req_client_id=$customer_id and issued_to_customer=$center_id and license_status=1 and license_used_by=$userId and license_used_by_email='$email'";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $con->prepare($sql);  
		$stmt->execute();		
		$stmt->bind_result($lic_test_battery,$lic_course);
		$licTestBatt_arr = array();
		while($stmt->fetch()) {
			
		$bcm = new stdClass();
		$bcm->lic_test_battery =$lic_test_battery;
		$bcm->lic_course=$lic_course;
			
		array_push($licTestBatt_arr,$bcm);
		}
		$stmt->close();	
		return $licTestBatt_arr;

	
}
function getLicenseDetailByLic($license){
		$con = createConnection();
		$stmt = $con->prepare("SELECT license_status,issued_to_customer,issued_date,trainer_limit,student_limit,lic_req_license_expiry_lan,lic_exp_day_af_reg_lan FROM tbl_client_licenses where license_value=?");
		$stmt->bind_param("s",$license);	
		$stmt->execute();
		$stmt->bind_result($license_status,$issued_to_customer,$issued_date,$trainer_limit,$student_limit,$lic_req_license_expiry_lan,$lic_exp_day_af_reg_lan);
		$license_arr = array();
		while($stmt->fetch()) {
			
		$bcm = new stdClass();
		
		$bcm->license_value = $license;
		$bcm->license_status = $license_status;
		$bcm->issued_to_customer = $issued_to_customer;
		$bcm->issued_date = $issued_date;
		$bcm->trainer_limit = $trainer_limit;
		$bcm->student_limit = $student_limit;
		$bcm->lic_req_license_expiry_lan = $lic_req_license_expiry_lan;
		$bcm->lic_exp_day_af_reg_lan = $lic_exp_day_af_reg_lan;
			
		array_push($license_arr,$bcm);
		}
		$stmt->close();	
		return $license_arr; 

}

function updateCenterLicense($res){
	    $code = "CN-".$res->center_id;
		$email_id =$res->email_id;	
		
		$con = createConnection();
		$sql = "UPDATE tbl_client_licenses SET  license_used_by = ?,license_used_by_name = ?, license_used_by_email = ?, used_date = NOW(), issued_date = '$res->license_issue_date',license_status = '4' where license_value = ?";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $con->prepare($sql);
		$stmt->bind_param("ssss",$code,$res->name,$email_id,$res->new_license_key);

        $stmt->execute();		
		$stmt->close();
		
		$sql2 = "UPDATE tblx_center SET  license_key = ?, expiry_date = '$res->expiry_date', expiry_days = '$res->expiry_days', trainer_limit = '$res->trainer_limit', student_limit = '$res->student_limit', sync_days = '$res->sync_days' where center_id=?";
		//echo "<pre>";print_r($sql2);exit;
		$stmt = $con->prepare($sql2);   
		$stmt->bind_param("si",$res->new_license_key,$res->center_id);
        $stmt->execute();		
		$stmt->close();
		
		 //// Adding license log
		$sql3 = "insert into  tblx_license_history(center_id,license,updated_by,updated_date) values(?,?,?,NOW())";
		$stmt = $con->prepare($sql3); 
		$stmt->bind_param("isi",$res->center_id,$res->new_license_key,$_SESSION['user_id']);
        $stmt->execute();		
		$stmt->close();
		
		//// old license is inactive
		 $sql4 = "UPDATE tbl_client_licenses SET  license_status = '0' where license_value = ?";
		$stmt = $con->prepare($sql4);
		$stmt->bind_param("s",$res->license_key);
        $stmt->execute();		
		$stmt->close();
		
        return true; 
	
}

function getLicenseDetailByCenter($center_id){
	    $code = "CN-".$center_id;
		
		$con = createConnection();
		$stmt1 = $con->prepare("SELECT license,created_date,updated_date from tblx_license_history  WHERE center_id=?");
		$stmt->bind_param("i",$center_id);
		$stmt1->bind_result($license,$created_date,$updated_date);
		$stmt1->execute();
		/* $history_arr=array();
		while($stmt1->fetch()) {
		   $bcm1 = new stdClass();
		   $bcm1->license=$license;
		   $bcm1->updated_date=$updated_date;
		   $bcm1->created_date=$created_date;
		   array_push($history_arr,$bcm1);
		} */
		$stmt1->close();
		 //echo "<pre>";print_r($history_arr);exit;
		 
		$stmt = $con->prepare("SELECT license_value,license_status,issued_to_customer,issued_date,trainer_limit,student_limit,lic_req_license_expiry_lan,lic_exp_day_af_reg_lan,used_date FROM tbl_client_licenses where license_used_by=? order BY license_status desc ");
		$stmt->bind_param("s",$code);
		$stmt->execute();
		$stmt->bind_result($license_value,$license_status,$issued_to_customer,$issued_date,$trainer_limit,$student_limit,$lic_req_license_expiry_lan,$lic_exp_day_af_reg_lan,$used_date);
		
	 
		
		$license_arr = array();
		while($stmt->fetch()) {
		$bcm = new stdClass();
		/* if($license_value==$license){
		 $bcm->updated_date=$updated_date;
		}else{
			$bcm->updated_date='';
		}  */
		if(isset($updated_date) && $updated_date !=""){
		  $bcm->updated_date=$updated_date;	
		}else{
			$bcm->updated_date='';
		}
		
		$bcm->license_value = $license_value;
		
		$bcm->license_status = $license_status;
		$bcm->issued_to_customer = $issued_to_customer;
		$bcm->issued_date = $issued_date;
		$bcm->trainer_limit = $trainer_limit;
		$bcm->student_limit = $student_limit;
		$bcm->lic_req_license_expiry_lan = $lic_req_license_expiry_lan;
		$bcm->lic_exp_day_af_reg_lan = $lic_exp_day_af_reg_lan;
		$bcm->used_date = $used_date;
		array_push($license_arr,$bcm);
		}
		$stmt->close();	
		return $license_arr; 

		
}

function getUsedLicenseByRegion($customer_id,$region_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT COUNT(DISTINCT(tcl.lic_req_id)) FROM tbl_client_licenses tcl JOIN tblx_center tc ON tcl.license_value=tc.license_key JOIN tblx_region_country_map trcm ON trcm.country_name = tc.country 
		where tcl.license_used_by!='' and tcl.lic_req_client_id=? and tcl.license_value!='1EAA401523' and trcm.region_id=?");
		$stmt->bind_param("ii",$customer_id,$region_id);
		$stmt->execute();
		$stmt->bind_result($Used);
		$stmt->fetch();
		$stmt->close(); 
		$bcm = new stdClass();
		$bcm->totalUsed = $Used;
		return $bcm;

}
?>