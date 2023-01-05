<?php
@session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'author_ilt_dseu_rnd');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', 'Liqvid@123');

ini_set('max_execution_time', 99999);

$arrSections = ['A','B','C','D','E','F','G','H','I','J'];
$sectionMaxCount=30;


/*
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
	$dbname = "author_ilt_dseu_rnd";
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
	$dbname = "author_ilt_dseu_rnd";
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
function shortcode_strings(){
    $length_of_minstring='3';
    $length_of_string='8';
	$str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
    return substr(str_shuffle(strtolower($str_result)), 0, $length_of_string);
} 
	$centerArr = array();
getAllCenter();
function getAllCenter(){
	$centerArr1 = array();
	$con = createConnection();
    $sql = "Select distinct dseu_center_code from tblx_center";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($center_code);
	$stmt->execute();
	while($stmt->fetch()) {
		if($center_code>0){
			$centerArr1[]=$center_code;
			///$bcm = new stdClass();
			//$bcm->center_code = $center_code;
			//array_push($centerArr1,$bcm);
		}
	}
	$stmt->close();
	return $centerArr1;
	//echo "<pre>";print_r($centerArr1);exit;
}


*/
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

       	  	$movedStudents[$k1]->{'slot_code'} = $mStu->{'slot_code'}. '-' .$arrSections[$i];
       	  } 
       $slots_arr_count[$key]['students'][$i] = $movedStudents;
       }
     
     }

   return ['students_data'=>$slots_arr_count];
}

function moveElement(&$array, $a, $b) {
    return array_splice($array, $a, $b);
    
}


echo '<pre>';
if($result->status=='200'){
	$students = createSlot($result->data->studentData_Macmillan);
	print_r($students);
}