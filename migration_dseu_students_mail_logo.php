<?php
@session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'author_ilt');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', 'Liqvid@123');

ini_set('max_execution_time', 99999);

$arrSections = ['A','B','C','D','E','F','G','H','I','J'];
$sectionMaxCount=40;


class DBConnection
{
	protected static $_DB_Conn;

	private function __construct()
	{
		try {
			self::$_DB_Conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		} catch (Exception $e) {
			echo "Oops. Something has gone wrong. Please try again.";
			//echo "ERROR: " . $e->getMessage();
		}
	}

	public static function getInstance()
	{
		if (!self::$_DB_Conn) {
			new DBConnection();
		}
		return self::$_DB_Conn;
	}
	public static function createConn()
	{
		try {
			if (!self::$_DB_Conn) {
				$dbConn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if (!$dbConn) {
					throw new exception(mysqli_error($dbConn));
				}
				self::$_DB_Conn = $dbConn;
			}
			return self::$_DB_Conn;
		} catch (Exception $e) {
			// echo "Class DB Error : ".$e->getMessage();
			echo "Oops. Something has gone wrong. Please try again.";
		}
	}
}

function createConnection()
{

	$host = "localhost";
	$dbname = "author_ilt";
	$dbuser =  "root";
	$dbpass  = "Liqvid@123";


	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	//$con->set_charset("utf8");
	if (mysqli_connect_errno()) {
		//print mysqli_connect_errno()."ERROR IN MYSQL";
		print "Oops. Something has gone wrong. Please try again.";
		return null;
	}
	return $con;
}
function createConnection2()
{

	$host = "localhost";
	$dbname = "author_ilt";
	$dbuser =  "root";
	$dbpass  = "Liqvid@123";


	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$con2 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	//$con->set_charset("utf8");
	if (mysqli_connect_errno()) {
		//print mysqli_connect_errno()."ERROR IN MYSQL";
		print "Oops. Something has gone wrong. Please try again.";
		return null;
	}
	return $con2;
}
$con = createConnection();
$con2 = createConnection2();
function closeConnection($con)
{
	mysqli_close($con);
}

function getCenterByDseuCenterCode($dseu_center_code){
	$con = createConnection();
    $sql = "Select center_id from tblx_center where dseu_center_code='$dseu_center_code'";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	return $center_id;	
}
function getBatchByCenterAndName($batchName,$center_id){
	$con = createConnection();
    $sql = "Select batch_id from tblx_batch where center_id='$center_id' AND batch_name='$batchName'";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($batch_id);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	return $batch_id;
	
}
$host=$_SERVER['HTTP_HOST'];
if($host=='iltnew.adurox.com'){
	$host='ilt-staging.adurox.com';
}

function getRegionLogo($host){
	$con = createConnection();
	$sql="Select id,region_name,region_logo,is_region_logo_show,is_app_logo_show,is_secondary_logo,secondary_logo from tblx_region where domain_url =?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("s",$host);
	$stmt->bind_result($id,$region_name,$region_logo,$is_region_logo_show,$is_app_logo_show,$is_secondary_logo,$secondary_logo);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	return ['region_id'=>$id,'region_logo'=>$region_logo,'is_region_logo_show'=>$is_region_logo_show,'region_name'=>$region_name];
}
$regionData=getRegionLogo($host);
//echo '<pre>';print_r($regionData['region_logo']);exit;
if($regionData['is_region_logo_show']=='1'){
	define('applogo','region/'.$regionData['region_logo']);
  }else{
	define('applogo','logo.png');  
  }
 define('APP_NAME',$regionData['region_name']);	
 define('REGIONID',$regionData['region_id']);	

 /* selected center mail send*/
function getAllCenterUserByDseuCenterCode($dseu_center_code){
	$con = createConnection();
    $sql = "Select center_id from tblx_center where dseu_center_code='$dseu_center_code'";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($center_id);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	return $center_id;
	
}

function getCenterUserMapByDseuCenterId($center_id){
	$con = createConnection();
	$userArr=array();
    $sql = "Select user_id from user_center_map where center_id='$center_id'";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($user_id);
	$stmt->execute();
	while($stmt->fetch()) {
	 $userArr[]=$user_id;
	}
	$stmt->close();
	return $userArr;
	
}
function getAllCenterUserMapByDseuCenterId($center_ids){
	$con = createConnection();
	$userArr=array();
    $sql = "Select user_id from user_center_map where center_id IN($center_ids)";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($user_id);
	$stmt->execute();
	while($stmt->fetch()) {
	 $userArr[]=$user_id;
	}
	$stmt->close();
	return $userArr;
	
}
function userdetails($user_id){
	try{	
		$con = createConnection();
		$stmt = $con->prepare("SELECT  c.client_id, u.first_name,u.last_name, u.roll_no,u.date_of_birth ,rd.role_definition_id, rd.name roleName , u.email_id, u.address_id, u.profile_pic,u.user_from,urm.user_group_id,u.user_client_id,u.firstTime_login
								FROM user u
								JOIN user_role_map urm ON urm.user_id = u.user_id
								JOIN role_definition rd ON rd.role_definition_id = urm.role_definition_id
								LEFT JOIN client c ON c.user_group_id = urm.user_group_id
								WHERE u.user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($client_id,$first_name , $last_name,$roll_no, $date_of_birth,$role_definition_id, $roleName , $email_id,$address_id,$profile_pic,$user_from,$user_group_id,$user_client_id,$firstTime_login);
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
			
			$stmt = $con->prepare("Select name as center_name,description,status,region from tblx_center where center_id = ?");
			$stmt->bind_param("i",$center_id);
			$stmt->bind_result($center_name,$center_description,$status,$region);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		
		$stmt = $con->prepare("Select loginid,password,is_active,expiry_date from user_credential where user_id = ?");
		$stmt->bind_param("i",$user_id);
		$stmt->bind_result($loginid,$password,$is_active,$expiry_date);
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
		$user->name = $first_name." ".$last_name;
		$user->date_of_birth = $date_of_birth;
		$user->roleName = $roleName;
		$user->email_id = $email_id;
		$user->address_id = $address_id;
		$user->profile_pic = $profile_pic;
		$user->user_from = $user_from;
		$user->user_group_id = $user_group_id;
		$user->gender = $gender;
		$user->mobile = $phone;
		$user->country = $country;
		$user->system_name = $system_name;
		$user->user_client_id = $user_client_id;
		$user->firstTime_login = $firstTime_login;
		$user->is_active = $is_active;
		$user->expiry_date = $expiry_date;
		$user->loginid = $loginid;
		$user->password = $password;
		$user->center_description = $center_description;
		$user->center_status = $status;
		$user->region = $region;
		$user->rno = $roll_no;
		$user->role_definition_id = $role_definition_id;
		$user->ip_address = $user_ip;
		closeConnection($con);
		//echo "<pre>";print_r($user);exit;
        if(REGIONID==$region){
		  return $user;
		}else{
			  return false;
		}
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}
//$dseu_center_code='1106113';//DSEUVV-41 DSEUP- 40,DSEUMC-39
//$center_id=getAllCenterUserByDseuCenterCode($dseu_center_code);

$centerStr='39,40,41';
$getUserIdArr=getAllCenterUserMapByDseuCenterId($centerStr);
echo "count ";echo count($getUserIdArr);echo "<br> ";
//echo "<pre>1";print_r($getUserIdArr);exit;
$sentUserStr='971854,971855,971856,975510,975511,975512,975513,975514,975515,975516,975517,975518,975519,975520,975521,975522,975523,975524,975525,975526,975527,975528,975529,975530,975531,975532,975533,975534,975535,975536,975537,975538,975539,975540,975541,975542,975543,975544,975545,975546,975547,975548,975549,975550,975551,975552,975553,975554,975555,975556,975557,975558,975559,975560,975561,975562,975563,975564,975565,975566,975567,975568,975569,975570,975571,975572,975573,975574,975575,975576,975577,975578,975579,975580,975581,975582,975583,975584,975585,975586,975587,975588,975589,975590,975591,975592,975593,975594,975595,975596,975597,975598,975599,975600,975601,975602,975603,975604,975605,975606,user_id,975607,975608,975609,975610,975611,975612,975613,975614,975615,975616,975617,975618,975619,975620,975621,975622,975623,975624,975625,975626,975627,975628,975629,975630,975631,975632,975633,975634,975635,975636,975637,975638,975639,975640,975641,975642,975643,975644,975645,975646,975647,975648,975649,975650,975651,975652,975653,975654,975655,975656,975657,975658,975659,975660,975661,975662,975663,975664,975665,975666,975667,975668,975669,975670,975671,975672,975673,975674,975675,975676,975677,975678,975679,975680,975681,975682,975683,975684,975685,975686,975687,975688,975689,975690,975691,975692,975693,975694,975695,975696,975697,975698,975699,975700,975701,975702,975703,975704,975705,975706,975707,975708,975709,975710,975711,975712,975713,975714,975715,975716,975717,975718,975719,975720,975721,975722,975723,975724,975725,975726,975727,975728,975729,975730,975731,975732,975733,975734,975735,975736,975737,975738,975739,975740,975741,975742,975743,975744,975745,975746,975747,975748,975749,975750,975751,975752,975753';
 $sentUserArr=Explode(',',$sentUserStr);
foreach($getUserIdArr as $key=>$value){
   $sno=$key+1;
	//echo "<pre>";print_r($value);exit;
	if (in_array($value, $sentUserArr)){
		//echo "<pre>exit ";print_r($value);//exit;
	}else{
		if($sno>472){
			$userData= userdetails($value);
			if($userData!=''){ 
			 //echo "<pre>1";print_r($userData);//exit;
			 echo $key+1;echo "  "; echo $userData->email_id;echo "<br>";
			  mailSenderUser1($userData->rno,$userData->loginid, $userData->password,$userData->name,$userData->mobile,$userData->email_id,$userData->center_name,$userData->role_definition_id,$userData->region,$userData->user_id);
		   }
		}
	}
}
//mailSenderUser1(100,'sarika.yadav@liqvid.com','Password@123','Sarika','9999999999','sarika.yadav@liqvid.com','Demo','2','5','100'); //student migration code	
function mailSenderUser1($rno,$loginid, $password,$name,$mobile,$email,$center_name,$role_id,$region_id,$user_id){
		    $con = createConnection();

			$client_id='46';
			$logoimg=applogo;
			if($rno!=''){
				$roll_no=$rno;
			}else{
				$roll_no=$email;
			}
		   

			
				echo "roleId: ";echo $role_id;	echo "<br>";
				if(isset($_SERVER['HTTPS'])) {
					$http='https://';
				}
				else{ $http='http://';}
				$dirmName= dirname($_SERVER['PHP_SELF']);
				$dirmName=explode('/',$dirmName);
				if($dirmName[1]!=""){
					$dirmName='/'.$dirmName[1];
				}
				else{$dirmName='';} 
				//$globalLink=$http.$_SERVER['HTTP_HOST'].$dirmName;
				$globalLink=$http.$_SERVER['HTTP_HOST'];
				//echo $globalLink;
				 $logo=$globalLink.'/images/'.$logoimg;
				$ProductName=APP_NAME;
				//$filepath= $globalLink.'/library/phpMailer/mail.php'; 
				$filepath= 'library/phpMailer/mail.php'; 
				include_once($filepath);
				//echo $filepath;
				//echo $email;
				$subject = "$ProductName Login Credential";
				echo  $str="<table style='' cellspacing='0' cellpadding='0' width='100%' bgcolor='#fff'><tbody><tr style='background-color:#fff;height:30px' ><td style='background-color:#dddddd;padding:5px 5px 0px 5px;height:30px' valign='middle'><span style='text-align:left;height:30px'><img style='max-width: 80px;height: auto;' src='$logo' alt='' border='0'/></span><span style='position:relative;top:-20px;color:#fff;display:inline-block;padding:5px 5px 2px 5px;font-size:10px;'>&nbsp;</span></td></tr><tr><td style='text-align:center; vertical-align:middle'><div style='padding:40px 10px 20px 10px;'><div style='padding:0px 0px 10px 0px;'><span style='font-size:18px;'>Dear <span style='font-size:18px;font-weight:bold;padding-left:5px'>$name</span></span></div><div style='padding:10px 0px 10px 0px;'><span style='font-size:18px;'>Welcome to </span><span style='font-size:18px;font-weight:bold;padding-left:5px'>$ProductName</span><span style='font-size:18px;'>. Following are your credentials.</span></div></div><div style='padding:30px 10px 10px 10px;'><p style='font-size: 13px;color: #8a8989'> Roll No / Email :<span style='font-weight:bold;padding-left:5px'> $roll_no</span> </p><p style='font-size: 13px;color: #8a8989'> Password : <span style='font-weight:bold;padding-left:5px'>$password</span></p></div><div style='padding:0px 10px 10px 10px;border-bottom:solid thin #ccc;'> <p>LMS URL: <a href='https://dseu.adurox.com/' target='_blank'>https://dseu.adurox.com/</a></p></div></td></tr></tbody></table>";
			    //$status = sendMailDseu($email, $subject, $str,$ProductName,$user_id); 
				// exit;
			
			
}




/*
//echo "<pre>";print_r($centerArr);exit;
$url="https://english.dseu.ac.in/api/student_data_Macmillan.php";

$file = $url;
$data = file_get_contents($file);
$result =''; //json_decode($data);


function createSlot($students){
	global $sectionMaxCount, $arrSections;
    $count = 1;
    $index = 0;
    $slots = [];
    $slots_arr_count = [];
    $slots_arr = [];
    $students_arr = [];
    $slot_code_arr = [];


 
  
    foreach ($students as $key => $student) {
    	
    		   

    	if(!in_array($student->slot_code, $slots)){
    		$slots[] = $student->slot_code;
    		$index = 0;

    		$final_students[$student->slot_code] = array();
    		
    		$slot_code_arr[$student->slot_code] = array();

    		$section_arr[$student->slot_code] = array();
    		$students_arr[$student->slot_code][] = $student->{'R.no'};

    		$final_students[$student->slot_code][] = $student;

    		$slot_code_arr[$student->slot_code][] = $student->{'slot_code'};

    		$slots_arr_count[$student->slot_code] = ['slot'=>$student->slot_code,'section_count'=>1,'roll_no'=>implode(',', $students_arr[$student->slot_code]),'slot_code'=>implode(',', $slot_code_arr[$student->slot_code]), 'final_students'=>$final_students[$student->slot_code]];

    	}else{
    		
    		$index = isset($slots_arr['sec_'.$student->slot_code])?$slots_arr['sec_'.$student->slot_code]:0;
    		$section_count = $slots_arr_count[$student->slot_code]['section_count'] + 1;
    		$students_arr[$student->slot_code][] = $student->{'R.no'};
    		$slot_code_arr[$student->slot_code][] = $student->{'slot_code'};
    		$final_students[$student->slot_code][] = $student;
    		$slots_arr_count[$student->slot_code] = ['slot'=>$student->slot_code,'section_count'=>$section_count,'roll_no'=>implode(',', $students_arr[$student->slot_code]),'slot_code'=>implode(',', $slot_code_arr[$student->slot_code]), 'final_students'=>$final_students[$student->slot_code] ];
    	}

   

    	if($count == $sectionMaxCount){
    		$index++;
    		$count = 1;
    	}

    

    	
    	$count++;
    }

 $moveStudentArr =  [];
 $moveStudentArr1 =  [];

     foreach ($slots_arr_count as $key => $value) {
     	$slots_arr_count[$key]['student_section'] = $value['section_count'] / $sectionMaxCount;
     	$indexCount = floor($value['section_count'] / $sectionMaxCount);
     		$slots_arr_count[$key]['student_section_index'] = $indexCount;
     		$slots_arr_count[$key]['slot_updated'] = $arrSections[$indexCount];
     	$rem = $value['section_count'] % $sectionMaxCount;

         $slots_arr_count[$key]['student_section_reminder'] = $rem;
    
      
       $final_students =  $value['final_students'];
       unset($slots_arr_count[$key]['final_students']);



       	
       	

       for ($i = 0; $i<=$indexCount; $i++) {
       	$movedStudents = moveElement($final_students,0,$sectionMaxCount);
      
    


       	 foreach ($movedStudents as $k1 => $mStu) {
			$dseu_center_code=$mStu->{'Centre ID'};
			$center_id=getCenterByDseuCenterCode($dseu_center_code);
			$movedStudents[$k1]->{'rno'} = $mStu->{'R.no'};
			$movedStudents[$k1]->{'name'} = $mStu->{'Name'};
			$movedStudents[$k1]->{'mobile'} = $mStu->{'Mobile number'};
			$movedStudents[$k1]->{'email_id'} = $mStu->{'Email id'};
			$movedStudents[$k1]->{'father_name'} = $mStu->{'Father name'};
            $movedStudents[$k1]->{'mother_name'} = $mStu->{'Mothers name'};
			
			$movedStudents[$k1]->{'slot_id'} = $mStu->{'slot_code'};		
       	  	$movedStudents[$k1]->{'slot_code'} = $mStu->{'slot_code'}. '-' .$arrSections[$i];
			$movedStudents[$k1]->{'batch_name'} = "Slot-".$mStu->{'slot_code'};
			$batchName= "Slot-".$mStu->{'slot_code'};
			$batch_id=getBatchByCenterAndName($batchName,$center_id);
			$movedStudents[$k1]->{'section'} = $arrSections[$i];
			$movedStudents[$k1]->{'center_id'} = $center_id;
			$movedStudents[$k1]->{'batch_id'} = $batch_id;
       	  } 
		 
		  $moveStudentArr["Slot-".$mStu->{'slot_code'}]= $movedStudents;
		   $moveStudentArr1[$mStu->{'slot_id'}]= $movedStudents;

          	$slots_arr_count[$key]['students'][$i] = $movedStudents;
       }
       


        if($rem < 20 && $rem > 0 && $value['section_count'] > $sectionMaxCount){
        	
        		
        		

     	    $rem_students = array_pop($slots_arr_count[$key]['students']);

     	    $allStudents = $slots_arr_count[$key]['students'];
     	    
     	   

     	 $all_stu_count = count($allStudents);
     	   $rem_stu_count = count($rem_students);
     	    $stu_rem = floor($rem_stu_count / $all_stu_count);
     	    
     	     for($cnt = 0; $cnt<$all_stu_count; $cnt++){
            	for($cnt2 = 0; $cnt2 < $stu_rem; $cnt2++){
            		$tmp_res_stu = removeArrEle($rem_students);
            		$tmp_res_stu->{'slot_code'} = $allStudents[$cnt][0]->{'slot_code'};
            		$tmp_res_stu->{'section'} = $allStudents[$cnt][0]->{'section'};
            		$tmp_res_stu->{'slot_id'} = $allStudents[$cnt][0]->{'slot_id'};
            		$tmp_res_stu->{'batch_name'} = $allStudents[$cnt][0]->{'batch_name'};
            		$tmp_res_stu->{'batch_id'} = $allStudents[$cnt][0]->{'batch_id'};
            		
					$allStudents[$cnt][] = $tmp_res_stu;


            	}
            	
            }

             
            foreach ($rem_students as $key4 => $rem_stu_rem) {
                          
            		$rem_stu_rem->{'slot_code'} = $allStudents[count($allStudents) - 1][0]->{'slot_code'};
            		$rem_stu_rem->{'section'} = $allStudents[count($allStudents) - 1][0]->{'section'};
                    $rem_stu_rem->{'slot_id'} = $allStudents[count($allStudents) - 1][0]->{'slot_id'};
            		$rem_stu_rem->{'batch_name'} = $allStudents[count($allStudents) - 1][0]->{'batch_name'};
            		$rem_stu_rem->{'batch_id'} = $allStudents[count($allStudents) - 1][0]->{'batch_id'};
            		
				   //print_r($rem_students);  
            	$allStudents[count($allStudents) - 1][] = $rem_stu_rem; 
            	unset($rem_students[$key4]);
            }
			
            $slots_arr_count[$key]['students']=$allStudents;

     	}else{
     		$allStudents = $slots_arr_count;
     	
     	}

     			
     }
    

//echo '<pre>';
   // print_r($allStudents);

   // exit;
   
  //echo '<pre>';print_r($moveStudentArr);
   return ['students_data'=>$allStudents,'moveStudent'=>$moveStudentArr,'moveStudent1'=>$moveStudentArr1];
}


function removeArrEle(&$array){
	return current(array_splice($array, 0, 1));
}

function moveElement(&$array, $a, $b) {
    return array_splice($array, $a, $b);  
}



if($result->status=='200'){
	$students = createSlot($result->data->studentData_Macmillan);
	//echo '<pre>';print_r($students['students_data']);
	//echo '<pre>';print_r($students['moveStudent']);
	
	$batchData2=$students['moveStudent1'];
	$studentData1=$students['students_data'];//exit;
     //echo '<pre>';print_r($batchData2);//exit;
	foreach ($batchData2 as $k2 => $btd) {

		$batchCount=count($btd);
	   for ($j = 1; $j<=$batchCount; $j++) {
         $batchData1=$btd[$j-1];
	   }
        // echo '<pre>';print_r($batchData1 );
         $slot_id=$batchData1->slot_id;
		
		//echo '<pre>';print_r($studentData1[$slot_id]['students']);exit;
		$studentData2=$studentData1[$slot_id]['students'];
		foreach ($studentData2 as $k4 => $std2) {
			//echo '<pre>';print_r($std2);
			foreach ($std2 as $k3 => $std) {
				//echo '<pre>';print_r($std);
				$rno=$std->rno;
				$mobile=$std->mobile ;
				$name=$std->name;
				$email=$std->email_id;
				$father_name=$std->father_name;
				$mother_name=$std->mother_name;
				$slot_id=$std->slot_id;
				$section=$std->section;
				$center_id=$std->center_id;
				$batch_id=$std->batch_id;
				//mailSenderUser($rno,$name,$mobile,$email,$father_name,$mother_name,$slot_id,$section,$center_id,$batch_id); //student migration code
			
			}
		}

		
	}
	
}

*/
  
 // mailSenderUser(100,'Sarika','','sarika.yadav@liqvid.com','','','','','',''); //student migration code
			
function mailSenderUser($rno,$name,$mobile,$email,$father_name,$mother_name,$slot_id,$section,$center_id,$batch_id){
		    $con = createConnection();

		   
			//$email_id='ST_'.$rno.'_'.$slot_id.'_'.$section.'_'.$center_id.'_'.$batch_id.'@dseu.com';echo '<br>';//exit;
			$stmt = $con->prepare("Select user_id,roll_no from user WHERE roll_no='$rno'");
			$stmt->bind_result($user_id,$roll_no);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close(); 
			$stmt = $con->prepare("Select loginid,password,is_active,expiry_date from user_credential where user_id = ?");
			$stmt->bind_param("i",$user_id);
			$stmt->bind_result($loginid,$password,$is_active,$expiry_date);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
			$client_id='46';
			$logoimg=applogo;
			if(!empty($roll_no)){
				echo "Insert";exit;
				/*if(isset($_SERVER['HTTPS'])) {
					$http='https://';
				}
				else{ $http='http://';}
				$dirmName= dirname($_SERVER['PHP_SELF']);
				$dirmName=explode('/',$dirmName);
				if($dirmName[1]!=""){
					$dirmName='/'.$dirmName[1];
				}
				else{$dirmName='';} 
				//$globalLink=$http.$_SERVER['HTTP_HOST'].$dirmName;
				$globalLink=$http.$_SERVER['HTTP_HOST'];
				//echo $globalLink;
				 $logo=$globalLink.'/images/'.$logoimg;
				$ProductName=APP_NAME;
				//$filepath= $globalLink.'/library/phpMailer/mail.php'; 
				$filepath= 'library/phpMailer/mail.php'; 
				include_once($filepath);
				//echo $filepath;
				//echo $email;
				$subject = "$ProductName Login Credential";
				 $str="<table style='' cellspacing='0' cellpadding='0' width='100%' bgcolor='#fff'><tbody><tr style='background-color:#fff;height:30px' ><td style='background-color:#dddddd;padding:5px 5px 0px 5px;height:30px' valign='middle'><span style='text-align:left;height:30px'><img style='max-width: 80px;height: auto;' src='$logo' alt='' border='0'/></span><span style='position:relative;top:-20px;color:#fff;display:inline-block;padding:5px 5px 2px 5px;font-size:10px;'>&nbsp;</span></td></tr><tr><td style='text-align:center; vertical-align:middle'><div style='padding:40px 10px 20px 10px;'><div style='padding:0px 0px 10px 0px;'><span style='font-size:18px;'>Dear <span style='font-size:18px;font-weight:bold;padding-left:5px'>$name</span></span></div><div style='padding:10px 0px 10px 0px;'><span style='font-size:18px;'>Welcome to </span><span style='font-size:18px;font-weight:bold;padding-left:5px'>$ProductName</span><span style='font-size:18px;'>. Following are your credentials.</span></div></div><div style='padding:30px 10px 10px 10px;'><p style='font-size: 13px;color: #8a8989'> Roll No / Email :<span style='font-weight:bold;padding-left:5px'> $roll_no</span> </p><p style='font-size: 13px;color: #8a8989'> Password : <span style='font-weight:bold;padding-left:5px'>$password</span></p></div><div style='padding:0px 10px 10px 10px;border-bottom:solid thin #ccc;'></div></td></tr></tbody></table>";
			     $status = sendMailDseu($email, $subject, $str,$ProductName); 
			
                */
			   
			}else{
				/*$roll_no=$rno;
				echo "update";
				if(isset($_SERVER['HTTPS'])) {
					$http='https://';
				}
				else{ $http='http://';}
				$dirmName= dirname($_SERVER['PHP_SELF']);
				$dirmName=explode('/',$dirmName);
				if($dirmName[1]!=""){
					$dirmName='/'.$dirmName[1];
				}
				else{$dirmName='';} 
				//$globalLink=$http.$_SERVER['HTTP_HOST'].$dirmName;
				$globalLink=$http.$_SERVER['HTTP_HOST'];
				//echo $globalLink;
				 $logo=$globalLink.'/images/'.$logoimg;
				$ProductName=APP_NAME;
				//$filepath= $globalLink.'/library/phpMailer/mail.php'; 
				$filepath= 'library/phpMailer/mail.php'; 
				include_once($filepath);
				//echo $filepath;
				//echo $email;
				$subject = "$ProductName Login Credential";
				 $str="<table style='' cellspacing='0' cellpadding='0' width='100%' bgcolor='#fff'><tbody><tr style='background-color:#fff;height:30px' ><td style='background-color:#dddddd;padding:5px 5px 0px 5px;height:30px' valign='middle'><span style='text-align:left;height:30px'><img style='max-width: 80px;height: auto;' src='$logo' alt='' border='0'/></span><span style='position:relative;top:-20px;color:#fff;display:inline-block;padding:5px 5px 2px 5px;font-size:10px;'>&nbsp;</span></td></tr><tr><td style='text-align:center; vertical-align:middle'><div style='padding:40px 10px 20px 10px;'><div style='padding:0px 0px 10px 0px;'><span style='font-size:18px;'>Dear <span style='font-size:18px;font-weight:bold;padding-left:5px'>$name</span></span></div><div style='padding:10px 0px 10px 0px;'><span style='font-size:18px;'>Welcome to </span><span style='font-size:18px;font-weight:bold;padding-left:5px'>$ProductName</span><span style='font-size:18px;'>. Following are your credentials.</span></div></div><div style='padding:30px 10px 10px 10px;'><p style='font-size: 13px;color: #8a8989'> Roll No / Email :<span style='font-weight:bold;padding-left:5px'> $roll_no</span> </p><p style='font-size: 13px;color: #8a8989'> Password : <span style='font-weight:bold;padding-left:5px'>$password</span></p></div><div style='padding:0px 10px 10px 10px;border-bottom:solid thin #ccc;'></div></td></tr></tbody></table>";
			     $status = sendMailDseu($email, $subject, $str,$ProductName); 
				 exit;
                    */
				
			}
}