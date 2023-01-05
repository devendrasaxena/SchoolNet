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
$sectionMaxCount=30;


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
function createBatch($batchName,$center_id,$slot_id,$slot_section){
	$con = createConnection();
	$batch_id=getBatchByCenterAndName($batchName,$center_id);
	
	if(empty($batch_id)){
		echo $batch_id." empty";//exit;
		echo "<br>";
		$sql = "SELECT MAX(batch_id) as maxBatchId from tblx_batch where center_id='".$center_id."'";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($maxBatchId);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		$section=$maxBatchId;
		$section=$section+1;
		$batch_code = "CN-".$center_id.'-B'.$section;
		//// Now Adding  Batch 
		$sql = "INSERT INTO tblx_batch(center_id,batch_id, batch_code, batch_name,status,batch_type,date_created,is_default) VALUES('$center_id', '$section','$batch_code','$batchName',1,'',NOW(),'1')";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$batchID =$section;
		$stmt->close(); 
	     // registerTeacher($slot_id,$slot_section,$center_id,$batchID);
		return $batchID;
		
	}else{
		//echo "not empty ";echo 'Center '.$center_id.' Batch '.$batch_id.' Slot '.$slot_id;
		//echo "<br>";
		$batch_code = "CN-".$center_id.'-B'.$batch_id;
	
		$stmt = $con->prepare("UPDATE `tblx_batch` SET `batch_code`= '$batch_code' ,is_default='1' WHERE batch_id='$batch_id' AND center_id='$center_id'");
		//$stmt->execute();
		$stmt->close();
		
		//registerTeacher($slot_id,$slot_section,$center_id,$batch_id);
	}
	
	
}
function shortcode_strings(){
    $length_of_minstring='3';
    $length_of_string='8';
	$str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
    return substr(str_shuffle(strtolower($str_result)), 0, $length_of_string);
} 
function registerTeacher($slot_id,$section,$center_id,$batch_id){
		    $con = createConnection();
            $name='Teacher';
			$mobile='';
		    $password='Password@123';
			$client_id='46';
			$email_id='TH_'.$slot_id.'_'.$section.'_'.$center_id.'_'.$batch_id.'@dseu.com';echo '<br>';//exit;
		
			//$stmt = $con->prepare("Select user_id from user WHERE ex_role_definition_id='1' AND slot='$slot_id' AND section='$section'");
			
			$stmt = $con->prepare("Select user_id from user WHERE email_id='$email_id'");
			$stmt->bind_result($user_id);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close(); 
			echo $user_id;//exit;
			if(empty($user_id)){
				echo "Insert";
				////Select the client to user group id 
				$stmt = $con->prepare("Select user_group_id from client WHERE client_id=$client_id");
				$stmt->bind_result($user_group_id);
				$stmt->execute();
				$stmt->fetch();
				$stmt->close(); 
				$client_group_id = $user_group_id;
				
			   //// Now Adding   address 
				$sql3 = "INSERT INTO address_master(phone,updated_by,created_date) VALUES('$mobile','1',NOW())";
				$stmt = $con->prepare($sql3);	
				$stmt->execute();
				$address_id =$con->insert_id;
				$stmt->close(); 
			   
			 //// Now Adding  Assest 
			   $sql4 = "INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())";
			   $stmt = $con->prepare($sql4);	
			   $stmt->execute();
			   $asset_id =$con->insert_id;
			   $stmt->close(); 
	 
			//// Now Adding  Admin Login 
			   $sql5 = "insert into user(first_name,email_id,address_id,profile_pic,updated_by,created_date,user_client_id,section,slot,ex_center_id,ex_phone,ex_role_name,ex_user_group_id,ex_role_definition_id,ex_password,ex_loginid) values('$name','$email_id','$address_id','$asset_id',1, NOW(),'$client_id','$section','$slot_id','$center_id','$mobile','Teacher','$client_group_id','1','$password','$email_id')";
			   $stmt = $con->prepare($sql5);	
			   $stmt->execute();
			   $user_id =$con->insert_id;
			   $stmt->close(); 		

			//// Adding user and center map 
			   $sql6 = "insert into user_center_map(user_id,center_id,client_id,created_date) values('$user_id','$center_id','$client_id',NOW())";
			   $stmt = $con->prepare($sql6);	
			   $stmt->execute();
			   $stmt->close();
			   
			   //// Adding Admin Credentials 
			   $sql7 = "insert into user_credential(user_id,loginid,password,updated_by,created_date) values('$user_id','$email_id','$password',1,NOW())";
			   $stmt = $con->prepare($sql7);	
			   $stmt->execute();
			   $stmt->close(); 



			  //// Adding  into role map group 
			  $role_type="1";//center Admin
			   $sql8 = "insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values('$user_id','$role_type','$client_group_id',1,1,NOW())";
			   $stmt = $con->prepare($sql8);	
			   $stmt->execute();
			   $stmt->close(); 
			   //// Adding batch user map 

			   $sql9 = "insert into tblx_batch_user_map(center_id,batch_id,user_id,user_server_id,status,date_created) values('$center_id','$batch_id','$user_id','$user_id',1,NOW())";
			   $stmt = $con->prepare($sql9);	
			   $stmt->execute();
			   $stmt->close();
			   
			}else{
				echo "update";
			   $sql5 = "update user SET email_id='$email_id',ex_loginid='$email_id' WHERE user_id='$user_id'";
			   $stmt = $con->prepare($sql5);	
			   $stmt->execute();
			   $stmt->close(); 		
                $sql5 = "update user_credential SET loginid='$email_id'  WHERE user_id='$user_id'";
			   $stmt = $con->prepare($sql5);	
			   $stmt->execute();
			   $stmt->close(); 		

			  $sql9 = "update  tblx_batch_user_map SET batch_id='$batch_id' WHERE center_id='$center_id' AND user_id='$user_id'";
			   $stmt = $con->prepare($sql9);	
			   //$stmt->execute();
			   $stmt->close();
			}
}
function registerUser($rno,$name,$mobile,$email,$father_name,$mother_name,$slot_id,$section,$center_id,$batch_id){
		    $con = createConnection();
            $pass = substr(str_shuffle("23456789abcdefghjkmnpqrstvwxyz"), 0, 6);
		    $password=$pass;
			$client_id='46';
			//$email_id='ST_'.$rno.'_'.$slot_id.'_'.$section.'_'.$center_id.'_'.$batch_id.'@dseu.com';echo '<br>';//exit;
			$stmt = $con->prepare("Select user_id,roll_no from user WHERE roll_no='$rno'");
			$stmt->bind_result($user_id,$roll_no);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close(); 
			if(empty($roll_no)){
				echo "Insert";
				////Select the client to user group id 
				$stmt = $con->prepare("Select user_group_id from client WHERE client_id=$client_id");
				$stmt->bind_result($user_group_id);
				$stmt->execute();
				$stmt->fetch();
				$stmt->close(); 
				$client_group_id = $user_group_id;
				
			   //// Now Adding   address 
				$sql3 = "INSERT INTO address_master(phone,updated_by,created_date) VALUES('$mobile','1',NOW())";
				$stmt = $con->prepare($sql3);	
				$stmt->execute();
				$address_id =$con->insert_id;
				$stmt->close(); 
			   
			 //// Now Adding  Assest 
			   $sql4 = "INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())";
			   $stmt = $con->prepare($sql4);	
			   $stmt->execute();
			   $asset_id =$con->insert_id;
			   $stmt->close(); 
	 
			//// Now Adding  Admin Login 
			   $sql5 = "insert into user(first_name,email_id,address_id,profile_pic,updated_by,created_date,user_client_id,roll_no,fathers_name,mothers_name,section,slot,ex_center_id,ex_phone,ex_role_name,ex_user_group_id,ex_role_definition_id,ex_password,ex_loginid) values('$name','$email','$address_id','$asset_id',1, NOW(),'$client_id','$rno','$father_name','$mother_name','$section','$slot_id','$center_id','$mobile','Learner','$client_group_id','2','$password','$rno')";
			   $stmt = $con->prepare($sql5);	
			   $stmt->execute();
			   $user_id =$con->insert_id;
			   $stmt->close(); 		

			//// Adding user and center map 
			   $sql6 = "insert into user_center_map(user_id,center_id,client_id,created_date) values('$user_id','$center_id','$client_id',NOW())";
			   $stmt = $con->prepare($sql6);	
			   $stmt->execute();
			   $stmt->close();
			   
			   //// Adding Admin Credentials 
			   $sql7 = "insert into user_credential(user_id,loginid,password,updated_by,created_date) values('$user_id','$rno','$password',1,NOW())";
			   $stmt = $con->prepare($sql7);	
			   $stmt->execute();
			   $stmt->close(); 



			  //// Adding  into role map group 
			  $role_type="2";//center Admin
			   $sql8 = "insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values('$user_id','$role_type','$client_group_id',1,1,NOW())";
			   $stmt = $con->prepare($sql8);	
			   $stmt->execute();
			   $stmt->close(); 
			   //// Adding batch user map 

			   $sql9 = "insert into tblx_batch_user_map(center_id,batch_id,user_id,user_server_id,status,date_created) values('$center_id','$batch_id','$user_id','$user_id',1,NOW())";
			   $stmt = $con->prepare($sql9);	
			   $stmt->execute();
			   $stmt->close();
			   
			}else{
				echo "update";
			   $sql5 = "update user SET section='$section',slot='$slot_id' WHERE roll_no='$rno' AND user_id='$user_id'";
			   $stmt = $con->prepare($sql5);	
			   $stmt->execute();
			   $stmt->close(); 		

				$sql9 = "update  tblx_batch_user_map SET batch_id='$batch_id' WHERE center_id='$center_id' AND user_id='$user_id'";
			   $stmt = $con->prepare($sql9);	
			   $stmt->execute();
			   $stmt->close();
				
				
			}
}
//echo "<pre>";print_r($centerArr);exit;
$url="https://english.dseu.ac.in/api/student_data_Macmillan.php";

$file = $url;
$data = file_get_contents($file);
$result = json_decode($data);


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
       


        if($rem < 15 && $rem > 0 && $value['section_count'] > $sectionMaxCount){
        	
        		
        		

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
            		
				  // print_r($rem_students);  
            	$allStudents[count($allStudents) - 1][] = $rem_stu_rem; 
            	unset($rem_students[$key4]);
            }
			
            $slots_arr_count[$key]['students']=$allStudents;
//echo 'all with rem<pre>';
       // print_r($allStudents);

       //  exit;
     	}else{
     		$allStudents = $slots_arr_count;
     	//echo 'all<pre>';
       // print_r($allStudents);

        // exit;
   
     	}

     			
     }
    

//echo '<pre>';
  //  print_r($allStudents);

  //  exit;
   
  //echo '<pre>';print_r($moveStudentArr1);
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
	$batchData=$students['moveStudent'];
	 //$studentData1=$students['moveStudent'];
	 // echo '<pre>';print_r($batchData);exit;
	foreach ($batchData as $k2 => $btd) {
		//echo count($btd);
		$batchCount=count($btd);
	   for ($j = 1; $j<=$batchCount; $j++) {
         $batchData1=$btd[$j-1];
	   }
      //echo '<pre>';print_r($batchData1);//exit;

         $slot_id=$batchData1->slot_id;
		 $slot_section=$batchData1->section;
		 $batchName="Slot-".$batchData1->slot_code;
		 $center_id=$batchData1->center_id;
		
		//createBatch($batchName,$center_id,$slot_id,$slot_section); //batch migration code
		//echo '<pre>';print_r($batchData1);exit;
		

		
	}
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
				//registerUser($rno,$name,$mobile,$email,$father_name,$mother_name,$slot_id,$section,$center_id,$batch_id); //student migration code
			
			}
		}

		
	}
	
}