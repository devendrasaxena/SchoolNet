<?php
ob_start();
@session_start();

//REPORTING ERROR ON/OFF
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// Turn off all error reporting
error_reporting(1);
ini_set('display_errors',1);
// Set Timezone of Server
date_default_timezone_set('Asia/Kolkata');
//require_once dirname(dirname(__FILE__)).'/controller/common.php';
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
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//echo 'User Real IP - '.getUserIpAddr();
$device_ip_Address=getUserIpAddr();
//location of the server dev/live/production


    $ip_address = 'https://lmsplatform.englishedge.in/';
	$ip_address_data='https://lmsplatform.englishedge.in/emp-ilt'; //'https://emp-ilt.adurox.com/';
    $enc_key = 'p1^bil';
	//$web_service_url = $ip_address.'/live/service.php';
    $web_service_url = $ip_address_data.'/live/service-aduro.php';
	//echo $web_service_url;exit;
	$course_vocab_url = $ip_address_data.'/view/vocabulary/';
    $course_data_url = $ip_address_data.'/view/course_data';
    $license_data_url = 'http://courses.englishedge.in/celp/service.php';
    $profile_img_upload_url = $ip_address_data.'/live/web-upload.php';
    $profile_img_hosting_url = $ip_address_data.'/view/profile_pic/';///$ip_address_data.'/view/profile_pic/';
	$product_img_hosting_url = $ip_address_data.'/view/images/product_thumb/web/';
	$product_manual_hosting_url = $ip_address_data.'/live/product_manuals/';
	$thumnail_Img_url = $ip_address_data.'/view/uploads/';
	$_media_path = $ip_address_data.'/view/uploads/ILT_media/';
	$game_Img_url = $ip_address_data.'/view/uploads/game_upload/';
    $asmt_quiz_rec_upload_url = $ip_address_data.'/view/assessment_files/';
	$sana_url = $globalLink.'/sana/sana-api.php';
	$speechAce_url = $globalLink.'/speechAce/web-api.php';

	$report_url = $ip_address_data;//http://3.7.119.102
	$lic_customer_id=0;
	$customer_id=46;
	$client_name='englishEdge';
	$client_id=$customer_id;

  
	
define('FEEDBACK_FORM_URL', $globalLink.'/feedback/feedback.php');

define('THUMNAIL_IMG_URL', $thumnail_Img_url );
define('IMG_URL', $img_url );

define('PROFILE_PIC_DIR', $ip_address.'/view/user/profile_pic/');
define('WEB_IP', $ip_address);

define('WEB_SERVICE_ENCRYPTION_KEY', $enc_key );
// service URL
define('WEB_SERVICE_URL', $web_service_url );
// course data download url 
define('WEB_COURSE_DATA_DOWNLOAD_URL', $course_data_url );

define('WEB_PROFILE_IMAGE_UPLOAD_URL', $profile_img_upload_url );
define('WEB_PROFILE_IMAGE_HOSTING_URL', $profile_img_hosting_url );
define('WEB_ASMT_QUIZ_REC_UPLOAD_URL', $asmt_quiz_rec_upload_url );

// this dir will be used for predefined media or any type of files 
define('APP_STORAGE', 'app');
define('APP_STORAGE_MEDIA', 'media');

// dir for storage for recordings 
define('ROLEPLAY_RECORD_MEDIA', 'roleplay_record_media');
define('VOCAB_RECORD_MEDIA', 'vocab_record_media');
define('QUIZ_ASMT_AV_RECORD_MEDIA', 'quiz_asmt_av_record_media');
define('QUIZ_ASMT_RA_RECORD_MEDIA', 'quiz_asmt_ra_record_media');

/**** application type ***************/
define('WEB_SERVICE_APP_VERSION', 2);
//define('WEB_SERVICE_DEVICE_ID', getDeviceID() );
define('WEB_SERVICE_DEVICE_ID', $device_ip_Address);
define('WEB_SERVICE_DEVICE_TYPE', 'Online');
define('WEB_SERVICE_PLATFORM', 'Web');
define('CLIENT_NAME', $client_name);
define('CERTIFICATE_DIR', '/usr/share/nginx/html/iltnew/user/certs/');
//Database Connection Information - EC2
//Database Connection Information - EC2

//DEFINE('DB_HOST', '18.169.47.27');
//DEFINE('DB_NAME', 'author_ilt');
//DEFINE('DB_USER', 'root');
//DEFINE('DB_PASSWORD', 'liqvid123');
//Database Connection Information - RDS
//DEFINE('DB_HOST', 'languagelab555.cpwwa3kju9uo.ap-southeast-1.rds.amazonaws.com');
//DEFINE('DB_HOST', '65.0.111.126');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'author_ilt');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', 'Liqvid@123');

function createConnection() {
    
	//Database Connection Information - EC2
	//$host = "18.169.47.27";
	//$dbname = "author_ilt";
    //$dbuser =  "root";
   // $dbpass  = "liqvid123";

	//Database Connection Information - EC2
	//$host = "languagelab555.cpwwa3kju9uo.ap-southeast-1.rds.amazonaws.com";
	//$host = "65.0.111.126";
	$host = "localhost";
	$dbname = "author_ilt";
    $dbuser =  "root";
    $dbpass  = "Liqvid1@23";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $con=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	//$con -> set_charset("utf8");
    if (mysqli_connect_errno()) {
      //  print mysqli_connect_errno()."ERROR IN MYSQL";
		print "Oops. Something has gone wrong. Please try again.";
        return null;
    }
    return $con;
}

function createConnection1() { //without UTF 
	//$host = "65.0.111.126";
	$host = "localhost";
	$dbname = "author_ilt";
    $dbuser =  "root";
    $dbpass  = "Liqvid@123";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $con=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (mysqli_connect_errno()) {
      //  print mysqli_connect_errno()."ERROR IN MYSQL";
		print "Oops. Something has gone wrong. Please try again.";
        return null;
    }
    return $con;
}

function closeConnection($con) {
    mysqli_close($con);
}

class DBConnection{
	
	 private static $_DB_Conn;
  
     public static function createConn() {
       
            if(!self::$_DB_Conn) { 
			  try {
               // $dbConn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			   //// QA
			 //$dbConn = new PDO("mysql:host=52.12.100.7;port=3306;dbname=author_ilt;charset=utf8", "root", "son123456", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			  //// PROD
			 
			 //$dbConn = new PDO("mysql:host=65.0.111.126;port=3306;dbname=author_ilt;charset=utf8", "root", "Liqvid@123", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			  //$dbConn = new PDO("mysql:host=localhost;port=3306;dbname=author_ilt;charset=utf8", "root", "Liqvid@123", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			  $dbConn = new PDO("mysql:host=localhost;port=3306;dbname=author_ilt;", "root", "Liqvid@123");
			 
                // set the PDO error mode to exception
                $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             
                self::$_DB_Conn = $dbConn;
             } 
			 catch (PDOException $e) {
           // echo "Class DB Error : ".$e->getMessage();
		     echo "Oops. Something has gone wrong. Please try again.";
           }
           
         } 
		 return self::$_DB_Conn;
    }
	public static function closeDBConn() {
            return self::$_DB_Conn = null;
    }
} 



//User role array
define('user_role', array(
    '',
    'Teacher',
    'Learner',
    'Customer',
    'Center Admin',
    'Batch Admin',
    'Author'
));
define('PRODUCTMODE','');
define('IS_PREPACKAGED',1);

$skillUrl = "https://skilful-platform.adurox.com/";
$domainSkill = parse_url($skillUrl);
$learning = "https://learning.adurox.com/";
$domainLearning = parse_url($learning);
$mainDomain = parse_url($ip_address);
 $dseuUrl = "https://dseu.adurox.com/";  
$domainDESEU = parse_url($dseuUrl);
//echo "The domain is ".$mainDomain['host'];
 //echo "The domain is ".$domain['host'];
$rp_upload_url = $globalLink.'/emp-ilt/live/web-rp-api.php';


$host=$_SERVER['HTTP_HOST'];
if($host=='lmsplatform.englishedge.in'){
	$host='lmsplatform.englishedge.in';
}

$con2 = createConnection();
$sql='Select id,region_name,region_logo,is_region_logo_show,is_app_logo_show,is_secondary_logo,secondary_logo,is_live_class,is_notification,is_assignment,poll_survey,is_placement_test,exam_type,placement_url,placement_course,is_azure,is_azure_rec_path,rec_path,is_resources,is_theme,is_language,is_leaderboard,is_authoring,is_signup,json_data from tblx_region where domain_url =?';
$stmt = $con2->prepare($sql);
$stmt->bind_param("s",$host);
$stmt->bind_result($id,$region_name,$region_logo,$is_region_logo_show,$is_app_logo_show,$is_secondary_logo,$secondary_logo,$is_live_class,$is_notification,$is_assignment,$poll_survey,$is_placement_test,$exam_type,$placement_url,$placement_course,$is_azure,$is_azure_rec_path,$rec_path,$is_resources,$is_theme,$is_language,$is_leaderboard,$is_authoring,$is_signup,$json_data);
$stmt->execute();
$stmt->fetch();
$stmt->close();
	if($is_placement_test==0 && $exam_type!=''){//skill full and dseu
		 define('SHOW_PLACEMENT_TEST','0');
		$exam_type= explode(',',$exam_type);
		if($exam_type[0]=='pre'){
		 define('SHOW_PRE_EXAM','1');
		 define('EXAM_TYPE',$exam_type[0]);
		 
		}
		if($exam_type[1]=='post'){
		 define('SHOW_POST_EXAM','1');
		  define('EXAM_TYPE',$exam_type[1]);
		}
		
	}elseif($is_placement_test==1 && $exam_type!=''){
		 define('SHOW_PLACEMENT_TEST','1');
		 $exam_type= explode(',',$exam_type);
		if($exam_type[0]=='pre'){
		 define('SHOW_PRE_EXAM','1');
		  define('EXAM_TYPE',$exam_type[0]);
		}
		if($exam_type[1]=='post'){
		 define('SHOW_POST_EXAM','1');
		  define('EXAM_TYPE',$exam_type[1]);
		}
	}elseif($is_placement_test==1 && $exam_type==''){
		 define('SHOW_PLACEMENT_TEST','1');
		 define('SHOW_PRE_EXAM','0');
		 define('SHOW_POST_EXAM','0');
		  define('EXAM_TYPE','');
	}else{
		 define('SHOW_PLACEMENT_TEST','0');
		 define('SHOW_PRE_EXAM','0');
		 define('SHOW_POST_EXAM','0');
		 define('EXAM_TYPE','');
		 
	}
	
	if($is_secondary_logo==1){
		 define('SECONDARY_LOGO','images/region/secondary/'.$secondary_logo);
	}



 define('SHOW_AZURE',$is_azure);
	if($is_azure==1){
		$_role_media_path = "../emp-ilt/live/srp/";//$ip_address_data.'/live/srp/';
	}else{
		 $_role_media_path = "../emp-ilt/live/rp_recording/";//$ip_address_data.'/live/srp/';
	}
  define('SHOW_REGION_THEME',$is_theme);
  define('THEME_NAME',$id);
  if($is_region_logo_show==1){
	define('applogo','../images/region/'.$region_logo);
  }else{
	define('applogo','../images/logo.png');  
  }
  define('APP_NAME',$region_name);	
  define('SHOW_RESOUCRES',$is_resources);
  define('SELF_REGISTER',$is_signup);
  define('client_reg_id',$id);
  define('JSON_DATA',$json_data);

//define('applogo','images/MePro.svg');


define('SHOW_COPYRIGHT',1);//1 means show ,0 means hide copyright details
define('COPYRIGHT','<span class="footerText">&copy;</span>');
define('SHOW_POWERED',1);//1 means show ,0 means hide powered by details
define('POWERED','<img src="'.$globalLink.'/images/adrulogo.png" width="60"/> <span class="footerText">powered by</span> <img src="'.$globalLink.'/images/englishedge.png" width="50"/>');
define('SHOW_WEBLINK',0);
define('WEBLINK','http://www.englishedge.in/');
define('B2C_CENTER',6);// 1 required must and 0 not required
define('mendontry',0);// 1 required must and 0 not required
define('skip',1);// 1 skip and 0 not 
define('skipTest',1);// 1 skip and 0 not 
define('walkThrough',0);// 1 yes and 0 not 
define('TOPIC_NAME','Module');// 1 yes and 0 not //
define('CHAPTER_NAME','');// 1 yes and 0 not //Lesson

define('showStarQuiz',0);// 1 yes and 0 not 
define('showQuesCountQuiz',0);// 1 yes and 0 not 
define('showProgressQuiz',1);// 1 yes and 0 not 
define('showCurrentlevel',0);// 1 yes and 0 not 
define('user_current_level',1);
define('session_max_seat',10);
define('topicName',27);
define('chDescription',100);
define('PAGINATION_LIMIT',10);

define('SMALLLOGO',1);//1 means show ,0 means hide
define('LOGOIMG',1);//1 means show ,0 means hide
define('LOGOTEXT',0);//1 means show ,0 means hide
define('APP_LOGO_TEXT','');//1 means show ,0 means hide
define('GURU_TEXT',"Teacher's Notes");//1 means show ,0 means hide

/*activity player instruction*/
define('IMG_TEXT',"Look at the image");//Look at the image.
define('AUDIO_TEXT',"Click the play button to listen to the audio");//Click the play button to listen to the audio.
define('VIDEO_TEXT',"Click the play button to watch the video");//Click the play button to watch the video.
define('PARA_TEXT',"Read the following text");//Read the following text.
define('STORY_IMG_TEXT',"Look at the image");//Look at the image.
define('STORY_AUDIO_TEXT',"Click the play button to listen to the audio");//Click the play button to listen to the audio.
define('STORY_VIDEO_TEXT',"Click the play button to watch the video");//Click the play button to watch the video.
define('STORY_PARA_TEXT',"Read the following text");//Read the following text.

define('PREPMODE',0);//1 means show ,0 means hide
define('ATTDENDANCE',0);//sessonMode=1 means hide ATTDENDANCE ,sessonMode=0 means show ATTDENDANCE
define('SHOW_GLOSSARLY_LINK', 0);//1 means show , 0 means hide 
define('SHOW_TRIVIA_LINK', 0);//1 means show , 0 means hide 
define('SHOW_HELPACTIVITYMENUAL',0);//1 means show ,0 means hide help menual
define('SHOW_CANVAS_LINK', 1);//1 means show , 0 means hide (if canvas hide imagezoom auto hide)
define('SHOW_IMG_ZOOM',0);//1 means show , 0 means hide and depend to canvas becoz canvas hide image zoom also hide
define('SHOW_VIRTUALLAB',0);//1 means show ,0 means hide virtual labs
define('SHOW_SECOND_LOGO',1);//1 means show ,0 means hide virtual labs
define('SHOW_THEME',0);//1 means show ,0 means hide virtual labs
$editRight=1;
//define('SOLR_URL','http://3.7.119.102/');

if (!function_exists('_dd')){
function _dd($data){
	ob_clean();
	echo "row data dump<pre style='color:red'>";
	print_r($data);
	exit;
 }
}


include_once 'DB/config.php';


?>