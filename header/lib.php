<?php
session_start();
//echo dirname(__DIR__) .'/config/config.php'; exit;
include_once dirname(__DIR__) .'/config/config.php';
//API call
//include_once dirname(__DIR__) .'/controller/serviceController.php'; 
//php mailer
include_once dirname(__DIR__) .'/library/phpMailer/mail.php';  
include_once dirname(__DIR__) .'/controller/common.php'; 
include_once dirname(__DIR__) .'/controller/commonController.php'; 
include_once dirname(__DIR__) .'/controller/loginController.php';
include_once dirname(__DIR__) .'/controller/registrationController.php';
include_once dirname(__DIR__) .'/controller/clientController.php';
include_once dirname(__DIR__) .'/controller/superCenterController.php';
include_once dirname(__DIR__) .'/controller/centerAdminController.php';
include_once dirname(__DIR__) .'/controller/userController.php';

include_once dirname(__DIR__) .'/controller/questionController.php';
include_once dirname(__DIR__) .'/controller/licenseController.php';
include_once dirname(__DIR__) .'/controller/assessmentController.php';
include_once dirname(__DIR__) .'/controller/batteryController.php';
include_once dirname(__DIR__) .'/controller/reportController.php';
include_once dirname(__DIR__) .'/controller/componentController.php';
include_once dirname(__DIR__) .'/controller/trackController.php';
include_once dirname(__DIR__) .'/controller/webinarController.php';
include_once dirname(__DIR__) .'/controller/PageClass.php';
include_once dirname(__DIR__) .'/controller/simple_html_dom.php';
include_once dirname(__DIR__) .'/controller/ies-controller.php';
include_once dirname(__DIR__) .'/controller/assignmentController.php';
include_once dirname(__DIR__) .'/controller/landingController.php';
include_once dirname(__DIR__) .'/controller/districtController.php';
include_once dirname(__DIR__) .'/controller/tehsilController.php';
include_once dirname(__DIR__) .'/controller/designationController.php';
include_once dirname(__DIR__) .'/controller/languageController.php';
include_once dirname(__DIR__) .'/controller/searchController.php';
include_once dirname(__DIR__) .'/controller/graphController.php';
include_once dirname(__DIR__) .'/controller/productController.php';
include_once dirname(__DIR__) .'/controller/pollMainController.php';

include_once dirname(__DIR__) .'/controller_ilt/class_ILT.php';
include_once dirname(__DIR__) .'/controller_ilt/functions.php';

//data-regexp="^(?=.*[A-Za-z])(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,15}$"  The password should be between 6 to 15 characters,  1 uppercase and lowercase character ,at least 1 number and at least 1 special character.

$passValidMsg='The password must have 8 or more characters, at least one uppercase letter, and one number.';
//$passRegexp='^(?=.*[A-Za-z])(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_@.-])[A-Za-z\d_@.-]{8,15}$';
//$passRegexpMsg='The password must have 8 or more characters, at least one uppercase letter, and one number. Password cannot contain special characters other than _ @ . -';
$passRegexp='^(?=.*[A-Za-z])(?=.*\d)(?=.*[A-Z])(?=.*[_@.-])[A-Za-z\d_@.-]{8,15}$';
$passRegexpMsg='The password must have 8 or more characters. It should contain at least one uppercase letter and one number. Allowed special characters are  _ @ . –';

$accCodeValidMsg='The access code you have enter is invalid or incorrect. Make sure you have a valid access code before you try again.';
$accCodeAlreadyUseMsg='You already have access to pearson. Please try a different access code.';

$isActiveMsg='<h2>Oops, your account is inactive</h2>
			<p>Please wait for approval . If you have any query, you can use it in “Support” to request for activate your account or email us: <a>support-mepro@pearson.com</a></p>';
			
$isExpiryMsg='<h2>Oops, you have no active product!</h2>
<p>Your access has expired. If you believe this is a mistake please contact your school or
organisation. If you have an access code you can use it in “My Profile” to activate a new
product.</p>';


 // Language
if($_GET['lan']){
 // unset($_SESSION['language']);
  $_SESSION['language']=  $_GET['lan'];
 }
else if(isset($_SESSION['language'])){
  $_SESSION['language']=  $_SESSION['language'];
 }else{
 	 // unset($_SESSION['language']);
  $_SESSION['language']=  "en";
 }
$datalanguages = dirname(__DIR__).'/language/language.json';
// Open the file to get existing content
$datalanguage = file_get_contents($datalanguages);
// $datalanguage = "./language/language.json";
$language = json_decode($datalanguage, true);
// print_r($datalanguage); die();

$passScore=80;//assessments / certification

 ?>