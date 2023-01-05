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
$product_id='8,9,10,11,12';

function getCourseProductmap($center_id,$batch_id,$product_id){
	$con = createConnection();
	$procourseArr=array();
    $protopicArr=array();
    $prochapterArr=array();	
	$whr='';
	if($product_id!=''){
	  $whr.= 'AND product_id IN('.$product_id.')';			  
	}
    $sql = "Select product_id,course,topic,chapter from tblx_product_configuration WHERE batch_id = $batch_id AND institute_id = $center_id ".$whr."";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($product_id,$course,$topic,$chapter);
	$stmt->execute();
	while($stmt->fetch()) {
			$procourseArr[$product_id]=$course;
			$protopicArr[$product_id]=$topic;
			$prochapterArr[$product_id]=$chapter;
		}
	$stmt->close();
	 return ['course_data'=>$procourseArr,'topic_data'=>$protopicArr,'chapter_data'=>$prochapterArr];
	
}

function mapBatchProduct($batchName,$center_id,$slot_id,$slot_section,$product_id1){
	$con = createConnection();
	$batch_id=getBatchByCenterAndName($batchName,$center_id);
	
	if(empty($batch_id)){
		
	}else{
		echo $center_id.' '.$batch_id;echo '<br>';
		$courseallData=getCourseProductmap(34,1,$product_id1);
		$product_arr=Explode(',',$product_id1);
		//echo "<pre>";print_r($courseallData);
		foreach($product_arr as $key=>$value){
			
			$cCode = 'CN-'.$center_id;
			$bcode = $cCode.'-'.$batch_id;
			$whr='';
			if($product_id1!=''){
			  $whr.= 'AND product_id IN('.$product_id1.')';			  
			}
		    $product_id=$value;
			$levellist=$courseallData['course_data'][$value];
			$modulelist=$courseallData['topic_data'][$value];
			$chapterlist=$courseallData['chapter_data'][$value];
			$sql = "Select id from tblx_product_configuration WHERE batch_id = $batch_id AND institute_id=$center_id AND product_id=$product_id";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();

		   if(!empty($id) ){
			   echo " exit".$id;echo " <br>";
				//$sql = "UPDATE tblx_product_configuration SET course=$levellist,topic=$modulelist,chapter=$chapterlist WHERE batch_id = $batch_id AND institute_id=$center_id AND product_id=$product_id";
				//$stmt = $con->prepare($sql);
				//$stmt->execute();
				//$stmt->close();	
		    }else{
			
				$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, product_id,course,topic,chapter) VALUES('batch',$center_id, $batch_id,'$bcode',$product_id,'$levellist','$modulelist','$chapterlist')";
				$stmt = $con->prepare($sql);
				$stmt->execute();
				$stmt->close(); 
			}
			
		}
		//echo "<pre>";print_r($product_arr);exit;
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
		
		mapBatchProduct($batchName,$center_id,$slot_id,$slot_section,$product_id); //batch migration code
		//echo '<pre>';print_r($batchData1);exit;
		

		
	}

	
}