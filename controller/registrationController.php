<?php 
require_once __DIR__ . '/serviceController.php';
class registrationController {
    
    public $con;
    
    private $appVersion;
    private $isCode;
    private $platform;
    private $deviceID;
    private $deviceType;
    private $centerId;
	 
    public function __construct() {
       $this->dbConn = DBConnection::createConn();
        
        $this->appVersion = WEB_SERVICE_APP_VERSION;
        $this->isCode = 1;
        $this->platform = WEB_SERVICE_PLATFORM;
        $this->deviceID = WEB_SERVICE_DEVICE_ID;
        //$this->deviceType = WEB_SERVICE_DEVICE_TYPE;
		$this->centerId = B2C_CENTER;
    }

	public function clientDetails($center_id){
		try{
			
			$sql="SELECT client_id FROM tblx_center WHERE center_id =:center_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT );
			$client_id = $row['client_id'];
			$stmt->closeCursor();
			//echo "<pre>";print_r($client_id);exit;
			if( !empty($client_id) ){
				return $client_id;
			}
             return false;
           
		 }//catch exception
			catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
	}
	public function centerDetails($region_id){
		try{
			
			$sql="SELECT center_id FROM tblx_center WHERE region =:region_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT );
			$center_id = $row['center_id'];
			$stmt->closeCursor();
			//echo "<pre>";print_r($center_id);exit;
			if( !empty($center_id) ){
				return $center_id;
			}
             return false;
           
		 }//catch exception
			catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
	}

	public function getDefaultbatch($center_id){
		try{
			$sql="SELECT batch_id,batch_code,batch_name,status FROM  tblx_batch WHERE center_id=:center_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT );
			$batch_id = $row['batch_id'];
			$stmt->closeCursor();
			//echo "<pre>";print_r($RESULT);exit;
			//echo "<pre>";print_r($batch_id);exit;
			if( !empty($batch_id) ){
				return $batch_id;
			}
           return false;
           
			
			/* $obj = new stdClass();
			$obj->batch_id = $batch_id;
			$obj->batch_code =$batch_code;
			$obj->batch_name =$batch_name;
			$obj->status =$status; 
			
			//echo "<pre>";print_r($obj);exit;
			return $obj;*/
		}//catch exception
			catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
	}
	
   public function regGenerateOTP($arr){
      
        $email = strtolower(trim($arr['reg_email']));
        $mobile = $arr['reg_mobile'];
		
        if( strlen($mobile) < 1){
            //return array('status' => 0, 'msg' => 'Mobile is empty.') ;
        }
        
        if( strlen($email) < 1){
            return array('status' => 0, 'msg' => 'Email is empty.') ;
        }
        
        if( ! filter_var($email, FILTER_VALIDATE_EMAIL )){
            return array('status' => 0, 'msg' => 'Invalid email.') ;
        }

			
		$serviceObj = new serviceController();
		$params = new stdClass();
		$params->user_email = $email;
		$params->user_phone = $mobile;
		$params->user_action = 'registration';
		
		$params->client = CLIENT_NAME;// $client name;
		$params->class_name = CLIENT_NAME;// $client name;
		$params->platform = WEB_SERVICE_PLATFORM;
		$params->deviceId = WEB_SERVICE_DEVICE_ID;
		$params->appVersion = WEB_SERVICE_APP_VERSION;
		//echo "<pre>";print_r($params);exit;
		
		$extra='';
		$res = $serviceObj->processRequest('', 'generateOTP', $params, $extra);
		//echo "<pre>";print_r($res);exit;
		$res_json = json_encode($res);
		$res = json_decode($res_json, true);
		if (isset($res) && ($res['retCode'] == 'SUCCESS' ) ) {

			$expires_on = $res['retVal']['expires_on'];
			$res = array('status' => 1, 'expires_on' => $expires_on);
		}else if (isset($res) && ($res['retCode'] == 'FAILURE' ) ) {
			$msg = $res['retVal']['msg'];
			$res = array('status' => 0, 'msg' => $msg);
			//echo "ff<pre>";print_r($res);exit;
			
		}else{
			$res = array('status' => 0, 'msg' => 'There was some error in registration. Please try again.');
		 //  echo "<pre>";print_r($res);exit;
		}
	   return $res;
 
   }
    
   public function regVerifyOTP($userotp,$arr){
	   //echo $userotp;
	  // echo "<pre>";print_r($arr);exit;
        $email = strtolower(trim($arr['reg_email']));
        $mobile = $arr['reg_mobile'];
        if( strlen($mobile) < 1){
            //return array('status' => 0, 'msg' => 'Mobile is empty.') ;
        }
        
        if( strlen($email) < 1){
            return array('status' => 0, 'msg' => 'Email is empty.') ;
        }
        
        if( ! filter_var($email, FILTER_VALIDATE_EMAIL )){
            return array('status' => 0, 'msg' => 'Invalid email.') ;
        }

			
		$serviceObj = new serviceController();
		$params = new stdClass();
		$params->user_email = $email;
		$params->user_phone = $mobile;
		$params->user_otp = $userotp;
		$params->user_action = 'registration';
		
		$params->client = CLIENT_NAME;// $client name;
		$params->class_name = CLIENT_NAME;// $client name;
		$params->platform = WEB_SERVICE_PLATFORM;
		$params->deviceId = WEB_SERVICE_DEVICE_ID;
		$params->appVersion = WEB_SERVICE_APP_VERSION;
		//echo "<pre>";print_r($params);exit;
		
		$extra='';
		$res = $serviceObj->processRequest('', 'verifyOTP', $params, $extra);
	//	echo "<pre>";print_r($res);exit;
		$res_json = json_encode($res);
		$res = json_decode($res_json, true);
		if (isset($res) && ($res['retCode'] == 'SUCCESS' ) ) {
			$msg = $res['retVal']['msg'];
			$res = array('status' => 1, 'msg' => $msg);
		}else if (isset($res) && ($res['retCode'] == 'FAILURE' ) ) {
			$msg = $res['retVal']['msg'];
			$res = array('status' => 0, 'msg' => $msg);
		}else{
			$res = array('status' => 0, 'msg' => 'There was some error in registration. Please try again.');
		}
	   return $res;
 
   }
	
    public function register($is_email_verified,$is_otp_based,$arr){
	     $first_name = $arr['reg_name'];
         $last_name = '';
        $email = strtolower(trim($arr['reg_email']));

		if(!isset($arr['user_email']) && empty($arr['user_email']))
		{
		$user_email_id =  $email;
		}
		else
		{
		$user_email_id = trim($arr['user_email']);
		}
        
		if($arr['region_id']==5){
		  $roll_no = $arr['roll_no']?$arr['roll_no']:null;
		}else{
			$roll_no=null;
		}
		
		$mobile = $arr['reg_mobile'];
		$country_code = $arr['country_code'];
        $password = $arr['reg_password'];
		$ukuser ='';// $arr['ukuser'];
		
        if( strlen($first_name) < 1 && strlen($last_name) < 1){
            //return array('status' => 0, 'msg' => 'Name is empty.') ;
        }
        
        if( strlen($last_name) < 1){
            //return array('status' => 0, 'msg' => 'Last name is empty.') ;
        }
        
        if( strlen($password) < 1){
            //return array('status' => 0, 'msg' => 'Password is empty.') ;
        }
        
        if( strlen($mobile) < 1){
            //return array('status' => 0, 'msg' => 'Mobile is empty.') ;
        }
        
        if( strlen($email) < 1){
            //return array('status' => 0, 'msg' => 'Email is empty.') ;
        }
        
        if( ! filter_var($email, FILTER_VALIDATE_EMAIL )){
           // return array('status' => 0, 'msg' => 'Invalid email.') ;
        }
		
		$region_id=$arr['region_id'];
		echo $center_id=$this->centerDetails($arr['region_id']);
        $center_id = ($arr['center_id']!='')?$arr['center_id']:$center_id;
		echo $batch_id=$this->getDefaultbatch($center_id);
        $batch_id =  ($arr['batch_id']!='')?$arr['batch_id']:$batch_id;
		
        
        $first_name = trim($first_name);
        $last_name = trim($last_name);
        $name = trim($first_name .' '. $last_name);
        $name_arr = explode(' ', $name);
        if( count($name_arr) > 1){
            $first_name = trim($name_arr[0]);
            //$last_name = $name_arr[1];
            $last_name = trim(substr($name, strlen($first_name)));
        }else{
            $first_name = trim($name_arr[0]);
            $last_name = '';
        }

	         $role_type="2";//student/learner
			
			$serviceObj = new serviceController();
			$params = new stdClass();
			$params->email_id = $email;
			$params->user_email_id = $user_email_id;
			$params->is_email_verified=$is_email_verified;
			$params->roll_no=$roll_no;
			$params->first_name = $first_name;
			$params->last_name = $last_name;
			$params->mobile = $mobile;
			$params->is_phone_verified=0;
			$params->is_otp_based = $is_otp_based;
			$params->country_code = $country_code;
			$params->password = $password;
			$params->ukuser = $ukuser;

			//echo $params->ukuser;exit;
			
		    
			
			$params->batch_id = $batch_id;
			$params->center_id = $center_id;
			$params->region_id = $region_id;
			$params->role_type=$role_type;
			$params->client = CLIENT_NAME;// $client name;
			$params->class_name = CLIENT_NAME;// $client name;
			$params->platform = WEB_SERVICE_PLATFORM;
			$params->deviceId = WEB_SERVICE_DEVICE_ID;
			$params->appVersion = WEB_SERVICE_APP_VERSION;
			//echo "<pre>";print_r($params);exit;
			
			$extra='';
			$res = $serviceObj->processRequest('', 'register', $params, $extra);
			//echo "<pre>dff";print_r($res);exit;
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			//echo "<pre>";print_r($res);//exit;
			if (isset($res) && ($res['retCode'] == 'SUCCESS' ) ) {
				
				$msg = $res['retVal']['msg'];
				$token = $res['retVal']['token'];
				$name = $res['retVal']['name'];
				$user_id = $res['retVal']['user_id'];
				$res = array('status' => 1, 'msg' => $msg,'token' => $token,'name' => $name,'user_id' => $user_id);
			}else if (isset($res) && ($res['retCode'] == 'EXISTS' ) ) {
				$msg = $res['retVal']['msg'];
				$res = array('status' => 0, 'msg' => $msg);
				//echo "ff<pre>";print_r($res);exit;
				
			}else{
				$res = array('status' => 0, 'msg' => 'There was some error in registration. Please try again.');
			 //  echo "<pre>";print_r($res);exit;
			}
		   return $res;
 
 
    }


	public function checkForExistingUser($user_loginid){
			
		$con = createConnection();
		$stmt = $con->prepare("SELECT user_id FROM user_credential where loginid=?");
		$stmt->bind_param("s",$user_loginid);
		$stmt->execute();
		$stmt->bind_result($user_id);
		$stmt->fetch();
		$stmt->close();
		return $user_id;
		
    }
    

}