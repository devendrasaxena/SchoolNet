<?php
//REPORTING ERROR ON/OFF
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// Turn off all error reporting
error_reporting(1);
ini_set('display_errors',1);
class commonController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }

  public function getDatabyId($table,$clmname,$clmnval){
	
	$sql = "Select * from $table where $clmname = '$clmnval'";
	$stmt = $this->dbConn->prepare($sql);	
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	if($RESULT!="" && count($RESULT)>1){
		return $RESULT[0];
	}else if($RESULT!="" && count($RESULT)==1){

		return $RESULT[0];

	}else{
		return $RESULT;}
   }

  public function getCountry(){
	$sql = "select id,country_name from country  order by country_name asc";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
    public function getCountries(){
	$sql = "select id,country_code, country_name from tbl_countries  order by country_name asc";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   
   public function getState($loadId){
	 
	$sql="select tu.id,tu.state_name from state tu , country tutt where tutt.id=tu.country_id and tutt.country_name = :id order by state_name asc";
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   
    public function getCityName($loadId){
	$sql="select tu.id,tu.city_name from city tu , state tutt where tutt.id=tu.state_id and tutt.state_name = :id order by city_name asc";
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   public function getCityName1($loadId){
	$sql="select tu.id,tu.city_name from city tu , state tutt where tutt.id=tu.state_id and tutt.id = :id order by city_name asc";
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
  
   public function getStatebyId($loadId){
	 
	$sql="select tu.id,tu.state_name from state tu , country tutt where tutt.id=tu.country_id and tutt.id =:id order by state_name asc";
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   
   public function getCity($loadId){
	$sql="select tu.id,tu.city_name from city tu , state tutt where tutt.id=tu.state_id and tutt.id = :id order by city_name asc";
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
	 public function getAge(){
	$sql = "select id,age_range from tblx_age_range";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();	
	 return $RESULT;

   }
   public function getGender(){
	$sql = "select id,name,description from tblx_gender";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   public function getMaritalStatus(){
	$sql = "select id,name from tblx_marital_status";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   public function getMotherTongue(){
	$sql = "select id,name from tblx_mother_tongue";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   public function getEducation(){
	$sql = "select id,name from tblx_education";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   
    public function getEmpStatus(){
	$sql = "select id,name from tblx_employment_status";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
  public function getPurJoining(){
	$sql = "select id,name from tblx_joining_purpose";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
  public function getUsersDicover(){
	$sql = "select id,name from tblx_app_discovered";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;

   }

   public function getAreaOfInterest(){
	$sql = "select id,name from tblx_area_of_interest";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  //echo  "<pre>";print_r($RESULT);exit;
	 return $RESULT;

   }
  public function getEnglishExp(){
	$sql = "select id,name from tblx_english_exp";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  //echo  "<pre>";print_r($RESULT);exit;
	 return $RESULT;

   }
   
   public function getProduct(){
	$sql="select * from tbl_product order by id";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
  
   public function getDailyGoal(){
	$sql = "select id,goal_name,goal_value from tblx_daily_goal order by id asc";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
  
  public function getDailyGoalTime(){
	$sql = "select id,goal_duration_name,goal_duration_value from tblx_daily_goal_duration order by id asc";
	$stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;

   }
   public function getDailyGoalById($loadId){
	$sql="select goal_name,goal_value from tblx_daily_goal where id=:id";
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;
   }
   public function getDailyGoalTimeById($loadId){
	$sql="select goal_duration_name,goal_duration_value from tblx_daily_goal_duration where id=:id";

	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':id', $loadId, PDO::PARAM_INT);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
	 return $RESULT;
   }
   
	  public function insertFeedbackVal( $fieldValues ){
	  
			$con = createConnection();
			//echo "<pre>";print_r($fieldValues);
			$userId = $fieldValues['userid'];
			$platform = $fieldValues['platform'];
			$device = $fieldValues['device'];
			$device_id = $fieldValues['device_id'];
			$product = $fieldValues['product'];
			$subject = $fieldValues['subject'];
			$mess = $fieldValues['mess'];
			$client_id= $fieldValues['client_id'];
			
			$query = "INSERT INTO tbl_feedback(user_id, platform, device, device_id, product, subject, message, date,client_id) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(),? )";
			$stmt = $con->prepare($query);
			$stmt->bind_param('issssssi', $userId, $platform, $device, $device_id, $product, $subject, $mess,$client_id);
			$stmt->execute();
			$stmt->close();
			return true;
		
	 }
        
	public function getFeedbackData( ){
		  try{	
			$con = createConnection();
			$query = "select * from tbl_feedback order by id DESC, date DESC";
			$stmt = $con->prepare($query);
			$stmt->execute();
			$RESULT = $this->get_dbresult($stmt);
			$stmt->close();
			foreach( $RESULT as $row ){
				$data[] = $row;
			}
			return $data;
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		} 
	}
   public function isConnected(){
            $connected = @fsockopen("www.google.com", 80); 
                        //website, port  (try 80 or 443)
            if ($connected){
                $is_conn = true; //action when connected
                fclose($connected);
            }else{
                $is_conn = false; //action in connection failure
            }
            return $is_conn;

        }

		public function aduroGetUserDetailsFromUserId($user_id){
			$con = createConnection();

			$stmt = $con->prepare("SELECT a.user_id,a.first_name,a.middle_name,a.last_name,a.email_id,a.address_id,b.loginid,c.phone,c.city,c.state,c.country_code,c.country from user a,user_credential b, address_master c where a.user_id=$user_id and a.user_id=b.user_id and c.address_id=a.address_id");
			$stmt->execute();
			$stmt->bind_result($user_id,$first_name, $middle_name, $last_name, $email_id,$address_id, $loginid,$phone,$city,$state,$country_code,$country);
			$stmt->fetch();
			$stmt->close();
			
			
			$stmt = $con->prepare("SELECT center_id,batch_id FROM tblx_batch_user_map where user_id=?");
			$stmt->bind_param("i",$user_id);
			$stmt->execute();
			$stmt->bind_result($center_id,$batch_id);
			$stmt->fetch();
			$stmt->close();

			if(isset($center_id))
			{
			$stmt = $con->prepare("SELECT name from tblx_center where center_id=?");
			$stmt->bind_param("i",$center_id);
			$stmt->execute();
			$stmt->bind_result($center_name);
			$stmt->fetch();
			$stmt->close();

			$stmt = $con->prepare("SELECT batch_name from tblx_batch where batch_id=?");
			$stmt->bind_param("i",$batch_id);
			$stmt->execute();
			$stmt->bind_result($batch_name);
			$stmt->fetch();
			$stmt->close();
			}


			
			$obj = new stdclass();
			$obj->user_id = $user_id;
			$obj->first_name = $first_name;
			$obj->address_id = $address_id;
			$obj->last_name = $last_name;
			$obj->email_id = $email_id;
			$obj->loginid = $loginid;
			if(isset($center_id))
			{
			$obj->center_name = $center_name;
			$obj->batch_name = $batch_name;
			}
			
			$obj->phone = $phone;
			$obj->city = $city;
			$obj->state = $state;
			$obj->country_code = $country_code;
			$obj->country = $country;
			
			return $obj;
		}

	public function getDesignation(){
		$sql = "select id,desination_short_code,designation,description from tblx_designation  order by id asc";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		 return $RESULT;

   }
  
}  ///// Close Class function 

///// Start  Global function 

function displayText($str){
	$str=stripslashes($str);

	//$str=str_replace("&rsquo;","'",$str);
	$str=str_replace("&quot;","'",$str);
	//$str=str_replace('"','',$str);
	$str=str_replace('&lt;','<',$str);
    $str=str_replace('&gt;','>',$str);
	////$str=str_replace('<','&lt;',$str);
	////$str=str_replace('>','&gt;',$str);
	//$str=str_replace('  ',' ',$str);
	//$str=str_replace('"',"'",$str);
	$str=str_replace("&#38;#39;","'",$str);
	$str=str_replace("&#39;","'",$str);

	$str=str_replace("â€‹Ã¢â‚¬â€¹","",$str);
   // $str=str_replace("&#38;",'&#38;',$str);
	$str=str_replace("&#38;","&",$str);
	$str=str_replace("&amp;nbsp;","",$str);
	

	
	$str= mb_convert_encoding($str, 'UTF-8', 'UTF-8');
	
	if($str==""){
		$str="";
	}
	if(strpos($str,"£"))
	{
	$str=iconv("UTF-8", "ISO-8859-1", $str);
	}
	return $str;
}




function displaySpecial($str){
	

	if(strpos($str,"£"))
	{
	$str=iconv("UTF-8", "ISO-8859-1", $str);
	}
	if($str==""){
		$str="";
	}
	return $str;
}

function displayTextHTML($str){

	
	$str=stripslashes($str);
	$str=str_replace("&#39;","'",$str);
	$str=str_replace("&quot;","'",$str);
	$str=str_replace('&lt;','<',$str);
    $str=str_replace('&gt;','>',$str);
	 $str=str_replace('&lt;&nbsp;&gt;','',$str);
	
	
	//$str= mb_convert_encoding($str, 'UTF-8', 'UTF-8');
	
	if($str==""){
		$str="";
	}
	return $str;
}

function getMapClmns($str){
	$str=explode(',',$str);
	$clmn_left=array();
	$clmn_right=array();
	foreach($str as $key=>$val){
		$nval=explode('-',$val);
		$clmn_left[]=$nval[0];
		$clmn_right[]=$nval[1];

	}
	$data=array('clmn_left'=>$clmn_left,'clmn_right'=>$clmn_right);
	return json_encode($data);	
}
function selectedCheck($value1,$value2){
	//echo $value1."==".$value2;
     if ($value1 == $value2) {
      echo 'selected="selected"';
     } else {
       echo '';
     }
     return;
}
// copy recursive
function cpy($courseCodeDir, $dest){
	if(is_dir($source)) {
		$dir_handle=opendir($source);
		while($file=readdir($dir_handle)){
			if($file!="." && $file!=".."){
				if(is_dir($source."/".$file)){
					if(!is_dir($dest."/".$file)){
						mkdir($dest."/".$file);
					}
					cpy($source."/".$file, $dest."/".$file);
				} else {
					copy($source."/".$file, $dest."/".$file);
				}
			}
		}
		closedir($dir_handle);
	} else {
		copy($source, $dest);
	}
}




function filter_query($str){
	$str=trim($str);
	$str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
	//$str=addslashes($str);
	$str=strip_tags($str);
	//$str=filter_string_special($str);
	return $str;
}

function filter_string($str){
	$str=trim($str);
	$str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
	$str=addslashes($str);
	$str=strip_tags($str);
	$str=filter_string_special($str);
	return $str;
}

function filter_string_special($str){	
	$str=trim($str);
    //$str=addslashes($str);
	$str=str_replace("​  ","",$str);
	$str=str_replace("console.","",$str);
	$str=str_replace("onmouseover","",$str);
	$str=str_replace("sleep(","",$str);
	$str=str_replace("alert","",$str);
	
    $str=str_replace("&",'&#38;',$str);
	$str=str_replace('"','&#39;',$str);
	$str=str_replace('<','&lt;',$str);
	$str=str_replace('>','&gt;',$str);
	$str=str_replace('  ',' ',$str);
	$str=str_replace("'",'&#39;',$str);
	$str=str_replace("\n\t",'\t',$str);
	$str=str_replace("=",'',$str);

	$str= mb_convert_encoding($str, 'UTF-8', 'UTF-8'); 
	if($str=="")
	{
	$str="";
	}
	return $str;
}

function shuffle_assoc($list) { 
    
  if (!is_array($list)) return $list; 

  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) { 
    $random[$key] = $list[$key]; 
  }
  return $random; 
  
} 


function truncateString($str, $chars, $to_space='', $replacement="...") {
   if($chars >= strlen($str)) return $str;

   $str = substr($str, 0, $chars);

   $space_pos = strrpos($str, " ");
   if($to_space && $space_pos >= 0) {
       $str = substr($str, 0, strrpos($str, " "));
   }

   return($str . $replacement);
}
function dump($x, $die = 0){
    echo '<pre>';
    print_r($x);
    if($die == 1){
        die();
    }
}
function getRandUniqueStr(){
    return md5(uniqid(rand(), true));
}

function getUniqID($prefix = '', $len = 6 ){
    $prefix = trim($prefix);
    if( !is_numeric( $len ) || ceil($len) != $len ){
        $len = 6;
    }
    
    $str = uniqid(). '_'. gen_random_code($len);
    if( $prefix != ''){
        $str = $prefix .'_'. $str;
    }
    
    return $str;
}
function gen_random_code($length) {
    $characters = "abcdefghijklmnopqrstuvwxyz0123456789";  
    $randomString = ""; 
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[mt_rand(0, strlen($characters)-1)];
    }
    return $randomString;
  } 

function trimCallback(&$val, $index){
    $val = trim($val);
}


function htmlentityCallback(&$val, $index){
    $val = trim($val);
}
function check_internet_connection($sCheckHost = 'www.google.com') 
{
    return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
}
function stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

    return $value;
}


function stripslashesFull($input)
{
    if (is_array($input)) {
        $input = array_map('stripslashesFull', $input);
    } elseif (is_object($input)) {
        $vars = get_object_vars($input);
        foreach ($vars as $k=>$v) {
            $input->{$k} = stripslashesFull($v);
        }
    } else {
        $input = stripslashes($input);
    }
    return $input;
}

function searchArr($value, $array) { 
		return(array_search($value, $array,false)); 
	  } 

function stripTags($value) { 
		return(strip_tags($value)); 
	  } 

	  function TrimString($tempStr,$length){
	if(strlen($tempStr) > $length){
		$tempStr=substr($tempStr,0,$length);
		return $tempStr.'...';
	}else{
		return $tempStr;
	}
	
}
?>