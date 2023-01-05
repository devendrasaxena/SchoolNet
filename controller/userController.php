<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);
require_once __DIR__ . '/serviceController.php';
class userController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }
    public static function getLoggedInUserData() {

        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        return array();
    }

    public static function getLoggedInUserID() {
        return $_SESSION['user']['user_id'];
    }

    public static function getLoggedInUserToken() {
        return $_SESSION['user']['token'];
    }

    public function setUserSession() {
        $_SESSION['user'] = getUserLogData($user_id);
    }

    public function getUSerDataFromDB($user_id){
        return $this->getUserLogData($user_id);
    }

    public function getUserLogData($user_id) {

        $sql = "select * from user where user_id = :uid ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = array();
        if (count($RESULT)) {
            $row = array_shift($RESULT);
        }
        $stmt->closeCursor();
        if (isset($row) && !empty($row) && count($row)) {
            return $row;
        }

        return array();
    }    

    public function getUserLogDataByEmail($email) {


        $sql = "select * from user where email = :email ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = array();
        if (count($RESULT)) {
            $row = array_shift($RESULT);
        }
        $stmt->closeCursor();
        if (isset($row) && !empty($row) && count($row)) {
            return $row;
        }

        return array();
    }

    public function getUserLogDataByEmailUserId($email, $userid) {
        $sql = "select password from user_credential where email = :email AND user_id =:user_id ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
        $row = array();
        if (count($RESULT)) {
            $row = array_shift($RESULT);
        }
        $stmt->closeCursor();
        if (isset($row) && !empty($row) && count($row)) {
            return $row;
        }

        return array();
    }

	public function getReportLink($user_token, $course_code, $pack_code){
        
        $serviceObj = new serviceController();
        $params = new stdClass();
        
        $params->platform = WEB_SERVICE_PLATFORM;
        $params->deviceId = WEB_SERVICE_DEVICE_ID;
        $params->appVersion = WEB_SERVICE_APP_VERSION;
        $params->package_code = $pack_code;
        $params->course_code = $course_code;
        

        $res = $serviceObj->processRequest($user_token, 'getUserCourseReport', $params);
        $res = json_encode($res);
        $res = json_decode($res, true);
        
        if( empty($res) ){
            return '';
        }
        
        if(strcasecmp($res['retCode'], 'success') == 0){
            $report_url = trim($res['retVal']['report_url']);
            return $report_url;
        }
        
        return '';
    }
    
	/* public function getTestReport($roleID, $batchID){
	  
		$sql = "SELECT * from temp_ans_push WHERE user_id='135098' order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batchID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$user_id=$RESULT[0]['user_id'];
		//echo "<pre>"; print_r($user_id); die;
	    //echo "<pre>"; print_r($RESULT); die;
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
	} */
	
   /*  public function updateUser($dataArr){

		$name =$dataArr->name;
		$lname =$dataArr->lname;
		$phone =$dataArr->mobile;
		$user_id=$dataArr->userIdVal;
		$profile_id=$dataArr->profile_id;
		$fileImgNamePro=$dataArr->fileImgNamePro;
		
		//Update user
		$sql = "UPDATE user SET  first_name = '$name' ,last_name= '$lname' where user_id = '$user_id'";
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

        return true;
		
        
  } */
  
  //============= Upadate Student  methods

	public function updateUser( array $request){
		try{
		$user_id = isset($request['userIdVal'])? $request['userIdVal'] : "";	
        $fName=filter_string($request['name']);
        $lName=filter_string($request['lastname']);			
		$first_name = isset($fName) ? $fName : "";
		$last_name = isset($lName) ? $lName : "";
       // $email = isset($request['email']) ? trim($request['email']) : "";
        $is_email_verified = 0;
		 $mobile1=filter_string($request['mobile']);
        $phone = isset($mobile1) ? trim($mobile1) : "";
		if($phone==''){
		  $country_code="";	
		}else{
		  $country_code = isset($request['cntryCode']) ? $request['cntryCode'] : "";	
		}
        $is_phone_verified = 0;

		$profile_id = isset($request['profile_id'])? trim($_POST["profile_id"]) : "";
		$fileImgNamePro = isset($request['fileImgNamePro'])? trim($_POST["fileImgNamePro"]) : "";
		//echo "<pre>";print_r($request);exit;
		
		//// for get address id by user id
	    $stmt = $this->dbConn->prepare("Select address_id,profile_pic FROM user  WHERE user_id=:user_id");
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
		$stmt->closeCursor();
		$address_id = $RESULT[0]['address_id'];
		$profile_pic = $RESULT[0]['profile_pic'];
		//echo $profile_pic;exit;
		 //// update phone in address master
	 
		$stmt = $this->dbConn->prepare("UPDATE address_master SET phone=:phone,country_code=:country_code,is_phone_verified=:is_phone_verified,modified_date= NOW() WHERE address_id=:address_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':country_code',$country_code, PDO::PARAM_STR);
		$stmt->bindValue(':phone',$phone, PDO::PARAM_STR);
		$stmt->bindValue(':is_phone_verified',$is_phone_verified, PDO::PARAM_STR);
		$stmt->bindValue(':address_id',$address_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();

		if(isset($profile_pic) && $profile_pic=="") {
			$stmt = $this->dbConn->prepare("update asset set display_name=:fileImgNamePro',system_name=:fileImgNamePro,modified_date=NOW() where asset_id=:profile_pic");
			$stmt->bindValue(':fileImgNamePro',$fileImgNamePro, PDO::PARAM_STR);
		    $stmt->bindValue(':profile_pic',$profile_pic, PDO::PARAM_INT);
			$stmt->execute();
		    $stmt->closeCursor();
		}
		else
		{
			$stmt = $this->dbConn->prepare("INSERT INTO asset(display_name,system_name,path,updated_by,created_date) values(:fileImgNamePro,:fileImgNamePro,'/profile_pic/',:user_id,NOW())");
			$stmt->bindValue(':fileImgNamePro',$fileImgNamePro, PDO::PARAM_STR);
			$stmt->bindValue(':user_id',$user_id, PDO::PARAM_STR);
		    
			$stmt->execute();
			$file_loc_id = $this->dbConn->lastInsertId();
            $stmt->closeCursor();

			$stmt = $this->dbConn->prepare("update user set profile_pic=:file_loc_id where user_id=:user_id");
			$stmt->bindValue(':file_loc_id',$file_loc_id, PDO::PARAM_INT);
		    $stmt->bindValue(':user_id',$user_id, PDO::PARAM_INT);
			$stmt->execute();
			$file_loc_id = $this->dbConn->lastInsertId();
			$stmt->closeCursor();
		}

     
	//// update user 
	   $stmt = $this->dbConn->prepare("UPDATE user SET first_name=:first_name ,last_name= :last_name, modified_date= Now() WHERE user_id=:user_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
		$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
		
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor(); 
		


		//// update profile pic  
		$stmt= $this->dbConn->prepare("UPDATE asset SET system_name=:system_name,modified_date=NOW() WHERE asset_id=:profile_id");
	    $stmt->bindValue(':profile_id', $profile_id, PDO::PARAM_INT);
		 $stmt->bindValue(':system_name', $fileImgNamePro, PDO::PARAM_STR);
		$stmt->execute();
		$stmt->closeCursor();
//echo "<pre>";print_r($address_id);//exit;
        return true;
		
		}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}

  	public function updatePernsoInfoUser( array $request){
		try{
		$user_id = isset($request['userIdVal'])? $request['userIdVal'] : "";	
        
		$gender = isset($request['gender']) ? trim($request['gender']) : "";
		$age= isset($request['age']) ? trim($request['age']) : "";
		$country_id = isset($request['country_dropdown']) ? trim($request['country_dropdown']) : "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		$education_id = isset($request['education']) ? trim($request['education']) : "";
		$emp_status_id = isset($request['empStatus']) ? trim($request['empStatus']) : "";
		$purpose_join_id = isset($request['purJoining']) ? trim($request['purJoining']) : "";
		$roll_no = isset($request['roll_no']) ? trim($request['roll_no']) : "";
		$fathers_name = isset($request['fathers_name']) ? trim($request['fathers_name']) : "";
		$mothers_name = isset($request['mothers_name']) ? trim($request['mothers_name']) : "";
		$section = isset($request['section']) ? trim($request['section']) : "";
		$slot = isset($request['slot']) ? trim($request['slot']) : "";
		
		//// for get address id by user id
	    $stmt = $this->dbConn->prepare("Select address_id,profile_pic FROM user  WHERE user_id=:user_id");
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
		$stmt->closeCursor();
		$address_id = $RESULT[0]['address_id'];

		 //// update phone in address master
	 
		$stmt = $this->dbConn->prepare("UPDATE address_master SET country=:country,modified_date= NOW() WHERE address_id=:address_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
		$stmt->bindValue(':address_id',$address_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();
		
	//// update user 
	   $stmt = $this->dbConn->prepare("UPDATE user SET gender=:gender,age_range=:age_range,mother_tongue=:mother_tongue,education=:education,employment_status=:employment_status,joining_purpose=:joining_purpose,modified_date= Now(), roll_no=:roll_no, fathers_name = :fathers_name, mothers_name = :mothers_name, section =:section, slot=:slot  WHERE user_id=:user_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
		$stmt->bindValue(':age_range', $age, PDO::PARAM_STR);
		$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);
		$stmt->bindValue(':education', $education_id, PDO::PARAM_INT);
		$stmt->bindValue(':employment_status', $emp_status_id, PDO::PARAM_INT);
		$stmt->bindValue(':joining_purpose', $purpose_join_id, PDO::PARAM_STR);

		$stmt->bindValue(':roll_no', $roll_no, PDO::PARAM_STR);
		$stmt->bindValue(':fathers_name', $fathers_name, PDO::PARAM_STR);
		$stmt->bindValue(':mothers_name', $mothers_name, PDO::PARAM_STR);
		$stmt->bindValue(':section', $section, PDO::PARAM_STR);
		$stmt->bindValue(':slot', $slot, PDO::PARAM_STR);

		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor(); 
		
        return true;
		
		}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
  //============= change user passoword
   public function updatePassword($dataArr){
		
		$password=trim(filter_string($dataArr->fld_password));
		$user_id=$dataArr->userIdVal;

		//// change password in  login Credentials 
		if($password!=""){
		$stmt= $this->dbConn->prepare("UPDATE `user_credential` SET `password`=:password ,`modified_date`=NOW() WHERE `user_id`=:user_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':password', $password, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();  
		}
        return true;
		
        
  }
  
	//============= Get User Test Count For last Week 
	public function getTestAttemptCount(){
	

		$sql = "SELECT COUNT(*) as 'cnt' FROM  tbl_test_complete_status where  user_id=:user_id and status='1' and (battery_id='' or battery_id IS NULL)";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$cnt1= $RESULT[0]['cnt'];	
		
		
		$cnt2=0;
		$sql = "SELECT battery_id from tbl_test_complete_status where user_id=:user_id and (battery_id!='' or battery_id IS NOT NULL) group by battery_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		while($row = array_shift( $RESULT )) {
			$status=getBatteryCompleteStatus($_SESSION['user_id'],$row['battery_id']);
			if($status==1){
				$cnt2++;
			}
		}
			
		return $cnt1+$cnt2;	

    }
	//============= Get User Batch Per
	public function getBatchPer($testId,$batch_id,$batt_id=''){
	

		if($batt_id!=''){
		
		
		$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,fld_datetime FROM temp_ans_push INNER JOIN tbl_test_complete_status ON temp_ans_push.battery_id=tbl_test_complete_status.battery_id WHERE  battery_id =:battery_id  and battery_status='1' and temp_ans_push.user_id IN(SELECT  uld.user_id FROM user_role_map uld JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id order by uld.user_id DESC)";
       

		$stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':battery_id',$batt_id, PDO::PARAM_INT);
	    $stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);	
		
		}
		else{
			
			
			
		$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,fld_datetime FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id IN(SELECT  uld.user_id FROM user_role_map uld JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id order by uld.user_id DESC)";
       

		$stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
	    $stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
		}
		
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];	
	

    }	
	
	public function getBatchPer2($testId,$batch_id,$center_id,$isBattery=''){
	
		$ttlQuestion=0;
		$ttlCorrect=0;
		if($isBattery==1){
			
			$userList=$this->getAllUserOfTest($testId,$isBattery);
			foreach($userList as $key=>$val){
				
				$chkBatch=$this->checkUserBatch($batch_id,$center_id,$val);
				
				if($chkBatch){
					
						$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id  and user_id=:user_id";
						$stmt = $this->dbConn->prepare($sql);
						$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
						$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);	
						$stmt->execute();
						$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						$RESULT =$RESULT[0];
						$ttlQuestion=$ttlQuestion+$RESULT['qCount'];
						$ttlCorrect=$ttlCorrect+$RESULT['ttlCorrect'];
				}
				
				
			}
			$resultArray=array('ttlCorrect'=>$ttlCorrect,'qCount'=>$ttlQuestion);
			return $resultArray;	
		}
		else{
			
			$userList=$this->getAllUserOfTest($testId,$isBattery);
			foreach($userList as $key=>$val){
			$chkBatch=$this->checkUserBatch($batch_id,$center_id,$val);
				
			if($chkBatch){
					
						$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
						$stmt = $this->dbConn->prepare($sql);
						$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
						$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);	
						$stmt->execute();
						$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						$RESULT =$RESULT[0];
						$ttlQuestion=$ttlQuestion+$RESULT['qCount'];
						$ttlCorrect=$ttlCorrect+$RESULT['ttlCorrect'];
				}
				
				
			}
			$resultArray=array('ttlCorrect'=>$ttlCorrect,'qCount'=>$ttlQuestion);
			return $resultArray;	
		}
}
	
	
	
	
//============= Get User Center Per
	public function getCustomerPer($testId,$isBattery=''){
	
		if($isBattery!=''){
		$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,fld_datetime FROM temp_ans_push WHERE  battery_id =:battery_id  and user_id IN(SELECT  uld.user_id FROM user_role_map uld JOIN user_center_map ucm ON ucm.user_id = uld.user_id WHERE uld.role_definition_id = :roleID AND ucm.center_id = :center_id  order by uld.user_id DESC)";
       

	    $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
	    $stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);	
		
		}
		else{
		$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,fld_datetime FROM temp_ans_push WHERE  test_id =:test_id  and (battery_id='' or battery_id IS NULL) and user_id IN(SELECT  uld.user_id FROM user_role_map uld JOIN user_center_map ucm ON ucm.user_id = uld.user_id WHERE uld.role_definition_id = :roleID AND ucm.center_id = :center_id  order by uld.user_id DESC)";
       

	   $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
	    $stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
		}
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];	
	

    }


	public function getCustomerPer2($testId,$center_id,$isBattery=''){
	
		$ttlQuestion=0;
		$ttlCorrect=0;
		if($isBattery==1){
			
			$userList=$this->getAllUserOfTest($testId,$isBattery);
			foreach($userList as $key=>$val){
				
				$chkCenter=$this->checkUserCenter($center_id,$val);
				
				if($chkCenter){
					
						$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id  and user_id=:user_id";
						$stmt = $this->dbConn->prepare($sql);
						$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
						$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);	
						$stmt->execute();
						$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						$RESULT =$RESULT[0];
						$ttlQuestion=$ttlQuestion+$RESULT['qCount'];
						$ttlCorrect=$ttlCorrect+$RESULT['ttlCorrect'];
				}
				
				
			}
			$resultArray=array('ttlCorrect'=>$ttlCorrect,'qCount'=>$ttlQuestion);
			return $resultArray;	
		}
		else{
			
			$userList=$this->getAllUserOfTest($testId,$isBattery);
			foreach($userList as $key=>$val){
			
			$chkCenter=$this->checkUserCenter($center_id,$val);	
			if($chkCenter){
					
						$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
						$stmt = $this->dbConn->prepare($sql);
						$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
						$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);	
						$stmt->execute();
						$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						$RESULT =$RESULT[0];
						$ttlQuestion=$ttlQuestion+$RESULT['qCount'];
						$ttlCorrect=$ttlCorrect+$RESULT['ttlCorrect'];
				}
				
				
			}
			$resultArray=array('ttlCorrect'=>$ttlCorrect,'qCount'=>$ttlQuestion);
			return $resultArray;	
		}
	}



//============= Get Test Average percentage
	public function getTestAvgPer($testId,$isBattery=''){
		$ttlQuestion=0;
		$ttlCorrect=0;
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		foreach($userList as $key=>$val){
	
			 if($isBattery!=''){
				$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,fld_datetime FROM temp_ans_push WHERE  battery_id =:battery_id and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$RESULT =$RESULT[0];
				$ttlQuestion=$ttlQuestion+$RESULT['qCount'];
				$ttlCorrect=$ttlCorrect+$RESULT['ttlCorrect'];
			}
			else{ 
				$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,fld_datetime FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);	
				$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$RESULT =$RESULT[0];
				$ttlQuestion=$ttlQuestion+$RESULT['qCount'];
				$ttlCorrect=$ttlCorrect+$RESULT['ttlCorrect'];
			}
		}
		
		$resultArray=array('ttlCorrect'=>$ttlCorrect,'qCount'=>$ttlQuestion);
		return $resultArray;	
    }
	
   //Get Batch Course map data
	public function getUserCourseList($user_id,$center_id){

		$sql = "SELECT course_id FROM tblx_batch_course_map WHERE center_id=:center_id AND batch_id IN(SELECT batch_id from tblx_batch_user_map where user_id=:user_id and status =1 AND center_id=:center_id1)";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':center_id1', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$courseArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($courseArr,$row['course_id']);
		}
        return $courseArr;
	
	}
	
	//Get Batch Battery map data
	public function getUserBatteryList($user_id,$center_id){

		$sql = "SELECT DISTINCT(tpbm.battery_id) FROM tbl_product_battery_map tpbm JOIN tblx_batch_battery_map tbbm ON tpbm.product_id=tbbm.battery_id WHERE tbbm.center_id=:center_id AND tbbm.batch_id IN(SELECT batch_id from tblx_batch_user_map where user_id=:user_id and status =1 AND center_id=:center_id1) order by tpbm.battery_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':center_id1', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batteryArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($batteryArr,$row['battery_id']);
		}
        return $batteryArr;
	
	}
	
	//Get Batch Battery map data
	public function getUserBatteryList2($user_id,$center_id){

		$sql = "SELECT battery_id FROM tblx_batch_battery_map WHERE center_id=:center_id AND batch_id IN(SELECT batch_id from tblx_batch_user_map where user_id=:user_id and status =1 AND center_id=:center_id1)";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':center_id1', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batteryArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($batteryArr,$row['battery_id']);
		}
        return $batteryArr;
	
	}
	 //Get Batch Battery map data
	public function getBatteryTestList($batt_id){

		$sql = "SELECT client_id,edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id order by sequence_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batteryArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($batteryArr,array('client_id'=>$row['client_id'],'edge_id'=>$row['edge_id'],'sequence_id'=>$row['sequence_id']));
		}
        return $batteryArr;
	
	} 	
	
	
	 //Get user test list based on user course and demographic details
	public function getUserBatteryTestList($uid,$course_id){

		
		$sql = "SELECT edge_id FROM tbl_course_stanine_range WHERE course_id=:course_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':course_id',$course_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$edgeidArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($edgeidArr,array('edge_id'=>$row['edge_id']));
		}
		
		 return $edgeidArr;
	
	} 	
	
	
	
	
	//Get Batch Battery map data
	public function getFirstTestBattery($batt_id,$course_count=''){

		if($course_count>1)
		{
		$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id order by sequence_id asc LIMIT 1";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];
		}
		elseif($course_count==1){
			$course_id=	$this->getUserCourseId($_SESSION['user_id']);
			$testList=$this->getUserBatteryTestList($_SESSION['user_id'],$course_id);
			foreach($testList as $key=>$test){
				$test_arr[]=$test['edge_id'];
				
			}
			$test_arr=implode(',',$test_arr);
			
			$sql = "SELECT edge_id,MIN(sequence_id) as sequence_id FROM client_battery_map WHERE battery_id=:battery_id and edge_id IN($test_arr) order by sequence_id asc LIMIT 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			return $RESULT[0];
			
		}
		
		else{
			
		$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id order by sequence_id asc LIMIT 1";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];
			
		}
		
		
	
	}
	//Get Next Edge And Sequence ID
	public function getNextEdgeSeqid($batt_id,$sequence_id,$course_count=''){

	if($course_count>1){

		$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id And sequence_id>$sequence_id order by sequence_id asc LIMIT 1";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];
	}
	elseif($course_count==1){
		
		$course_id=$this->getUserCourseId($_SESSION['user_id']);
		$test_arr=array();
		$testlist=$this->getUserBatteryTestList($_SESSION['user_id'],$course_id);
		foreach($testlist as $key=>$test){
				$test_arr[]=$test['edge_id'];
				
		}
		
		$test_arr=implode(',',$test_arr);
		
		$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id And sequence_id>$sequence_id And edge_id IN($test_arr) order by sequence_id asc LIMIT 1";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];
	}
	else{
		$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id And sequence_id>$sequence_id order by sequence_id asc LIMIT 1";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];
		
	}
	
	
	} 
	//Get Last Edge And Sequence ID
	public function getLastEdgeSeqid($batt_id,$course_count=''){
	
		if($course_count>1){
			$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id  order by sequence_id desc LIMIT 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			return $RESULT[0];
		}
		elseif($course_count==1){
			$course_id=$this->getUserCourseId($_SESSION['user_id']);
			$test_arr=array();
			$testlist=$this->getUserBatteryTestList($_SESSION['user_id'],$course_id);
			foreach($testlist as $key=>$test){
					$test_arr[]=$test['edge_id'];
					
			}
			$test_arr=implode(',',$test_arr);
			
			$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id And edge_id IN($test_arr) order by sequence_id desc LIMIT 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			return $RESULT[0];
			
		}else{
			$sql = "SELECT edge_id,sequence_id FROM client_battery_map WHERE battery_id=:battery_id  order by sequence_id desc LIMIT 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id', $batt_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			return $RESULT[0];
			
		}
	
	
	
	} 
	
//Get Complete Battery reslt
	public function getAllBatteryResult($battId,$user_id){
		
		$testList=$this->getBatteryTestList($battId);
		$batteryResultArr=array();
		$cumulativeQuesCnt=0;
		$cumulativeCrctCnt=0;
		$cumulativeTimeSp=0;
		$cumulativeQuesCntBat=0;
		$attemptDate=$this->getMaxAttemptDate($battId,$user_id,1);
		foreach($testList as $key=>$test){
			
			$userResult= getBatteryTestResult($battId,$test['edge_id'],$user_id);
			$quesCount= $this->getTestQuesCount($test['edge_id']);
			$quesCount=$quesCount['qCount'];
			$userResult=$userResult[0];
			$per=round(($userResult->ttlCorrect*100)/$userResult->qCount);
			$time_sp=$userResult->time_sp;
			if($userResult->fld_datetime!=''){
				$fld_datetime=$userResult->fld_datetime;
			}
			
			array_push($batteryResultArr,array('edge_id'=>$test['edge_id'],'ttl_questions'=>$userResult->qCount,'ttl_correct'=>$userResult->ttlCorrect,'per'=>$per,'time_sp'=>$time_sp,'fld_datetime'=>$fld_datetime));
		
			$cumulativeQuesCnt+=$userResult->qCount;
			$cumulativeQuesCntBat+=$quesCount;
			$cumulativeCrctCnt+=$userResult->ttlCorrect;
			$cumulativeTimeSp+=$time_sp;
		
		
		}
		
	$cumulativePer=round(($cumulativeCrctCnt*100)/$cumulativeQuesCntBat);

	$cumulativeScorearr=array('cumQuesCnt'=>$cumulativeQuesCnt,'cumAllQuesCnt'=>$cumulativeQuesCntBat,'cumCrctCnt'=>$cumulativeCrctCnt,'cumPer'=>$cumulativePer,'cumTimeSp'=>$cumulativeTimeSp,'fld_datetime'=>$fld_datetime,'attemptDate'=>$attemptDate); 
	return json_encode(array('batteryResultArr'=>$batteryResultArr,'cumScorearr'=>$cumulativeScorearr));
	
}

//============= Get Test Question Count
	public function getTestQuesCount($testId){
	

		 $sql = "SELECT count(`id`) as qCount FROM tbl_questions WHERE  parent_edge_id=:parent_edge_id and status='1' and isPractice='0'";
       

	   $stmt = $this->dbConn->prepare($sql);
	   $stmt->bindValue(':parent_edge_id',$testId, PDO::PARAM_INT);
		
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT[0];	
	

    }
	
	//============= Get User Rank In Batch
	public function getBatchRank($testId,$user_id,$batch_id,$center_id,$isBattery=''){
	
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		
		$rankArr=array();
		foreach($userList as $key=>$userId){
			
			$chkBatch=$this->checkUserBatch($batch_id,$center_id,$userId);
			if($chkBatch){
				if($isBattery!=''){
					$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id  and user_id=:user_id";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
					$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
				}
				else{
					$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
					$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
				}

				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$per=round((($RESULT[0]['ttlCorrect']*100)/$RESULT[0]['qCount']),2);
				$rankArr[$userId]=$per;
			
			}
		}
		
		arsort($rankArr);
		$newrank = array();
		$i = 0;
		$last_v = null;
		foreach ($rankArr as $k => $v) {
			if ($v !== $last_v) {
				$i++;
				$last_v = $v;
			}
			$newrank[$k] = $i;
		}
		
		return $newrank[$user_id];
	
    }	
	
	
		//============= Get User Rank Per In Batch
	public function getBatchRankPercentile($testId,$user_id,$batch_id,$center_id,$isBattery=''){
	
		
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		
		$rankArr=array();
		
		foreach($userList as $key=>$userId){
			$chkBatch=$this->checkUserBatch($batch_id,$center_id,$userId);
			if($chkBatch){
				if($isBattery!=''){
					$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id  and user_id=:user_id";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
					$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
				}
				else{
					$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
					$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
				}

				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$per=round((($RESULT[0]['ttlCorrect']*100)/$RESULT[0]['qCount']),2);
				$rankArr[$userId]=$per;
			
			}
		}
		arsort($rankArr);
		$newrank = array();
		$i = 0;
		$last_v = null;
		foreach ($rankArr as $k => $v) {
			if ($v !== $last_v) {
				$i++;
				$last_v = $v;
			}
			$newrank[$k] = $i;
		}
		
		
		
		//Calculate rank
		$total_test_taker=count($rankArr);
		$percentile=round((($total_test_taker-$newrank[$user_id])/($total_test_taker))*100);
		
		
		
		return $percentile;
	
    }
	
	
	
	 //Get Batch Battery map data
	public function getSameBatchUser($batch_id){

		$sql="SELECT  uld.user_id FROM user_role_map uld JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$userArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($userArr,$row['user_id']);
		}
        return $userArr;
	
	} 

	//============= Get User Rank In Customer
	public function getCustomerRank($testId,$user_id,$center_id,$isBattery=''){
	
		
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		$rankArr=array();
		foreach($userList as $key=>$userId){
			$chkCenter=$this->checkUserCenter($center_id,$userId);	
			if($chkCenter){
				if($isBattery!=''){
				$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
				}
				else{
				$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);	
				}
			

			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$per=round((($RESULT[0]['ttlCorrect']*100)/$RESULT[0]['qCount']),2);
			$rankArr[$userId]=$per;
		}
		}
		arsort($rankArr);
		$newrank = array();
		$i = 0;
		$last_v = null;
		foreach ($rankArr as $k => $v) {
			if ($v !== $last_v) {
				$i++;
				$last_v = $v;
			}
			$newrank[$k] = $i;
		}
		
		return $newrank[$user_id];
	
    }	
	
	//============= Get User Rank Percentile In Customer
	public function getCustomerRankPercentile($testId,$user_id,$center_id,$isBattery=''){
	
		
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		$rankArr=array();
		foreach($userList as $key=>$userId){
			$chkCenter=$this->checkUserCenter($center_id,$userId);
			if($chkCenter){
				
				if($isBattery!=''){
				$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
				}
				else{
				$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);	
				}

				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$per=round((($RESULT[0]['ttlCorrect']*100)/$RESULT[0]['qCount']),2);
				$rankArr[$userId]=$per;
			
			}
		}
		arsort($rankArr);
		$newrank = array();
		$i = 0;
		$last_v = null;
		foreach ($rankArr as $k => $v) {
			if ($v !== $last_v) {
				$i++;
				$last_v = $v;
			}
			$newrank[$k] = $i;
		}
		
		//Calculate rank percentile
		$total_test_taker=count($rankArr);
		$percentile=round((($total_test_taker-$newrank[$user_id])/($total_test_taker))*100);
		
		
		
		return $percentile;
	
    }
	 //Get Same Customer User
	public function getSameCustomerUser($center_id){

		$sql="SELECT  uld.user_id FROM user_role_map uld JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND  bum.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$userArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($userArr,$row['user_id']);
		}
        return $userArr;
	
	} 
	

	//============= Get User Rank In Overall
	public function getOverAllRank($testId,$user_id,$client_id,$isBattery=''){
	
		
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		$rankArr=array();
		foreach($userList as $key=>$userId){
			if($isBattery!=''){
			 $sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id and user_id=:user_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
			$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
			}
			else{
			$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
			$stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);	
			}

			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$per=round((($RESULT[0]['ttlCorrect']*100)/$RESULT[0]['qCount']),2);
			$rankArr[$userId]=$per;
		
		}
		arsort($rankArr);
		$newrank = array();
		$i = 0;
		$last_v = null;
		foreach ($rankArr as $k => $v) {
			if ($v !== $last_v) {
				$i++;
				$last_v = $v;
			}
			$newrank[$k] = $i;
		}
		
		return $newrank[$user_id];
	
    }

	//============= Get User Rank Percentile In Overall
	public function getOverAllRankPercentile($testId,$user_id,$client_id,$isBattery=''){
	
		$userList=$this->getAllUserOfTest($testId,$isBattery);
		/* echo $testId;echo '<br>';
		echo $isBattery;
		print_r($userList);exit; */
		$rankArr=array();
		foreach($userList as $key=>$userId){
		if($isBattery!=''){
		 $sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id and user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':battery_id',$testId, PDO::PARAM_INT);
	    $stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);
		}
		else{
		$sql = "SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  test_id =:test_id and (battery_id='' or battery_id IS NULL) and user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':test_id',$testId, PDO::PARAM_INT);
	    $stmt->bindValue(':user_id',$userId, PDO::PARAM_INT);	
		}

		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$per=round((($RESULT[0]['ttlCorrect']*100)/$RESULT[0]['qCount']),2);
		$rankArr[$userId]=$per;
		
		}
		arsort($rankArr);
		$newrank = array();
		$i = 0;
		$last_v = null;
		foreach ($rankArr as $k => $v) {
			if ($v !== $last_v) {
				$i++;
				$last_v = $v;
			}
			$newrank[$k] = $i;
		}
		
		//Calculate rank percentile
		$total_test_taker=count($rankArr);
		$percentile=round((($total_test_taker-$newrank[$user_id])/($total_test_taker))*100);
		
		
		
		return $percentile;
	
    }

	
//Get OverAll User for Same Client
public function getOverAllUser($client_id){

		$sql="SELECT user_id FROM user where user_client_id=:user_client_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_client_id',$client_id,PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$userArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($userArr,$row['user_id']);
		}
		
        return $userArr;
	
	} 	
	
	 //Get Same Customer User
	public function getBatteryName($batt_id){

		$sql="SELECT  battery_name from tblx_battery where id=:id";
        $stmt = $this->dbConn->prepare($sql);
	    $stmt->bindValue(':id', $batt_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
        return $RESULT;
	
	}  
	
	//Get max attempted date
	public function getMaxAttemptDate($test_id,$user_id,$isBattery=''){
		
		if($isBattery!=''){
			$sql="SELECT MAX(attempt_date) as mx_date FROM `tbl_test_complete_status` where user_id=:user_id and battery_id=:battery_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id',$test_id, PDO::PARAM_INT);
			$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			if($stmt->execute()){
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				return $RESULT[0]['mx_date'];
			}
			else{
				return false;
			}
		}
		else{
			$sql="SELECT MAX(attempt_date) as mx_date FROM `tbl_test_complete_status` where user_id=:user_id and test_id=:test_id and (battery_id='' or battery_id IS NULL)";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':test_id',$test_id, PDO::PARAM_INT);
			$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			if($stmt->execute()){
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				return $RESULT[0]['mx_date'];
			}
			else{
				return false;
			}
		}
	} 
	
	public function getAllUserOfTest($batt_id,$isBattery){
		
		if($isBattery=='1'){
		
			$sql="SELECT DISTINCT user_id FROM tbl_test_complete_status where battery_id=:battery_id and battery_status='1'";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id',$batt_id,PDO::PARAM_INT);
		}
		else{
			
			$sql="SELECT DISTINCT user_id FROM tbl_test_complete_status where test_id=:test_id and status='1' and (battery_id='' or battery_id IS NULL)";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':test_id',$batt_id,PDO::PARAM_INT);
		}
		
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($userArr,$row['user_id']);
		}
		$stmt->closeCursor();
        return $userArr;
	
	}
	
	public function checkUserBatch($batch_id,$center_id,$user_id){
		
		$sql="SELECT  uld.user_id FROM user_role_map uld INNER JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id  AND bum.user_id=:user_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$center_id, PDO::PARAM_INT);
		$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
		if($stmt->execute()){
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			return $RESULT;
		}
		else{
			return false;
		}
	}
	
	public function checkUserCenter($center_id,$user_id){
		
		$sql="SELECT  uld.user_id FROM user_role_map uld INNER JOIN user_center_map ucm ON ucm.user_id = uld.user_id WHERE uld.role_definition_id = :roleID AND ucm.center_id = :center_id AND ucm.user_id=:user_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':roleID', 2, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$center_id, PDO::PARAM_INT);
		$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
		if($stmt->execute()){
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			return $RESULT;
		}
		else{
			return false;
		}
	}
	
	public function get_stanine_score($my_score,$testId,$bat_type,$gender,$testClmnName,$education_label){

		if($bat_type==1){
			
				if($education_label!='After 12th'){
						$education_label='After 10th';
					}
			
			$sql="SELECT stanine_value,percentile FROM tbl_stanine_mapping  WHERE battery_type=:battery_type AND education=:education AND gender=:gender AND $testClmnName<=:score  order by $testClmnName DESC LIMIT 1";
			
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_type',$bat_type, PDO::PARAM_INT);
			$stmt->bindValue(':education',$education_label,PDO::PARAM_STR);
			$stmt->bindValue(':gender',$gender, PDO::PARAM_INT);
			$stmt->bindValue(':score',$my_score,PDO::PARAM_INT);
			if($stmt->execute()){
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$RESULT= $RESULT[0];
		
			}
		
			return $RESULT;
		}
		elseif($bat_type==2){
			
			
			$sql="SELECT stanine_value,percentile FROM tbl_stanine_mapping  WHERE battery_type=:battery_type AND education=:education AND gender=:gender AND $testClmnName<=:score  order by $testClmnName DESC LIMIT 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_type',$bat_type, PDO::PARAM_INT);
			$stmt->bindValue(':education',$education_label,PDO::PARAM_STR);
			$stmt->bindValue(':gender',$gender, PDO::PARAM_INT);
			$stmt->bindValue(':score',$my_score,PDO::PARAM_INT);
			if($stmt->execute()){
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				
				$RESULT= $RESULT[0];
				if($RESULT['stanine_value']==""){
					
					$education_label='Global';
					$sql="SELECT stanine_value,percentile FROM tbl_stanine_mapping  WHERE battery_type=:battery_type AND education=:education AND gender=:gender AND $testClmnName<=:score  order by $testClmnName DESC LIMIT 1";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':battery_type',$bat_type, PDO::PARAM_INT);
					$stmt->bindValue(':education',$education_label,PDO::PARAM_STR);
					$stmt->bindValue(':gender',$gender, PDO::PARAM_INT);
					$stmt->bindValue(':score',$my_score,PDO::PARAM_INT);
					$stmt->execute();
					$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$stmt->closeCursor();
					$RESULT= $RESULT[0];
					
				}
			
				return $RESULT;
			
			}
		
		}
	
	
	
	}
	
	
	public function getAllUserTestScore($test_id,$batt_id=''){
		
		
		if($batt_id!=''){
		
			$sql="SELECT DISTINCT user_id FROM tbl_test_complete_status where battery_id=:battery_id and test_id=:test_id and status='1'";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':battery_id',$batt_id,PDO::PARAM_INT);
			$stmt->bindValue(':test_id',$test_id,PDO::PARAM_INT);
		}
		else{
			
			$sql="SELECT DISTINCT user_id FROM tbl_test_complete_status where test_id=:test_id and status='1' and (battery_id='' or battery_id IS NULL)";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':test_id',$test_id,PDO::PARAM_INT);
		}
		
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($userArr,$row['user_id']);
		}
		$stmt->closeCursor();
       $scoreArr=array();
	   
	   foreach($userArr as $key=>$val){
		   if($batt_id!=''){
				$sql = "SELECT SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE  battery_id =:battery_id and test_id=:test_id and user_id=:user_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':battery_id',$batt_id, PDO::PARAM_INT);
				$stmt->bindValue(':test_id',$test_id, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$ttlCorrect=$RESULT[0]['ttlCorrect'];
				$scoreArr[]=$ttlCorrect;
		   }
		   else{
			 $sql = "SELECT SUM(`correct`) as ttlCorrect FROM temp_ans_push WHERE test_id=:test_id and user_id=:user_id and (battery_id='' or battery_id IS NULL)";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':test_id',$test_id, PDO::PARAM_INT);
				$stmt->bindValue(':user_id',$val, PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$ttlCorrect=$RESULT[0]['ttlCorrect'];
				$scoreArr[]=$ttlCorrect;  
			   
		   }
		   
	   }
	   
	  return $scoreArr;
	
	}
	
	public function updateStanine($uid,$stanine_score,$percentile,$testId,$battId=''){
		
		////Adding stanine score
		$sql = "SELECT id FROM tbl_stanine_score WHERE user_id=:user_id and test_id=:test_id and battery_id=:battery_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
        $stmt->bindValue(':test_id', $testId, PDO::PARAM_INT);
        $stmt->bindValue(':battery_id', $battId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$id= $RESULT[0]['id'];
		if($id!=""){
			
			$sql = "UPDATE tbl_stanine_score SET stanine_score='$stanine_score',percentile='$percentile' where id=$id";
			
		}
		else{
			$sql = "INSERT INTO tbl_stanine_score(user_id,test_id,battery_id,stanine_score,percentile) VALUES('$uid', '$testId','$battId','$stanine_score','$percentile')";	
		}
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();
	
	
	
	
	}
	
	
	public function getStanineDtl($uid,$test_id,$batt_id){
		
		$sql = "SELECT * FROM tbl_stanine_score WHERE user_id=:user_id and test_id=:test_id and battery_id=:battery_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id',$uid, PDO::PARAM_INT);
        $stmt->bindValue(':test_id',$test_id, PDO::PARAM_INT);
        $stmt->bindValue(':battery_id',$batt_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT[0];
	}
	
	public function getDistinctDatabyId($table,$distict_clmn,$clmname,$clmnval,$whr=''){
	
	
	$sql = "Select DISTINCT $distict_clmn from $table where $clmname =:clmnvalue  $whr order by $distict_clmn";
	$stmt = $this->dbConn->prepare($sql);	
	$stmt->bindValue(':clmnvalue',$clmnval, PDO::PARAM_STR);
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
		return $RESULT;
   }
   
   public function getStreamCourseId($career,$educationLabel,$education,$stream,$course){
	
	$course_ids=array();
	
	
	foreach($stream as $key=>$stream_val){
		
		foreach($course as $key1=>$course_val){
			
		$sql = "Select id from tbl_stream_course where type=:type and education_label=:education_label and education_eligiblity=:education_eligiblity and stream =:stream and course=:course";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->bindValue(':type',$career, PDO::PARAM_INT);
		$stmt->bindValue(':education_label',$educationLabel, PDO::PARAM_STR);
		$stmt->bindValue(':education_eligiblity',$education, PDO::PARAM_STR);
		$stmt->bindValue(':stream',$stream_val, PDO::PARAM_STR);
		$stmt->bindValue(':course',$course_val, PDO::PARAM_STR);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$id=$RESULT[0]['id'];
		if($id!=""){
			array_push($course_ids,$id);
		}
		}
	}
	return $course_ids;

  }
	
	
public function getUserEdge($career,$education,$stream,$course){
	
	$course_ids=array();
	$whr= " and type=$career and education_eligiblity='$education'";
	
	foreach($stream as $key=>$stream_val){
		
		foreach($course as $key1=>$course_val){
			
		 $sql = "Select id from tbl_stream_course where stream = '$stream_val' and course='$course_val' $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		 $id=$RESULT[0]['id'];
		if($id!=""){
			array_push($course_ids,$id);
		}
		}
	}
	return $course_ids;

  }
	
public function getUserCourseId($uid){
		
		$sql = "SELECT course_id FROM user WHERE user_id=:user_id";	
		$stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$RESULT =$RESULT[0];
		return $course_id =$RESULT['course_id'];
	}
	

}

?>