
<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);

require_once __DIR__ . '/serviceController.php';

function userLogin($username,$password,$client_name){
	
  try{	
		$con = createConnection();
		
		$query1 = "SELECT user_id,roll_no from user where email_id=?";

		$stmt1 = $con->prepare($query1);
		$stmt1->bind_param("s",$username);
		$stmt1->bind_result($userId,$roll_no);
		$stmt1->execute();
		$stmt1->fetch();
		$stmt1->close();
		//file_put_contents("test/roll.txt",$login." == ".$roll_no);
		if(!empty($roll_no)){
			$username=$roll_no;
		}else{
			$username=$username;
		}	
		//var_dump($con); die();WHERE uc.loginid = ? AND uc.password = ? AND urm.is_active = 1 AND 
		$stmt = $con->prepare("SELECT uc.user_id, role_definition_id, uc.is_active,uc.expiry_date,urm.user_group_id, us.user_client_id,us.firstTime_login FROM user_credential uc
								JOIN user_role_map urm ON urm.user_id = uc.user_id 
								JOIN user us ON us.user_id = uc.user_id
								WHERE uc.loginid = ? AND uc.password = ? AND (urm.role_definition_id = 4 || urm.role_definition_id = 3 || urm.role_definition_id = 2 || urm.role_definition_id = 5  || urm.role_definition_id = 7  || urm.role_definition_id = 1)");
		$stmt->bind_param("ss",$username,$password);
		$stmt->execute();
		$stmt->bind_result($user_id, $roleId, $is_active,$expiry_date, $user_group_id,$client,$firstTime_login);
		$stmt->fetch();
		$stmt->close();	
		$user = new stdClass();
		$user->is_active = $is_active;
		$user->user_group_id = $user_group_id;
		$user->user_id = $user_id;
		$user->roleId = $roleId;
		$user->client = $client;
		$user->client_name = $client_name;
		$user->firstVisit = $firstTime_login;
		$user->expiry_date = $expiry_date;
		//if($user->is_active==1){
		
			if($roleId==2){
			
				$serviceObj = new serviceController();
				$params = new stdClass();
				$params->login = $username;
				$params->password = $password;
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;
				//echo "<pre>";print_r($params);//exit;
				$res = $serviceObj->processRequest('', 'login', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);exit; 
				if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retVal = $res['retVal'];
					if( isset($retVal['token']) ){
					  $user->token= $retVal['token'];
					  $user->user_id= $retVal['user_id'];
					 }
					//echo "<pre>";print_r($user->token);
					$params_visit = new stdClass();
					$params_visit->user_id = $user->user_id;
					$params_visit->role = 'learner';
					$params_visit->date = date('Y-m-d');
					$params_visit->date_with_time = date('Y-m-d H:i:s');
					$params_visit->event = '';
					$params_visitArr=array();
					$params_visitArr[]=$params_visit;
					$params->visit_data=$params_visitArr;
					
					
					$res1 = $serviceObj->processRequest($user->token,'visiting_user', $params);
					$res_json1 = json_encode($res1);
					$res1 = json_decode($res_json1, true);
					//echo "<pre>";print_r($res1);exit;
					if(strcasecmp($res1['retCode'], 'SUCCESS') == 0 && count($res1['retVal']) ){

						return true;
				   }
					$cTime=getTime();
					$user->cTime=$cTime;
                    $checkUserIp=userIpTrack($user->user_id);
					if($checkUserIp){
					  $user->ip_address=$checkUserIp;
					}else{
					  $user->ip_address='';
					}
					//echo "<pre>";print_r($checkUserIp); exit;	
						
				   $checkCurrentCourseVisit=currentCourseVisit($user->user_id);
					if($checkCurrentCourseVisit){
					  $user->checkCourseVisit='complete';
					}else{
					  $user->checkCourseVisit='done';
					}
				
				   return $user;
				}else{
					 return false;
				}
				
			  
			}
				
			  if($user->user_id!=''){
				$checkUserIp=userIpTrack($user->user_id);
				if($checkUserIp){
				  $user->ip_address=$checkUserIp;
				}else{
				  $user->ip_address='';
				}
				
			  if($roleId==1){	
				   $checkCurrentCourseVisit=currentCourseVisit($user->user_id);
					if($checkCurrentCourseVisit){
					  $user->checkCourseVisit='complete';
					}else{
					  $user->checkCourseVisit='done';
					}
				}	
				
			//echo "<pre>";print_r($user); exit;	
		  closeConnection($con);
		  return $user;
	
		}
		
		/* }else{
			//$user->is_active=0;
		   return $user;	
		} */
		
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}



function userdetails($user_id){
	try{	
		$con = createConnection();
		$stmt = $con->prepare("SELECT  c.client_id, u.first_name,u.last_name, u.date_of_birth ,rd.role_definition_id, rd.name roleName , u.email_id, u.address_id, u.profile_pic,u.user_from,urm.user_group_id,u.user_client_id,u.firstTime_login
								FROM user u
								JOIN user_role_map urm ON urm.user_id = u.user_id
								JOIN role_definition rd ON rd.role_definition_id = urm.role_definition_id
								LEFT JOIN client c ON c.user_group_id = urm.user_group_id
								WHERE u.user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($client_id,$first_name , $last_name, $date_of_birth,$role_definition_id, $roleName , $email_id,$address_id,$profile_pic,$user_from,$user_group_id,$user_client_id,$firstTime_login);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$stmt = $con->prepare("SELECT u.gender, am.phone,am.country FROM user u  JOIN address_master am ON am.address_id = u.address_id
								WHERE u.user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($gender,$phone,$country);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		
		$stmt = $con->prepare("SELECT  a.system_name FROM user u JOIN asset a ON a.asset_id = u.profile_pic JOIN address_master am ON am.address_id = u.address_id
								WHERE u.user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($system_name);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		
		
		$stmt = $con->prepare("SELECT  center_id FROM user_center_map WHERE user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($center_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		if($role_definition_id==7){
			$stmt = $con->prepare("Select region_id from tblx_region_user_map where user_id = ?");
			$stmt->bind_param("i",$user_id);
			$stmt->bind_result($region);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}else{
			
			$stmt = $con->prepare("Select name as center_name,description,status,region,proctoring,exam_date from tblx_center where center_id = ?");
			$stmt->bind_param("i",$center_id);
			$stmt->bind_result($center_name,$center_description,$status,$region,$proctoring,$exam_date);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		
		$stmt = $con->prepare("Select loginid,is_active,expiry_date from user_credential where user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($loginid,$is_active,$expiry_date);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$stmt = $con->prepare("Select user_ip from tblx_user_ip_track where user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($user_ip);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$user = new stdClass();
		$user->client_id = $client_id;
		$user->center_id =$center_id;
		$user->center_name =$center_name;
		$user->user_id = $user_id;
		$user->first_name = $first_name;
		$user->last_name = $last_name;
		$user->date_of_birth = $date_of_birth;
		$user->roleName = $roleName;
		$user->email_id = $email_id;
		$user->address_id = $address_id;
		$user->profile_pic = $profile_pic;
		$user->user_from = $user_from;
		$user->user_group_id = $user_group_id;
		$user->gender = $gender;
		$user->phone = $phone;
		$user->country = $country;
		$user->system_name = $system_name;
		$user->user_client_id = $user_client_id;
		$user->firstTime_login = $firstTime_login;
		$user->is_active = $is_active;
		$user->expiry_date = $expiry_date;
		$user->loginid = $loginid;
		$user->center_description = $center_description;
		$user->center_status = $status;
		$user->region = $region;
		$user->ip_address = $user_ip;
		$user->proctoring = $proctoring;
		$user->exam_date = $exam_date;
		
		closeConnection($con);
		//echo "<pre>";print_r($user);exit;
		return $user;
		
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

function centerDetails($user_id){
	try{
		$con = createConnection();
		
		$stmt = $con->prepare("SELECT  center_id FROM user_center_map WHERE user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($center_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		 
		$obj = new stdClass();
		$obj->client_id = $client_id;
		$obj->center_id =$center_id;
		closeConnection($con);
		//echo "<pre>";print_r(center);exit;
		return $obj;
		
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}
function regionDetails($user_id){
	try{
		$con = createConnection();
		
		$stmt = $con->prepare("SELECT  region_id FROM tblx_region_user_map WHERE user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($region_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		 
		$obj = new stdClass();
		$obj->region_id =$region_id;
		closeConnection($con);
		//echo "<pre>";print_r(center);exit;
		return $obj;
		
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}


function getUserIdByClientId($client_id){
  try{
		$con = createConnection();
		
		$stmt = $con->prepare("SELECT  u.user_id
								FROM user u
								JOIN user_role_map urm ON urm.user_id = u.user_id
								JOIN role_definition rd ON rd.role_definition_id = urm.role_definition_id
								JOIN client c ON c.user_group_id = urm.user_group_id
								WHERE c.client_id = ?");			
		$stmt->bind_param("i",$client_id);
		$stmt->execute();
		$stmt->bind_result($user_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		closeConnection($con);
		
		//echo $user_id;exit;
		return $user_id;
		
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

function clientDetails($center_id){
	try{
		$con = createConnection();
		
		$stmt = $con->prepare("SELECT  client_id FROM tblx_center WHERE center_id = ?");
		$stmt->bind_param("i",$center_id);
		$stmt->bind_result($client_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		 
		$obj = new stdClass();
		$obj->client_id = $client_id;
		closeConnection($con);
		//echo "<pre>";print_r(center);exit;
		return $obj;
		
     }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

function getDefaultbatch($center_id){
	try{
		$con = createConnection();
		
		$stmt = $con->prepare("SELECT batch_id,batch_code,batch_name,status FROM  tblx_batch WHERE center_id= ?");
		$stmt->bind_param("i",$center_id);
		$stmt->bind_result($batch_id,$batch_code,$batch_name,$status);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$obj = new stdClass();
		$obj->batch_id = $batch_id;
		$obj->batch_code =$batch_code;
		$obj->batch_name =$batch_name;
		$obj->status =$status;
		closeConnection($con);
		//echo "<pre>";print_r($obj);exit;
		return $obj;
	  
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}


//============= REg Student  default center and batch B2C methods
	
  function userReg( array $request){
	  
	try{
	    $con = createConnection();
		$center_id=B2C_CENTER;
		
		$client=clientDetails($center_id);
		$client_id=$client->client_id;

		$batch_id=getDefaultbatch($center_id);
		$batch=$batch_id->batch_id;	
		//echo "<pre>";print_r($batch);exit;
		 
		if($batch){
	
				$role_type="2";//student/learner
				
				$first_name = isset($request['reg_name']) ? $request['reg_name'] : "";
				$email = isset($request['reg_email']) ? trim($request['reg_email']) : "";
				$is_email_verified ='0';
				$phone = isset($request['reg_mobile']) ? trim($request['reg_mobile']) : "";
				$is_phone_verified = '0';
				$country_code = isset($request['country_code']) ? trim($request['country_code']) : "";
				$password = isset($request['reg_password']) ? trim($request['reg_password']) : "";
				
			   //echo "<pre>";print_r($email);exit;

				//// Now Adding  user address 
				$stmt = $con->prepare("INSERT INTO address_master(phone,is_phone_verified,country_code,updated_by,created_date) VALUES(?,?,?,1,NOW())");
				$stmt->bind_param("sss",$phone,$is_phone_verified,$country_code);
				// echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$address_id =$con->insert_id;
				$stmt->close();  

				
				  //// Now Adding  Assest 
				 $stmt = $con->prepare("INSERT INTO asset(updated_by,created_date) VALUES(1,NOW())");
				//echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$profile_pic = $con->insert_id;
				$stmt->close();
				
				
				$stmt= $con->prepare("insert into user(first_name,email_id,is_email_verified,address_id,profile_pic,updated_by,user_client_id,created_date) values(?,?,?,?,?,1,?,NOW())");
				
				$stmt->bind_param('sssiii', $first_name,$email,$is_email_verified,$address_id,$profile_pic,$client_id);
				//echo "<pre>user";print_r($stmt);exit;
				$stmt->execute();
				$user_id =$con->insert_id;
				$stmt->close();

				//// Adding user and center map 
				$stmt = $con->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values(?,?,?,NOW())");
				//echo "<pre>";print_r($stmt);exit;
				$stmt->bind_param("iii", $user_id,$center_id,$client_id);
				$stmt->execute();
				$stmt->close(); 
				
				//// Adding Admin Credentials 
				$stmt= $con->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(?,?,?,1,NOW())");
				//echo "<pre>";print_r($stmt);exit
				$stmt->bind_param("iss",$user_id, $email,$password);
				$stmt->execute();
				$stmt->close(); 
				
				////Select the client to user group id */
				$stmt = $con->prepare("Select user_group_id from client WHERE client_id=?");
					//echo "<pre>";print_r($stmt);exit;
				$stmt->bind_param("i",$client_id);
				$stmt->execute();
				$stmt->bind_result($user_group_id);
				$stmt->fetch();
				$stmt->close();
				$client_group_id = $user_group_id;
				
				//// Adding user into role map group 
				$stmt = $con->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(?,?,?,1,1,NOW())");
				//echo "<pre>";print_r($stmt);exit;
				$stmt->bind_param("iii",$user_id,$role_type,$client_group_id);
				$stmt->execute();
				$stmt->close(); 
				
				// For batch_user_map table insert
				
				$stmt = $con->prepare("insert into tblx_batch_user_map (user_id, batch_id, center_id,status) values (?,?,?,1)");
				$stmt->bind_param("iii",$user_id,$batch,$center_id);
				$stmt->execute();
				$stmt->close(); 
				closeConnection($con); 
				return true;
		
			}else{
				return false;
			   }
		   
		}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	
  }
	
	
//============= Student  first log 
	
  function firstTimeLogin($firstlog,$userId){
	  
	try{
	    $con = createConnection();
		if($userId){
	
				$role_type="2";//student/learner

				$stmt= $con->prepare("update user SET  firstTime_login= ? WHERE user_id = ?");
				$stmt->bind_param('si',$firstlog,$userId);
				//echo "<pre>user";print_r($stmt);exit;
				$stmt->execute();
				$stmt->close();
				 closeConnection($con);
				return true;
		
			}else{
				return false;
			 }
		
		}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	
  }
	
  function selectfirstTimeLogin($userId){
	  
	try{
	    $con = createConnection();
		if($userId){
	
				$role_type="2";//student/learner

				$stmt= $con->prepare("select firstTime_login from user WHERE user_id = ?");
				$stmt->bind_param('i',$userId);
			    $stmt->bind_result($firstlog);
				$stmt->execute();
		        $stmt->fetch();
		        $stmt->close();
				closeConnection($con);
				return $firstlog;
		
			}else{
				return false;
			}
		 
		}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	
  }	


function getPassword($email){
  try{	
		$email=trim($email);
		$con = createConnection();
		$stmt = $con->prepare("SELECT uc.password,uc.is_active FROM user_credential uc
								JOIN user u ON u.user_id = uc.user_id
								WHERE u.email_id = ?");
		$stmt->bind_param("s",$email);
		$stmt->execute();
		$stmt->bind_result($password, $is_active);
		$stmt->fetch();
		$stmt->close();	
		
		if($password !="" && $is_active=='1'){
		
			// require_once('forgot_password_mailer.php');
			 	//echo $password;exit;
			/* $to = $email;
			$subject = "Forgot password";
			$message = "Your password is : $password";
			$mail = sendMailer($email, $subject, $message);//fn path library/phpMailer/mail.php */
			// send email
			//return $mail;
			closeConnection($con);
			return true;
		}
		else{
			return 'inValidE';
		}
		
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
  }
  
  
  function getUserRefreshToken($email,$userId) {
	  
	  try{	
		$email=trim($email);
		$con = createConnection();
		$stmt = $con->prepare("SELECT uc.loginid, uc.password,uc.is_active FROM user_credential uc
								JOIN user u ON u.user_id = uc.user_id
								WHERE u.user_id = ?");
		$stmt->bind_param("s",$userId);
		$stmt->execute();
		$stmt->bind_result($loginid,$password, $is_active);
		$stmt->fetch();
		$stmt->close();	
		$tokenArr = new stdClass();
		if($password !=""){
		//if($password !="" && $is_active=='1'){
			$serviceObj = new serviceController();
			$params = new stdClass();
			$params->login = $loginid;
			$params->password = $password;
			$params->client = CLIENT_NAME;// $client name;
			$params->class_name = CLIENT_NAME;// $client name;
			$params->platform = WEB_SERVICE_PLATFORM;
			$params->deviceId = WEB_SERVICE_DEVICE_ID;
			$params->appVersion = WEB_SERVICE_APP_VERSION;
			//echo "<pre>";print_r($params);
			$res = $serviceObj->processRequest('', 'refreshtoken',$params);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			//echo "<pre>";print_r($res);
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				
				$retVal = $res['retVal'];
				if( isset($retVal['token']) ){
				  $tokenArr->token= $retVal['token'];
				  $tokenArr->user_id= $retVal['user_id'];
				}
			}
			closeConnection($con);
		  return $tokenArr;

		}
		
		
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}

  }
  
  
 function checkCourseLevelVisitByUserToken($uToken) {
	  
	  try{	
		    $serviceObj = new serviceController();
			$res = $serviceObj->processRequest($uToken, 'get_visited_course', $params);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			$courseVisitArr = new stdClass();
			
			if(!empty($res)){
				 if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					
					$retVal = $res['retVal'];
					if( isset($retVal['user_id']) ){
					   $courseVisitArr->user_id= $retVal['user_id'];
					   $courseVisitArr->user_start_level= $retVal['user_start_level'];
					   $courseVisitArr->user_current_level= $retVal['user_current_level'];
					  }
				}
				closeConnection($con);
		        return $courseVisitArr;	
			}else{
				closeConnection($con);
			  return $res;	
			} 
		
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}


  }
   
  
function getTime(){
  try{	
	
		$con = createConnection();
		$stmt = $con->prepare("SELECT NOW()");
		$stmt->execute();
		$stmt->bind_result($cTime);
		$stmt->fetch();
		$stmt->close();	
		closeConnection($con);
		
		if($cTime !="" ){
			return $cTime;
		}
		else{
			return false;
		}
		
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
  }
  function currentCourseVisit($user_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT id from tbl_user_visited_course where user_id=?");
		$stmt->bind_param("i",$user_id);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		$stmt->close();
     if(empty($id)){
			$stmt= $con->prepare("insert into tbl_user_visited_course (user_id, user_start_level, user_current_level,date_attempted) values(?,?,?,NOW())");
			$startLevel=1;
			$currentLevel=1;
			$stmt->bind_param("iss",$user_id,$startLevel,$currentLevel);
			$stmt->execute();
			$stmt->close();
			 closeConnection($con);
			return 1;
		}else{
			return 0;
		}
	
  } 
  
  function getComponentScore($user_id,$course_id,$topic_edge_id){
	  try{
		$con = createConnection();
		
		$stmt = $con->prepare("select id,score,attempted_date,modified_date from tblx_user_score where user_id=? and course_id=? and topic_id=?");
        $stmt->bind_param("iii",$user_id,$course_id,$topic_edge_id);
        $stmt->bind_result($id,$getScore,$attempted_date,$modified_date);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
		
		$obj = new stdClass();
		if(!empty($getScore)){
			
			//$score_date ='';
			$score_date = $modified_date;	
			$obj->getScore = $getScore;
			$obj->score_date = $score_date;
			
		}else{
			$obj->getScore = 0;
			$obj->score_date = '';
		}
		
		closeConnection($con);
		return $obj;
		
		
	}//catch exception
	catch(Exception $e) {
	  echo 'Message: ' .$e->getMessage();exit;
	}
  }
  
  function userIpTrack($user_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT id from tblx_user_ip_track where user_id=?");
		$stmt->bind_param("i",$user_id);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		$stmt->close();
		$ip_addres=WEB_SERVICE_DEVICE_ID;
     if(empty($id)){
			$stmt= $con->prepare("insert into tblx_user_ip_track ( user_id, user_ip) values(?,?)");
			
			$stmt->bind_param("is",$user_id,$ip_addres);
			$stmt->execute();
			$stmt->close();
			 closeConnection($con);
			return $ip_addres;
		}else{
			$stmt= $con->prepare("update tblx_user_ip_track set user_ip=? where user_id=?");
			
			$stmt->bind_param("si",$ip_addres,$user_id);
			$stmt->execute();
			$stmt->close();
			 closeConnection($con);
			return $ip_addres;
		}
	
  }
function milliseconds() {
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}  
  
?>