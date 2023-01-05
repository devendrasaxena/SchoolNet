<?php
/* This file carries all decree handlers 
   Only the decrees in this file should be honored 
   All decree functions should be declared as decree_<decree_name>
*/

require_once  __DIR__."/configure.php";
  /*  error_reporting(E_ALL);
ini_set('display_errors',1); */     
//require_once  __DIR__."/aduro_connect.php";
require_once  __DIR__."/api_connect.php";

## DECREE : authLicenseKey
## Category 
##       Authentication of Client Code
## Input param
##       client_code  : varchar
## Output Obj
##       client_id
function decree_authLicenseKey($token, $param){
	$con = createConnection();
	$sr = new ServiceResponse("NO_ACTION", 0, null);
	if(isset($param->license_key) && $param->license_key != "" && strlen($param->license_key) >= 10){		
		if($param->cur_ser == 'qa'){
			global $serviceQaURL;
			$request = curl_init($serviceQaURL);
		}else{
			global $serviceProdURL;
			$request = curl_init($serviceProdURL);
		}
		
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'aduroRegister', 'unique_code' => $param->license_key, 'code' => $param->code));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($request);
		curl_close($request);
		$res = json_decode($resp);
		//error_log('lic ressssssssssssssssssssssssssssssssssssssss=============='.json_encode($res));
		if($res->STATUS == 'FAILURE'){
			$sr->setCode("FAILURE");
			$sr->setStat(0);
		}else{
			//error_log('====================FINAL-RESULT'.json_encode($res));
			$license_key = aduroAuthCentralLicenseKey($con, $res, $param->license_key);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
			$obj = new stdclass();
			$obj->data = $license_key;
			$sr->setVal($obj);
		}
		//exit;
	}
	
	closeConnection($con);
    return $sr;
}

## DECREE : createCenter
## Makes a new center/school
# Category 
##      Center Creation
## Input Param
##      center_name : varchar
##      center_address : address object
##      user_full_name : varchar
##      email_id : varchar
##      user_mobile : varchar
##      center_phone : varchar	
## Output param
##      center_id
function decree_createCenter($token, $param){
	$con = createConnection();
	$sr = new ServiceResponse("NO_ACTION", 0, null);
	//error_log("center registration::::::::::::::::::::".json_encode($param));
	$center_code = aduroCreateCenter($con, $param);	
	if($center_code){
		$sr->setCode("SUCCESS");
		$sr->setStat(1);
		$obj = new stdclass();
		$obj->data = $center_code;
		$sr->setVal($obj);
	} else {
		$sr->setCode("FAILURE");
		$sr->setStat(0);
	}
	
	closeConnection($con);
    return $sr;
}

## DECREE : token
## Category 
##       Authentication
## Input param
##       login  : user login
##       password : use password 
## Output Obj
##       token
function decree_token($token, $param, $extraParam = array()) {
    /* To get a login token - valid for 4 hours */
    $login = $param->login;
	
    $password = $param->password;

  //  error_log(json_encode($param));
    /* Check the Database for validity */
    $con = createConnection();
    if($con == null)
       return 0;
    $ssid = aduroGetToken($con,$login,$password);
    closeConnection($con);
    $sr = new ServiceResponse("SUCCESS",1,$ssid);
   // error_log(json_encode($sr));
    return $sr;
}

## DECREE : login
## Category 
##       Authentication
## Input param
##       login  : user login
##       password : use password 
## Output Obj
##       token : the tokem for this session
##       name  : name of the user
function decree_login ($token, $param, $extraParams = array()) {
	    
	$alert_msg_arr = alertMessage();
    /* To get a login token - valid for 4 hours */ 
    $login = $param->login;
    $password = $param->password;
	$deviceId = $param->deviceId;
	$platform = $param->platform;
    file_put_contents("test/ch_login22.txt",$login."==".$password."==".$deviceId."==".$platform);
    
    /* Check the Database for validity */
    $con = createConnection();
    if($con == null)
       return 0;
	     $class_name=($extraParams['class_name']!='')?$extraParams['class_name']:$param->class_name;
		$client_id = getClientIdByClassName($class_name);
		if($client_id!==false)
			{
			 $source = $class_name;
			}
		else
			{ 	//file_put_contents("test/exp.txt",$class_name);
  
			return 0;
			}
		
		//file_put_contents("test/place_exp1.txt",$class_name);
  
		//file_put_contents("test/exp2.txt",$client_id);
  
		
		//new function for login
		$ssid = apiLogin($con, $login, $password, $deviceId,$platform,$class_name,$client_id);

		if(!isset($ssid) || $ssid == null  || $ssid == 'INACTIVE_USER')
			{
				$sr = new ServiceResponse("LOGIN_FAILED",1,null);
				 if($ssid == 'INACTIVE_USER'){
						$sr->setCode("LOGIN_FAILED");
						$sr->retVal = new stdClass();
						$sr->retVal->msg = $alert_msg_arr['LOGIN_FAILED_FOR_INACTIVE_USER'];
						$sr->setStat(0);

					}	
					else{
						$sr->setCode("LOGIN_FAILED");
						$sr->setStat(0);
						$sr->retVal = new stdClass();
						$sr->retVal->msg = $alert_msg_arr['LOGIN_FAILED'];
					}
			}else{
				$sr = new ServiceResponse("SUCCESS",1,null);
				$sr->retVal->msg = "Logged in successfully";
				$sr->setCode("SUCCESS");
				$sr->setStat(1);
				$sr->setVal($ssid);
			}
   closeConnection($con);
	
    return $sr;
}

## DECREE : valtok
## Returns success if the token is still valid 
## Category 
##       Authentication
## Input param
##      none
## Output Obj
##      user_id
function decree_valtok ($token, $param, $extraParams = array()) {
    /* Validate the token and if it is still valid */
    $con = createConnection();
    if($con == null)
       return 0;
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
        $sr->setVal($user_id);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    } 
    closeConnection($con); 
    return $sr;
}

## DECREE : lsclass
## List future classes available for a specific module
## Category 
##       Live Class
## Input param
##       topic_ids  : array of topic ids
## Output Obj
##       user_state : 0=Has Not Bought
##                    1=Has Bought but not booked
##                    2=Has Booked and Class is in Future
##                    3=Has Booked and Class is in Past
##       classes : array 
##           title :  name of class
##           desc  : description
##           tname : teacher name
##           stime : start time
##           dur   : duration
##           booked : has been booked by this user
##           mxs    : number of total seats
##           avs    : number of available seats
##           recording_url : string, in case the user_state is 3

function decree_lsclass($token, $param, $extraParams = array()) {
    $con = createConnection();
    if($con == null)
       return 0;
    $user_id = tokenValidate($con,$token);
   // error_log( "$user_id is user_id\n");
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $max_count = ( !isset($param->max_count)) ? 5 : $param->max_count;
        if($param->topic_ids == null) {
            $sr->setCode("TOPIC_LIST_MISSING");
            return $sr;
        }
        $userState =  aduroGetUserStateOfLiveClasses($con,$user_id,$param->topic_ids);
        $liveClass = new LiveClassObject();
       // error_log("THE USER STATE IS $userState\n");
        switch($userState) {
            case 0 : 
                $liveClass  = aduroGetListOfLiveClasses($con,$user_id, $param->topic_ids, $max_count);
                break;
            case 1 :
                $liveClass  = aduroGetListOfLiveClasses($con,$user_id, $param->topic_ids, $max_count);
                break;
            case 2 :
                $liveClass  = aduroGetListOfLiveClasses($con,$user_id, $param->topic_ids, $max_count);
                break;
            case 3 :
                $liveClass = aduroGetBookedClass($con,$user_id, $param->topic_ids,null);
                break;
        }
        //$objRet  = aduroGetListOfLiveClasses($con,$user_id, $param->topic_ids, $max_count);
        /* No shopping flow at this point of time */
        if($userState==0)
            $userState = 1;
        $liveClass->user_state= $userState;
        $sr->setVal($liveClass);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    } 
     closeConnection($con); 
    return $sr;
}

## DECREE : bkclass
## Book a future class 
## Category 
##       Live Class
## Input param
##       class_id  : id of class to be booked
## Output Obj
##       none
function  decree_bkclass($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroBookClassForUser($con,$user_id, $param->class_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : cancelclass
## Cancel a future class 
## Category 
##       Live Class
## Input param
##       class_id  : id of class to be booked
## Output Obj
##       none
function  decree_cancelclass($token, $param, $extraParams = array()) {
    $con = createConnection();

	$user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroCancelClassForUser($con,$user_id, $param->class_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


## DECREE : track
## Insert tracking data
## Category 
##       Tracking
## Input param
##  array :
##      edge_id : edge_id of the leaf session 
##      start_date_ms : the start date in milliseconds since epoch
##      end_date_ms : the end date in milliseconds since epoch
## Output Obj
##      none
function decree_track($token, $param, $extraParams = array()) {
	$con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    
    if($user_id >=0) {

		foreach($param as $obj) {
				
				if($obj->end_date_ms != "" && $obj->edge_id != ""){
					$objRet  = trackCentralLicensingData($con,$user_id,$obj->edge_id,$obj->start_date_ms, $obj->end_date_ms, $obj->course_code,$obj->package_code, $extraParams['platform']);
					
				}
			}	
			$sr->setVal($objRet);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);		
        
			$objRet = getTrackcentralLicensingData($con,$user_id);
			$sr->setVal($objRet);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		
    } else {
        ////file_put_contents("test.txt","token expired");
		$sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : irpush
## Insert ir data
## Category 
##       Tracking
## Input param
##  array :
##      edge_id : edge_id of the module
##      ir      : ir of the user
## Output obj
##      none

function decree_irpush($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        foreach($param as $obj) {
            aduroPushIr($con,$user_id,$obj->edge_id,$obj->ir);
        }
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : irpull
## Get average IR data and maximum score till date 
## Category 
##       Tracking
## Input Param 
##     course_edge_id : edge id of course
## Output Object
##     maxir : maximum ir till date
##     avg_irs : array
##          edge_id : edge id of the module
##          avgir   : average ir
function decree_irpull($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroGetAverageIr($con,$user_id, $param->course_edge_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


## DECREE : getmoduleir
## Get module ids and the respective scores for a particular user
## Category 
##       Tracking
## Input Param 
##     none
## Output Object
##     module_irs : array
##          edge_id : edge id of the module
##          ir      : ir of the user
function decree_getmoduleir($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroRestoreIrForUser($con,$user_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : gettracking
## Get tracking records stored till date for user
## Category 
##       Tracking
## Input Param 
##     none
## Output Object
##     tracks : array
##              edge_id : edge_id of the leaf session 
##              start_date_ms : the start date in milliseconds since epoch
##              end_date_ms : the end date in milliseconds since epoch
function decree_gettracking($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroRestoreTrackingForUser($con,$user_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : pushanswer
## Push answers for an assessment for a user
## Category 
##       Assessment
## Input param
##      array 
##          date_ms  : milliseconds date when the answer was put
##          test_uniqid : uniq_id of the test
##          ques_uniqid : uniq id of the question
##          ans_uniqid  : uniq id of the answer chosen
## Output Object
##      none
function  decree_pushanswer($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    //error_log("questionAnswer extra param--------".json_encode($extraParams));
	//error_log("questionAnswerammmmmmmmmmmmmmmmmmmmmmm--------".json_encode($param));
	////file_put_contents('test/param.txt',$extraParams['unique_code']);
    if($user_id >=0) {

		/*if($user_id==921123 || $user_id==920708)
		{
		return true;
		}*/
			$userEdgeIdArr = array();
			
			if(isset($extraParams['appVersion']) && $extraParams['appVersion'] == 2 && $user_id!=920708){
				//error_log("in appVersion 22222222222222222222222222222222222222--------");
				foreach($param as $obj){
					if(!in_array($obj->test_uniqid, $userEdgeIdArr)){			
						$userEdgeIdArr[] = $obj->test_uniqid;
						aduroDetetePreAnsCL($con,$user_id, $obj->test_uniqid, $obj->package_code,$obj->course_code);
					}
					$objRet  = aduroCentralLicensingTrackAnswer($con,$user_id,$obj->test_uniqid,$obj->ques_uniqid,$obj->ans_uniqid,$obj->date_ms,$extraParams['platform'], $obj->package_code,$obj->course_code, $obj->essay_answer, $obj->av_media_files,$obj->user_response,$obj->correct);
				}
			
			}else{
				foreach($param as $obj){
					if(!in_array($obj->test_uniqid, $userEdgeIdArr)){			
						$userEdgeIdArr[] = $obj->test_uniqid;
						aduroDetetePreAns($con,$user_id, $obj->test_uniqid);
					}
				$objRet  = aduroTrackAnswer($con,$user_id,$obj->test_uniqid,$obj->ques_uniqid,$obj->ans_uniqid,$obj->date_ms,$extraParams['platform'], $extraParams['unique_code']);		
				}	
			}			
			
			$sr->setVal($objRet);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
			if(isset($extraParams['unique_code']) && $extraParams['unique_code'] != ''){				
				$objRet  = getAduroTrackAnswer($con,$user_id,$extraParams['unique_code']);
				////file_put_contents('test/ans.txt',json_encode($objRet));
				$sr->setVal($objRet);
				$sr->setCode("SUCCESS");
				$sr->setStat(1);
			}	
			if(isset($extraParams['appVersion']) && $extraParams['appVersion'] == 2){
				$objRet  = getAduroCentralLicensingTrackAnswer($con,$user_id);
				////file_put_contents('test/ans.txt',json_encode($objRet));
				$sr->setVal($objRet);
				$sr->setCode("SUCCESS");
				$sr->setStat(1);
			}	
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


function  decree_pushanswerattempt($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	file_put_contents('put_test/chk_user1.txt',$user_id);
	file_put_contents('put_test/chk_test1.txt',print_r($param,true));
    //error_log("questionAnswer extra param--------".json_encode($extraParams));
	
    if($user_id >=0) {
			$userEdgeIdArr = array();
			
			if(isset($extraParams['appVersion']) && $extraParams['appVersion'] == 2){
				$objRet = $param;
				$plateform = $extraParams['platform'];
				foreach($param as $att_obj){

					if($test_id!=$att_obj->test_uniqid || $attempt_id!=$att_obj->attempt_id){
						
						$test_id = $att_obj->test_uniqid;
						$attempt_id = $att_obj->attempt_id;
						$type_of_test = $att_obj->type_of_test;
						$package_code = $att_obj->package_code;
						$course_code = $att_obj->course_code;
						$quesArr = $att_obj->ques_arr;
						
						$no_of_ques = 0;
						$ttl_correct = 0;
						$ttl_incorrect = 0;
						$ttl_time_sp = 0;
						$score_per = 0;
						$avg_time_sp = 0;
						
						$objAtt = aduroGetMaxTestAttemptNo($con,$test_id,$user_id);
						$attempt_no = $objAtt->attempt_no;
						$last_score_id = $objAtt->id;
						$attempt_no = $attempt_no+1;
						foreach($quesArr as $obj){
							
							
							$objRet  = aduroCentralLicensingTrackAnswerAttempt($con,$user_id,$test_id,$obj->ques_uniqid,$obj->ans_uniqid,$obj->date_ms,$extraParams['platform'], $package_code,$course_code, $obj->essay_answer, $obj->av_media_files,$obj->user_response,$obj->correct,$attempt_no);
					
							$no_of_ques++;
							
							if($obj->ans_uniqid==1){
								$ttl_correct++;
							}else{
								$ttl_incorrect++;
							}
							
							$ttl_time_sp+=$obj->date_ms; 
						
						}
					
						$score_per = round(($ttl_correct/$no_of_ques)*100);
						
						$avg_time_sp = round(($ttl_time_sp/$no_of_ques),2);
					
						$course_code_Arr = explode('-',$course_code);
						$course_id =$course_code_Arr[1];
						//get category id
						$query = "SELECT tnct.category_id FROM tree_node_cat_master tnct
						JOIN tree_node_def tnd ON tnd.tree_node_category_id = tnct.category_id
						JOIN generic_mpre_tree gmt ON tnd.tree_node_id = gmt.tree_node_id
						WHERE gmt.edge_id = ? ";
						$stmt = $con->prepare($query);
						$stmt->bind_param("i",$test_id);
						$stmt->bind_result($category_id); 
						$stmt->execute();
						$stmt->fetch();
						$stmt->close();
						if($category_id == 5){
							$sessionType = 'AS';
							$chapter_edge_id = 0;
							$topic_edge_id =$test_id;
							$course_edge_id = getParentEdgeId($test_id);
						}else{
							$component_edge_id = $test_id;
							$actvity_edge_id = getParentEdgeId($component_edge_id);
							$chapter_edge_id = getParentEdgeId($actvity_edge_id);
							$topic_edge_id = getParentEdgeId($chapter_edge_id);
							$course_edge_id = getSuperRootEdgeId($topic_edge_id);
						}							
						
						
						
						aduroCentralLicensingTrackTest($con,$test_id,$course_id,$course_code,$course_edge_id,$topic_edge_id,$chapter_edge_id, $user_id, $no_of_ques, $ttl_correct, $ttl_incorrect, $score_per, $avg_time_sp, $ttl_time_sp, $type_of_test, $plateform,$unique_code,$attempt_no,$last_score_id);
				
					}
				}
			
			}		
			
			$sr->setVal($objRet);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
			
	} else{
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}
 
 
## DECREE :  buyclass 
## Buy a set of live classes. It is assumed that payment etc is hadnled before calling this PAI
## Category 
##       Live Class
## Input param
##      order_num : string 
## Output Object
##      none
function decree_buyclass($token, $param, $extraParams = array()) {
    $con = createConnection();
    $order = aduroGetOrderDetails($con,$param->order_num);
    if($order != null) {
        $user_id = $order->user_id;
        $num_class  = $order->num_class;
        $sr = new ServiceResponse("NO_ACTION",0,null);
        if(aduroAddClassPaymentForUser($con,$user_id,$num_class)){
            $sr->setCode("SUCCESS");
            $sr->setStat(1);
        }
    } else {    
        $sr->setCode("ORDER_NOT_FOUND");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE :  uploadloc
## For a series of file types , return upload locations 
## Category 
##       File Upload
## Input param
##      file_types : array of integers 
##                   0 for user_picture
##                   1 for user_log
## Output param 
##      file_locs  : array of string
##                   each string represents a location of file. 
function decree_uploadloc($token, $param, $extraParams = array()) {
   $con = createConnection();
	//file_put_contents("upload1.txt",$token);
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    $file_locs = array();
    if($user_id >=0) {
        foreach($param->file_types as $file_type) {
			$location = aduroGetUserFileLocation($con, $user_id,$file_type);
            array_push($file_locs,$location);
        }
        $objRet->file_locs = $file_locs;
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


## DECREE :  mkclassorder 
## Buy a set of live classes. It is assumed that payment etc is hadnled before calling this PAI
## Category 
##       Live Class
## Input param
##      edge_id      : integer 
##      order_prefix : string - max 5 characters 
## Output Object
##      order_num   : string - max 30 char 
##      amount      : amount to be charged
##      course_name : string
##      num_class   : number of classes bought
##      user_name   : string
function decree_mkclassorder($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $class_count = aduroGetClassCountFromEdge($con,$param->edge_id);
        if($class_count > 0) {
            $course = aduroGetCourseFromEdge($con,$param->edge_id);
            $retObj->num_class=$class_count;
            $retObj->course_name= $course->course_name;
            $retObj->user_name = aduroGetUserName($con,$user_id);
            $retObj->order_num = aduroCreateLiveClassOrder($con,$param->order_prefix,$user_id,$class_count,$course->course_id);
            $retObj->amount = 150 * $class_count;
            $sr->setVal($retObj);
            $sr->setCode("SUCCESS");
            $sr->setStat(1);
        } else {
            $sr->setCode("EDGE_NOT_FOUND");
            $sr->setStat(0);
        }
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : enroll
## Enroll a user for a course
## Category 
##       Course Consumption
## Input Param 
##     course_code    : string
## Output Object
##     none
function decree_enroll($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		if(property_exists($param, 'date_of_expiry'))
		{
			$dateOfExpiry = $param->date_of_expiry;
			aduroEnrollUserForCourseWithExpiry($con,$user_id,$course_code,$dateOfExpiry);
		}
		else
			aduroEnrollUserForCourse($con,$user_id,$course_code);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}     

## DECREE : chkenroll
## Check the enrollment status of a user for a course 
## Category 
##       Course Consumption
## Input param 
##      course_code : string
## Output Param 
##      none
function decree_chkenroll($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
        $isEnrolled = aduroChkEnrollUserForCourse($con,$user_id,$course_code);
        if($isEnrolled == true) {
            $sr->setCode("SUCCESS");
            $sr->setStat(1);
        } else {
            $sr->setCode("NOT_ENROLLED");
            $sr->setStat(0);
        }
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : listcourse
## Return a list of courses that user is enrolled to
## Category 
##       Course Consumption
## Input param
##     None 
## Output param
##     courses : array 
##              course_code : course code , string
##              course_name : course name , string
##              launch_type : string
function decree_listcourse($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $list_courses = aduroGetListOfCoursesForUser($con,$user_id);
		$obj = new stdclass();
        $obj->courses = $list_courses;
        $sr->setVal($obj);
       // error_log(json_encode($obj));
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : getLoginIDFromToken
## Returns the uid of the token issuer in case of success and -1 if fails.
## Category 
##       Authentication
## Input param
##     token 
## Output param
##     uid
function decree_getLoginIDFromToken($token) {
    $con = createConnection();
	
	$user_id = tokenValidate($con,$token); 
	$loginid = aduroGetLoginIDFromUserID($con,$user_id);

	closeConnection($con);
	
	if ($loginid == null || $loginid == "" || $loginid == " ")
		$loginid = -1;
	
	return $loginid;
}


## DECREE : rlblsession
## Gets token for a reliable  client. A reliable client is someone whose users can log in without having to enter password. All that is requires is the user_identification sent from a fixed IP Address. Thr retuen value is a session id that encompassess the token. This is done so that only a few relaible services are exported to the reliable client.
## Category 
##       Authentication
## Input param
##       user_identity : varchar that uniquely identifies a user
##       client_id     : id of the client
##       course_code   : code of the course to be launched
## Output Obj
##       session_id    : the session id that encompasses the token if the operation succeeds
##       time_spent    : total time spent on the course til now
##       is_completed : 0 or 1 baesd on whether the course has been completed or not 
function decree_rlblsession($token, $param, $extraParams = array()) {
    $con = createConnection();
   // error_log(json_encode($param));
    $ssid = aduroGetReliableToken($con,$param->user_identity,$param->client_id);
    if(!isset($ssid) || $ssid == null) {
        closeConnection($con);
        return  new ServiceResponse("RELIABILITY_FAILED",0,null);
    }
    $retVal->session_id = $ssid;
    $course_code = $param->course_code;

    /* Check if the user is enrolled for this course */
    $user_id = tokenValidate($con,$ssid);
    $isEnrolled = aduroChkEnrollUserForCourse($con,$user_id,$course_code);
    if($isEnrolled == false) {
        aduroEnrollUserForCourse($con,$user_id,$course_code);
    }
    $seconds_spent = aduroGetUserTimeSpentForCourse($con,$user_id,$course_code);
    #$is_complete = aduroIsUserCourseComplete($con,$user_id,$course_code);
    $is_complete = 0;
    $retVal->time_spent = $seconds_spent;
    $retVal->is_completed = $is_complete;
    
    $sr = new ServiceResponse("SUCCESS",1,$retVal);
    
        closeConnection($con);
    return $sr;
}
    
## DECREE : rlblhbeat
## Reliable client request for a user fo their course details
## Category 
##       Tracking
## Input param 
##       course_code  : varchar
## Output Object
##       time_spent   : total time spent on this course by the user till date in seconds
##       is_completed : 0 or 1 baesd on whether the course has been completed or not 
##
function decree_rlblhbeat($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
        $seconds_spent = aduroGetUserTimeSpentForCourse($con,$user_id,$course_code);
        #$is_complete = aduroIsUserCourseComplete($con,$user_id,$course_code);
        $is_complete = 0;
        $sr->retVal->time_spent = $seconds_spent;
        $sr->retVal->is_completed = $is_complete;
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : frgtpass
## Forgot Password Service - given the user name it will send the password to the registered emailid
## Category
##      Authentication
## Input param 
##      login_id : varchar
## Output Object
##      msg : message to display to the user
function decree_frgtpass($token, $param, $extraParams = array()) {
    $con = createConnection();
    $sr = resetAndSendPassword($con,$param->login_id,$param->class_name);
    closeConnection($con);
    return $sr;
}

## DECREE : register
## Register a user on the liqvid network 
## Category 
##      Authentication
## Input param
##          email_id : varchar
##          first_name : varchar
##          last_name : varchar 
##          mobile  : varchar
##          password : varchar
## Output param
##      none
function decree_register($token, $param, $extraParams = array()) {

    $con = createConnection();
	$client_id = getClientIdByClassName($extraParams['class_name']);
	if($client_id!==false)
		{
		 $source = $extraParams['class_name'];
		}
	else
		{
		return 0;
		}
		
	//new function for register
	//file_put_contents('chk.txt',print_r($param,true));
	$sr = apiRegister($con,$param,$extraParams['class_name'],$client_id);
	closeConnection($con); 
    return $sr;
}

## DECREE : buypkg
## Buy a pakcage for the user 
## Category 
##      E-Commerce
## Input Param
##      package_code : varchar
## Output param
##      none
function decree_buypkg($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0 ) {
        if(aduroBuyPackageForUser($con,$user_id,$param->package_code)) {
            $sr->setCode("SUCCESS");
            $sr->setStat(1);
        } else {
            $sr->setCode("FAILURE");
            $sr->setStat(0);
        }
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : pkgstat
## Check Package Status for User
# Category 
##      E-Commerce
## Input Param
##      package_code : varchar
## Output param
##      none
function decree_pkgstat($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0 ) {
        if(aduroCheckPackageForUser($con,$user_id,$param->package_code)) {
            $sr->setCode("SUCCESS");
            $sr->setStat(1);
        } else {
            $sr->setCode("NOT_PURCHASED");
            $sr->setStat(0);
        }  
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

## DECREE : rlblreg
## reliable registration of the user. This assumes that the user already has been authenticated from an OpenAPI like service (facebook.google etc)
## Category 
##        Authentication
## Input Param
##      unique_id : uniq open api id , to identify this person in future
##      email_id  : email id of the person
##      phone_num : optional phone number
##      first_name : first name
##      last_name : last name
##      appname : the name of the app sending this reliable user - set for ensuring addition to the correct client. 
## Output Param
##      None
function decree_rlblreg($token, $param, $extraParams = array()) {
    $con = createConnection();
    $sr = new ServiceResponse("NO_ACTION",0,null);
    $sr = aduroCreateRlblUser($con,$param->first_name,$param->last_name,$param->email_id,$param->mobile,$param->unique_id);
    closeConnection($con);
    return $sr;
}

## DECREE : rlbltok
## reliable token of the user. This assumes that the user already has been authenticated from an OpenAPI like service (facebook.google etc)
## Category 
##        Authentication
## Input Param
##      unique_id : uniq open api id , to identify this person in future
## Output Param
##      token
function decree_rlbltok($token, $param, $extraParams = array()) {
    $con = createConnection();
    $sr = new ServiceResponse("NO_ACTION",0,null);
    $ssid = aduroGetRlblToken($con,$param->unique_id);
    $sr = new ServiceResponse("SUCCESS",1,$ssid);
    closeConnection($con);
    return $sr;
}




## DECREE : getUidFromToken
## Returns the uid of the token issuer in case of success and -1 if fails.
## Category 
##       Authentication
## Input param
##     token 
## Output param
##     uid
##     
function decree_getUidFromToken($token) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token); 
    
    return $user_id;
}


## DECREE : assignUserToBatch
## Assigns Batch to the user
# Category 
##      Cousre Consumption
## Input Param
##      clientid : varchar
##		userid : varchar
## Output param
##      none
function decree_assignUserToBatch($token, $param, $extraParams = array())
{
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
	$sr = new ServiceResponse("NO_ACTION", 0, null);
	if($user_id >=0) 
	{
		if(aduroAssignmentUserToBatch($con, $param->clientID, $user_id))
		{
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		} 
		else {
			$sr->setCode("FAILURE");
			$sr->setStat(0);
		}
	}
	else
	{
		$sr->setCode("TOKEN_EXPIRED");
		$sr->setStat(0);
	}
    closeConnection($con);
    return $sr;
}


## DECREE : coursever
## Returns the current version of the course. If the course needs to be updates, then the client would repull the version
## Category 
##       Course Consumption
## Input param
##     course_code 
## Output param
##     ver: version number interger
function decree_coursever($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0 ) {
        $sr = aduroGetCourseVersion($con,$param->course_code);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

///////////////////////////////////////////////////////For WizIQ//////////////////////////////
## DECREE : eventlist
## List all the classes available for a specific course / activity
## Category 
##       Live Class
## Input param
##       edgeId  : edgeId of Course or Activity
##		 type	: type (either C or A)
## Output Obj
##       user_state : 0=Has Not Bought
##                    1=Has Bought but not booked
##                    2=Has Booked and Class is in Future
##                    3=Has Booked and Class is in Past
##       classes : array 
##           title :  name of class
##           desc  : description
##           tname : teacher name
##           stime : start time
##           dur   : duration
##           booked : has been booked by this user
##           mxs    : number of total seats
##           avs    : number of available seats
##           recording_url : string, in case the user_state is 3

function decree_eventlist($token, $param, $extraParams = array()) {
    $con = createConnection();
    if($con == null)
       return 0;
   if(!empty($token))
	{
	$user_id = tokenValidate($con,$token);
	}
	else
	{
	$user_id=$param->user_id;
	}
	$center_id = $param->center_id;
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//file_put_contents('wiz.txt',$user_id.'='.$center_id);
    if($user_id >=0) {
        $liveClass = new LiveClassObject();
        $liveClass  = aduroGetWebinars($con,$user_id,$center_id);
		if(count($liveClass)<=0)
		{
		$sr->setCode("NO_RECORDS");
        $sr->setStat(0);
		return $sr;
		}
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
        $sr->setVal($liveClass);
		return $sr;
    } else {
		$sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
		return $sr;
		
    }
     closeConnection($con);  
}


function decree_joinevent($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
	//file_put_contents('wq1.txt',$user_id."-".$param->class_id);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroJoinWebinar($con,$user_id, $param->class_id);
		if($objRet->status=='ALREADY_BOOKED')
		{
		 $sr->setCode("ALREADY_BOOKED");
		 $sr->setStat(0);
		 return $sr;
		}
		if($objRet->status=='BOOKING_FAILED')
		{
		 $sr->setCode("BOOKING_FAILED");
		 $sr->setStat(0);
		 return $sr;
		}
		if($objRet->status=="SUCCESS")
		{
        $sr = new ServiceResponse("NO_ACTION",0,null);
		$sr->setCode("SUCCESS");
        $sr->setStat(1);
		return $sr;
		}
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
		return $sr;
    }
  closeConnection($con);  
}

## DECREE : cancelclass
## Cancel a future class 
## Category 
##       Live Class
## Input param
##       class_id  : id of class to be booked
## Output Obj
##       none
function  decree_cancelevent($token, $param, $extraParams = array()) {
    $con = createConnection();
      if(!empty($token))
	{
	$user_id = tokenValidate($con,$token);
	}
	else
	{
	$user_id=$param->user_id;
	}
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = aduroCancelWebinar($con,$user_id, $param->class_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}
/*
///////////////////////////////////////////////////////For WizIQ//////////////////////////////
## DECREE : eventlist
## List all the classes available for a specific course / activity
## Category 
##       Live Class
## Input param
##       edgeId  : edgeId of Course or Activity
##		 type	: type (either C or A)
## Output Obj
##       user_state : 0=Has Not Bought
##                    1=Has Bought but not booked
##                    2=Has Booked and Class is in Future
##                    3=Has Booked and Class is in Past
##       classes : array 
##           title :  name of class
##           desc  : description
##           tname : teacher name
##           stime : start time
##           dur   : duration
##           booked : has been booked by this user
##           mxs    : number of total seats
##           avs    : number of available seats
##           recording_url : string, in case the user_state is 3

function decree_notievent($token, $param, $extraParams = array()) {
    $con = createConnection();
    if($con == null)
       return 0;
    $user_id = tokenValidate($con,$token);
    //error_log( "$user_id is user_id\n");
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$edgeId=$param->edge_id;
	$otype=$param->otype;

    if($user_id >=0) {
        if($edgeId == null) {
            $sr->setCode("EDGE_ID_MISSING");
			$sr->setStat(0);
			$sr->setVal("Edge ID missing.");
            return $sr;
        }
        $liveClass = new LiveClassObject();
        $liveClass  = aduroWebinarNotification($con,$user_id, $param->edge_id);
		if(count($liveClass)<=0)
		{
		$sr->setCode("NO_RECORDS");
        $sr->setStat(0);
		return $sr;
		}
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
        $sr->setVal($liveClass);
		return $sr;
    } else {
		$sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
		return $sr;
		
    }
     closeConnection($con);  
}
*/
///////////////////////////////////////////////////For WizIQ/////////////////////////////////////////

///////////////////////////////////////////////////For User SignUp/////////////////////////////////////////
## DECREE : user_course_signup
## Enroll a user for a course
## Category 
##       Course Consumption
## Input Param 
##     course_code    : string
## Output Object
##     none


function decree_user_course_signup($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		if($course_code=='')
		{
		 $sr->setCode("EMPTY_COURSE_CODE");
         $sr->setStat(0);
		 return $sr;
		}
		$check = userCourseSignUp($con,$user_id,$course_code);
		if($check){		
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		}else{
			$sr->setCode("COURSE_SIGNUP_FAILED");
			$sr->setStat(0);
			return $sr;
		}
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}  
///////////////////////////////////////////////////For User SignUp/////////////////////////////////////////

function decree_courseCheck($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $product_id = $param->product_id;
		$center_id = $param->center_id;
		$batch_id = $param->batch_id;
		$course_code = $param->course_code;
		$licence_key = $param->licence_key;
		$appVersion = $param->appVersion;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
		
		$objRet  = centralLicensingCourseCheck($con,$param,$user_id);
		
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
	return $sr;
} 

## DECREE : vesion
## Book a future class 
## Category 
##       update Class
## Input param
##       course code and version
## Output Obj
##       class and chapters array
function  decree_version($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);

	
    if($user_id >=0) {
        $objRet  = updateClass($con,$param);

        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

//////////////// course json(for course download) ///////////////////

/*function decree_courseJson(){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//error_log("welcome to the course update");
	$course_code = $param->course_code;
	error_log("requested Param is  --------------------- ".json_encode($param));
	
    if($user_id >=0) {
        $objRet  = aduroGetCourseJson($con,$course_code);
	error_log("course array is  --------------------- ".json_encode($objRet));

        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}*/

//////////////// chapter json(for chapter download) ///////////////////

function decree_chapComponent($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//error_log("welcome to the course update");
	$course_code = $param->course_code;
	$edgeId = $param->edge_id;
	
	
    if($user_id >=0) {
        $objRet  = aduroGetChapterJson($con,$course_code, $edgeId);
	

        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


//////////////// chapter multiple component json ///////////////////

function decree_getChapterMultiComponent($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//error_log("welcome to the course update");
	$course_code = $param->course_code;
	$edgeId = $param->edge_id;
	
	
    if($user_id >=0) {
        $objRet  = getChapterMultiComponentJson($con,$course_code, $edgeId);
	

        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}




function decree_packageinfo($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//////error_log('packageinfo=============================='.json_encode($param));
	//$user_id = 130757;
    if($user_id >=0) {
		global $serviceURL;
		//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
		$request = curl_init($serviceURL);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'packageInfo', 'package_code' => trim($param->package_code)));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($request);
		curl_close($request);
		$res = json_decode($res);
		//////error_log('res=============================='.json_encode($res));
        $sr  = aduroPackageInfo($con, $res, $param, $user_id);
		//file_put_contents('test/package_'.$user_id.'.txt',print_r($sr,true)); 
		
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
}

function decree_refreshtoken($token, $param, $extraParams = array()){
    $con = createConnection();
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$login = $param->login;
    $password = $param->password;
	$deviceId = $param->deviceId;
    $platform = $param->platform;
	$client = $param->client;
	$appVersion = $param->appVersion;
		
    if($login != "") {
		$sr  = refreshToken($con, $login, $deviceId, $platform, $client, $appVersion);
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
}

function decree_getpackageinfo($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//////error_log('packageinfo=============================='.json_encode($param));
	//$user_id = 130757;
    if($user_id >=0) {
		global $serviceURL;
		//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
		$request = curl_init($serviceURL);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'getPackageInfo', 'package_code' => $param->package_code));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($request);
		curl_close($request);
		$res = json_decode($res);
		//////error_log('res=============================='.json_encode($res));
        $sr  = aduroGetPackageInfo($con, $res, $param, $user_id);
        
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
}

function decree_getword($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	//////error_log('packageinfo=============================='.json_encode($param));
	//$user_id = 130757;
    //if($user_id >=0) {
		global $serviceURL;
		//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
		$request = curl_init($serviceURL);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'getWord'));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($request);
		curl_close($request);
		$res = json_decode($res);
		$sr = new ServiceResponse("SUCCESS",0,null);
		$retVal->wordInfo = $res;
		$sr->setval($retVal);
        
   //} else {
        //$sr->setCode("TOKEN_EXPIRED");
        //$sr->setStat(0);
   //}
    closeConnection($con);
    return $sr;
}

function decree_user_device_token($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	
	$platform = $param->platform;
	$deviceId = $param->deviceId;
	$product_codes = implode(',',$param->product_codes);
	$token_value = $param->token_value;
	//error_log('decree_user_device_token=============================='.json_encode($param));
	//error_log('product_codes=============================='.$product_codes);
	//$user_id = 130757;
    if($user_id >=0) {
		global $serviceURL;
		//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
		$request = curl_init($serviceURL);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'userDeviceToken', 'platform' => $platform, 'deviceId' => $deviceId, 'product_codes' => $product_codes, 'token_value' => $token_value, 'user_id' => $user_id));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($request);
		curl_close($request);
		$res = json_decode($res);
		$sr = new ServiceResponse("SUCCESS",0,null);
		$retVal->token_res = $res;
		$sr->setval($retVal);
        
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
}

function decree_getUserCourseReport($token, $param, $extraParams = array()){
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$course_code = $param->course_code;
	
    if($user_id != "") {
		$sr  = aduroUserCourseReport($con, $user_id, $course_code);
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
}




function decree_syncBatch($token, $param, $extraParams = array() ){
    
    global $stopSync;
	
	$sr = new ServiceResponse("NO_ACTION",0,null);
	

	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    
	
    if( is_numeric($super_user_id) && $super_user_id > 0 ) {
		$sr  = aduroSyncBatch($con, $param, $extraParams );
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
	
    
	return $sr;
        
}


function decree_syncBatchCourseMap($token, $param, $extraParams = array() ){
    
	$sr = new ServiceResponse("NO_ACTION",0,null);
	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    
    
	
    if( is_numeric($super_user_id) && $super_user_id > 0 ) {
		$sr  = aduroSyncBatchCourseMap($con, $param, $extraParams );
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
	
	
    return $sr;
    
    
}


function decree_syncUser($token, $param, $extraParams = array() ){
    
  
   $sr = new ServiceResponse("NO_ACTION",0,null);

	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    
	
    if( is_numeric($super_user_id) && $super_user_id > 0 ) {
		$sr  = aduroSyncUser($con, $param, $extraParams );
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);

    return $sr;
    
    
}


function decree_syncBatchUserMap($token, $param, $extraParams = array() ){
    
	$sr = new ServiceResponse("NO_ACTION",0,null);
	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    
	
    if( is_numeric($super_user_id) && $super_user_id > 0 ) {
		$sr  = aduroSyncBatchUserMap($con, $param, $extraParams );
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);

    return $sr;
    
    
}


function decree_syncTeacherSession($token, $param, $extraParams = array() ){
    
	$sr = new ServiceResponse("NO_ACTION",0,null);

	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    
    
    if( is_numeric($super_user_id) && $super_user_id > 0 ) {
        $sr  = aduroSyncTeacherSession($con, $param, $extraParams );
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
    
    
}


function decree_syncStudentAttendance($token, $param, $extraParams = array() ){
    
	$sr = new ServiceResponse("NO_ACTION",0,null);
	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    
    
    if( is_numeric($super_user_id) && $super_user_id > 0 ) {
        $sr  = aduroSyncStudentAttendance($con, $param, $extraParams );
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
    
    
}


function decree_tracker($token, $param, $extraParams = array() ){
    
	 $sr = new ServiceResponse("NO_ACTION",0,null);
	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
   
	
    if($super_user_id != "") {
		$res  = aduroTracker($con, $param, $extraParams );
        if( $res['status'] == 1){
            $sr->setCode("SUCCESS");
        }else{
            $sr->setCode("FAILURE");
            $sr->setVal( array('msg' => $res['msg']) );
        }
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
	
    return $sr;
    
    
}


function  decree_aduroTrackQuizAns($token, $param, $extraParams = array() ) {
    
	 $sr = new ServiceResponse("NO_ACTION",0,null);
	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
   
    
    $platform = $extraParams['platform'];
    $center_id = $extraParams['center_id'];
    
    if($super_user_id >=0) {
        
			$deleted_edge_id_arr = array();
            
            foreach($param as $obj){
                
                $obj->platform = $platform;
                $obj->center_id = $center_id;
                
                if( empty($obj->platform) || empty($obj->package_code) || empty($obj->course_code) ){
                    continue;
                }
                
                if( empty($obj->test_uniqid) || empty($obj->ques_uniqid) ){
                    continue;
                }
                
                
                if( empty($obj->user_id) || empty($obj->batch_id) || empty($obj->center_id) ){
                    continue;
                }
                
                $check_id = $obj->user_id .'_'. $obj->test_uniqid .'_'. $obj->package_code .'_'. $obj->batch_id .'_'. $obj->center_id;
                
                $obj_arr = (array) $obj;
                
                if( !in_array($check_id, $deleted_edge_id_arr) ){
                    $deleted_edge_id_arr[] = $check_id;
                    aduroDetetePreviousQuizAttempt($con, $obj_arr);
                }
                
                aduroTrackQuizAns($con, $obj_arr);
                
            }
			
			$sr->setVal( array() );
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
			
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_updateCenterLastSync($token, $param, $extraParams = array() ){
    
	$sr = new ServiceResponse("NO_ACTION",0,null);

	$con = createConnection();
    $super_user_id = tokenValidate($con,$token);
   
	
    if($super_user_id != "") {
		$status  = aduroUpdateCenterLastSyncDate($con, $param, $extraParams );
        if( $status == true){
            $sr->setCode("SUCCESS");
        }else{
            $sr->setCode("FAILURE");
            $sr->setVal( array('msg' => $res['msg']) );
        }
   } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
   }
    closeConnection($con);
    return $sr;
}


function decree_getCourseDetailsByCourseCodes($token, $param, $extraParams = array() ){
    
    $con = createConnection();
    
    $sr = new ServiceResponse("NO_ACTION",0,null);
    $course_code_arr = json_decode( json_encode($param->course_codes) , true );
	$arr  = getCourseDetailsByCourseCodes($con, $course_code_arr);
    $sr->setCode("SUCCESS");
    $sr->setVal( array('course_list' => $arr ) );
    
    closeConnection($con);
    return $sr;
}



function decree_checkAduroSoftwareUpdate($token, $param, $extraParams = array() ){
    $con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    
    $platform = $extraParams['platform'];
    $center_id = $extraParams['center_id'];
    
    if($super_user_id >=0 || true ) {
        
        $update_arr = checkAduroSoftwareUpdate($param, $extraParams);
        $sr = new ServiceResponse("SUCCESS",0,null);
        $sr->setVal( $update_arr );
        
    }else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}



function decree_unlockedChapterList($token, $param, $extraParams = array() ){
    
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        
        $course_codes = $param->course_code;
		
		if( empty($course_codes) ){
            $sr->setCode("EMPTY_COURSE_CODE");
         	$sr->setStat(0);
            return $sr;
		}
		
        
        $unlock_chap_list = getUnlockedChapterList($con,  array('course_codes' => $course_codes, 'client_name' => $param->client_name) );
        
		//file_put_contents('test/res.txt',json_encode($objRet));
        $val = array('chapter_list' => $unlock_chap_list); 
		$sr->setVal($val);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


function decree_visiting_user($token, $param, $extraParams = array() ){
    
    $con = createConnection();
    $super_user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    
    
    if($super_user_id >= 0  ) {
        
        $check = aduroVisitingUser($param);
        if( $check){
            $sr = new ServiceResponse("SUCCESS",0,null);
        }else{
            $sr = new ServiceResponse("FAILURE",0,null);
        }
        
        
    }else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_getAppUpdate($token, $param, $extraParams = array()){
    
    //$con = createConnection();
	//file_put_contents('class.txt',$param->class_name);
	$client_id = $param->class_name;
	$sr = aduroGetAppVersion($client_id); 
    return $sr;
}

function decree_getAppUpdate_test($token, $param, $extraParams = array()){
    
	$client_id = $param->class_name;
	$sr = getAppVersionTest($client_id); 
    return $sr;
}

/////Decree added specifically for CUP - Online to enable / disabled units
function  decree_course_status($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	
    if($user_id >=0) {
        $objRet  = aduroGetCourseStatus($con,$user_id,$param);

        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_generateOTP($token, $param, $extraParams = array()){		
    		
	//file_put_contents('decree.txt',$param->user_phone);		
	$con = createConnection();		
	$user_phone = $param->user_phone;		
	$user_email = $param->user_email;
	$user_name = $param->user_name;
	$user_action = $param->user_action;
	$sr = generateOTP($con,$user_phone,$user_email,$user_action,$user_name); 		
    return $sr;		
}		
function decree_verifyOTP($token, $param, $extraParams = array()){		
    		
	//file_put_contents('decree.txt',$param->user_phone);		
	$con = createConnection();		
	if($param->user_action == 'profile_update')		
	{		
	$user_id = tokenValidate($con,$token);		
	}		
	else		
	{		
	$user_id=0;		
	}		
			
	$user_phone = $param->user_phone;		
	$user_otp = $param->user_otp;		
	$user_email = $param->user_email;
	$user_name = $param->user_name;
	$user_action = $param->user_action;		
	$sr = verifyOTP($con,$user_otp,$user_phone,$user_email,$user_action,$user_id,$user_name); 		
    return $sr;		
}		
function decree_changePassword($token, $param, $extraParams = array()){		
    		
	//file_put_contents('decree.txt',$param->user_phone);		
	$con = createConnection();		
			
	$user_phone = $param->user_phone;		
	$user_email = $param->user_email;		
	$user_password = $param->user_password;	
	$user_name = $param->user_name;
	$sr = changePassword($con,$user_phone,$user_email,$user_password,$user_name); 		
    return $sr;		
}		
function decree_getuserdetails($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = getUserDetailsFromUserId($con,$user_id);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}		
function decree_setuserdetails($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = setUserDetailsFromUserId($con,$user_id,$param);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

function decree_getCaptcha($token,$param,$extraParams = array()) {
   	$con = createConnection();
    $sr = new ServiceResponse("NO_ACTION",0,null);
   if(!empty($extraParams[deviceId])) {
        $objRet  = aduroGetCaptcha($con,$extraParams[deviceId]);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_validateCaptcha($token,$param,$extraParams = array()) {
   	$con = createConnection();
     $sr = new ServiceResponse("NO_ACTION",0,null);
   if(!empty($extraParams[deviceId])) {
        $objRet  = aduroValidateCaptcha($con,$param->user_captcha,$extraParams[deviceId]);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_track_sp($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);

    if($user_id >=0) {
	
		
			foreach($param as $obj) {
				
					$objRet  = aduroTrackSPData($con,$user_id,$obj->word_id,$obj->quiz_id, $obj->ques_id, $obj->quiz_mode,$obj->learn_view,$obj->audio_learn_play,$obj->video_learn_play,$obj->media_record,$obj->record_play,$obj->time_spent_learn,$obj->quiz_view,$obj->audio_quiz_play,$obj->video_quiz_play,$obj->ans_clicked,$obj->ans_submit,$obj->is_correct,$obj->option_attempted,$obj->ques_time_spent,$obj->attempt_on,$obj->record_compare, $extraParams['platform']);
			}	
			$sr->setVal($objRet);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);		
   
    } else {
		$sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_clientStatus($token, $param, $extraParams = array()) {		
    $con = createConnection();	
   	$user_id = tokenValidate($con,$token);
	$sr = new ServiceResponse("NO_ACTION",0,null);	
    $objRet = checkClientStatus($param);	
	if($objRet=='yes' && $user_id >=0)
	{	
		$objRet = checkUserStatus($con,$user_id);	 
	}
	$sr->setVal($objRet);		
	$sr->setCode("SUCCESS");		
	$sr->setStat(1);			
    return $sr;		
}

function decree_get_visited_course($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = getUserVisitedCourse($con,$user_id);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}	

function decree_registerOnlineCenterUser($token, $param, $extraParams = array()) {
	//error_log("Param is while register --------------------- ".json_encode($param));
    $con = createConnection();
	if(isset($param->appVersion) && $param->appVersion != ""){
		
		if($param->appVersion == 2){
			if($param->class_name == 'orion'){
				$source = 'orion';
			}
			else if($param->class_name == 'evox'){
				$source = 'evox';
			}
			else if($param->class_name == 'cambridge')
		    {
			$source = 'cambridge';
		    }
			else if($param->class_name == 'etoe')
		    {
			$source = 'etoe';
		    }
			else if($param->class_name == 'hero')
		    {
			$source = 'hero';
		    }
			else if($param->class_name == 'absf')
		    {
			$source = 'absf';
		    }
			else if($param->class_name == 'ee-hindi')
		    {
			$source = 'ee-hindi';
		    }
			else if($param->class_name == 'ee-marathi')
		    {
			$source = 'ee-marathi';
		    }
			else if($param->class_name == 'ee-bangla')
		    {
			$source = 'ee-bangla';
		    }
			else if($param->class_name == 'ee-gujarati')
		    {
			$source = 'ee-gujarati';
		    }
			else if($param->class_name == 'ee-tamil')
		    {
			$source = 'ee-tamil';
		    }
			else if($param->class_name == 'ee-telugu')
		    {
			$source = 'ee-telugu';
		    }
			else if($param->class_name == 'ee-kannada')
		    {
			$source = 'ee-kannada';
		    }
			else if($param->class_name == 'ee-malayalam')
		    {
			$source = 'ee-malayalam';
		    }
			else if($param->class_name == 'ee-oriya')
		    {
			$source = 'ee-oriya';
		    }
			else if($param->class_name == 'ee-assamese')
		    {
			$source = 'ee-assamese';
		    }
			else if($param->class_name == 'bridgestone')
		    {
			$source = 'bridgestone';
		    }
			else if($param->class_name == 'bbc')
		    {
			$source = 'bbc';
		    }
			else if($param->class_name == 'cuponline')
		    {
			$source = 'cuponline';
		    }
			else if($param->class_name == 'tulleeho')
		    {
			$source = 'tulleeho';
		    }
			else if($param->class_name == 'wiley')
		    {
			$source = 'wiley';
		    }
			else if($param->class_name == 'mepro')
		    {
			$source = 'mepro';
		    }
			else if($param->class_name == 'cam_capable')
		    {
			$source = 'cam_capable';
		    }
			else if($param->class_name == 'wileynxt')
		    {
			$source = 'wileynxt';
		    }
			else if($param->class_name == 'ptea')
		    {
			$source = 'ptea';
		    }
			else if($param->class_name == 'awards')
		    {
			$source = 'awards';
		    }
			else if($param->class_name == 'ace')
		    {
			$source = 'ace';
		    }
			else if($param->class_name == 'quizky')
			 {
			$source = 'quizky';
			 } 
			else if($param->class_name == 'kannanprep')
			{
			$source = 'kannanprep';
			} 
			else{
				$source = 'englishEdge';
			}
			
			/*global $serviceURL;
			//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
			$request = curl_init($serviceURL);
			curl_setopt($request, CURLOPT_POST, true);
			curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'empRegister', 'sourse' => $source));
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($request);
			curl_close($request);
			$res = json_decode($res);
			////file_put_contents('test/1.txt','aaa');*/
			$sr = aduroCreateOnlineCenterUser($con, $param);
			
		}
	}else{
	
		$sr = aduroCreateUser($con,$param->first_name,$param->last_name,$param->email_id,$param->mobile,$param->password, $param->isCode, $param->unique_code, $param->course_code, $param->class_name, $param->is_otp_based );
	}
    closeConnection($con);
    return $sr;
}

function decree_syncComponentCompletion($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		
		foreach($param as $obj){
		 setComponentCompletion($con,$user_id,$obj);
		}
        $objRet  = getComponentCompletion($con,$user_id,$param);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

function decree_getChapterOrTopicCompletion($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
	foreach($param as $obj){
        $objRet  = aduroGetChapterOrTopicCompletion($con,$user_id,$obj);
       }		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

function decree_syncWordCompletion($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		foreach($param as $obj){
		 aduroSetWordCompletion($con,$user_id,$obj);
		}
        $objRet  = aduroGetwordCompletion($con,$user_id);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}	



///////////////////////////////////////////////////Course data for graph/////////////////////////////////////////

function decree_course_overall_data($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		$package_code = $param->package_code;
		$client = $param->client;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = aduroCourseOverallData($con,$user_id,$course_code,$package_code,$client);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 



function decree_course_overall_data2($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		$package_code = $param->package_code;
		$client = $param->client;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = aduroCourseOverallData2($con,$user_id,$course_code,$package_code,$client);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}
/////////////////////////////// Course data skill wise ///////////////////////////////////////

function decree_course_skill_data($token, $param, $extraParams = array()) {
	
	$con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $courseArr = $param->courseArr;
        $course_code = $courseArr[0];
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = aduroCourseSkillData($con,$user_id,$courseArr);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

//////// Course test(assessment) data ///////////////

function decree_get_test_data($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = aduroGetTestData($con,$user_id,$course_code);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 



function decree_set_user_bookmark($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = aduroSetUserBookmark($con,$user_id,$param);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

function decree_get_user_bookmark($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = aduroGetUserBookmark($con,$user_id,$param);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}	

function decree_getUserAssignments($token, $param, $extraParams = array()) {
    $con = createConnection();
	$con2 = createConnection();
    $user_id = tokenValidate($con,$token);
	$client_id=$param->class_name;
	//file_put_contents('is_class_name.txt',$param->class_name);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $objRet  = getUserAssignments($con,$con2,$user_id,$client_id);
        $sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
	 } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}





function decree_setUserCoins($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);

    if($user_id >=0) {

		foreach($param as $obj){ 
			
				$objRet  = aduroSetUserCoins($con,$user_id,$obj->course_code,$obj->topic_edge_id,$obj->chapter_edge_id,$obj->component_edge_id,$obj->component_data,$obj->component_type, $obj->user_coins);
			
			}
		$sr->setVal($objRet);
		$sr->setCode("SUCCESS");
		$sr->setStat(1);
			
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


function decree_getUserPerformance($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		$package_code = $param->package_code;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = getUserPerfomance($con,$user_id,$course_code,$package_code);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

function decree_getMyPerformance($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		$package_code = $param->package_code;
		//$client = $param->client;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = aduroGetMyPerfomance($con,$user_id,$course_code,$package_code);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 
function decree_getUserCoins($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $edge_id = $param->edge_id;
        $edge_id_category = $param->edge_id_category;
        $component_type = $param->component_type;
		$package_code = $param->package_code;
		if($edge_id==''){
		 $sr->setCode("EMPTY_EDGE_ID");
         	 $sr->setStat(0);
		 return $sr;
		}
		if($edge_id_category==''){
		 $sr->setCode("EMPTY_EDGE_ID_CATEGORY");
         	 $sr->setStat(0);
		 return $sr;
		}
		if($edge_id_category=='component' && $component_type==''){
		 $sr->setCode("EMPTY_COMPONENT_TYPE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = getUserCoins($con,$user_id,$edge_id,$edge_id_category,$component_type,$package_code);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 




function decree_settnc($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = setAcceptance($con,$user_id);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}


function decree_setUserAction($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $client_id = $param->client_id;
		$action_code = $param->action_code;
		$action_description = $param->action_description;
		$platform = $param->platform;
		$device_id = $param->deviceId;
		$information = $param->information;
		
		$objRet  = aduroSetUserAction($con,$user_id,$client_id,$action_code,$action_description,$platform,$device_id,$information);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

//Api to get course completion
function decree_getCompletion($token,$param, $extraParams = array()) { 		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {
		
		$course_code = $param->course_code;
		$package_code = $param->package_code;
		//$client = $param->client;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
			 $sr->setStat(0);
		 return $sr;
		}
		if($package_code==''){
		 $sr->setCode("EMPTY_PACKAGE_CODE");
			 $sr->setStat(0);
		 return $sr;
		}		
		
		$objRet  = getCompletion($con,$user_id,$param);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
   
    }else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//Api to get course completion
function decree_getDataByLevel($token,$param, $extraParams = array()) { 		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {
		
		$level_arr = $param->level_arr;
		$package_code = $param->package_code;
		if($level_arr==''){
		 $sr->setCode("EMPTY_LEVEL_ARR");
			 $sr->setStat(0);
		 return $sr;
		}
		if($package_code==''){
		 $sr->setCode("EMPTY_PACKAGE_CODE");
			 $sr->setStat(0);
		 return $sr;
		}		
		
		$objRet  = getDataByLevel($con,$user_id,$param);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
   
    }else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//Api to get resource
function decree_getResourcesByCourse($token,$param, $extraParams = array()){ 		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {
		
		$course_code = $param->course_code;
		//$client = $param->client;
		if($course_code==''){
			$sr->setCode("EMPTY_COURSE_CODE");
			$sr->setStat(0);
			return $sr;
		}
				
		$objRet  = aduroGetResourcesByCourse($con,$user_id,$course_code);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);	
	
    }else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//Api to get resource
function decree_getResourcesByCourseFileFolder($token,$param, $extraParams = array()){ 		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {
		
		$course_code = $param->course_code;
		//$client = $param->client;
		if($course_code==''){
			$sr->setCode("EMPTY_COURSE_CODE");
			$sr->setStat(0);
			return $sr;
		}
				
		$objRet  = getResourcesByCourseFileFolder($con,$user_id,$course_code);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);	
	
    }else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}
//api to save  user log
function decree_save_user_log($token, $param, $extraParams = array()){		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$objRet  = aduroSaveUserLog($con,$user_id,$param);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}


//api to get product by user
function decree_product_by_user($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$objRet  = getProductByUser($con,$user_id);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//api to get course by product
function decree_course_by_product($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$sr  = getCourseFromProductId($con,$param->product_id);
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}



function decree_profileCompletion($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $profile_details = getUserProfileCompletion($con,$user_id);
		$obj = new stdclass();
        $obj->courses = $profile_details;
        $sr->setVal($obj);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_userDiscount($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0){
        $arrstatus = setUserDiscount($con,$user_id,$param);
		$obj = new stdclass();
        $obj->status = $arrstatus;
        $sr->setVal($obj);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else{
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_setDisclaimerChecked($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0){
        $is_checked = setDisclaimerChecked($con,$user_id,$param);
		$sr->setVal($is_checked);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else{
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

function decree_getDiscountCoupons($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$client_id = getClientIdByClassName($extraParams['class_name']);
	if($client_id!==false){
		if($user_id >=0) {
			$discount_list = getDiscountCoupons($con,$user_id,$param,$client_id);
			$obj = new stdclass();
			$obj->discounts = $discount_list;
			$sr->setVal($obj);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		} else {
			$sr->setCode("TOKEN_EXPIRED");
			$sr->setStat(0);
		}
	}else{
		$sr->setCode("CLASS_NAME_REQUIRED");
        $sr->setStat(0);
	}
    closeConnection($con);
    return $sr;
}

function decree_getLeaderboard($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$client_id = getClientIdByClassName($param->class_name);
	if($client_id!==false){ 
		if($user_id >=0) {
			$badge_list = getLeaderboard($user_id,$param,$client_id);
			$obj = new stdclass();
			$obj->badges = $badge_list;
			$sr->setVal($obj);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		} else {
			$sr->setCode("TOKEN_EXPIRED");
			$sr->setStat(0);
		}
	}else{
		$sr->setCode("CLASS_NAME_REQUIRED");
        $sr->setStat(0);
	}
    closeConnection($con);
    return $sr;
}

function decree_getLevelLeaderboard($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$client_id = getClientIdByClassName($extraParams['class_name']);
	if($client_id!==false){
		if($user_id >=0) {
			$badge_list = getLevelLeaderboard($user_id,$param,$client_id);
			$obj = new stdclass();
			$obj->badges = $badge_list;
			$sr->setVal($obj);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		} else {
			$sr->setCode("TOKEN_EXPIRED");
			$sr->setStat(0);
		}
    }else{
		$sr->setCode("CLASS_NAME_REQUIRED");
        $sr->setStat(0);
	}
	closeConnection($con);
    return $sr;
}

function decree_getAllBadges($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$client_id = getClientIdByClassName($extraParams['class_name']);
	if($client_id!==false)
		{
		if($user_id >=0) {
			$badge_list = getAllBadges($con,$user_id,$param,$client_id);
			$obj = new stdclass();
			$obj->badges = $badge_list;
			$sr->setVal($obj);
			$sr->setCode("SUCCESS");
			$sr->setStat(1);
		} else{
			$sr->setCode("TOKEN_EXPIRED");
			$sr->setStat(0);
		}
	}else{
		$sr->setCode("CLASS_NAME_REQUIRED");
        $sr->setStat(0);
	}
    closeConnection($con);
    return $sr;
}
function decree_getAllAvatars($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$client_id = getClientIdByClassName($extraParams['class_name']);
	if($client_id !==false){
    if($user_id>=0){
        $avatar_list = getAllAvatars($con,$param,$client_id);
		$obj = new stdclass();
        $obj->avatars = $avatar_list;
        $sr->setVal($obj);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    }else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
	}
    }else{
		$sr->setCode("CLASS_NAME_REQUIRED");
        $sr->setStat(0);
	}
    closeConnection($con);
    return $sr;
}

//Get Visiting Level
function decree_get_visiting_level($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);
	
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$product_id=$param->product_id;
		$objRet  = getVisitingLevel($con,$user_id,$product_id);
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//Update Visiting Level
function decree_update_visiting_level($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$product_id=$param->product_id;
	    $current_level=$param->visiting_level;
		$objRet  = updateVisitingLevel($con,$user_id,$product_id,$current_level);
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//decree for teacher sessioninsertTeacherSession
function decree_startTeacherSession($token, $param, $extraParams = array()) {		
    $con = createConnection();
	 $user_id = tokenValidate($con,$token);
	$chapter_edge_id=$param->chapter_edge_id;
	$topic_edge_id=$param->topic_edge_id;
	$course_code=$param->course_code;
	$batch_id=$param->batch_id;
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		
		$objRet  = insertTeacherSession($con,$user_id,$batch_id,$chapter_edge_id,$topic_edge_id,$course_code);
        if($objRet){
			$sr->setVal($objRet);
		}else{
			$alert_msg_arr = alertMessage();
			$sr->setVal($alert_msg_arr['STUDENTS_NOT_FOUND']);
		}	
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//Decree for set attendance
function decree_setAttendance($token, $param, $extraParams = array()) {		
    $con = createConnection();
	$user_id = tokenValidate($con,$token);
	$ts_id=$param->ts_id;
	$batch_id=$param->batch_id;
	$date=$param->date;
	$all_students_ids=$param->all_students_ids;
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		
		$objRet  = setAttendance($con,$user_id,$batch_id,$ts_id,$date,$all_students_ids);
		$sr->setVal($objRet);
		$sr->setCode("SUCCESS");		
        $sr->setStat(1);		
   
   } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
   closeConnection($con);		
   return $sr;		
}

//Decree for getting ilt activity
function decree_getIltActivity($token, $param, $extraParams = array()) {		
    $con = createConnection();
	$user_id = tokenValidate($con,$token);
	$course_code = $param->course_code;
	$edgeId = $param->edge_id;
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		
		$objRet  = getIltActivity($con,$edgeId,$course_code);
		$sr->setVal($objRet);
		$sr->setCode("SUCCESS");		
        $sr->setStat(1);		
   
   } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
   closeConnection($con);		
   return $sr;		
}

//Decree for tracking ilt activity
function decree_trackIltActivity($token, $param, $extraParams = array()) {		
    $con = createConnection();
	$user_id = tokenValidate($con,$token);
	$ts_id = $param->ts_id;
	$page_num = $param->page_num;
	$page_type = $param->page_type;
	$activity_id = $param->activity_id;
	$start_date_ms = $param->start_date_ms;
	$end_date_ms = $param->end_date_ms;
	//$session_mode = $param->session_mode;
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		
		$objRet  = trackIltActivity($con,$ts_id,$user_id,$page_num,$page_type,$activity_id,$start_date_ms,$end_date_ms);
		$sr->setVal($objRet);
		$sr->setCode("SUCCESS");		
        $sr->setStat(1);		
   
   } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
   closeConnection($con);		
   return $sr;		
}

//Decree for updating chapter completion and chapter time
function decree_syncComponentCompletionIlt($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);	
	$edge_id = $param->edge_id;
	$batch_id = $param->batch_id;
	$package_code = $param->package_code;
	$platform = $param->platform;
	$course_code = $param->course_code;
	$start_date_ms = $param->start_date_ms;
	$end_date_ms = $param->end_date_ms;
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
	
		setComponentCompletionIlt($con,$user_id,$param);
		
		$objRet = trackChapterTime($con,$user_id,$batch_id,$edge_id,$start_date_ms, $end_date_ms,$course_code, $package_code, $platform);
		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}


//Decree for sending track record 
function decree_getTrack($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $course_code = $param->course_code;
		$batch_id = $param->batch_id;
		if($course_code==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = getTrack($con,$user_id,$course_code,$batch_id);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

//Decree for sending track record 
function decree_getCompletionAndPer($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $edge_id = $param->edge_id;
		$batch_id = $param->batch_id;
		if($edge_id==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = getCompletionAndPer($con,$user_id,$edge_id,$batch_id);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}

//Decree for sending track all record 
function decree_getAllCompletionAndPer($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $edge_id = $param->edge_id;
		$batch_id = $param->batch_id;
		if($edge_id==''){
		 $sr->setCode("EMPTY_COURSE_CODE");
         	 $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = getAllCompletionAndPer($con,$user_id,$param);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}  

//Decree for creating group  
function decree_createGroup($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$ts_id = $param->ts_id;
    if($user_id >=0) {
        $ts_id = $param->ts_id;
		$group_id_arr = $param->group_id_arr;
		if($ts_id=='' || count($group_id_arr)==0){
		 $sr->setCode("EMPTY_TS_ID_OR_GROUP_ID");
         $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = createGroup($con,$user_id,$ts_id,$group_id_arr);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 


//Decree for  setting group activity score
function decree_saveGroupActivityScore($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $ts_id = $param->ts_id;
		if($ts_id==''){
		 $sr->setCode("EMPTY_TS_ID");
         $sr->setStat(0);
		 return $sr;
		}
		foreach($param->scoreArr as $obj) {
			$group_id = $obj->group_id;
			$activity_id = $obj->activity_id;
			$by_grp_id = $obj->by_grp_id;
			$score = $obj->score;
			
			$objRet  = saveGroupActivityScore($con,$user_id,$ts_id, $activity_id, $group_id, $by_grp_id, $user_id, $score);
			
		}
		
		$sr->setVal($objRet);
		$sr->setCode("SUCCESS");
		$sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

function decree_getUserRecordingReview($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $response_list = aduroGetUserRecordingReview($con,$user_id,$param);
		$sr->setVal($response_list);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}
function decree_getUserRecordingResponse($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
    if($user_id >=0) {
        $response_list = aduroGetUserRecordingResponse($con,$user_id,$param);
		$sr->setVal($response_list);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
    } else {
        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
}


//Decree for creating group  
function decree_getGroup($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$ts_id = $param->ts_id;
    if($user_id >=0) {
		if($ts_id==''){
		 $sr->setCode("EMPTY_TS_ID");
         $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = getGroup($con,$ts_id);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

//Decree for deleting teacher chapter session records  
function decree_removeTSGroups($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$ts_id = $param->ts_id;
    if($user_id >=0) {
		if($ts_id==''){
		 $sr->setCode("EMPTY_TS_ID");
         $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = removeTSGroups($con,$ts_id);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

//Decree for checking techer chapter session
function decree_chkTeacherChapterSession($token, $param, $extraParams = array()) {
    $con = createConnection();
    $user_id = tokenValidate($con,$token);
    $sr = new ServiceResponse("NO_ACTION",0,null);
	$batch_id = $param->batch_id;
	$chapter_edge_id = $param->edge_id;
    if($user_id >=0) {
		if($batch_id==''){
		 $sr->setCode("EMPTY_BATCH_ID");
         $sr->setStat(0);
		 return $sr;
		}
	
		$objRet  = chkTeacherChapterSession($con,$user_id,$batch_id,$chapter_edge_id);
		$sr->setVal($objRet);
        $sr->setCode("SUCCESS");
        $sr->setStat(1);
		
    } else {

        $sr->setCode("TOKEN_EXPIRED");
        $sr->setStat(0);
    }
    closeConnection($con);
    return $sr;
} 

//Apis for daily goal
function decree_set_goal($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = aduroSetGoal($con,$user_id,$param);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

function decree_get_goal($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = aduroGetGoal($con,$user_id);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}	

//Get daily goal and user streak
function decree_get_daily_goal($token, $param, $extraParams = array()) {		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {		
        $objRet  = aduroGetDailyGoal($con,$user_id,$param);		
        $sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

//logout
function decree_logout($token, $param, $extraParams = array()){		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$objRet  = aduroLogout($con,$user_id);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

function decree_deleteaccount($token, $param, $extraParams = array()){		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$objRet  = aduroDeleteAccount($con,$user_id);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}


//api to get user placement status
function decree_checkPlacement($token, $param, $extraParams = array()){		
    $con = createConnection();		
    $user_id = tokenValidate($con,$token);		
    $sr = new ServiceResponse("NO_ACTION",0,null);		
    if($user_id >=0) {	
		$objRet  = aduroGetUserPlacement($con,$token,$user_id,$param);
       	$sr->setVal($objRet);		
        $sr->setCode("SUCCESS");		
        $sr->setStat(1);		
    } else {		
        $sr->setCode("TOKEN_EXPIRED");		
        $sr->setStat(0);		
    }		
    closeConnection($con);		
    return $sr;		
}

?>