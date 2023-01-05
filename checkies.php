<?php

setUserPassword("ffffffff5ef30bbb49390d0134e154b0","Password@321");
function getUserBasicDetails($user_login)
{
	$apiURL="https://int-piapi-internal.stg-openclass.com/tokens/?clientId=JteCGkGrn4IBrLZkpV0wBDABEJc49TUr";
	$bodyData='{"userName":"mepro_system","password":"17ZICMmivcUw2tWB05aKkxDC6qC6Fb4M"}';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyData);
	$result = curl_exec($ch);
	$resArray = json_decode($result, true);
	if($resArray['status']=='success')
	{
	//echo $resArray['data'];exit;
	$apiURL="https://int-piapi-internal.stg-openclass.com/usercomposite/".$user_login;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json','X-Authorization: '.$resArray['data']));
	curl_setopt($ch, CURLOPT_POST, 0);
	$result = curl_exec($ch);
	$myArray = json_decode($result, true);
	//print_r($myArray);
		if($resArray['status']=='success')
		{
			
			return $myArray['data']['identityProfile']['givenName']." ".$myArray['data']['identityProfile']['familyName']."^".$myArray['data']['identityProfile']['emails'][0]['emailAddress'];
		}
		else
		{
			return "User";
		}

	}
	else
	{		
		  return "User";
	}
}

function createUserIES($email,$password,$first_name,$last_name)
{
	$apiURL="https://int-piapi-internal.stg-openclass.com/tokens/?clientId=JteCGkGrn4IBrLZkpV0wBDABEJc49TUr";
	$bodyData='{"userName":"mepro_system","password":"17ZICMmivcUw2tWB05aKkxDC6qC6Fb4M"}';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyData);
	$result = curl_exec($ch);
	$resArray = json_decode($result, true);
	if($resArray['status']=='success')
	{
	//echo $resArray['data'];exit;
	$bodyData='{"identity":{"accountStatus":"ACTIVE","lifecycleStatus":"ACTIVE"},"identityProfile":{"givenName":"'.$first_name.'","familyName":"'.$last_name.'","emails":[   {  "emailAddress":"'.$email.'",  "isPrimary":true,  "isValidated":"N"   }]},"credentials":[{   "userName":"'.$email.'",   "password":"'.$password.'",   "resetPassword":false}] }';

	$apiURL="https://int-piapi-internal.stg-openclass.com/usercomposite";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json','X-Authorization: '.$resArray['data']));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyData);
	$result = curl_exec($ch);
	$myArray = json_decode($result, true);
	//print_r($myArray);exit;
		if($resArray['status']=='success')
		{
			return $myArray['data'];
		}
		else
		{
			return "Failed";
		}

	}
	else
	{		
		  return "Failed";
	}
}


///////////////////////////////////////IES Method: Reset User Password////////////////////////////////
function setUserPassword($user_ies_login_id)
{

	$apiURL="https://int-piapi-internal.stg-openclass.com/tokens/?clientId=JteCGkGrn4IBrLZkpV0wBDABEJc49TUr";
	$bodyData='{"userName":"mepro_system","password":"17ZICMmivcUw2tWB05aKkxDC6qC6Fb4M"}';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyData);
	$result = curl_exec($ch);
	$resArray = json_decode($result, true);
	if($resArray['status']=='success')
	{
	//echo $resArray['data'];exit;
	$apiURL="https://int-piapi-internal.stg-openclass.com/credentials/".$user_ies_login_id;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json','X-Authorization: '.$resArray['data']));
	curl_setopt($ch, CURLOPT_POST, 0);
	$result = curl_exec($ch);
	$myArray = json_decode($result, true);
	//print_r($myArray);
		if($resArray['status']=='success')
		{

			$userName=$myArray['data']['userName'];
			$credentialID=$myArray['data']['identity']['id'];
			$bodyDataSet='{"userName":"'.$userName.'","password":"Password@1234"}';
			$apiURL="https://int-piapi-internal.stg-openclass.com/credentials/".$credentialID;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json;charset=UTF-8','X-Authorization: '.$resArray['data']));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyDataSet);
			$result = curl_exec($ch);
			print_r($result);
			return true;
			
	
		}
		else
		{
			return false;
		}

	}
	else
	{		
		  return false;
	}
}
?>