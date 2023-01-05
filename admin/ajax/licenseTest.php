<?php
	if(isset($_POST['license_key']) && $_POST['license_key']!=''){
		$request = curl_init('http://courses.englishedge.in/celp/service.php');
		$unique_code=$_POST['license_key'];
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request,CURLOPT_POSTFIELDS,array('action' => 'aduroRegisterOnline', 'unique_code' => $unique_code, 'sourse' => 'pearson'));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($request);
		curl_close($request);
		$res = json_decode($res);
		$myArray = json_decode(json_encode($res), true);
		
		echo "<pre>";print_r($myArray);
		//echo $myArray['STATUS'];
		//exit; 
		//echo '92B3CE1C81 =='.$unique_code;exit;
		//E9D4C85FDA
	}
	
	echo "<pre>";print_r($myArray);
		echo $myArray['STATUS'];
?>
 <form role="form" method = "POST" action = "licenseTest.php" id="schoolRegForm" autocomplete="off">

                  <label for="licenseKey" >License Key <span class="required">*</span></label>
        <input type="text"  id="license_key" name="license_key"   value=""/>

              <button type="submit" >SUBMIT</button>

    </form>
 
	
    