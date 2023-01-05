<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors','1');
//include_once('../../header/lib.php');
include_once('../../library/phpMailer/mail.php');



   //$code =$obj->code;
   $email_id ="saxena.devendra@gmail.com";
   $password ="Password@1234";
    $centerAdminName="Devendra";


	$subject = "Welcome to Pearson MePro";
	$str="<table style='' cellspacing='0' cellpadding='0' width='100%' bgcolor='#fff'><tbody><tr style='background-color:#fff;height:30px' ><td style='background-color:#dddddd;padding:5px 5px 0px 5px;height:30px' valign='middle'><span style='text-align:left;height:30px'><img style='max-width: 200px;height: auto;' src='https://stg.adurox.com/images/logo.png' alt='' border='0'/></span><span style='position:relative;top:-20px;color:#fff;display:inline-block;padding:5px 5px 2px 5px;font-size:10px;'>&nbsp;</span></td></tr><tr><td style='text-align:center; vertical-align:middle'><div style='padding:25px 10px 0px 10px;color:#ef7823;font-size:25px;'>Hi, $centerAdminName!</div><div style='padding:0px 10px 10px 10px;'><p style='padding:0px 10px 10px 10px;font-size:16px;'>Thank you for choosing <b>Pearson MePro</b>! We hope that you will find the experience engaging.</p></div><div style='padding:50px 10px 10px 10px;color:#ef7823;font-size:25px;'><span style='border-bottom:solid thin #ccc; padding:0px 15px 6px 15px'>Your Login Details</span></div><div><span style='font-size:18px;'>Username: </span><span style='font-size:18px;font-weight:bold;padding-left:5px'>$email_id</span></div><div><span style='font-size:18px;'>Password: </span><span style='font-size:18px;font-weight:bold;padding-left:5px'>$password </span></div><div style='padding:30px 10px 10px 10px;'><p style='font-size: 13px;color: #8a8989'>If you need help with the app, please write in to  <a style='font-size: 11px; color: #ef7823;font-family: Arial; text-decoration: none;' href='mailto:customercare@englishedge.in' target='_blank'>customercare@englishedge.in</a>.</p></div><div style='padding:0px 10px 10px 10px;border-bottom:solid thin #ccc;'></div></td></tr></tbody></table>";

	$mail = sendMail($email_id, $subject, $str);
	  
  echo "Done";
?>