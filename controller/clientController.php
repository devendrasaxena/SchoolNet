<?php
class clientController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }
	public function getCenterByClientId(){
		 	 //echo "<pre>";print_r($_SESSION['client_id']);exit;
			$sql = "SELECT c.* FROM tblx_center c where c.client_id=:client_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':client_id', $_SESSION['client_id'], PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		
    }

	

	public function getClientDetails(){
		 	 //echo "<pre>";print_r($_SESSION['client_id']);exit;
			$sql = "SELECT c.* FROM user_center_map c where c.center_id=:center_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		
    }
	
		//============= Get select center details  By Id methods
	public function getCenterDetailsById($center_id){
		$sql = "SELECT c.* FROM tblx_center c where c.center_id=:center_id AND client_id=:client_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $_SESSION['client_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}		

    }
	//============= Get client product info methods
	 public function getClientProductInfo(){
		
		if(isset($_SESSION['client_id']) && !empty($_SESSION['client_id'])){
				$client_id = $_SESSION['client_id'];
			}
	        else{
				$clientDetails = $this->getClientDetails();
				$client_id = $clientDetails[0]['client_id'];
			}
			
		$sql = "SELECT * FROM client  where client_id=:client_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}		 
    }
	
	//============= ADD/Update company set up details in client config====
 function addCompanyInfo($res){
	  // echo "<pre>";print_r($res);exit;
            $app_name=$res['app_name'];
			$logo=$res['fileImgNamePro'];
			$company_name=$res['company_name'];
			$support_email=$res['support_email'];
			$support_contact=$res['support_contact'];
			$company_address=$res['company_address'];
			$country=$res['countryData'];
			$state=$res['state'];
			$city=$res['city'];
			$zipcode=$res['zipcode'];
			$is_teacher=$res['is_teacher'];
			$lbl_center=$res['lbl_center'];
			$lbl_batch=$res['lbl_batch'];
			$lbl_teacher=$res['lbl_teacher'];
			$lbl_student=$res['lbl_student'];
			$lbl_test=$res['lbl_test'];
			$sectionType=$res['sectionType'];
			$lbl_section=$res['lbl_section'];
			$lbl_battery=$res['lbl_battery'];
			$is_color=$res['is_color'];
			$tbColor =$res['tbColor'];
			$tbFontColor=$res['tbFontColor'];
			$arrTB = array('bg' => $tbColor, 'fc' => $tbFontColor);
			$top_banner =json_encode($arrTB);
			
			$lbColor=$res['lbColor'];
			$lbFontColor=$res['lbFontColor'];
			$arrLB = array('bg' => $lbColor, 'fc' => $lbFontColor);
			$left_banner =json_encode($arrLB);
			
			$sql = "UPDATE client SET name = :app_name , modified_date= now() WHERE client_id = :client_id";
			$stmt = $this->dbConn->prepare($sql);                                  
			$stmt->bindParam(':app_name', $app_name, PDO::PARAM_STR);
			$stmt->bindParam(':client_id', $_SESSION['client_id'], PDO::PARAM_INT);   
			$stmt->execute(); 
			$stmt->closeCursor();			
			
			$sql = "UPDATE client_config SET app_name = :app_name,logo = :logo,company_name = :company_name,support_email = :support_email,support_contact = :support_contact,  company_address = :company_address,country = :country,state = :state,city = :city, zipcode = :zipcode,is_teacher = :is_teacher, lbl_center = :lbl_center,lbl_batch = :lbl_batch, lbl_teacher = :lbl_teacher,lbl_student = :lbl_student,lbl_test = :lbl_test,section_type = :section_type,lbl_section = :lbl_section,lbl_battery = :lbl_battery, is_color = :is_color,top_banner = :top_banner,left_banner = :left_banner WHERE client_id = :client_id";
			
			$stmt = $this->dbConn->prepare($sql);                                  
			$stmt->bindParam(':app_name', $app_name, PDO::PARAM_STR);       
			$stmt->bindParam(':logo', $logo, PDO::PARAM_STR);    
			$stmt->bindParam(':company_name', $company_name, PDO::PARAM_STR);
			// use PARAM_STR although a number  
			$stmt->bindParam(':support_email', $support_email, PDO::PARAM_STR); 
			$stmt->bindParam(':support_contact', $support_contact, PDO::PARAM_INT);   
			$stmt->bindParam(':company_address', $company_address, PDO::PARAM_STR);   
			$stmt->bindParam(':country', $country, PDO::PARAM_STR);   
			$stmt->bindParam(':state', $state, PDO::PARAM_STR);   
			$stmt->bindParam(':city', $city, PDO::PARAM_STR);   
			$stmt->bindParam(':zipcode', $zipcode, PDO::PARAM_INT);   
			$stmt->bindParam(':is_teacher', $is_teacher, PDO::PARAM_STR);   
			$stmt->bindParam(':lbl_center', $lbl_center, PDO::PARAM_STR);   
			$stmt->bindParam(':lbl_batch', $lbl_batch, PDO::PARAM_STR);   
			$stmt->bindParam(':lbl_teacher', $lbl_teacher, PDO::PARAM_STR);   
			$stmt->bindParam(':lbl_student', $lbl_student, PDO::PARAM_STR);   
			$stmt->bindParam(':lbl_test', $lbl_test, PDO::PARAM_STR);
			$stmt->bindParam(':section_type', $sectionType, PDO::PARAM_STR);	
            $stmt->bindParam(':lbl_section', $lbl_section, PDO::PARAM_STR);
			$stmt->bindParam(':lbl_battery', $lbl_battery, PDO::PARAM_STR);
			$stmt->bindParam(':is_color', $is_color, PDO::PARAM_STR); 
			$stmt->bindParam(':top_banner', $top_banner, PDO::PARAM_STR);
			$stmt->bindParam(':left_banner', $left_banner, PDO::PARAM_STR);			
			$stmt->bindParam(':client_id', $_SESSION['client_id'], PDO::PARAM_INT);   
			//$stmt->execute(); 
			$RESULT =$stmt->execute(); 
			$stmt->closeCursor();
			  //echo "<pre>";print_r($result);exit;
			return $RESULT; 
   }
   	//=============Get/Select company set up details from client config====
  function getCompanyInfo(){
	   
	    if(isset($_SESSION['client_id']) && !empty($_SESSION['client_id'])){
				$client_id = $_SESSION['client_id'];
			}
	        else{
				$clientDetails = $this->getClientDetails();
				$client_id = $clientDetails[0]['client_id'];
			}
			$sql = "Select * from client_config  WHERE client_id = :client_id";
			 //echo "<pre>";print_r($sql);exit;
			$stmt = $this->dbConn->prepare($sql);                                  
			$stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);	
			$stmt->execute(); 			
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			 //echo "<pre>";print_r($RESULT);exit;
			if($RESULT!="" && count($RESULT)>1){
				return $RESULT[0];
			}else if($RESULT!="" && count($RESULT)==1){

				return $RESULT[0];

			}else{
				return $RESULT;
				} 
   
   }
    
   	//============= Get User Test Attempt  details  by center map 
	public function getUserTestsCenterMap($center_id){
			
			
		/* $sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.test_id FROM `temp_ans_push` INNER JOIN user_center_map on temp_ans_push.user_id = user_center_map.user_id and user_center_map.center_id=:center_id) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0]['cnt'];	 */
		$sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.test_id FROM `temp_ans_push` INNER JOIN user_center_map on temp_ans_push.user_id = user_center_map.user_id and user_center_map.center_id=:center_id and (temp_ans_push.battery_id='' or temp_ans_push.battery_id IS NULL)) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        $cnt1= $RESULT[0]['cnt'];
		//echo "<pre>";print_r($cnt1);
		
		$sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.battery_id FROM `temp_ans_push` INNER JOIN user_center_map on temp_ans_push.user_id = user_center_map.user_id and user_center_map.center_id=:center_id and (temp_ans_push.battery_id!='' and temp_ans_push.battery_id IS NOT NULL)) AS DerivedTableAlias";
		
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $cnt2= $RESULT[0]['cnt'];
	   //echo "<pre>";print_r($cnt2);
		 $cnt=$cnt1+$cnt2;
		//echo "<pre>";print_r($cnt);
		return $cnt;		

    }
	//============= Get License  by center map 
	public function getLicenseCenterMap($customer_id,$center_id){
			
		$con = createConnection();
		$stmt = $con->prepare("SELECT COUNT(*) FROM tbl_client_licenses where  lic_req_client_id=? and issued_to_customer=?");
		$stmt->bind_param("ii",$customer_id,$center_id);
		$stmt->execute();
		$stmt->bind_result($Issued);
		$stmt->fetch();
		$stmt->close();
		$bcm = new stdClass();
		$bcm->totalIssued = $Issued;
		
		$stmt = $con->prepare("SELECT COUNT(*) FROM tbl_client_licenses where license_used_by!='' and lic_req_client_id=$customer_id and issued_to_customer=$center_id");
		$stmt->bind_param("ii",$customer_id,$center_id);
		$stmt->execute();
		$stmt->bind_result($Used);
		$stmt->fetch();
		$stmt->close();
		$bcm->totalUsed = $Used;
		$bcm->totalUnUsed = ($Issued-$Used);
		
		return $bcm;
 

			

    }
	//============= Get License  details by center map 
   public function getLicenseListByCenter($customer_id,$center_id,$license=''){
	  // echo "<pre>";print_r($center_id);exit;
	   
        $whr="where lic_req_client_id=$customer_id and license_value!='1EAA401523'";
		if($license!=""){
				$whr.= " AND license_value = '".$license."'";
			}
		$con = createConnection();
		$stmt = $con->prepare("SELECT license_id,license_value,trainer_limit,student_limit,lic_req_license_expiry_lan,lic_exp_day_af_reg_lan,license_status,license_used_by,license_used_by_name,used_date,issued_date,license_created_date FROM tbl_client_licenses $whr order by license_created_date DESC");
		$stmt->execute();
		$stmt->bind_result($license_id,$license_value,$trainer_limit,$student_limit,$lic_req_license_expiry_lan,$lic_exp_day_af_reg_lan,$license_status,$license_used_by,$license_used_by_name,$used_date,$issued_date,$license_created_date);
		$license_arr = array();
		while($stmt->fetch()) {
			
		$bcm = new stdClass();
		$bcm->license_id = $license_id;
		$bcm->license_value = $license_value;
		$bcm->trainer_limit = $trainer_limit;
		$bcm->student_limit = $student_limit;
		$bcm->lic_req_license_expiry_lan = $lic_req_license_expiry_lan;
		$bcm->lic_exp_day_af_reg_lan = $lic_exp_day_af_reg_lan;

		$bcm->license_status = $license_status;
		$bcm->license_used_by = $license_used_by;
		$bcm->license_used_by_name = $license_used_by_name;
		$bcm->used_date = $used_date;
		$bcm->issued_date = $issued_date;
		$bcm->license_created_date = $license_created_date;
			
		array_push($license_arr,$bcm);
		}
		$stmt->close();	
		return $license_arr;

	}

	//============= Get License  details by center map ==//
   public function getLicenseListByCenterAndStatus($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){

		$whr="where tcl.lic_req_client_id='".$cond_arr['customer_id']."' and tcl.license_value!='1EAA401523'";

		if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
		   $whr.= " AND trcm.region_id = '".$cond_arr['region_id']."'";
		}
		if($_SESSION['role_id']==7){
		   $whr.= " AND tcl.lic_req_by_user = '".$_SESSION['user_id']."'"; 
		}
		
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
		   $center_code = 'CN-'.$cond_arr['center_id'];
		   $whr.= " AND tcl.license_used_by = '".$center_code."'";
		}
		if($cond_arr['status']!="" && $cond_arr['status']!='All'){
		if($cond_arr['status']=='active'){
		  $whr.= " AND  ((tcl.license_status = 4   AND (
		  ((DATE(NOW())< DATE(tcl.lic_req_license_expiry_lan)) AND  tcl.lic_req_license_expiry_lan IS NOT NULL and  tcl.lic_req_license_expiry_lan!='0000-00-00') or (tcl.lic_exp_day_af_reg_lan is not null and (DATE(NOW())< DATE_ADD(DATE(tcl.used_date), INTERVAL tcl.lic_exp_day_af_reg_lan DAY))))))";

		}elseif($cond_arr['status']=='available'){
		  $whr.= " AND tcl.license_status = 1 AND (tcl.used_date IS NULL or tcl.used_date='0000-00-00 00:00:00')";
		} elseif($cond_arr['status']=='expired'){
		
			$whr.= " AND  tcl.license_status = 0 or (tcl.license_status = 4  AND ((tcl.lic_exp_day_af_reg_lan is not null  and (DATE(NOW())> DATE_ADD(DATE(tcl.used_date), INTERVAL tcl.lic_exp_day_af_reg_lan DAY))) or (tcl.lic_req_license_expiry_lan is not null and ((DATE(NOW())> DATE(tcl.lic_req_license_expiry_lan))))))";
		}

		}
		if($cond_arr['license_type']!="" && $cond_arr['license_type']!='All'){
		 $whr.= " AND tcl.license_type LIKE '".$cond_arr['license_type']."'";
		}
		if($cond_arr['license']!="" && $cond_arr['license']!='All'){
		 $whr.= " AND tcl.license_value LIKE '%".$cond_arr['license']."%'";
		}


		$sql = "Select count(DISTINCT tcl.license_id) as 'cnt' FROM tbl_client_licenses tcl LEFT JOIN tblx_center tc ON tcl.license_value = tc.license_key LEFT JOIN tblx_region_country_map trcm on tc.country = trcm.country_name LEFT JOIN user u on tcl.lic_req_by_user = u.user_id $whr ";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );



		$limit_sql = '';
		if( !empty($limit) ){
		 $limit_sql .= " LIMIT $start, $limit";
		}


		$con = createConnection();
		$stmt = $con->prepare("SELECT tcl.license_id,tcl.license_value,tcl.trainer_limit,tcl.student_limit,tcl.lic_req_license_expiry_lan,tcl.lic_exp_day_af_reg_lan,tcl.license_status,tcl.license_used_by,tcl.license_used_by_name,tcl.used_date,tcl.issued_date,tcl.license_created_date,tcl.license_type,tcl.lic_req_by_user,u.first_name,u.last_name FROM tbl_client_licenses tcl LEFT JOIN tblx_center tc ON tcl.license_value = tc.license_key LEFT JOIN tblx_region_country_map trcm on tc.country = trcm.country_name LEFT JOIN user u on tcl.lic_req_by_user = u.user_id $whr group by tcl.license_id  ORDER BY ".$order." ".$dir." $limit_sql");
		$stmt->execute();
		$stmt->bind_result($license_id,$license_value,$trainer_limit,$student_limit,$lic_req_license_expiry_lan,$lic_exp_day_af_reg_lan,$license_status,$license_used_by,$license_used_by_name,$used_date,$issued_date,$license_created_date,$license_type,$lic_req_by_user,$first_name,$last_name);
		$license_arr = array();
		while($stmt->fetch()) {

		$bcm = new stdClass();
		$bcm->license_id = $license_id;
		$bcm->license_value = $license_value;
		$bcm->trainer_limit = $trainer_limit;
		$bcm->student_limit = $student_limit;
		$bcm->lic_req_license_expiry_lan = $lic_req_license_expiry_lan;
		$bcm->lic_exp_day_af_reg_lan = $lic_exp_day_af_reg_lan;

		$bcm->license_status = $license_status;
		$bcm->license_used_by = $license_used_by;
		$bcm->license_used_by_name = $license_used_by_name;
		$bcm->used_date = $used_date;
		$bcm->issued_date = $issued_date;
		$bcm->license_created_date = $license_created_date;
		$bcm->license_type = $license_type;
		$bcm->lic_req_by_user = $lic_req_by_user;
		$full_name = $first_name.' '.$last_name;
		$bcm->full_name = $full_name;
		array_push($license_arr,$bcm);
		}
		$stmt->close();

		return array('total' =>$row_cnt['cnt'] , 'result' => $license_arr);

	}

	
	public function getReqLicenseDetails(){
		 	 //echo "<pre>";print_r($_SESSION['client_id']);exit;
			$sql = "SELECT c.* FROM tblx_center c where c.client_id=:client_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':client_id', $_SESSION['client_id'], PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		
    }
	
	//============= Get User Test Count   For last Weak 
	public function getUserTestsLastWeak($center_id){
	
		$startdate=date('Y-m-d 00:00:00', strtotime('-7 days'));
		$enddate=date('Y-m-d H:i:s');
			
		$sql = "SELECT * FROM(
		(SELECT tbl_test_complete_status.user_id,tbl_test_complete_status.test_id,tbl_test_complete_status.battery_id,tbl_test_complete_status.attempt_date FROM `tbl_test_complete_status` INNER JOIN user_center_map on tbl_test_complete_status.user_id = user_center_map.user_id and user_center_map.center_id=:center_id where tbl_test_complete_status.attempt_date>= '$startdate' AND tbl_test_complete_status.attempt_date <= '$enddate' and (tbl_test_complete_status.battery_id='' or tbl_test_complete_status.battery_id IS NULL) and tbl_test_complete_status.status='1')UNION
		(SELECT  tbl_test_complete_status.user_id,tbl_test_complete_status.test_id,tbl_test_complete_status.battery_id,tbl_test_complete_status.attempt_date FROM `tbl_test_complete_status` INNER JOIN user_center_map on tbl_test_complete_status.user_id = user_center_map.user_id and user_center_map.center_id=:center_id where tbl_test_complete_status.attempt_date>= '$startdate' AND tbl_test_complete_status.attempt_date <= '$enddate' and (tbl_test_complete_status.battery_id!='' or tbl_test_complete_status.battery_id IS NOT NULL) and tbl_test_complete_status.battery_status='1' group by tbl_test_complete_status.battery_id,tbl_test_complete_status.user_id)
		) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT;	
			

    }
	
	//============= Get User Test Count   For last Weak For all center
	public function getAllUserTestsLastWeak(){
		
		/* foreach($centers_arr as $key=>$val){
			$arrCenterId[]=
		} */
	
		$startdate=date('Y-m-d 00:00:00', strtotime('-7 days'));
		$enddate=date('Y-m-d H:i:s');
		
		
			
		
		$sql = "SELECT * FROM((SELECT tbl_test_complete_status.user_id,tbl_test_complete_status.test_id,tbl_test_complete_status.battery_id,tbl_test_complete_status.attempt_date FROM `tbl_test_complete_status` INNER JOIN user on tbl_test_complete_status.user_id = user.user_id and user.user_client_id=:user_client_id where attempt_date>= '$startdate' AND attempt_date <= '$enddate'  and (tbl_test_complete_status.battery_id='' or tbl_test_complete_status.battery_id IS NULL)) UNION (SELECT  tbl_test_complete_status.user_id,tbl_test_complete_status.test_id,tbl_test_complete_status.battery_id,tbl_test_complete_status.attempt_date FROM `tbl_test_complete_status` INNER JOIN user on tbl_test_complete_status.user_id = user.user_id and user.user_client_id=:user_client_id where attempt_date>= '$startdate' AND attempt_date <= '$enddate'  and (tbl_test_complete_status.battery_id!='' or tbl_test_complete_status.battery_id IS NOT NULL) and tbl_test_complete_status.battery_status='1' group by tbl_test_complete_status.battery_id,tbl_test_complete_status.user_id))AS DerivedTableAlias";
		
		
		
		
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_client_id',$_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
		$array_date=array();
		for($i=-6;$i<=0;$i++){
			 $date =date('Y-m-d', strtotime($i.' days'));
			 $cnt=0;
			foreach($RESULT as $key=>$val){
				$dte=date('Y-m-d',strtotime($val['attempt_date']));
				if($dte==$date){
					$cnt=$cnt+1;
				}
			}
			$array_date[$i]=array($date,$cnt);
		}
        return $array_date;	
			

    }
   	//============= Get Batch Count By Center
	public function getBatchCount($center_id){
			
			
		$sql = "SELECT COUNT(*) as 'totalBatch' FROM tblx_batch WHERE center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0]['totalBatch'];	
			

    }
	//============= Get Batch Count By Client Id
	public function getAllBatchCount(){
			
			
		$sql = "SELECT COUNT(*) as 'totalBatch' FROM tblx_batch tb INNER JOIN tblx_center tc ON tb.center_id=tc.center_id where tc.client_id=:client_id";
		 $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id', $_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0]['totalBatch'];	
			

    }
	
	
    public function searchLicense($customer_id,$license, $center_id='', $region_id='', $status=''){
		$whr = "where lic_req_client_id=$customer_id AND license_value LIKE '%$license%' AND license_value!='1EAA401523'";	
		if($center_id!="" && $center_id!='All'){
			   $center_code = 'CN-'.$center_id;
			   $whr.= " AND tcl.license_used_by = '".$center_code."'";
			}	
		if($region_id!="" && $region_id!='All'){
			   $whr.= " AND trcm.region_id = '".$region_id."'";
			}
		if($status!="" && $status!='All'){
		if($status=='active'){
		  $whr.= " AND  ((tcl.license_status = 4  AND (tcl.used_date IS NOT NULL or tcl.used_date!='0000-00-00 00:00:00') AND (((DATE(NOW())< DATE(tcl.lic_req_license_expiry_lan)) AND  tcl.lic_req_license_expiry_lan IS NOT NULL and  tcl.lic_req_license_expiry_lan!='0000-00-00') or  ((DATE(NOW())< DATE_ADD(DATE(tcl.used_date), INTERVAL tcl.lic_exp_day_af_reg_emp DAY))))) or (tcl.license_status = 1  AND (tcl.used_date IS NOT NULL or tcl.used_date!='0000-00-00 00:00:00')))  ";

		}elseif($status=='available'){
		  $whr.= " AND tcl.license_status = 1 AND (tcl.used_date IS NULL or tcl.used_date='0000-00-00 00:00:00')";
		} elseif($status=='expired'){
		   $whr.= " AND  tcl.license_status = 4  AND (tcl.used_date IS NOT NULL or tcl.used_date!='0000-00-00 00:00:00') AND (((DATE(NOW())> DATE(tcl.lic_req_license_expiry_lan)) AND  tcl.lic_req_license_expiry_lan IS NOT NULL and  tcl.lic_req_license_expiry_lan!='0000-00-00') or  ((DATE(NOW())> DATE_ADD(DATE(tcl.used_date), INTERVAL tcl.lic_exp_day_af_reg_emp DAY)))) ";
		}
		}

	$con = createConnection();
	$stmt = $con->prepare("SELECT tcl.license_id,tcl.license_value,tcl.trainer_limit,tcl.student_limit,tcl.lic_req_license_expiry_lan,tcl.lic_exp_day_af_reg_lan,tcl.license_status,tcl.license_used_by,tcl.license_used_by_name,tcl.used_date,tcl.issued_date,tcl.license_created_date FROM tbl_client_licenses tcl LEFT JOIN tblx_center tc ON tcl.license_value = tc.license_key LEFT JOIN tblx_region_country_map trcm on tc.country = trcm.country_name $whr order by tcl.license_created_date DESC");
	$stmt->execute();
	$stmt->bind_result($license_id,$license_value,$trainer_limit,$student_limit,$lic_req_license_expiry_lan,$lic_exp_day_af_reg_lan,$license_status,$license_used_by,$license_used_by_name,$used_date,$issued_date,$license_created_date); 
	$license_arr = array();
	while($stmt->fetch()) { 

	$bcm = new stdClass();
	$bcm->license_id = $license_id;
	$bcm->license_value = $license_value;
	$bcm->trainer_limit = $trainer_limit;
	$bcm->student_limit = $student_limit;
	$bcm->lic_req_license_expiry_lan = $lic_req_license_expiry_lan;
	$bcm->lic_exp_day_af_reg_lan = $lic_exp_day_af_reg_lan;

	$bcm->license_status = $license_status;
	$bcm->license_used_by = $license_used_by;
	$bcm->license_used_by_name = $license_used_by_name;
	$bcm->used_date = $used_date;
	$bcm->issued_date = $issued_date;
	$bcm->license_created_date = $license_created_date;

	array_push($license_arr,$bcm);
	}
	$stmt->close();
	return $license_arr;

	}

	
}

?>