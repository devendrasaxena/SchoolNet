<?php 
class centerAdminController {
    
    public $dbConn;
	
	private $appVersion;
    private $isCode;
    private $platform;
    private $deviceID;
    private $deviceType;
    private $centerId;
	private $thumnail_Img_url;
	private $img_url;
	
    public function __construct() {
     	$this->dbConn = DBConnection::createConn();
		$this->appVersion = WEB_SERVICE_APP_VERSION;
        $this->isCode = 1;
        $this->platform = WEB_SERVICE_PLATFORM;
        $this->deviceID = WEB_SERVICE_DEVICE_ID;
        //$this->deviceType = WEB_SERVICE_DEVICE_TYPE;
		$this->centerId = B2C_CENTER;
		$this->thumnail_Img_url = THUMNAIL_IMG_URL;
		$this->img_url = IMG_URL;
    }
	
	 
	//============= Get center details  methods
	
	public function getClientDetails(){
		$sql = "SELECT c.* FROM user_center_map c where c.center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $clientResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($clientResult) > 0 ){
				return $clientResult;
			}else{
				return false;
			}		 
    }
	//============= Get center details  methods
	public function getCenterDetails(){
		$sql = "SELECT c.* FROM tblx_center c where c.center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $centerResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($centerResult) > 0 ){
				return $centerResult;
			}else{
				return false;
			}		
		/* 
		$obj = new stdclass();

		if(!isset($centerResult['code']) || empty($centerResult['code'])){
			return $obj;
		}

		$obj->center_code = $centerResult['code'];
		$obj->expiry_date = $centerResult['expiry_date'];
		$obj->expiry_days = $centerResult['expiry_days'];
		$obj->license_key = $centerResult['license_key'];
		$obj->trainer_limit = $centerResult['trainer_limit'];
		$obj->student_limit = $centerResult['student_limit'];
		$mac_address = $centerResult['mac_address'];

		if($centerResult['expiry_days']>0){
			$expiryDate = ($centerResult['created_date '] == "0000-00-00 00:00:00") ? date("Y-m-d H:m:s") : $centerResult['created_date'];
			$licExpDate = $centerResult['expiry_days']-(ceil(abs(strtotime(date("Y-m-d H:m:s"))-strtotime($expiryDate)) / 86400));
		}else{
			$expiryDate = ($centerResult['expiry_date'] == "0000-00-00 00:00:00") ? date("Y-m-d H:m:s") : $centerResult['expiry_date'];
			$sign = ($centerResult['expiry_date'] > date("Y-m-d H:m:s")) ? "" : "-";
			$licExpDate = $sign.ceil(abs(strtotime($centerResult['expiry_date'])-strtotime(date("Y-m-d H:m:s"))) / 86400);
		}

		if($licExpDate < 1){
			$obj->error_code = "ExpiryDays";
			$obj->licExpDate = $licExpDate;
			return $obj;
		}

		if($centerResult['sync_days']){	
			$lastSyncDate = ($centerResult['last_sync'] == "0000-00-00 00:00:00") ? date("Y-m-d H:m:s") : $centerResult['last_sync'];
			$remainingSyncDate = $centerResult['sync_days']-(ceil(abs(strtotime(date("Y-m-d H:m:s"))-strtotime($lastSyncDate)) / 86400));
		}

		if($remainingSyncDate < 1){
			$obj->error_code = "SyncDays";
			$obj->remainingSyncDate = $remainingSyncDate;
			return $obj;
		}

		return $obj; */
        
    }
	

   //============= Check Exit  batch methods
	 public function checkExitBatch($batchType){
		 //echo "<pre>";print_r($section);exit;
		$centerDetails = $this->getCenterDetails();
		$bcode = $centerDetails[0]['code'];
		$exp = explode('-',$bcode);
		
		 $sql = "Select batch_id from tblx_batch Where center_id==:center_id AND batch_type=:batchType";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $exp[1], PDO::PARAM_INT);
		$stmt->bindValue(':batchType', $batch_type, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}		
		// echo "<pre>";print_r($batchID);exit;	
	 }
   //============= Create batch methods
	 public function createCenterBatch($batchName,$batchType,$lmode){
		
		try{
			$centerDetails = $this->getCenterDetails();
			$bcode = $centerDetails[0]['code'];
			$exp = explode('-',$bcode);
			
			$sql = "SELECT MAX(batch_id) as maxBatchId from tblx_batch where center_id='".$exp[1]."'";
			//echo "<pre>";print_r($sql);exit;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$section=$RESULT[0]['maxBatchId'];
			$section=$section+1;
			 //echo "<pre>";print($section);exit;
			//// Now Adding  Batch 
			$sql = "INSERT INTO tblx_batch(center_id,batch_id, batch_code, batch_name,date_created,status,batch_type,learning_mode,is_default) VALUES('$exp[1]', '$section','$exp[0]','$batchName',NOW(),1,'$batchType','$lmode','1')";
			//echo $sql;exit;
			//echo "<pre>";print_r($sql);exit;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$batchID =$section;//$this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
			 //echo "<pre>";print($batchID);exit;
			 
			$batch_code = $bcode.'-B'.$batchID;
			$stmt = $this->dbConn->prepare("UPDATE `tblx_batch` SET `batch_code`= '$batch_code' WHERE `batch_id`=:batchID");
			$stmt->bindValue(':batchID', $batchID, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			
			$obj = new stdclass();
			$obj->batchID = $batchID;
			 return $obj;//array('$batchID' => $batchID);
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
		
    }
	
	//=============  Get batch data  methods by batch Id
	
	public function getBatchDataByID($batch_id){
		 //echo "<pre>";print_r($batch_id);exit;
		$centerDetails = $this->getCenterDetails();
		$bcode = $centerDetails[0]['code'];
		$exp = explode('-',$bcode);
		
		$sql = "Select * from tblx_batch WHERE batch_id = :batchID AND center_id=:center_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $exp[1], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
		
	}

	//=============  Get batch data  for teachers
	
	public function getTeacherBatches($teacher_id){
		try{
			$sql = "SELECT * FROM tblx_batch tb, tblx_batch_user_map tbum WHERE tb.batch_id=tbum.batch_id and tbum.user_id=:teacher_id and tbum.center_id=tb.center_id order by tb.batch_name; ";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':teacher_id', $teacher_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
			
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}	
		
	}
	
	//=============  Update batch name methods by batch Id
	
	public function updateBatchDataByID($batch_id,$batchName,$batchType,$centerId,$lmode){
		 
		try{
			 
			$centerDetails = $this->getCenterDetails();
			$bcode = $centerDetails[0]['code'];
			$exp = explode('-',$bcode);
			
			$sql = "UPDATE tblx_batch SET batch_name='$batchName',batch_type='$batchType',learning_mode='$lmode' WHERE batch_id = :batchID AND center_id=:center_id";
			
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':center_id', $exp[1], PDO::PARAM_INT);

			$stmt->execute();
			$stmt->closeCursor();
			return true;	
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	//=============  Get batch details methods
	public function getBatchDeatils($center_id){
		$sql = "SELECT * FROM tblx_batch  where center_id=:center_id and is_default='1'";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
	}
	
	//=============  Get Acc data  methods by organization/center Id with limit
	 public function getBatchList($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){  
 
		// echo "<pre>"; print_r($cond_arr);exit;

		$whr="where 1=1 AND tb.center_id= tc.center_id AND tb.is_default= '1'";
		 
		if($cond_arr['region_id']!="" && $cond_arr['region_id']!="All" && $cond_arr['region_id']!="0"){
		$whr.= " AND tr.id = '".$cond_arr['region_id']."'";
		}
		if($cond_arr['country']!="" && $cond_arr['country']!="All" && $cond_arr['country']!="0"){
		$whr.= " AND tc.country = '".$cond_arr['country']."'";
		}
		if($cond_arr['client_id']!=""){
		$whr.= " AND tc.client_id = '".$cond_arr['client_id']."'";
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!="All" && $cond_arr['center_id']!="0"){
		$whr.= " AND tc.center_id = '".$cond_arr['center_id']."'";
		}
		if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!="All" && $cond_arr['batch_id']!="0"){
		$whr.= " AND tb.batch_id = '".$cond_arr['batch_id']."'";
		}
		if($cond_arr['class_name']!="" && $cond_arr['class_name']!="All" && $cond_arr['class_name']!=""){
		$whr.= " AND tb.batch_name LIKE '%".$cond_arr['class_name']."%'";
		}


		 $sql = "SELECT    COUNT(*) as 'cnt' FROM
		(Select tb.*,tc.name as center_name from tblx_batch tb,tblx_center tc 
LEFT JOIN tblx_region AS tr ON tc.region=tr.id $whr GROUP BY tb.batch_id,tb.center_id) as DerivedTableAlias"; 
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );


		$limit_sql = '';
		if( !empty($limit) ){
		$limit_sql .= " LIMIT $start, $limit";
		}

		$sql = "Select tb.*,tc.name as center_name from tblx_batch tb,tblx_center tc 
LEFT JOIN tblx_region AS tr ON tc.region=tr.id $whr GROUP BY tb.batch_id,tb.center_id ORDER BY ".$order." ".$dir." $limit_sql";
		$stmt = $this->dbConn->prepare($sql);
		
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);
   
   }


   	public function searchClass($name,$center_id){ 
		
			
			
			
			if($name!=""){
				$whr.= " WHERE tb.batch_name LIKE '%".$name."%'";
				//$whr.= " AND tc.country = '$country'";
			}
			$whr .=" AND tb.center_id = $center_id AND tb.is_default= '1'";
				$order = "DESC";
			
				$sql = "Select * from tblx_batch tb  $whr "; 
				  // print_r($sql); exit();
			// $sql = "Select td.* from tblx_designation td $whr ORDER BY ".$order." ".$dir." $limit_sql";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
		
			

		return $RESULT;
}

	//=============  SELECT batch name methods by batch Id
	
	public function getBatchNameByID($batch_id){
		$sql = "SELECT batch_name from tblx_batch WHERE batch_id = :batchID AND center_id=:center_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
	}


	public function getTeacherBatchCount($user_id){

		$sql = "SELECT count(*) teacherCount from tblx_batch_user_map WHERE status='1' AND user_id = :user_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$tRESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if($tRESULT[0]['teacherCount']){
			$teacher = $tRESULT[0]['teacherCount'];
		}else{
	    	$teacher = 0;
		}
        $stmt->closeCursor();
		return $teacher;
	}

    //============= Get user batch detail methods by user id
    public function getUserBatch($user_id){
        //echo "<pre>";print_r($user_id);exit;
        $sql = "SELECT batch_id from tblx_batch_user_map where user_id=:user_id and status =1 AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
		$stmt->bindValue(":center_id",  $_SESSION['center_id'], PDO::PARAM_INT); 
		//echo "<pre>";print_r($stmt);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
    }
	//============= Get user details methods
	public function getUserDetails($roleID, $userID){
		if($userID != ''){
			$stmt = $this->dbConn->prepare("SELECT u.* FROM user_log_data u WHERE u.role_id = :roleID AND u.user_id = :userID AND  u.center_id=:center_id");
		 	$stmt->bindValue(":roleID", $roleID, PDO::PARAM_INT);
			$stmt->bindValue(":userID", $userID, PDO::PARAM_INT); 
			$stmt->bindValue(":center_id", $_SESSION['center_id'], PDO::PARAM_INT); 
			
		}else{
			$stmt = $this->dbConn->prepare("SELECT u.* FROM user_log_data u WHERE u.role_id = :roleID AND  u.center_id=:center_id");
			$stmt->bindValue(":roleID", $roleID, PDO::PARAM_INT);
			$stmt->bindValue(":center_id", $_SESSION['center_id'], PDO::PARAM_INT); 
		}     		
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
	}
    
	function checkEmailExits($email){
		$stmt = $this->dbConn->prepare("select c.user_id,loginid, u.address_id from user u, user_credential c where email_id=:email and c.user_id=u.user_id");
		$stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();	
		//echo '<pre>';print_r($RESULT);exit;
		if(count($RESULT) > 0 ){
				return $RESULT[0];
			}else{
				return false;
			}
	}

//////////////////////////////////////////////
	//============= Create Student and teacher methods

	public function registerUser( array $request){
		
		$roleID = ( $request['uSignUp'] == 'studentReg' ) ? 2 : 1;		
		
		$fName=filter_string($request['name']);
		$lName=filter_string($request['lastname']);
		$first_name = isset($fName) ? $fName : "";
		$last_name = isset($lName) ? $lName : "";
        $email1=filter_string($request['email']);
        $email = isset($email1) ? trim($email1) : "";
        $is_email_verified = 0;
		$mobile1=filter_string($request['mobile']);
        $phone = isset($mobile1) ? trim($mobile1) : "";
        $is_phone_verified = 0;
		$password1=filter_string($request['password']);
		$password = isset($password1) ? trim($password1) : "";
		$batch = isset($request['batch']) ? trim($request['batch']) : "";
		$gender = isset($request['gender']) ? trim($request['gender']) : "";
		$roll_no = isset($request['roll_no']) ? trim($request['roll_no']) : "";
		$fathers_name = isset($request['fathers_name']) ? trim($request['fathers_name']) : "";
		$mothers_name = isset($request['mothers_name']) ? trim($request['mothers_name']) : "";
		$slot = isset($request['slot']) ? trim($request['slot']) : "";
		$section = isset($request['section']) ? trim($request['section']) : "";


		//$marital_id = isset($request['maritalStatus']) ? trim($request['maritalStatus']) : "";
		$age = isset($request['age']) ? trim($request['age']) : "";
		//$dob= isset($request['age']) ? trim($request['age']) : "";
		$country_id = isset($request['country']) ? trim($request['country']) : "";
		//$state_id = isset($request['state_dropdown']) ? trim($request['state_dropdown']) : "";
		//$city_id = isset($request['city_dropdown']) ? trim($request['city_dropdown']) : "";
		//$pincode = isset($request['pincode']) ? trim($request['pincode']) : "";
		//$nationality = isset($request['nationality']) ? trim($request['nationality']) : "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		$education_id = isset($request['education']) ? trim($request['education']) : "";
		$emp_status_id = isset($request['empStatus']) ? trim($request['empStatus']) : "";
		$perpose_join_id = isset($request['purJoining']) ? trim($request['purJoining']) : "";
		$english_exp_id = isset($request['englishExp']) ? trim($request['englishExp']) : "";
		
		//$discover_app_id = isset($request['usersDicover']) ? trim($request['usersDicover']) : "";
		$profile_id = isset($request['profile_id'])? trim($_POST["profile_id"]) : "";
		$fileImgNamePro = isset($request['fileImgNamePro'])?trim($_POST["fileImgNamePro"]) : "";
		$status = isset($request['status']) ? $request['status'] : "";
		
		$centerDetails = $this->getCenterDetails();
		$center_id = $centerDetails[0]['center_id'];
		
		$trainer_limit = $centerDetails[0]['trainer_limit'];
		$student_limit = $centerDetails[0]['student_limit'];
		
		$clientDetails = $this->getClientDetails();
		$client_id = $clientDetails[0]['client_id'];

		//Check for registration limit
		$chkLimit= $this->getUserLimit($center_id,$roleID);
		
		$stmt = $this->dbConn->prepare("select c.user_id,loginid, u.address_id from user u, user_credential c where email_id=:email and c.user_id=u.user_id");
		$stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
			 //echo "<pre>";print_r($RESULT);exit;
				return $RESULT[0];
		}else{
			if((($roleID==1) && ($trainer_limit>$chkLimit)) || ($roleID==2) && ($student_limit>$chkLimit)){
				//// Now Adding  user address 
				//$stmt = $this->dbConn->prepare("INSERT INTO address_master( city,state,country,postal_code,phone,is_phone_verified,updated_by,created_date,nationality) VALUES(:city,:state,:country,:postal_code,:phone,:is_phone_verified,:user_id,NOW(),:nationality)");
				$stmt = $this->dbConn->prepare("INSERT INTO address_master(country,phone,is_phone_verified,updated_by,created_date) VALUES(:country,:phone,:is_phone_verified,:user_id,NOW())");
				$stmt->bindValue(':phone',$phone, PDO::PARAM_STR);
				//$stmt->bindValue(':city',$city_id, PDO::PARAM_STR);
				//$stmt->bindValue(':state',$state_id, PDO::PARAM_STR);
				$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
				//$stmt->bindValue(':postal_code',$pincode, PDO::PARAM_STR);
				$stmt->bindValue(':is_phone_verified',$is_phone_verified, PDO::PARAM_STR);
				$stmt->bindValue(':user_id',$_SESSION['user_id'], PDO::PARAM_INT);
				//$stmt->bindValue(':nationality',$nationality, PDO::PARAM_STR);
				$stmt->execute();
				$address_id =$this->dbConn->lastInsertId();
				$stmt->closeCursor(); 
				 //echo "<pre>";print_r($address_id);exit;
				 
				  //// Now Adding  Assest 
				$stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
				$stmt->execute();
				$asset_id = $this->dbConn->lastInsertId();
				$stmt->closeCursor(); 
				
				
			 //// Adding user 
				//$dateOfBirth = $dob;
				//$today = date("Y-m-d");
				//$diff = date_diff(date_create($dateOfBirth), date_create($today));
				//$age=$diff->format('%y');
				//$dob= date('Y-m-d',strtotime($dob));
				
				$stmt= $this->dbConn->prepare("insert into user(first_name,last_name,email_id,is_email_verified,address_id,profile_pic,updated_by,gender,user_client_id,age_range,mother_tongue,education,employment_status,joining_purpose,years_eng_edu,created_date, roll_no, fathers_name, mothers_name, section, slot) values(:first_name,:last_name,:email_id,:is_email_verified,:address_id,:profile_pic,:updated_by,:gender,:user_client_id,:age_range,:mother_tongue,:education,:employment_status,:joining_purpose,:years_eng_edu,NOW(), :roll_no, :fathers_name, :mothers_name, :section, :slot)");
				
				$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
				$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
				$stmt->bindValue(':email_id', $email, PDO::PARAM_STR);
				$stmt->bindValue(':is_email_verified',$is_email_verified, PDO::PARAM_STR);
				$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
				$stmt->bindValue(':profile_pic', $asset_id, PDO::PARAM_INT);
				$stmt->bindValue(':updated_by',$_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
				$stmt->bindValue(':user_client_id', $client_id, PDO::PARAM_INT);
				//$stmt->bindValue(':date_of_birth', $dob, PDO::PARAM_STR);
				$stmt->bindValue(':age_range', $age, PDO::PARAM_STR);
				//$stmt->bindValue(':marital_status', $marital_id, PDO::PARAM_INT);
				$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);
				$stmt->bindValue(':education', $education_id, PDO::PARAM_INT);
				$stmt->bindValue(':employment_status', $emp_status_id, PDO::PARAM_INT);
				$stmt->bindValue(':joining_purpose', $perpose_join_id, PDO::PARAM_INT);
				$stmt->bindValue(':years_eng_edu', $english_exp_id, PDO::PARAM_INT);
				$stmt->bindValue(':roll_no', $roll_no, PDO::PARAM_STR);
				$stmt->bindValue(':fathers_name', $fathers_name, PDO::PARAM_STR);
				$stmt->bindValue(':mothers_name', $mothers_name, PDO::PARAM_STR);
				$stmt->bindValue(':section', $section, PDO::PARAM_STR);
				$stmt->bindValue(':slot', $slot, PDO::PARAM_STR);
				
				$stmt->execute();
				
				$user_id =$this->dbConn->lastInsertId();
				$stmt->closeCursor(); 
				
				//// update profile pic  
				$stmt= $this->dbConn->prepare("UPDATE `asset` SET `system_name`=:system_name WHERE `asset_id`=:asset_id");
				$stmt->bindValue(':system_name', $fileImgNamePro, PDO::PARAM_STR);
				$stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
			   //echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$stmt->closeCursor();
				
				//// Adding user and center map 
				
				$stmt = $this->dbConn->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values('$user_id','$center_id','$client_id',NOW())");
				//echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$stmt->closeCursor(); 
				
				//// Adding Admin Credentials 
				
				$stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:loginid,:password,1,NOW())");
				$stmt->bindValue(':user_id',$user_id, PDO::PARAM_INT);
				$stmt->bindValue(':loginid', $email, PDO::PARAM_STR);
				$stmt->bindValue(':password',$password, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor(); 
				//echo "<pre>";print_r($stmt);exit;
			
				
				////Select the client to user group id */
				$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id='$client_id'");
					//echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				//echo "<pre>";print_r($RESULT[0]['user_group_id']);exit;
				$client_group_id = $RESULT[0]['user_group_id'];

				if( $roleID == 2 ){
					
					$role_type="2";//student/learner
				}
				else{
					$role_type="1";//teacher
					
				}
				//// Adding user into role map group 
				$stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values('$user_id','$role_type','$client_group_id',1,1,NOW())");
					//echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$stmt->closeCursor(); 
				
				// For batch_user_map table insert
				if( $roleID == 2 ){	
				
				    $default_batch_id = isset($request['default_batch_id']) ? trim($request['default_batch_id']) : "";
					
					if(count($request['batch']) > 0){
					  $batch = isset($request['batch']) ? $request['batch'] : 0;
					}	
				    
				  if($batch==$default_batch_id){	
							$stmt = $this->dbConn->prepare("update user_credential set is_active= '$status'   WHERE user_id=$user_id");
							$stmt->execute();
							$stmt->closeCursor();
					}else{
						$stmt = $this->dbConn->prepare("update user_credential set is_active= '$status'  WHERE user_id=$user_id");
						$stmt->execute();
						$stmt->closeCursor();
					} 
					
				  	
					$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id,status,user_server_id) values (:userID, :batchID, :center_id,1,:userServerID) ";
					
					$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
					$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':userServerID', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
					$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					//echo "<pre>";print_r($btUserMap_sql);exit;
					$btUserMap->execute();
					$btUserMap->closeCursor();
					
					
				
				}else{

					for( $i=0; $i<count($request['batch']); $i++){
						$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id) values (:userID, :batchID, :center_id) ";
						$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
						$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
						$btUserMap->bindValue(':batchID', $request['batch'][$i], PDO::PARAM_INT);
						$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
						$btUserMap->execute();
						$btUserMap->closeCursor();
					}
						
				}
				$btUserMap->closeCursor();
				
				return array('roleID' => $roleID, 'loginid' => $loginid, 'password' => $password);
				
			}else{
				return false;
			}
		}
  }
	
	//============= Upadate Student and teacher methods

	public function updateUser( array $request){
		//echo "<pre>";print_r($request);exit;
		$user_id = isset($request['userIdVal'])? $request['userIdVal'] : '';		
		$roleID = ( $request['uSignUp'] == 'studentReg' ) ? 2 : 1;		// 2 student and 1 trainer

		$fName=filter_string($request['name']);
		$lName=filter_string($request['lastname']);
		$first_name = isset($fName) ? $fName : "";
		$last_name = isset($lName) ? $lName : "";
		$email1=filter_string($request['email']);
        $email = isset($email1) ? trim($email1) : "";
        $is_email_verified = 0;
		$mobile1=filter_string($request['mobile']);
        $phone = isset($mobile1) ? trim($mobile1) : "";
        $is_phone_verified = 0;
		$password1=filter_string($request['fld_password']);
		$password = isset($password1) ? trim($password1) : "";
		$batch = isset($request['batch']) ? trim($request['batch']) : "";
		$gender = isset($request['gender']) ? trim($request['gender']) : "";
		//$marital_id = isset($request['maritalStatus']) ? trim($request['maritalStatus']) : "";
		$age = isset($request['age']) ? trim($request['age']) : "";
		//$dob= isset($request['age']) ? trim($request['age']) : "";
		$country_id = isset($request['country']) ? trim($request['country']) : "";
		//$state_id = isset($request['state_dropdown']) ? trim($request['state_dropdown']) : "";
		//$city_id = isset($request['city_dropdown']) ? trim($request['city_dropdown']) : "";
		//$pincode = isset($request['pincode']) ? trim($request['pincode']) : "";
		//$nationality = isset($request['nationality']) ? trim($request['nationality']): "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		$education_id = isset($request['education']) ? trim($request['education']) : "";
		$emp_status_id = isset($request['empStatus']) ? trim($request['empStatus']) : "";
		$perpose_join_id = isset($request['purJoining']) ? trim($request['purJoining']) : "";
		$english_exp_id = isset($request['englishExp']) ? trim($request['englishExp']) : "";

		$roll_no = isset($request['roll_no']) ? trim($request['roll_no']) : "";
		$fathers_name = isset($request['fathers_name']) ? trim($request['fathers_name']) : "";
		$mothers_name = isset($request['mothers_name']) ? trim($request['mothers_name']) : "";

		
		//$discover_app_id = isset($request['usersDicover']) ? trim($request['usersDicover']) : "";
		$profile_id = isset($request['profile_id'])? trim($_POST["profile_id"]) : "";
		$fileImgNamePro = isset($request['fileImgNamePro'])?trim($_POST["fileImgNamePro"]) : "";
		$status = isset($request['status']) ? $request['status'] : "";
		$userType = isset($request['userType']) ? $request['userType'] : "b2b";
		
		$centerDetails = $this->getCenterDetails();
		$center_id = $centerDetails[0]['center_id'];
		
		$clientDetails = $this->getClientDetails();
		$client_id = $clientDetails[0]['client_id'];
        try{
			  //// for phone get address id by user id
			$stmt = $this->dbConn->prepare("Select address_id from user  WHERE user_id=:user_id");
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$address_id = $RESULT[0]['address_id'];
			
			 //// update phone in address master
			 //$stmt = $this->dbConn->prepare("UPDATE `address_master` SET `phone`=:phone,`city`=:city,`state`=:state,`country`=:country,`postal_code`=:postal_code,`is_phone_verified`=:is_phone_verified,`modified_date`= NOW(),`nationality`=:nationality WHERE `address_id`=:address_id");
			 
			$stmt = $this->dbConn->prepare("UPDATE address_master SET phone=:phone,country=:country,is_phone_verified=:is_phone_verified,modified_date= NOW() WHERE address_id=:address_id");
			//$stmt->bindValue(':nationality',$nationality, PDO::PARAM_STR);
			$stmt->bindValue(':phone',$phone, PDO::PARAM_STR);
			//$stmt->bindValue(':city',$city_id, PDO::PARAM_STR);
			//$stmt->bindValue(':state',$state_id, PDO::PARAM_STR);
			$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
			//$stmt->bindValue(':postal_code',$pincode, PDO::PARAM_STR);
			$stmt->bindValue(':is_phone_verified',$is_phone_verified, PDO::PARAM_STR);
			$stmt->bindValue(':address_id',$address_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			
		   //// update user 
			//$dateOfBirth = $dob;
			//$today = date("Y-m-d");
			//$diff = date_diff(date_create($dateOfBirth), date_create($today));
			//$age=$diff->format('%y');
			//$dob= date('Y-m-d',strtotime($dob));
			
	  
		   $stmt = $this->dbConn->prepare("UPDATE user SET first_name=:first_name ,last_name= :last_name,gender=:gender,age_range=:age_range,mother_tongue=:mother_tongue,education=:education,employment_status=:employment_status,joining_purpose=:joining_purpose,years_eng_edu=:years_eng_edu,modified_date= Now(),  fathers_name = :fathers_name, mothers_name = :mothers_name WHERE user_id=:user_id");
			$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
			$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
			$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
			//$stmt->bindValue(':date_of_birth', $dob, PDO::PARAM_STR);
			$stmt->bindValue(':age_range', $age, PDO::PARAM_STR);
			//$stmt->bindValue(':marital_status', $marital_id, PDO::PARAM_INT);
			$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);
			$stmt->bindValue(':education', $education_id, PDO::PARAM_INT);
			$stmt->bindValue(':employment_status', $emp_status_id, PDO::PARAM_INT);
			$stmt->bindValue(':joining_purpose', $perpose_join_id, PDO::PARAM_INT);
			$stmt->bindValue(':years_eng_edu', $english_exp_id, PDO::PARAM_INT);

			$stmt->bindValue(':fathers_name', $fathers_name, PDO::PARAM_STR);
			$stmt->bindValue(':mothers_name', $mothers_name, PDO::PARAM_STR);
			

			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
			
			//// update profile pic  
			$stmt= $this->dbConn->prepare("UPDATE asset SET system_name=:system_name,modified_date=NOW() WHERE asset_id=:profile_id");
			$stmt->bindValue(':system_name', $fileImgNamePro, PDO::PARAM_STR);
			$stmt->bindValue(':profile_id', $profile_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			//// update login Credentials 
			if($password!=''){
				$sql="UPDATE user_credential SET password=:password ,modified_date=NOW() WHERE user_id=:user_id";
				$stmt= $this->dbConn->prepare($sql);
				$stmt->bindValue(':password',$password, PDO::PARAM_STR);
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor(); 
			}
		
			// For batch_user_map table UPDATE for trainer only

			//// Adding user and center map 
			if( $roleID == 1 ){
				
				$update_sql = "Update tblx_batch_user_map set status = 0 WHERE user_id = $user_id AND center_id = $center_id ";
				$update_stmt = $this->dbConn->prepare( $update_sql );
				$update_stmt->execute();
				$update_stmt->closeCursor();
				
				for( $i=0; $i<count($request['batch']); $i++){
					
					$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id) values (:user_id, :batch_id, :center_id) "
							. "ON DUPLICATE KEY UPDATE status = 1";
					$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
					
					$btUserMap->bindValue(':user_id', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':batch_id', $request['batch'][$i], PDO::PARAM_INT);
					$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					$btUserMap->execute();
					$btUserMap->closeCursor();
					
				}
					
			}else{
				
				$default_batch_id = isset($request['default_batch_id']) ? trim($request['default_batch_id']) : "";
				
				
				   if(count($request['batch']) > 0){
						  $batch = isset($request['batch']) ? $request['batch'] : 0;
				   }	
				   if(count($request['cBatch']) > 0){
						$cBatch = isset($request['cBatch']) ? $request['cBatch'] : 0;
					}
				    
				  if($batch==$default_batch_id){	
							$stmt = $this->dbConn->prepare("update user_credential set is_active= '$status'   WHERE user_id=$user_id");
							$stmt->execute();
							$stmt->closeCursor();
					}else{
						$stmt = $this->dbConn->prepare("update user_credential set is_active= '$status'  WHERE user_id=$user_id");
						$stmt->execute();
						$stmt->closeCursor();
					}
					
					
					if($cBatch!=$batch){
						$btUserMap_sql = "Update tblx_batch_user_map set batch_id =:batchID WHERE user_id=:userID AND center_id=:center_id AND status ='1'";
						$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
						$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
						$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
						$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
						$btUserMap->execute();
						$btUserMap->closeCursor();  
	
					}				
			  }

			return array('roleID' => $roleID);
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

		 //============= Get user role and  batch based
	public function getUserList($roleID, $batchID){
	  
		$sql = "SELECT uld.* FROM user_role_map uld	JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batchID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//$user_id=$RESULT[0]['user_id'];
		//echo "<pre>"; print_r(array_filter($RESULT)); //die;
		if(count($RESULT)>0){
		  $userArr = array();
		  while($row = array_shift( $RESULT ) ) {
			array_push($userArr,$row);
		  }
		  $userArr1=array_filter($userArr);
		  //echo "<pre>"; print_r($userArr1); 
          return $userArr1; 
		}else{
			return false; 
		}
   /*  echo "<pre>"; print_r($userArr); die;
		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
	    echo "<pre>"; print_r($RESULT); die;
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}  */
	}
	
	 //============= Get Global teacher for all batch 
	public function getAllUserDetails($roleID){
	  
		$sql = "SELECT uld.* FROM user_role_map uld	JOIN user_center_map ucm ON ucm.user_id = uld.user_id WHERE uld.role_definition_id = :roleID  AND ucm.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//$user_id=$RESULT[0]['user_id'];
		$userArr = array();
		while($row = array_shift( $RESULT ) ) {
			array_push($userArr,$row);
		}
      return $userArr;
			
			
	} 
	
	//============= Get Global teacher for all batch 
	public function getUserDetailsById($userID,$roleID){
	  
		$sql = "SELECT uld.* FROM user_role_map uld	JOIN user_center_map ucm ON ucm.user_id = uld.user_id WHERE uld.role_definition_id = :roleID  AND ucm.center_id=:center_id AND uld.user_id=:userID";
        $stmt = $this->dbConn->prepare($sql);
		 $stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//$user_id=$RESULT[0]['user_id'];
		$userArr = array();
		while($row = array_shift( $RESULT ) ) {
			array_push($userArr,$row);
		}
      return $userArr;
			
			
	}

	//============= updateStudentStatus activ and dective login
	public function updateStudentStatus($userID,$status){
	  
		$sql = "Update user_credential set is_active=:status WHERE user_id=:userID";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->execute();
		$stmt->closeCursor();
		return true;
			
			
	}

//============= Get user detail by  user id and role id 
   public function getUserDataByID($uid,$loginId,$roleID){

		$sql = "SELECT bum.batch_id, uld.* FROM tblx_batch_user_map bum join user_role_map uld ON bum.user_id = uld.user_id join user_credential uc "
                . " WHERE bum.status = 1 AND uld.user_id = :user_id AND uc.loginid = :loginid AND uld.role_definition_id = :roleID AND bum.center_id = :center_id";
			
				/*$sql = "SELECT bum.batch_id, uld.* FROM tblx_batch_user_map bum join user_role_map uld ON bum.user_id = uld.user_id "
                . " WHERE bum.status = 1 AND uld.user_id = ".$uid." AND uld.role_definition_id = ".$roleID." AND bum.center_id = ".$_SESSION['center_id'];*/ 

        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
		$stmt->bindValue(':loginid', $loginId, PDO::PARAM_STR);
		$stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$user_id=$RESULT[0]['user_id'];
		$list_batch = array();
		while($row = array_shift( $RESULT ) ) {
			array_push($list_batch,$row);
		}
		
		//echo "<pre>"; print_r($list_batch); die;

		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
		$first_name=$RESULT[0]['first_name'];
		$last_name=$RESULT[0]['last_name'];
		$email_id=$RESULT[0]['email_id'];
		$profile_id=$RESULT[0]['profile_pic'];
		$gender=$RESULT[0]['gender'];
		$dob=$RESULT[0]['date_of_birth'];
		if($dob=='0000-00-00'){
           $dob='';
		}else{
		  $dob= date('d-m-Y',strtotime($dob));
		}
		$age_range=$RESULT[0]['age_range'];
		$marital_status=$RESULT[0]['marital_status'];
		$mother_tongue=$RESULT[0]['mother_tongue'];
		$education=$RESULT[0]['education'];
		$employment_status=$RESULT[0]['employment_status'];
		$joining_purpose=$RESULT[0]['joining_purpose'];
		$app_discovered	=$RESULT[0]['app_discovered'];
		$course_id	=$RESULT[0]['course_id'];
		$career	=$RESULT[0]['career'];
		$areaofinterest	=$RESULT[0]['area_of_interest'];
		$userType	=$RESULT[0]['user_from'];
		$roll_no	=$RESULT[0]['roll_no'];
		$fathers_name	= $RESULT[0]['fathers_name'];
		$mothers_name	= $RESULT[0]['mothers_name'];
		$section	= $RESULT[0]['section'];
		$slot	= $RESULT[0]['slot'];
		$englishexp='';//$RESULT[0]['years_eng_edu'];
		/* $type=array();
		$stream=array();
		$course=array();
		$education_eligiblity=array();		
       
		//Get courses name
		$course_id_arr=explode(',',$course_id);
		foreach($course_id_arr as $key=>$val){
			
			$sql = "SELECT * FROM tbl_stream_course WHERE id=:id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':id',$val, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$type[]=$RESULT[0]['type'];
			$stream[]=$RESULT[0]['stream'];
			$course[]=$RESULT[0]['course'];
			$education_eligiblity[]=$RESULT[0]['education_eligiblity'];
			$education_label[]=$RESULT[0]['education_label'];

		}

		$type=array_filter($type);
		$type=array_unique($type);
		$type=$type[0];
		$stream=array_filter($stream);
		$stream=array_unique($stream);
		$course=array_filter($course);
		$course=array_unique($course);
		$education_label=array_filter($education_label);
		$education_label=array_unique($education_label);
		$education_label=$education_label[0];
		$education_eligiblity=array_filter($education_eligiblity);
		$education_eligiblity=array_unique($education_eligiblity);
		$education_eligiblity=$education_eligiblity[0]; */
	   //echo "<pre>"; print_r($RESULT); die;

		$sql = "SELECT * FROM user_center_map WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
		
		$district_id=$RESULT[0]['district_id'];
		$tehsil_id=$RESULT[0]['tehsil_id'];
		
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$loginid=$RESULT[0]['loginid'];
		$password=$RESULT[0]['password'];
		$is_active=$RESULT[0]['is_active'];
	    $expiry_date=$RESULT[0]['expiry_date'];

		$sql = "SELECT default_batch_id from tblx_center WHERE center_id=:center_id ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$default_batch_id=$RESULT[0]['default_batch_id'];
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$country_code=$RESULT[0]['country_code'];
		$phone=$RESULT[0]['phone'];
		$city=$RESULT[0]['city'];
		$state=$RESULT[0]['state'];
		$country=$RESULT[0]['country'];
		$postal_code=$RESULT[0]['postal_code'];
		$nationality=$RESULT[0]['nationality'];
		
		$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$profileName=$RESULT[0]['display_name']; 
		$profilePath=$RESULT[0]['path']; 
		$system_name=$RESULT[0]['system_name']; 
		/* $sql = "SELECT * FROM tblx_user_company WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$company=$RESULT[0]['company_id']; */
		$obj = new stdclass();
		$obj->batch_id = $list_batch;
		$obj->first_name = $first_name;
		$obj->last_name = $last_name;
		$obj->email_id = $email_id;
		$obj->password = $password;
		$obj->country_code = $country_code;
		$obj->phone = $phone;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		$obj->gender = $gender;
		$obj->dob = $dob;
		$obj->age_range = $age_range;
		$obj->marital_status = $marital_status;
		$obj->mother_tongue = $mother_tongue;
		$obj->education = $education;
		$obj->employment_status = $employment_status;
		$obj->joining_purpose = $joining_purpose;
		$obj->app_discovered = $app_discovered;
		$obj->city = $city;
		$obj->state = $state;
		$obj->country = $country;
		$obj->postal_code = $postal_code;
		$obj->nationality = $nationality;

		/* Devendra Verma*/
		$obj->district_id = $district_id;
		$obj->tehsil_id = $tehsil_id;
		/* Devendra Verma*/
		/* $obj->type = $type;
		$obj->stream = $stream;
		$obj->course = $course;
		$obj->education_eligiblity = $education_eligiblity; */
		$obj->education_label = $education_label;
		
		$obj->course_id = $course_id;
		$obj->career = $career;
		$obj->area_of_interest = $areaofinterest;
		//$obj->company = $company;
		$obj->is_active = $is_active;
		$obj->expiry_date = $expiry_date;
		$obj->default_batch_id = $default_batch_id;
		$obj->loginid = $loginid;
		$obj->userType = $userType;
		$obj->englishexp = $englishexp; 
		$obj->roll_no = $roll_no; 
		$obj->fathers_name = $fathers_name; 
		$obj->mothers_name = $mothers_name; 
		$obj->section = $section; 
		$obj->slot = $slot; 
        //echo "<pre>";print_r($obj);exit;
		return $obj;
	}
	
//============= Get center admin detail by  user id and role id 
  public function getCenterAdminDataByID($uid,$roleID){

		$sql = "SELECT uld.user_id FROM user_center_map ucm join user_role_map uld ON ucm.user_id = uld.user_id "
                . " WHERE  uld.user_id = :user_id AND uld.role_definition_id = :roleID AND ucm.center_id = :center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
		$stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$user_id=$RESULT[0]['user_id'];
		

		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
		$first_name=$RESULT[0]['first_name'];
		$email_id=$RESULT[0]['email_id'];
		$qualification=$RESULT[0]['education'];
		$business_unit=$RESULT[0]['business_unit'];
		$profile_id=$RESULT[0]['profile_pic'];
        //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$password=$RESULT[0]['password'];
		$is_active=$RESULT[0]['is_active'];
		$loginid=$RESULT[0]['loginid'];
	    //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$phone=$RESULT[0]['phone'];
		
		$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$profileName=$RESULT[0]['display_name']; 
		$profilePath=$RESULT[0]['path']; 
		$system_name=$RESULT[0]['system_name']; 
		/* $sql = "SELECT * FROM tblx_user_company WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$company=$RESULT[0]['company_id']; */
		
		$obj = new stdclass();
		$obj->first_name = $first_name;
		$obj->email_id = $email_id;
		$obj->qualification = $qualification;
		$obj->business_unit = $business_unit;
		
		$obj->password = $password;
		$obj->phone = $phone;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		//$obj->company = $company;
		$obj->is_active = $is_active;
		$obj->expiry_date = $expiry_date;
		$obj->default_batch_id = $default_batch_id;  
		$obj->loginid = $loginid;
        //echo "<pre>";print_r($obj);exit;
		return $obj;
	}
//============= Get center admin detail by  user id and role id 
  public function getAdminDataByID($uid,$roleID){

		
		$user_id=trim($uid);
		
		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
		$first_name=$RESULT[0]['first_name'];
		$email_id=$RESULT[0]['email_id'];
		$profile_id=$RESULT[0]['profile_pic'];
        //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$password=$RESULT[0]['password'];
		$loginid=$RESULT[0]['loginid'];
		
	    //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$phone=$RESULT[0]['phone'];
		
		$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$profileName=$RESULT[0]['display_name']; 
		$profilePath=$RESULT[0]['path']; 
		$system_name=$RESULT[0]['system_name']; 
		//$profile_pic=$profilePath.'/'.$profileName;
		/* $sql = "SELECT * FROM tblx_user_company WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$company=$RESULT[0]['company_id']; */
		
		$obj = new stdclass();
		$obj->first_name = $first_name;
		$obj->email_id = $email_id;
		$obj->password = $password;
		$obj->phone = $phone;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		$obj->loginid = $loginid;
		//$obj->company = $company;
		
        //echo "<pre>";print_r($obj);exit;
		return $obj;
	}
	
	public function updateCenterAdmin($dataArr){
      //echo "<pre>";print_r($dataArr);exit;		 
     	$name =$dataArr->name;
		$phone =$dataArr->mobile;
		$qualification =$dataArr->qualification;
		$business_unit =$dataArr->business_unit;
		$user_id=$dataArr->userIdVal;
		$profile_id=$dataArr->profile_id;
		$fileImgNamePro=$dataArr->fileImgNamePro;
		$center_id=$dataArr->center_id;
		$password=$dataArr->fld_password;
	try{
		
		 /* $sql = "UPDATE tblx_center SET  name = '$dataArr->name', mobile = '$dataArr->user_mobile', password='$dataArr->password', modified_date = NOW() where center_id = :center_id";
		  $stmt = $this->dbConn->prepare($sql);	
          $stmt->bindValue('center_id', $center_id, PDO::PARAM_INT);		  
		  $stmt->execute();
		  $stmt->closeCursor(); */
		//Update user
		$sql = "UPDATE user SET  first_name = '$name',  education='$qualification',business_unit='$business_unit', modified_date = NOW()  where user_id = :user_id";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);		  
		$stmt->execute();
		$stmt->closeCursor();
		
		//// for phone get address id by user id
		$stmt = $this->dbConn->prepare("Select address_id,`modified_date`= Now() from user  WHERE `user_id`=:user_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
		$stmt->closeCursor();
		$address_id = $RESULT[0]['address_id'];
		
		//// update profile pic  
		$stmt= $this->dbConn->prepare("UPDATE `asset` SET `system_name`='$fileImgNamePro' ,`modified_date`=NOW() WHERE `asset_id`= :profile_id");
	   //echo "<pre>";print_r($stmt);exit;
	    $stmt->bindValue(':profile_id', $profile_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor(); 
		
		//// update phone in address master
		$stmt = $this->dbConn->prepare("UPDATE `address_master` SET `phone`= '$phone'  WHERE `address_id`=$address_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();

		//// Update Password
		if($password!=''){
		   $sql = "UPDATE user_credential SET password='$password', modified_date = NOW()  where user_id = :user_id";
		   $stmt = $this->dbConn->prepare($sql);
		    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		   $stmt->execute();
		   $stmt->closeCursor(); 
		}
		
        return true;
	 }//catch exception
	    catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
		}
        
  } 
	 public function updateAdmin($dataArr){

		$name =$dataArr->name;
		$phone =$dataArr->mobile;
		$user_id=$dataArr->userIdVal;
		$profile_id=$dataArr->profile_id;
		$fileImgNamePro=$dataArr->fileImgNamePro;
		$password=$dataArr->fld_password;
		
		//Update user
		$sql = "UPDATE user SET  first_name = '$name'  where user_id = '$user_id'";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);		  
		$stmt->execute();
		$stmt->closeCursor();
		
		//// for phone get address id by user id
		$stmt = $this->dbConn->prepare("Select address_id,`modified_date`= Now() from user  WHERE `user_id`=$user_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
		$stmt->closeCursor();
		$address_id = $RESULT[0]['address_id'];
		
		//// update profile pic  
		$stmt= $this->dbConn->prepare("UPDATE `asset` SET `system_name`='$fileImgNamePro' ,`modified_date`=NOW() WHERE `asset_id`=$profile_id");
	   //echo "<pre>";print_r($stmt);exit;
		$stmt->execute();
		$stmt->closeCursor(); 
		
		//// update phone in address master
		$stmt = $this->dbConn->prepare("UPDATE `address_master` SET `phone`= '$phone'  WHERE `address_id`=$address_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();
		
		if($password!=''){
		//// Update Password
		   $sql = "UPDATE user_credential SET password='$password', modified_date = NOW()  where user_id = :user_id";
		   $stmt = $this->dbConn->prepare($sql);	
		   $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		   $stmt->execute();
		  $stmt->closeCursor(); 
		}

        return true;
		
        
  } 
   public function updateCenterTrainer($dataArr){
      //echo "<pre>";print_r($dataArr);exit;
        $name =$dataArr->name;
		$phone =$dataArr->mobile;	  
		$qualification =$dataArr->qualification;
		$business_unit =$dataArr->business_unit;
		$user_id=$dataArr->userIdVal;
		$profile_id=$dataArr->profile_id;
		$fileImgNamePro=$dataArr->fileImgNamePro;
		$center_id=$dataArr->center_id;
		$password=$dataArr->fld_password;
		
		try{
		
		//Update user
		$sql = "UPDATE user SET  first_name = '$name',  education='$qualification',business_unit='$business_unit', modified_date = NOW()  where user_id = :user_id";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);		  
		$stmt->execute();
		$stmt->closeCursor();
		
		//// for phone get address id by user id
		$stmt = $this->dbConn->prepare("Select address_id,`modified_date`= Now() from user  WHERE `user_id`=:user_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
		$stmt->closeCursor();
		$address_id = $RESULT[0]['address_id'];
		
		//// update profile pic  
		$stmt= $this->dbConn->prepare("UPDATE `asset` SET `system_name`='$fileImgNamePro' ,`modified_date`=NOW() WHERE `asset_id`=$profile_id");
	   //echo "<pre>";print_r($stmt);exit;
		$stmt->execute();
		$stmt->closeCursor(); 
		
		//// update phone in address master
		$stmt = $this->dbConn->prepare("UPDATE `address_master` SET `phone`= '$phone'  WHERE `address_id`=$address_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();

		//// Update Password
	   /*  $sql = "UPDATE user_credential SET password='$password', modified_date = NOW()  where user_id = '$user_id'";
	   $stmt = $this->dbConn->prepare($sql);		  
	   $stmt->execute();
	   $stmt->closeCursor();  */
		
        return true;
	 }//catch exception
	    catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
		}
        
  } 
  
	public function getSignedUpUserCount(){
		$sql="Select count(DISTINCT u1.user_id) as 'teacherReg' FROM user u1 JOIN user_credential uc1 ON u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region_country_map trcm ON tc.country=trcm.country_name where 1=1 AND urm.role_definition_id='1' AND uc1.is_active ='1'";
		if(isset($_SESSION['center_id'])){
			$sql .=" AND ubm.center_id = ".$_SESSION['center_id'];
		}
		
		//$sql = "SELECT count(*) teacherReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id join tblx_batch_user_map tbum ON ucm.user_id =  tbum.user_id and uld.role_definition_id =1  and uc.is_active =1 AND ucm.center_id = ".$_SESSION['center_id'];
		//SELECT COUNT(DISTINCT(user_role_map_id)) teacherReg FROM  user_role_map  uld join user_center_map ucm ON uld.user_id =  ucm.user_id"
       //         . " WHERE  uld.role_definition_id =1 AND ucm.center_id = ".$_SESSION['center_id'];
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$tRESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if($tRESULT[0]['teacherReg']){
			$teacher = $tRESULT[0]['teacherReg'];
		}else{
	    	$teacher = 0;
		}
        $stmt->closeCursor();
		//echo "<pre>";print_r($tRESULT[0]['teacherReg']);exit;
		$sql="Select count(DISTINCT u1.user_id) as 'studentReg' FROM user u1 JOIN user_credential uc1 ON u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region_country_map trcm ON tc.country=trcm.country_name where 1=1 AND urm.role_definition_id='2' AND uc1.is_active ='1'";
		//echo $sql="SELECT count(*) studentReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id join tblx_batch_user_map tbum ON ucm.user_id =  tbum.user_id and uld.role_definition_id =2  and uc.is_active =1 AND ucm.center_id = ".$_SESSION['center_id'];
		if(isset($_SESSION['center_id'])){
			$sql .=" AND ubm.center_id = ".$_SESSION['center_id'];
		}
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();																		
		$sRESULT =$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($sRESULT[0]['studentReg']){
			$student = $sRESULT[0]['studentReg'];
		}else{
			$student = 0;
		}
		$stmt->closeCursor();
		
		
		$obj = new stdClass();
		
		$obj->teacher = $teacher;
		$obj->student = $student;
		
		return $obj; 
	}
	public function getSignedUpUserCountByCenter($center_id){
		
		//echo "<pre>";print_r($tRESULT[0]['teacherReg']);exit;
		$sql="Select count(DISTINCT u1.user_id) as 'studentReg' FROM user u1 JOIN user_credential uc1 ON u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region_country_map trcm ON tc.country=trcm.country_name where 1=1 AND urm.role_definition_id='2' AND uc1.is_active ='1' AND ubm.center_id = ".$center_id;
		//echo $sql="SELECT count(*) studentReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id join tblx_batch_user_map tbum ON ucm.user_id =  tbum.user_id and uld.role_definition_id =2  and uc.is_active =1 AND ucm.center_id = ".$_SESSION['center_id'];
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();																		
		$sRESULT =$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($sRESULT[0]['studentReg']){
			$student = $sRESULT[0]['studentReg'];
		}else{
			$student = 0;
		}
		$stmt->closeCursor();

		
		return $student; 
	}
			//============= bulk uplaod xml
	public function bulkDataInsert(array $request){
		global $region_id;
		//_dd($region_id);
		$roleID = 2;
		$centerDetails = $this->getCenterDetails();
		$center_id = $centerDetails[0]['center_id'];
		$clientDetails = $this->getClientDetails();
		$client_id = $clientDetails[0]['client_id'];
		
		$first_name = isset($request['first_name']) ? $request['first_name'] : "";
		$last_name = isset($request['last_name']) ? $request['last_name'] : "";
        $email_id = isset($request['email_id']) ? trim($request['email_id']) : "";
        $is_email_verified = isset($request['is_email_verified']) ? trim($request['is_email_verified']) : "";
        $phone = isset($request['mobile']) ? trim($request['mobile']) : "";
        $is_phone_verified = isset($request['is_phone_verified']) ? trim($request['is_phone_verified']) : "";
		$password = isset($request['password']) ? trim($request['password']) : "";
		$batch = isset($request['batch']) ? trim($request['batch']) : "";
		$center_id = isset($request['center_id']) ? trim($request['center_id']) : $center_id;
		$client_id = isset($_SESSION['client_id']) ? trim($_SESSION['client_id']) : $client_id;
		$country_id = isset($request['country']) ? trim($request['country']) : "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		$roll_no = isset($request['roll_no']) ? trim($request['roll_no']) : "";
		$fathers_name = isset($request['fathers_name']) ? trim($request['fathers_name']) : "";
		$mothers_name = isset($request['mothers_name']) ? trim($request['mothers_name']) : "";
		$section = isset($request['section']) ? trim($request['section']) : "";
		$slot = isset($request['slot']) ? trim($request['slot']) : "";
		
		/* $gender = isset($request['gender']) ? trim($request['gender']) : "";
		$age_id = isset($request['age_id']) ? trim($request['age_id']) : "";
		$marital_id = isset($request['marital_id']) ? trim($request['marital_id']) : "";
		$country_id = isset($request['country_id']) ? trim($request['country_id']) : "";
		$state_id = isset($request['state_id']) ? trim($request['state_id']) : "";
		$city_id = isset($request['city_id']) ? trim($request['city_id']) : "";
		$pincode = isset($request['pincode']) ? trim($request['pincode']) : "";
		$nationality = isset($request['nationality']) ? trim($request['nationality']) : "";
		$mother_tongue_id = isset($request['mother_tongue_id']) ? trim($request['mother_tongue_id']) : "";
		$education_id = isset($request['education_id']) ? trim($request['education_id']) : "";
		$emp_status_id = isset($request['emp_status_id']) ? trim($request['emp_status_id']) : "";
		$perpose_join_id = isset($request['perpose_join_id']) ? trim($request['perpose_join_id']) : "";
		$discover_app_id = isset($request['discover_app_id']) ? trim($request['discover_app_id']) : ""; */
		
		
         $resEmail = $this->checkEmailExits($email_id);
		if(isset($resEmail['user_id']) && $resEmail['user_id']!='' && $resEmail['loginid']!=''){
		
		  return false;
			
		}else{
			//// Now Adding  user address 
			$stmt = $this->dbConn->prepare("INSERT INTO address_master(country, phone,is_phone_verified,updated_by,created_date) VALUES(:country,:phone,:is_phone_verified,".$_SESSION['user_id'].",NOW())");
			$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
			$stmt->bindValue(':is_phone_verified', $is_phone_verified, PDO::PARAM_STR);
			$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
			$stmt->execute();
			$address_id =$this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
		  //  echo "<pre>";print_r($address_id);exit;
			 
			  //// Now Adding  Assest 
			$stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
			$stmt->execute();
			$asset_id = $this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
			// echo "<pre>";print_r($asset_id);exit;
			
		 //// Adding user 
		 if ($region_id==5){
			 $qry = "insert into user(first_name,last_name,email_id,is_email_verified,address_id,profile_pic,mother_tongue,updated_by,created_date,user_client_id,roll_no,fathers_name,mothers_name,section,slot) values(:first_name,:last_name,:email_id,:is_email_verified,:address_id,:asset_id,:mother_tongue,".$_SESSION['user_id'].", NOW(), :client_id, :roll_no, :fathers_name, :mothers_name, :section, :slot)";
		 }else{
			 $qry = "insert into user(first_name,last_name,email_id,is_email_verified,address_id,profile_pic,mother_tongue,updated_by,created_date,user_client_id) values(:first_name,:last_name,:email_id,:is_email_verified,:address_id,:asset_id,:mother_tongue,".$_SESSION['user_id'].", NOW(), :client_id)";
		 }
		 
			$stmt= $this->dbConn->prepare($qry);
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
			$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
			$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
			$stmt->bindValue(':is_email_verified', $is_email_verified, PDO::PARAM_INT);
			$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
			$stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
			$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);	
			$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			if ($region_id==5){
			$stmt->bindValue(':roll_no', $roll_no, PDO::PARAM_STR);
			$stmt->bindValue(':fathers_name', $fathers_name, PDO::PARAM_STR);
			$stmt->bindValue(':mothers_name', $mothers_name, PDO::PARAM_STR);
			$stmt->bindValue(':section', $section, PDO::PARAM_STR);
			$stmt->bindValue(':slot', $slot, PDO::PARAM_STR);
			}
			$stmt->execute();
			$user_id =$this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
			// echo "<pre>";print_r($user_id);exit;
			
			
			//// Adding user and center map 
			$stmt = $this->dbConn->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values(:user_id,:center_id,:client_id,NOW())");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
			
			//// Adding Admin Credentials 
			$stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:email_id,:password,1,NOW())");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
			$stmt->bindValue(':password', $password, PDO::PARAM_STR);
			
			$stmt->execute();
			$stmt->closeCursor(); 
			
			////Select the client to user group id */
			$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id='$client_id'");
				//echo "<pre>";print_r($stmt);exit;
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			//echo "<pre>";print_r($RESULT[0]['user_group_id']);exit;
			$client_group_id = $RESULT[0]['user_group_id'];
			$role_type="2";//student/learner
			
			//// Adding user into role map group 
			$stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(:user_id,:role_type,:client_group_id,1,1,NOW())");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':role_type', $role_type, PDO::PARAM_INT);
			$stmt->bindValue(':client_group_id', $client_group_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
			
			// For batch_user_map table insert
					
				$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id,status) values (:userID, :batchID, :center_id,1) ";
				
				$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
				$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
				$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
				$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				//echo "<pre>";print_r($btUserMap_sql);exit;
				$btUserMap->execute();
				$btUserMap->closeCursor();
			   // echo "<pre>";print_r($btUserMap);exit;
				return true;
         }
	}

	 //============= Get Address deatils  by user if 
	public function getAddressDetail($user_id){
	  
	  $sql = "SELECT * FROM user WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
	  
		$sql = "SELECT * FROM address_master WHERE address_id = :address_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':address_id',$address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$phone=$RESULT[0]['phone'];
        return $phone;
			
			
	}
  //============= Get Company methods
	 public function getAllCompany(){
		$centerDetails = $this->getCenterDetails();
        $sql = "Select * from  tblx_company";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor(); 
		return $RESULT;	
      
    }	
	// Get max registration limit
	public function getUserLimit($centerId,$userRole){
		$sql = "select count('*') as cnt from user_center_map INNER JOIN user_role_map ON user_center_map.user_id=user_role_map.user_id and user_center_map.center_id=:center_id and user_role_map.role_definition_id=:role_definition_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id',$centerId, PDO::PARAM_INT);
		$stmt->bindValue(':role_definition_id',$userRole, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$cnt=$RESULT[0]['cnt'];
        return $cnt;
	}
	
	//============= Get Company methods
	 public function getCompanyByUserId($user_id){
		 $centerDetails = $this->getCenterDetails();
		 $centerName = $centerDetails[0]['name'];
        $sql = "Select c.company_name  from  tblx_company c INNER JOIN tblx_user_company tc on c.id=tc.company_id where user_id=:user_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id',$user_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor(); 
		$company=$RESULT[0]['company_name'];
		
		$obj = new stdClass();
		
		$obj->company = $company;
		$obj->centerName = $centerName;
		
		return $obj; 

      
    }	
	
	// Get test report
	public function getTestsReport($roleID=2, $batchID){
	  
	   $sql = "SELECT uld.* FROM user_role_map uld	JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batchID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$userArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($userArr,$row);
		}
        return $userArr;
	
	}
	
	//Get Test Attempt
	public function getTestAttempt($test_id,$userids){

		$sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT user_id FROM `temp_ans_push` WHERE test_id =:test_id and user_id IN($userids)) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':test_id', $test_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
	
	//Get Batch Course map
	public function getBatchCourseMapDetails($batch_id,$courseid){

		$sql = "SELECT COUNT(*) as 'cnt' FROM tblx_batch_course_map WHERE center_id=:center_id and batch_id=:batch_id and course_id=:course_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->bindValue(':course_id', $courseid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
	
	
	//=============  Update Batch Course Map
	
	public function updateBatchCourseMap($batch_id,$courseid){
		
		$batchCourseMapDetails = $this->getBatchCourseMapDetails($batch_id,$courseid);
		if($batchCourseMapDetails[0]['cnt']>0)
		{
			$sql = "UPDATE tblx_batch_course_map SET date_created=NOW() WHERE center_id=:center_id AND batch_id = :batch_id AND course_id=:course_id";

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':course_id', $courseid, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();  
		}
		else{
			$sql = "INSERT INTO tblx_batch_course_map (center_id,batch_id,course_id,date_created) values (:center_id, :batch_id, :course_id,NOW())";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':course_id', $courseid, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
		}
		return true;	
		 
	}
	
	//Get Batch Course map data
	public function getBatchCourseMapList($batch_id){

		$sql = "SELECT course_id FROM tblx_batch_course_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$courseArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($courseArr,$row['course_id']);
		}
        return $courseArr;
	
	}
	//Delete Batch Course map data
	public function deleteBatchCourseMapDetails($batch_id){

		$sql = "DELETE  FROM tblx_batch_course_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
	//Check Availlable License for Customer
	public function availLicense(){

		$sql = "SELECT COUNT(*) as 'cnt' FROM `tbl_client_licenses` WHERE lic_req_client_id =:lic_req_client_id and issued_to_customer=0";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':lic_req_client_id', 90, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;	
	
	}
	//Save License for Customer
	public function saveLicenseIssue($noOfLicense,$customer){
		$licenseArr=$this->getLicenseToIssue($noOfLicense);
		$date=date('Y-m-d H:i:s');
		foreach($licenseArr as $key=>$val){
		$license_id =$val[1];
		$lic_req_id =$val[0];
		
		 $sql = "UPDATE `tbl_client_licenses` SET issued_to_customer=:issued_to_customer,issued_date=:issued_date WHERE lic_req_id =:lic_req_id and license_id=:license_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':issued_to_customer',$customer, PDO::PARAM_INT);
        $stmt->bindValue(':issued_date',$date, PDO::PARAM_STR);
        $stmt->bindValue(':lic_req_id',$lic_req_id, PDO::PARAM_INT);
        $stmt->bindValue(':license_id',$license_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor(); 
		}
		
		return true;
	
	}
	
	
	
	//Get Un Used License for Customer
	public function getLicenseToIssue($noOfLicense){

		 $sql = "SELECT * FROM tbl_client_licenses WHERE lic_req_client_id =:lic_req_client_id and issued_to_customer=0 LIMIT $noOfLicense";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':lic_req_client_id', 90, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$licenseArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($licenseArr,array($row['lic_req_id'],$row['license_id']));
		}
        return $licenseArr;
	}
	//Save request License
	public function saveLicenserequest($noOfLicense,$customer,$expiryDate,$expDay,$no_of_trainer,$no_of_learner,$licenseType){

		if($expiryDate!=""){
			$expiryDate=date('Y-m-d H:i:s',strtotime($expiryDate));
		}

		 $sql = "INSERT INTO tblx_request_license (noOfLicense,customer_id,expiry_date,expiry_day,no_of_trainers,no_of_learners,requested_date,license_type,lic_req_by_user) values (:noOfLicense,:customer_id, :expiry_date, :expiry_day,:no_of_trainer,:no_of_learner,NOW(),:licenseType,:lic_req_by_user)";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':noOfLicense', $noOfLicense, PDO::PARAM_INT);
			$stmt->bindValue(':customer_id', $customer, PDO::PARAM_INT);
			$stmt->bindValue(':expiry_date', $expiryDate);
			$stmt->bindValue(':expiry_day', $expDay, PDO::PARAM_INT);
			$stmt->bindValue(':no_of_trainer', $no_of_trainer, PDO::PARAM_INT);
			$stmt->bindValue(':no_of_learner', $no_of_learner, PDO::PARAM_INT);
			$stmt->bindValue(':licenseType', $licenseType, PDO::PARAM_STR);
			$stmt->bindValue(':lic_req_by_user', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
	  return true;
	}
	//request License detail by client
	public function getReqLicenseDetails($client_id){
		 	
			$whr = "";
			if($_SESSION['role_id']==7){
				$whr = " AND lic_req_by_user = '".$_SESSION['user_id']."'"; 
			}
			
			$sql = "SELECT * FROM tblx_request_license where customer_id=:client_id $whr order by id DESC";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			 //echo "<pre>";print_r($RESULT);exit;
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		
    }
	
	//Get total test Attempted
	public function getTestAttempted(){
		
		/*  $sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.test_id FROM `temp_ans_push` INNER JOIN user on temp_ans_push.user_id = user.user_id and user.user_client_id=:user_client_id and (temp_ans_push.battery_id='' or temp_ans_push.battery_id IS NULL)) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_client_id', $_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0]; */
		$sql = "SELECT COUNT(*) as 'cnt' FROM  tbl_test_complete_status ttcs  INNER JOIN  user u ON u.user_id = ttcs.user_id where u.user_client_id=:user_client_id and ttcs.status='1' and (ttcs.battery_id='' or ttcs.battery_id IS NULL)";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_client_id',$_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$cnt1= $RESULT[0]['cnt'];	
		
		
		$cnt2=0;
		
		$sql = "SELECT ttcs.user_id,ttcs.battery_id from tbl_test_complete_status ttcs  INNER JOIN  user u ON u.user_id = ttcs.user_id where u.user_client_id=:user_client_id and ttcs.status='1' and (ttcs.battery_id!='' or ttcs.battery_id IS NOT NULL) group by battery_id,user_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_client_id', $_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		while($row = array_shift( $RESULT )) {
			$status=getBatteryCompleteStatus($row['user_id'],$row['battery_id']);
			if($status==1){
				$cnt2++;
			}
		}
		
		
		
		
			
		$cnt= $cnt1+$cnt2;	
		return $cnt;
		
		/* $sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.test_id FROM `temp_ans_push` INNER JOIN user on temp_ans_push.user_id = user.user_id and user.user_client_id=:user_client_id and (temp_ans_push.battery_id='' or temp_ans_push.battery_id IS NULL)) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_client_id', $_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$cnt1= $RESULT[0]['cnt'];
		
		
		 $sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.battery_id FROM `temp_ans_push` INNER JOIN user on temp_ans_push.user_id = user.user_id and user.user_client_id=:user_client_id and (temp_ans_push.battery_id!='' and temp_ans_push.battery_id IS NOT NULL)) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':user_client_id', $_SESSION['client_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $cnt2= $RESULT[0]['cnt'];
         
		 $cnt=$cnt1+$cnt2;
			//echo "<pre>";print_r($cnt);exit;
		return $cnt; */	
	}
	//============= Get User Test Count 
	public function getUserTestAttemptedCountById($user_id){

		$sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id,temp_ans_push.test_id FROM `temp_ans_push` where temp_ans_push.user_id=:user_id  ) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$cnt1= $RESULT[0]['cnt'];	
		
		 $sql = "SELECT COUNT(*) as 'cnt' FROM(SELECT DISTINCT temp_ans_push.user_id FROM `temp_ans_push` where temp_ans_push.user_id=:user_id ) AS DerivedTableAlias";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $cnt2= $RESULT[0]['cnt'];	
		return $cnt1+$cnt2;	

    }
	
	public function getCourseListClientBy($group_client){
		if($courseType != ''){
			$sql = "SELECT * FROM course WHERE client_id = :group_client";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':group_client', $group_client, PDO::PARAM_INT);
		}else{
			$sql = "SELECT * FROM course";
			$stmt = $this->dbConn->prepare($sql);
		}
        
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	}
	
	public function getCourseListByLevel($courseType,$group_client,$product_standard_id=null){
		    $whr='';
			if($product_standard_id!=''){
			  $whr.= 'product_id IN('.$product_standard_id.')';			  
			}
		  
		    $sql = "SELECT c.code, c.title, c.description,c.course_type,c.product_id, c.level_id,c.thumnailImg,c.sequence_id, gmt.edge_id FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE ".$whr." ";
         // echo "<pre>";print_r($sql);//exit;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$cList = array();
			while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->title = $row['title'];
				$bcm->course_code = $row['code'];
				$bcm->course_id = str_replace("CRS-","",$row['code']);
				$bcm->description = $row['description'];
				$bcm->edge_id = $row['edge_id'];
				$bcm->course_type = $row['course_type'];
				$bcm->product_id = $row['product_id'];
				$bcm->level_id = $row['level_id'];
				$bcm->thumnailImg = $row['thumnailImg'];
				$bcm->sequence_id = $row['sequence_id'];
				array_push($cList,$bcm);
		   }
		 //echo "<pre>";print_r($cList);//exit;
		 $courseArr= array();
		foreach($cList as $key => $value){
			$stmt = $this->dbConn->prepare("select st.standard, slm.level_text,slm.level_description,slm.level_cefr_map from tblx_standards st, tblx_standards_levels slm, course c where c.standard_id=st.id and c.level_id=slm.id and c.code='".$value->course_code."'");
			
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//$RESULT1[0]
			 if($value->thumnailImg!=""){
				$crsImagetemp=$this->thumnail_Img_url.$value->thumnailImg;
			}else{
				$crsImagetemp=$this->img_url.$value->course_code.".png";
			} 
			 while($row = array_shift( $RESULT1 )) {
				$bcm = new stdClass();
				$bcm->percentage = 0;
				$bcm->edge_id = $value->edge_id;
				$bcm->course_type= $value->course_type;
				$bcm->product_id= $value->product_id;
				$bcm->name = $value->title;
				$bcm->desc = $value->description;
				$bcm->course_code = $value->course_code;
				$bcm->course_id = $value->course_id;
				$bcm->sequence_id = $value->sequence_id;
				$bcm->level_id = $value->level_id;
				$bcm->imgPath = $crsImagetemp;
				$bcm->standard = $row['standard'];
				$bcm->level_text = $row['level_text'];
				$bcm->level_description = $row['level_description'];
				$bcm->level_cefr_map = $row['level_cefr_map'];
				array_push($courseArr,$bcm);
			 }
			$stmt->closeCursor();
			//echo "<pre>";print_r($bcm);//exit;	
		
	}
     //echo "<pre>";print_r($courseArr);exit;
		return $courseArr;
}


	/* public function getCourseListByLevel($courseType,$standardId,$group_client){
		if($courseType != ''){
			$sql = "SELECT * FROM course WHERE course_type = :courseType AND standard_id  = :standardId AND client_id = :group_client";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':courseType', $courseType, PDO::PARAM_INT);
			$stmt->bindValue(':standardId', $standardId, PDO::PARAM_INT);
			$stmt->bindValue(':group_client', $group_client, PDO::PARAM_INT);
		}else{
			$sql = "SELECT * FROM course";
			$stmt = $this->dbConn->prepare($sql);
		}
        
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	} */

	/*  public function getCourseListByBatchID($batch_id,$center_id){
		$sql = "SELECT c.* FROM course c JOIN  tblx_batch_course_map bcm ON bcm.course_i = c.code WHERE bcm.batch_id = :batchID AND bcm.center_id = :centerID AND c.client_id=:clientID";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':centerID', $center_id, PDO::PARAM_INT);
		$stmt->bindValue(':clientID', '2', PDO::PARAM_INT);
	    $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	}  */
	public function checkAccessCodeValid($accessCode,$client_id){
		 try{
			$sql = "SELECT * FROM tbl_access_codes  WHERE access_code = :accessCode AND client_id=:clientID";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':accessCode', $accessCode, PDO::PARAM_STR);
			$stmt->bindValue(':clientID', $client_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT[0];
				}else{
					return false;
			}	
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
   }
	
	 public function updateUsedAccessCode( array $request,$radeemCenter){
		   //echo "<pre>";print_r($request);exit;
		 $radeemAccess1=filter_string($request['radeemAccess']);
		$radeemAccess = isset($radeemAccess1) ? trim($radeemAccess1) : "";
		$radeemCenter = isset($radeemCenter) ? trim($radeemCenter) : "";
		$cCenter = isset($request['cCenter']) ? trim($request['cCenter']) : "";
		$cBatch = isset($request['cBatch']) ? trim($request['cBatch']) : "";
		$user_id = isset($request['userId'])? $request['userId'] : '';		
		$userName = isset($request['uName']) ? $request['uName'] : "";
        $email = isset($request['cemail']) ? trim($request['cemail']) : ""; 
		$user_from = isset($request['user_from']) ? trim($request['user_from']) : ""; 
		  try{

				$batch = '1'; 
				//if($cCenter!=$radeemCenter){
                       $stmt = $this->dbConn->prepare("UPDATE tbl_access_codes SET access_code_status='1',code_used_by_id=:userID,code_used_by_name='$userName',code_used_by_email='$email',code_used_by_b2c='yes',code_used_date=NOW() WHERE organization_id=:center_id AND access_code_status='0' AND access_code=:accessCode");
						$stmt->bindValue(':userID', $user_id, PDO::PARAM_STR);
						$stmt->bindValue(':accessCode', $radeemAccess, PDO::PARAM_STR);
						$stmt->bindValue(':center_id', $radeemCenter, PDO::PARAM_INT);
						$stmt->execute();
						$stmt->closeCursor();
					
						$sql = "insert into tblx_user_center_batch_migrate(user_id,current_center,move_center,current_batch,move_batch,updated_by,updated_date) values('$user_id','$cCenter','$radeemCenter','$cBatch','$batch',".$_SESSION['user_id'].", NOW())";
						$stmt = $this->dbConn->prepare( $sql );
						$stmt->execute();
						$stmt->closeCursor(); 

						//// Adding user and center map 
						$stmt1 = $this->dbConn->prepare("update user_center_map set center_id= '$radeemCenter' WHERE user_id=$user_id");
						$stmt1->execute();
						$stmt1->closeCursor(); 
						
		
						$stmt = $this->dbConn->prepare("update user set user_from= '$user_from' WHERE user_id=$user_id");
						$stmt->execute();
						$stmt->closeCursor(); 

						$stmt = $this->dbConn->prepare("update user_credential set is_active= '1' , expiry_date=NULL WHERE user_id=$user_id");
						$stmt->execute();
						$stmt->closeCursor(); 								
							
						$btUserMap_sql = "Update tblx_batch_user_map set batch_id =:batchID,center_id=:center_id WHERE user_id=:userID AND status ='1'";
						$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
						$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
						$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
						$btUserMap->bindValue(':center_id', $radeemCenter, PDO::PARAM_INT);
						$btUserMap->execute();
						$btUserMap->closeCursor();
						return true;
				//}	
					
			 }//catch exception
			  catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			} 
	 } 
  public function checkApproveUser($user_id,$center_id){ 
		 try{
			$sql = "SELECT * FROM user_credential uc join  tbl_access_codes tcc on tcc.code_used_by_id= uc.user_id WHERE code_used_by_id = :userId AND organization_id=:organization_id order BY code_used_date desc LIMIT 1 ";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':userId', $user_id, PDO::PARAM_STR);
			$stmt->bindValue(':organization_id', $center_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT[0];
				}else{
					return false;
			}	
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
   }
   
//============= Create batch details methods
   
	 public function createCenterBatchdetails($bid,$batchName,$batchType,$centerId,$lmode,$levellist,$modulelist,$chapterlist){
		 //echo "<pre>";print_r($batchType);exit;
		$cCode = 'CN-'.$centerId;
		$bcode = $cCode;
		 
		$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, type, is_enabled) VALUES('batch','$centerId', '$bid','$bcode','Level','$levellist')";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor(); 
			$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, type, is_enabled) VALUES('batch','$centerId', '$bid','$bcode','Topic','$modulelist')";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor(); 
			$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, type, is_enabled) VALUES('batch','$centerId', '$bid','$bcode','Chapter','$chapterlist')";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor(); 
			return true;
    }
   



//=============  Update batch details ankesh
	
	public function updateBatchDataByDetails($batch_id,$centerId,$levellist,$modulelist,$chapterlist){
		 
		try{
			
			$cCode = 'CN-'.$centerId;
			$bcode = $cCode;
			$sql = "Select * from tblx_product_configuration WHERE batch_id = $batch_id AND institute_id=$centerId";
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if(count($RESULT) > 0 ){
				$sql = "UPDATE tblx_product_configuration SET is_enabled='$levellist' WHERE type='LEVEL' AND batch_id = :batchID AND institute_id=:center_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				$sql = "UPDATE tblx_product_configuration SET is_enabled='$modulelist' WHERE type='TOPIC' AND batch_id = :batchID AND institute_id=:center_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				$sql = "UPDATE tblx_product_configuration SET is_enabled='$chapterlist' WHERE type='CHAPTER' AND batch_id = :batchID AND institute_id=:center_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				 return true;
			}
			else{
				$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, type, is_enabled) VALUES('batch','$centerId', '$batch_id','$bcode','Level','$levellist')";
				
				$stmt = $this->dbConn->prepare($sql);
				$stmt->execute();
				$stmt->closeCursor(); 
				$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, type, is_enabled) VALUES('batch','$centerId', '$batch_id','$bcode','Topic','$modulelist')";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->execute();
				$stmt->closeCursor(); 
				$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, type, is_enabled) VALUES('batch','$centerId', '$batch_id','$bcode','Chapter','$chapterlist')";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->execute();
				$stmt->closeCursor(); 
				return true;
			}	
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}	
	
	public function getBatchDataByIDDetails($batch_id,$centerId){
		try{ 
			$sql = "Select * from tblx_product_configuration WHERE batch_id = :batchID AND institute_id = :centerID";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':centerID', $centerId, PDO::PARAM_INT);
			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
   	//=============  Get batch data  methods by batch Id



   	public function getAllowedExtensions($type)
   	{
   		$sql = "SELECT * from tblx_allow_extensions WHERE file_type = :type";
		$stmt = $this->dbConn->prepare($sql);
		 $stmt->bindValue(':type', $type, PDO::PARAM_STR);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		$exts = [];
		if(count($RESULT) > 0 ){
			foreach ($RESULT as $row) {
				$exts[] = $row['extension'];
			}
			
		}
		return $exts;
   	}

   	public function getNotAllowedExtensions($type)
    {
    	$sql = "SELECT * from tblx_allow_extensions WHERE file_type != :type";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':type', $type, PDO::PARAM_STR);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		$exts = [];
		if(count($RESULT) > 0 ){
			foreach ($RESULT as $row) {
				$exts[] = $row['extension'];
			}
			
		}
		return $exts;
    }
	public function getLogoById($id){
		$sql = "SELECT tr.* FROM tblx_region tr where tr.id=:id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $Result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($Result) > 0 ){
			return $Result;
		}else{
			return false;
		}		
    }

   //=============  Get batch data  methods by batch Id
   	public function getCustomCourseList($courseType,$course_list,$product_standard_id){
		
		if($courseType != ''){
			//echo $course_list;exit;
			 $whr='';
			if($product_standard_id!=''){
			  //$whr.= ' and standard_id IN('.$product_standard_id.')';
			  $whr.= " and c.product_id IN($product_standard_id)";
			 } 
		     if($course_list!=''){
			  $whr.= " and c.course_id IN($course_list)";
			  
			 }
		    $sql = "SELECT c.code, c.title, c.description,c.product_id, c.level_id,c.thumnailImg, c.sequence_id,c.course_type,gmt.edge_id FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE course_type=$courseType ".$whr."";
        //echo "<pre>";print_r($sql);//exit;
			$stmt = $this->dbConn->prepare($sql);
			//$stmt->bindValue(':courseType', $courseType, PDO::PARAM_INT);
			
			
		}else{
			$sql = "SELECT * FROM course";
			$stmt = $this->dbConn->prepare($sql);
		}
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		///echo "<pre>";print_r($RESULT);exit;
		$cList = array();
		while($row = array_shift( $RESULT )) {
			$bcm = new stdClass();
			$bcm->title = $row['title'];
			$bcm->course_code = $row['code'];
			$bcm->course_id = str_replace("CRS-","",$row['code']);
			$bcm->course_type = $row['course_type'];
			$bcm->description = $row['description'];
			$bcm->edge_id = $row['edge_id'];
			$bcm->product_id = $row['product_id'];
			$bcm->thumnailImg = $row['thumnailImg'];
			$bcm->sequence_id = $row['sequence_id'];
			
			array_push($cList,$bcm);
			
		}
		 //echo "<pre>";print_r($cList);exit;
		 $courseArr= array();
		foreach($cList as $key => $value){
			$stmt = $this->dbConn->prepare("select st.standard, slm.level_text,slm.level_description,slm.level_cefr_map from tblx_standards st, tblx_standards_levels slm, course c where c.standard_id=st.id and c.level_id=slm.id and c.code='".$value->course_code."'");

			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//$RESULT1[0]
			 if($value->thumnailImg!=""){
				$crsImagetemp=$this->thumnail_Img_url.$value->thumnailImg;
			}else{
				$crsImagetemp=$this->img_url.$value->course_code.".png";
			} 
			 while($row = array_shift( $RESULT1 )) {
				$bcm = new stdClass();
				$bcm->percentage = 0;
				$bcm->edge_id = $value->edge_id;
				$bcm->product_id= $value->product_id;
				$bcm->name = $value->title;
				$bcm->desc = $value->description;
				$bcm->course_code = $value->course_code;
				$bcm->course_type = $value->course_type;
				$bcm->course_id = $value->course_id;
				$bcm->imgPath = $crsImagetemp;
				$bcm->sequence_id = $value->sequence_id;
				$bcm->standard = $row['standard'];
				$bcm->level_text = $row['level_text'];
				$bcm->level_description = $row['level_description']; 
				$bcm->level_cefr_map = $row['level_cefr_map'];
				array_push($courseArr,$bcm);
			 }
			$stmt->closeCursor();
			//echo "<pre>";print_r($bcm);exit;
	}
     //echo "<pre>";print_r($courseArr);exit;
		return $courseArr;
    }
  	
	//=============  Get batch data  methods by batch Id
   	public function getCustomProductCourseList($courseType,$course_list,$product_standard_id){
		
		
			 $whr='';
			  if($course_list!=''){
			  $whr.= 'c.course_id IN('.$course_list.')';
			  
			 }
			if($product_standard_id!=''){
			  $whr.= ' and product_id IN('.$product_standard_id.')';			  
			}
		    
		      $sql = "SELECT c.code, c.title, c.description,c.course_type,c.product_id, c.level_id,c.thumnailImg,c.sequence_id, gmt.edge_id FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE ".$whr." ";
         // echo "<pre>";print_r($sql);//exit;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$cList = array();
			while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->title = $row['title'];
				$bcm->course_code = $row['code'];
				$bcm->course_id = str_replace("CRS-","",$row['code']);
				$bcm->description = $row['description'];
				$bcm->edge_id = $row['edge_id'];
				$bcm->course_type = $row['course_type'];
				$bcm->product_id = $row['product_id'];
				$bcm->level_id = $row['level_id'];
				$bcm->thumnailImg = $row['thumnailImg'];
				$bcm->sequence_id = $row['sequence_id'];
				array_push($cList,$bcm);
		   }
		 //echo "<pre>";print_r($cList);//exit;
		 $courseArr= array();
		foreach($cList as $key => $value){
			$stmt = $this->dbConn->prepare("select st.standard, slm.level_text,slm.level_description,slm.level_cefr_map from tblx_standards st, tblx_standards_levels slm, course c where c.standard_id=st.id and c.level_id=slm.id and c.code='".$value->course_code."'");
			
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//$RESULT1[0]
			 if($value->thumnailImg!=""){
				$crsImagetemp=$this->thumnail_Img_url.$value->thumnailImg;
			}else{
				$crsImagetemp=$this->img_url.$value->course_code.".png";
			} 
			 while($row = array_shift( $RESULT1 )) {
				$bcm = new stdClass();
				$bcm->percentage = 0;
				$bcm->edge_id = $value->edge_id;
				$bcm->course_type= $value->course_type;
				$bcm->product_id= $value->product_id;
				$bcm->name = $value->title;
				$bcm->desc = $value->description;
				$bcm->course_code = $value->course_code;
				$bcm->course_id = $value->course_id;
				$bcm->sequence_id = $value->sequence_id;
				$bcm->level_id = $value->level_id;
				$bcm->imgPath = $crsImagetemp;
				$bcm->standard = $row['standard'];
				$bcm->level_text = $row['level_text'];
				$bcm->level_description = $row['level_description'];
				$bcm->level_cefr_map = $row['level_cefr_map'];
				array_push($courseArr,$bcm);
			 }
			$stmt->closeCursor();
			//echo "<pre>";print_r($bcm);//exit;	
			//echo "<pre>";print_r($bcm);exit;
	  }
     //echo "<pre>";print_r($courseArr);exit;
		return $courseArr;
    }
	//delete Batch ManadatoryModule map
	public function deleteBatchManadatoryModuleMap($batch_id,$center_id){

		$sql = "delete  FROM tblx_mandatory_module WHERE batch_id=:batch_id AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
		return true;
	
	}
	
	//=============  insert ManadatoryModule Map
	
	public function insertManadatoryModuleMap($center_id,$batch_id,$module_edge_id,$mandatory_module,$deadline_date){
		 $module_edge_id1=explode(",",$module_edge_id);
		 $mandatory_module1=explode(",",$mandatory_module);
		 $deadline_date1=explode(",",$deadline_date);
		 
		try{
			//  echo "<pre>insert";print_r($deadline_date1);exit;
				//$mandatory_module2=array();
				$mandatory_module2='';
			for( $i=0; $i<count($module_edge_id1); $i++){
				
				//echo "<pre>insert";print_r($mandatory_module1[$i]);
				if (in_array($module_edge_id1[$i],$mandatory_module1)) { 
					  $mandatory_module2='yes';
					} else { 
					   $mandatory_module2='no';					   
					}
					
				if($deadline_date1[$i]!=''){
					$sql = "INSERT INTO tblx_mandatory_module (center_id,batch_id,module_edge_id,is_mandatory,deadline_date) values (:center_id,:batch_id, :module_edge_id, :mandatoryModule,:deadline_date)";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
					$stmt->bindValue(':module_edge_id', $module_edge_id1[$i], PDO::PARAM_INT);
					$stmt->bindValue(':mandatoryModule', $mandatory_module2, PDO::PARAM_STR);
					$stmt->bindValue(':deadline_date', date('Y-m-d',strtotime($deadline_date1[$i])), PDO::PARAM_STR);
				}else{
					$sql = "INSERT INTO tblx_mandatory_module (center_id,batch_id,module_edge_id,is_mandatory) values (:center_id,:batch_id, :module_edge_id, :mandatoryModule)";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
					$stmt->bindValue(':module_edge_id', $module_edge_id1[$i], PDO::PARAM_INT);
					$stmt->bindValue(':mandatoryModule', $mandatory_module2, PDO::PARAM_STR);
					} 
				$stmt->execute();
				$stmt->closeCursor();
		
			}
	//echo "<pre>insert";print_r($mandatory_module2);exit;
			return true;	
		 }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}

		//Get  Batch ManadatoryModule map
	public function getBatchManadatoryModuleMap($batch_id,$center_id){

		$sql = "Select *  FROM tblx_mandatory_module WHERE batch_id=:batch_id AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC); 	
		$stmt->closeCursor(); 

		return $RESULT;
	
	}
 
	//=============  Get batch details methods
	public function getBatchDeatilsAsDesignation($center_id){
		$sql = "SELECT * FROM tblx_batch  where center_id=:center_id and is_default='1'";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batchArr = array();
		if(count($RESULT) > 0 ){
				while($row = array_shift( $RESULT )) {
					$batch_name = explode('-',$row['batch_name']);
					if(isset($batch_name[1]) && $batch_name[1]!="")
					{
						$batch_name = $batch_name[1]; 
					}else{
						$batch_name = $row['batch_name']; 
					}
				$bcm = array('batch_id'=>$row['batch_id'],'batch_name'=>$batch_name);
				
				
				array_push($batchArr,$bcm);
			 }
			 return $batchArr;
			}else{
				return false;
			} 
	}  
		 //============= Get user course detail methods by user id
    public function getUserCourseMap($user_id){
        //echo "<pre>";print_r($user_id);exit;
        $sql = "SELECT course_id from tblx_user_course_map where user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
		//echo "<pre>";print_r($stmt);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
    }
	 //============= Get user course detail methods by user id
    public function getBatchCourseMap($batch_id,$center_id){
        //echo "<pre>";print_r($user_id);exit;
        $sql = "SELECT course_id from tblx_batch_course_map where batch_id=:batch_id and center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(":batch_id", $batch_id, PDO::PARAM_INT); 
		$stmt->bindValue(":center_id", $center_id, PDO::PARAM_INT); 
		//echo "<pre>";print_r($center_id);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
    }
	public function getCourseListByCourseId($courseType,$course_id){
		if($courseType != ''){
		    $sql = "SELECT c.course_id, c.code, c.title, c.description, c.product_id, c.level_id,c.thumnailImg,gmt.edge_id FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE course_type=:courseType and status = 'active'  and course_id = :course_id order by c.course_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':courseType', $courseType, PDO::PARAM_INT);
			$stmt->bindValue(':course_id', $course_id, PDO::PARAM_INT); 
		}else{
			$sql = "SELECT * FROM course";
			$stmt = $this->dbConn->prepare($sql);
		}
        
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);

		 $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT[0];
			}else{
				return false;
			}
  }
  
   public function getCourseListByLanguage($courseId,$group_client){
		 $sql = "SELECT c.course_id,c.code, c.title, c.description, c.thumnailImg,gmt.edge_id FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE course_id=:courseId and client_id = :group_client and status = 'active'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
		$stmt->bindValue(':group_client', $group_client, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
       if(count($RESULT) > 0 ){
				return $RESULT[0];
			}else{
				return false;
			} 
  }
  
public function getStateIdByName($name)
{
   
    $stmt = $this->dbConn->prepare("SELECT center_id FROM tblx_center WHERE name=:name");
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	if(count($RESULT) > 0 ){
			return $RESULT[0]['center_id'];
		}else{
			return '';
		} 
   
}
public function getBatchIdByName($center_id,$name)
{		$name = 'Class-'.trim($name);
		$sql = "SELECT batch_id FROM tblx_batch  where center_id=:center_id and batch_name=:batch_name";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		 $stmt->bindValue(':batch_name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
			return $RESULT[0]['batch_id'];
		}else{
			return '';
		} 
   
}

	public function getSignedUpUserCountDistrcitBased($center_id,$district_id){
		$sql = "SELECT count(*) teacherReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id and uld.role_definition_id =1  and uc.is_active =1 AND ucm.center_id = '$center_id' AND ucm.district_id=:district_id";
		
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':district_id', $district_id, PDO::PARAM_INT);
		$stmt->execute();
		$tRESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if($tRESULT[0]['teacherReg']){
			$teacher = $tRESULT[0]['teacherReg'];
		}else{
	    	$teacher = 0;
		}
        $stmt->closeCursor();
		//echo "<pre>";print_r($tRESULT[0]['teacherReg']);exit;
		
		$sql="SELECT count(*) studentReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id and uld.role_definition_id =2  and uc.is_active =1 AND ucm.center_id = '$center_id' AND ucm.district_id=:district_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':district_id', $district_id, PDO::PARAM_INT);
		$stmt->execute();																		
		$sRESULT =$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($sRESULT[0]['studentReg']){
			$student = $sRESULT[0]['studentReg'];
		}else{
			$student = 0;
		}
		$stmt->closeCursor();
		
		
		$obj = new stdClass();
		
		$obj->teacher = $teacher;
		$obj->student = $student;
		
		return $obj; 
	}

  public function getBatchDetailByUserID($uid,$roleID){

		$sql = "SELECT bum.batch_id FROM tblx_batch_user_map bum join user_role_map uld ON bum.user_id = uld.user_id join user_credential uc "
                . " WHERE bum.status = 1 AND uld.user_id = ".$uid." AND uld.role_definition_id = ".$roleID." AND bum.center_id = ".$_SESSION['center_id']." group by bum.batch_id order by bum.batch_id asc";
			
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
		
	}
	
	public function sendToMixPanel($data)
	{
		$curl = curl_init();

		$jsonData=json_encode($data);

		//print_r($jsonData);exit;
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://65.2.86.61:6000/pushMixPanelData',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>$jsonData,
		CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response; 
	}
	
}	