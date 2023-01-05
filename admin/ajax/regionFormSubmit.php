<?php 
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$centerObj = new centerController();
/* for Add region*/
$regionIdVal =filter_query($_POST['regionIdVal']);
if(empty($regionIdVal)){

	// print_r($_POST); die;

 $regionName = addslashes(trim(filter_string($_POST["region"])));
 $regionDescription = addslashes(trim(filter_string($_POST["region_description"])));
 $countryList = $_POST["country"];
 $client_id = $_POST["client_id"];
 
 $tandc = addslashes(trim(filter_string($_POST["termandcondition"])));
 $policy = addslashes(trim(filter_string($_POST["policy"])));
 $faq = addslashes(trim(filter_string($_POST["faq"])));
 $productList =$_POST["product_id"];
// $productList=implode(',', $productArr);
 $regionNameArr=array();
 $allRegion=$centerObj->getRegionDetails();
	for( $i=0; $i<count($allRegion); $i++){
		$regionNameArr[]=$allRegion[$i]['region_name'];
	}	
 $regionExit = in_array($regionName,$regionNameArr);
if($regionExit){
	 $_SESSION['error'] =3;
	 $_SESSION['msg'] =$region." is already exist. Please try another.";
	 header("location:../createRegion.php");
	  exit;
		
 }else{
 	$region_logo = '';
 	if($_FILES){
 		$file = $_FILES['region_logo'];
 		$name_array = explode('.', $file['name']);
 		$ext = end($name_array);
 		array_pop($name_array);
 		$path = '../../images/region/';
 		$filename = strtolower(implode('_', $name_array)).'_'.time().'.'.$ext;
		$tmp_name = $file['tmp_name'];
		if(move_uploaded_file($tmp_name, $path.$filename)){
			$region_logo = $filename;
		}
 	} 
	    $masterMapID='0';
	    $entity_type='Master';
		
	    $obj = new stdClass();
		$obj->regionName = $regionName;
	    $obj->regionDescription = $regionDescription;
		$obj->region_logo = $region_logo;
		$obj->tandc = $tandc;
		$obj->policy = $policy;
		$obj->faq = $faq;
		
 		// echo "<pre>";print_r($_POST);print_r($_FILES); die;
	   $regionData = $centerObj->createRegion($obj);
	   $regionData = (object) $regionData;

	  if($regionData){
		 $rId= $regionData->regionID;
		   //$mapMasterProductData=$centerObj->setProductMap($entity_type,$rId,$programArr);
		    foreach($productList as $key=>$val){

				 $rpmapData  = $centerObj->addRegionProductMap($rId,$val);
			  }
			foreach($countryList as $key=>$val){

				 $rcmapData  = $centerObj->addRegionCountryMap($rId,$val);
			  }
			$_SESSION['succ'] = 1;
			$_SESSION['msg'] =$region." created successfully.";
			header("Location:../regionList.php");
			exit;
		 }else{
			 $_SESSION['error'] =1;
			 $_SESSION['msg'] =$region." not saved. Please try again.";
			 header("location:../createRegion.php");
			  exit;
			} 
  	
	} 

}
 
/*  For Update edit and update region*/
 if(isset($regionIdVal) && $regionIdVal!= ''){
	$cRegionName = addslashes(trim(filter_string($_POST["cRegionName"])));
	$rId = addslashes(trim($regionIdVal));
	 $countryList = $_POST["country"];
	 $regionName = addslashes(trim(filter_string($_POST["region"])));
	 $regionDescription = addslashes(trim(filter_string($_POST["region_description"])));
     $tandc = addslashes(trim(filter_string($_POST["termandcondition"])));
	 $policy = addslashes(trim(filter_string($_POST["policy"])));
	 $faq = addslashes(trim(filter_string($_POST["faq"])));
	 $productList =$_POST["product_id"];
	$region_logo = '';
 	if($_FILES){
 		$file = $_FILES['region_logo'];
 		$name_array = explode('.', $file['name']);
 		$ext = end($name_array);
 		array_pop($name_array);
 		$path = '../../images/region/';
 		$filename = strtolower(implode('_', $name_array)).'_'.time().'.'.$ext;
		$tmp_name = $file['tmp_name'];
		if(move_uploaded_file($tmp_name, $path.$filename)){
			$region_logo = $filename;
		}
 	} 
		$obj = new stdClass();
		$obj->rId = $rId;
		$obj->regionName = $regionName;
	    $obj->regionDescription = $regionDescription;
		$obj->region_logo = $region_logo;
		$obj->tandc = $tandc;
		$obj->policy = $policy;
		$obj->faq = $faq;
	
	  $regionData  = $centerObj->updateRegionDataByID($obj);
	  $regionData = (object) $regionData;
	
	  //echo "<pre>";print_r($productList);exit;
	   if($regionData){
		 
		 $deleteRegionCountryMapDetails = $centerObj->deleteRegionCountryMapDetails($rId);
         $deleteRegionProductMapDetails = $centerObj->deleteRegionProductMapDetails($rId);
        foreach($productList as $key=>$val){
			 $productData  = $centerObj->addRegionProductMap($rId,$val);
		  } 
		 foreach($countryList as $key=>$val){
			 $countryData  = $centerObj->addRegionCountryMap($rId,$val);
			
		  } 

			 $_SESSION['succ'] = 2;
			 $_SESSION['msg'] =$region." updated successfully.";
			// echo "<pre>";print_r($regionData);exit;
			header("Location:../regionList.php");
			exit;
		 }else{
			 $_SESSION['error'] =1;
			 $_SESSION['msg'] =$region." not saved. Please try again.";
			 header("location:../regionList.php");
			  exit;
		 }
	  
/*
 if($cRegionName==$regionName){
	 }else{
		   $regionNameArr=array();
		 $allRegion=$centerObj->getRegionDetails();
			for( $i=0; $i<count($allRegion); $i++){
				$regionNameArr[]=$allRegion[$i]['region_name'];
			}	
			
		 $regionExit = in_array($regionName,$regionNameArr);
		
		if($regionExit){
			 $_SESSION['error'] =3;
			 $_SESSION['msg'] ="Region is already exist. Please try another.";
			 header("location:../createRegion.php?rid=".base64_encode($rId);
			  exit;
				
		 }else{
			  $regionData  = $centerObj->updateRegionDataByID($rId,$regionName);
			  $regionData = (object) $regionData;
			  //echo "<pre>";print_r($regionData);exit;
			 if($regionData){
				 
				 $deleteRegionCountryMapDetails = $centerObj->deleteRegionCountryMapDetails($rId);

				 foreach($countryList as $key=>$val){

					 $countryData  = $centerObj->updateRegionCountryMap($rId,$val);
				  }

					 $_SESSION['succ'] = 2;
					 $_SESSION['msg'] ="Region updated successfully.";
					// echo "<pre>";print_r($regionData);exit;
					header("Location:../regionList.php");
					exit; 
				 }else{
					 $_SESSION['error'] =1;
					 $_SESSION['msg'] ="Region not saved. Please try again.";
					 header("location:../regionList.php");
					  exit;
				 }  
          } 
   } */
    
}
 
?>
