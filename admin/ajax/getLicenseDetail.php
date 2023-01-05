<?php
include_once('../../header/lib.php');
if(isset($_POST['newlicense']) && $_POST['newlicense'] != ''){
   $newlicense = $_POST['newlicense'];
   $oldlicense = $_POST['oldlicense'];
     $oldLicInfo=getLicenseDetailByLic($oldlicense);
     $old_trainer_limit= $oldLicInfo[0]->trainer_limit;
	 $old_student_limit= $oldLicInfo[0]->student_limit;
	 $old_license_date= $oldLicInfo[0]->lic_req_license_expiry_lan;
	 $old_license_day= $oldLicInfo[0]->lic_exp_day_af_reg_lan;
	 
	 $newLicInfo=getLicenseDetailByLic($newlicense);
	 $new_trainer_limit= $newLicInfo[0]->trainer_limit;
	 $new_student_limit= $newLicInfo[0]->student_limit;
	 $new_license_date= $newLicInfo[0]->lic_req_license_expiry_lan;
	 $new_license_day= $newLicInfo[0]->lic_exp_day_af_reg_lan;
    if($new_license_date!='-'){
		$current_date = date('Y-m-d');
		$dateTimestamp1 = strtotime($current_date); 
		$dateTimestamp2 = strtotime($new_license_date); 
		
	}

	 if($old_trainer_limit>$new_trainer_limit || $old_student_limit> $new_student_limit || $dateTimestamp2<$dateTimestamp1){
		echo json_encode(['status'=>'0']); die;
	 }else{
		echo json_encode(['status'=>'1']); die; 
	 }


}
?>