<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';

/* error_reporting(E_ALL);
ini_set('display_errors',1); */

	$region_id = trim($_POST['region_id']);
	if($region_id!="" && $region_id!="All" && $region_id!=0) {
		$region_id = "region_id:".$region_id;
	}else{
		$region_id = "";
	}
	
	$country_name = trim($_POST['country']);
	if($country_name!="" && $country_name!="All" && $country_name!=0) {
		$country_name = "country_name:".$country_name;
	}else{
		$country_name = ""; 
	}
	
	$center_id = trim($_POST['center_id']);
	if($center_id!="" && $center_id!="All" && $center_id!=0) {
		$center_id = "center_id:".$center_id;
	}else{
		$center_id = "";
	}
	
	$batch_id = trim($_POST['batch_id']);
	if($batch_id!="" && $batch_id!="All" && $batch_id!=0) {
		$batch_id = "batch_id:".$batch_id;
	}else{
		$batch_id = "";
	}

	$name= trim(str_replace(" ","%20",$_POST['uname']));
	$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
	
$url ="http://liqvidreports:SolrRocks@3.7.119.102:8983/solr/wfp_performance/select?fq=(user_email%3A*".$name."*%20OR%20user_name%3A*".$name."*)&q=*%3A*&fq=".$country_name."&fq=".$region_id."&fq=".$center_id."&fq=".$batch_id."&group=true&group.field=user_id&rows=10";


	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
				$request_param);
	// In real life you should use something like:
	// curl_setopt($ch, CURLOPT_POSTFIELDS, 
	//          http_build_query(array('postvar1' => 'value1')));

	// Receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close ($ch);

	// Initiate curl
	$res = json_decode($server_output, true);
	$users_arr = $res['grouped']['user_id']['groups'];
	$ttl_record = $res['grouped']['user_id']['matches'];



	 
	 if(count($users_arr)>0){
		 
		 $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
				$user_id = $value['doclist']['docs'][0]['user_id'];
				$fullname = $value['doclist']['docs'][0]['user_name'];
				
				
		
				$optionSelected = ($valSelected == $user_id) ? "selected" : "";
				echo '<option   value="'.$user_id.'" '.$optionSelected.' >'.$fullname.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}

?>