<?php
function createConnection() {
    ///EC2 Connection
	
	$dbname = "author_ilt";
    $dbuser =  "root";
    $dbpass  = "Liqvid@123";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    //$con=mysqli_connect("65.0.111.126",$dbuser, $dbpass, $dbname);
	$con=mysqli_connect("localhost",$dbuser, $dbpass, $dbname);
	//$con->set_charset("utf8");   
    if (mysqli_connect_errno()) {
        print mysqli_connect_errno()."ERROR IN MYSQL";
        return null;
    }
    return $con;
}

function closeConnection($con) {
    mysqli_close($con);

}

//gamification connection
function createConnectionGamification() {
	
	$dbname = "gamification";
    $dbuser =  "root";
    $dbpass  = "Liqvid@123#";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $con_g=mysqli_connect("65.0.142.231",$dbuser, $dbpass, $dbname);
	//$con_g->set_charset("utf8");   
    if (mysqli_connect_errno()) {
        print mysqli_connect_errno()."ERROR IN MYSQL";
        return null;
    }
    return $con_g;
}

function closeConnectionGamification($con_g) {
    mysqli_close($con_g);

}

function apiLogin($con, $login, $password, $deviceId,$platform,$class_name,$client_id){
	//file_put_contents('test/chk_login.txt','login '.$login.' Password '. $password.' devicid '.$deviceId.' plateform '.$platform.'  '.$class_name.' '.$client_id);
	global $chk_client;
	global $placementTest_path;
	//global $placementTest_path1;
	global $placementPage;
	
	//file_put_contents('test/chk_login2.txt','login '.$login.' Password '. $password.' devicid '.$deviceId.' plateform '.$platform.'  '.$class_name.' '.$client_id);

	$query1 = "SELECT user_id,roll_no from user where email_id=?";
	//file_put_contents("test/roll2.txt",$query1);
	$stmt1 = $con->prepare($query1);
	$stmt1->bind_param("s",$login);
	$stmt1->bind_result($userId,$roll_no);
	$stmt1->execute();
	$stmt1->fetch();
	$stmt1->close();
	//file_put_contents("test/roll.txt",$login." == ".$roll_no);
	if(!empty($roll_no)){
		$login=$roll_no;
	}else{
		$login=$login;
	}
	
	
	if($chk_client==1){
		$whrChk = "and b.user_client_id=?";
		$whr = "and b.user_client_id='".$client_id."'";
	}else{
		$whrChk = "";
		$whr = "";
	}
	
	
	$stmt = $con->prepare("SELECT a.user_id,a.is_active,first_name,middle_name,last_name,is_accepted,user_from from user_credential a, user b where loginid=? and password=? and a.user_id=b.user_id  $whrChk");
    if($stmt) {
		
		if($chk_client==1){
			$stmt->bind_param("ssi",$login,$password,$client_id);
		}else{
			$stmt->bind_param("ss",$login,$password);
		}
		
        if($stmt->execute()) {
				$query = "SELECT a.user_id,a.is_active,first_name,middle_name,last_name,is_accepted,user_from from user_credential a, user b where loginid=? and password=? and a.user_id=b.user_id $whr";
				$stmt->bind_param("ss",$login,$password);
                $stmt->bind_result($user_id,$is_active,$fname,$mname,$lname,$is_accepted,$user_from);
				$stmt->execute();
                $stmt->fetch();
                $stmt->close();
				//file_put_contents('test/chkk.txt',$query);
                /*Create a session code */
                if(!isset($user_id) || $user_id==="") {
                    return null;
                }
                if(!isset($is_active) || $is_active==="" || $is_active==0) {
                    
					return 'INACTIVE_USER';
                }
				
				//file_put_contents("test/userID.txt",$user_id);
               //check if a valid session already exists 
                $fetechstmt = $con->prepare("select session_id from api_session where user_id=? and valid_upto > NOW()");
                $fetechstmt->bind_param("i",$user_id);
                $fetechstmt->bind_result($ssid);
                $fetechstmt->execute();
                $fetechstmt->fetch();
                if(!isset($ssid) || $ssid===null || $ssid ==="") {
                    $part1 = md5($user_id);
                    $part2 = uniqid();
                    $entireKey = $part1.$part2;
                    $ssid = md5($entireKey);
                }
                $fetechstmt->close();	

				
				
				$stmt = $con->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ".$user_id);
				$stmt->execute();
				$stmt->bind_result($system_name);
				$stmt->fetch();
				$stmt->close();	
				
				$stmt = $con->prepare("SELECT role_definition_id FROM user_role_map WHERE user_id = ".$user_id);
				$stmt->execute();
				$stmt->bind_result($role_definition_id);
				$stmt->fetch();
				$stmt->close();	
				
				$stmt = $con->prepare("SELECT tc.region,tc.name,tc.exam_date FROM tblx_center tc join user_center_map ucm on tc.center_id=ucm.center_id WHERE ucm.user_id = ".$user_id);
				$stmt->execute();
				$stmt->bind_result($region_id,$center_name,$exam_date);
				$stmt->fetch();
				$stmt->close();	
				if($region_id!=""){
					$stmt = $con->prepare("SELECT tandc_link,policy_link,faq_link,exam_type,domain_url FROM tblx_region WHERE id = ".$region_id);
					$stmt->execute();
					$stmt->bind_result($tandc_link,$policy_link,$faq_link,$exam_type,$domain_url);
					$stmt->fetch();
					$stmt->close();	
				}

				
				$duration_in_days = '-1';				
				
				/* Create the session in the session table */
                $updatest = $con->prepare("insert INTO api_session(user_id,session_id,valid_upto) values(?,?,DATE_ADD(NOW(), INTERVAL +4 HOUR)) ON DUPLICATE KEY UPDATE session_id=?,valid_upto=DATE_ADD(NOW(), INTERVAL +4 HOUR)") or die ('some issue here '.$con->error);
                $xcv = $updatest->bind_param("iss",$user_id,$ssid,$ssid);
                $updatest->execute();
				
				$exam_date1 = strtotime($exam_date);
				$cTime1 = strtotime(date('Y-m-d 00:00:00'));
				if($exam_date1!=''){
					
				 $diffInSecondsExam = $cTime1 - $exam_date1;
				 $is_examDate=($diffInSecondsExam>=0)?'1':'0';
				}else{
				  $is_examDate='0';
				}
				
				////Code for MixPanel/////
		
				$post_data = array();

				$parentObj = new stdClass();
				$parentObj->eventName = 'Login';
				$parentObj->clientCode = 'CommonApp';
				$data = new stdClass();
				$data->user_id = $user_id;
				$data->first_name = $fname;
				$data->last_name = $lname;
				$data->loginid = $login;
				$data->client_code = 'CommonApp';  
				$data->timestamp = date('Y-m-d H:i:s');

				array_push($post_data,$data);
				//print_r($post_data);exit;
				$parentObj->userProps=$post_data;
				//$MTResponse=sendToMixPanel($parentObj);
				
				////Check for placement test
				if($user_from!='b2c' && $exam_type!="" ){
					if($domain_url=='ilt-staging.adurox.com'){
						$domain_url='iltnew.adurox.com';
					}
					$placementTest_path='https://'.$domain_url.$placementPage;
					$obj = new stdClass();	
					$exam_type1= explode(',',$exam_type);
					if($exam_type1[0]=='pre'){
						//file_put_contents("test/puser.txt",$user_id.'=='.$param->product_id);
						$stmt = $con->prepare("select id,exam_type from tblx_placement_result where user_id=".$user_id." AND exam_type='$exam_type1[0]'");
						$stmt->execute();
						$stmt->bind_result($placement_id_pre,$examTypePre);
						$stmt->fetch();
						$stmt->close();
						 
					}
					if($exam_type1[1]=='post'){
					    $stmt = $con->prepare("select id,exam_type from tblx_placement_result where user_id=".$user_id." AND exam_type='$exam_type1[1]'");
						$stmt->execute();
						$stmt->bind_result($placement_id_post,$examTypePost);
						$stmt->fetch();
						$stmt->close();
					}
					
					//file_put_contents("test/puser.txt",$user_id.'=='.$param->product_id);
					/*$stmt = $con->prepare("select id,exam_type from tblx_placement_result where user_id=".$user_id);
					$stmt->execute();
					$stmt->bind_result($placement_id,$examType);
					$stmt->fetch();
					$stmt->close();*/
					
					
					if(!empty($placement_id_pre)){
							$obj->placement_test_type = $exam_type;
							$obj->attempted = "yes";
							$obj->placement_url = "";
							$obj->attempted_pre = "yes";
							$obj->placement_url_pre = "";
							$obj->placement_type_pre = $examTypePre;
							if($exam_type1[1]=='post' && $user_id==976246){
								if(!empty($placement_id_post)){
									$obj->attempted_post = "yes";
									$obj->placement_url_post = "";
									$obj->placement_type_post = $examTypePost;
								}else{
									if($is_examDate==1){
										$obj->attempted_post = "no";
										$obj->placement_url_post = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[1]);
										$obj->placement_type_post = $exam_type1[1];
									}else{
										$obj->attempted_post = "";
										$obj->placement_url_post = "";
										$obj->placement_type_post = "";	
									}
								}
							}else{
							    $obj->attempted_post = "";
								$obj->placement_url_post = "";
								$obj->placement_type_post = "";	
							}
							$obj->level_assigned = "";
					}else{
							
						$obj->placement_test_type =$exam_type;	
						$obj->attempted = "no";
						if($class_name=='skilful'){
							$obj->placement_url = $placementTest_path1."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[0]);
						}else{
							//$obj->placement_url = 'https://'.$domain_url.$placementPage."?token=".$ssid."&user_id=".base64_encode($user_id);
							$obj->placement_url = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[0]);
							if($exam_type1[0]=='pre'){
							   $obj->attempted_pre = "no";
							   $obj->placement_url_pre = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[0]);
							   $obj->placement_type_pre = $exam_type1[0];
							}else{
								$obj->attempted_pre  ='';
								$obj->placement_url_pre ='';
								$obj->placement_type_pre = $exam_type1[0];
							}
							if($exam_type1[1]=='post'  && $user_id==976246){
								if($is_examDate==1){
									 $obj->attempted_post = "no";
									 $obj->placement_url_post = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[1]);
									 $obj->placement_type_post = $exam_type1[1];
								}else{
									$obj->attempted_post = "";
									$obj->placement_url_post = "";
									$obj->placement_type_post = "";	
								}
							  
							}else{
								$obj->attempted_post  ='';
								$obj->placement_url_post ='';
								$obj->placement_type_post = '';
							}
						}
						$obj->level_assigned = "";
						
					}
					$obj->is_accepted = $is_accepted;
					$obj->product_id_placement = 1;
				}else{
					$obj->placement_test_type = "";
					$obj->attempted = "";
					$obj->placement_url = "";
					$obj->attempted_pre  ='';
					$obj->placement_url_pre ='';
					$obj->placement_type_pre = '';
					$obj->attempted_post  ='';
					$obj->placement_url_post ='';
					$obj->placement_type_post = '';
					$obj->level_assigned = "";
					$obj->is_accepted = "1";
					$obj->product_id_placement = "";
				}
				/////////////////////////////////
				
				////Code for MixPanel///// 
				
				
                $obj->token = $ssid;
				$obj->name = $fname." ";
                if(isset($lname))
                    $obj->name .= $lname;
					$obj->user_id = $user_id;
					if(empty($system_name))
					{
					$system_name="default_profile.jpg";
					}
					$obj->profile_pic = $system_name;	
					$obj->role_id = $role_definition_id;
					$obj->center_name = $center_name;
					$obj->user_from = $user_from;

					$obj->about_us_link = $tandc_link;
					$obj->policy_link = $policy_link;
					$obj->faq_link = $faq_link;

					return $obj;	
		
		}
	}
}

function aduroGetUserPlacement($con,$token,$user_id,$param){
	//global $placementTest_path;
   /* global $placementTest_path1;
	global $placementPage;
	$stmt = $con->prepare("SELECT tc.region,tc.name,tc.exam_date FROM tblx_center tc join user_center_map ucm on tc.center_id=ucm.center_id WHERE ucm.user_id = ".$user_id);
	$stmt->execute();
	$stmt->bind_result($region_id,$center_name,$exam_date);
	$stmt->fetch();
	$stmt->close();	
	if($region_id!=""){
		$stmt = $con->prepare("SELECT tandc_link,policy_link,faq_link,exam_type,domain_url FROM tblx_region WHERE id = ".$region_id);
		$stmt->execute();
		$stmt->bind_result($tandc_link,$policy_link,$faq_link,$exam_type,$domain_url);
		$stmt->fetch();
		$stmt->close();	
	}
	//file_put_contents("test/puser.txt",$user_id.'=='.$param->product_id);
	////Check for placement test
	  if($exam_type!="" ){
		  if($domain_url=='ilt-staging.adurox.com'){
						$domain_url='iltnew.adurox.com';
					}
		       $placementTest_path='https://'.$domain_url.$placementPage;
		       $exam_date1 = strtotime($exam_date);
				$cTime1 = strtotime(date('Y-m-d 00:00:00'));
				if($exam_date1!=''){
					
				 $diffInSecondsExam = $cTime1 - $exam_date1;
				 $is_examDate=($diffInSecondsExam>=0)?'1':'0';
				}else{
				  $is_examDate='0';
				}
					
					$obj = new stdClass();	
					$exam_type1= explode(',',$exam_type);
					if($exam_type1[0]=='pre'){
						//file_put_contents("test/puser.txt",$user_id.'=='.$param->product_id);
						$stmt = $con->prepare("select id,exam_type from tblx_placement_result where user_id=".$user_id." AND exam_type='$exam_type1[0]'");
						$stmt->execute();
						$stmt->bind_result($placement_id_pre,$examTypePre);
						$stmt->fetch();
						$stmt->close();
						 
					}
					if($exam_type1[1]=='post' && $user_id==976246){
					    $stmt = $con->prepare("select id,exam_type from tblx_placement_result where user_id=".$user_id." AND exam_type='$exam_type1[1]'");
						$stmt->execute();
						$stmt->bind_result($placement_id_post,$examTypePost);
						$stmt->fetch();
						$stmt->close();
					}
					
					//file_put_contents("test/puser.txt",$user_id.'=='.$param->product_id);
					//$stmt = $con->prepare("select id,exam_type from tblx_placement_result where user_id=".$user_id);
					//$stmt->execute();
					//$stmt->bind_result($placement_id,$examType);
					//$stmt->fetch();
                     //$stmt->close();
					
					if(!empty($placement_id_pre)){
						    $obj->placement_test_type = $exam_type;
							$obj->attempted = "yes";
							$obj->placement_url = "";
							$obj->attempted_pre = "yes";
							$obj->placement_url_pre = "";
							$obj->placement_type_pre = $examTypePre;
							if($exam_type1[1]=='post' && $user_id==976246){
								if(!empty($placement_id_post)){
									$obj->attempted_post = "yes";
									$obj->placement_url_post = "";
									$obj->placement_type_post = $examTypePost;
								}else{
									if($is_examDate==1){
										 $obj->attempted_post = "no";
										 $obj->placement_url_post = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[1]);
										 $obj->placement_type_post = $exam_type1[1];
									}else{
										$obj->attempted_post = "";
										$obj->placement_url_post = "";
										$obj->placement_type_post = "";	
									}
									
								}
							}else{
							    $obj->attempted_post = "";
								$obj->placement_url_post = "";
								$obj->placement_type_post = "";	
							}
							$obj->level_assigned = "";
							
					}else{
						$obj->placement_test_type =$exam_type;	
						$obj->attempted = "no";
						if($class_name=='skilful'){
							$obj->placement_url = $placementTest_path1."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[0]);
						}else{
							$obj->placement_url = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[0]);
							if($exam_type1[0]=='pre'){
							   $obj->attempted_ppre = "no";
							   $obj->placement_url_pre = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[0]);
							   $obj->placement_type_pre = $exam_type1[0];
							}else{
								$obj->attempted_pre  ='';
								$obj->placement_url_pre ='';
								$obj->placement_type_pre = $exam_type1[0];
							}
							if($exam_type1[1]=='post'){
								if($is_examDate==1){
										$obj->attempted_post = "no";
										$obj->placement_url_post = $placementTest_path."?token=".$ssid."&user_id=".base64_encode($user_id).'&type='.base64_encode($exam_type1[1]);
										$obj->placement_type_post = $exam_type1[1];
									}else{
										$obj->attempted_post = "";
										$obj->placement_url_post = "";
										$obj->placement_type_post = "";	
									}
							   
							}else{
								$obj->attempted_post  ='';
								$obj->placement_url_post ='';
								$obj->placement_type_post = '';
							}
						}
						$obj->level_assigned = "";
						
					}
					$obj->is_accepted = $is_accepted;
					$obj->product_id_placement = 1;
				}else{
					$obj->attempted = "";
					$obj->placement_url = "";
					$obj->attempted_pre  ='';
					$obj->placement_url_pre ='';
					$obj->placement_type_pre = '';
					$obj->attempted_post  ='';
					$obj->placement_url_post ='';
					$obj->placement_type_post = '';
					$obj->level_assigned = "";
					$obj->is_accepted = "1";
					$obj->product_id_placement = "";
					$obj->placement_test_type = '';
				}
	    return $obj;*/
		
			
	    
       $obj = new stdClass();	
		$obj->attempted = "";
		$obj->placement_url = "";
		$obj->attempted_pre  ='';
		$obj->placement_url_pre ='';
		$obj->placement_type_pre = '';
		$obj->attempted_post  ='';
		$obj->placement_url_post ='';
		$obj->placement_type_post = '';
		$obj->level_assigned = "";
		$obj->is_accepted = "1";
		$obj->product_id_placement = "";
		$obj->placement_test_type = '';
				
	    return $obj;
 }


function apiRegister($con, $param,$class_name,$client_id){
	$alert_msg_arr = alertMessage();
	$curDate = date('Y-m-d');
	
	
	$isOTPBased=$param->is_otp_based;		
	if($isOTPBased==1 && $param->country_code=='IN')		
	{		
	$stmt = $con->prepare("select user_id,loginid from user_credential where loginid=?");		
	$stmt->bind_param("s",$param->mobile);		
	$stmt->execute();		
	$stmt->bind_result($user_id,$loginid);		
	$stmt->fetch();		
	$stmt->close();		
	}
	elseif($isOTPBased==1 && $param->country_code!='IN')		
	{		
	$stmt = $con->prepare("select user_id,loginid from user_credential where loginid=?");		
	$stmt->bind_param("s",$param->email_id);		
	$stmt->execute();		
	$stmt->bind_result($user_id,$loginid);		
	$stmt->fetch();		
	$stmt->close();
	}
	else		
	{
	$stmt = $con->prepare("select c.user_id,loginid, u.address_id from user u, user_credential c where email_id=? and c.user_id=u.user_id");
	$stmt->bind_param("s",$param->email_id);
	$stmt->execute();
	$stmt->bind_result($user_id,$loginid, $address_id);
	$stmt->fetch();
	$stmt->close();
	}
	
	
	
	if(isset($user_id) && !empty($user_id) ) {	
		
		$sr = new ServiceResponse("EXISTS",0,null);
		$sr->retVal = new stdClass();
		$sr->retVal->msg = $alert_msg_arr['REGISTER_PACKAGE_CODE_ALREADY_REGISTERED_USER'];
		return $sr;            
            
	}
	// create address master  
	$stmt= $con->prepare("Insert into address_master (phone, modified_date, created_date) Values(?, NOW(), NOW() ) ");
	$stmt->bind_param("s",$param->mobile);
	$stmt->execute();
	$stmt->close();
	$address_id = $con->insert_id;
	
	//insert for assets
	$system_name='default_profile.jpg';
	$stmt = $con->prepare("INSERT INTO asset(updated_by,system_name,created_date) VALUES('1',?,NOW())");
	$stmt->bind_param("s",$system_name);
	$stmt->execute();
	$stmt->close();
	$asset_id = $con->insert_id;	
	


	
	$stmt= $con->prepare("update address_master set country = ?, modified_date = NOW() where address_id = ? ");
	$stmt->bind_param("si",$param->country, $address_id);
	$stmt->execute();
	$stmt->close();
	
	 if($isOTPBased==1 && $param->country_code=='IN')		
		{		
		$userLoginId=$param->mobile;		
		$stmt= $con->prepare("update address_master set is_phone_verified='1' where address_id = ? ");		
		$stmt->bind_param("i",$address_id);		
		$stmt->execute();		
		$stmt->close();
		
		$is_phone_verified=1;
		$is_email_verified=0;		
		}
		
		else		
		{
		$userLoginId=$param->email_id;		
				
		$is_phone_verified=0;
		$is_email_verified=1;		
		}

		////Select the client to user group id 
	$stmt = $con->prepare("Select user_group_id from client WHERE client_id=?");
	$stmt->bind_param("i",$client_id);
	$stmt->execute();
	$stmt->bind_result($user_group_id);
	$stmt->fetch();
	$stmt->close();
	$client_group_id = $user_group_id;
	
	$center_id=0;
	$batch_id=0;
	// Create  user 
	if(!isset($param->email_id)){ $param->email_id = "";}
	$roll_no=$param->roll_no?',roll_no':'';
	$roll_no_v=$param->roll_no?',?':'';
	$stmt= $con->prepare("insert into user(first_name,last_name,date_of_birth,email_id,is_email_verified,ex_phone,ex_is_phone_verified,ex_loginid,ex_password,ex_role_definition_id,ex_user_group_id,address_id,updated_by,modified_date,created_date,user_client_id,profile_pic,user_from,ex_role_name,ex_system_name $roll_no) values(?, ?, NOW(), ?, ?, ?, ?, ?,?,2,?, ?, 1, NOW(), NOW(),?,?,'b2c','learner',? $roll_no_v)");
	if($param->roll_no){
	$stmt->bind_param("ssssssssiiiiss",$param->first_name,$param->last_name,$param->email_id,$is_email_verified,$param->mobile,$is_phone_verified,$userLoginId,$param->password,$user_group_id,$address_id,$client_id,$asset_id,$system_name,$param->roll_no);
	}else{
		$stmt->bind_param("ssssssssiiiis",$param->first_name,$param->last_name,$param->email_id,$is_email_verified,$param->mobile,$is_phone_verified,$userLoginId,$param->password,$user_group_id,$address_id,$client_id,$asset_id,$system_name);
	}
	$stmt->execute();
	$user_id = $con->insert_id;
	$stmt->close();	
	
	$stmt= $con->prepare("insert into user_credential(user_id,loginid,password,updated_by,modified_date,created_date) values(?,?,?,1,NOW(), NOW() )");
	$stmt->bind_param("iss",$user_id,$userLoginId,$param->password);
	$stmt->execute();
	$stmt->close();
	
	
	
	//Adding user into role map group 
	if(isset($param->role_id) && $param->role_id!='')
	{ $role_id=$param->role_id;
	}
	else{ $role_id=2;}
	
	$stmt = $con->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(?,?,?,1,1,NOW())");
	$stmt->bind_param("iii",$user_id,$role_id,$client_group_id);
	$stmt->execute();
	$stmt->close(); 
		
	//file_put_contents('test/cclass.txt',$class_name);	
	if(isset($param->batch_id) && $param->batch_id!=''){ 
         $batch_id=$param->batch_id;
		 
	}else{
		if($class_name=='skilful'){
		  $batch_id=4;
		}else{
		  $batch_id=1;
		}
	 }
	
	if(isset($param->center_id) && $param->center_id!=''){
		
		$center_id=$param->center_id;
	}else{

		if($class_name=='skilful'){
		  $center_id=2;
		}else{
		  $center_id=1; 
		}
	}
		
		$stmt= $con->prepare("update user set ex_center_id='$center_id' where user_id = ? ");		
		$stmt->bind_param("i",$user_id);		
		$stmt->execute();		
		$stmt->close();	
		
		
		$stmt = $con->prepare("SELECT region FROM tblx_center WHERE center_id = ".$center_id);
		$stmt->execute();
		$stmt->bind_result($region_id);
		$stmt->fetch();
		$stmt->close();	

		$stmt = $con->prepare("SELECT tandc_link,policy_link,faq_link FROM tblx_region WHERE id = ".$region_id);
		$stmt->execute();
		$stmt->bind_result($tandc_link,$policy_link,$faq_link);
		$stmt->fetch();
		$stmt->close();	

			//file_put_contents('test/class1.txt',$center_id."==".$batch_id);		
			
		//// Adding user and center map 
		$stmt = $con->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values(?,?,?,NOW())");
		$stmt->bind_param("iii", $user_id,$center_id,$client_id);
		$stmt->execute();
		$stmt->close(); 
			
		//For batch_user_map table insert
		$stmt = $con->prepare("insert into tblx_batch_user_map (user_id, batch_id, center_id,status,user_server_id) values (?,?,?,1,?)");
		$stmt->bind_param("iiii",$user_id,$batch_id,$center_id,$user_id);
		$stmt->execute();
		$stmt->close(); 
	//}
	
		$stmt = $con->prepare("SELECT tc.name FROM tblx_center tc join user_center_map ucm on tc.center_id=ucm.center_id WHERE ucm.user_id = ".$user_id);
		$stmt->execute();
		$stmt->bind_result($center_name);
		$stmt->fetch();
		$stmt->close();	
				
		
	////Send to collection/////
		$parentObj = new stdClass();
		$parentObj->user_id = $user_id;
		$parentObj->user_name = $param->first_name.' '.$param->last_name;
		$parentObj->date_of_birth = date('Y-m-d');
		$parentObj->email_id = $param->email_id;
		$parentObj->is_email_verified = $is_email_verified;
		$parentObj->is_active = '1';
		$parentObj->updated_by = '1';
		$parentObj->created_date = date('Y-m-d H:i:s');
		$parentObj->modified_date = date('Y-m-d H:i:s');
		$parentObj->user_client_id = $client_id;
		$parentObj->loginid = $userLoginId;
		$parentObj->password = $param->password;
		$parentObj->role_definition_id = 2;
		$parentObj->user_group_id = $client_group_id;
		$parentObj->phone = $param->mobile;
		$parentObj->is_phone_verified = $is_phone_verified;
		$parentObj->center_id = $center_id;
		$parentObj->batch_id = $batch_id;
		$parentObj->action = 'add_user';
		$parentObj->course_id = '';
		$parentObj->course_code = '';
		$parentObj->course_name = '';
		$parentObj->mother_tongue = '';
		$parentObj->education = '';
		$parentObj->user_from = 'b2c';
		$parentObj->batch_unique_id = '';
		
		//sendToCollection($parentObj);
		
		//send to batch user map
		$parentObj = new stdClass();
		$parentObj->id = $id;
		$parentObj->center_id = $center_id;
		$parentObj->batch_id = $batch_id;
		$parentObj->user_id = $user_id;
		$parentObj->status = 1;
		$parentObj->action = 'add_user_batch_map';
		//sendToCollection($parentObj);	
		
		
	////Code for MixPanel/////

	$post_data = array();

	$parentObj = new stdClass();
	$parentObj->eventName = 'Signup';
	$parentObj->clientCode = 'CommonApp';

	$data = new stdClass();
	$data->user_id = $user_id;
	$data->first_name = $param->first_name;
	$data->last_name = $param->last_name;
	$data->email_id = $param->email_id;
	$data->phone = $param->mobile;
	$data->loginid = $userLoginId;


	array_push($post_data,$data);
	$parentObj->userProps=$post_data;
	$MTResponse=sendToMixPanel($parentObj);
	
	////Code for MixPanel/////
	
		
	//Check token
	$fetechstmt = $con->prepare("select session_id from api_session where user_id=? and valid_upto > NOW()");
	$fetechstmt->bind_param("i",$user_id);
	$fetechstmt->bind_result($ssid);
	$fetechstmt->execute();
	$fetechstmt->fetch();
	if(!isset($ssid) || $ssid===null || $ssid ==="") {
		$part1 = md5($user_id);
		$part2 = uniqid();
		$entireKey = $part1.$part2;
		$ssid = md5($entireKey);
	}
	$fetechstmt->close();

	$updatest = $con->prepare("insert INTO api_session(user_id,session_id,valid_upto) values(?,?,DATE_ADD(NOW(), INTERVAL +4 HOUR)) ON DUPLICATE KEY UPDATE session_id=?,valid_upto=DATE_ADD(NOW(), INTERVAL +4 HOUR)") or die ('some issue here '.$con->error);
	$xcv = $updatest->bind_param("iss",$user_id,$ssid,$ssid);
	$updatest->execute();
		
	$sr = new ServiceResponse("SUCCESS",0,null);	
	$retVal->token = $ssid;
	$retVal->name = $param->first_name." ";
	if(isset($param->last_name))
		$retVal->name .= $param->last_name;
	$retVal->msg = $alert_msg_arr['REGISTER_SUCCESS'];
	$retVal->user_id = $user_id;
	$retVal->role_id = $role_id;
	$retVal->profile_pic = $system_name;
	$retVal->center_name = $center_name;
	$retVal->is_accepted = "1";
	$retVal->attempted = "";
	$retVal->placement_url = "";
	$retVal->product_id_placement = "";
	$retVal->user_from = 'b2c';
	$retVal->about_us_link = $tandc_link;
	$retVal->policy_link = $policy_link;
	$retVal->faq_link = $faq_link;
	$sr->setval($retVal);

    return $sr;
	
}

function resetAndSendPassword($con,$login_id,$class_name) {

    $stmt = $con->prepare("select u.first_name,password,email_id from user_credential c, user u where loginid=? and c.user_id=u.user_id");
    $stmt->bind_param("s",$login_id);
    $stmt->bind_result($fname, $password,$emailid);
    $stmt->execute();
    if($stmt->fetch()) {
       
        if(filter_var($emailid,FILTER_VALIDATE_EMAIL)) {
			
			////send here
			$cmd  = "echo \"$str\" | java -jar ./SendMail.jar \"$emailid\" \"Your Password for App\"";
				$retval = `$cmd`;
		
            $sr = new ServiceResponse("SUCCESS",1,null);
            $sr->retVal->msg = "Password has been sent to $emailid";
            $stmt->close();
            return $sr;
        } else {
            $stmt->close();
            $sr = new ServiceResponse("FAILURE",0,null);
            $sr->retVal->msg = "Password could not be sent as your email id is not valid";
            return $sr;
        }
    }
    $stmt->close();
    $sr = new ServiceResponse("NFOUND",0,null);
    $sr->retVal->msg = "Password could not be sent as there is no user record with this loginid";
    return $sr;

}


//get product by user
function getProductByUser($con,$user_id){
	global $authoring_path;
	$con2 = createConnection();
	/* $getProductUserInfo = getPaymentStatusByUserId($con,$user_id);
	if(count($getProductUserInfo)>0){
		return $getProductUserInfo;
	}else{ */
	
	$batch_arr = array();	
	//get user batch and center
	$stmt = $con->prepare("select tb.batch_id,tb.center_id,tb.batch_name from tblx_batch_user_map tbm join tblx_batch tb on tbm.batch_id=tb.batch_id and tbm.center_id=tb.center_id where tbm.user_id=? and tbm.status='1'");
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($batch_id,$center_id,$batch_name);
	while($stmt->fetch()) { 
	$obj = new stdclass();
	$obj->batch_id = $batch_id;
	$obj->center_id = $center_id;
	$obj->batch_name = $batch_name;
	array_push($batch_arr,$obj);
	}
	$stmt->close();
	
	$stmt = $con->prepare("SELECT role_definition_id FROM user_role_map WHERE user_id = ?");
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($role_definition_id);
	$stmt->fetch();
	$stmt->close();	
	
	
	
	$batch_product_arr = array();
	foreach($batch_arr as $key=>$val){
		
		$product_arr = array();
		$stmt = $con->prepare("select entity_type,course,topic,chapter,tp.id,client_id,product_name,product_desc,thumbnail,package_code,master_products_ids,product_type from tblx_product_configuration tpc join tbl_product tp on tpc.product_id=tp.id where tpc.batch_id=? and tpc.institute_id=?");
		$stmt->bind_param("ii",$val->batch_id,$val->center_id);
		$stmt->execute();
		$stmt->bind_result($entity_type,$course,$topic,$chapter,$product_id,$client_id,$product_name,$product_desc,$thumbnail,$package_code,$master_products_ids,$product_type);
		while($stmt->fetch()) { 
		
			$obj = new stdclass();
			$obj->entity_type = $entity_type;
			$obj->course = $course;
			$obj->batch_id = $batch_id;
			$obj->center_id = $center_id;
			$obj->product_id = $product_id;
			$obj->client_id = $client_id;
			$obj->product_name = $product_name;
			$obj->product_desc = $product_desc;
			$obj->is_purchased = true; 
			if($thumbnail!=""){
				$obj->thumbnail = $authoring_path."/view/images/product_thumb/".$thumbnail;
			}else{
				$obj->thumbnail = $authoring_path."/view/images/product_thumb/product1.jpg";
			}
			$obj->package_code = $package_code;
			$obj->master_products_ids = $master_products_ids;
			$obj->product_type = $product_type;
			array_push($product_arr,$obj);
		}
		$stmt->close();
		
		$obj = new stdclass();
		$obj->batch_id = $val->batch_id;
		$obj->center_id = $val->center_id;
		$obj->batch_name = $val->batch_name;
		$obj->product_arr = $product_arr;
		array_push($batch_product_arr,$obj);
	
	}
	
	
	$batch_product_course_arr= array();
	////Code for MixPanel/////
	$post_data = array();
	$parentObj = new stdClass();
	$parentObj->eventName = 'CourseAssignment';
	$parentObj->clientCode = 'CommonApp';
	
	$stmt = $con->prepare("SELECT a.loginid,first_name,middle_name,last_name from user_credential a join  user b on a.user_id=b.user_id where b.user_id=?");
	$stmt->bind_param("i",$user_id); 
	$stmt->execute(); 
	$stmt->bind_result($loginid,$fname,$mname,$lname);
	$stmt->fetch();
	$stmt->close();
		
	foreach($batch_product_arr as $bPkey=>$bPval){
		
		$product_course_arr = array();	
		foreach($bPval->product_arr as $pKey=>$pVal){
			
			$obj = new stdclass();
			$obj->entity_type = $pVal->entity_type;
			$obj->product_id = $pVal->product_id;
			$obj->client_id = $pVal->client_id;
			$obj->product_name = $pVal->product_name;
			$obj->product_desc = $pVal->product_desc;
			$obj->is_purchased = $pVal->is_purchased; 
			$obj->thumbnail = $pVal->thumbnail; 
			$obj->package_code = $pVal->package_code;
			$obj->master_products_ids = $pVal->master_products_ids;
			$obj->product_type = $pVal->product_type;
			
			$crs_arr = explode(',',$pVal->course);
			$list_course_wbt = $list_course_ilt = array();
			foreach($crs_arr as $key=>$val){
				
				$stmt = $con->prepare("SELECT c.course_id,gmt.edge_id, c.code, c.title, c.description, c.course_type, c.duration, c.thumnailImg FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE c.code=?");
				$stmt->bind_param("s",$val);
				$stmt->execute();
				$stmt->bind_result($course_id,$edge_id,$course_code,$title, $description, $course_type, $duration, $thumnailImg);
				while($stmt->fetch()) {
					$obj_c = new stdclass();
					$obj_c->edge_id = $edge_id;
					$obj_c->code = stripslashes(publishText($course_code));
					$obj_c->title = stripslashes(publishText($title));
					$obj_c->description = stripslashes(publishText($description));
					$obj_c->duration = $duration;
					$obj_c->course_type = $course_type;
					if($thumnailImg!=""){
						$thumnailImg=$authoring_path."/view/uploads/".$thumnailImg;
						$obj_c->imgPath = stripslashes(publishText($thumnailImg));
					}else{
						//$thumnailImg=$authoring_path."/view/images/".$course_code.".png";
						$thumnailImg=$authoring_path."/view/images/default_course.png";
						$obj_c->imgPath = $thumnailImg;
					}
					
					
					$stmt_c = $con2->prepare("select st.standard, slm.level_text,slm.level_description,slm.level_cefr_map from tblx_standards st, tblx_standards_levels slm, course c where c.standard_id=st.id and c.level_id=slm.id and c.code=?");
					$stmt_c->bind_param("s",$course_code);
					$stmt_c->execute();
					$stmt_c->bind_result($standard,$level_text,$level_description,$level_cefr_map);
					$stmt_c->execute();
					$stmt_c->fetch();
					$stmt_c->close();
					
					$obj_c->standard = $standard;
					$obj_c->level_text = $level_text;
					$obj_c->level_description = $level_description;
					$obj_c->level_cefr_map = $level_cefr_map;
					
					
					if($course_type=='1' && $role_definition_id=='1'){
						array_push($list_course_ilt,$obj_c);
					}elseif($course_type=='0'){
						array_push($list_course_wbt,$obj_c);
					}
				}
				$stmt->close();	
			
			//code for mixpanel
			$data = new stdClass();
			$data->user_id = $user_id;
			$data->first_name = $fname;
			$data->last_name = $lname;
			$data->loginid = $loginid;
			$data->course_id = $course_id;
			$data->course_code = $val;
			$data->course_name = $title;
			$data->timestamp = date('Y-m-d H:i:s');
			$data->client_code = 'CommonApp';
			array_push($post_data,$data);
			$parentObj->userProps=$post_data;
			//$MTResponse=sendToMixPanel($parentObj);
			
			}
			
			$obj->list_course_ilt = $list_course_ilt;
			$obj->list_course_wbt = $list_course_wbt;
			$obj->resource_pdf = $authoring_path.'/live/product_manuals/'.$pVal->product_id.'.pdf';
			
			array_push($product_course_arr,$obj);
		
		}
		
	
		$obj = new stdclass();
		$obj->batch_id = $bPval->batch_id;
		$obj->center_id = $bPval->center_id;
		$obj->batch_name = $bPval->batch_name;
		$obj->product_arr = $product_course_arr;
		array_push($batch_product_course_arr,$obj);
	}
		
	closeConnection($con);
	closeConnection($con2);
	return $batch_product_course_arr;
	
}		
//} 





function getBatchDataByIDDetails($con,$batch_id,$centerId){
		
			$sql = "Select type,is_enabled from tblx_product_configuration WHERE batch_id = ? AND institute_id = ?";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii', $batch_id, $centerId);
			$stmt->execute();
			$stmt->bind_result($type,$is_enabled);
			$productArr=array();
			while($stmt->fetch()) { 
				$obj = new stdclass();
				$obj->type = $type;
				$obj->is_enabled = $is_enabled;
				$productArr[]=$obj;
			}
			$stmt->close();
			
			return $productArr;
		
		
}

function getProductIdFromCourse($con,$course_id){
		$sql = "SELECT product_id from  course where course_id IN($course_id)  group by product_id";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($product_id);
		$productArr= array();
		while($stmt->fetch()) {
			$obj = new stdclass();
			$obj->product_id = $product_id;
			$productArr[]=$obj;
		}
		closeConnection($con);
		return $productArr;
	}
	
function getPaymentStatusByUserId($con,$user_id){
		$prodArr = array();
		$sql = "SELECT tpp.product_id, tp.client_id,tp.standard_id, tp.product_name ,tp.product_desc ,tpp.payment_status,tpp.payment_date,tp.thumbnail,tp.package_code,tp.master_products_ids,tp.product_type from  tbl_product_purchase as tpp JOIN tbl_product AS tp ON tp.id=tpp.product_id WHERE user_id =? and payment_status='success'";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i', $user_id);
		$stmt->execute();
		$stmt->bind_result($product_id,$client_id,$standard_id,$product_name,$product_desc,$payment_status,$payment_date,$thumbnail,$package_code,$master_products_ids,$product_type);
		$productArr=array();
		while($stmt->fetch()) { 
			$obj = new stdclass();
			$obj->product_id = $product_id;
			$obj->client_id = $client_id;
			$obj->payment_status = $payment_status;
			$obj->payment_date = $payment_date;
			$obj->product_name = $product_name;
			$obj->product_desc = $product_desc;
			$obj->thumbnail = $thumbnail;
			$obj->package_code = $package_code;
			$obj->master_products_ids = $master_products_ids;
			$obj->product_type = $product_type;
			$productArr[]=$obj;
	 }
	
	$productChkArr = array();
	foreach($productArr as $product_key=>$product_val){
		
			
		$master_products_ids	= explode(',',$product_val->master_products_ids);
		
		foreach($master_products_ids as $master_product_key=>$master_product_val){
		
		$stmt = $con->prepare("SELECT name,is_show_dashboard FROM tbl_master_product_list WHERE id =? and publish ='yes'");
		$stmt->bind_param("i",$master_product_val);
		$stmt->execute();
		$stmt->bind_result($name,$is_show_dashboard);
		while($stmt->fetch()) {
			
			$obj = new stdclass();
			$obj->product_id = $product_val->product_id;
			$obj->client_id = $product_val->client_id;
			$obj->product_name = $product_val->product_name;
			$obj->product_desc = $product_val->product_desc;
			if($product_val->thumbnail!=""){
				$product_val->thumbnail = $authoring_path."/images/product_thumb/".$product_val->thumbnail;
			}else{
				$product_val->thumbnail = $authoring_path."/images/product_thumb/product1.jpg";
			}
			$obj->thumbnail = $product_val->thumbnail;
			$obj->package_code = $product_val->package_code;
			$obj->master_product_name = $name;
			$obj->is_show_dashboard = $is_show_dashboard;
			$obj->is_purchased = true; 
			$productChkArr[]= $obj;
			
			
		}
		$stmt->close();
	}
		
	}
	closeConnection($con);
	return $productChkArr;
}


function getClientIdByClassName($class_name){
	/*$con = createConnection();
	$query = "SELECT client_id from  client where class_name=?";
	$stmt = $con->prepare($query);
	$stmt->bind_param('s',$class_name);
	$stmt->execute();	
	$stmt->bind_result($client_id);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	if(isset($client_id) || $client_id!==null || $client_id !=="") {
               return $client_id;
	}

	 
	closeConnection($con);
	return false;*/
	return 46;
}

function getUserProfileCompletion($con,$user_id){

	$stmt = $con->prepare("SELECT a.user_id,a.first_name,a.middle_name,a.last_name,a.email_id,a.is_email_verified,a.address_id,a.age_range,a.gender,a.education,a.employment_status,a.joining_purpose,a.marital_status,a.mother_tongue,a.exculsive_offers,a.instructions_tips,a.news_discount,a.years_eng_edu,a.is_scheduled_for_certificate,a.discount_readiness, b.loginid,c.phone,c.is_phone_verified,c.city,c.state,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=$user_id and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt->execute();
	$stmt->bind_result($user_id,$first_name, $middle_name, $last_name, $email_id, $is_email_verified,$address_id, $age_range, $gender, $education, $employment_status, $joining_purpose,$marital_status,$mother_tongue,$exculsive_offers,$instructions_tips, $news_discount,$years_eng_edu,$is_scheduled_for_certificate,$discount_readiness,$loginid,$phone,$is_phone_verified,$city,$state,$country_code,$country);
	$stmt->fetch();
	$stmt->close();

	
	$obj = new stdclass();
	
	$obj->first_name = $first_name;
	$obj->email_id = $email_id;
	$obj->gender = $gender;
	$obj->age_range = $age_range;
	$obj->joining_purpose = $joining_purpose;
	$obj->country = $country;
	if(empty($is_scheduled_for_certificate))
	{
	$is_scheduled_for_certificate='0';
	}
	$obj->is_scheduled_for_certificate = $is_scheduled_for_certificate;

	$profile_completion=getProfileCompletion($first_name,$email_id,$gender,$age_range,$country,$joining_purpose,$is_scheduled_for_certificate);

	$obj->profile_completion = $profile_completion;
	closeConnection($con);
	return $obj;

}

function getDataByLevel($con,$user_id,$param){
	
	$level_arr = $param->level_arr;
	$package_code = $param->package_code;
	$level_text =  $level_arr->level_text;
	$course_arr =  $level_arr->course_arr;
	$level_time_arr = $level_chapter_arr  = $level_chapter_complt_arr =  $level_chapter_not_complt_arr = $level_earned_coins_arr = $skill_ques_arr = $skill_ques_crrct_arr = $skill_arr =$level_skill_arr = array();
	
	foreach($course_arr as $key1=>$course_code){
		
		$courseID = getCourseIdByCourseCode($course_code);
		
		if($courseID){
			
			$course_edge_id = getCourseEdgeIdByCourseId($courseID);
			
			//total time spent in course
			$duration_ms = 0;
			$stmt = $con->prepare("SELECT COALESCE(SUM(ust.actual_seconds), 0) as ttlCompTimeSp from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
			JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
			JOIN user_session_tracking ust ON ust.session_id= tc.component_edge_id 
			where gmt.tree_node_super_root = ? AND ust.user_id = ? AND course_code = ? AND ust.session_type = 'CM' AND ust.unique_code = ? AND LENGTH(ust.unique_code) >= 10  GROUP BY ust.unique_code, ust.course_code");
			$stmt->bind_param("iiss",$course_edge_id,$user_id,$course_code,$package_code);
			$stmt->execute();
			$stmt->bind_result($duration_ms);
			$stmt->fetch();
			$stmt->close();
			
			$duration_ms = !empty($duration_ms)?$duration_ms:0;
			$level_time_arr[] = $duration_ms;
			
			//Number of chapter
			$number_of_chapters = 0;
			$stmt = $con->prepare("SELECT count(cm.session_node_id) as 'cnt' from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tnd.tree_node_category_id=2  AND gmt.is_active = 1");
			$stmt->bind_param("i",$course_edge_id);
			$stmt->execute();
			$stmt->bind_result($number_of_chapters);
			$stmt->fetch();
			$stmt->close();	
			
			$number_of_chapters = !empty($number_of_chapters)?$number_of_chapters:0;
			$level_chapter_arr[] = $number_of_chapters;
			
			//get completed chapter 
			$number_of_completed_chapter = 0;
			$stmt = $con->prepare("SELECT count(tcc.id) as 'cnt' from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
			JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tcc.course_code = ? AND tcc.user_id = ? AND tcc.completion = 'c'  AND tnd.tree_node_category_id=2  AND gmt.is_active = 1");
			$stmt->bind_param("isi",$course_edge_id,$course_code,$user_id);
			$stmt->execute();
			$stmt->bind_result($number_of_completed_chapter);
			$stmt->fetch();
			$stmt->close();	  
			$number_of_completed_chapter = !empty($number_of_completed_chapter)?$number_of_completed_chapter:0;
			$level_chapter_complt_arr[] = $number_of_completed_chapter;
			
			//get not completed chapter 
			$number_of_not_completed_chapter = 0;
			$stmt = $con->prepare("SELECT count(tcc.id) as 'cnt' from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
			JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tcc.course_code = ? AND tcc.user_id = ? AND tcc.completion = 'nc'  AND tnd.tree_node_category_id=2  AND gmt.is_active = 1");
			$stmt->bind_param("isi",$course_edge_id,$course_code,$user_id);
			$stmt->execute();
			$stmt->bind_result($number_of_not_completed_chapter);
			$stmt->fetch();
			$stmt->close();	  
			$number_of_not_completed_chapter = !empty($number_of_not_completed_chapter)?$number_of_not_completed_chapter:0;
			$level_chapter_not_complt_arr[] = $number_of_not_completed_chapter;
			
			
			//total earned coins in course
			$total_earned_coins= 0;
			$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ?");
			$stmt->bind_param("is",$user_id,$course_code);
			$stmt->execute();
			$stmt->bind_result($total_earned_coins);
			$stmt->fetch();
			$stmt->close();	 
			$total_earned_coins = !empty($total_earned_coins)?$total_earned_coins:0;
			$level_earned_coins_arr[] = $total_earned_coins;
			
			//get all chapter of course
			$stmt = $con->prepare("SELECT cm.session_node_id,gmt.edge_id,cm.chapterSkill,trc.competency from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
			JOIN tbl_rubric_competency trc ON trc.id = cm.chapterSkill
			where gmt.tree_node_super_root = ? AND tnd.tree_node_category_id=2  AND gmt.is_active = 1 order by cm.session_node_id");
			$stmt->bind_param("i",$course_edge_id);
			$stmt->execute();
			$stmt->bind_result($chapter_id,$chapter_edge_id,$chapter_skill,$skill_name);
			$chapterArr=array();
			while($stmt->fetch()) {
				
				$obj = new stdclass();
				$obj->chapter_id = $chapter_id;
				$obj->chapter_edge_id = $chapter_edge_id;
				$obj->chapter_skill = $chapter_skill;
				$obj->skill_name = $skill_name;
				$chapterArr[]=$obj;
			}
			$stmt->close();	
			
			foreach($chapterArr as $key2=>$chapter_val){
			
				$sklArr = array('skill_id'=>$chapter_val->chapter_skill,'skill_name'=>$chapter_val->skill_name);
				
				
				$skill_arr[$chapter_val->chapter_skill] = $sklArr;
				
				$qCount = 0;
				$stmt = $con->prepare("SELECT count(tq.id) as qCount from generic_mpre_tree gmt
				JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
				JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
				JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
				JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
				where gmt.edge_id=? and tc.scenario_subtype='Quiz' and tc.status = 1");
				$stmt->bind_param("i",$chapter_val->chapter_edge_id);
				$stmt->execute();
				$stmt->bind_result($qCount);
				$stmt->fetch();
				$stmt->close();
				$qCount = !empty($qCount)?$qCount:0;
				$skill_ques_arr[$chapter_val->chapter_skill][] = $qCount;
				
				$ttlCorrect = 0;$ttlAttempt = 0;
				$stmt = $con->prepare("SELECT COALESCE(SUM(tap.ans_id), 0) as ttlCorrect ,COUNT(tap.id) as ttlAttempt from generic_mpre_tree gmt 
							JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
							JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
							JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
							JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
							JOIN temp_ans_push tap ON tap.ques_id = tq.id 
							where gmt.edge_id=? and tap.user_id = ?  and tc.scenario_subtype='Quiz' and tc.status = 1");
				$stmt->bind_param("ii",$chapter_val->chapter_edge_id,$user_id);
				$stmt->execute();
				$stmt->bind_result($ttlCorrect,$ttlAttempt);
				$stmt->fetch();
				$stmt->close();
				$ttlCorrect = !empty($ttlCorrect)?$ttlCorrect:0;
				$ttlAttempt = !empty($ttlAttempt)?$ttlAttempt:0;
				if($chapter_val->chapter_skill ==2 || $chapter_val->chapter_skill ==4){
					
					$skill_ques_crrct_arr[$chapter_val->chapter_skill][] = $ttlAttempt;
				}else{
					$skill_ques_crrct_arr[$chapter_val->chapter_skill][] = $ttlCorrect;
				}
				
			}
	
		}
	
	}

	

	
	
	$ttl_chapter = array_sum($level_chapter_arr);
	$ttl_completed_chapter = array_sum($level_chapter_complt_arr);
	$ttl_not_completed_chapter = array_sum($level_chapter_not_complt_arr);
	$ttl_time_sp = array_sum($level_time_arr);
	$ttl_earned_coins = array_sum($level_earned_coins_arr);
	
	//sort in specific order
	 $order = array(1,3,4,2);
	
 
	usort($skill_arr, function ($a, $b) use ($order) {
		$pos_a = array_search($a['skill_id'], $order);
		$pos_b = array_search($b['skill_id'], $order);
		return $pos_a - $pos_b;
	});  
 
	
	foreach($skill_arr as $value){
		$skill_id = $value['skill_id'];
		$skill_name = $value['skill_name'];
		
		$skill_time = 0;
		$stmt = $con->prepare("SELECT COALESCE(SUM(ust.actual_seconds),0) as ttlCompTimeSp from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
			JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
			JOIN user_session_tracking ust ON ust.session_id= tc.component_edge_id 
			where cm.chapterSkill=? AND gmt.tree_node_super_root = ? AND ust.user_id = ? AND course_code = ? AND ust.session_type = 'CM' AND ust.unique_code = ? AND LENGTH(ust.unique_code) >= 10  GROUP BY ust.unique_code, ust.course_code");
		$stmt->bind_param("iiiss",$skill_id,$course_edge_id,$user_id,$course_code,$package_code);
		$stmt->execute();
		$stmt->bind_result($skill_time);
		$stmt->fetch();
		$stmt->close();
		$skill_time = !empty($skill_time)?$skill_time:0; 
		
		$ttl_skill_ques = array_sum($skill_ques_arr[$skill_id]);
		$ttl_skill_crct = array_sum($skill_ques_crrct_arr[$skill_id]);
		$skill_per = round(($ttl_skill_crct/$ttl_skill_ques)*100);
		$skill_per = !empty($skill_per)?$skill_per:0;
		$level_skill_arr[] = array('skill_id'=>$skill_id,'skill_name'=>$skill_name,'skill_per'=>$skill_per,'skill_ttl_ques'=>$ttl_skill_ques,'skill_ttl_crct'=>$ttl_skill_crct,'skill_time'=>$skill_time);
	}
	
	$levelArr = array('level_text'=>$level_text,'ttl_chapter'=>$ttl_chapter,'ttl_completed_chapter'=>$ttl_completed_chapter,'ttl_not_completed_chapter'=>$ttl_not_completed_chapter,'ttl_time_sp'=>$ttl_time_sp,'ttl_earned_coins'=>$ttl_earned_coins,'skill_arr'=>$level_skill_arr,'skill_arra'=>$skill_arr);
	
	closeConnection($con);
	return $levelArr; 

}

function getUserPerfomance($con,$user_id,$course_code,$package_code){
	
	$courseID = getCourseIdByCourseCode($course_code);
	
	//$checkComponentarr = array();
	$courseDetails = getCourseDetailsCourseId($courseID);
	$number_of_chapters = 0;
	$topic_time_arr = $ttlCrsQCount = $ttlCrsCrrct = $assignment_score_arr = array();
	$total_time = 0;
	
	if($courseID){
		$courseName = publishText($courseDetails->title);
		$course_edge_id = getCourseEdgeIdByCourseId($courseID);//course edge id
		
		//get batch code
		$sql = "SELECT center_id,batch_id from tblx_batch_user_map WHERE user_id=$user_id";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($center_id,$batch_id);
		$stmt->close();

		$sql = "SELECT batch_code from tblx_batch where center_id=? and batch_id=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("ii",$center_id,$batch_id);
		$stmt->execute();
		$stmt->bind_result($batch_code);
		$stmt->close(); 
		
		
		
		//get topics
		$topicArr = getTopicByCourseEdgeId($con,$course_edge_id);
		
		$number_of_topics =0;
		
		//get completed topic
		$number_of_completed_topic = 0;
		$stmt = $con->prepare("SELECT count(tcc.id) as 'cnt'
							FROM generic_mpre_tree gmt
							JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
							JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
							JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
							WHERE gmt.is_active = 1 AND tree_node_super_root = ? AND tcc.course_code = ? AND tcc.user_id = ? AND tcc.completion = 'c' AND tnd.tree_node_category_id IN(3,5) AND cm.assessment_type IS NULL");
		$stmt->bind_param("isi",$course_edge_id,$course_code,$user_id);
		$stmt->execute();
		$stmt->bind_result($number_of_completed_topic);
		$stmt->fetch();
		$stmt->close();
		
		//get completed chapter 
		$number_of_completed_chapter = 0;
		$stmt = $con->prepare("SELECT count(tcc.id) as 'cnt' from generic_mpre_tree gmt 
		JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
		JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
		JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tcc.course_code = ? AND tcc.user_id = ? AND tcc.completion = 'c'  AND tnd.tree_node_category_id=2  AND gmt.is_active = 1");
		$stmt->bind_param("isi",$course_edge_id,$course_code,$user_id);
		$stmt->execute();
		$stmt->bind_result($number_of_completed_chapter);
		$stmt->fetch();
		$stmt->close();	 
		
		
		
		//total coins 
		$total_earned_coins= 0;
		$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ?");
		$stmt->bind_param("is",$user_id,$course_code);
		$stmt->execute();
		$stmt->bind_result($total_earned_coins);
		$stmt->fetch();
		$stmt->close();	 
		
		 $quiz_coins = $vocab_coins = $rp_coins = $speedreading_coins = $chapter_total_coin_arr= array();
		 $ttl_topic_coins = 0;
		
		foreach($topicArr as $key => $value){
			$topic_edge_id = $value->edge_id;
			$topic_name = $value->name;
			$quiz_topic_coins = $vocab_topic_coins = $rp_topic_coins = $speedreading_topic_coins = array();
			
			if($value->assessment_type==""){
				
				
				$chapter_Array = $chapter_time_arr = $chpaterCrrctArr = $chpaterQCountArr = array();
				$assign_count = $assignment_score = 0;
				
				$singleChapterArr = getChpaterByTopicEdgeId($con,$topic_edge_id);
				$chapter_count = count($singleChapterArr);
				
				foreach($singleChapterArr as $chapterArrKey=>$chapterArrVal){
					
					$chapterEdgeId = $chapterArrVal->edge_id;
					$ttlChpaterCrrct = $ttlChpaterQCount = 0;

					//get coins
					$scenarios = getScenarioByChapterId($chapterEdgeId);

						$totalScenarios=count($scenarios);
										
						if($totalScenarios > 0){
							
							
						/* $chapter_total_coin = getChapterCoinByChapterEdgeId($con,$chapterEdgeId);
						if($chapter_total_coin!=""){
							
							$chapter_total_coin_arr[] = $chapter_total_coin;
						
						}else{ */
							foreach($scenarios as $keys=>$values){
							
								
									if($values->scenario_subtype=="Quiz"){
									
										$compEdgeIdQ = $values->component_edge_id;
										
										$stmt = $con->prepare("SELECT count(tq.id) as qCount from  tbl_component tc JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
										where tc.component_id=?");
										$stmt->bind_param("i",$values->component_id);
										$stmt->execute();
										$stmt->bind_result($qCount);
										$stmt->fetch();
										$stmt->close();
										
										$quiz_coins[] = $qCount;
										$quiz_topic_coins[] = $qCount;
						
									}
									if($values->scenario_subtype=="Conversation Practice")
									{
									
										$compEdgeIdR = $values->component_edge_id;
										
										$stmt = $con->prepare("SELECT count(tv.id) as coin_count from tbl_vocabulary tv JOIN tbl_component tc on tv.parent_edge_id = tc.parent_edge_id where tc.component_edge_id = ?  and tc.status = 1");
										$stmt->bind_param("i",$compEdgeIdR);
										$stmt->execute();
										$stmt->bind_result($total_vocab_coin);
										$stmt->fetch();
										$stmt->close();	
										
										$vocab_coins[] = $total_vocab_coin;
										$vocab_topic_coins[] = $total_vocab_coin;

									
									}														
										
									if($values->scenario_subtype=="Role-play")
									{			
										
										$rolePlayEdgeId = $values->component_edge_id;
									
										$stmt = $con->prepare("SELECT count(id) as videoCount from  tbl_component_data where component_id =? and scenario_answer_media_file!=''");
										$stmt->bind_param("i",$values->component_id);
										$stmt->execute();
										$stmt->bind_result($videoCount);
										$stmt->fetch();
										$stmt->close();
										
										$rp_coins[] = $videoCount;
										$rp_topic_coins[] = $videoCount;
					
									}
									
									if($values->scenario_subtype=="SpeedReading"){
									
										$compEdgeIdQ = $values->component_edge_id;
										$stmt = $con->prepare("SELECT count(tq.id) as qCount from  tbl_component tc JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
										where tc.component_id=?");
										$stmt->bind_param("i",$values->component_id);
										$stmt->execute();
										$stmt->bind_result($srQCount);
										$stmt->fetch();
										$stmt->close();
										
										$speedreading_coins[] = $srQCount;
										$speedreading_topic_coins[] = $srQCount;
										
									
									}

							}
						
						//}
						
						
							//total ques of chapter
							$stmt = $con->prepare("SELECT count(tq.id) as qCount from generic_mpre_tree gmt 
							JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
							JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
							JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
							JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
							where tc.parent_edge_id = ? and gmt.is_active = 1 and tc.status =1");
							$stmt->bind_param("i",$chapterEdgeId);
							$stmt->execute();
							$stmt->bind_result($ttlChpaterQCount);
							$stmt->fetch();
							$stmt->close();				
							
							$chpaterQCountArr[] = $ttlChpaterQCount;
							
							//total correct of chapter
							$stmt = $con->prepare("SELECT COALESCE(SUM(tap.ans_id), 0) as ttlCorrect from generic_mpre_tree gmt 
							JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
							JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
							JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
							JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
							JOIN temp_ans_push tap ON tap.ques_id = tq.id 
							where tc.parent_edge_id = ? and tap.user_id = ?  and gmt.is_active = 1 and tc.status =1");
							$stmt->bind_param("ii",$chapterEdgeId,$user_id);
							$stmt->execute();
							$stmt->bind_result($ttlChpaterCrrct);
							$stmt->fetch();
							$stmt->close();					
							
							$chpaterCrrctArr[] = $ttlChpaterCrrct;
							
						
						}


					
					
					
					
					$chapter_time = $chapter_coin = $chapter_per = 0;
					
					//Chapter time												
					$stmt = $con->prepare("SELECT COALESCE(SUM(ust.actual_seconds), 0) from user_session_tracking ust where ust.session_type = 'CM'  AND ust.user_id=? and ust.session_id IN(SELECT distinct component_edge_id from tbl_component tc where tc.parent_edge_id = ?)");
					$stmt->bind_param("ii",$user_id,$chapterArrVal->edge_id);
					$stmt->execute();
					$stmt->bind_result($chapter_time);
					$stmt->fetch();
					$stmt->close();	
					
					
					$chapter_time_arr[] = $chapter_time;
					
					
					/* //Chapter percentage calculation of myprgress
					$stmt = $con->prepare("select complete_per from tblx_component_completion where user_id=? AND component_edge_id=? AND license_key=?");
					$stmt->bind_param("iis",$user_id,$chapterArrVal->edge_id,$package_code);	
					$stmt->execute();
					$stmt->bind_result($chapter_per);
					$stmt->fetch();
					$stmt->close(); */
					
					
					//chapter percentage my performance
					if($ttlChpaterCrrct>0){
					$chapter_per = ($ttlChpaterCrrct/$ttlChpaterQCount)*100;
					}else{$chapter_per =0;}
					$chapter_per = round($chapter_per);
					$chapter_per = !empty($chapter_per)?$chapter_per:0;
					
					
					//Chapter coin
					$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ? AND chapter_edge_id = ? ");
					$stmt->bind_param("isi",$user_id,$course_code,$chapterArrVal->edge_id);
					$stmt->execute();
					$stmt->bind_result($chapter_coin);
					$stmt->fetch();
					$stmt->close();	 
					
						
			
					$chapter_Array[] =  array("chapter_name"=>$chapterArrVal->name,"chapter_coin"=>$chapter_coin, "chapter_time"=>$chapter_time, "chapter_percentage"=>$chapter_per, "ttlChpaterCrrct"=>$ttlChpaterCrrct, "ttlChpaterQCount"=>$ttlChpaterQCount, "chapterEdgeId"=>$chapterEdgeId);
					
					
					$number_of_chapters++;
				
				
		 
				
				}
				
				
				
				//Topic percentage calculation for my progress
				/* $stmt = $con->prepare("select complete_per from tblx_component_completion where user_id=? AND component_edge_id=? AND license_key=?");
				$stmt->bind_param("iis",$user_id,$topic_edge_id,$package_code);	
				$stmt->execute();
				$stmt->bind_result($topic_per);
				$stmt->fetch();
				$stmt->close(); */
				
				//Topic coin
				$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ? AND topic_edge_id = ?");
				$stmt->bind_param("isi",$user_id,$course_code,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($topic_coin);
				$stmt->fetch();
				$stmt->close();	 
			
				$topic_time = array_sum($chapter_time_arr);
				$topic_time_arr[] = $topic_time;
				
				
				$ttlTopicQCount = array_sum($chpaterQCountArr);
				$ttlTopicCrrct = array_sum($chpaterCrrctArr);
				
				
				//asignment 
				$sql = "SELECT count(id) as assign_count from tblx_assignments where course_code=? and batch_code=? and topic_edge_id=?";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("ssi",$course_code,$batch_code,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($assign_count);
				$stmt->fetch();
				$stmt->close();	
				
				//asignment score
				$sql = "SELECT COALESCE(SUM(evaluated_rating), 0) from tblx_assignments ta JOIN tblx_assignment_evaluation tae ON ta.id=tae.assignment_id where ta.course_code=? and ta.batch_code=? and ta.topic_edge_id=? and tae.student_id=?";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("ssii",$course_code,$batch_code,$topic_edge_id,$user_id);
				$stmt->execute();
				$stmt->bind_result($assignment_score);
				$stmt->fetch();
				$stmt->close();	
				
				
				$assignment_score_arr[] = $assignment_score;
				$ttlAssigmentQCount = $assign_count*10;
				
				$ttlTopicQCount = $ttlTopicQCount+$ttlAssigmentQCount;
				
				$ttlTopicCrrct= $ttlTopicCrrct+$assignment_score;
				
				if($ttlTopicCrrct>0){
					$topic_per = ($ttlTopicCrrct/$ttlTopicQCount)*100;
				}else{
					$topic_per = 0;
				}
				$topic_per = round($topic_per);
				$topic_per = !empty($topic_per)?$topic_per:0;
				
				
				$ttlCrsQCount[] = $ttlTopicQCount;
				$ttlCrsCrrct[] = $ttlTopicCrrct;
				
				//Total topic coins
				$quiz_topic_coins = array_sum($quiz_topic_coins); 
				$vocab_topic_coins = array_sum($vocab_topic_coins); 
				$rp_topic_coins = array_sum($rp_topic_coins); 
				$speedreading_topic_coins = array_sum($speedreading_topic_coins); 
				
				$ttl_topic_coins = $quiz_topic_coins + $vocab_topic_coins + $rp_topic_coins + $speedreading_topic_coins;
				
				
				
				
				$topic_Array[] = array("chapter_Array"=>$chapter_Array,"chapter_complete_count"=>0, "chapter_count"=>$chapter_count, "topic_time"=> $topic_time, "topic_name"=>$topic_name,"total_coins"=>$topic_coin,"topic_percentage"=>$topic_per,"ttlTopicQCount"=>$ttlTopicQCount,"ttlTopicCrrct"=>$ttlTopicCrrct,"componentType" => "module","ttl_topic_coins" => $ttl_topic_coins,"topic_edge_id" => $topic_edge_id);
			
				$number_of_topics++;
			}
			else{
				$assessment_time = $assessment_coin = $ttlAssessmentQCount = $ttlAssessmentCrrct = 0;
				//assessment time									
				$stmt = $con->prepare("SELECT COALESCE(SUM(actual_seconds), 0) from user_session_tracking where user_id = ? AND course_code = ?  AND session_id = ? AND unique_code = ? AND session_type = 'AS'");
				$stmt->bind_param("isis",$user_id,$course_code,$topic_edge_id,$package_code);
				$stmt->execute();
				$stmt->bind_result($assessment_time);
				$stmt->fetch();
				$stmt->close();	
				
				//assessment coin
				$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ? AND topic_edge_id = ?");
				$stmt->bind_param("isi",$user_id,$course_code,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($assessment_coin);
				$stmt->fetch();
				$stmt->close();	 
				
				//assessment total question	
				$stmt = $con->prepare("SELECT count(id) as qCount from   tbl_questions where parent_edge_id=?");
				$stmt->bind_param("i",$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($ttlAssessmentQCount);
				$stmt->fetch();
				$stmt->close();
				
				
				//total correct of assessment
				$stmt = $con->prepare("SELECT COALESCE(SUM(tap.ans_id), 0) as ttlCorrect from tbl_questions tq 
				JOIN temp_ans_push tap ON tap.ques_id = tq.id 
				where tq.parent_edge_id = ? and tap.user_id = ?");
				$stmt->bind_param("ii",$topic_edge_id,$user_id);
				$stmt->execute();
				$stmt->bind_result($ttlAssessmentCrrct);
				$stmt->fetch();
				$stmt->close();	
	
				if($ttlAssessmentCrrct>0){
				$assessment_per = ($ttlAssessmentCrrct/$ttlAssessmentQCount)*100;
				}else{
					$assessment_per =0;
				}
				$assessment_per = round($assessment_per);
				$assessment_per = !empty($assessment_per)?$assessment_per:0;
				
				$topic_Array[] = array("topic_time"=> $assessment_time, "topic_name"=>$topic_name,"total_coins"=>$assessment_coin,"topic_percentage"=>$assessment_per,"ttlTopicQCount"=>$ttlAssessmentQCount,"ttlTopicCrrct"=>$ttlAssessmentCrrct,"componentType" => "assessment","topic_edge_id" => "topic_edge_id");
				
			}
		}
		
		//Course total time
		$total_time = array_sum($topic_time_arr);
		
		//Course percentage my performance
		$ttlCrsQCount = array_sum($ttlCrsQCount);
		$ttlCrsCrrct = array_sum($ttlCrsCrrct);
		if($ttlCrsCrrct>0){
		$coursePer = (($ttlCrsCrrct*100)/$ttlCrsQCount);
		}else{
			$coursePer =0;
		}
		$coursePer = round($coursePer);
		$coursePer = !empty($coursePer)?$coursePer:0;
		
		$coursePer = ($coursePer)?$coursePer:0;
		
		
		//Get total coins
		array_filter($quiz_coins); 
		array_filter($vocab_coins); 
		array_filter($rp_coins); 
		array_filter($speedreading_coins); 
		
		
		$quiz_coins = array_sum($quiz_coins); 
		$vocab_coins = array_sum($vocab_coins); 
		$rp_coins = array_sum($rp_coins); 
		$speedreading_coins = array_sum($speedreading_coins); 
		$chapter_total_coins = array_sum($chapter_total_coin_arr); 
		
		
		//Assignment coins
		 $sql = "SELECT count(id) as assign_count from tblx_assignments where course_code=? and batch_code=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("ss",$course_code,$batch_code);
		$stmt->execute();
		$stmt->bind_result($assign_count);
		$stmt->fetch();
		$stmt->close();	

		$assignment_coins = $assign_count*10;
		
		
		$total_coins = $quiz_coins + $vocab_coins + $rp_coins + $speedreading_coins + $assignment_coins + $chapter_total_coins;
		
				
		$earned_per = ($total_earned_coins*100)/$total_coins;
		
		$earned_per = !empty($earned_per)?$earned_per:0;
		
		$badgeNo = 1;
		
		if($earned_per<=30){
			$badgeNo = 1;
		}else if($earned_per>30 && $earned_per<=50){
			$badgeNo = 2;
		}else if($earned_per>50 && $earned_per<=80){
			$badgeNo = 3;
		}else if($earned_per>80){
			$badgeNo = 4;
		}
		 
		//$retObj = new stdClass();	
		$retObj->badgeNo = $badgeNo;	
		$retObj->course_code = $course_code;	
		$retObj->course_id = $courseID;	
		$retObj->topic_complete_count = $number_of_completed_topic;	
		$retObj->topic_count = $number_of_topics;	
		$retObj->total_coins = $total_earned_coins;	
		$retObj->total_coins_avail = $total_coins;	
		$retObj->total_time = $total_time;	
		$retObj->chapter_complete_count = $number_of_completed_chapter;	
		$retObj->chapter_count = $number_of_chapters;	
		$retObj->course_percentage = $coursePer;	
		$retObj->topic_Array = $topic_Array;		
		$retObj->user_id = $user_id;	
		closeConnection($con);
		return $retObj;
	}
	
}

function getTopicByCourseEdgeId($con,$course_edge_id){
	$topicArr = array();
		$stmt = $con->prepare("SELECT cm.tree_node_id, cm.name,
            gmt.edge_id,tnd.tree_node_category_id, cm.assessment_type
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = ? AND tnd.tree_node_category_id IN(3,5) ORDER BY sequence_id");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$edge_id,$tree_node_category_id,$assessment_type);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->tree_node_id = $tree_node_id;
			$topic2->name = $name;
			$topic2->edge_id = $edge_id;
			$topic2->tree_node_category_id = $tree_node_category_id;
			$topic2->assessment_type = $assessment_type;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
	return $topicArr;

}

function getChpaterByTopicEdgeId($con,$topic_edge_id){
	$chapterArray = array();
	$stmt = $con->prepare("SELECT gmt.edge_id, cm.tree_node_id, cm.code, cm.chapterSkill FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
									WHERE gmt.is_active = 1 AND tree_node_parent = ? AND tnd.tree_node_category_id=2 AND cm.is_hide_resource!='1'"); 
			$stmt->bind_param("i",$topic_edge_id); 
			$stmt->execute();
			$stmt->bind_result($edge_id,$tree_node_id,$code,$chapterSkill);
			while($stmt->fetch()) {
				$topic = new stdClass(); 
				$topic->edge_id = $edge_id;
				$topic->tree_node_id = $tree_node_id;
				$topic->name = $code;
				$topic->chapterSkill = $chapterSkill;
				//$topic->competency = $competency;
				array_push($chapterArray,$topic);
			}
			$stmt->close();
			//closeConnection($con);
	return $chapterArray;
} 

function setUserDiscount($con,$user_id,$param) {
    
	$query = "insert into tblx_user_discount(user_id,discount_id,date_created) values(?,?,NOW())";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii",$user_id,$param->discount_id);
    $stmt->execute();
    $stmt->close();
    $retObj->status="SUCCESS";
	closeConnection($con);
	return $retObj;

}
function setDisclaimerChecked($con,$user_id,$param) {
    
	$check_type=$param->check_type;
	if($check_type=='disclaimer')
	{
	$query = "update user set disclaimer_checked='1' where user_id=?";
	}
	else
	{
	$query = "update user set instruction_checked='1' where user_id=?";
	}
    $stmt = $con->prepare($query);
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $stmt->close();
    $retObj->status="SUCCESS";
	closeConnection($con);
	return $retObj;

}

function getDiscountCoupons($con,$user_id,$param,$client_id) {
    

	$userProfileCompletion=fnGetUserProfileCompletion($con,$user_id);
	
	$discounts_list=array();
	$stmt = $con->prepare("select id,client_id,discount_title,discount_description, discount_month,discount_month_code,discount_coupon,discount_event,discount_status,external_link,fail_message,success_message from tblx_discounts where discount_status='1' and client_id=? order by id asc");
	$stmt->bind_param("i",$client_id);
	$stmt->execute();
	$stmt->bind_result($id,$client_id,$discount_title,$discount_description,$discount_month,$discount_month_code,$discount_coupon,$discount_event,$discount_status,$external_link,$fail_message,$success_message);
	while($stmt->fetch()) {
	$obj = new stdclass();
	$obj->discount_id = $id;
	$obj->client_id = $client_id;
	$obj->discount_title = $discount_title;
	$obj->discount_description = $discount_description;
	$obj->discount_month = $discount_month;
	$obj->discount_month_code = $discount_month_code;
	$obj->discount_coupon = $discount_coupon;
	$obj->discount_event = $discount_event;
	$obj->discount_status = $discount_status;
	
		if($id==1 && $userProfileCompletion==100)
		{
			$obj->external_link = $external_link;
			$obj->message_text=$discount_coupon."&lt;br&gt;&lt;br&gt;Use this code to receive a 50% discount on the Pearson English Readiness Test.&lt;br&gt;This quick, online test boosts your confidence and tells you if you're ready to take an English exam. And if you're not, it provides lots of recommendations to reach your goals!&lt;br&gt;' target='_blank'>click here&lt;/a&gt;.";
		}
		else if($id==1 && $userProfileCompletion!=100)
		{			
			$obj->external_link = '';
			$obj->message_text="It looks like you haven't collected any discounts yet. To get started, you can complete your personal profile and get access to an exclusive 50% discount on the Pearson English Readiness Test.";
		}
		if($id==2)
		{
			$obj->external_link = '';
			$obj->message_text=$fail_message;
			
		}

	array_push($discounts_list,$obj);

	}
	$stmt->close();	
	closeConnection($con);

	return $discounts_list;
}

function fnGetUserProfileCompletion($con,$user_id){

	
	/*$stmt = $con->prepare("SELECT a.user_id,a.first_name,a.middle_name,a.last_name,a.email_id,a.is_email_verified,a.address_id,a.age_range,a.gender,a.education,a.employment_status,a.joining_purpose,a.marital_status,a.mother_tongue,a.exculsive_offers,a.instructions_tips,a.news_discount,a.years_eng_edu,a.is_scheduled_for_certificate,a.discount_readiness, b.loginid,c.phone,c.is_phone_verified,c.city,c.state,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=$user_id and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt->execute();
	$stmt->bind_result($user_id,$first_name, $middle_name, $last_name, $email_id, $is_email_verified,$address_id, $age_range, $gender, $education, $employment_status, $joining_purpose,$marital_status,$mother_tongue,$exculsive_offers,$instructions_tips, $news_discount,$years_eng_edu,$is_scheduled_for_certificate,$discount_readiness,$loginid,$phone,$is_phone_verified,$city,$state,$country_code,$country);
	$stmt->fetch();
	$stmt->close();

	if(empty($is_scheduled_for_certificate))
	{
	$is_scheduled_for_certificate='0';
	}

	$profile_completion=getProfileCompletion($first_name,$email_id,$gender,$age_range,$country,$joining_purpose,$is_scheduled_for_certificate);
	closeConnection($con);
	return $profile_completion;*/
	$stmt = $con->prepare("SELECT a.user_id,a.first_name,a.middle_name,a.last_name,a.email_id,a.is_email_verified,a.address_id,a.age_range,a.gender,a.education,a.employment_status,a.joining_purpose,a.marital_status,a.mother_tongue,a.exculsive_offers,a.instructions_tips,a.news_discount,a.years_eng_edu,a.is_scheduled_for_certificate,a.discount_readiness, b.loginid,c.phone,c.is_phone_verified,c.city,c.state,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=$user_id and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt->execute();
	$stmt->bind_result($user_id,$first_name, $middle_name, $last_name, $email_id, $is_email_verified,$address_id, $age_range, $gender, $education, $employment_status, $joining_purpose,$marital_status,$mother_tongue,$exculsive_offers,$instructions_tips, $news_discount,$years_eng_edu,$is_scheduled_for_certificate,$discount_readiness,$loginid,$phone,$is_phone_verified,$city,$state,$country_code,$country);
	$stmt->fetch();
	$stmt->close();

	
	$obj = new stdclass();
	
	$obj->first_name = $first_name;
	$obj->email_id = $email_id;
	$obj->gender = $gender;
	$obj->age_range = $age_range;
	$obj->joining_purpose = $joining_purpose;
	$obj->country = $country;
	if(empty($is_scheduled_for_certificate))
	{
	$is_scheduled_for_certificate='0';
	}
	$obj->is_scheduled_for_certificate = $is_scheduled_for_certificate;

	$profile_completion=getProfileCompletion($first_name,$email_id,$gender,$age_range,$country,$joining_purpose,$is_scheduled_for_certificate);

	$obj->profile_completion = $profile_completion;
	
    closeConnection($con);
	return $obj;

}


function getProfileCompletion($first_name,$email_id,$gender,$age_range,$country,$joining_purpose,$is_scheduled_for_certificate)
{
	//file_put_contents("test/profileCompletion.txt",$gender."---".$age_range);
	$percent=0;
	if(!empty($first_name) )
	{
		$percent+=28;
	}
	if(!empty($email_id))
	{
		$percent+=30;
	}
	if(!empty($gender) && $gender!='')
	{
		$percent+=14;
	}
	if(!empty($age_range) && $age_range!='')
	{
		$percent+=14;
	}
	if(!empty($country))
	{
		$percent+=14;
	}
	/*if(!empty($joining_purpose))
	{
		$percent+=14;
	}
	if($is_scheduled_for_certificate!='0')
	{
		$percent+=14;
	}*/
//file_put_contents("test/profilePer.txt",$percent);
	
	return $percent;
}

function getLeaderboard($user_id,$param,$client_id)
{
	//file_put_contents('test/user_id.txt',$user_id);
	global $authoring_path;$rank=0;
	$userCurrent_list=array();
	$user_list=array();
	$pic_url=$authoring_path."/view/profile_pic/";
	$flag_url="https://flagcdn.com/60x45/";
	$parentObj = new stdClass();

	
	$add_sql='';
	$add_sql.=" where clientId=$client_id ";
	if($param->list_type=='country')
	{
		$add_sql.=" and countryCode='$param->country_code' ";
	}

	$offset=($param->page-1)*$param->record_limit;

	///////////////////////////////////For Current User/////

	$con_g2 = createConnection();
	$stmt1 = $con_g2->prepare("SELECT a.first_name,a.middle_name,a.last_name,a.email_id,a.address_id, b.loginid,c.phone,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=? and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt1->bind_param("i",$user_id);
	$stmt1->execute();
	$stmt1->bind_result($first_name, $middle_name, $last_name, $email_id, $address_id,$loginid,$phone,$country_code,$country);
	$stmt1->fetch();
	$stmt1->close();
			
	if(!empty($country))
	{
		$stmt2 = $con_g2->prepare("SELECT country_code from tbl_countries where country_name=?");
		$stmt2->bind_param("s",$country);
		$stmt2->execute();
		$stmt2->bind_result($country_code);
		$stmt2->fetch();
		$stmt2->close();
		if(!empty($country_code))
		{
		$flag_image=$flag_url.strtolower($country_code).".png";
		}
		else
		{
		$flag_image="";
		}
	}
	else
	{
		$flag_image="";
	}

	$stmt3 = $con_g2->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ?");
	$stmt3->bind_param("i",$user_id);
	$stmt3->execute();
	$stmt3->bind_result($system_name);
	$stmt3->fetch();
	$stmt3->close();	
	closeConnection($con_g2);


	$con_g = createConnectionGamification();
	$stmt4 = $con_g->prepare("select userId, totalPoint,clientId,countryCode from user_total_point where clientId=? and userId=?");
	$stmt4->bind_param("ii",$client_id,$user_id);
	$stmt4->execute();
	$stmt4->bind_result($userId,$totalPoint,$clientId,$countryCode);
	$stmt4->fetch();
	$stmt4->close();
	$objUser = new stdclass();
	if(empty($userId))
	{
		$objUser->userId = $user_id;
		$objUser->totalPoint = '0';
		$objUser->clientId = $client_id;
		$objUser->countryCode = '';
	}
	else
	{
		$objUser->userId = $userId;
		$objUser->totalPoint = $totalPoint;
		$objUser->clientId = $clientId;
		$objUser->countryCode = $countryCode;
	}
		
	$objUser->first_name = $first_name;
	$objUser->middle_name = $middle_name;
	$objUser->last_name = $last_name;
	$objUser->email_id = $email_id;
	$objUser->phone = $phone;
	$objUser->country_code = $country_code;
	$objUser->country = $country;
	$objUser->country_flag = $flag_image;

	if(!empty($system_name))
	{
		$objUser->profile_pic = $pic_url.$system_name;
	}
	else
	{
		$objUser->profile_pic = "";
	}
	///get rank of current user
	$currentUserRank=0;
	$con_g14 = createConnectionGamification();
	$stmt14 = $con_g14->prepare("select userId, totalPoint from user_total_point $add_sql order by totalPoint desc");
	$stmt14->execute();
    $stmt14->bind_result($userId14,$totalPoint14);
    while($stmt14->fetch()) {
			
			$currentUserRank++;
			if($userId14==$user_id)
			{
			$objUser->currentUserRank = $currentUserRank;
			break;
			}	
			else
			{
			$objUser->currentUserRank = 0;
			}
	}
	$stmt14->close();
	closeConnectionGamification($con_g14);

	//////////////////////////
	array_push($userCurrent_list,$objUser);
	closeConnectionGamification($con_g);
			
	///////////////////////////////////For Current User/////

	///////////////////////////////////All Users////////////
	$con_g = createConnectionGamification();
	$stmt5 = $con_g->prepare("select userId, totalPoint, clientId, countryCode from user_total_point $add_sql order by totalPoint desc limit $param->record_limit offset $offset");
	$stmt5->execute();
    $stmt5->bind_result($userId1,$totalPoint1,$clientId1,$countryCode1);
		
    while($stmt5->fetch()) {
			$rank++;
			$obj = new stdclass();
			$obj->userId = $userId1;
			$obj->totalPoint = $totalPoint1;
			$obj->clientId = $clientId1;
			$obj->countryCode = $countryCode1;
			$con_g2 = createConnection();

			$stmt6 = $con_g2->prepare("SELECT a.first_name,a.middle_name,a.last_name,a.email_id,a.address_id, b.loginid,c.phone,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=? and a.user_id=b.user_id and c.address_id=a.address_id");
			$stmt6->bind_param("i",$userId1);
			$stmt6->execute();
			$stmt6->bind_result($first_name1, $middle_name1, $last_name1, $email_id1, $address_id1,$loginid1,$phone1,$country_code1,$country1);
			$stmt6->fetch();
			$stmt6->close();
			if(!empty($country1))
		    {
			$stmt7 = $con_g2->prepare("SELECT country_code from tbl_countries where country_name=?");
			$stmt7->bind_param("s",$country1);
			$stmt7->execute();
			$stmt7->bind_result($country_code1);
			$stmt7->fetch();
			$stmt7->close();
				if(!empty($country_code1))
				{
						$flag_image1=$flag_url.strtolower($country_code1).".png";
				}
					else
				{
					$flag_image1="";
				}
			}
			else
			{
			$flag_image1="";
			}
			
			$system_name1="";
			$stmt8 = $con_g2->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ?");
			$stmt8->bind_param("i",$userId1);
			$stmt8->execute();
			$stmt8->bind_result($system_name1);
			$stmt8->fetch();
			$stmt8->close();	
			closeConnection($con_g2);
			//////
			$obj->first_name = $first_name1;
			$obj->middle_name = $middle_name1;
			$obj->last_name = $last_name1;
			$obj->email_id = $email_id1;
			$obj->phone = $phone1;
			$obj->country_code = $country_code1;
			$obj->country = $country1;
			$obj->country_flag = $flag_image1;
			if(empty($system_name1) || $system_name1=="")
			{
				$obj->profile_pic = "";
			}
			else
			{
				$obj->profile_pic = $pic_url.$system_name1;
			}
			
			$obj->rank = $rank;

			array_push($user_list,$obj);
    }
	
	
	
	
	
	$parentObj->currentUserDetail=$userCurrent_list;
	$parentObj->LeaderboardList=$user_list;
	
	closeConnectionGamification($con_g);
	///////////////////////////////////All Users/////

	$stmt5->close();
	
	return $parentObj;
}

function getLevelLeaderboard($user_id,$param,$client_id)
{
	global $authoring_path;
	$rank=0;
	$userCurrent_list=array();
	$user_list=array();
	$pic_url=$authoring_path."/view/profile_pic/";
	$flag_url="https://flagcdn.com/60x45/";
	$parentObj = new stdClass();


	$statndard_id=5;//will discuss and update

	$offset=($param->page-1)*$param->record_limit;

	
	$con_g2 = createConnection();
	$stmt1 = $con_g2->prepare("SELECT id from tblx_standards_levels where level_text=? and standard_id=?");
	$stmt1->bind_param("si",$param->levelId,$statndard_id);
	$stmt1->execute();
	$stmt1->bind_result($level_id);
	$stmt1->fetch();
	$stmt1->close();
	closeConnection($con_g2);

	if(empty($level_id))
	{
	return $parentObj;
	}

	$add_sql='';
	$add_sql.=" where clientId=$client_id and levelId=$level_id ";

	///////////////////////////////////For Current User/////

	$con_g2 = createConnection();
	$stmt1 = $con_g2->prepare("SELECT a.first_name,a.middle_name,a.last_name,a.email_id,a.address_id, b.loginid,c.phone,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=? and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt1->bind_param("i",$user_id);
	$stmt1->execute();
	$stmt1->bind_result($first_name, $middle_name, $last_name, $email_id, $address_id,$loginid,$phone,$country_code,$country);
	$stmt1->fetch();
	$stmt1->close();

	if(!empty($country))
	{
		$stmt2 = $con_g2->prepare("SELECT country_code from tbl_countries where country_name=?");
		$stmt2->bind_param("s",$country);
		$stmt2->execute();
		$stmt2->bind_result($country_code);
		$stmt2->fetch();
		$stmt2->close();
		$flag_image=$flag_url.strtolower($country_code).".png";
	}
	else
	{
		$flag_image="";
	}

	$stmt3 = $con_g2->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ?");
	$stmt3->bind_param("i",$user_id);
	$stmt3->execute();
	$stmt3->bind_result($system_name);
	$stmt3->fetch();
	$stmt3->close();	
	closeConnection($con_g2);


	$con_g = createConnectionGamification();
	$stmt4 = $con_g->prepare("select userId, totalPoint,clientId,levelId from user_level_point $add_sql and userId=?");
	$stmt4->bind_param("ii",$client_id,$user_id);
	$stmt4->execute();
	$stmt4->bind_result($userId,$totalPoint,$clientId,$levelId);
	$stmt4->fetch();
	$stmt4->close();
	$objUser = new stdclass();
	if(empty($userId))
	{
		$objUser->userId = $user_id;
		$objUser->totalPoint = '0';
		$objUser->clientId = $client_id;
		$objUser->clientId = '0';
	}
	else
	{
		$objUser->userId = $userId;
		$objUser->totalPoint = $totalPoint;
		$objUser->clientId = $clientId;
		$objUser->levelId = $levelId;
	}
		
	$objUser->first_name = $first_name;
	$objUser->middle_name = $middle_name;
	$objUser->last_name = $last_name;
	$objUser->email_id = $email_id;
	$objUser->phone = $phone;
	$objUser->country_code = $country_code;
	$objUser->country = $country;
	$objUser->country_flag = $flag_image;

	if(!empty($system_name))
	{
		$objUser->profile_pic = $pic_url.$system_name;
	}
	else
	{
		$objUser->profile_pic = "";
	}

	///get rank of current user
	$currentUserRank=0;
	$con_g14 = createConnectionGamification();
	$stmt14 = $con_g14->prepare("select userId, totalPoint from user_level_point $add_sql order by totalPoint desc");
	$stmt14->execute();
    $stmt14->bind_result($userId14,$totalPoint14);
    while($stmt14->fetch()) {
			
			$currentUserRank++;
			if($userId14==$user_id)
			{
			$objUser->currentUserRank = $currentUserRank;
			break;
			}	
			else
			{
			$objUser->currentUserRank = 0;
			}
	}
	$stmt14->close();
	closeConnectionGamification($con_g14);

	//////////////////////////
			
	array_push($userCurrent_list,$objUser);
	closeConnectionGamification($con_g);
			
	//////////////////////////////////For Current User/////

	///////////////////////////////////All Users/////
	$con_g = createConnectionGamification();
	$stmt5 = $con_g->prepare("select userId, totalPoint, clientId, levelId from user_level_point $add_sql order by totalPoint desc limit $param->record_limit offset $offset");
	$stmt5->execute();
	$stmt5->bind_result($userId1,$totalPoint1,$clientId1,$levelId1);
		
	while($stmt5->fetch()) {
		$rank++;
		$obj = new stdclass();
		$obj->userId = $userId1;
		$obj->totalPoint = $totalPoint1;
		$obj->clientId = $clientId1;
		$obj->levelId = $levelId1;
		$con_g2 = createConnection();
		$stmt6 = $con_g2->prepare("SELECT a.first_name,a.middle_name,a.last_name,a.email_id,a.address_id, b.loginid,c.phone,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=? and a.user_id=b.user_id and c.address_id=a.address_id");
		$stmt6->bind_param("i",$userId1);
		$stmt6->execute();
		$stmt6->bind_result($first_name1, $middle_name1, $last_name1, $email_id1, $address_id1,$loginid1,$phone1,$country_code1,$country1);
		$stmt6->fetch();
		$stmt6->close();

		if(!empty($country))
		{
			$stmt7 = $con_g2->prepare("SELECT country_code from tbl_countries where country_name=?");
			$stmt7->bind_param("s",$country1);
			$stmt7->execute();
			$stmt7->bind_result($country_code1);
			$stmt7->fetch();
			$stmt7->close();
			if(!empty($country_code1))
			{
				$flag_image1=$flag_url.strtolower($country_code1).".png";
			}
			else
			{
				$flag_image1="";
			}
		}
		else
		{
			$flag_image1="";
		}
		$system_name1="";
		$stmt8 = $con_g2->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ?");
		$stmt8->bind_param("i",$userId1);
		$stmt8->execute();
		$stmt8->bind_result($system_name1);
		$stmt8->fetch();
		$stmt8->close();	
		closeConnection($con_g2);

		$obj->first_name = $first_name1;
		$obj->middle_name = $middle_name1;
		$obj->last_name = $last_name1;
		$obj->email_id = $email_id1;
		$obj->phone = $phone1;
		$obj->country_code = $country_code1;
		$obj->country = $country1;
		$obj->country_flag = $flag_image1;
		if(empty($system_name1) || $system_name1=="")
		{
			$obj->profile_pic = "";
		}
		else
		{
			$obj->profile_pic = $pic_url.$system_name1;
		}
				
		$obj->rank = $rank;
		array_push($user_list,$obj);
				
	}
		///////////////////////////////////All Users/////
	$parentObj->currentUserDetail=$userCurrent_list;
	$parentObj->LeaderboardList=$user_list;
	$stmt5->close();
	closeConnectionGamification($con_g);
	return $parentObj;
	
}

function getAllBadges($con1,$user_id,$param,$client_id) {
    
	$badge_url="http://gamification.adurox.com:3000/uploads/badgeIcon/";

	$badge_list = array();

	if(!empty($client_id))
	{
	$con=createConnectionGamification();
	$stmt = $con->prepare("select badgeId,eventId,badgeName,badgeDetails,badgeIcon,clientId from badge_master where status='active' and clientId=? order by badgeId asc");
    $stmt->bind_param("i",$client_id);
	$stmt->execute();
    $stmt->bind_result($badgeId,$eventId,$badgeName,$badgeDetails,$badgeIcon,$clientId);
    while($stmt->fetch()) {
        $obj = new stdclass();
			$obj->badgeId = $badgeId;
			$obj->eventId = $eventId;
			$obj->badgeName = $badgeName;
			$obj->badgeDetails = $badgeDetails;
			$obj->badgeIcon = $badge_url.$badgeIcon;
			$obj->clientId = $clientId;

			

			$badgeIdFound='';
			$con2=createConnectionGamification();
			$stmt2 = $con2->prepare("SELECT badgeId FROM user_badges where userId=? and badgeId=? and clientId=?");
			$stmt2->bind_param("iii",$user_id,$badgeId,$client_id);
			$stmt2->execute();
			$stmt2->bind_result($badgeIdFound);
			$stmt2->fetch();
			closeConnectionGamification($con2);

			if(empty($badgeIdFound))
			{
				$obj->badge_received = 'no';
				$obj->is_new_badge = 'no';
			}
			else
		    {
				$obj->badge_received = 'yes';


				$isBadgeNew='';
				$con5=createConnection();
				$stmt5 = $con5->prepare("SELECT id FROM tblx_user_badge where user_id=? and badge_id=?");
				$stmt5->bind_param("ii",$user_id,$badgeId);
				$stmt5->execute();
				$stmt5->bind_result($isBadgeNew);
				$stmt5->fetch();
				closeConnection($con5);

				if(empty($isBadgeNew))
				{
					$obj->is_new_badge = 'yes';
				}
				else
				{
					$obj->is_new_badge = 'no';
				}

				$con6=createConnection();
				$stmt6 = $con6->prepare("SELECT id FROM tblx_user_badge where user_id=? and badge_id=?");
				$stmt6->bind_param("ii",$user_id,$badgeId);
				$stmt6->execute();
				$stmt6->bind_result($ifUserBadgeExists);
				$stmt6->fetch();
				closeConnection($con6);

				if(empty($ifUserBadgeExists))
				{
					$con4=createConnection();
					$stmt4 = $con4->prepare("insert into tblx_user_badge(user_id,badge_id,date_received) values(?,?,NOW())");
					$stmt4->bind_param("ii",$user_id,$badgeId);
					$stmt4->execute();
					$stmt4->close();
					closeConnection($con4);
				}
			}
			

			$totalBadgeUsers=0;
			$con3=createConnectionGamification();
			$stmt3 = $con3->prepare("SELECT count(userId) as totalBadgeUsers FROM user_badges where badgeId=? and clientId=?");
			$stmt3->bind_param("ii",$badgeId,$client_id);
			$stmt3->execute();
			$stmt3->bind_result($totalBadgeUsers);
			$stmt3->fetch();
			closeConnectionGamification($con3);

			$obj->userCounter = $totalBadgeUsers;
			 
        array_push($badge_list,$obj);
    }
    
	$stmt->close();
	closeConnectionGamification($con);
	
	}
    return $badge_list;
}

function getAllAvatars($con1,$param,$client_id) {
    
	$avatar_url="http://gamification.adurox.com:3000/uploads/avatarIcon/";
	
	
	$avatar_list = array();
	if(!empty($client_id))
	{	
	$con=createConnectionGamification();
	$stmt = $con->prepare("select avatarId,avatarName,avatarImgPath,clientId from avatar_master where status='active' and clientId=? order by avatarId asc");
    $stmt->bind_param("i",$client_id);
	$stmt->execute();
    $stmt->bind_result($avatarId,$avatarName,$avatarImgPath,$clientId);
    while($stmt->fetch()) {
        $obj = new stdclass();
			$obj->avatarId = $avatarId;
			$obj->avatarName = $avatarName;
			$obj->avatarImgPath = $avatar_url.$avatarImgPath;
			$obj->clientId = $clientId;
        array_push($avatar_list,$obj);
    }
    
	$stmt->close();
	closeConnectionGamification($con);
	
	}
    return $avatar_list;
}
function generateOTP($con,$user_phone,$user_email,$user_action,$user_name)
{
	
	$alert_msg_arr = alertMessage();
	$arr = array('status' => 0, 'msg' => '', 'data' => array());
	$data = array();
	$otp=mt_rand(100000,999999);
	$retmsg = "$otp is your one time password (OTP)";
	
	if(!empty($user_phone))
    {
		$requested_via=$user_phone;
	}
	else if (!empty($user_email))
    {
		$requested_via=$user_email;
	}
	else if (!empty($user_name))
    {
		$requested_via=$user_name;
	}

	 $stmt = $con->prepare("select id,requested_by,created_on,NOW() from tblx_otp where requested_by='$requested_via' and is_used='0' order by created_on desc limit 1");
     $stmt->bind_result($oid,$requested_by,$created_on,$current_time);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();
	
	if(!empty($oid))
	{
		//file_put_contents("test/results.txt",$oid."-".$requested_by."-".$created_on."-".$current_time);
		$timeFirst  = strtotime($current_time);
		$timeSecond = strtotime($created_on);
		$differenceInSeconds = $timeFirst - $timeSecond;
		//file_put_contents("test/diff.txt",$differenceInSeconds);
		if($differenceInSeconds < 120)
		{
				$sr = new ServiceResponse("FAILURE",0,null);
				$sr->retVal = new stdClass();
				$sr->retVal->msg = $alert_msg_arr['OTP_FAILED_MULTIPLE'];
				return $sr;	
		}
	}

	if(!empty($user_phone))
	{
	//file_put_contents('test/otp.txt',$otp);
		if($user_action=='registration')
		{
		 $stmt = $con->prepare("select user_id from user_credential where loginid='$user_phone'");
		 $stmt->bind_result($user_id_exists);
		 $stmt->execute();
		 $stmt->fetch();
		 $stmt->close();
		 
			if(!empty($user_id_exists)) {
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['OTP_FAILED_USER_EXISTS'];
			return $sr;	
			}
		}

	   if($user_action=='forgot_password' || $user_action=='profile_update')
		{
		 $stmt = $con->prepare("select user_id from user_credential where loginid='$user_phone'");
		 $stmt->bind_result($user_id_exists);
		 $stmt->execute();
		 $stmt->fetch();
		 $stmt->close();
		 
			if(empty($user_id_exists)) {
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['OTP_FAILED_USER_NOT_EXISTS'];
			return $sr;	
			}
		}
	

	$pushSmsUrl="http://bulksms.sms2india.info/sendsms.php?user=liqvid&password=liqvid%40123&sender=LIQVID&countrycode=91&PhoneNumber=".$user_phone."&text=".urlencode($retmsg)."&gateway=UES3B2ZX";
	$postResult = file_get_contents($pushSmsUrl);

	/*if (!empty($user_email))
    {
	require_once './phpMailer/mail.php';
	$subject = "OTP verification";
	$mail = sendMail($user_email, $subject, $retmsg);
	}*/
	
	
	
	$query = "INSERT INTO  tblx_otp(otp,requested_by,created_on,valid_upto) values(?,?,NOW(),DATE_ADD(NOW(), INTERVAL 120 SECOND))";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $otp, $user_phone);
    $stmt->execute();
    $stmt->close();
	

	$sr = new ServiceResponse("SUCCESS",0,null);
	$obj->expires_on = '120';
	$retVal = new stdClass();
	$sr->retVal->msg = $alert_msg_arr['OTP_SENT'];
	$sr->setval($obj);
	}

	if(!empty($user_email))
    {
	
	 if($user_action=='registration')
		{
		 $stmt = $con->prepare("select user_id from user_credential where loginid='$user_email'");
		 $stmt->bind_result($user_id_exists);
		 $stmt->execute();
		 $stmt->fetch();
		 $stmt->close();
		 
			if(!empty($user_id_exists)) {
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['OTP_FAILED_USER_EXISTS'];
			return $sr;	
			}
		}
	 
	 if($user_action=='forgot_password')
		{
		 $stmt = $con->prepare("select user_id from user_credential where loginid='$user_email'");
		 $stmt->bind_result($user_id_exists);
		 $stmt->execute();
		 $stmt->fetch();
		 $stmt->close();
		 
			if(empty($user_id_exists)) {
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['OTP_FAILED_USER_NOT_EXISTS'];
			return $sr;	
			}

		}

	require_once './phpMailer/mail-ee.php';
	$subject = "OTP Mail";
	$mail = sendMail($user_email, $subject, $retmsg);
	
	$query = "INSERT INTO  tblx_otp(otp,requested_by,created_on,valid_upto) values(?,?,NOW(),DATE_ADD(NOW(), INTERVAL 120 SECOND))";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $otp, $user_email);
    $stmt->execute();
    $stmt->close();
	

	$sr = new ServiceResponse("SUCCESS",0,null);
	$obj->expires_on = '120';
	$retVal = new stdClass();
	$sr->retVal->msg = $alert_msg_arr['OTP_SENT'];
	$sr->setval($obj);
	}

	///////////////check for username////////
	if(!empty($user_name))
    {
	
	 if($user_action=='forgot_password')
		{
		 $stmt = $con->prepare("select user_id from user_credential where loginid='$user_name'");
		 $stmt->bind_result($user_id_exists);
		 $stmt->execute();
		 $stmt->fetch();
		 $stmt->close();
		 
			if(empty($user_id_exists)) {
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['OTP_FAILED_USER_NOT_EXISTS'];
			return $sr;	
			}
			$stmt = $con->prepare("SELECT u.email_id FROM user u, user_credential uc WHERE u.user_id=uc.user_id and uc.loginid='$user_name'");
		    $stmt->bind_result($user_name_email);
		    $stmt->execute();
		    $stmt->fetch();
		    $stmt->close();

		}

	require_once './phpMailer/mail-ee.php';
	$subject = "OTP Mail";
	$mail = sendMail($user_name_email, $subject, $retmsg);
	
	$query = "INSERT INTO  tblx_otp(otp,requested_by,created_on,valid_upto) values(?,?,NOW(),DATE_ADD(NOW(), INTERVAL 120 SECOND))";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $otp, $user_name_email);
    $stmt->execute();
    $stmt->close();
	

	$sr = new ServiceResponse("SUCCESS",0,null);
	$obj->expires_on = '120';
	$retVal = new stdClass();
	$sr->retVal->msg = $alert_msg_arr['OTP_SENT'];
	$sr->setval($obj);
	}
	//////////////////
	
	return $sr;	

}

function getAppVersionTest($client_id)
{
	$arr = array('status' => 0, 'msg' => '', 'data' => array());
	$data = array();
	//new server
	if($client_id=='wiley')
	{
		$row['android'] = "1";
		$row['android_url'] = "https://play.google.com/store/apps/details?id=com.wiley.app";
		$row['ios'] = "1.10";
		$row['ios_url'] = "https://apps.apple.com/us/app/american-life-art/id1497272274";
	}

	$data[] = $row;
	$arr['msg'] = '';
	$arr['status'] = 1;
	$arr['force_update'] = 'no';
	$arr['isLive'] = 'yes';
	$arr['data'] = $data;
	return $arr;
}

function refreshToken($con, $loginid, $deviceId, $platform, $client, $appVersion){
		
		global $demoLicense;
		global $wileyDemo;

		if($client == 'wiley'){
			$demoLicense = $wileyDemo;
		}
		else {
			$demoLicense = $demoLicense;
		}
		$stmt = $con->prepare("SELECT user_id FROM user_credential WHERE loginid = ?");
		$stmt->bind_param("s",$loginid);
		$stmt->execute();
		$stmt->bind_result($user_id);
		$stmt->fetch();
		$stmt->close();
		
		//////////calculate  duration_in_days////////////////////
		$infoArr = array();
		$curDate = date('Y-m-d');
		$registeredLic = getUserRegisteredLicense($con, $user_id);
		if(count($registeredLic) > 0){ 
			$temppc = "";
			foreach($registeredLic as $pc){
				$temppc.= $pc->package_code.',';
			}
			$temppc = $temppc.$demoLicense;
			
			$dataArr = getLicenseDeviceCount($con, $temppc);
			foreach($dataArr as $value){
				if($value->EXP_DATE != ""){
					$duration_in_days = count_days($curDate, $value->EXP_DATE);
				}else{					
		
					$stmt = $con->prepare("SELECT DATE(created_date) created_date FROM course_codes WHERE unique_code = ?");
					$stmt->bind_param("s",$value->PACKAGE_CODE);
					$stmt->execute();
					$stmt->bind_result($created_date);
					$stmt->fetch();
					$stmt->close();
				
					$ddays = $value->EXP_DAYS;
					$date = strtotime("+".$ddays." days", strtotime($created_date));
					$exp_date = date("Y-m-d", $date);
					$duration_in_days = count_days($curDate, $exp_date);
				}
				
				if($duration_in_days == 0){
					$duration_in_days = 1;
				}
				if($duration_in_days < 0){
					$duration_in_days = 0;
				}
				
				
				if($value->IS_BLOCK == 'yes'){
					$value->IS_BLOCK = 0;
				}else{
					$value->IS_BLOCK = 1;
				}
				
				if (strpos($value->PLATFORM, strtolower($platform)) !== false) {
					$platform_status = 1;
				}else{
					$platform_status = 0;
				}

				////// check max usage of devices ///////				
				
				$stmt = $con->prepare("SELECT COUNT(*) record_exists FROM tbl_app_device_used WHERE license = ? AND device_id = ?");
				$stmt->bind_param("ss",$value->PACKAGE_CODE, $deviceId);
				$stmt->execute();
				$stmt->bind_result($record_exists);
				$stmt->fetch();
				$stmt->close();
				
				if(!$record_exists){					
					$stmt = $con->prepare("SELECT COUNT(DISTINCT(device_id)) lic_used FROM tbl_app_device_used WHERE BINARY license = ?");
					$stmt->bind_param("s",$value->PACKAGE_CODE);
					$stmt->execute();
					$stmt->bind_result($lic_used);
					$stmt->fetch();
					$stmt->close();
					
					if($lic_used >= $value->DEVICE_COUNT){
						$device_status = 0;
					}else{
						$device_status = 1;
						$stmt= $con->prepare("INSERT INTO tbl_app_device_used(app_id,license,device_id,consumer_id,device_type) values('1000', ?, ?, ?, ? )");
						$stmt->bind_param("ssis",$value->package_code,$deviceId, $user_id, $platform);
						$stmt->execute();
						$stmt->close();
					}
					
				}else{
					$device_status = 1;				
				}
				
				if($value->PACKAGE_CODE == $demoLicense){
					$duration_in_days = '-1';
					$device_status = 1;
				}
				
				$infoArr[] = array('duration_in_days' => $duration_in_days, 'device_status' => $device_status, 'platform_status' =>$platform_status, 'is_block' => $value->IS_BLOCK, 'package_code' => $value->PACKAGE_CODE, "product" => $value->PRODUCT);
			}
		}
	
		$fetechstmt = $con->prepare("select session_id from api_session where user_id=? and valid_upto > NOW()");
		$fetechstmt->bind_param("i",$user_id);
		$fetechstmt->bind_result($ssid);
		$fetechstmt->execute();
		$fetechstmt->fetch();
		if(!isset($ssid) || $ssid===null || $ssid ==="") {
			$part1 = md5($user_id);
			$part2 = uniqid();
			$entireKey = $part1.$part2;
			$ssid = md5($entireKey);
		}
		$fetechstmt->close();
		
		
		/* $stmt = $con->prepare("SELECT first_name,middle_name,last_name, loginid from user_credential a, user b where a.user_id='".$user_id."' and a.user_id=b.user_id");
		$stmt->bind_result($fname,$mname,$lname, $loginid);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close(); */
		$stmt = $con->prepare("SELECT us.first_name,us.middle_name,us.last_name, uc.loginid,urm.role_definition_id from user_credential uc
								JOIN user_role_map urm ON urm.user_id = uc.user_id 
								JOIN user us ON us.user_id = uc.user_id where uc.user_id=?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($fname,$mname,$lname, $loginid,$role_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		
		 $updatest = $con->prepare("insert INTO api_session(user_id,session_id,valid_upto,app_version) values(?,?,DATE_ADD(NOW(), INTERVAL +4 HOUR),?) ON DUPLICATE KEY UPDATE session_id=?,valid_upto=DATE_ADD(NOW(), INTERVAL +4 HOUR)") or die ('some issue here '.$con->error);
		$xcv = $updatest->bind_param("isis",$user_id,$ssid,$appVersion,$ssid);
		$updatest->execute();
		
		$stmt = $con->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ".$user_id);
		$stmt->execute();
		$stmt->bind_result($system_name);
		$stmt->fetch();
		$stmt->close();
		
         if($role_id==5){
			$role_type='masterAdmin';
		}elseif($role_id==3){
			$role_type='superAdmin';
		}elseif($role_id==7){
			$role_type='regionAdmin';
		}elseif($role_id==4){
			$role_type='centerAdmin';
		}elseif($role_id==1){
			$role_type='trainer';
		}elseif($role_id==2){
			$role_type='learner';
		}else{
			$role_type='default';			
		}
        // track visiting user
        $trv_arr = array();
        $trv_arr['user_id'] = $user_id;
        $trv_arr['date'] = date('Y-m-d'); 
        $trv_arr['date_with_time'] = date('Y-m-d h:i:s');
        $trv_arr['platform'] = $platform;
        $trv_arr['device'] = $deviceId;
        $trv_arr['role'] = $role_type;
        addVisitingUser($con, $trv_arr);
		
		$sr = new ServiceResponse("SUCCESS",0,null);
		$retVal->token = $ssid;
		$retVal->profile_pic = $system_name;
		$retVal->packageInfo = $infoArr;
		$retVal->name = $fname." ";
		if(isset($lname))
			$retVal->name .= $lname;
		$retVal->user_id = $user_id;
		$sr->setval($retVal);
        return $sr;
}

function addVisitingUser($con , $arr){
    
    $user_id = isset($arr['user_id']) ? $arr['user_id'] : '';
    $date = isset($arr['date']) ? $arr['date'] : '';
    $date_with_time = isset($arr['date_with_time']) ? $arr['date_with_time'] : '';
    $platform = isset($arr['platform']) ? $arr['platform'] : '';
    $device = isset($arr['device']) ? $arr['device'] : '';
    $event = isset($arr['event']) ? $arr['event'] : '';
    $role = isset($arr['role']) ? $arr['role'] : '';
    
    if( empty($user_id) || empty($date) ){
        return false;
    }
    
    $sql = "INSERT INTO visiting_user (user_id, date, date_with_time, role, event, platform, device, create_date, update_date) "
            . " VALUES (?,?,?,?,?,?,?, NOW(), NOW() ) ON DUPLICATE KEY UPDATE update_date = NOW() ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $date, $date_with_time, $role, $event, $platform, $device);
    $stmt->execute();
    $stmt->close();
    
    
}


function aduroVisitingUser($params){
    
    $platform = $params->platform;
    $device = $params->deviceId;
    
    foreach($params->visit_data as $vd){
        if( empty($vd->user_id) || empty($vd->date) || empty($vd->role) ){
            return false;
        }
    }
    
    $con = createConnection();
    
    foreach($params->visit_data as $vd){
        
        $user_data = (array) $vd;
        $user_data['platform'] = $platform;
        $user_data['device'] = $device;
        
        addVisitingUser($con, $user_data);
        
    }
    
    return true;
    
}

function getUserRegisteredLicense($con, $user_id){
	$stmt = $con->prepare("SELECT package_code, GROUP_CONCAT(course_code) course_code, product FROM tbl_user_license_course_map WHERE user_id = ? GROUP BY user_id, package_code ORDER BY id ASC");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($package_code,$course_code, $product);
	$stmt->execute();
	$cList = array();
	$package_codes = "";
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->package_code = $package_code;
		$bcm->course_code = $course_code;
		$bcm->product = $product;
		array_push($cList,$bcm);
	}
	$stmt->close();
	return $cList;
}

function trackCentralLicensingData($con,$user_id,$edge_id,$start_date_ms, $end_date_ms,$course_code, $unique_code='', $platform='') {
	
	
    global $client_id_to_block;
	$second_time = $start_date_ms / 1000;
	$start_datetime = date('Y-m-d H:i:s',($start_date_ms/1000));
	//file_put_contents("datetime.txt",$start_date_ms."--".$start_datetime);
	if($end_date_ms > $start_date_ms){    
		$second_spent = ($end_date_ms - $start_date_ms) / 1000;
	}else{
		//////error_log("something is wrong in tracking::end_date_ms=$end_date_ms::start_date_ms=$start_date_ms");
		$second_spent = 60;
	}
	
  /*   $query = "select b.batch_id, b.center_id from user_role_map a,batch b where a.user_id=? and b.user_group_id=a.user_group_id";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i",$user_id);
    $stmt->bind_result($batch_id, $center_id); 
    $stmt->execute();
    $stmt->fetch();
    $stmt->close(); */

	
	 $query = "select role_definition_id from user_role_map where user_id=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i",$user_id);
    $stmt->bind_result($role_definition_id); 
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	
	
	
	$sessionType = "CM";	
	if($edge_id != "" ){
		$query = "SELECT tnct.category_id FROM tree_node_cat_master tnct
					JOIN tree_node_def tnd ON tnd.tree_node_category_id = tnct.category_id
					JOIN generic_mpre_tree gmt ON tnd.tree_node_id = gmt.tree_node_id
					WHERE gmt.edge_id = ? ";
		$stmt = $con->prepare($query);
		$stmt->bind_param("i",$edge_id);
		$stmt->bind_result($category_id); 
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
	//get course id from code
		$course_code_arr = explode('-',$course_code);
		$course_id = $course_code_arr[1];
		$component_type='';
		
		if($category_id == 2){
			$sessionType = 'CH';
			$chapter_edge_id = $edge_id;
			$topic_edge_id = getParentEdgeId($edge_id);
			$course_edge_id = getSuperRootEdgeId($topic_edge_id);
			
		}elseif($category_id == 5){
			$sessionType = 'AS';
			$chapter_edge_id = 0;
			$topic_edge_id = 0;
			$course_edge_id = getParentEdgeId($edge_id);
		}elseif($category_id == 6 || $category_id == 9 || $category_id == 10 || $category_id == 11 || $category_id == 15 || $category_id == 17 || $category_id == 18 || $category_id == 19 || $category_id == 20 || $category_id == 21){
			$sessionType = 'CM';
			$component_edge_id = $edge_id;
			$chapter_edge_id = getParentEdgeId($edge_id);
			$topic_edge_id = getParentEdgeId($chapter_edge_id);
			$course_edge_id = getSuperRootEdgeId($topic_edge_id);
			$component_type = getComponentType($component_edge_id);
		}elseif($category_id == 12 || $category_id == 13 || $category_id == 14 || $category_id == 22){
			$sessionType = 'CM';
			$component_edge_id = getParentEdgeId($edge_id);
			$chapter_edge_id = getParentEdgeId($component_edge_id);
			$topic_edge_id = getParentEdgeId($chapter_edge_id);
			$course_edge_id = getSuperRootEdgeId($topic_edge_id);
			$component_type= getComponentType($component_edge_id);
		}else{
			$sessionType = '';
		}
	}
	
	if($sessionType!=""){
	
	$second_spent = intval($second_spent);
    $second_time  = intval($second_time);
	if($edge_id){
		if($center_id=="")
		{
		$center_id=0;
		}
		if($batch_id=="")
		{
		$batch_id=0;
		}
		if($ts_id=="")
			{
			$ts_id=0;
			}
			
		$dateTime = date('Y-m-d H:i:s');

		if($second_spent > 300){
			$second_spent = 300;
		}

		
		
			
			

		$ex_user_name='';
		$query = "INSERT INTO user_session_tracking(user_id,session_id,session_type,center_id,user_role_id,actual_seconds,track_datettime,batch_id,course_code,platform,unique_code,start_datetime,ex_course_id,ex_course_edge_id,ex_topic_edge_id,ex_chapter_edge_id,ex_component_edge_id,ex_component_type,ex_ts_id) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		
		$aduro_batch_center = getAduroBatchAndCenterForUser($con, $user_id); 
        $center_id_aduro = $aduro_batch_center['center_id'];
        $batch_id_aduro = $aduro_batch_center['batch_id'];
		$batch_code = $aduro_batch_center['batch_code'];
		$center_code = $aduro_batch_center['center_code'];
		$client_id = getUserClientId($con,$user_id);

		$sync_client_id=checkBlockCenters($con, $center_id_aduro);
		if (!in_array($sync_client_id, $client_id_to_block))
		{	
			if($second_spent!=0 && $role_definition_id==2)
				{
			$stmt1 = $con->prepare($query);
			$stmt1->bind_param("iisiiisissssiiiiisi",$user_id,$edge_id,$sessionType,$center_id,$role_definition_id,$second_spent,$dateTime,$batch_id,$course_code,$platform,$unique_code,$start_datetime,$course_id,$course_edge_id,$topic_edge_id,$chapter_edge_id,$component_edge_id,$component_type,$ts_id);
			$stmt1->execute();
			$stmt1->close();
			$tracking_id = $con->insert_id;
			$parentObj = new stdClass();
			$parentObj->tracking_id = $tracking_id;
			$parentObj->client_id = $client_id;
			$parentObj->center_id = $center_id_aduro;
			$parentObj->batch_id = $batch_id_aduro;
			$parentObj->center_code = $center_code;
			$parentObj->batch_code = $batch_code;
			$parentObj->user_id = $user_id;
			$parentObj->user_role = $role_definition_id;
			$parentObj->course_code = $course_code;
			$parentObj->course_id =$course_id;
			$parentObj->course_edge_id = $course_edge_id;
			$parentObj->topic_edge_id = $topic_edge_id;
			$parentObj->chapter_edge_id = $chapter_edge_id;
			$parentObj->component_edge_id = $component_edge_id;
			$parentObj->component_type = $component_type;
			$parentObj->session_type = $sessionType;
			$parentObj->time_spent = $second_spent;
			$parentObj->date_attempted = date('Y-m-d H:i:s');
			$parentObj->platform = $platform;
			$parentObj->unique_code = $unique_code;
			$parentObj->chapter_attempted = date('Y-m-d').$chapter_edge_id.$user_id;
			$parentObj->action = 'add_user_session_tracking';
			//sendToCollection($parentObj);
			}
			
			
		}

	}
    if ($con->error) {
        $retObj->status="FAILURE";
        $retObj->reason=$con->error;
    } else {
        $retObj->status="SUCCESS";
    }
	
	

	}else{
		
        $retObj->status="FAILURE";
        $retObj->reason='Invalid Session Type';
    
	}
	
}

function getTrackcentralLicensingData($con,$user_id){
	$dataArr = array();
				$query = "SELECT  SUM(actual_seconds) duration_ms , course_code, session_id, unique_code FROM user_session_tracking WHERE user_id = ? AND unique_code != '' AND LENGTH(unique_code) >= 10 GROUP BY unique_code, course_code, session_id ORDER BY unique_code, course_code";			
				$stmt = $con->prepare($query);  
				$stmt->bind_param("i",$user_id);     
				$stmt->bind_result($duration_ms,$course_code, $edge_id, $unique_code);
				$stmt->execute();
				
				while($stmt->fetch()) {
					$bcm = new stdClass();
					$bcm->duration_ms = $duration_ms * 1000;
					$bcm->course_code = $course_code;
					$bcm->edge_id = $edge_id;
					$bcm->package_code = $unique_code;
					array_push($dataArr,$bcm);
				}

			foreach($dataArr as $key => $value){
				$finalArr[] = array('package_code' => $value->package_code, 'duration_ms' => $value->duration_ms, 'course_code' => $value->course_code, 'edge_id' => $value->edge_id);
			}

	$retObj->status="SUCCESS";
	$retObj->tracking = $finalArr;
    return $retObj;
}


function aduroTracker($con,  $params, $extra_params ) {
    
    // calculate type 
	global $client_id_to_block;
    $edge_id_arr = array();
    foreach( $params as $obj){
        if( !empty($obj->edge_id) ){
            $edge_id_arr[$obj->edge_id] = $obj->edge_id;
        }
    }
    $session_type_arr = array();
    if( count($edge_id_arr) ){
        $sub_sql = "'" . implode("','", $edge_id_arr) . "'";
        $query = "SELECT gmt.edge_id, tnct.category_id FROM tree_node_cat_master tnct
                        JOIN tree_node_def tnd ON tnd.tree_node_category_id = tnct.category_id
                        JOIN generic_mpre_tree gmt ON tnd.tree_node_id = gmt.tree_node_id
                        WHERE gmt.edge_id IN ($sub_sql) ";
        $stmt = $con->prepare($query);
        $stmt->bind_result($loop_egde_id, $loop_category_id); 
        $stmt->execute();
        while( $stmt->fetch() ){
            
            if($loop_category_id == 2){
                $sessionType = 'CH';
            }elseif($loop_category_id == 5){
                $sessionType = 'AS';
            }else{
                $sessionType = 'CM';
            }
            $session_type_arr[$loop_egde_id] = $sessionType;
        }
        $stmt->close();
        
    }
    
    $dateTime = date('Y-m-d H:i:s');
    $date = date('Y-m-d');
    
    file_put_contents('chk_session_3.txt','ok');
    foreach($params as $obj){
        
		 $query = "select role_definition_id from user_role_map where user_id=?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($role_definition_id); 
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
        $user_id = $obj->user_id;
        $center_id = $obj->center_id;
        $batch_id = $obj->batch_id;
        $unique_code = $obj->package_code;
        $course_code = $obj->course_code;
        $edge_id = $obj->edge_id;
        $time_spent = $obj->seconds_spent; // in seconds 
        $platform = $extra_params['platform'];
        $track_date_time = $dateTime;
        $track_date = $date;
        if( isset($obj->track_date) && !empty($obj->track_date) ){
            $track_date_time = date('Y-m-d H:i:s', strtotime($obj->track_date));
            $track_date = date('Y-m-d', strtotime($obj->track_date));
        }
        
        if( empty($user_id) || empty($center_id) || empty($batch_id) || empty($unique_code) || 
                empty($course_code) || empty($edge_id) || empty($time_spent)  ){
            continue;
        }
        
        $sessionType = "CM";	
        if( isset($session_type_arr[$edge_id])){
            $sessionType = $session_type_arr[$edge_id];
        }
        $query = "SELECT tnct.category_id FROM tree_node_cat_master tnct
					JOIN tree_node_def tnd ON tnd.tree_node_category_id = tnct.category_id
					JOIN generic_mpre_tree gmt ON tnd.tree_node_id = gmt.tree_node_id
					WHERE gmt.edge_id = ? ";
		$stmt = $con->prepare($query);
		$stmt->bind_param("i",$edge_id);
		$stmt->bind_result($category_id); 
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		//get course id from code
		$course_code_arr = explode('-',$course_code);
		$course_id = $course_code_arr[1];
		$component_type='';
		
		if($category_id == 2){
			$sessionType = 'CH';
			$chapter_edge_id = $edge_id;
			$topic_edge_id = getParentEdgeId($edge_id);
			$course_edge_id = getSuperRootEdgeId($topic_edge_id);
			
		}elseif($category_id == 5){
			$sessionType = 'AS';
			$chapter_edge_id = 0;
			$topic_edge_id = 0;
			$course_edge_id = getParentEdgeId($edge_id);
		}elseif($category_id == 6 || $category_id == 9 || $category_id == 10 || $category_id == 11 || $category_id == 15 || $category_id == 17 || $category_id == 18 || $category_id == 19 || $category_id == 20 || $category_id == 21){
			$sessionType = 'CM';
			$component_edge_id = $edge_id;
			$chapter_edge_id = getParentEdgeId($edge_id);
			$topic_edge_id = getParentEdgeId($chapter_edge_id);
			$course_edge_id = getSuperRootEdgeId($topic_edge_id);
			$component_type = getComponentType($component_edge_id);
		}elseif($category_id == 12 || $category_id == 13 || $category_id == 14 || $category_id == 22){
			$sessionType = 'CM';
			$component_edge_id = getParentEdgeId($edge_id);
			$chapter_edge_id = getParentEdgeId($component_edge_id);
			$topic_edge_id = getParentEdgeId($chapter_edge_id);
			$course_edge_id = getSuperRootEdgeId($topic_edge_id);
			$component_type= getComponentType($component_edge_id);
		}else{
			$sessionType = '';
		}
	
	if($sessionType!=""){
		
		// check for duplicates 
		$select_qry = "Select count(*) as cnt from user_session_tracking WHERE user_id = ? AND session_id = ? AND session_type = ? AND actual_seconds = ? "
            . " AND date(track_datettime) = ? AND platform = ? AND unique_code = ? "
            . " AND center_id_aduro = ? AND batch_id_aduro = ? ";

		$select_stmt = $con->prepare($select_qry);
        
        $select_stmt->bind_param("sssssssss", $user_id, $edge_id, $sessionType, $time_spent, 
                $track_date, $platform, $unique_code, $center_id, $batch_id );

        $select_stmt->bind_result($records_found);
        $select_stmt->execute();
        $select_stmt->fetch();
        $select_stmt->close();
        
        if( $records_found < 1){
            
         

		if($time_spent > 2000)
		{
			$time_spent = rand(1900,2050);
		}
		
		if($time_spent!=0 && $role_definition_id==2)
			{
				
				
				
				
			/* $query = "INSERT INTO user_session_tracking(user_id, session_id, session_type, center_id, user_role_id, ideal_seconds"
            . ", actual_seconds, track_datettime, batch_id, course_code, platform, unique_code, center_id_aduro, batch_id_aduro ) "
            . " values(?,?,?,0,0,0,?,?,0,?,?,?,?,?) ";

    
			$stmt1 = $con->prepare($query);
            $stmt1->bind_param("ssssssssss", $user_id, $edge_id, $sessionType, $time_spent, 
                    $track_date_time, $course_code, $platform, $unique_code, $center_id, $batch_id );
            $stmt1->execute();
			$stmt1->close(); */
			
			
			
			$query = "INSERT INTO user_session_tracking(user_id,session_id,session_type,center_id,user_role_id,ideal_seconds,actual_seconds,track_datettime,batch_id,course_code,platform,unique_code,center_id_aduro,batch_id_aduro,start_datetime,ex_course_id,ex_course_edge_id,ex_topic_edge_id,ex_chapter_edge_id,ex_component_edge_id,ex_component_type) values(?,?,?,?,?,500,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			
			$stmt1 = $con->prepare($query);
			$stmt1->bind_param("iisiiisisssiisiiiiis",$user_id,$edge_id,$sessionType,$center_id,$role_definition_id,$second_spent,$dateTime,$batch_id,$course_code,$platform,$unique_code,$center_id_aduro,$batch_id_aduro,$dateTime,$course_id,$course_edge_id,$topic_edge_id,$chapter_edge_id,$component_edge_id,$component_type);
			$stmt1->execute();
			$stmt1->close();
			$tracking_id = $con->insert_id;
			
			
			$client_id = getUserClientId($con,$user_id);
			$parentObj = new stdClass();
			$parentObj->tracking_id = $tracking_id;
			$parentObj->client_id = $client_id;
			$parentObj->center_id = $center_id_aduro;
			$parentObj->batch_id = $batch_id_aduro;
			$parentObj->center_code = $center_code;
			$parentObj->batch_code = $batch_code;
			$parentObj->user_id = $user_id;
			$parentObj->user_role = $role_definition_id;
			$parentObj->course_code = $course_code;
			$parentObj->course_id =$course_id;
			$parentObj->course_edge_id = $course_edge_id;
			$parentObj->topic_edge_id = $topic_edge_id;
			$parentObj->chapter_edge_id = $chapter_edge_id;
			$parentObj->component_edge_id = $component_edge_id;
			$parentObj->component_type = $component_type;
			$parentObj->session_type = $sessionType;
			$parentObj->time_spent = $second_spent;
			$parentObj->date_attempted = date('Y-m-d H:i:s');
			$parentObj->platform = $platform;
			$parentObj->unique_code = $unique_code;
			$parentObj->chapter_attempted = date('Y-m-d').$chapter_edge_id.$user_id;
			$parentObj->action = 'add_user_session_tracking';
			//sendToCollection($parentObj);
			
			}
			//}

		////}

        }
        
    } 
    } 
    
    $res = array('status' => 1 , 'msg' => '');
    
    if ($con->error ) {
        $res['status'] = 0;
        $res['msg'] = $con->error;
    } 
    
    return $res;
}


function centralLicensingCourseCheck($con,$param,$user_id){
	global $authoring_path;
	$product_id = $param->product_id;
	$center_id = $param->center_id;
	$batch_id = $param->batch_id;
	$course_code = $param->course_code;
	$licence_key = $param->licence_key;
	// add custom topic
	if($product_id!='' && $center_id!='' && $batch_id!='' && $course_code!=''){
		$stmt = $con->prepare("select topic,chapter from tblx_product_configuration  where product_id=? and institute_id=? and batch_id=?");
		$stmt->bind_param("iii",$product_id,$center_id,$batch_id);
		$stmt->execute();
		$stmt->bind_result($topic,$chapter);
		$stmt->fetch();
		$stmt->close();
		$customTopic=$topic;
		$customChapter=$chapter;
		
	}else{
		$customTopic='';
		$customChapter='';
	}
	
	
	$courseID = getCourseIdByCourseCode($course_code);
	$course_type = getCourseTypeByCourseCode($course_code);
	if($courseID){
		$courseDetails=getCourseDetailsCourseId($courseID);
			if($courseDetails->thumnailImg!=""){
				$imgPath=$authoring_path."/view/uploads/".$courseDetails->thumnailImg;
			}else{
				//$imgPath=$authoring_path."/view/images/".$course_code.".png";
				$imgPath=$authoring_path."/view/images/default_course.png";
			} 
			$courseCatalogID = getCatLogEdgeIdByCourseId($courseID);
			$courseArr = array("course" => array("name" => stripslashes(publishText($courseDetails->title)), "description" => stripslashes(publishText($courseDetails->description)), "duration" => $courseDetails->duration, "version" => $courseDetails->published_version, "edgeId" => $courseDetails->edge_id, "imgPath" => $imgPath, "catlog" => array("edgeId" =>$courseCatalogID, 'catComponent' => array())));
		
			$topics = getTopicOrAssessmentByCourseId($courseID,$customTopic);
				
	
			$totalTopics=count($topics);
			$a = 1;
			$i = 1;
			$scn=1;
			$k = 1;
			$courseACnt=0;
			foreach($topics as $key => $value){
			
				if($value->thumnailImg!=""){
					$imgPath=$authoring_path."/view/uploads/".$value->thumnailImg;
				}else{
					$imgPath=$authoring_path."/view/images/courseimage.png";
				}
			
			
				if($value->assessment_type ==""){
					$activity_count = getActivityCountByTopic($value->edge_id);
					$courseArr['course']['catlog']['catComponent'][] = array("name" => stripslashes(publishText($value->name)), 'componentType' => 'module', "description" => stripslashes(publishText($value->description)), "duration" => $value->duration, "thumnailImg" =>$imgPath,'edgeId' => $value->edge_id, "data" => "PracticeApp/".$course_code."/course/module".$i,'isLocked' => $value->isLocked,'activity_count' => $activity_count);
					$chapters=getChapterByTopicEdgeId($value->edge_id,$customChapter);
					$totalChapters=count($chapters);
					$ch=1;$topicACnt=0;
						
						if($totalChapters>=0){
							$scenarioArr = array();
							foreach($chapters as $keyc=>$valuec){
								
								
								if($course_type == '0'){
									$sCnt=getChapterScenarios($valuec->edge_id);	
									if(count($sCnt) > 0){
										$data ="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn;
										$zipurl = $course_code."/scenario".$scn.".zip";
										$zipsize ="0";
									}else{
										$data = "";
										$zipurl = ""; 
									}
									$zipsize = $valuec->zip_size;
									if($valuec->thumnailImg!=""){
										$imgPath=$authoring_path."/view/uploads/".$valuec->thumnailImg;
									}else{
										$imgPath=$authoring_path."/view/images/courseimage.png";
									}
				
									$scenarioArr[] = array('data' => $data, 'zipurl' => $zipurl, 'zipsize' => $zipsize, 'name' => stripslashes(publishText($valuec->name)), 'description' => stripslashes(publishText($valuec->description)), 'bgcolor' => stripslashes(publishText($valuec->bg_color)), 'chapterSkill' =>$valuec->chapterSkill, 'quesCount' =>$valuec->quesCount,'duration' =>$valuec->duration, 'thumnailImg' => $imgPath, 'edgeId' => $valuec->edge_id, 'chap_sequence' => $valuec->sequence_no);
									
									$scn++;
									$ch++;
							
								
							}elseif($course_type == '1'){
								
								$actvity_type=getChapterScenarioActivity($valuec->edge_id);
								$aCnt=getActivityCountByChapter($valuec->edge_id);
								if($actvity_type!=""){
									$data ="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn;
									$zipurl = $course_code."/scenario".$scn.".zip";
									$zipsize ="0";
								}else{
									$data = "";
									$zipurl = "";
								}
								$zipsize = $valuec->zip_size;
								if($valuec->thumnailImg!=""){
									$imgPath=$authoring_path."/view/uploads/".$valuec->thumnailImg;
								}else{
									$imgPath=$authoring_path."/view/images/courseimage.png";
								}
									
								$scenarioArr[] = array('data' => $data, 'zipurl' => $zipurl, 'zipsize' => $zipsize, 'name' => stripslashes(publishText($valuec->name)), 'description' => stripslashes(publishText($valuec->description)), 'bgcolor' => stripslashes(publishText($valuec->bg_color)), 'chapterSkill' =>$valuec->chapterSkill, 'quesCount' =>$valuec->quesCount,'duration' =>$valuec->duration, 'thumnailImg' => $imgPath, 'edgeId' => $valuec->edge_id, 'chap_sequence' => $valuec->sequence_no, 'actvity_type' => $actvity_type,'activity_count' => $aCnt,'duration' =>60);
								
								$scn++;
								$ch++;
								$courseACnt = $courseACnt+$aCnt;
							}
							
							$courseArr['course']['catlog']['catComponent'][$key]['scenario'] = $scenarioArr;
							
						}
						
						}
						
					
					
					
					$i++;
					}else{
						
						if($value->assessment_type=="pre")
						{
							$assessXML = 'pre_assess.xml';
						}
						elseif($value->assessment_type=="mid")
						{
							$assessXML="assess".$a.".xml";
							$a++;
						}
						elseif($value->assessment_type=="post")
						{
							$assessXML = 'post_assess.xml';
						}
						
						$assSkillArr = getAssessmentSkills($con, $value->edge_id);
						$assQuesCount = getAssessmentQuesCount($con, $value->edge_id);
						if(checkMidQuestion($con, $value->edge_id) != ''){
							$courseArr['course']['catlog']['catComponent'][] = array("name" => stripslashes(publishText($value->name)), 'componentType' => 'assessment', 'edgeId' => $value->edge_id, "thumnailImg" =>$imgPath, "data" => "PracticeApp/".$course_code."/course/".$assessXML, 'zipurl' => $course_code."/assessment-".$value->edge_id.".zip", 'zipsize' => $value->zip_size, 'assessment_type' => $value->is_survey, 'skills' => $assSkillArr, 'total_question' => $assQuesCount, 'ttl_ques_to_show' => $value->ttl_ques_to_show,'passing_score' =>$value->passing_score,'no_of_skill_ques' =>$value->no_of_skill_ques);
							$k++;
						}else{
							$courseArr['course']['catlog']['catComponent'][] = array("name" => stripslashes(publishText($value->name)), 'componentType' => 'assessment', 'edgeId' => $value->edge_id, "thumnailImg" =>$imgPath, "data" => "", 'zipurl' => "", 'zipsize' => $value->zip_size,'assessment_type' => $value->is_survey, 'skills' => $assSkillArr, 'total_question' => $assQuesCount, 'ttl_ques_to_show' => $value->ttl_ques_to_show,'passing_score' =>$value->passing_score,'no_of_skill_ques' =>$value->no_of_skill_ques);
						}
					}
				
			}
			
		
	}

	$retObj->status=1;
	$retObj->course_code = $course_code;
	$retObj->activity_count = $courseACnt;
	$retObj->courseArr = $courseArr;
	$retObj->msg='SUCCESS';
	return $retObj;

}

function getChapterScenarioActivity($chap_edge_id) {
        $con = createConnection();
        $sql = "select activity_type from tblx_activity WHERE chapter_edge_id =? ";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i",$chap_edge_id);     
		$stmt->bind_result($activity_type);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		if(!isset($activity_type) || $activity_type==="") {
			return null;
		}

        return $activity_type;
    }

function checkMidQuestion($con, $edge_id){
	$query = "SELECT id FROM tbl_questions WHERE parent_edge_id = ? LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $edge_id);
    $stmt->bind_result($id);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	return $id;
}

function getChapterMultiComponentJson($con,$course_code, $edgeId){
	global $authoring_path;
	$courseID = getCourseIdByCourseCode($course_code);
	if($courseID){
		$courseDetails=getCourseDetailsCourseId($courseID);
		$courseCatalogID = getCatLogEdgeIdByCourseId($courseID);
		$course_type = getCourseTypeByCourseCode($course_code);
		$topics = getTopicOrAssessmentByCourseId($courseID,'');
		$totalTopics=count($topics);
		
		$a = 1;
		$i = 1;
		$scn=1;
		
		$chapterEdgeId = $edgeId;
		
		foreach($topics as $key => $value){
		
			if($value->assessment_type ==""){
					$chapters=getChapterByTopicEdgeId($value->edge_id,'');
					$totalChapters=count($chapters);
					$ch=1;
					
					if($totalChapters>=0){
						$scenarioArr = array();
						foreach($chapters as $keyc=>$valuec){
                            
                            if($course_type == 0){
							$scenarioArr = array();
							$sCnt=getChapterScenarios($valuec->edge_id);
							
								if($valuec->edge_id == $chapterEdgeId){
									$scenarioArr = array("edgeId" => $chapterEdgeId, "component" => array());
									$scenarios = getScenarioByChapterId($chapterEdgeId);
									
									$totalScenarios=count($scenarios);
									if($totalScenarios > 0){
										
										
										foreach($scenarios as $keys=>$values){
										
										 
										$component_description = $values->component_description;
									    $comp_sequence = $values->sequence_no;
										
										 if($values->scenario_type=="Concept"){
										 	if(!empty($values->component_edge_id)){
														$compEdgeIdCC = $values->component_edge_id;
												}
											else{ $compEdgeIdCC = getComponentEdgeIdByChapterEdgeId($chapterEdgeId,6);}
											
											$conceptFilePath = getComponentXmlPath($con, $compEdgeIdCC);
											
											if($conceptFilePath!=""){
												$data="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$conceptFilePath;
											}else{
												$data="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/concept".$scn.".xml";
											}
											
											
											if($values->thumbnailImg!=""){
												$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
											}else{
												$thumbnailImg=$authoring_path."/view/images/concept.png";
											}
											
											
											$scenarioArr['component']['concept'][] = array("name" => stripslashes(publishText($values->scenario_name)), "edgeId" => $compEdgeIdCC, "data" => $data, "comp_sequence" => $comp_sequence, "thumbnailImg" => $thumbnailImg, "component_description" => $component_description);
					 
										 } 
									
										 if($values->scenario_type=="Practice"){
												
												$capPracticeEdgeId = getComponentEdgeIdByChapterEdgeId($chapterEdgeId,8);
												$scenarioArr['component']['practice']['edgeId'] = $capPracticeEdgeId;
												
												
												if($values->scenario_subtype=="Quiz"){
												
													if(!empty($values->component_edge_id)){
														$compEdgeIdQ = $values->component_edge_id;
													}
													else{ $compEdgeIdQ=getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,15); }
													
												    $quizFilePath = getComponentXmlPath($con, $compEdgeIdQ);
											
													if($quizFilePath!=""){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$quizFilePath;
													}else{
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice_mcq".$scn.".xml";
													}
													
													if($values->thumbnailImg!=""){
												$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
												}else{
												$thumbnailImg=$authoring_path."/view/images/practice.png";
												}
													
												   $scenarioArr['component']['practice']['mcq_practice'][] = array("name" => stripslashes(publishText($values->scenario_name)), "edgeId" => $compEdgeIdQ, "data" => $data, "comp_sequence" => $comp_sequence, "thumbnailImg" => $thumbnailImg, "component_description" => $component_description, "is_qt" => $values->is_qt, "is_wc" => $values->is_wc, "statement" => $values->statement, "tips_naming" => $values->tips_naming);
												}
												if($values->scenario_subtype=="Conversation Practice")
												{
												
													if(!empty($values->component_edge_id)){
														$compEdgeIdR = $values->component_edge_id;
													}
													else{ $compEdgeIdR=getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,10); }
													if($compEdgeIdR){	
														
														$vocabFilePath = getComponentXmlPath($con, $compEdgeIdR,1);
												
														if($vocabFilePath!=""){
															$data ="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$vocabFilePath;
														}
														else{
															$data ="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/vocabulary_appVersion.xml";
														}
														
												if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/conversation.png";
													}
														
														$scenarioArr['component']['practice']['vocab_practice'][] = array("name" => stripslashes(publishText($values->scenario_name)), "edgeId" => $compEdgeIdR, "data" => $data, "comp_sequence" => $comp_sequence, "thumbnailImg" => $thumbnailImg, "component_description" => $component_description);
													}	
													
												}
												if($values->scenario_subtype=="Role-play")
												{			
													if(!empty($values->component_edge_id)){
														$rolePlayEdgeId = $values->component_edge_id;
													}
													else{ $rolePlayEdgeId = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,9); }
													////$xmlText.="\t\t\t\t\t\t<edgeId>".$values->parent_edge_id."</edgeId>\n";
													$rpFilePath = getComponentXmlPath($con, $rolePlayEdgeId);
												
													if($rpFilePath!=""){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$rpFilePath;
													}
													else{
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice".$scn.".xml";
													}
													
													
													$rpJsonFilePath = getComponentXmlPath($con, $rolePlayEdgeId,'0',1);
												
													if($rpJsonFilePath!=""){
														$data_json = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$rpJsonFilePath;
													}
													else{
														$data_json = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice".$scn.".json";
													}
								
													
													$rpwEdgeId = getComponentEdgeIdByChapterEdgeId($rolePlayEdgeId,12);
													
													$rpeEdgeId = getComponentEdgeIdByChapterEdgeId($rolePlayEdgeId,13);
													
													$rprEdgeId = getComponentEdgeIdByChapterEdgeId($rolePlayEdgeId,14);
													if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/roleplay.png";
													}
													$scenarioArr['component']['practice']['scenario_practice'][] = array("name" => stripslashes(publishText($values->scenario_name)), "edgeId" => $rolePlayEdgeId, "data" => $data, "data_json" => $data_json, "watch" => array("edgeId"=>$rpwEdgeId), "enact" => array("edgeId"=>$rpeEdgeId), "review" => array("edgeId"=>$rprEdgeId), "comp_sequence" => $comp_sequence, "thumbnailImg" => $thumbnailImg, "component_description" => $component_description);
													
												}
												if($values->scenario_subtype=="Speech Role-play")
												{			
													if(!empty($values->component_edge_id)){
														$rolePlayEdgeId = $values->component_edge_id;
													}
													else{ $rolePlayEdgeId = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,9); }
													
													$rpFilePath = getComponentXmlPath($con, $rolePlayEdgeId);
												
													if($rpFilePath!=""){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$rpFilePath;
													}
													else{
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice".$scn.".json";
													}
								
													
													$rpwEdgeId = getComponentEdgeIdByChapterEdgeId($rolePlayEdgeId,12);
													
													$rpeEdgeId = getComponentEdgeIdByChapterEdgeId($rolePlayEdgeId,13);
													
													$rprEdgeId = getComponentEdgeIdByChapterEdgeId($rolePlayEdgeId,14);
													if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/roleplay.png";
													}
													$scenarioArr['component']['practice']['scenario_speech_practice'][] = array("name" => stripslashes(publishText($values->scenario_name)), "edgeId" => $rolePlayEdgeId, "data" => $data,"comp_sequence" => $comp_sequence, "thumbnailImg" => $thumbnailImg, "component_description" => $component_description);
													
												}
												if($values->scenario_subtype=="Game")
												{			
													if(!empty($values->component_edge_id)){
														$gameEdgeId = $values->component_edge_id;
													}
													else{ $gameEdgeId = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,11);}
													
													if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/game.png";
													}
													
													$scenarioArr['component']['practice']['games_practice']['edgeId'] = $gameEdgeId;
													$scenarioArr['component']['practice']['games_practice']['name'] = stripslashes(publishText($values->scenario_name));
													$scenarioArr['component']['practice']['games_practice']['comp_sequence'] =$comp_sequence;
													$scenarioArr['component']['practice']['games_practice']['thumbnailImg'] =$thumbnailImg;
													$scenarioArr['component']['practice']['games_practice']['component_description'] =$component_description;
													$scenarioArr['component']['practice']['games_practice']['interactive_html'] =$values->interactive_html;$scenarioArr['component']['practice']['games_practice']['scenario_description'] =stripslashes(publishText($values->scenario_description));
													$r = 1;
													$gameArr = getComponentDtlByChapterEdgeId($chapterEdgeId);
													foreach($gameArr as $ky => $vl){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/games/game".$r."/".$vl->launch_file;
														$scenarioArr['component']['practice']['games_practice']['game'][] = array('title' => $vl->game_name, 'data' => $data);
														$r++;
													}
												
												}

												if($values->scenario_subtype=="SpeedReading"){
												
													if(!empty($values->component_edge_id)){
														$compEdgeIdQ = $values->component_edge_id;
													}
													else{ $compEdgeIdQ = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,17); }
													
													$srFilePath = getComponentXmlPath($con, $compEdgeIdQ);

													if($srFilePath!=""){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$srFilePath;
													}
													else{
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice_speed_reading_".$compEdgeIdQ.".xml";
													}
													if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/speedreading.png";
													}							
													$scenarioArr['component']['practice']['speed_reading_practice'][] = array("name" => stripslashes(publishText($values->scenario_name)), "edgeId" => $compEdgeIdQ, "data" => $data, "comp_sequence" => $comp_sequence, "thumbnailImg" => $thumbnailImg, "component_description" => $component_description);
													//echo "<pre>";print_r($scenarioArr);exit;
												}

												if($values->scenario_subtype  == "SpeechRecognition"){
												
													if(!empty($values->component_edge_id)){
														$compEdgeIdQ = $values->component_edge_id;
													}
													else{ $compEdgeIdQ = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,18); }
													
													$file_path = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn.'/media/'. md5($values->file);
													$scenarioArr['component']['practice']['speech_recognition'][] = 
                                                            array( "name" => stripslashes(publishText($values->scenario_name)), 
                                                                "edgeId" => $compEdgeIdQ, 
                                                                "data" => '', 
                                                                'file' => $file_path,
                                                                'instruction' => stripslashes(publishText($values->instruction)),
                                                                'para' => stripslashes(publishText($values->para)),
																"comp_sequence" => $comp_sequence
                                                                );
													//echo "<pre>";print_r($scenarioArr);exit;
												}
												if($values->scenario_subtype  == "Resources"){
												
													if(!empty($values->component_edge_id)){
														$compEdgeIdQ = $values->component_edge_id;
													}
													else{ $compEdgeIdQ = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,19); }
													$resourceFilePath = getComponentXmlPath($con, $compEdgeIdQ);
													
													if($resourceFilePath!=""){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$resourceFilePath;
													}
													else{
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice_resources_".$compEdgeIdQ.".xml";
													}
								
													if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/resources.png";
													}	
								
													//Check for json
													$json_rs_file=json_decode($values->file);
													if($json_rs_file!=""){
														
														$rs_file = $json_rs_file[0]->file;
														$path_info = pathinfo($rs_file);
														if($path_info['extension']=='pdf'){
															$file_path = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn.'/media/'. $rs_file;
															$docType='pdf';
														}else{
															$filePath=$rs_file;
															$docType='videoLink';
														}
														
														$file_path_pdf = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn.'/media/';
														$resourcesPdfCmpData=array();
														foreach($json_rs_file as $key=>$val){
	
															$empty_row = new stdClass();
															$empty_row->file_title = $val->file_title;
															$empty_row->file = $file_path_pdf.$val->file;
															$empty_row->type =$val->type;
															$resourcesPdfCmpData[] = $empty_row;
														}

														$resourcesPdfCmpData=json_encode($resourcesPdfCmpData);
														$scenarioArr['component']['practice']['resources_practice'][] = 
														array("name" => stripslashes(publishText($values->scenario_name)),
														"edgeId" => $compEdgeIdQ, 
														"data" => $file_path,
														"docType" => $docType,
														//'file' => $file_path,
														//'instruction' => stripslashes(publishText($values->instruction))
														'file_array' =>$resourcesPdfCmpData,
														"comp_sequence" => $comp_sequence, 
														"thumbnailImg" => $thumbnailImg, 
														"component_description" => $component_description
														);	
															
														
													}
													else{
													
													$path_info = pathinfo($values->file);
												
													if($path_info['extension']=='pdf'){
														$file_path = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn.'/media/'. $values->file;
														$docType='pdf';
													}else{
														$filePath=$values->file;
														$docType='videoLink';
													}
													
													$scenarioArr['component']['practice']['resources_practice'][] = 
													array("name" => stripslashes(publishText($values->scenario_name)),
													"edgeId" => $compEdgeIdQ, 
													"data" => $file_path,
													"docType" => $docType,
                                                    'file_array' =>'',
													"comp_sequence" => $comp_sequence,
													"thumbnailImg" => $thumbnailImg, 
													"component_description" => $component_description
                                                    );
													
													}
												
												
												}
											if($values->scenario_subtype  == "Conversation Video"){
													if($values->thumbnailImg!=""){
													$thumbnailImg=$authoring_path."/view/uploads/".$values->thumbnailImg;
													}else{
													$thumbnailImg=$authoring_path."/view/images/conversation.png";
													}	
												
													if(!empty($values->component_edge_id)){
														$compEdgeIdQ = $values->component_edge_id;
													}
													else{ $compEdgeIdQ = getComponentEdgeIdByChapterEdgeId($capPracticeEdgeId,20);}
													$cvFilePath = getComponentXmlPath($con, $compEdgeIdQ);
													
													if($cvFilePath!=""){
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/".$cvFilePath;
													}else{
														$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/practice_conversation_".$compEdgeIdQ.".xml";
													}
													
													$file_path = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn.'/media/'. md5($values->file);
													
													$scenarioArr['component']['practice']['conversation_practice'][] = 
													array("name" => stripslashes(publishText($values->scenario_name)),
													"edgeId" => $compEdgeIdQ, 
													"data" => $data,
													"comp_sequence" => $comp_sequence,
													"thumbnailImg" => $thumbnailImg
													);
												}

												
											}
											
										}
									}
									$retObj->status=1;
									$retObj->course_code=$course_code;
									$retObj->chapComponent=$scenarioArr;
									$retObj->msg='SUCCESS';
									return $retObj;	
								}
							
							$scn++;
							$ch++;
						
                        }else{
                            
                            $scenarioArr = array();
								
									if($valuec->edge_id == $chapterEdgeId){
										$scenarioArr = array("edgeId" => $chapterEdgeId, "component" => array());
										$scenarios = getActivitiesByChapterId($chapterEdgeId);
										$totalScenarios=count($scenarios);
										if($totalScenarios > 0){
											
											foreach($scenarios as $keys=>$values){												
												$data="PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/chapter.xml";
												
												$scenarioArr['component']['activity'][] = array("name" => stripslashes(publishText($values->title)), "edgeId" => '', "data" => $data);
											
										
												
											}
										}
										
										$retObj->status=1;
										$retObj->course_code=$course_code;
										$retObj->chapComponent=$scenarioArr;
										$retObj->msg='SUCCESS';
										return $retObj;	
									}
								
								$scn++;
								$ch++;
                            
                        }
                              
						}
						
					}
				$i++;
			}
			
		}
		
		$retObj->status=1;
		$retObj->course_code=$course_code;
		$retObj->chapterArr=$scenarioArr;
		$retObj->msg='SUCCESS';
		return $retObj;	
	}else{
		$retObj->status=0;
		$retObj->course_code=$course_code;
		$retObj->chapterArr="";
		$retObj->msg='FAILER';
		return $retObj;	
	}
}

function getActivityCountByChapter($chapter_id){
	$con = createConnection();
	$query = "SELECT count(activity_id) as cnt FROM  tblx_activity WHERE chapter_edge_id = ? and is_activity='1'";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $chapter_id);
    $stmt->bind_result($cnt);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	if($cnt=='' || $cnt==null){return 0;}
	return $cnt;
}
function getCourseIdByCourseCode($course_code){
	$con = createConnection();
	$query = "SELECT course_id FROM course WHERE code = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $course_code);
    $stmt->bind_result($course_id);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	return $course_id;
}

function getActivityCountByTopic($topic_edge_id){
		$con = createConnection();
		$topicArr = array();
		$stmt = $con->prepare("SELECT count(activity_id) as cnt FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id JOIN tblx_activity ta ON ta.chapter_edge_id = gmt.edge_id
								WHERE gmt.is_active = 1 AND tree_node_parent = ? AND tnd.tree_node_category_id=2 AND ta.is_activity='1'");
		$stmt->bind_param("i",$topic_edge_id);
		$stmt->bind_result($cnt);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		return $cnt;
}


function getCourseDetailsCourseId($course_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT c.tree_node_id, c.code, c.title, c.description, c.duration, c.published_version, c.thumnailImg, c.level_id, gmt.edge_id, gmt.is_active FROM generic_mpre_tree gmt
							JOIN course c ON c.tree_node_id = gmt.tree_node_id 
							WHERE  c.course_id =?");
	$stmt->bind_param("i",$course_id);
	$stmt->execute();
	$stmt->bind_result($tree_node_id,$code, $title, $description, $duration, $published_version, $thumnailImg,$level_id, $edge_id, $is_active);
	$stmt->fetch();
	$stmt->close();
	
	$obj = new stdclass();
	$obj->tree_node_id = $tree_node_id;
	$obj->code = $code;
	$obj->edge_id = $edge_id;
	$obj->title = $title;
	$obj->description = $description;
	$obj->duration = $duration;
	$obj->published_version = $published_version;
	$obj->thumnailImg = $thumnailImg;
	$obj->level_id = $level_id;
	$obj->is_active = $is_active;
	return $obj;

}

function getCourseTypeByCourseCode($course_code){
	$con = createConnection();
	$query = "SELECT course_type FROM course WHERE code = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $course_code);
    $stmt->bind_result($course_type);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	return $course_type;
}

function getCatLogEdgeIdByCourseId($course_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT gmt.edge_id FROM generic_mpre_tree gmt
							JOIN course c ON c.tree_node_id = gmt.tree_node_id
							WHERE  c.course_id=?");
	$stmt->bind_param("i",$course_id);
	$stmt->execute();
	$stmt->bind_result($edge_id);
	$stmt->fetch();
	$stmt->close();
	
	$stmt = $con->prepare("select gmt.edge_id from generic_mpre_tree gmt
							join tree_node_def tnd on tnd.tree_node_id = gmt.tree_node_id
							where tree_node_super_root = ? and tnd.tree_node_category_id=16");
	$stmt->bind_param("i",$edge_id);
	$stmt->execute();
	$stmt->bind_result($catEdgeId);
	$stmt->fetch();
	$stmt->close();	
	return $catEdgeId;

}

function getTopicOrAssessmentByCourseId($course_id,$customTopic){
		$con = createConnection();
		$course_edge_id = getCourseEdgeIdByCourseId($course_id);
		//echo $course_edge_id;exit;
		
		$topicArr = array();

		$stmt = $con->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type,cm.zip_size,cm.duration,cm.thumnailImg,cm.is_survey,cm.ttl_ques_to_show,cm.passing_score,cm.no_of_skill_ques, 
            gmt.edge_id,tnd.tree_node_category_id,sequence_id
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = ? AND tnd.tree_node_category_id = 5 AND cm.assessment_type = 'pre'");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$assessment_type,$zip_size,$duration,$thumnailImg,$is_survey,$ttl_ques_to_show,$passing_score,$no_of_skill_ques,$edge_id,$tree_node_category_id,$sequence_id);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic1 = new stdClass();
			$topic1->tree_node_id = $tree_node_id;
			$topic1->name = $name;
			$topic1->description = $description;
			$topic1->assessment_type = $assessment_type;
            $topic1->zip_size = $zip_size;
            $topic1->duration = $duration;
            $topic1->thumnailImg = $thumnailImg;
            $topic1->is_survey = $is_survey;
            $topic1->ttl_ques_to_show = $ttl_ques_to_show;
            $topic1->passing_score = $passing_score;
            $topic1->no_of_skill_ques = $no_of_skill_ques;
			$topic1->edge_id = $edge_id;
			$topic1->tree_node_category_id = $tree_node_category_id;
			array_push($topicArr,$topic1);
		}
		$stmt->close();
		
		$sl="SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, cm.zip_size,cm.duration,cm.thumnailImg,cm.is_survey,cm.ttl_ques_to_show,cm.passing_score,cm.no_of_skill_ques,
            gmt.edge_id,tnd.tree_node_category_id, sequence_id,isLocked
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = $course_edge_id  AND tnd.tree_node_category_id IN(3,5) AND gmt.edge_id IN(".$customTopic.") AND (cm.assessment_type = 'mid' OR cm.assessment_type IS NULL) ORDER BY sequence_id )";
		//file_put_contents('test/checkQuery.txt',$sl);
   
		$whr.='WHERE gmt.is_active=1 AND cm.is_topic_active="1" AND tree_node_super_root=? AND tnd.tree_node_category_id IN(3,5)';
		 if($customTopic!=""){
			$whr.=' AND gmt.edge_id IN('.$customTopic.')'; 
		} 
		$whr.=' AND (cm.assessment_type="mid" OR cm.assessment_type IS NULL) ORDER BY sequence_id';

		
		$stmt = $con->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, cm.zip_size,cm.duration,cm.thumnailImg,cm.is_survey,cm.ttl_ques_to_show,cm.passing_score,cm.no_of_skill_ques,
            gmt.edge_id,tnd.tree_node_category_id, sequence_id,isLocked
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								$whr");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$assessment_type,$zip_size,$duration,$thumnailImg,$is_survey,$ttl_ques_to_show,$passing_score,$no_of_skill_ques,$edge_id,$tree_node_category_id,$sequence_id,$isLocked);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->tree_node_id = $tree_node_id;
			$topic2->name = $name;
			$topic2->description = $description;
			$topic2->assessment_type = $assessment_type;
            $topic2->zip_size = $zip_size;
			$topic2->duration = $duration;
            $topic2->thumnailImg = $thumnailImg;
            $topic2->is_survey = $is_survey;
			$topic2->ttl_ques_to_show = $ttl_ques_to_show;
            $topic2->passing_score = $passing_score;
            $topic2->no_of_skill_ques = $no_of_skill_ques;
			$topic2->edge_id = $edge_id;
			$topic2->tree_node_category_id = $tree_node_category_id;
			if($isLocked==1){$isLocked = true;}else{$isLocked = false;}
			$topic2->isLocked = $isLocked;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
		
		$topicArr3 = array();
		$stmt = $con->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type,cm.zip_size,cm.duration,cm.thumnailImg,cm.is_survey,cm.ttl_ques_to_show,cm.passing_score,cm.no_of_skill_ques, 
            gmt.edge_id,tnd.tree_node_category_id, sequence_id 
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = ? AND tnd.tree_node_category_id = 5 AND cm.assessment_type = 'post'");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$assessment_type,$zip_size,$duration,$thumnailImg,$is_survey,$ttl_ques_to_show,$passing_score,$no_of_skill_ques,$edge_id,$tree_node_category_id,$sequence_id);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic3 = new stdClass();
			$topic3->tree_node_id = $tree_node_id;
			$topic3->name = $name;
			$topic3->description = $description;
			$topic3->assessment_type = $assessment_type;
            $topic3->zip_size = $zip_size;
			$topic3->duration = $duration;
            $topic3->thumnailImg = $thumnailImg;
            $topic3->is_survey = $is_survey;
			$topic3->ttl_ques_to_show = $ttl_ques_to_show;
            $topic3->passing_score = $passing_score;
            $topic3->no_of_skill_ques = $no_of_skill_ques;
			$topic3->edge_id = $edge_id;
			$topic3->tree_node_category_id = $tree_node_category_id;
			array_push($topicArr,$topic3);
		}
		
		return $topicArr;

}

function getChapterByTopicEdgeId($topic_edge_id,$customChapter){
		$con = createConnection();
		$topicArr = array();
		
		$whr.='WHERE gmt.is_active=1 AND tree_node_parent=?';
			 if($customChapter!=""){
				$whr.=' AND gmt.edge_id IN('.$customChapter.')'; 
			} 
			$whr.=' AND tnd.tree_node_category_id=2 ORDER BY sequence_no';
				
			
		$stmt = $con->prepare("SELECT gmt.edge_id, cm.tree_node_id, cm.code,cm.title,cm.zip_size,cm.bgColor,cm.chapterSkill,cm.quesCount,cm.duration,cm.thumnailImg,cm.	sequence_no,cm.is_hide_resource FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
								$whr");
		$stmt->bind_param("i",$topic_edge_id);
		$stmt->execute();
		$stmt->bind_result($edge_id,$tree_node_id,$code,$title, $zip_size, $bg_color, $chapterSkill, $quesCount, $duration, $thumnailImg, $sequence_no, $is_hide_resource);
		while($stmt->fetch()) {
			$topic = new stdClass();
			$topic->edge_id = $edge_id;
			$topic->tree_node_id = $tree_node_id;
			$topic->name = $code;
			$topic->description = $title;
            $topic->zip_size = $zip_size;
            $topic->bg_color = $bg_color;
            $topic->chapterSkill = $chapterSkill;
            $topic->quesCount = $quesCount;
            $topic->duration = $duration;
            $topic->thumnailImg = $thumnailImg;
            $topic->sequence_no = $sequence_no;
            $topic->is_hide_resource = $is_hide_resource;
			array_push($topicArr,$topic);
		}
		$stmt->close();
		//echo "<pre>";print_r($topicArr);exit;
		return $topicArr;
}

function getChapterScenarios($edgeId){
		$con = createConnection();
		$topicArr = array();
		$stmt = $con->prepare("SELECT component_id, parent_edge_id, scenario_type, scenario_subtype, scenario_name 
								FROM tbl_component
								WHERE scenario_type != 'Activity' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$edgeId);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name);		
		while($stmt->fetch()) {
			$topic1 = new stdClass();
			$topic1->component_id = $component_id;
			$topic1->parent_edge_id = $parent_edge_id;
			$topic1->scenario_type = $scenario_type;
			$topic1->scenario_subtype = $scenario_subtype;
			$topic1->scenario_name = $scenario_name;
			array_push($topicArr,$topic1);
		}
		
		return $topicArr;
}

function getScenarioByChapterId($tree_node_id){
		$con = createConnection();
		$topicArr = array();
		 /*==========Concept===========*/
		$stmt = $con->prepare("SELECT component_id, parent_edge_id ,component_edge_id, scenario_type, scenario_subtype, scenario_name, thumbnailImg, component_description, sequence_no
								FROM tbl_component
								WHERE scenario_type = 'Concept' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$component_description,$sequence_no);	
		while($stmt->fetch()) {
			$topic1 = new stdClass();
			$topic1->component_id = $component_id;
			$topic1->parent_edge_id = $parent_edge_id;
			$topic1->component_edge_id = $component_edge_id;
			$topic1->scenario_type = $scenario_type;
			$topic1->scenario_subtype = $scenario_subtype;
			$topic1->scenario_name = $scenario_name;
			$topic1->thumbnailImg = $thumbnailImg;
			$topic1->component_description = $component_description;
			$topic1->sequence_no = $sequence_no;
			array_push($topicArr,$topic1);
		}
		$stmt->close();
		
		 /*==========Activity===========*/
		$stmt = $con->prepare("SELECT component_id, parent_edge_id, component_edge_id, scenario_type, scenario_subtype, scenario_name , thumbnailImg, sequence_no
								FROM tbl_component
								WHERE scenario_type = 'Activity' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$sequence_no);		
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->component_id = $component_id;
			$topic2->parent_edge_id = $parent_edge_id;
			$topic2->component_edge_id = $component_edge_id;
			$topic2->scenario_type = $scenario_type;
			$topic2->scenario_subtype = $scenario_subtype;
			$topic2->scenario_name = $scenario_name;
			$topic2->thumbnailImg = $thumbnailImg;
			$topic2->sequence_no = $sequence_no;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT component_id, parent_edge_id, component_edge_id, scenario_type, scenario_subtype, scenario_name, thumbnailImg, sequence_no
								FROM tbl_component
								WHERE scenario_subtype = 'Role-play' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$sequence_no);		
		while($stmt->fetch()) {
			$topic3 = new stdClass();
			$topic3->component_id = $component_id;
			$topic3->parent_edge_id = $parent_edge_id;
			$topic3->component_edge_id = $component_edge_id;
			$topic3->scenario_type = $scenario_type;
			$topic3->scenario_subtype = $scenario_subtype;
			$topic3->scenario_name = $scenario_name;
			$topic3->thumbnailImg = $thumbnailImg;
			$topic3->sequence_no = $sequence_no;
			array_push($topicArr,$topic3);
		}
		$stmt->close();
		
		
			 /*==========Quiz===========*/
		$stmt = $con->prepare("SELECT component_id, parent_edge_id, component_edge_id, scenario_type, scenario_subtype, scenario_name, thumbnailImg,scenario_description,scenario_duration,scenario_image,timeleft_warn,isQuesRand,isAnsRand,is_show_feedback,sequence_no,is_qt,writing_challenge,statement,tips_naming  
								FROM tbl_component
								WHERE scenario_subtype = 'Quiz' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$scenario_description,$scenario_duration,$scenario_image,$timeleft_warn,$isQuesRand,$isAnsRand,$is_show_feedback,$sequence_no,$is_qt,$writing_challenge,$statement,$tips_naming);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic4 = new stdClass();
			$topic4->component_id = $component_id;
			$topic4->parent_edge_id = $parent_edge_id;
			$topic4->component_edge_id = $component_edge_id;
			$topic4->scenario_type = $scenario_type;
			$topic4->scenario_subtype = $scenario_subtype;
			$topic4->scenario_name = $scenario_name;
			$topic4->thumbnailImg = $thumbnailImg;
			$topic4->scenario_description = $scenario_description;
			$topic4->scenario_duration = $scenario_duration;
			$topic4->scenario_image = $scenario_image;
			$topic4->timeleft_warn=$timeleft_warn;
			$topic4->isQuesRand=$isQuesRand;
			$topic4->isAnsRand=$isAnsRand;
			$topic4->is_show_feedback=$is_show_feedback;
			$topic4->sequence_no=$sequence_no;
			$topic4->is_qt=$is_qt;
			$topic4->is_wc=$writing_challenge;
			$topic4->statement=$statement;
			$topic4->tips_naming=$tips_naming;
			array_push($topicArr,$topic4);
		}
		$stmt->close();
		
		/*==========Conversation===========*/
		$stmt = $con->prepare("SELECT component_id, parent_edge_id,component_edge_id, scenario_type, scenario_subtype, scenario_name, thumbnailImg, sequence_no
								FROM tbl_component
								WHERE scenario_subtype = 'Conversation Practice' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$sequence_no);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic5 = new stdClass();
			$topic5->component_id = $component_id;
			$topic5->parent_edge_id = $parent_edge_id;
			$topic5->component_edge_id = $component_edge_id;
			$topic5->scenario_type = $scenario_type;
			$topic5->scenario_subtype = $scenario_subtype;
			$topic5->scenario_name = $scenario_name;
			$topic5->thumbnailImg = $thumbnailImg;
			$topic5->sequence_no = $sequence_no;
			array_push($topicArr,$topic5);
		}
		$stmt->close();
		
     /*=============Game=============*/
		$stmt = $con->prepare("SELECT component_id, parent_edge_id, component_edge_id, scenario_type, scenario_subtype, scenario_name, thumbnailImg, sequence_no,interactive_html,scenario_description,component_description
								FROM tbl_component
								WHERE scenario_subtype = 'Game' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$sequence_no,$interactive_html,$scenario_description,$component_description);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic6 = new stdClass();
			$topic6->component_id = $component_id;
			$topic6->parent_edge_id = $parent_edge_id;
			$topic6->component_edge_id = $component_edge_id;
			$topic6->scenario_type = $scenario_type;
			$topic6->scenario_subtype = $scenario_subtype;
			$topic6->scenario_name = $scenario_name;
			$topic6->thumbnailImg = $thumbnailImg;
			$topic6->sequence_no = $sequence_no;
			$topic6->interactive_html = $interactive_html;
			$topic6->scenario_description = $scenario_description;
			$topic6->component_description = $component_description;
			array_push($topicArr,$topic6);
		}
		$stmt->close();

      /*===========SpeedReading==========*/
		$stmt = $con->prepare("SELECT `component_id`,`parent_edge_id`,`component_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`thumbnailImg`,`scenario_description`,`scenario_duration`, wordCount, instruction, para, json,sequence_no FROM `tbl_component` WHERE `scenario_subtype`='SpeedReading' AND `parent_edge_id`=? AND `status`=1");
        $stmt->bind_param("i",$tree_node_id);
        $stmt->execute();
        $stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$scenario_description,$scenario_duration, 
                $wc, $ins, $para, $json,$sequence_no);
        $stmt->execute();		
        while($stmt->fetch()) {
            $topic7 = new stdClass();
            $topic7->component_id = $component_id;
            $topic7->parent_edge_id = $parent_edge_id;
            $topic7->component_edge_id = $component_edge_id;
            $topic7->scenario_type = $scenario_type;
            $topic7->scenario_subtype = $scenario_subtype;
            $topic7->scenario_name = $scenario_name;
            $topic7->thumbnailImg = $thumbnailImg;
            $topic7->scenario_description = $scenario_description;
            $topic7->scenario_duration = $scenario_duration;
            $topic7->sequence_no = $sequence_no;

            $topic7->wordCount = $wc;
            $topic7->instruction = $ins;
            $topic7->para = $para;
            $topic7->json = $json;

            array_push($topicArr,$topic7);
        }
		$stmt->close();
		
     /*==========SpeechRecognition===========*/
		$stmt = $con->prepare("SELECT `component_id`,`parent_edge_id`,`component_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`thumbnailImg`,`scenario_description`,`scenario_duration`, wordCount, instruction, para, json, file, sequence_no FROM `tbl_component` WHERE `scenario_subtype`='SpeechRecognition' AND `parent_edge_id`=? AND `status`=1");
        $stmt->bind_param("i",$tree_node_id);
        $stmt->execute();
        $stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$scenario_description,$scenario_duration, 
                $wc, $ins, $para, $json, $file, $sequence_no);
        $stmt->execute();		
        while($stmt->fetch()) {
            $topic8 = new stdClass();
            $topic8->component_id = $component_id;
            $topic8->parent_edge_id = $parent_edge_id;
            $topic8->component_edge_id = $component_edge_id;
            $topic8->scenario_type = $scenario_type;
            $topic8->scenario_subtype = $scenario_subtype;
            $topic8->scenario_name = $scenario_name;
            $topic8->thumbnailImg = $thumbnailImg;
            $topic8->scenario_description = $scenario_description;
            $topic8->scenario_duration = $scenario_duration;

            $topic8->wordCount = $wc;
            $topic8->instruction = $ins;
            $topic8->para = $para;
            $topic8->json = $json;
            $topic8->file = $file;
            $topic8->sequence_no = $sequence_no;

            array_push($topicArr,$topic8);
        }
           $stmt->close();
		  
		 /*==========Resources============*/
		$stmt = $con->prepare("SELECT `component_id`,`parent_edge_id`,`component_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`thumbnailImg`,`scenario_description`,instruction, file, sequence_no FROM `tbl_component` WHERE `scenario_subtype`='Resources' AND `parent_edge_id`=? AND `status`=1");
        $stmt->bind_param("i",$tree_node_id);
        $stmt->execute();
        $stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$scenario_description,
                $ins, $file, $sequence_no);
        $stmt->execute();		
        while($stmt->fetch()) {
            $topic9 = new stdClass();
            $topic9->component_id = $component_id;
            $topic9->parent_edge_id = $parent_edge_id;
            $topic9->component_edge_id = $component_edge_id;
            $topic9->scenario_type = $scenario_type;
            $topic9->scenario_subtype = $scenario_subtype;
            $topic9->scenario_name = $scenario_name;
            $topic9->thumbnailImg = $thumbnailImg;
            $topic9->scenario_description = $scenario_description;


            $topic9->instruction = $ins;
            $topic9->file = $file;
            $topic9->sequence_no = $sequence_no;

            array_push($topicArr,$topic9);
        }
       $stmt->close();
	   
 /*==========Conversation Video============*/
		$stmt = $con->prepare("SELECT `component_id`,`parent_edge_id`,`component_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`thumbnailImg`,`scenario_description`,instruction,sequence_no FROM `tbl_component` WHERE `scenario_subtype`='Conversation Video' AND `parent_edge_id`=? AND `status`=1");
        $stmt->bind_param("i",$tree_node_id);
        $stmt->execute();
        $stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$scenario_description,
                $ins,$sequence_no);
        $stmt->execute();		
        while($stmt->fetch()) {
            $topic10 = new stdClass();
            $topic10->component_id = $component_id;
            $topic10->parent_edge_id = $parent_edge_id;
            $topic10->component_edge_id = $component_edge_id;
            $topic10->scenario_type = $scenario_type;
            $topic10->scenario_subtype = $scenario_subtype;
            $topic10->scenario_name = $scenario_name;
            $topic10->thumbnailImg = $thumbnailImg;
            $topic10->scenario_description = $scenario_description;

            $topic10->instruction = $ins;
            $topic10->sequence_no = $sequence_no;
            array_push($topicArr,$topic10);
        }
       $stmt->close();
	   
	   /*========== Speech Role-play===========*/
		$stmt = $con->prepare("SELECT component_id, parent_edge_id, component_edge_id, scenario_type, scenario_subtype, scenario_name, thumbnailImg, sequence_no,rp_media,rp_transcript
								FROM tbl_component
								WHERE scenario_subtype = 'Speech Role-play' AND parent_edge_id = ? AND status = 1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$parent_edge_id,$component_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$thumbnailImg,$sequence_no,$rp_media,$rp_transcript);		
		while($stmt->fetch()) {
			$topic3 = new stdClass();
			$topic3->component_id = $component_id;
			$topic3->parent_edge_id = $parent_edge_id;
			$topic3->component_edge_id = $component_edge_id;
			$topic3->scenario_type = $scenario_type;
			$topic3->scenario_subtype = $scenario_subtype;
			$topic3->scenario_name = $scenario_name;
			$topic3->thumbnailImg = $thumbnailImg;
			$topic3->sequence_no = $sequence_no;
			$topic3->rp_media = $rp_media;
			$topic3->rp_transcript = $rp_transcript;
			array_push($topicArr,$topic3);
		}
		$stmt->close();
		closeConnection($con);
		return $topicArr;
	   
		
}

function getComponentEdgeIdByChapterEdgeId($chapterEdgeId,$category_type){
		$con = createConnection();
		$topicArr = array();
		$stmt = $con->prepare("SELECT gmt.edge_id FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								WHERE tnd.tree_node_category_id = ?
								AND gmt.tree_node_parent = ? AND gmt.is_active = 1");
		$stmt->bind_param("ii",$category_type,$chapterEdgeId);
		$stmt->execute();
		$stmt->bind_result($edge_id);
		$stmt->fetch();
		return $edge_id;
}

/* Get component xml file path */
function getComponentXmlPath($con,$comp_edge_id,$vocab_file=0,$json_file=0){
	
	if($json_file==1){
		$stmt = $con->prepare("SELECT json_file FROM tbl_component_xml_path WHERE comp_edge_id = ? AND vocab_file = ?");
		$stmt->bind_param("is",$comp_edge_id,$vocab_file);
		$stmt->execute();
		$stmt->bind_result($file_name);
		$stmt->fetch();
		$stmt->close();

		if(!empty($file_name)){
					
			return $file_name;
		}
	}else{
		$stmt = $con->prepare("SELECT file_name FROM tbl_component_xml_path WHERE comp_edge_id = ? AND vocab_file = ?");
		$stmt->bind_param("is",$comp_edge_id,$vocab_file);
		$stmt->execute();
		$stmt->bind_result($file_name);
		$stmt->fetch();
		$stmt->close();

		if(!empty($file_name)){
					
			return $file_name;
		}

	}
	return;	
	
}
 
// Check user active status
function checkUserStatus($con,$user_id){
	
	$query = "SELECT a.is_active from user_credential a, user b where a.user_id='".$user_id."' and a.user_id=b.user_id";
	$stmt = $con->prepare($query);
	$stmt->bind_result($is_active);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
  
	if(isset($is_active) && $is_active===1){
		return 'yes';
	}
	return 'no';	
}
 
/*function checkClientStatus($param)
{
	$data = new stdClass();
	if($param->class_name=='skilful')
	{
		$data->policy_link = "https://iltnew.adurox.com/emp-ilt/live/resource/skilful/terms.php";
	}
	else if($param->class_name=='englishEdge')
	{

		$data->policy_link = "https://iltnew.adurox.com/emp-ilt/live/resource/englishEdge/termsandcondition.html";
	}
	else
	{
		//client is active / unblocked
		//return 'yes';
		$data->policy_link = "https://iltnew.adurox.com/emp-ilt/live/resource/englishEdge/termsandcondition.html";
	}
	return $data;
}*/

function checkClientStatus($param)
{
	if($param->class_name=='skilful')
	{
		//client is inactive / blocked
		return 'no';
	}
	else if($param->class_name=='englishEdge')
	{
		//client is inactive / blocked
		return 'yes';
	}
	else
	{
		return 'yes';
	}
}

function tokenValidate($con,$token) {
    /* Check if the token exists */
	if($token!="xxxxxxxxxxxxxxxxxxxxxxxxxxxx")
	{
	
	$stmt = $con->prepare("SELECT user_id from api_session where session_id=? and valid_upto > NOW()");
    if($stmt) {
        $stmt->bind_param("s",$token);
        if($stmt->execute()) {
            $stmt->bind_result($user_id);
            if($stmt->fetch()) {
                $stmt->close();
               
				$updatest = $con->prepare("update api_session set valid_upto=DATE_ADD(NOW(), INTERVAL + 4 HOUR) where session_id=?");
                $updatest->bind_param("s",$token);
                $updatest->execute();
                $updatest->close();
				
                return $user_id;
            }
            $stmt->close();
        } 
    }
	
	}
	

    return -1;
}
function getLicenseDeviceCount($con, $package_codes){
	global $serviceURL;
	//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
	$request = curl_init($serviceURL);
	curl_setopt($request, CURLOPT_POST, true);
	curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'getCurrentPackageDtl', 'package_codes' => $package_codes, 'sourse' => 'englishEdge'));
	curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
	$res = curl_exec($request);
	curl_close($request);
	$res = json_decode($res);
	return $res;
}
function count_days($start_date, $end_date){
   
	$d1 = new DateTime($start_date);
	$d2 = new DateTime($end_date);
	$difference = $d1->diff($d2);
	return $difference->format('%r%a');

}

function updateClass($con,$jsonArr){
	foreach($jsonArr as $key => $value){
			$edgeIdArr = array();
			$stmt = $con->prepare("SELECT gmt.edge_id, gmt.is_active, c.published_version FROM generic_mpre_tree gmt
									JOIN course c ON gmt.tree_node_id = c.tree_node_id
									WHERE c.code = '".$value->course_code."'");						
			$stmt->execute();
			$stmt->bind_result($cedge_id, $is_active, $published_version);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
			if($is_active){
				if($published_version > $value->version){
					$stmt = $con->prepare("SELECT version_id, edge_id, action_taken, entity  
										FROM tbl_publish_history 
										WHERE id IN(SELECT MAX(id) FROM tbl_publish_history WHERE course_code = '".$value->course_code."' GROUP BY edge_id) 
										AND version_id > ".$value->version. " AND version_id <= ".
										$published_version. " AND course_code = '".$value->course_code."' AND entity IN('chapter','assessment')");			
					$stmt->execute();
					$stmt->bind_result($version_id, $edge_id, $action_taken, $entity);
					$stmt->execute();
					
					while($stmt->fetch()) {
						$bcm = new stdClass();
						
						$bcm->edgeId = $edge_id;
						$bcm->action = $action_taken;
						array_push($edgeIdArr,$bcm);
					}
					$stmt->close();
					$course_version = "";
					$stmt = $con->prepare("SELECT version_id course_version, action_taken
											FROM tbl_publish_history 
											WHERE id IN(SELECT MAX(id) FROM tbl_publish_history WHERE course_code = '".$value->course_code."' GROUP BY edge_id)
											AND version_id > ".$value->version. " AND version_id <= ".$published_version. 
											" AND course_code = '".$value->course_code."' AND entity = 'course'");						
					$stmt->execute();
					$stmt->bind_result($course_version, $action);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
					
					$action = '2';
					$courseArr = array('course_code' => $value->course_code, 'version' => $published_version,'edgeId' => $cedge_id, 'action' => $action);				
					$chapterArr = array('chapters'=>$edgeIdArr);
					$finalArr[] = array_merge($courseArr,$chapterArr);
				
				}else{
					continue;
				}
			
		}else{

			continue;
		}
	}
	return array_values($finalArr);
}

function getCompletion($con,$user_id,$param){
	$product_id = $param->product_id;
	$center_id = $param->center_id;
	$batch_id = $param->batch_id;
	$course_code = $param->course_code;
	
	// add custom topic
	if($product_id!='' && $center_id!='' && $batch_id!='' && $course_code!=''){
		$stmt = $con->prepare("select topic,chapter from tblx_product_configuration  where product_id=? and institute_id=? and batch_id=?");
		$stmt->bind_param("iii",$product_id,$center_id,$batch_id);
		$stmt->execute();
		$stmt->bind_result($topic,$chapter);
		$stmt->fetch();
		$stmt->close();
		$customTopic=$topic;
		$customChapter=$chapter;
		
	}else{
		$customTopic='';
		$customChapter='';
	}
	
	$courseID = getCourseIdByCourseCode($param->course_code);
	if($courseID){
			
			$topics = getTopicOrAssessmentByCourseId($courseID,$customTopic);
			$totalTopics=count($topics);
			//file_put_contents('test/check5.txt',$totalTopics);
	
			if($totalTopics>=0){
				$topic_cmpt_cnt = $topic_not_cmpt_cnt = 0;
				
				foreach($topics as $key => $value){
					
					$ch_cmpt_cnt = $ch_not_cmpt_cnt = 0;
					if($value->assessment_type ==""){
					
						$chapters=getChapterByTopicEdgeId($value->edge_id,$customChapter);
						
						$totalChapters=count($chapters);
						$chapter_arr = array();
						if($totalChapters>=0){
							
							foreach($chapters as $keyc=>$valuec){
								
								$completion = "na";
								$stmt = $con->prepare("select completion from tblx_component_completion where user_id=? and component_edge_id=? and course_code=? and license_key=?");
								$stmt->bind_param("iiss",$user_id,$valuec->edge_id,$param->course_code,$param->package_code);
								$stmt->execute();
								$stmt->bind_result($completion);
								$stmt->fetch();
								$stmt->close();
								
								if($completion=='c'){
									$ch_cmpt_cnt++;
								}elseif($completion=='nc'){
									$ch_not_cmpt_cnt++;
								}
								
								$chapter_arr[] = array('edge_id'=>$valuec->edge_id,'status'=>$completion);
								
							}
						
							if($ch_cmpt_cnt==$totalChapters){
								
								$topic_completion = 'c';
								$topic_cmpt_cnt++;
							
							}
							elseif($ch_not_cmpt_cnt>0 || $ch_cmpt_cnt>0){
								
								$topic_completion = 'nc';
								$topic_not_cmpt_cnt++;
							
							}else{
								
								$topic_completion = 'na';
							}
							
						}
						
						$topic_arr[] = array('edge_id'=>$value->edge_id,'status'=>$topic_completion,'chapter_arr'=>$chapter_arr);
					
					}else{
					
						$asscompletion = 'na';
						$stmt = $con->prepare("select completion from tblx_component_completion where user_id=? and component_edge_id=? and course_code=? and license_key=?");
						$stmt->bind_param("iiss",$user_id,$value->edge_id,$param->course_code,$param->package_code);
						$stmt->execute();
						$stmt->bind_result($asscompletion);
						$stmt->fetch();
						$stmt->close();
						
						if($asscompletion=='c'){
							
							$topic_cmpt_cnt++;
						}
						elseif($asscompletion=='nc'){
							
							$topic_not_cmpt_cnt++;
						}
						
						$ass_arr[] = array('edge_id'=>$value->edge_id,'status'=>$asscompletion);
						
					}
			
				}
			
			
				if($topic_cmpt_cnt==$totalTopics){
								
					$course_completion = 'c';
				}
				elseif($topic_not_cmpt_cnt>0 || $topic_cmpt_cnt>0){
					
					$course_completion = 'nc';
				}else{
					
					$course_completion = 'na';
				}
			
			
		}
	
	}
	$completionArr = array('status'=>$course_completion,'course_code'=>$param->course_code,'topic_arr'=>$topic_arr,'ass_arr'=>$ass_arr);
	return $completionArr; 
}

function getCourseEdgeIdByCourseId($course_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT gmt.edge_id FROM generic_mpre_tree gmt
							JOIN course c ON c.tree_node_id = gmt.tree_node_id
							WHERE  c.course_id=?");
	$stmt->bind_param("i",$course_id);
	$stmt->execute();
	$stmt->bind_result($edge_id);
	$stmt->fetch();
	$stmt->close();
	return $edge_id;
	
}

//Get Assessment Skills  
function getAssessmentSkills($con,$edge_id){
	
	$qCount = 0;
	$stmt = $con->prepare("SELECT distinct tr.compentency_id FROM tbl_questions tq JOIN tbl_questions_rubric tr ON tq.id=tr.question_id WHERE tq.parent_edge_id = ? AND tq.status = 1");
	$stmt->bind_param("i",$edge_id);
	$stmt->execute();
	$stmt->bind_result($compentency_id);
	
	 $skillArr=array();
	 while($stmt->fetch()) {
			$obj = new stdclass();
			$obj->skill_id = $compentency_id;
			$skillArr[]=$obj;
	 }
	$stmt->close();
	return $skillArr;
}

//Get Assessment Questions  
function getAssessmentQuesCount($con,$edge_id){
	
	$qCount = 0;
	$stmt = $con->prepare("SELECT count('id') qCount FROM tbl_questions WHERE parent_edge_id = ? AND status = 1");
	$stmt->bind_param("i",$edge_id);
	$stmt->execute();
	$stmt->bind_result($qCount);
	$stmt->fetch();
	$stmt->close();
    
	return $qCount;
}

//Get Visited Course
function getUserVisitedCourse($con,$user_id){
	//$con = createConnection();
	$stmt = $con->prepare("select user_id, user_start_level,user_current_level from tbl_user_visited_course where user_id=?");
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($user_row_id,$user_start_level, $user_current_level);
	$stmt->fetch();
	$stmt->close();
	$obj = new stdclass();
	if(!isset($user_row_id) || $user_row_id==null || $user_row_id =="")
	{
		return $obj;
	}
	else
	{
		//get current level description and mapto
		
		$obj->user_id = $user_id;
		$obj->score = $score;
		$obj->user_start_level = $user_start_level;
		$obj->user_current_level = $user_current_level;
		
		
		return $obj;
	}
}

function getLevelTxt($con,$level){

	$stmt = $con->prepare("select level_description, level_cefr_map from tblx_standards_levels where level_text=".$level);
	$stmt->execute();
	$stmt->bind_result($description_text,$level_cefr_map);
	$stmt->fetch();
	$stmt->close();
	
	$obj = new stdclass();
	$obj->description_text = $description_text;
	$obj->level_cefr_map = $level_cefr_map;
	
	return $obj;	
}

//For User Course SignUp
function userCourseSignUp($con,$user_id,$course_code) {

	$check_course = courseStatus($con,$course_code, $user_id, $license_key='');
    if($check_course->status){
		$query = "SELECT COUNT(*) from user_course_signup where user_id=? and course_code=?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("is", $user_id, $course_code);
		$stmt->bind_result($num_bookings);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		if($num_bookings == 0) {
			$stmt = $con->prepare("INSERT INTO user_course_signup(user_id,course_code,date_of_expiry,reg_date) values(?,?,DATE_ADD(NOW(), INTERVAL +3 YEAR),NOW())");
			$stmt->bind_param("is",$user_id,$course_code);
			$stmt->execute();
			$stmt->close();
		}
		////Code for MixPanel/////
			$post_data = array();

			$parentObj = new stdClass();
			$parentObj->eventName = 'CourseStart';
			$parentObj->clientCode = 'wiley';
			
			$stmt = $con->prepare("SELECT a.loginid,first_name,middle_name,last_name from user_credential a join  user b on a.user_id=b.user_id where b.user_id=?");
			$stmt->bind_param("i",$user_id); 
			$stmt->execute(); 
			$stmt->bind_result($loginid,$fname,$mname,$lname);
            $stmt->fetch();
            $stmt->close();
			
			$stmt = $con->prepare("SELECT course_id,title,course_type from course where code=?");
			$stmt->bind_param("s",$course_code);
			$stmt->execute(); 
			$stmt->bind_result($course_id,$title,$course_type);
            $stmt->fetch();
            $stmt->close();
			if($course_type=='1'){$course_type ='ILT'; }else{
				$course_type ='WBT';
			}
			
			$data = new stdClass();
			$data->user_id = $user_id;
			$data->first_name = $fname;
			$data->last_name = $lname;
			$data->loginid = $loginid;
			$data->course_id = $course_id;
			$data->course_code = $course_code;
			$data->course_name = $title;
			$data->course_type = $course_type;
			$data->timestamp = date('Y-m-d H:i:s');
			$data->client_code = 'CommonApp';
			array_push($post_data,$data);
			$parentObj->userProps=$post_data;
			$MTResponse=sendToMixPanel($parentObj);
		return 1;
	}else{
		return 0;
	}	
}
function courseStatus($con,$course_code,$user_id,$licence_key){
	
	if($licence_key == ""){
		//file_put_contents('test/hiichecklicense.txt','giiii');
		$is_active = 0;
		
		$query = "SELECT gmt.is_active FROM generic_mpre_tree gmt 
					JOIN course c ON c.tree_node_id = gmt.tree_node_id
					WHERE BINARY c.code = ? AND c.published_version > 0";
		$stmt = $con->prepare($query);
		$stmt->bind_param("s", $course_code);
		$stmt->bind_result($is_active);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();

		if ($con->error) {
			$retObj->status="FAILURE";
			$retObj->reason=$con->error;
		} else {
			$retObj->status=$is_active;
			$retObj->course_code=$course_code;
			
		}
		return $retObj;
	}else{
			
		$query = "SELECT gmt.is_active FROM generic_mpre_tree gmt 
					JOIN course c ON c.tree_node_id = gmt.tree_node_id
					WHERE BINARY c.code = ? AND c.published_version > 0";
		$stmt = $con->prepare($query);
		$stmt->bind_param("s", $course_code);
		$stmt->bind_result($is_active);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
	
		
		if($is_active==0 || $is_active == ""){

				$is_active = 0;
				$retObj->status=0;
				$retObj->course_code=$course_code;
				$retObj->msg='Invalid course code.';
				return $retObj;
		}
		
		$query = "SELECT cg.client_id FROM course_group cg JOIN course_codes cc ON cc.group_code = cg.code WHERE cc.unique_code = ?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("s", $licence_key);
		$stmt->bind_result($client_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();

		$query = "SELECT c.client_id FROM course c WHERE c.code = ?";
		$stmt = $con->prepare($query);
		$stmt->bind_param("s", $course_code);
		$stmt->bind_result($clientId);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		if(($client_id == $clientId) && ($client_id == 130397)){
			$query = "select loginid from user_credential where user_id=?";
			$stmt = $con->prepare($query);
			$stmt->bind_param("i",$user_id);
			$stmt->bind_result($uname);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'http://goodluckpublishers.com/index.php?route=product/androidapi',
			CURLOPT_USERAGENT => 'Codular Sample cURL Request',
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => array(
			email  => $uname,
			model => $course_code,
			_token => 'krSj7qjWDx0jPnHLb2A9E6nNIGkzlm7NZe0kESFvLpmakqMFM0'
			)
			));
			//print_r($resp);
			$resp = curl_exec($curl);			
			curl_close($curl);
			
			$resp = json_decode($resp, true);

			$api_res = $resp['status'];

			if(!$api_res){
				////Stauts 2 is sent in case of goodluck, prompting the user to buy the course.
				$retObj->status=2;
				$retObj->course_code=$course_code;
				$retObj->msg='You are not authorized to access this course. You need to buy this course by clicking on Buy Button.';
			}else{
				$retObj->status=1;
				$retObj->course_code=$course_code;
				$retObj->msg='SUCCESS';
			}

		}else{
			$retObj->status=0;
			$retObj->course_code=$course_code;
			$retObj->msg='Invalid course code.';
		
		}
	}
	return $retObj;
}

//Component Completion Set and Get WBT
function setComponentCompletion($con,$user_id,$param){
	 //file_put_contents("test/set1.txt",$user_id." ".$param->batch_id." ".$param->course_code." ".$param->edge_id);
	$sql = "SELECT center_id,batch_id from tblx_batch_user_map WHERE user_id=?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($center_id,$batch_id);
	$stmt->fetch();
	$stmt->close(); 
	
	$stmt = $con->prepare("select id, completion from tblx_component_completion where user_id=? AND component_edge_id=? AND batch_id=? AND center_id=? AND license_key=?");
	$stmt->bind_param("iiiis",$user_id,$param->edge_id,$batch_id,$center_id,$param->package_code);	
	$stmt->execute();
	$stmt->bind_result($id,$completion);
	$stmt->fetch();
	$stmt->close();
	
	if(empty($id)){
		$complete_per = isset($param->complete_per)?$param->complete_per:0;
		if($param->completion=='c'){
			$complete_per =100;
		}elseif($param->completion=='nc'){
			$complete_per =50;
		}elseif($param->completion=='na'){
			$complete_per =0;
		}
		
		$query="INSERT INTO tblx_component_completion(user_id,batch_id,center_id,course_code,	component_edge_id,completion,license_key,created_date,complete_per) values(?,?,?,?,?,?,?,NOW(),?)";
		$stmt= $con->prepare($query);
		$stmt->bind_param("iiisissi",$user_id,$batch_id,$center_id,$param->course_code,$param->edge_id,$param->completion,$param->package_code,$complete_per);
		$stmt->execute();
		$stmt->close();

	}else{
	     $complete_per = isset($param->complete_per)?$param->complete_per:0;
		
	     if($completion!='c'){
		  if($param->completion=='c'){
			 $complete_per =100;
			}elseif($param->completion=='nc'){
				$complete_per =50;
			}elseif($param->completion=='na'){
				$complete_per =0;
			}
			$stmt= $con->prepare("update tblx_component_completion set completion=?,complete_per=? where id=?");
			$stmt->bind_param("sii",$param->completion,$complete_per,$id);
			$stmt->execute();
		    $stmt->close();

		}
	}

}

function getComponentCompletion($con,$user_id,$param){

	$stmt = $con->prepare("select course_code,component_edge_id,completion,license_key from tblx_component_completion where user_id=".$user_id);
	$stmt->execute();
	$stmt->bind_result($course_code,$component_edge_id,$completion,$license_key);
	
	 $completionArr=array();
	 while($stmt->fetch()) {
		 if(!empty($component_edge_id)){
			$obj = new stdclass();
			$obj->course_code = $course_code;
			$obj->edge_id = $component_edge_id;
			$obj->completion = $completion;
			$obj->package_code = $license_key;
			$completionArr[]=$obj;
		}
	 }
	$stmt->close();
	return $completionArr; 

}


//Component Completion Set ILT
function setComponentCompletionIlt($con,$user_id,$param){
	
	$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->fetch();
	$stmt->close(); 
	
	$stmt = $con->prepare("select id, completion from tblx_component_completion where user_id=? AND component_edge_id=? AND batch_id=? AND center_id=? AND license_key=?");
	$stmt->bind_param("iiiis",$user_id,$param->edge_id,$param->batch_id,$center_id,$param->package_code);	
	$stmt->execute();
	$stmt->bind_result($id,$completion);
	$stmt->fetch();
	$stmt->close();
	
	if(empty($id)){
		
		$query="INSERT INTO tblx_component_completion(user_id,batch_id,center_id,course_code,	component_edge_id,completion,license_key,created_date) values(?,?,?,?,?,?,?,NOW())";
		$stmt= $con->prepare($query);
		$stmt->bind_param("iiisiss",$user_id,$param->batch_id,$center_id,$param->course_code,$param->edge_id,$param->completion,$param->package_code);
		$stmt->execute();
		$stmt->close();
		
		if($param->completion=='c'){
			updateTSCompletion($con,$ts_id, 100);
		}elseif($param->completion=='nc'){
			updateTSCompletion($con,$ts_id, 50);
		}elseif($param->completion=='na'){
			updateTSCompletion($con,$ts_id, 0);
		}
		
		//Check for topic completion
		$stmt = $con->prepare("select id, completion from tblx_component_completion where user_id=? AND component_edge_id=? AND batch_id=? AND center_id=? AND license_key=?");
		$stmt->bind_param("iiiis",$user_id,$param->topic_edge_id,$param->batch_id,$center_id,$param->package_code);	
		$stmt->execute();
		$stmt->bind_result($tid,$completion_topic);
		$stmt->fetch();
		$stmt->close();
		if(empty($tid)){
			if($param->completion=='c'){
				$topic_completion = chkTopicCompletion($con,$param->topic_edge_id,$user_id,$param->batch_id,$center_id,$param->package_code);
			}
			else{
				$topic_completion='nc';
				}
			
			$query="INSERT INTO tblx_component_completion(user_id,batch_id,center_id,course_code,	component_edge_id,completion,license_key,created_date) values(?,?,?,?,?,?,?,NOW())";
			$stmt= $con->prepare($query);
			$stmt->bind_param("iiisiss",$user_id,$param->batch_id,$center_id,$param->course_code,$param->topic_edge_id,$topic_completion,$param->package_code);
			$stmt->execute();
			$stmt->close();
			
		}else{
			if($completion_topic!='c'){
				
				if($param->completion=='c'){
				
					$topic_completion = chkTopicCompletion($con,$param->topic_edge_id,$user_id,$param->batch_id,$center_id,$param->package_code);
				}else{
					$topic_completion='nc';
				}	
					
				$stmt= $con->prepare("update tblx_component_completion set completion=? where id=?");
				$stmt->bind_param("si",$topic_completion,$tid);
				$stmt->execute();
				$stmt->close();
			}
		}
		
	}else{
	
	if($completion!='c'){
			$stmt= $con->prepare("update tblx_component_completion set completion=? where id=?");
			$stmt->bind_param("si",$param->completion,$id);
			$stmt->execute();
		    $stmt->close();
			
			if($param->completion=='nc'){
				updateTSCompletion($con,$ts_id, 50);
			}elseif($param->completion=='na'){
				updateTSCompletion($con,$ts_id, 0);
			}
		}
	
	//Check for topic completion
		$stmt = $con->prepare("select id, completion from tblx_component_completion where user_id=? AND component_edge_id=? AND batch_id=? AND center_id=? AND license_key=?");
		$stmt->bind_param("iiiis",$user_id,$param->topic_edge_id,$param->batch_id,$center_id,$param->package_code);	
		$stmt->execute();
		$stmt->bind_result($tid,$completion_topic);
		$stmt->fetch();
		$stmt->close();
		if(empty($tid)){
			if($param->completion=='c'){
				$topic_completion = chkTopicCompletion($con,$param->topic_edge_id,$user_id,$param->batch_id,$center_id,$param->package_code);
			}
			else{
				$topic_completion='nc';
				}
			
			$query="INSERT INTO tblx_component_completion(user_id,batch_id,center_id,course_code,	component_edge_id,completion,license_key,created_date) values(?,?,?,?,?,?,?,NOW())";
			$stmt= $con->prepare($query);
			$stmt->bind_param("iiisiss",$user_id,$param->batch_id,$center_id,$param->course_code,$param->topic_edge_id,$topic_completion,$param->package_code);
			$stmt->execute();
			$stmt->close();
			
		}else{
			if($completion_topic!='c'){
				
				if($param->completion=='c'){
				
					$topic_completion = chkTopicCompletion($con,$param->topic_edge_id,$user_id,$param->batch_id,$center_id,$param->package_code);
				}else{
					$topic_completion='nc';
				}	
					
				$stmt= $con->prepare("update tblx_component_completion set completion=? where id=?");
				$stmt->bind_param("si",$topic_completion,$tid);
				$stmt->execute();
				$stmt->close();
			}
		}
	
	
	
	
	}
}

function chkTopicCompletion($con,$topic_edge_id,$user_id,$batch_id,$center_id,$package_code){
	
	$singleChapterArr = getChpaterByTopicEdgeId($con,$topic_edge_id);
	$chapter_count = count($singleChapterArr);
	$number_of_completed_chapter=0;
	foreach($singleChapterArr as $chapterArrKey=>$chapterArrVal){
		
		$chapterEdgeId = $chapterArrVal->edge_id;
		$chapter_time=0;
		$stmt = $con->prepare("SELECT completion_status from tblx_component_completion where user_id=? AND component_edge_id=? AND batch_id=? AND center_id=? AND license_key=?");
		$stmt->bind_param("iiiis",$user_id,$chapterEdgeId,$batch_id,$center_id,$package_code);	
		$stmt->execute();
		$stmt->bind_result($completion_status);
		$stmt->fetch();
		$stmt->close();
		if($completion_status=='c'){
			$number_of_completed_chapter++;
		}
	}
	if($number_of_completed_chapter==$chapter_count){
			$topic_completion = 'c';
		}
	else{
		$topic_completion='nc';
		}
	
	return $topic_completion;
	
}


//Save time for teacher ilt

function trackChapterTime($con,$user_id,$batch_id,$edge_id,$start_date_ms, $end_date_ms,$course_code, $unique_code, $platform) {
	
	$second_time = $start_date_ms / 1000;
	$start_datetime = date('Y-m-d H:i:s',($start_date_ms/1000));
	if($end_date_ms > $start_date_ms){    
		$second_spent = ($end_date_ms - $start_date_ms) / 1000;
	}else{
		$second_spent = 60;
	}
	
    $query = "select  center_id from tblx_batch_user_map where user_id=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i",$user_id);
    $stmt->bind_result($center_id); 
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	
	$sessionType = "CH";	
	
	$second_spent = intval($second_spent);
    $second_time  = intval($second_time);
	if($edge_id){
		
		$dateTime = date('Y-m-d H:i:s');

		$query = "INSERT INTO user_session_tracking(user_id,session_id,session_type,center_id,user_role_id,actual_seconds,track_datettime,batch_id,course_code,platform,unique_code, start_datetime) values(?,?,?,?,2,?,?,?,?,?,?,?)";
		
		$stmt1 = $con->prepare($query);
		$stmt1->bind_param("iisiisissss",$user_id,$edge_id,$sessionType,$center_id,$second_spent,$dateTime,$batch_id,$course_code,$platform,$unique_code,$start_datetime);
		$stmt1->execute();
		$stmt1->close();
	
	}
    if ($con->error) {
        $retObj->status="FAILURE";
        $retObj->reason=$con->error;
    } else {
        $retObj->status="SUCCESS";
    }
	
}

//Get User Coins
function getUserCoins($con,$user_id,$edge_id,$edge_id_category,$component_type,$package_code){ 
	
	if($edge_id_category=='component'){
	
		if($component_type==3){
			
			$stmt = $con->prepare("SELECT count(tq.id) as qCount from  tbl_component tc JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
			where tc.component_edge_id=?");
			$stmt->bind_param("i",$edge_id);
			$stmt->execute();
			$stmt->bind_result($qCount);
			$stmt->fetch();
			$stmt->close();
			
			$quiz_coins = !empty($qCount)?$qCount:0;
			
			$total_coins = $quiz_coins;

		}
		if($component_type==7)
		{
					
			$stmt = $con->prepare("SELECT count(tv.id) as coin_count from tbl_vocabulary tv JOIN tbl_component tc on tv.parent_edge_id = tc.parent_edge_id where tc.component_edge_id = ? and tc.status = 1");
			$stmt->bind_param("i",$edge_id);
			$stmt->execute();
			$stmt->bind_result($total_vocab_coin);
			$stmt->fetch();
			$stmt->close();	
			
			$vocab_coins = !empty($total_vocab_coin)?$total_vocab_coin:0;
			$total_coins = $vocab_coins;
		
		}														
			
		if($component_type==2)
		{			
			
			$rolePlayEdgeId = $values->component_edge_id;
		
			$stmt = $con->prepare("SELECT count(tcd.id) as videoCount from  tbl_component_data tcd JOIN tbl_component tc on tcd.component_id = tc.component_id  where tc.component_edge_id = ? and tcd.scenario_answer_media_file!=''");
			$stmt->bind_param("i",$edge_id);
			$stmt->execute();
			$stmt->bind_result($videoCount);
			$stmt->fetch();
			$stmt->close();
			
			$rp_coins =!empty($videoCount)?$videoCount:0;
			$total_coins = $rp_coins;

		}
		
		if($component_type==6){
		
			$compEdgeIdQ = $values->component_edge_id;
			$stmt = $con->prepare("SELECT count(tq.id) as qCount from  tbl_component tc JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
			where tc.component_edge_id = ?");
			$stmt->bind_param("i",$edge_id);
			$stmt->execute();
			$stmt->bind_result($srQCount);
			$stmt->fetch();
			$stmt->close();
			
			$speedreading_coins = !empty($srQCount)?$srQCount:0;
			$total_coins = $speedreading_coins;
			
		}
			
		//component earned coin
		$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND component_edge_id = ? AND component_type = ? GROUP BY user_id,component_edge_id,component_type");
		$stmt->bind_param("iii",$user_id,$edge_id,$component_type);
		$stmt->execute();
		$stmt->bind_result($total_earned_coins);
		$stmt->fetch();
		$stmt->close();		
		
		$total_earned_coins = !empty($total_earned_coins)?$total_earned_coins:0;

		//component earned coin with detail
		$stmt = $con->prepare("SELECT course_code,topic_edge_id,chapter_edge_id,component_edge_id,component_data, component_type,user_coins from tblx_user_coins where user_id = ? AND component_edge_id = ? AND component_type = ?");
		$stmt->bind_param("iii",$user_id,$edge_id,$component_type);
		$stmt->execute();
		$stmt->bind_result($course_code,$topic_edge_id,$chapter_edge_id,$component_edge_id,$component_data, $component_type,$user_coins);
		$stmt->execute();
		$coins_detail_arr = array();
		while($stmt->fetch()) {
			
			$obj = new stdclass();
			$obj->course_code= $course_code;
			$obj->topic_edge_id= $topic_edge_id;
			$obj->chapter_edge_id= $chapter_edge_id;
			$obj->component_edge_id= $component_edge_id;
			$obj->component_data= $component_data;
			$obj->component_type= $component_type;
			$obj->user_coins = $user_coins;
			array_push($coins_detail_arr,$obj);
		}
		$stmt->close();	

		$retObj->total_earned_coins = $total_earned_coins;	
		$retObj->total_coins_avail = $total_coins;	
		$retObj->coins_detail = $coins_detail_arr;	
		$retObj->user_id = $user_id;	
		$retObj->edgeId = $edge_id;	
		
		return $retObj;
		
	}
	elseif($edge_id_category=='chapter'){
		
	}
}

//Get User Details
function getUserDetailsFromUserId($con,$user_id){
	//$con = createConnection();
	$stmt = $con->prepare("SELECT a.user_id,a.first_name,a.middle_name,a.last_name,a.email_id,a.is_email_verified,a.address_id,a.age_range,a.gender,a.education,a.employment_status,a.joining_purpose,a.marital_status,a.mother_tongue,a.exculsive_offers,a.instructions_tips,a.news_discount,a.years_eng_edu,a.is_scheduled_for_certificate,a.discount_readiness, b.loginid,c.phone,c.is_phone_verified,c.city,c.state,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=$user_id and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt->execute();
	$stmt->bind_result($user_id,$first_name, $middle_name, $last_name, $email_id, $is_email_verified,$address_id, $age_range, $gender, $education, $employment_status, $joining_purpose,$marital_status,$mother_tongue,$exculsive_offers,$instructions_tips, $news_discount,$years_eng_edu,$is_scheduled_for_certificate,$discount_readiness,$loginid,$phone,$is_phone_verified,$city,$state,$country_code,$country);
	$stmt->fetch();
	$stmt->close();

	$stmt = $con->prepare("SELECT a.system_name FROM asset a JOIN user u ON u.profile_pic = a.asset_id WHERE u.user_id = ".$user_id);
	$stmt->execute();
	$stmt->bind_result($system_name);
	$stmt->fetch();
	$stmt->close();	
	
	$obj = new stdclass();
	$obj->user_id = $user_id;
	$obj->first_name = $first_name;
	$obj->address_id = $address_id;
	$obj->last_name = $last_name;
	$obj->email_id = $email_id;
	$obj->is_email_verified = $is_email_verified;
	$obj->loginid = $loginid;
	$obj->phone = $phone;
	$obj->is_phone_verified = $is_phone_verified;
	$obj->age_range = $age_range;
	$obj->gender = $gender;
	$obj->education = $education;
	$obj->employment = $employment_status;
	$obj->joining_purpose = $joining_purpose;
	$obj->marital_status = $marital_status;
	$obj->mother_tongue = $mother_tongue;
	$obj->city = $city;
	$obj->state = $state;
	$obj->country_code = $country_code;
	$obj->country = $country;
	$obj->profile_pic = $system_name;
	$obj->exculsive_offers = $exculsive_offers;
	$obj->instructions_tips = $instructions_tips;
	$obj->news_discount = $news_discount;
	$obj->years_eng_edu = $years_eng_edu;
	if(empty($is_scheduled_for_certificate))
	{
	$is_scheduled_for_certificate='0';
	}
	if(empty($discount_readiness))
	{
	$discount_readiness='0';
	}
	$obj->is_scheduled_for_certificate = $is_scheduled_for_certificate;
	$obj->discount_readiness = $discount_readiness;

	$profile_completion=getProfileCompletion($first_name,$email_id,$gender,$age_range,$country,$joining_purpose,$is_scheduled_for_certificate);
	$obj->profile_completion = $profile_completion;

	return $obj;

}

//Set User Details
function setUserDetailsFromUserId($con,$user_id,$param){
	$alert_msg_arr = alertMessage();
	$con = createConnection();
	$first_name = $param->first_name;
	$last_name = $param->last_name;
	$email_id = $param->email_id;
	$phone = $param->phone;
	$address_id = $param->address_id;
	$password = $param->password;
	$age_range = $param->age_range;
	$gender = $param->gender;
	$education = $param->education;
	$employment = $param->employment;
	$joining_purpose = $param->joining_purpose;
	$marital_status = $param->marital_status;
	$city = $param->city;
	$state = $param->state;
	$country_code = $param->country_code;
	$country = $param->country;
	$exculsive_offers = $param->exculsive_offers;
	$instructions_tips = $param->instructions_tips;
	$news_discount = $param->news_discount;
	$mother_tongue = $param->mother_tongue;
	$years_eng_edu = $param->years_eng_edu;
	$is_scheduled_for_certificate = $param->is_scheduled_for_certificate;
	$discount_readiness = $param->discount_readiness;

	//$system_name = $obj->profile_pic;

	 $stmt= $con->prepare("update user set first_name = ?, last_name = ?, email_id=?, gender=?, age_range=?, marital_status=?, mother_tongue=?, education=?, employment_status=?, joining_purpose=?, exculsive_offers=?, instructions_tips=?, news_discount=?, years_eng_edu=?, is_scheduled_for_certificate=?, discount_readiness=?,ex_phone = ?, ex_city = ?, ex_state = ?, ex_country_code = ?, ex_country = ?,  modified_date = NOW() where user_id = ? ");
     $stmt->bind_param("ssssiiiiiisssisssssssi",$first_name,$last_name, $email_id, $gender, $age_range, $marital_status, $mother_tongue, $education, $employment, $joining_purpose, $exculsive_offers, $instructions_tips, $news_discount, $years_eng_edu, $is_scheduled_for_certificate, $discount_readiness,$phone, $city, $state, $country_code, $country,  $user_id);
     $stmt->execute();
     $stmt->close();

	

	 if(!empty($password))
	 {
		  $stmt= $con->prepare("update user_credential set password = ?, modified_date = NOW() where user_id = ? ");
		  $stmt->bind_param("si",$password, $user_id);
	      $stmt->execute();
		  $stmt->close();
		  
		  $stmt= $con->prepare("update user set ex_password = ?, modified_date = NOW() where user_id = ? ");
		  $stmt->bind_param("si",$password, $user_id);
	      $stmt->execute();
		  $stmt->close();
	 }
	 
	 	////Update user collection
	$parentObj = new stdClass();
	if($address_id!=''){
		 $stmt= $con->prepare("update address_master set phone = ?, city = ?, state = ?, country_code = ?, country = ?, modified_date = NOW() where address_id = ? ");
		 $stmt->bind_param("sssssi",$phone, $city, $state, $country_code, $country, $address_id);
		 $stmt->execute();
		 $stmt->close();
		
		 $stmt= $con->prepare("update user set ex_phone = ?, ex_city = ?, ex_state = ?, ex_country_code = ?, ex_country = ?, modified_date = NOW() where address_id = ? ");
		 $stmt->bind_param("sssssi",$phone, $city, $state, $country_code, $country, $address_id);
		 $stmt->execute();
		 $stmt->close();
	 }else{
		 if($phone!='' || $city!='' || $state!='' || $country_code!='' ||  $country!=''){
			$stmt= $con->prepare("Insert into address_master (phone, city , state , country_code, country,modified_date,created_date) Values('$phone','$city','$state','$country_code', '$country',NOW(),NOW() ) ");
			$stmt->execute();
			$stmt->close();
			$address_id = $con->insert_id;
			
			$stmt= $con->prepare("update user set address_id = ?,ex_phone = '$phone', ex_city = '$city', ex_state = '$state', ex_country_code = '$country_code', ex_country = '$country' where user_id = ? ");
			$stmt->bind_param("ii",$address_id,$user_id);
			$stmt->execute();
			$stmt->close();
			
			$parentObj->address_id=$address_id;
		 }
		 
	 }
	 
	 
	 /////Get profile completion
	$stmt = $con->prepare("SELECT a.user_id,a.first_name,a.email_id,a.age_range,a.gender,a.joining_purpose, a.is_scheduled_for_certificate,c.country from user a,user_credential b, address_master c where a.user_id=$user_id and a.user_id=b.user_id and c.address_id=a.address_id");
	$stmt->execute();
	$stmt->bind_result($user_id,$first_name, $email_id, $age_range, $gender, $joining_purpose,$is_scheduled_for_certificate,$country);
	$stmt->fetch();
	$stmt->close();

	if(empty($is_scheduled_for_certificate))
	{
	$is_scheduled_for_certificate='0';
	}
	

	$profile_completion=getProfileCompletion($first_name,$email_id,$gender,$age_range,$country,$joining_purpose,$is_scheduled_for_certificate);
	/////Get profile completion

	 //$sr = new ServiceResponse("SUCCESS",0,null);
	 $obj = new stdClass();
	 $obj->profile_completion = $profile_completion;
	 if($profile_completion==100)
	{
		
		$obj->msg = $alert_msg_arr['USER_PROFILE_COMPLETED'];
	}
	else
	{
		$obj->msg = $alert_msg_arr['USER_DETAILS_UPDATED'];
	}


	$con_g=createConnectionGamification();
	$stmt = $con_g->prepare("update user_total_point set countryCode=? where userId=?");
	$stmt->bind_param("si",$country,$user_id);
	$stmt->close();
	closeConnectionGamification($con_g);

	 return $obj;
    
	 /*$sr = new ServiceResponse("SUCCESS",0,null);
	 $retVal = new stdClass();
	 $sr->retVal->msg = $alert_msg_arr['USER_DETAILS_UPDATED'];
	 return $sr;*/	
}

//Get Assignments
function getUserAssignments($con,$con2,$user_id,$client_id){

			if(empty($client_id) || $client_id=='wiley')
	        {
			$client_id_val=46;
			}
			elseif($client_id=='englishEdge')
	        {
			$client_id_val=46;
			}
    
			$cList= $courseArr = array();
			
			$sql = "SELECT center_id,batch_id from tblx_batch_user_map WHERE user_id=$user_id";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($center_id,$batch_id);
			$stmt->fetch();
			$stmt->close();

			$sql = "SELECT batch_code from tblx_batch where center_id=$center_id and batch_id=$batch_id";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($batch_code);
			$stmt->fetch();
			$stmt->close();

			
			$sql = "Select DISTINCT ta.course_code,c.title from tblx_assignments ta JOIN course c on ta.course_code=c.course_id where ta.status=1 and ta.client_id=$client_id_val and (ta.batch_code='$batch_id' or ta.batch_code IS NULL)";
			
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($course_code,$course_name);
			$stmt->execute();
			while($stmt->fetch()) {
				$bcm = new stdClass();
				$bcm->course_code = $course_code;
				$bcm->course_name = $course_name;
				array_push($cList,$bcm);
			}
			$stmt->close();
			
			foreach($cList as $cKey=>$cVal){
				
				$tList=array();$topicArr=array();
		
				$sql = "Select DISTINCT ta.topic_edge_id,cm.name from tblx_assignments ta JOIN generic_mpre_tree gmp ON gmp.edge_id=ta.topic_edge_id JOIN cap_module cm ON gmp.tree_node_id=cm.tree_node_id where  ta.course_code=? and ta.client_id=$client_id_val and ta.status=1 and (ta.batch_code='$batch_id' or ta.batch_code IS NULL) order by cm.sequence_id ASC";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("s",$cVal->course_code);
				$stmt->execute();
				$stmt->bind_result($topic_edge_id,$topic_name);
				$stmt->execute();
				
				while($stmt->fetch()) {
					$bcm = new stdClass();
					$bcm->topic_name = $topic_name;
					$bcm->topic_edge_id = $topic_edge_id;
					array_push($tList,$bcm);
					
				}
				//file_put_contents("test/assign2.txt",print_r($tList,true));
			
				foreach($tList as $tKey=>$tVal){	
					$aList=array(); $aeList = array();
					
					$sql = "SELECT a.id, a.assignment_name, a.assignment_desc, a.assignment_text, a.assignment_file, a.assignment_end_date, a.assignment_type, a.created_date from tblx_assignments a where a.topic_edge_id =?  AND a.status =1 and a.client_id=$client_id_val and (a.batch_code='$batch_id' or a.batch_code IS NULL)";

					//file_put_contents("test/assign1.txt",$sql);
					$stmt = $con->prepare($sql);
					$stmt->bind_param("i",$tVal->topic_edge_id);
					$stmt->execute();
					$stmt->bind_result($assignment_id,$assignment_name,$assignment_desc,$assignment_text,$assignment_file,$assignment_end_date,$assignment_type,$created_date);
					$stmt->execute();
					
					while($stmt->fetch()) {
						$bcm = new stdClass();
						$bcm->assignment_id = $assignment_id;
						//file_put_contents("test/assign3.txt",$assignment_id, FILE_APPEND);
						$bcm->assignment_name = $assignment_name;
						$bcm->assignment_desc = $assignment_desc;
						$bcm->assignment_text = $assignment_text;
						if(!empty($assignment_file))
						{
							
						$bcm->assignment_file = $authoring_path."view/assignment_files/".$assignment_file;
						}
						else
						{
						$bcm->assignment_file = "";
						}
						$bcm->assignment_end_date = $assignment_end_date;
						$bcm->assignment_type = $assignment_type;
						$bcm->created_date = $created_date;
						
						
						array_push($aList,$bcm);
						
					}
					
					foreach($aList as $key=>$val){
						
						$bcm = new stdClass();
						$bcm->assignment_id = $val->assignment_id;
						//file_put_contents("test/assign4.txt",$assignment_id, FILE_APPEND);
						$bcm->assignment_name = $val->assignment_name;
						$bcm->assignment_desc = $val->assignment_desc;
						$bcm->assignment_text = $val->assignment_text;
						$bcm->assignment_file = $val->assignment_file;
						$bcm->assignment_end_date = $val->assignment_end_date;
						$bcm->assignment_type = $val->assignment_type;
						$bcm->created_date = $val->created_date;

						$evaluated_comment="";
						$evaluated_rating="";
						$student_id="";
						$sql = "SELECT evaluated_comment, evaluated_rating,student_id from tblx_assignment_evaluation  where assignment_id =?  AND student_id =?";
						$con->set_charset("utf8");
						$stmt = $con->prepare($sql);
						$stmt->bind_param("ii",$val->assignment_id,$user_id);
						$stmt->execute();
						$stmt->bind_result($evaluated_comment,$evaluated_rating,$student_id);
						$stmt->fetch();
						$stmt->close();
						
						$response_text="";
						$response_file="";
						$sql2 = "select response_text, response_file from tblx_assignment_response where assignment_id =?  AND user_id =?";
						$con2->set_charset("utf8");
						$stmt2 = $con2->prepare($sql2);
						$stmt2->bind_param("ii",$val->assignment_id,$user_id);
						$stmt2->execute();
						$stmt2->bind_result($response_text,$response_file);
						$stmt2->fetch();
						$stmt2->close();

						if(!empty($response_text))
						{

						$bcm->response_text = urlencode($response_text);
						$bcm->response_file = $response_file;
						$bcm->user_attempted = 'yes';
						}
						else
						{
						$bcm->response_text = '';
						$bcm->response_file = '';
						$bcm->user_attempted = 'no';
						}
						//file_put_contents("test/su.txt",$student_id."-".$user_id."\n",FILE_APPEND);
						if(($student_id==$user_id) && !empty($evaluated_rating))
						{
						$bcm->teacher_evaluated = 'yes';
						$bcm->teacher_feedback = urlencode($evaluated_comment);
						$bcm->teacher_grade = $evaluated_rating;
						$bcm->total_grade = '10'; 
						}
						else
						{
						$bcm->teacher_evaluated = 'no';
						}

						array_push($aeList,$bcm);
					
					}
					
					$topicArr[] = array('topic_edge_id'=>$tVal->topic_edge_id,'topic_name'=>publishText($tVal->topic_name),'assignmentArr'=>$aeList);
				
				}
				
				//total assignment coins
				$sql = "SELECT count(id) as assign_count from tblx_assignments where course_code=? and client_id=$client_id_val and (batch_code='$batch_id' or batch_code IS NULL)";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("s",$cVal->course_code);
				$stmt->execute();
				$stmt->bind_result($assign_count);
				$stmt->fetch();
				$stmt->close();	

				$assignment_coins = $assign_count*10;
				
				//assignment earned coins
				$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ? AND component_type = 8 GROUP BY user_id,course_code,component_type");
				$stmt->bind_param("is",$user_id,$cVal->course_code);
				$stmt->execute();
				$stmt->bind_result($assignment_coins_earned);
				$stmt->fetch();
				$stmt->close();	

				$assignment_coins_earned = !empty($assignment_coins_earned)?$assignment_coins_earned:0;
					
				$courseArr[] = array('course_code'=>$cVal->code,'course_name'=>$cVal->course_name,'topicArr'=>$topicArr,'assignment_coins'=>$assignment_coins,'assignment_coins_earned'=>$assignment_coins_earned);
			
			}
		
	if(count($courseArr)>0){
		return $courseArr;
	}
	return null;
}

//Verify OTP
function verifyOTP($con,$user_otp,$user_phone,$user_email,$user_action,$user_id,$user_name)
{
	$alert_msg_arr = alertMessage();
	$arr = array('status' => 0, 'msg' => '', 'data' => array());
	//$data = array();
	
	if(!empty($user_phone))
    {
		$requested_via=$user_phone;
	}
	else if (!empty($user_email))
    {
		$requested_via=$user_email;
	}
	
   else if (!empty($user_name))
    {
	      $stmt = $con->prepare("SELECT u.email_id FROM user u, user_credential uc WHERE u.user_id=uc.user_id and uc.loginid='$user_name'");
		    $stmt->bind_result($user_name_email);
		    $stmt->execute();
		    $stmt->fetch();
		    $stmt->close();
			$requested_via=$user_name_email;
	}

	 $stmt = $con->prepare("select id,otp,requested_by,created_on,NOW() from tblx_otp where otp='$user_otp' and requested_by='$requested_via' and is_used='0'");
     $stmt->bind_result($oid,$otp,$requested_by,$created_on,$current_time);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();
	 $timeFirst  = strtotime($current_time);
	 $timeSecond = strtotime($created_on);
	 $differenceInSeconds = $timeFirst - $timeSecond;

		

	if(empty($oid)) {
		$sr = new ServiceResponse("FAILURE",0,null);
		$sr->retVal = new stdClass();
		$sr->retVal->msg = $alert_msg_arr['OTP_FAILED'];
		return $sr;	
   }
	else if(!empty($oid) &&  $differenceInSeconds <= 120)
	{
	    $sr = new ServiceResponse("SUCCESS",0,null);
	    $retVal = new stdClass();
	    $sr->retVal->msg = $alert_msg_arr['OTP_PASS'];
	    
		$stmt = $con->prepare("update tblx_otp set is_used='1' where id=? limit 1");
        $stmt->bind_param("i", $oid);
		$stmt->execute();
		$stmt->close();
	//	file_put_contents("test/email.txt","update user set is_email_verified='1' where user_id=$user_id limit 1");
	if(!empty($user_phone) && $user_id!=0 && $user_action='profile_update')
     {
		
		 $stmt = $con->prepare("select address_id from user where user_id=$user_id");
		 $stmt->bind_result($address_id);
		 $stmt->execute();
		 $stmt->fetch();
		 $stmt->close();

		 $stmt = $con->prepare("update address_master set phone='$user_phone',is_phone_verified='1', modified_date = NOW() where address_id=? limit 1");
         $stmt->bind_param("i", $address_id);
		 $stmt->execute();
		 $stmt->close();
	 }
	 else if (!empty($user_email) && $user_id!=0 && $user_action='profile_update')
	 {
		//file_put_contents("test/ab.txt","update user set email_id='$user_email',is_email_verified='1' where user_id=$user_id limit 1");
		$stmt = $con->prepare("update user set email_id='$user_email',is_email_verified='1' where user_id=? limit 1");
        $stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->close();
	 }

		return $sr;	
	}
	else
    {
		$sr = new ServiceResponse("FAILURE",0,null);
		$sr->retVal = new stdClass();
		$sr->retVal->msg = $alert_msg_arr['OTP_EXPIRED'];
		return $sr;
	}
       
}

//Change Password
function changePassword($con,$user_phone,$user_email,$user_password,$user_name)
{
	$alert_msg_arr = alertMessage();
	if(!empty($user_phone))
    {
		$user_loginid=$user_phone;
	}
	else if (!empty($user_email))
    {
		$user_loginid=$user_email;
	}
	else if (!empty($user_name))
    {
		$user_loginid=$user_name;
	}


	$stmt = $con->prepare("update user_credential set password='$user_password', modified_date = NOW() where loginid=?");
    $stmt->bind_param("s", $user_loginid);
	$stmt->execute();
	$stmt->close();
	
	$stmt = $con->prepare("update user set ex_password='$user_password', modified_date = NOW() where ex_loginid=?");
    $stmt->bind_param("s", $user_loginid);
	$stmt->execute();
	$stmt->close(); 

	$sr = new ServiceResponse("SUCCESS",0,null);
	$retVal = new stdClass();
	$sr->retVal->msg = $alert_msg_arr['PASSWORD_CHANGED'];
	return $sr;	
}

//Set Acceptance
function setAcceptance($con,$user_id) {
 
    $query = "update user set is_accepted=1 where user_id=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $stmt->close();
    $retObj->status="SUCCESS";
    if ($con->error) {
        $retObj->status="FAILURE";
        $retObj->reason=$con->error;
    } else {
        $retObj->status="SUCCESS";
    }
    return $retObj;
}

//Get Visiting Level
function getVisitingLevel($con,$user_id,$product_id){
    
	    $productArr = array();
	   $stmt = $con->prepare("select product_id from tbl_user_visited_course where user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->execute();
		$stmt->bind_result($product_id1);
		while($stmt->fetch()) {
          array_push($productArr,$product_id1);
		}
		$stmt->close();
		 

		if(count($productArr)>1){
			if(in_array($product_id,$productArr)){
				$stmt = $con->prepare("select id,product_id,user_visiting_level from tbl_user_visited_course where user_id = ? and product_id=?");
				$stmt->bind_param("ii",$user_id,$product_id);
				$stmt->execute();
				$stmt->bind_result($id,$product_id,$user_visiting_level);
				$stmt->fetch();
				$stmt->close();
				if(!empty($id)){
					return $user_visiting_level;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{

		 if($product_id1==0){
			 $user_visiting_level=1;
			 return $user_visiting_level;
		}else{
			$stmt = $con->prepare("select id,product_id,user_visiting_level from tbl_user_visited_course where user_id = ? and product_id=?");
			$stmt->bind_param("ii",$user_id,$product_id);
			$stmt->execute();
			$stmt->bind_result($id,$product_id,$user_visiting_level);
			$stmt->fetch();
			$stmt->close();
			if(!empty($id)){
				return $user_visiting_level;
			}else{
				return false;
			}
		}
    }
	
}

//Update Visiting Level
function updateVisitingLevel($con,$user_id,$product_id,$visiting_level){
	   $productArr = array();
	   $stmt = $con->prepare("select product_id from tbl_user_visited_course where user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->execute();
		$stmt->bind_result($product_id1);
		while($stmt->fetch()) {
          array_push($productArr,$product_id1);
		}
		$stmt->close();
		 
	if(!empty($visiting_level)){
		if(count($productArr)>1){
			//file_put_contents("test/visit2.txt",print_r($productArr,true));
			if(in_array($product_id,$productArr)){
			//file_put_contents("test/exist.txt",$product_id);
			  $stmt= $con->prepare("update tbl_user_visited_course set user_visiting_level=? where user_id = ? and product_id = ?");		
			  $stmt->bind_param("sii",$visiting_level,$user_id,$product_id);			
			  $stmt->execute();		
			  $stmt->close(); 
			  return true;
			}else{
				//file_put_contents("test/insert2.txt",$product_id1);
			   $stmt= $con->prepare("insert into tbl_user_visited_course (user_id,product_id, user_start_level, user_current_level,user_visiting_level,date_attempted) values(?,?,1,1,?,NOW())");
			   $stmt->bind_param("iis",$user_id,$product_id,$visiting_level);			
			   $stmt->execute();		
			   $stmt->close(); 
			  return true;
			}
		}else{
			//file_put_contents("test/visit1.txt",$product_id1);
			if($product_id1==$product_id){ 
				$stmt= $con->prepare("update tbl_user_visited_course set user_visiting_level=? where user_id = ? and product_id=?");		
				$stmt->bind_param("sii",$visiting_level,$user_id,$product_id);			
				$stmt->execute();		
				$stmt->close();
				return true;
			}else if($product_id1=='0'){ 	
			  $stmt= $con->prepare("update tbl_user_visited_course set user_visiting_level=?,product_id=? where user_id = ?");		
			  $stmt->bind_param("sii",$visiting_level,$product_id,$user_id);			
			  $stmt->execute();		
			  $stmt->close(); 
			  return true;
			}else{
			  //file_put_contents("test/insert1.txt",$product_id."  ==  ".$product_id1);
			  $stmt= $con->prepare("insert into tbl_user_visited_course (user_id,product_id, user_start_level, user_current_level,user_visiting_level,date_attempted) values(?,?,1,1,?,NOW())");
			  $stmt->bind_param("iis",$user_id,$product_id,$visiting_level);			
			  $stmt->execute();		
			  $stmt->close(); 
			  return true;
			}
		}  
	}else
	{
	return false;
	}
	
}

//Insert Teacher Session
function insertTeacherSession($con,$teacher_id,$batch_id,$chapter_edge_id,$topic_edge_id,$course_code,$sessionMode=0){
       
        if( empty($teacher_id) ||  empty($chapter_edge_id) ){
            return false;
        }  
		if($sessionMode==1){
		   $ilt_session_type='prepmode';
		}else{
			$ilt_session_type='classmode';
			}
		
		$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$teacher_id);
		$stmt->bind_result($center_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$student_list = getStudentListByBatchAndCenter($con,$batch_id,$center_id);
		
        $sql = "select id from tblx_teacher_session WHERE teacher_id =? AND batch_id = ? AND chapter_edge_id =? AND center_id =?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('iiii',$teacher_id,$batch_id,$chapter_edge_id,$center_id);
        $stmt->bind_result($id);
		$stmt->execute();
        $stmt->fetch();
        $stmt->close();
        if($id!=""){
		    $obj = new stdClass();
		    $obj->student_list=$student_list;
            $obj->ts_id=$id;
		    return $obj;
        }
		$sql = "Insert into tblx_teacher_session (center_id,teacher_id, batch_id, chapter_edge_id,topic_edge_id,course_code,create_date,update_date,session_mode)  Values (?,?, ?,?, ?, ?, now(), now(),?)";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('iiiiiss', $center_id,$teacher_id,$batch_id,$chapter_edge_id,$topic_edge_id,$course_code,$ilt_session_type);
		$stmt->execute();
		$stmt->close();  
		$obj = new stdClass();
		$obj->student_list=$student_list;
        $obj->ts_id=$con->insert_id;
		return $obj;
       
}

//Get Student list
function getStudentListByBatchAndCenter($con,$batch_id,$center_id){
       
        $studentArr = array();
		$sql = "select u.user_id,u.first_name,u.middle_name,u.last_name from user u join tblx_batch_user_map tbum on u.user_id=tbum.user_id join user_role_map urm on u.user_id=urm.user_id WHERE tbum.batch_id =? AND tbum.center_id =? AND urm.role_definition_id='2'";
        $stmt = $con->prepare($sql); 
        $stmt->bind_param("ii",$batch_id,$center_id);
		$stmt->execute();
		$stmt->bind_result($user_id,$first_name,$middle_name,$last_name);
		while($stmt->fetch()) {
			$student = new stdClass();
			$student->user_id = $user_id;
			$student->first_name = $first_name;
			//$student->middle_name = $middle_name;
			$student->last_name = ($last_name!='')?$last_name :'';
            $student->attendance = 1;
            
			array_push($studentArr,$student);
		}
		$stmt->close();
		
		if(count($studentArr)>0){
			return $studentArr;
		}
		return false;
		
}

//Update Teacher Session
function updateTSCompletion($con,$ts_id, $comp_per) {
        
		$ilt_session_type='classmode';
		$sql = "update tblx_teacher_session SET completion_status = ?,session_mode=? WHERE id = ? AND completion_status < ?";
        $stmt = $con->prepare($sql);
		$stmt->bind_param("iisi",$comp_per,$ts_id,$ilt_session_type,$comp_per);
        $stmt->execute();
        $stmt->close();
        return true;
        
    }
    
function setAttendance($con,$teacher_id,$batch_id,$ts_id,$date,$all_students_ids) {
        
        if( !is_numeric($ts_id) || empty($date) ){
            return false;
        }
        
		
		$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$teacher_id);
		$stmt->bind_result($center_id);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$sql = "Delete From tblx_ts_student_attendance WHERE ts_id = ? and att_date = ? and center_id=?";
        $stmt = $con->prepare($sql);
		$stmt->bind_param('isi',$ts_id,$date,$center_id);
        $stmt->execute();
        $stmt->close();
        
        foreach($all_students_ids as $st_id ){
			
			$user_id = $st_id->user_id;
			$is_present = $st_id->attendance;
            $sql = "Insert into tblx_ts_student_attendance (ts_id,att_date,student_id,center_id,is_present,create_date,update_date ) Values (?,?,?,?,?, now(), now()) ";
            $stmt = $con->prepare($sql);
			$stmt->bind_param('isiii',$ts_id,$date,$user_id,$center_id,$is_present);
            $stmt->execute();
            $stmt->close();
            
        }
        
       return true;
        
}

function getIltActivity($con,$edgeId,$course_code){
	$courseID = getCourseIdByCourseCode($course_code);
	if($courseID){
		$courseDetails=getCourseDetailsCourseId($courseID);
		$courseCatalogID = getCatLogEdgeIdByCourseId($courseID);
		$course_type = getCourseTypeByCourseCode($course_code);
		$topics = getTopicOrAssessmentByCourseId($courseID,'');
		$totalTopics=count($topics);
		
		$a = 1;
		$i = 1;
		$scn=1;
		
		$chapterEdgeId = $edgeId;
		
		foreach($topics as $key => $value){
		
			if($value->assessment_type ==""){
					$chapters=getChapterByTopicEdgeId($value->edge_id,'');
					$totalChapters=count($chapters);
					$ch=1;
					
					if($totalChapters>=0){
						$scenarioArr = array();
						foreach($chapters as $keyc=>$valuec){
                            
							$scenarioArr = array();
							
								if($valuec->edge_id == $chapterEdgeId){
									$ttl_activity = getActvityCountByChapterId($chapterEdgeId);
									
									if($ttl_activity > 0){
										$data = "PracticeApp/".$course_code."/course/module".$i."/scenario".$scn."/chapter.xml";
										$xml = simplexml_load_file($data);
                                  		$json =  json_encode($xml);
										}
									   $scenarioArr = array("edgeId" => $chapterEdgeId,"data" => $json);	
									}
									
									$retObj->status=1;
									$retObj->course_code=$course_code;
									$retObj->chapComponent=$scenarioArr;
									$retObj->msg='SUCCESS';
									return $retObj;	
							
							$scn++;
							$ch++;
						
                        }
								
                        }
                        
						$i++;      
					}
			}
				
		
	}else{
		$retObj->status=0;
		$retObj->course_code=$course_code;
		$retObj->chapterArr="";
		$retObj->msg='FAILER';
		return $retObj;	
	}
}

function getActvityCountByChapterId($tree_node_id){
		$con = createConnection();
		
		$stmt = $con->prepare("SELECT count(*) as cnt
								FROM tblx_activity
								WHERE  chapter_edge_id = ?");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($cnt);		
		$stmt->fetch();
		$stmt->close();
		
		closeConnection($con);
		return $cnt;
	   		
}

function trackIltActivity($con,$ts_id,$user_id,$page_num,$page_type,$activity_id,$start_date_ms,$end_date_ms) {
        
       $second_time = $start_date_ms / 1000;
	   $entry_date_time = date('Y-m-d H:i:s',($start_date_ms/1000));
	   $exit_date_time = date('Y-m-d H:i:s',($end_date_ms/1000));
	   if($end_date_ms > $start_date_ms){    
		$time_spent = ($end_date_ms - $start_date_ms) / 1000;
	   }else{
		$time_spent = 0;
	   }
	   
	   $session_mode='classmode';
		
        $sql = "Insert into tblx_ts_page_tracking (ts_id,page_num,page_type,activity_id,entry_date_time,exit_date_time,time_spent,session_mode,create_date,update_date) Values (?,?,?,?,?,?,?,?, now(), now()) ON DUPLICATE KEY "
         . " update page_type = ?, time_spent = time_spent + ? ,session_mode= ? ,update_date = NOW()  ";
        $stmt = $con->prepare($sql);
	    $stmt->bind_param('iisississis',$ts_id,$page_num,$page_type,$activity_id,$entry_date_time,$exit_date_time,$time_spent,$session_mode,$page_type,$time_spent,$session_mode);
        $stmt->execute();
        $stmt->close();
            
      
        
       return true;
        
}

//Send completion api

/* get course overall data, skill wise data and test wise data */

function getTrack($con,$user_id,$course_code,$batch_id){
	
	$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->fetch();
	$stmt->close(); 

	$courseID = getCourseIdByCourseCode($course_code);

	if($courseID){
		$courseDetails = getCourseDetailsCourseId($courseID);
		$courseName = publishText($courseDetails->title);
		$course_edge_id = getCourseEdgeIdByCourseId($courseID);//course edge id
		$duration_ms = 0;
		$stmt = $con->prepare("SELECT COALESCE(SUM(actual_seconds), 0) from user_session_tracking where course_code = ? AND user_id = ? AND batch_id= ? AND center_id= ?  AND session_type='CH'");
		$stmt->bind_param("siii",$course_code,$user_id,$batch_id,$center_id);
		$stmt->execute();
		$stmt->bind_result($duration_ms);
		$stmt->fetch();
		$stmt->close(); 
		
		//get topics
		$topicArr = getTopicByCourseEdgeId($con,$course_edge_id);
		
		$number_of_topics=count($topicArr); 
		
		//get completed topic
		$number_of_completed_chapter = $number_of_completed_topic = 0;
		foreach($topicArr as $key => $value){
			$topic_edge_id = $value->edge_id;
			$topic_name = $value->name;
			$quiz_topic_coins = $vocab_topic_coins = $rp_topic_coins = $speedreading_topic_coins = array();
			$topic_completion = false;
			if($value->assessment_type==""){
				
				$topic_started = false;$topic_completion = false;
				$chapter_Array = $chapter_time_arr = $chpaterCrrctArr = $chpaterQCountArr = array();
				$assign_count = $assignment_score = 0;
				
				$singleChapterArr = getChapterByTopicEdgeId($topic_edge_id,'');
				$chapter_count = count($singleChapterArr);
				$number_of_completed_chapter_topic = 0;
				foreach($singleChapterArr as $chapterArrKey=>$chapterArrVal){
					
					$chapterEdgeId = $chapterArrVal->edge_id;
					$sequence_no = $chapterArrVal->sequence_no;
					$chapter_time=0;

					//total time spent chapter
					$stmt = $con->prepare("SELECT COALESCE(SUM(actual_seconds), 0) as ttlTimeSp from user_session_tracking where session_id = ? AND user_id = ? AND batch_id = ? AND center_id= ? AND session_type='CH'");
					$stmt->bind_param("iiii",$chapterEdgeId,$user_id,$batch_id,$center_id);
					$stmt->execute();
					$stmt->bind_result($chapter_time);
					$stmt->fetch();
					$stmt->close(); 
					$chapter_time_arr[] = $chapter_time;
					
					$completion_status='';
					
					//total time spent chapter
					$stmt = $con->prepare("SELECT completion from tblx_component_completion where component_edge_id = ? AND user_id = ? AND center_id = ? AND batch_id= ?");
					$stmt->bind_param("iiii",$chapterEdgeId,$user_id,$center_id,$batch_id);
					$stmt->execute();
					$stmt->bind_result($completion_status);
					$stmt->fetch();
					$stmt->close(); 			
					if($completion_status=='c'){
						$topic_completion = true;
						$number_of_completed_chapter_topic++;
						$number_of_completed_chapter++;
						$topic_started = true;
					}elseif($completion_status!=''){
						$topic_started = true;
						$topic_completion = false;
					}else{
						$completion_status='na';
						$topic_completion = false;
					}
						
					$chapter_Array[] =  array("chapter_name"=>$chapterArrVal->name,"chapter_time"=>$chapter_time,"chapter_completion"=>$completion_status, "chapterEdgeId"=>$chapterEdgeId, "chap_sequence"=>$sequence_no);
				
				
				}
				
				if($topic_completion ==true && ($number_of_completed_chapter_topic==$chapter_count)){
					$topic_completion = 'c';
					$number_of_completed_topic++;
				}elseif($topic_started==true){
					$topic_completion = 'nc';
				}else{
					$topic_completion = 'na';
				}
				
			
				$topic_time = array_sum($chapter_time_arr);
			
				//Number of completed activity
				$number_of_completed_activity = 0;
				$stmt = $con->prepare("SELECT count(distinct tspt.activity_id) as 'cnt' from tblx_activity ta 
				JOIN tblx_ts_page_tracking tspt ON ta.activity_id = tspt.activity_id
				JOIN tblx_teacher_session tts ON tts.id = tspt.ts_id where tts.batch_id = ? AND tts.topic_edge_id=?  AND ta.is_activity = '1'");
				$stmt->bind_param("ii",$batch_id,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($number_of_completed_activity);
				$stmt->fetch();
				$stmt->close();	
			
			
				
				$topic_Array[] = array("chapter_Array"=>$chapter_Array,"chapter_complete_count"=>$number_of_completed_chapter_topic, "chapter_count"=>$chapter_count, "topic_time"=> $topic_time, "topic_name"=>$topic_name,"topic_edge_id" => $topic_edge_id,"number_of_completed_activity" => $number_of_completed_activity,"topic_completion" => $topic_completion);
			
				$number_of_topics++;
			}
		}
		
		
		//Number of chapter
		$number_of_chapters = 0;
		$stmt = $con->prepare("SELECT count(cm.session_node_id) as 'cnt' from generic_mpre_tree gmt 
		JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
		JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tnd.tree_node_category_id=2  AND gmt.is_active = 1");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($number_of_chapters);
		$stmt->fetch();
		$stmt->close();	
		
		$retObj->edge_id=$course_edge_id;
		$retObj->course_name = $courseName;
		$retObj->total_time_spent = $duration_ms;	
		$retObj->number_of_topics = $number_of_topics;	
		$retObj->number_of_completed_topic = $number_of_completed_topic;	
		$retObj->number_of_chapters = $number_of_chapters;	
		$retObj->number_of_completed_chapter = $number_of_completed_chapter;	
		$retObj->user_id = $user_id;	
		$retObj->topic_Array = $topic_Array;	
		return $retObj;
	}
	
}

function getCompletionAndPer($con,$user_id,$edge_id,$batch_id){
	
	$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->fetch();
	$stmt->close(); 

	$stmt = $con->prepare("SELECT completion,complete_per from tblx_component_completion where component_edge_id = ? AND user_id = ? AND center_id = ? AND batch_id= ?");
	$stmt->bind_param("iiii",$edge_id,$user_id,$center_id,$batch_id);
	$stmt->execute();
	$stmt->bind_result($completion_status,$complete_per);
	$stmt->fetch();
	$stmt->close(); 			
	if($completion_status==''){
		$completion_status='na';
	}
	$retObj->edge_id=$edge_id;
	$retObj->completion_status=$completion_status;
	$retObj->complete_per=$complete_per;
	$retObj->user_id = $user_id;	

	return $retObj;
	
}
/* get all complear edge_id as arr */
function getAllCompletionAndPer($con,$user_id,$param){
	
	$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->fetch();
	$stmt->close(); 
	
	$completionArr=array();
	if(is_array($param->edge_id)==1){
		
		foreach($param->edge_id as $key=>$val){
			$stmt = $con->prepare("select completion,complete_per from tblx_component_completion where user_id=? and center_id = ? and batch_id= ? and component_edge_id=? and course_code=? and license_key=?");
			$stmt->bind_param("iiiiss",$user_id,$center_id,$param->batch_id,$val,$param->course_code,$param->package_code);
			$stmt->execute();
			$stmt->bind_result($completion_status,$complete_per);
			$stmt->fetch();
			$stmt->close();
			$obj = new stdclass();
			$obj->edge_id = $val;
			$obj->completion_status = $completion_status;
			$obj->complete_per = $complete_per;
			$retObj->user_id = $user_id;	
			$completionArr[$val]=$obj;
		}
	}else{
		
			$stmt = $con->prepare("select completion,complete_per from tblx_component_completion where user_id=? and component_edge_id=? and center_id = ? and batch_id= ? and course_code=? and license_key=?");
			$stmt->bind_param("iiiiss",$user_id,$center_id,$param->batch_id,$param->edge_id,$param->course_code,$param->package_code);
			$stmt->execute();
			$stmt->bind_result($completion_status,$complete_per);
			$stmt->fetch();
			$stmt->close();
			$obj = new stdclass();
			$obj->edge_id = $param->edge_id;
			$obj->completion_status = $completion_status;
			$obj->complete_per = $complete_per;
			$retObj->user_id = $user_id;	
			$completionArr[$param->edge_id]=$obj;
	}
	
	return $completionArr;

}

function createGroup($con,$user_id,$ts_id, $group_id_arr) {
       
       foreach($group_id_arr as $key=>$val){
		
		$group_id = $val->group_id;
		$students_id_arr = $val->students_id_arr;
		
		$sql = "Select group_id from tblx_ts_group where ts_id = ? AND group_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('ii', $ts_id, $group_id);
		$stmt->execute();
		$stmt->bind_result($group_id_exist);
		$stmt->fetch();
		$stmt->close();
        if($group_id_exist!=""){
            return true;
        }
        
        $sql = "Insert into tblx_ts_group (ts_id,group_id,create_date,update_date) "
                . " Values ( ?, ?, now(), now() ) ";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('ii', $ts_id, $group_id);
        $stmt->execute();
        $stmt->close();
		
		
		
		if($group_id!='' && count($students_id_arr)>0){
			addGroupStudents($con,$user_id,$ts_id,$group_id,$students_id_arr);
		 
       } 
	   /*  file_put_contents('test/ddd.txt','group_id '.$val->group_id,FILE_APPEND);
		 file_put_contents('test/ddd1.txt',print_r($val->students_id_arr,true),FILE_APPEND); */
	}
   return true;
}
	
function addGroupStudents($con,$user_id,$ts_id, $group_id, $students_id_arr) {
        
        if( empty($ts_id) || empty($group_id) ){
            return false;
        }
        
        $sql = "Delete From tblx_ts_group_student WHERE ts_id = ? AND group_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('ii', $ts_id, $group_id);
        $stmt->execute();
        $stmt->close();
        
        if( count($students_id_arr) && is_array($students_id_arr) ){
           
		   foreach($students_id_arr as $st_id){
                $sql = "Insert into tblx_ts_group_student (ts_id,group_id,student_id,create_date,update_date) Values (?,?,?, NOW(), NOW()) ";
                $stmt = $con->prepare($sql);
				$stmt->bind_param('iii',$ts_id,$group_id,$st_id);
                $stmt->execute();
                $stmt->close();
            }

        }
        
}	


function saveGroupActivityScore($con,$user_id,$ts_id, $activity_id, $grp_id, $by_grp_id, $teacher_id, $score){
        
        $sql = "Select id from tblx_ts_group_score WHERE ts_id = ? AND activity_id = ? AND ts_group_id = ? "
                . " AND by_ts_group_id = ? AND teacher_id = ? ";
         $stmt = $con->prepare($sql);
        $stmt->bind_param('iiiii', $ts_id,$activity_id,$grp_id,$by_grp_id,$teacher_id);
		$stmt->execute();
		$stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();
        
        
        if($id!='' && $id!=null){
            // update
            
            $sql = "UPDATE tblx_ts_group_score SET score = ? WHERE ts_id = ? AND activity_id = ? AND ts_group_id = ? "
                . " AND by_ts_group_id = ? AND teacher_id = ? ";
        
            $stmt = $con->prepare($sql);
            $stmt->bind_param('iiiiii',$score, $ts_id,$activity_id,$grp_id,$by_grp_id,$teacher_id);
			$stmt->execute();
            $stmt->close();
            
            
        }else{
            
            $sql = "Insert into tblx_ts_group_score (ts_id, activity_id, ts_group_id, by_ts_group_id, teacher_id, score, create_date, update_date ) "
                . " Values (?, ?, ? , ? , ?, ?, now(), now() ) ";
        
            $stmt = $con->prepare($sql);
            $stmt->bind_param('iiiiii', $ts_id,$activity_id,$grp_id,$by_grp_id,$teacher_id,$score);
			$stmt->execute();
            $stmt->close();
        
        }
        
        //return false;
}

function getGroup($con,$ts_id) {
    
	$group_list = array();
	$sql = "Select group_id from tblx_ts_group where ts_id = ?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param('i',$ts_id);
	$stmt->execute();
	$stmt->bind_result($group_id);
	while($stmt->fetch()) {
		array_push($group_list,$group_id);
    }
    
	$stmt->close();
	
	closeConnection($con);
	
	return $group_list;
}

function chkTeacherChapterSession($con,$user_id,$batch_id,$chapter_edge_id){
	
	$sql = "SELECT center_id from tblx_batch_user_map WHERE user_id=?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->fetch();
	$stmt->close(); 
	
	$sql = "select id,completion_status from tblx_teacher_session WHERE teacher_id =? AND batch_id = ? AND chapter_edge_id =? AND center_id =?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param('iiii',$user_id,$batch_id,$chapter_edge_id,$center_id);
	$stmt->bind_result($id,$completion_status);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	if($id!=""){
		$obj = new stdClass();
		$obj->is_already_started=1;
		$obj->ts_id=$id;
		$obj->completion_status=$completion_status;
		return $obj;
	}
	
	$obj = new stdClass();
	$obj->is_already_started=0;
	$obj->ts_id='';
	$obj->completion_status=0;
	return $obj;
	
}

function removeTSgroups($con,$ts_id) {
        
        $sql = "Delete From tblx_ts_group WHERE ts_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $ts_id);
        $stmt->execute();
        $stmt->close();
        
        $sql = "Delete From tblx_ts_group_student WHERE ts_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $ts_id);
        $stmt->execute();
        $stmt->close();
        
        $sql = "Delete from tblx_ts_group_score WHERE ts_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $ts_id);
        $stmt->execute();
        $stmt->close();
        
        
 }
	
 /* vocab word Completion Set and Get*/

function aduroSetWordCompletion($con,$user_id,$param){
	 
	$stmt = $con->prepare("select word_id,completion from tblx_word_completion where user_id=? AND component_edge_id=? AND word_id=?");
	$stmt->bind_param("iii",$user_id,$param->edge_id,$param->word_id);	
	$stmt->execute();
	$stmt->bind_result($word_id,$completion);
	$stmt->fetch();
	$stmt->close();
	if(empty($word_id)){
         //file_put_contents('test/complete.txt',$param->component_edge_id."==".$param->completion);
		$query="INSERT INTO tblx_word_completion(user_id,course_code,chapter_edge_id,component_edge_id,word_id,completion,created_date) values(?,?,?,?,?,?,NOW())";
		$stmt= $con->prepare($query);
		$stmt->bind_param("isiiis",$user_id,$param->course_code,$param->chapter_edge_id,$param->edge_id,$param->word_id,$param->completion);
		$stmt->execute();
		$stmt->close();
	}else{
		if($completion!='c'){
			$stmt= $con->prepare("update tblx_word_completion set completion=? where word_id=?");
			$stmt->bind_param("si",$param->completion,$word_id);
			$stmt->execute();
		    $stmt->close();
		}
	}
}


function aduroGetWordCompletion($con,$user_id){

	$stmt = $con->prepare("select course_code,chapter_edge_id,component_edge_id,word_id,completion from tblx_word_completion where user_id=".$user_id);
	$stmt->execute();
	$stmt->bind_result($course_code,$chapter_edge_id,$component_edge_id,$word_id,$completion);
	
	 $completionArr=array();
	 while($stmt->fetch()) {
		 if(!empty($component_edge_id)){
			$obj = new stdclass();
			$obj->course_code = $course_code;
			$obj->chapter_edge_id= $chapter_edge_id;
			$obj->edge_id = $component_edge_id;
			$obj->word_id = $word_id;
			$obj->completion = $completion;
			$completionArr[]=$obj;
		}
	 }
	$stmt->close();
	return $completionArr;

}

 /* User bookmark Set and Get*/
function aduroSetUserBookmark($con,$user_id,$param){
	 
	$stmt = $con->prepare("select id,course_code, topic_edge_id ,chapter_edge_id,component_edge_id,other from tblx_user_bookmark where user_id=? AND license_key=?");
	$stmt->bind_param("is",$user_id,$param->package_code);	
	$stmt->execute();
	$stmt->bind_result($id,$course_code,$topic_edge_id,$chapter_edge_id,$component_edge_id,$other);
	$stmt->fetch();
	$stmt->close();
	if(empty($id)){
         //file_put_contents('test/complete.txt',$param->component_edge_id."==".$param->completion);
		/* $query="INSERT INTO tblx_user_bookmark(user_id,course_code,	license_key,created_date) values(?,?,?,NOW())";
		$stmt= $con->prepare($query);
		$stmt->bind_param("iss",$user_id,$param->course_code,$param->package_code);
		$stmt->execute();
		$stmt->close(); */
		$query="INSERT INTO tblx_user_bookmark(user_id,course_code,topic_edge_id,chapter_edge_id,	license_key,created_date) values(?,?,?,?,?,NOW())";
		$stmt= $con->prepare($query);
		$stmt->bind_param("isiis",$user_id,$param->course_code,$param->topic_edge_id,$param->chapter_edge_id,$param->package_code);
		$stmt->execute();
		$stmt->close();
	}else{
		/* if($param->course_code!=''){
			$stmt= $con->prepare("update tblx_user_bookmark set course_code=?,topic_edge_id='',chapter_edge_id='',component_edge_id='',other='',modified_date=NOW() where id=?");
			$stmt->bind_param("si",$param->course_code,$id);
			$stmt->execute();
		    $stmt->close();
		} */
		if($param->topic_edge_id!='' && $topic_edge_id!=$param->topic_edge_id){
			$stmt= $con->prepare("update tblx_user_bookmark set course_code=?, topic_edge_id=?,chapter_edge_id='',modified_date=NOW() where id=? AND license_key=?");
			$stmt->bind_param("siis",$param->course_code,$param->topic_edge_id,$id,$param->package_code);
			$stmt->execute();
		    $stmt->close();
		}
		if($param->chapter_edge_id!='' ){
			$stmt= $con->prepare("update tblx_user_bookmark set course_code=?, topic_edge_id=?,chapter_edge_id=?,modified_date=NOW() where id=? AND license_key=?");
			$stmt->bind_param("siiis",$param->course_code,$param->topic_edge_id,$param->chapter_edge_id,$id,$param->package_code);
			$stmt->execute();
		    $stmt->close();
		}
		/* if($param->component_edge_id!=''){
			$stmt= $con->prepare("update tblx_user_bookmark set course_code=?, topic_edge_id=?,chapter_edge_id=? ,component_edge_id=?, other='', modified_date=NOW() where id=?");
			$stmt->bind_param("siiii",$param->course_code,$param->topic_edge_id,$param->chapter_edge_id,$param->component_edge_id,$id);
			$stmt->execute();
		    $stmt->close();
		}
		if($param->other!=''){
			$stmt= $con->prepare("update tblx_user_bookmark set update tblx_user_bookmark set course_code=?, topic_edge_id=?,chapter_edge_id=? ,other=?,modified_date=NOW() where id=?");
			$stmt->bind_param("siiiii",$param->course_code,$param->topic_edge_id,$param->chapter_edge_id,$param->component_edge_id,$param->other,$id);
			$stmt->execute();
		    $stmt->close();
		} */
	}
}

function aduroGetUserBookmark($con,$user_id,$param){

	$stmt = $con->prepare("select id,license_key,course_code, topic_edge_id ,chapter_edge_id,component_edge_id,other from tblx_user_bookmark where user_id=".$user_id);
	$stmt->execute();
	$stmt->bind_result($id,$license_key,$course_code,$topic_edge_id,$chapter_edge_id,$component_edge_id,$other);
	
	 $bookMarkArr=array();
	 while($stmt->fetch()) {
		 if(!empty($course_code)){
			$obj = new stdclass();
			$obj->license_key = $license_key;
			$obj->course_code = $course_code;
			$obj->topic_edge_id = $topic_edge_id;
			$obj->chapter_edge_id = $chapter_edge_id;
			$obj->component_edge_id = $component_edge_id;
			$obj->other = $other;
			$bookMarkArr[]=$obj;
		}
	 }
	$stmt->close();
	return $bookMarkArr;

}


function getComponentDtlByChapterEdgeId($parent_edge_id){
		$con = createConnection();
	
		$stmt = $con->prepare("SELECT tcd.category, tcd.cc_media_file, tcd.link_text FROM tbl_component_data tcd JOIN tbl_component tc ON tc.component_id = tcd.component_id WHERE tc.parent_edge_id = ? AND tc.status = 1 AND tc.scenario_subtype = 'Game'");	
		$stmt->bind_param("i",$parent_edge_id);		
		$stmt->execute();
		$stmt->bind_result($category, $cc_media_file, $link_text);
		$compDtl = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->game_name = $category;
			$bcm->game_file = $cc_media_file;
			$bcm->launch_file = $link_text;
			array_push($compDtl,$bcm);
		}
		$stmt->close();
		return $compDtl;
}

function aduroGetUserRecordingReview($con,$user_id,$param) {
    
	$rp_edge_id=$param->rp_edge_id;

	$review_list = array();
	$stmt = $con->prepare("SELECT comp_data_id,file_name,audio_file_name FROM tblx_user_recordings where rp_edge_id=? and user_id=?");
    $stmt->bind_param("ii",$rp_edge_id,$user_id);
	$stmt->execute();
    $stmt->bind_result($comp_data_id,$file_name,$audio_file_name);
    while($stmt->fetch()) {
        $obj = new stdclass();
			$obj->comp_data_id = $comp_data_id;
			$obj->file_name = $file_name;
			$obj->audio_file_name = $audio_file_name;
        array_push($review_list,$obj);
    }
    
	$stmt->close();
	
	closeConnection($con);
	
    return $review_list;
}
function aduroGetUserRecordingResponse($con,$user_id,$param) {
    
	$rp_edge_id=$param->rp_edge_id;

	$response_list = array();
	$stmt = $con->prepare("SELECT comp_data_id,file_name,audio_file_name,response,error_msg,response_date,status FROM tblx_user_recordings where rp_edge_id=? and user_id=?");
    $stmt->bind_param("ii",$rp_edge_id,$user_id);
	$stmt->execute();
    $stmt->bind_result($comp_data_id,$file_name,$audio_file_name,$response,$error_msg,$response_date,$status);
    while($stmt->fetch()) {
        $obj = new stdclass();
			$obj->comp_data_id = $comp_data_id;
			$obj->file_name = $file_name;
			$obj->audio_file_name = $audio_file_name;
			$obj->response = $response;
			$obj->error_msg = $error_msg;
			$obj->response_date = $response_date;
			$obj->status = $status;
        array_push($response_list,$obj);
    }
    
	$stmt->close();
	
	closeConnection($con);
	
    return $response_list;
}


//functions for daily goal
function aduroSetGoal($con,$user_id,$param)
{
	$stmt = $con->prepare("select id from tblx_user_goal_map where user_id=".$user_id);
	$stmt->execute();
	$stmt->bind_result($id);
	$stmt->fetch();
	$stmt->close();
	if(empty($id)){
		$query="INSERT INTO tblx_user_goal_map(user_id,goal_id,duration_id,created_date,modified_date) values(?,?,?,NOW(),NOW())";
		$stmt= $con->prepare($query);
		$stmt->bind_param("iii",$user_id,$param->goal_id,$param->duration_id);
		$stmt->execute();
		$stmt->close();
	}
	else
	{
		$stmt= $con->prepare("update tblx_user_goal_map set goal_id=?, duration_id=? where user_id=?");
		$stmt->bind_param("iii",$param->goal_id,$param->duration_id,$user_id);
		$stmt->execute();
		$stmt->close();
	}
}

function aduroGetGoal($con,$user_id){

	$stmt = $con->prepare("select goal_id, duration_id, created_date from tblx_user_goal_map where user_id=".$user_id);
	$stmt->execute();
	$stmt->bind_result($goal_id,$duration_id,$created_date);
	$stmt->fetch();
	$stmt->close();
	
	if(!empty($goal_id)){
		$obj = new stdclass();
		$obj->goal_id = $goal_id;
		$obj->duration_id = $duration_id;
		$obj->created_date = $created_date;
		return $obj;
	}
	else{
		return null;
	}		
 }
 
//Track daily goal and get streak count
function aduroGetDailyGoal($con,$user_id,$param){
		//file_put_contents('test/goal_get.txt',$param->course_code.'=='.$param->unique_code.'==='.$param->date_time);
	$daysArr=array();

	$stmt = $con->prepare("select goal_id, duration_id, created_date from tblx_user_goal_map where user_id=".$user_id);
	$stmt->execute();
	$stmt->bind_result($goal_id,$duration_id,$created_date);
	$stmt->fetch();
	$stmt->close();
	if(!empty($goal_id)){
		/* $query = "SELECT course FROM tblx_product_configuration WHERE institute_id = ? AND batch_id = ? AND product_id = ?";			
		$stmt = $con->prepare($query);  
		$stmt->bind_param("iii",$user_id,$center_id,$param->unique_code,$param->date_time);     
		$stmt->execute();
		$stmt->bind_result($duration_mnt,$course_code, $unique_code);
		$stmt->fetch();
		$stmt->close(); */
		
		$query = "SELECT SUM(actual_seconds) duration_ms , course_code,  unique_code FROM user_session_tracking WHERE user_id = ? AND course_code = ? AND unique_code = ? AND LENGTH(unique_code) >= 6 AND DATE(track_datettime) = ? AND session_type = 'CM' GROUP BY unique_code, course_code ORDER BY unique_code, course_code";			
		$stmt = $con->prepare($query);  
		$stmt->bind_param("isss",$user_id,$param->course_code,$param->unique_code,$param->date_time);     
		$stmt->execute();
		$stmt->bind_result($duration_mnt,$course_code, $unique_code);
		$stmt->fetch();
		$stmt->close();
	
		$bcm = new stdClass();
		$bcm->duration_mnt = $duration_mnt;
		$bcm->course_code = $course_code;
		$bcm->package_code = $unique_code;
		$bcm->user_id = $user_id;
		$bcm->goal_id = $goal_id;
		$bcm->duration_id = $duration_id;
		$bcm->created_date = $created_date;
		$streakCount= aduroGetStreakCount($con,$user_id,$created_date,$param);
		$daysArr = aduroGetDaysCompleted($con,$user_id,$created_date,$param);
		$bcm->days_completed = $daysArr;
		$bcm->streakCount = $streakCount;
		
		
		return $bcm;
	}
	else{
		return null;
	}		
}

function aduroGetStreakCount($con,$user_id,$created_date,$param){

	$start_date = $created_date;
	$end_date = date('Y-m-d');
	$streak_count=0;
	
	while (strtotime($start_date) <= strtotime($end_date)) {
		$query = "SELECT SUM(actual_seconds) duration_ms  FROM user_session_tracking WHERE user_id = ? AND course_code = ? AND unique_code = ? AND LENGTH(unique_code) >= 10 AND DATE(track_datettime) = ? AND session_type = 'CM' GROUP BY unique_code, course_code";			
		$stmt = $con->prepare($query);  
		$stmt->bind_param("isss",$user_id,$param->course_code,$param->unique_code,$start_date);     
		$stmt->execute();
		$stmt->bind_result($duration_ms);
		$stmt->fetch();
		$stmt->close();
		
		if(!empty($duration_ms) && ($duration_ms>0)){
			$streak_count++;
		}else{
			if(strtotime($start_date) != strtotime($end_date)){
				$streak_count = 0;
			}
		}
		
		$start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		$duration_ms=0;
	}
	
	return $streak_count;		
}

function aduroGetDaysCompleted($con,$user_id,$created_date,$param){

	$wrange = x_week_range(date("Y-m-d"));
	$arraydates=date_range($wrange[0],$wrange[1]);
	$compltedArray=array();
	for($i=0;$i<count($arraydates);$i++)
	{
		$query = "SELECT tracking_id FROM user_session_tracking WHERE user_id = ? AND DATE(track_datettime) = ?";			
		$stmt = $con->prepare($query);  
		$stmt->bind_param("is",$user_id,$arraydates[$i]);     
		$stmt->execute();
		$stmt->bind_result($tracking_id);
		$stmt->fetch();
		$stmt->close();
		if(!empty($tracking_id))
		{
		$compltedArray[$i]="1";
		}
		else
		{
		$compltedArray[$i]="0";
		}
		//file_put_contents("test/c.txt",$i."-".$compltedArray[$i]."\n",FILE_APPEND);
	}
	
	/*while (strtotime($start_date) <= strtotime($end_date)) {
		$query = "SELECT SUM(actual_seconds) duration_ms  FROM user_session_tracking WHERE user_id = ? AND course_code = ? AND unique_code = ? AND LENGTH(unique_code) >= 10 AND DATE(track_datettime) = ? AND session_type = 'CM' GROUP BY unique_code, course_code";			
		$stmt = $con->prepare($query);  
		$stmt->bind_param("isss",$user_id,$param->course_code,$param->unique_code,$start_date);     
		$stmt->execute();
		$stmt->bind_result($duration_ms);
		$stmt->fetch();
		$stmt->close();
		
		if(!empty($duration_ms) && ($duration_ms>0)){
			$streak_count++;
		}else{
			if(strtotime($start_date) != strtotime($end_date)){
				$streak_count = 0;
			}
		}
		
		$start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		$duration_ms=0;
	}*/
	
	return $compltedArray;		
}
 
//Return Messages
function alertMessage(){
    $arr = array(
       'LOGIN_FAILED' => 'Invalid username or password.',
        'LOGIN_FAILED_FOR_UNIQUE_CODE' => 'You need to register again.',
        'LOGIN_FAILED_FOR_INACTIVE_USER' => 'Account does not exist or deactivated. Please contact your administrator.',
        'REGISTER_INVALID_COURSE_CODE' => 'You entered an invalid Content Pack.',
        'REGISTER_INVALID_UNIQUE_CODE' => 'You entered an invalid Content Pack.',
        'REGISTER_UNIQUE_CODE_ALREADY_REGISTERED' => 'This Content Pack is already used by another user.',
		'REGISTER_UNIQUE_CODE_REACHED_MAX_DEVICE' =>  'Device limit exceeded for this Content Pack.',
		'EDGE_ID_MISSING' => 'Edge ID Missing.',
        'REGISTER_SUCCESS' =>  'Login credentials have been created and sent to your email. Please check your email.',
		'INVALID_PLATFORM' => 'You are not authorized to access Content Pack on this device.',
		'LICENSE_EXPIRED' => 'Content Pack has expired. ',
		'LOGIN_UNIQUE_CODE_REACHED_MAX_DEVICE' => 'Device limit exceeded for this Content Pack.',
		'INVALID_UNIQUE_CODE' => 'Invalid Content Pack.',
		'REGISTER_UNIQUE_CODE_ALREADY_REGISTERED_USER' => 'You have already registered with same credentials.',
        'NOT_ASSOCIATED_WITH_KEY' => 'You need to be registered with respective key.',
		'IS_BLOCK' =>  'Content Pack deactivated. Please contact administrator.',    
		'PACKAGE_CODE_USED' => 'Content Pack already used.',
		'REGISTER_INVALID_PACKAGE_CODE' =>  'Invalid Content Pack.', 
		'REGISTER_PACKAGE_CODE_ALREADY_REGISTERED_USER' =>  'You have already registered with same credentials.',
		'PACKAGE_EXPIRED' => 'Content Pack expired.',
		'REGISTER_UNIQUE_CODE_JSON_ERROR' => 'There is some network problem. Please try again later.',				
		'OTP_SENT' => 'OTP sent successfully.',		
		'OTP_FAILED' => 'OTP verification failed.',		
		'OTP_PASS' => 'OTP verified successfuly.',		
		'PASSWORD_CHANGED' => 'Password changed successfuly.',		
		'USER_DETAILS_UPDATED' => 'User details updated successfuly.',		
		'USER_PROFILE_COMPLETED' => 'Profile updated successfully.',
		'OTP_FAILED_USER_EXISTS' => 'User alerady exists. Please go to sign in page to sign in.',		
		'OTP_FAILED_USER_NOT_EXISTS' => 'User does not exist. Please go to sign up page to register.',		
		'OTP_EXPIRED' => 'OTP Expired. Please try again.',
		'OTP_FAILED_MULTIPLE' => 'OTP request failed. Please try again after 2 minutes.',
		'STUDENTS_NOT_FOUND' => 'Students have not registered yet.');
    
    return $arr; 
}

function publishText($str){
	$str=stripslashes($str);
	
	////check: $str=str_replace('"','&quot;',$str);
	$str=str_replace('&quot;',"'",$str);
	$str=str_replace("&#39;","'",$str);
	$str=str_replace("&#38;#39;","'",$str);
	//$str=str_replace("&#38;",'&',$str);
	$str=str_replace("&",'&#38;',$str);
	$str=str_replace('<','&lt;',$str);
	$str=str_replace('>','&gt;',$str);
	$str=str_replace('&#38;#38;','&#38;',$str);
	
	
	
	//$str= mb_convert_encoding($str, 'UTF-8', 'UTF-8');
	
	if($str==""){
		$str="";
	}
	return $str;
}
function aduroLogout($con,$user_id) {

	
			////Code for MixPanel/////
			$post_data = array();
			$parentObj = new stdClass();
			$parentObj->eventName = 'Logout';
			$parentObj->clientCode = 'CommonApp';
			
			$stmt = $con->prepare("SELECT a.loginid,first_name,middle_name,last_name from user_credential a join  user b on a.user_id=b.user_id where b.user_id=?");
			$stmt->bind_param("i",$user_id); 
			$stmt->execute(); 
			$stmt->bind_result($loginid,$fname,$mname,$lname);
            $stmt->fetch();
            $stmt->close();
			
			
			
			$data = new stdClass();
			$data->user_id = $user_id;
			$data->first_name = $fname;
			$data->last_name = $lname;
			$data->loginid = $loginid;
			$data->duration = '';
			$data->timestamp = date('Y-m-d H:i:s');
			$data->client_code = 'CommonApp';
			array_push($post_data,$data);
			//print_r($post_data);exit;
			$parentObj->userProps=$post_data;
			$MTResponse=sendToMixPanel($parentObj);
		
}

function aduroDeleteAccount($con,$user_id) {


	$stmt= $con->prepare("update user_credential set is_active = 0 where user_id = ? ");
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->close();
	
    $stmt= $con->prepare("update user set is_active = 0 where user_id = ? ");
	$stmt->bind_param("i",$user_id);
	$stmt->execute();
	$stmt->close();
	
	$obj = new stdClass();
	$obj->message = "Your account has been temporarily deleted. It will take 30 days to delete it permanently. Meanwhile, If you like to re-active your account please contact your administrator.";
	return $obj;
		
}

function sendToMixPanel($data)
{
/*$curl = curl_init();

$jsonData=json_encode($data);

//print_r($jsonData);exit;
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://65.0.142.231:6000/pushMixPanelData',
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
return $response; */
}

function aduroGetUserFileLocation($con, $user_id,$file_type) {

    $stmt = $con->prepare("INSERT INTO user_file_upload_location(user_id,file_type,date_of_entry) values(?,?,NOW())");
    $stmt->bind_param("ii",$user_id,$file_type);
    $stmt->execute();
    $file_loc_id = $con->insert_id;
    $stmt->close();
    $str_to_return = $user_id."-".$file_loc_id."-".$file_type;
    return $str_to_return;
}

//get resources by course code and file folder
function getResourcesByCourseFileFolder($con,$user_id,$course_code){
	
	$courseID = getCourseIdByCourseCode($course_code);

	if($courseID){
		$courseDetails=getCourseDetailsCourseId($courseID);
		$topicArr = $fileArr = array();
		$files = glob('course_pdf/'.$course_code.'/*pdf');
		foreach($files as $file) {
			$file_name = basename($file, ".pdf");
			$fileArr[]=$file_name;
		 }

		//$sorted_db = array_multisort($sort, SORT_ASC, $topicArr); 
		natsort($fileArr);
		
		foreach($fileArr as $key=>$file_name) {
			$file_path = 'course_pdf/'.$course_code.'/'.$file_name.'.pdf';
			$obj = new stdClass();
			$obj->file_name = $file_name;
			$obj->file_path = $file_path;
			array_push($topicArr,$obj);
		 }
		return $courseArr  = array("name" => stripslashes(publishText($courseDetails->title)), "edgeId" => $courseDetails->edge_id, 'pdfArr' => $topicArr, 'fileArr' => $fileArr); 
	}
	
}


function aduroGetMyPerfomance($con,$user_id,$course_code,$package_code){

 
	
	$courseID = getCourseIdByCourseCode($course_code);
	
	//$checkComponentarr = array();
	$courseDetails = getCourseDetailsCourseId($courseID);
	$number_of_chapters = 0;
	$topic_time_arr = $ttlCrsQCount = $ttlCrsCrrct = $assignment_score_arr = array();
	$total_time = 0;
	
	if($courseID){
		$courseName = publishText($courseDetails->title);
		$course_edge_id = getCourseEdgeIdByCourseId($courseID);//course edge id
		
		//get batch code
		$sql = "SELECT center_id,batch_id from tblx_batch_user_map WHERE user_server_id=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("i",$user_id);
		$stmt->execute();
		$stmt->bind_result($center_id,$batch_id);
		$stmt->fetch();
		$stmt->close();

		$sql = "SELECT batch_code from tblx_batch where center_id=? and batch_id=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("ii",$center_id,$batch_id);
		$stmt->execute();
		$stmt->bind_result($batch_code);  
		$stmt->fetch();
		$stmt->close(); 
		
		
		
		//get topics
		$topicArr = getTopicByCourseEdgeId($con,$course_edge_id);
		
		$number_of_topics =0;
		
		//get completed topic
		$number_of_completed_topic = 0;
		
		/* $stmt = $con->prepare("SELECT count(tcc.id) as 'cnt' from generic_mpre_tree gmt 
		JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
		JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
		JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tcc.course_code = ? AND tcc.user_id = ? AND tcc.completion = 'c'  AND tnd.tree_node_category_id=2  AND gmt.is_active = 1 AND cm.is_hide_resource='0'");
		$stmt->bind_param("isi",$course_edge_id,$course_code,$user_id);
		$stmt->execute();
		$stmt->bind_result($number_of_completed_chapter_corse);
		$stmt->fetch();
		$stmt->close();	 */ 
		
		$number_of_completed_chapter_corse =0;
		
		//total coins 
		$total_earned_coins= 0;
		$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ?");
		$stmt->bind_param("is",$user_id,$course_code);
		$stmt->execute();
		$stmt->bind_result($total_earned_coins);
		$stmt->fetch();
		$stmt->close();	 
		
		 $quiz_coins = $vocab_coins = $rp_coins = $speedreading_coins = $chapter_total_coin_arr= array();
		 $ttl_topic_coins = 0;
		
		foreach($topicArr as $key => $value){
			$topic_edge_id = $value->edge_id;
			$topic_name = $value->name;
			$quiz_topic_coins = $vocab_topic_coins = $rp_topic_coins = $speedreading_topic_coins = array();
			$global_assign_earned_coins = $assign_count_global = $ttl_class_assign = $class_assign_earned_coins = $topic_time = 0;
			
			if($value->assessment_type==""){
				
				
				$chapter_Array = $chapter_time_arr = $chpaterCrrctArr = $chpaterQCountArr = array();
				$assign_count = $assignment_score = $topic_coins_earned = 0;
				
				$singleChapterArr = getChpaterByTopicEdgeId($con,$topic_edge_id);
				$chapter_count = count($singleChapterArr);
				
				foreach($singleChapterArr as $chapterArrKey=>$chapterArrVal){
					
					$chapterEdgeId = $chapterArrVal->edge_id;
					$ttlChpaterCrrct = $ttlChpaterQCount = 0;

					//get coins
					$scenarios = getScenarioByChapterId($chapterEdgeId);

						$totalScenarios=count($scenarios);
										
						if($totalScenarios > 0){
						
							foreach($scenarios as $keys=>$values){
							
								
									if($values->scenario_subtype=="Quiz"){
									
										$compEdgeIdQ = $values->component_edge_id;
										
										$stmt = $con->prepare("SELECT count(tq.id) as qCount from  tbl_component tc JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
										where tc.component_id=? and tq.question_type!='RA-TT-AU'");
										$stmt->bind_param("i",$values->component_id);
										$stmt->execute();
										$stmt->bind_result($qCount);
										$stmt->fetch();
										$stmt->close();
										
										$quiz_coins[] = $qCount;
										$quiz_topic_coins[] = $qCount;
						
									}
									if($values->scenario_subtype=="Conversation Practice")
									{
									
										$compEdgeIdR = $values->component_edge_id;
										
										$stmt = $con->prepare("SELECT count(tv.id) as coin_count from tbl_vocabulary tv JOIN tbl_component tc on tv.parent_edge_id = tc.parent_edge_id where tc.component_edge_id = ?  and tc.status = 1");
										$stmt->bind_param("i",$compEdgeIdR);
										$stmt->execute();
										$stmt->bind_result($total_vocab_coin);
										$stmt->fetch();
										$stmt->close();	
										
										$vocab_coins[] = $total_vocab_coin;
										$vocab_topic_coins[] = $total_vocab_coin;

									
									}													
										
									if($values->scenario_subtype=="Role-play")
									{			
										
										$rolePlayEdgeId = $values->component_edge_id;
									
										$stmt = $con->prepare("SELECT count(id) as videoCount from  tbl_component_data where component_id =? and scenario_answer_media_file!=''");
										$stmt->bind_param("i",$values->component_id);
										$stmt->execute();
										$stmt->bind_result($videoCount);
										$stmt->fetch();
										$stmt->close();
										
										$rp_coins[] = $videoCount;
										$rp_topic_coins[] = $videoCount;
					
									}
									
									if($values->scenario_subtype=="SpeedReading"){
									
										$compEdgeIdQ = $values->component_edge_id;
										$stmt = $con->prepare("SELECT count(tq.id) as qCount from  tbl_component tc JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
										where tc.component_id=?");
										$stmt->bind_param("i",$values->component_id);
										$stmt->execute();
										$stmt->bind_result($srQCount);
										$stmt->fetch();
										$stmt->close();
										
										$speedreading_coins[] = $srQCount;
										$speedreading_topic_coins[] = $srQCount;
										
									
									}

							}
						
						//}
							
						}

					$chapter_time = $chapter_coin = $chapter_per = 0;
					
					//Chapter time	
				
					$stmt = $con->prepare("SELECT COALESCE(SUM(ust.actual_seconds), 0) from user_session_tracking ust where ust.session_type = 'CM'  AND ust.user_id=? and ust.session_id IN(SELECT distinct component_edge_id from tbl_component tc where tc.parent_edge_id = ?)");
					$stmt->bind_param("ii",$user_id,$chapterArrVal->edge_id);
					$stmt->execute();
					$stmt->bind_result($chapter_time);
					$stmt->fetch();
					$stmt->close();	
					
					$chapter_time_arr[] = $chapter_time;
					
					$number_of_chapters++;
				
				
				}
				
				$topic_time = array_sum($chapter_time_arr); 
				$topic_time_arr[] = $topic_time; 
				//Total topic earned coins
				$stmt = $con->prepare("SELECT COALESCE(SUM(user_coins), 0) from tblx_user_coins where user_id = ? AND course_code = ? AND topic_edge_id = ?");
				$stmt->bind_param("isi",$user_id,$course_code,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($topic_coins_earned);
				$stmt->fetch();
				$stmt->close();	 
			
				
				//topic global asignment total coins
				$sql = "SELECT count(id) as assign_count from tblx_assignments where course_code=? and topic_edge_id=? and assignment_type='global'";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("si",$course_code,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($assign_count_global);
				$stmt->fetch();
				$stmt->close();	
				$ttl_global_assignment_coins = $assign_count_global*10;
				 
				//topic global asignment earned coins
				$sql = "SELECT COALESCE(SUM(evaluated_rating), 0) from tblx_assignments ta JOIN tblx_assignment_evaluation tae ON ta.id=tae.assignment_id where ta.topic_edge_id=? and tae.student_id=? and assignment_type='global'";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("ii",$topic_edge_id,$user_id);
				$stmt->execute();
				$stmt->bind_result($global_assign_earned_coins);
				$stmt->fetch();
				$stmt->close();	
								
				
				//class asignment total coins
				$sql = "SELECT count(id) as assign_count from tblx_assignments where course_code=? and batch_code=? and topic_edge_id=? and assignment_type='teacher'";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("ssi",$course_code,$batch_code,$topic_edge_id);
				$stmt->execute();
				$stmt->bind_result($ttl_class_assign);
				$stmt->fetch();
				$stmt->close();	
				$ttl_class_assign_coins = $ttl_class_assign*10; 
				
				//class asignment earned coins
				$sql = "SELECT COALESCE(SUM(evaluated_rating), 0) from tblx_assignments ta JOIN tblx_assignment_evaluation tae ON ta.id=tae.assignment_id where ta.course_code=? and ta.batch_code=? and ta.topic_edge_id=? and tae.student_id=? and assignment_type='teacher'";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("ssii",$course_code,$batch_code,$topic_edge_id,$user_id);
				$stmt->execute();
				$stmt->bind_result($class_assign_earned_coins);
				$stmt->fetch();
				$stmt->close();

				//Get completed chapter
				$topic_complt="";
				$stmt = $con->prepare("select completion from tblx_component_completion where user_id=? AND component_edge_id=?");
				$stmt->bind_param("ii",$user_id,$topic_edge_id);		
				$stmt->execute();
				$stmt->bind_result($topic_complt);
				$stmt->fetch();
				$stmt->close(); 
				if($topic_complt=='c'){
					$topic_per = 100;
					$number_of_completed_chapter = $chapter_count; 
					$number_of_completed_topic++;
				}elseif($topic_complt=='nc'){
					$stmt = $con->prepare("SELECT count(tcc.id) as 'cnt' FROM generic_mpre_tree gmt
								JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_parent = ? AND tcc.completion = 'c'  AND tcc.user_id = ? AND tnd.tree_node_category_id=2 AND cm.is_hide_resource='0'");
					$stmt->bind_param("ii",$topic_edge_id,$user_id);
					$stmt->execute();
					$stmt->bind_result($number_of_completed_chapter);
					$stmt->fetch();
					$stmt->close();	
					$number_of_completed_chapter = !empty($number_of_completed_chapter)?$number_of_completed_chapter:0;
					$topic_per  = ($number_of_completed_chapter*100)/$chapter_count;
					$topic_per = round($topic_per);
					
				}else{
					$topic_per = 0;
					$$number_of_completed_chapter =0;
					
				}
				
							
				//Total topic coins
				$quiz_topic_coins = array_sum($quiz_topic_coins); 
				$vocab_topic_coins = array_sum($vocab_topic_coins); 
				$rp_topic_coins = array_sum($rp_topic_coins); 
				$speedreading_topic_coins = array_sum($speedreading_topic_coins); 
				
				$ttl_topic_coins = $quiz_topic_coins + $vocab_topic_coins + $rp_topic_coins + $speedreading_topic_coins; 
				
				
				
				
				$topic_Array[] = array("topic_per"=>$topic_per,"chapter_complete_count"=>$number_of_completed_chapter, "chapter_count"=>$chapter_count, "topic_time"=> $topic_time, "topic_name"=>$topic_name,"topic_coins_earned"=>$topic_coins_earned,"ttl_topic_coins" => $ttl_topic_coins,"ttl_global_assign_coins"=>$ttl_global_assignment_coins,"global_assign_coins_earned"=>$global_assign_earned_coins,"ttl_class_assign_coins"=>$ttl_class_assign_coins,"class_assign_earned_coins"=>$class_assign_earned_coins,"componentType" => "module");
				$number_of_topics++;
			}
		
		}
		
		//Course total time
		$total_time = array_sum($topic_time_arr);
		//Get total coins
		array_filter($quiz_coins); 
		array_filter($vocab_coins); 
		array_filter($rp_coins); 
		array_filter($speedreading_coins); 
		
		
		$quiz_coins = array_sum($quiz_coins); 
		$vocab_coins = array_sum($vocab_coins); 
		$rp_coins = array_sum($rp_coins); 
		$speedreading_coins = array_sum($speedreading_coins); 
		$chapter_total_coins = array_sum($chapter_total_coin_arr); 
		
		//Assignment coins
		 $sql = "SELECT count(id) as assign_count from tblx_assignments where course_code=?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("s",$course_code);
		$stmt->execute();
		$stmt->bind_result($assign_count);
		$stmt->fetch();
		$stmt->close();	

		$assignment_coins = $assign_count*10;
		
		
		$total_coins = $quiz_coins + $vocab_coins + $rp_coins + $speedreading_coins + $assignment_coins + $chapter_total_coins;
		
				
		$earned_per = ($total_earned_coins*100)/$total_coins;
		
		$earned_per = !empty($earned_per)?$earned_per:0;
		
		$badgeNo = 1;
		
		if($earned_per<=30){
			$badgeNo = 1;
		}else if($earned_per>30 && $earned_per<=50){
			$badgeNo = 2;
		}else if($earned_per>50 && $earned_per<=80){
			$badgeNo = 3;
		}else if($earned_per>80){
			$badgeNo = 4;
		}
		 
		//$retObj = new stdClass();	$center_id,$batch_id
		$retObj->batch_id = $batch_id;  	
		$retObj->center_id = $center_id;  	
		$retObj->batch_code = $batch_code;  	
		$retObj->badgeNo = $badgeNo;	
		$retObj->course_code = $course_code;	
		$retObj->course_id = $courseID;	
		$retObj->topic_complete_count = $number_of_completed_topic;	
		$retObj->topic_count = $number_of_topics;	
		$retObj->total_coins = $total_earned_coins;	
		$retObj->total_coins_avail = $total_coins;	
		$retObj->total_time = $total_time;	
		$retObj->chapter_complete_count = $number_of_completed_chapter_corse;	
		$retObj->chapter_count = $number_of_chapters;		
		$retObj->topic_Array = $topic_Array;		
		//$retObj->componentArr = $checkComponentarr;	
		$retObj->user_id = $user_id;	
		//$retObj->chapterSkillArr = $chapterSkillArr;	
		//file_put_contents('test/cgh.txt',print_r($retObj,true)); 
		return $retObj;
	}
	
}

function x_week_range($date) {
    $ts = strtotime($date);
    
	if(date('D')=='Mon')
	{   
	 $start = (date('w', $ts) == 0) ? $ts : strtotime('this monday', $ts);
	}
	else
	{
	$start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
	}
    return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next sunday', $start)));
}

function aduroSetUserCoins($con,$user_id,$course_code,$topic_edge_id,$chapter_edge_id,$component_edge_id,$component_data,$component_type, $user_coins){

   
	$sql2 = "select id from tblx_user_coins where user_id =? AND course_code =? AND topic_edge_id =? AND chapter_edge_id =? AND component_edge_id =? AND component_data =? AND 	component_type =?";
	$stmt2 = $con->prepare($sql2);
	$stmt2->bind_param("isiiisi",$user_id,$course_code,$topic_edge_id,$chapter_edge_id,$component_edge_id,$component_data,$component_type);
	$stmt2->execute();
	$stmt2->bind_result($coin_id);
	$stmt2->fetch();
	$stmt2->close();
	if($coin_id==""){
		$stmt = $con->prepare("INSERT INTO tblx_user_coins (user_id,course_code, topic_edge_id,chapter_edge_id,component_edge_id,component_data,component_type,user_coins,date_created) values(?,?,?,?,?,?,?,?,NOW())");
		$stmt->bind_param("isiiisii",$user_id,$course_code,$topic_edge_id,$chapter_edge_id,$component_edge_id,$component_data,$component_type, $user_coins);
		$stmt->execute();
		$stmt->close();
		if ($con->error) {
			return false;
		}
	/* $parentObj = new stdClass();
	$parentObj->eventName = 'TrackVocab';
	$parentObj->clientCode = $class_name;
	
	$data = new stdClass();
	$data->user_id = $user_id;
	$data->first_name = $fname;
	$data->last_name = $lname;
	$data->loginid = $loginid;
	$data->course_id = $course_id;
	$data->course_code = $course_code;
	$data->course_name = $title;
	$data->vocabulary_id = $component_edge_id;
	$data->vocabulary_word = $word;
	$data->user_action = '';
	$data->duration = '';
	$data->chpater_id = $parent_edge_id;
	$data->chapter_name = $code;
	$data->topic_id = $topic_edge_id;
	$data->topic_name = $topic_name;
	$data->timestamp = date('Y-m-d H:i:s');
	$data->client_code = $class_name;
	array_push($post_data,$data);

	$parentObj->userProps=$post_data;
	$MTResponse=sendToMixPanel($parentObj); */
	}
    return true;
}


//Insert answer and test attempt data
function aduroCentralLicensingTrackAnswerAttempt($con,$user_id,$test_uniqid, $ques_uniqid,$ans_uniqid,$date_ms, $platform, $unique_code, $course_code, $essay_answer, $av_media_files,$user_response,$correct,$attempt_no) {
  
	$updatest = $con->prepare("insert INTO temp_ans_push(user_id,test_id,ques_id,ans_id,time_sp,fld_datetime,platform,unique_code,course_code,essay_answer, av_media_files,user_response,correct,attempt_no) values(?,?,?,?,?,NOW(),?,?,?,?,?,?,?,?)");
    $updatest->bind_param("isssissssssii",$user_id,$test_uniqid, $ques_uniqid,$ans_uniqid,$date_ms,$platform,$unique_code, $course_code,$essay_answer,$av_media_files,$user_response,$correct,$attempt_no);
    $updatest->execute();

}
function aduroGetMaxTestAttemptNo($con,$test_uniqid,$user_id) {
	
	$attempt_no= 0;$id ='';
	
	$stmt = $con->prepare("select MAX(id),MAX(attempt_no) from user_test_score where user_id=? and test_id=?");
	$stmt->bind_param("ii",$user_id,$test_uniqid);
	$stmt->execute();
	$stmt->bind_result($id,$attempt_no);
	$stmt->fetch();
	$stmt->close();
	$Obj = new stdClass();
	$Obj->id = $id;
	$Obj->attempt_no = $attempt_no;
	return $Obj;	
} 

function aduroCentralLicensingTrackTest($con,$test_id,$course_id,$course_code,$course_edge_id,$topic_edge_id,$chapter_edge_id, $user_id, $no_of_ques, $ttl_correct, $ttl_incorrect, $score_per, $avg_time_sp, $ttl_time_sp, $type_of_test, $plateform,$unique_code,$attempt_no,$last_score_id) {
	
	
    $updatest = $con->prepare("insert INTO user_test_score(test_id,course_id,course_code,course_edge_id,topic_edge_id,chapter_edge_id, user_id, no_of_ques, ttl_correct, ttl_incorrect, score_per, avg_time_sp, ttl_time_sp, type_of_test, plateform,unique_code,attempt_no) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $updatest->bind_param("iisiiiiiiiiiiissi",$test_id,$course_id,$course_code,$course_edge_id,$topic_edge_id,$chapter_edge_id, $user_id, $no_of_ques, $ttl_correct, $ttl_incorrect, $score_per, $avg_time_sp, $ttl_time_sp, $type_of_test, $plateform,$unique_code,$attempt_no);
    $updatest->execute();
	$id = $con->insert_id;
	if($id!=''){
		$parentObj = new stdClass();
		$parentObj->id = $id;
		$parentObj->test_id = $test_id;
		$parentObj->course_id = $course_id;
		$parentObj->course_code = $course_code;
		$parentObj->course_edge_id = $course_edge_id;
		$parentObj->topic_edge_id = $topic_edge_id;
		$parentObj->chapter_edge_id = $chapter_edge_id;
		$parentObj->user_id = $user_id;
		$parentObj->no_of_ques = $no_of_ques;
		$parentObj->ttl_correct = $ttl_correct;
		$parentObj->ttl_incorrect = $ttl_incorrect;
		$parentObj->score_per = $score_per;
		$parentObj->avg_time_sp = $avg_time_sp;
		$parentObj->ttl_time_sp =$ttl_time_sp;
		$parentObj->type_of_test = $type_of_test;
		$parentObj->plateform = $plateform;
		$parentObj->attempt_no = $attempt_no;
		$parentObj->action = 'add_user_score';
		if($last_score_id!=""){
			$parentObj->last_score_id = $last_score_id;
			$parentObj->first_action = 'del_user_score';
		}
		//sendToCollection($parentObj);
	
	}

} 



function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}


function aduroGetCaptcha($con,$device_id){
	$con = createConnection();
	$captcha=generate_string(6);
	$stmt = $con->prepare("SELECT device_id from tbl_captcha where device_id=?");	
	$stmt->bind_param("s",$device_id);
	$stmt->execute();
	$stmt->bind_result($deviceId);
	$stmt->fetch();	
	$stmt->close();	

	if(empty($deviceId)){

		$query="INSERT INTO tbl_captcha(device_id,captcha) values('$device_id', '$captcha')";
		//file_put_contents('test/dev1.txt',$query);
		$stmt= $con->prepare($query);
		$stmt->execute();
		$stmt->close();
	}
	else
	{
		$stmt= $con->prepare("update tbl_captcha set captcha='$captcha' where device_id='$device_id'");
		$stmt->execute();
		$stmt->close();
	}

	$obj = new stdclass();
	$obj->captcha = $captcha;
	return $obj;
}


 
function generate_string($strength) {
    $input = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
	$input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

function aduroValidateCaptcha($con,$userCaptach,$device_id){
	$con = createConnection();
	$newcaptcha=generate_string(6);
	$stmt = $con->prepare("SELECT captcha from tbl_captcha where device_id=?");	
	$stmt->bind_param("s",$device_id);
	$stmt->execute();
	$stmt->bind_result($captcha);
	$stmt->fetch();	
	$stmt->close();	
	//file_put_contents("test/ab.txt",$userCaptach."-".$captcha);
	if(!empty($userCaptach) & (strtolower($userCaptach) == strtolower($captcha)))
	{
		$stmt= $con->prepare("update tbl_captcha set captcha='$newcaptcha' where device_id='$device_id'");
		$stmt->execute();
		$stmt->close();
		$sr = new ServiceResponse("SUCCESS",0,null);
		$sr->retVal = new stdClass();
		$sr->retVal->captach = $newcaptcha;
		return $sr;
	}
	else
	{
		$stmt= $con->prepare("update tbl_captcha set captcha='$newcaptcha' where device_id='$device_id'");
		$stmt->execute();
		$stmt->close();
		$sr = new ServiceResponse("FAILURE",0,null);
		$sr->retVal = new stdClass();
		$sr->retVal->captach = $newcaptcha;
		return $sr;
	}

}


//////////////
function aduroPackageInfo($con, $dataArr, $param, $user_id){
		$alert_msg_arr = aduroAlertMessage();
		$curDate = date('Y-m-d');
	//////////////////////// check license ///////////////////////	
		if($dataArr->STATUS == 'FAILURE'){
			//file_put_contents("test/dev2.txt",$dataArr->STATUS);
			updateCentralLicenseStatus($param->package_code, $dataArr->LIC_STATUS, $loginid = '');
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['REGISTER_INVALID_PACKAGE_CODE'];
			return $sr;
		}
		
	//////////////////////// check blocked ///////////////////////
		if($dataArr->IS_BLOCK == 'yes'){
			updateCentralLicenseStatus($param->package_code, $dataArr->LIC_STATUS, $loginid = '');
			$sr = new ServiceResponse("BLOCKED",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['IS_BLOCK'];
			return $sr;
		}			
		///////////////////////////////// check valid platform for license ///////////////////////////////
		$platformArr = explode(',',$dataArr->PLATFORM);		
		if(!in_array(strtolower($param->platform), $platformArr)){
			updateCentralLicenseStatus($param->package_code, $dataArr->LIC_STATUS, $loginid = '');
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['INVALID_PLATFORM'];
			return $sr;
		}
		
		/////////////////////// check expiry date /////////////////////////////////////////	
		if(strtotime($curDate) > strtotime($dataArr->EXP_DATE) && $dataArr->EXP_DATE != ""){
			updateCentralLicenseStatus($param->package_code, $dataArr->LIC_STATUS, $loginid = '');
			$sr = new ServiceResponse("EXPIRED",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['PACKAGE_EXPIRED'];
			return $sr;
		}
		
		////////////////////////////////////// check max usage of devices ////////////////					
		$stmt = $con->prepare("SELECT COUNT(DISTINCT(device_id)) lic_used FROM tbl_app_device_used WHERE BINARY license = ?");
		$stmt->bind_param("s",$dataArr->package_code);
		$stmt->execute();
		$stmt->bind_result($lic_used);
		$stmt->fetch();
		$stmt->close();	
		
		if($lic_used >= $dataArr->DEVICE_COUNT){
			updateCentralLicenseStatus($param->package_code, $dataArr->LIC_STATUS, $loginid = '');
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['REGISTER_UNIQUE_CODE_REACHED_MAX_DEVICE'];
			return $sr;
		}
		
		$group_code = 'APP-1000';
		$stmt = $con->prepare("insert into course_codes (`group_code`,`unique_code`,`usage_count`,`created_date`,`modified_date`,`use_date`) values (?,?,?,NOW(),NOW(),NOW()) ON DUPLICATE KEY update use_date = NOW(), modified_date = NOW() ");
        $stmt->bind_param("ssi",$group_code,$param->package_code, $dataArr->USAGE_COUNT);
        $stmt->execute();
        $stmt->close();

		$stmt = $con->prepare("insert into code_user_usage (`unique_code`,`user_id`,`use_date`,`created_date`,`modified_date`) values (?,?,NOW(),NOW(),NOW()) ON DUPLICATE KEY update use_date = NOW(), modified_date = NOW() ");
        $stmt->bind_param("si",$param->package_code,$user_id );
        $stmt->execute();
        $stmt->close();
		
		$stmt = $con->prepare("SELECT COUNT(*) record_exists FROM tbl_app_device_used WHERE license = ? AND device_id = ?");
		$stmt->bind_param("ss",$param->package_code, $param->deviceId);
		$stmt->execute();
		$stmt->bind_result($record_exists);
		$stmt->fetch();
		$stmt->close();
		
		if(!$record_exists){
			$stmt= $con->prepare("INSERT INTO tbl_app_device_used(app_id,license,device_id,consumer_id,device_type) values('1000', ?, ?, ?, ? )");
			$stmt->bind_param("ssis",$param->package_code,$param->deviceId, $user_id, $param->platform);
			$stmt->execute();
			$stmt->close();
		}
		
		
		//////////////     calculate  duration_in_days    ///////////////////////////////////
		if($dataArr->EXP_DATE != ""){
			$duration_in_days = count_days($curDate, $dataArr->EXP_DATE);
		}else{
			$duration_in_days = $dataArr->EXP_DAYS;
		}

		if($duration_in_days == 0){
			$duration_in_days = 1;
		}
		
		$fetechstmt = $con->prepare("select session_id from api_session where user_id=? and valid_upto > NOW()");
		$fetechstmt->bind_param("i",$user_id);
		$fetechstmt->bind_result($ssid);
		$fetechstmt->execute();
		$fetechstmt->fetch();
		if(!isset($ssid) || $ssid===null || $ssid ==="") {
			$part1 = md5($user_id);
			$part2 = uniqid();
			$entireKey = $part1.$part2;
			$ssid = md5($entireKey);
		}
		$fetechstmt->close();
		
		
		$stmt = $con->prepare("SELECT first_name,middle_name,last_name, loginid from user_credential a, user b where a.user_id='".$user_id."' and a.user_id=b.user_id");
		$stmt->bind_result($fname,$mname,$lname, $loginid);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		 $updatest = $con->prepare("insert INTO api_session(user_id,session_id,valid_upto,app_version) values(?,?,DATE_ADD(NOW(), INTERVAL +4 HOUR),?) ON DUPLICATE KEY UPDATE session_id=?,valid_upto=DATE_ADD(NOW(), INTERVAL +4 HOUR)") or die ('some issue here '.$con->error);
		$xcv = $updatest->bind_param("isis",$user_id,$ssid,$param->appVersion,$ssid);
		$updatest->execute();
		$mode = 'packageInfo';
		$sr = new ServiceResponse("SUCCESS",0,null);		
		$licenseArr = aduroCentralLicenseKeyJson($con, $duration_in_days, $device_status=1, $platform=1, $dataArr->PRODUCT, $dataArr->PRODUCT_CODE, $dataArr->APP_COURSES, $param->package_code, $user_id,$mode, $param->email_id);
		if(count($licenseArr) > 0){
			if($dataArr->APP_COURSES != ""){
				$expCourse = explode(',', $dataArr->APP_COURSES);
				
				$i = 0;
				foreach($expCourse as $value){

					$stmt= $con->prepare("INSERT INTO tbl_user_license_course_map(user_id,package_code,course_code,product,created_date) values(?, ?, ?, ?, NOW())");
					$stmt->bind_param("isss", $user_id, $param->package_code, $value, $dataArr->PRODUCT);
					$stmt->execute();
					$stmt->close();
					$i++;
				}
				
			}
			$status = 4;
			updateCentralLicenseStatus($param->package_code, $status, $loginid);
		}else{
			updateCentralLicenseStatus($param->package_code, $dataArr->LIC_STATUS, $loginid = '');
			$sr = new ServiceResponse("FAILURE",0,null);
			$sr->retVal = new stdClass();
			$sr->retVal->msg = $alert_msg_arr['REGISTER_UNIQUE_CODE_JSON_ERROR'];
			return $sr;
		}
		
		$packageInfoArr = array();
		$packageInfoArr[] = $licenseArr;
		$retVal->packageInfo = $packageInfoArr;
		$retVal->token = $ssid;
		$retVal->name = $fname." ";
		if(isset($lname))
			$retVal->name .= $lname;
		$retVal->user_id = $user_id;
		$sr->setval($retVal);
		////error_log("ssid--------------------- ".json_encode($sr));
        return $sr;
}


function checkBlockCenters($con, $center_id){
    
    $stmt = $con->prepare("SELECT client_id from tblx_center where center_id=$center_id");
	$stmt->execute();
	$stmt->bind_result($client_id);
	$stmt->fetch();
	$stmt->close();
	return $client_id;
    
}

function getAduroBatchAndCenterForUser($con, $user_id ){
    
    static $bcrr = array();
    
    if( isset($bcrr[$user_id]) ){
        return $bcrr[$user_id];
    }
    
    $query = "SELECT center_id, batch_id from tblx_batch_user_map where user_server_id = ? AND status = 1 ";			
    $stmt = $con->prepare($query);   
    $stmt->bind_param('i', $user_id);
    $stmt->bind_result($center_id, $batch_id);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    
    if( empty($center_id) || empty($batch_id) ){
        $center_id = 0;
        $batch_id = 0;
    }
    $bcrr[$user_id] = array('center_id' => $center_id, 'batch_id' => $batch_id);
    return $bcrr[$user_id];
    
}


function getParentEdgeId($edge_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT tree_node_parent FROM generic_mpre_tree 
							WHERE  edge_id=?");
	$stmt->bind_param("i",$edge_id);
	$stmt->execute();
	$stmt->bind_result($tree_node_parent);
	$stmt->fetch();
	$stmt->close();
	closeConnection($con);
	return $tree_node_parent;
	
}
 function getSuperRootEdgeId($edge_id){
		$con2 = createConnection();
		$stmt = $con2->prepare("SELECT tree_node_super_root FROM generic_mpre_tree 
								WHERE  edge_id=?");
		$stmt->bind_param("i",$edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_super_root);
		$stmt->fetch();
		$stmt->close();
		//echo $tree_node_parent;
		closeConnection($con2);
		
		return $tree_node_super_root;
		
	  }
function getComponentType($edge_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT scenario_subtype FROM tbl_component WHERE  component_edge_id=?");
	$stmt->bind_param("i",$edge_id);
	$stmt->execute();
	$stmt->bind_result($scenario_subtype);
	$stmt->fetch();
	$stmt->close();
	closeConnection($con);
	return $scenario_subtype;
	
}


function getUserClientId($con,$user_id){
		$stmt = $con->prepare("SELECT user_client_id FROM user 
								WHERE  user_id=?");
		$stmt->bind_param("i",$user_id);
		$stmt->execute();
		$stmt->bind_result($user_client_id);
		$stmt->fetch();
		$stmt->close();
		
		return $user_client_id;
}
function updateCentralLicenseStatus($licenseKey, $status, $loginid){
	global $serviceURL;
	//$request = curl_init('http://courses.englishedge.in/englishedge-admin/service.php');
	$request = curl_init($serviceURL);
	curl_setopt($request, CURLOPT_POST, true);
	curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'licenseUpdate', 'license_value' => $licenseKey, 'license_status' => $status, 'loginid' => $loginid));
	curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
	$res = curl_exec($request);
	curl_close($request);
}

function aduroAlertMessage(){
    $arr = array(
        'LOGIN_FAILED' => 'Invalid username or password.',
        'LOGIN_FAILED_FOR_UNIQUE_CODE' => 'You need to register again.',
        'LOGIN_FAILED_FOR_INACTIVE_USER' => 'Your account is deactivated. Please contact your administrator.',
        'REGISTER_INVALID_COURSE_CODE' => 'You entered an invalid Content Pack.',
        'REGISTER_INVALID_UNIQUE_CODE' => 'You entered an invalid Content Pack.',
        'REGISTER_UNIQUE_CODE_ALREADY_REGISTERED' => 'This Content Pack is already used by another user.',
		'REGISTER_UNIQUE_CODE_REACHED_MAX_DEVICE' =>  'Device limit exceeded for this Content Pack.',
		'EDGE_ID_MISSING' => 'Edge ID Missing.',
        'REGISTER_SUCCESS' =>  'Login credentials have been created and sent to your email. Please check your email.',
		'INVALID_PLATFORM' => 'You are not authorized to access Content Pack on this device.',
		'LICENSE_EXPIRED' => 'Content Pack has expired. ',
		'LOGIN_UNIQUE_CODE_REACHED_MAX_DEVICE' => 'Device limit exceeded for this Content Pack.',
		'INVALID_UNIQUE_CODE' => 'Invalid Content Pack.',
		'REGISTER_UNIQUE_CODE_ALREADY_REGISTERED_USER' => 'You have already registered with same credentials.',
        'NOT_ASSOCIATED_WITH_KEY' => 'You need to be registered with respective key.',
		'IS_BLOCK' =>  'Content Pack deactivated. Please contact administrator.',    
		'PACKAGE_CODE_USED' => 'Content Pack already used.',
		'REGISTER_INVALID_PACKAGE_CODE' =>  'Invalid Content Pack.', 
		'REGISTER_PACKAGE_CODE_ALREADY_REGISTERED_USER' =>  'You have already registered with same credentials.',
		'PACKAGE_EXPIRED' => 'Content Pack expired.',
		'REGISTER_UNIQUE_CODE_JSON_ERROR' => 'There is some network problem. Please try again later.',				
		'OTP_SENT' => 'OTP sent successfully.',		
		'OTP_FAILED' => 'OTP verification failed.',		
		'OTP_PASS' => 'OTP verified successfuly.',		
		'PASSWORD_CHANGED' => 'Password changed successfuly.',		
		'USER_DETAILS_UPDATED' => 'User details updated successfuly.',		
		'OTP_FAILED_USER_EXISTS' => 'User alerady exists. Please go to sign in page to sign in.',		
		'OTP_FAILED_USER_NOT_EXISTS' => 'User does not exist. Please go to sign up page to register.',		
		'OTP_EXPIRED' => 'OTP Expired. Please try again.',
		'OTP_FAILED_MULTIPLE' => 'OTP request failed. Please try again after 2 minutes.'
		
        );
    
    return $arr;
}
