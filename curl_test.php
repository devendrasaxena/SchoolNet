<?php 
$url='https://iltnew.adurox.com/emp-ilt/live/service-aduro.php';
$request = curl_init($url);
$unique_code=$new_license_key;
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'aduroRegisterOnline', 'unique_code' => $unique_code, 'sourse' => 'pearson'));
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($request);
curl_close($request);
$res = json_decode($res);
?>