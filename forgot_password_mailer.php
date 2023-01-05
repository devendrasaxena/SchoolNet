<?php
include_once dirname(__DIR__) .'/config/config.php';
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
	$logo=$ip_address_data.'live/assests/images/logo.png';
	$ProductName=APP_NAME;
	//$filepath= $globalLink.'/library/phpMailer/mail.php'; 
	$filepath= 'library/phpMailer/mail.php'; 
    include_once($filepath);
	//echo $filepath;
	//echo $email;
	$subject = "$ProductName - Forgot Password";
	$str="<table style='' cellspacing='0' cellpadding='0' width='100%' bgcolor='#fff'><tbody><tr style='background-color:#fff;height:30px' ><td style='background-color:#dddddd;padding:5px 5px 0px 5px;height:30px' valign='middle'><span style='text-align:left;height:30px'><img style='max-width: 80px;height: auto;' src='$logo' alt='' border='0'/></span><span style='position:relative;top:-20px;color:#fff;display:inline-block;padding:5px 5px 2px 5px;font-size:10px;'>&nbsp;</span></td></tr><tr><td style='text-align:center; vertical-align:middle'><div style='padding:40px 10px 20px 10px;'><span style='font-size:18px;'>Your password is : </span><span style='font-size:18px;font-weight:bold;padding-left:5px'>$password </span></div><div style='padding:30px 10px 10px 10px;'><p style='font-size: 13px;color: #8a8989'>If you need help with the app, please write in to  <a style='font-size: 11px; color: #ef7823;font-family: Arial; text-decoration: none;' href='mailto:customercare@englishedge.in' target='_blank'>customercare@englishedge.in</a>.</p></div><div style='padding:0px 10px 10px 10px;border-bottom:solid thin #ccc;'></div></td></tr></tbody></table>";
 	$status = sendMail($email, $subject, $str); 
	  


?>