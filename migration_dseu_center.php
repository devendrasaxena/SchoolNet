<?php
@session_start();
print_r($_SESSION);
error_reporting(E_ALL);
ini_set('display_errors', 1);
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'author_ilt');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', 'Liqvid@123');
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
function shortcode_strings(){
    $length_of_minstring='3';
    $length_of_string='8';
	$str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
    return substr(str_shuffle(strtolower($str_result)), 0, $length_of_string);
} 
$centerArr = array();
	

function getAllCenter(){
	$centerArr1 = array();
	$con = createConnection();
    $sql = "Select distinct dseu_center_code from tblx_center";
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($center_code);
	$stmt->execute();
	while($stmt->fetch()) {
		
		if($center_code!='0' && $center_code!=''){
			$centerArr1[]=$center_code;
		}
	}
	$stmt->close();
	//echo "<pre>";print_r($centerArr1);exit;
	return $centerArr1;
	//
}

getAllCenter();

function deleteCenterProductMap($region_id,$center_id){
	   $con = createConnection();
		$sql = "DELETE  FROM tblx_center_product_map WHERE region_id='$region_id' AND center_id='$center_id'";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $stmt->close();	
		return true;
}
 
function addCenterProductMap($region_id,$center_id,$product){
	   $con = createConnection();

		$sql = "INSERT INTO tblx_center_product_map (region_id,center_id,product_id,created_date) values ('$region_id','$center_id','$product',NOW())";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->close(); 
		return true;
}

$url="https://english.dseu.ac.in/api/centre_slot_partner_Macmillan.php";

$file = $url;
$data = file_get_contents($file);
$result = json_decode($data);
//echo "<pre>";print_r($result);	exit;
if($result->status=='200'){
	$data=$result->data;
	$dseuCenter=array();
//echo "<pre>";print_r($data->CenterData);exit;
	if(count($data->CenterData)>0){
	
		$totalCenterLen=count($data->CenterData);
		for($j=1;$j<=$totalCenterLen;$j++){
			$dseuCenter[]=$data->CenterData[$j-1];
		}
           //echo "<pre>";print_r($centerArr);   
			
		//echo "<pre>";print_r($center_code);
        echo count($dseuCenter);
        $i=0;		
		foreach ($dseuCenter  as $key => $value) {
			$center_code1=$value->center_code;
			$slot_code=$value->slot_code;
			$slot_name=$value->slot_name;
			$email_id='center_'.$center_code1.'@dseu.com';
		    $password='Password@123';
			$center_name=$value->center_name;
			$shortcode=shortcode_strings();
			$centerArr=getAllCenter();
			$client_id='46';
			$region_id=5;
			$expiry_days=365;
			$trainer_limit=1000;
			$student_limit=10000;
			$adminName='Admin';
			$product_str='8,9,10,11,12';
			$country='India';
			  
			  
			//$sql11= "INSERT INTO tblx_slot_master SET slot_id = '$slot_code',slot_name ='$slot_name',created_date = NOW()";
           // $stmt = $con->prepare($sql11);		
			//$stmt->execute();
			//$stmt->close();  
			
           // echo "<pre>";print_r($center_code);
			 //echo "<pre>";print_r($centerArr);
			if (in_array($value->center_code, $centerArr)){
				echo "found";echo "<br>";echo "<br>";
				echo $value->center_code;
				echo "<br>";echo "<br>";
					echo $sql = "Select center_id from tblx_center where dseu_center_code='$center_code1'";
					$stmt = $con->prepare($sql);
					$stmt->execute();
					$stmt->bind_result($center_id);
					$stmt->execute();
					$stmt->fetch();
					 $stmt->close();
					/* deleteCenterProductMap($region_id,$center_id);
					echo "<pre>";print_r($center_id);
					 $product_arr=Explode(',',$product_str);
					echo "<pre>";print_r($product_arr);//exit;
					foreach($product_arr as $key=>$value){
						addCenterProductMap($region_id,$center_id,$value);
					}
					echo $sql2 = "UPDATE tblx_center SET country ='$country' where center_id ='$center_id'";
					  $stmt = $con->prepare($sql2);	
					 $stmt->execute();
					  $stmt->close();*/
			  }else{
				echo $i++;
				echo "not found";echo "<br>";echo "<br>";
				echo $value->center_code;
			    echo "<br>";echo "<br>";
			  
				
				/*$sql1= "INSERT INTO tblx_center SET client_id = '$client_id',client_name ='englishEdge', product ='englishEdge', license_key = 'DEMOWILEY0', expiry_days ='$expiry_days',trainer_limit = '$trainer_limit', student_limit = '$student_limit', name = '$center_name',
				region ='$region_id', email_id ='$email_id',description='$adminName', password ='$password',dseu_center_code='$center_code1',org_short_code='$shortcode',country='$country',created_date = NOW()";

				$stmt = $con->prepare($sql1);		
				$stmt->execute();
				$center_id_new =$con->insert_id;
				$stmt->close();  


				  $nextId =  $center_id_new;			
				  $code = "CN-".$center_id_new;
				  $sql2 = "UPDATE tblx_center SET code ='$code' where center_id = $center_id_new";
				  $stmt = $con->prepare($sql2);	
				  $stmt->execute();
				  $stmt->close();
				  
				  //// Now Adding  Admin address 
				   $sql3 = "INSERT INTO address_master(updated_by,created_date) VALUES('1',NOW())";
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
		           $sql5 = "insert into user(first_name,email_id,address_id,profile_pic,updated_by,created_date,user_client_id) values('$adminName','$email_id','$address_id','$asset_id',1, NOW(),'$client_id')";
				   $stmt = $con->prepare($sql5);	
				   $stmt->execute();
				   $user_id =$con->insert_id;
				   $stmt->close(); 		
		
				//// Adding user and center map 
				   $sql6 = "insert into user_center_map(user_id,center_id,client_id,created_date) values('$user_id','$center_id_new','$client_id',NOW())";
				   $stmt = $con->prepare($sql6);	
				   $stmt->execute();
				   $stmt->close();
				   
				   //// Adding Admin Credentials 
                   $sql7 = "insert into user_credential(user_id,loginid,password,updated_by,created_date) values('$user_id','$email_id','$password',1,NOW())";
				   $stmt = $con->prepare($sql7);	
				   $stmt->execute();
				   $stmt->close(); 
		
					////Select the client to user group id 
					$stmt = $con->prepare("Select user_group_id from client WHERE client_id=$client_id");
					$stmt->bind_result($user_group_id);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close(); 
					$client_group_id = $user_group_id;

		          //// Adding Admin into role map group 
		          $role_type="4";//center Admin
                   $sql8 = "insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values('$user_id','$role_type','$client_group_id',1,1,NOW())";
				   $stmt = $con->prepare($sql8);	
				   $stmt->execute();
				   $stmt->close(); 
				   
		      $product_arr=Explode(',',$product_str);
				//echo "<pre>";print_r($courseallData);
				foreach($product_arr as $key=>$value){
					addCenterProductMap($region_id,$center_id_new,$value)
				}
		   */
		
			  }

		}
	}
	//echo "<pre>";print_r($dseuCenter);exit;
}