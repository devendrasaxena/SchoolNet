<?php 

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$centerObj = new centerController();
$adminObj = new centerAdminController();
error_reporting(1);
ini_set('display_errors',1);
/* for Add batch*/
$batchIdVal = filter_query($_POST['batchIdVal']); 
if(empty($batchIdVal)){
 //echo "<pre>";print_r($_POST);exit;
  $levc = $_POST['levc'];
  $modc = $_POST['modc'];
  $chapc = $_POST['chapc'];
	
 $sectionConfig = addslashes(trim($_POST["sectionType"]));
 $centerId = addslashes(trim($_POST["center_id"]));
 $lmode = addslashes(trim($_POST["lmode"]));
 
  if($sectionConfig=='None'){
	//$section = addslashes(trim($_POST["section"]));
	$batchName = addslashes(trim(filter_string($_POST["batch"])));
  }else{
	  $batchName = addslashes(trim(filter_string($_POST["batch"])));
	  $batchName= $batchName;
  }
   
$batchNameArr=array();
$allBatch=$adminObj->getBatchDeatils($centerId);
for( $i=0; $i<count($allBatch); $i++){
	$batchNameArr[]=$allBatch[$i]['batch_name'];
}	
 $batchExit = in_array($batchName,$batchNameArr);
 if($batchExit){
	 $_SESSION['error'] =3;
	 $_SESSION['msg'] ="$batch is already exist. Please try another.";
	 header("location:../createBatch.php");
	  exit;
		
 }else{
	   $batchData = $centerObj->createCenterBatch($batchName,$sectionConfig,$centerId,$lmode);
	   $batchData = (object) $batchData;
	  if($batchData){
		 $bId= $batchData->batchID;	
				
		  $productData =$_POST["product_id"];
		   //$product_id=implode(',', $productArr);
		for( $i=0; $i<count($productData); $i++){
			$levelList='';
				$moduleList='';
				$chapterList='';
                $levelPost = 'level'.$productData[$i];
				$topicPost = 'module'.$productData[$i];
				$chapterPost = 'chapter'.$productData[$i];
					
				$product_id=$productData[$i];  
				
				$levc1.$productData[$i] = count($_POST[$levelPost]);
				$modc1.$productData[$i] = count($_POST[$topicPost]);
				$chapc1.$productData[$i] = count($_POST[$chapterPost]);
				  
					if ($levc == $levc1.$productData[$i]) {
						$levelList.$productData[$i] = implode(',', $_POST[$levelPost]);
					}
					else{
						$levelList.$productData[$i] = implode(',', $_POST[$levelPost]);
					}
					 $levelList=$levelList.$productData[$i];
					if ($modc == $modc1.$productData[$i]) {
						$moduleList.$productData[$i] = implode(',', $_POST[$topicPost]);
					}
					else{
						$moduleList.$productData[$i] = implode(',', $_POST[$topicPost]); 
					}
					 $moduleList=$moduleList.$productData[$i];
					 
					if ($chapc == $chapc1.$productData[$i]) {
						$chapterList.$productData[$i] = implode(',', $_POST[$chapterPost]); 
					}
					else{
						$chapterList.$productData[$i] = implode(',', $_POST[$chapterPost]); 
					}
					$chapterList=$chapterList.$productData[$i];	
					
				if($levelList!=''){
					$centerObj->createCenterBatchdetails($bId,$centerId,$product_id,$levelList,$moduleList,$chapterList);
	            }
			}			
			$_SESSION['succ'] = 1;
			$_SESSION['msg'] ="$batch created successfully.";
			header("Location:../batchList.php");
			exit;
		 }else{
			 $_SESSION['error'] =1;
			 $_SESSION['msg'] ="$batch not saved. Please try again.";
			 header("location:../batchList.php");
			  exit;
			} 
  	
	}

}

/*  For Update edit and update batch*/
if(isset($batchIdVal) && $batchIdVal != ''){
	//echo "<pre>";print_r($_POST);exit;
	
	$levc = $_POST['levc'];
	$modc = $_POST['modc'];
	$chapc = $_POST['chapc'];

	$sectionConfig = addslashes(trim($_POST["sectionType"]));
	$centerId = addslashes(trim($_POST["center_id"]));
	$lmode = addslashes(trim($_POST["lmode"]));
	$cbatchName = addslashes(trim(filter_string($_POST["cbatchName"])));
	$bId = addslashes(trim($batchIdVal));

	if($sectionConfig=='None'){
		$batchName = addslashes(trim(filter_string($_POST["batch"])));
	}else{
		$batchName = addslashes(trim(filter_string($_POST["batch"])));
		$batchName=$batchName;
	}
$productData =$_POST["product_id"];
//echo "<pre>";print_r($_POST);exit;	
if($cbatchName==$batchName){

	  $batchData  = $centerObj->updateBatchDataByID($bId,$batchName,$sectionConfig,$centerId,$lmode);
	  $batchData = (object) $batchData;
	  
	  $exitProduct=$centerObj->deleteBatchDataByDetails($bId,$centerId,'');	
			
	  //echo "<pre>";print_r($productData);
		   //$product_id=implode(',', $productArr);
		for( $i=0; $i<count($productData); $i++){
			 $levelList='';
				$moduleList='';
				$chapterList='';
                $levelPost = 'level'.$productData[$i];
				$topicPost = 'module'.$productData[$i];
				$chapterPost = 'chapter'.$productData[$i];
					
				$product_id=$productData[$i];  
				
				$levc1.$productData[$i] = count($_POST[$levelPost]);
				$modc1.$productData[$i] = count($_POST[$topicPost]);
				$chapc1.$productData[$i] = count($_POST[$chapterPost]);
				  
					if ($levc == $levc1.$productData[$i]) {
						$levelList.$productData[$i] = implode(',', $_POST[$levelPost]);
					}
					else{
						$levelList.$productData[$i] = implode(',', $_POST[$levelPost]);
					}
					 $levelList=$levelList.$productData[$i];
					if ($modc == $modc1.$productData[$i]) {
						$moduleList.$productData[$i] = implode(',', $_POST[$topicPost]);
					}
					else{
						$moduleList.$productData[$i] = implode(',', $_POST[$topicPost]); 
					}
					 $moduleList=$moduleList.$productData[$i];
					 
					if ($chapc == $chapc1.$productData[$i]) {
						$chapterList.$productData[$i] = implode(',', $_POST[$chapterPost]); 
					}
					else{
						$chapterList.$productData[$i] = implode(',', $_POST[$chapterPost]); 
					}
					$chapterList=$chapterList.$productData[$i];	
					
				if($levelList!=''){
					$centerObj->updateBatchDataByDetails($bId,$centerId,$product_id,$levelList,$moduleList,$chapterList);
				}
			}	
	  
	  //echo "<pre>";print_r($batchData);exit;
	   if($batchData){
		
			 $_SESSION['succ'] = 2;
			 $_SESSION['msg'] ="$batch updated successfully.";
			// echo "<pre>";print_r($batchData);exit;
			header("Location:../batchList.php");
			exit;
		 }else{
			 $_SESSION['error'] =1;
			 $_SESSION['msg'] ="$batch not saved. Please try again.";
			 header("location:../batchList.php");
			  exit;
		 }
	  
  }else{
		 $batchNameArr=array();
		 $allBatch=$adminObj->getBatchDeatils($centerId);
		 for( $i=0; $i<count($allBatch); $i++){
			$batchNameArr[]=$allBatch[$i]['batch_name'];
		 }	 
		
		 $batchExit = in_array($batchName,$batchNameArr, TRUE);
		 if($batchExit){
			 $_SESSION['error'] =3;
			 $_SESSION['msg'] ="$batch is already exist. Please try another.";
			 header("location:../createBatch.php?bid=".base64_encode($bId)."&cid=".base64_encode($centerId));
			  exit;
				
		 }else{

			  $batchData  = $centerObj->updateBatchDataByID($bId,$batchName,$sectionConfig,$centerId,$lmode);
			  $batchData = (object) $batchData;
			  
			  $exitProduct=$centerObj->deleteBatchDataByDetails($bId,$centerId,'');	
			
			
			  //echo "<pre>";print_r($batchData);exit;
			   for( $i=0; $i<count($productData); $i++){
			
				$levelList='';
				$moduleList='';
				$chapterList='';
                $levelPost = 'level'.$productData[$i];
				$topicPost = 'module'.$productData[$i];
				$chapterPost = 'chapter'.$productData[$i];
					
				$product_id=$productData[$i];  
				
				$levc1.$productData[$i] = count($_POST[$levelPost]);
				$modc1.$productData[$i] = count($_POST[$topicPost]);
				$chapc1.$productData[$i] = count($_POST[$chapterPost]);
				  
					if ($levc == $levc1.$productData[$i]) {
						$levelList.$productData[$i] = implode(',', $_POST[$levelPost]);
					}
					else{
						$levelList.$productData[$i] = implode(',', $_POST[$levelPost]);
					}
					 $levelList=$levelList.$productData[$i];
					if ($modc == $modc1.$productData[$i]) {
						$moduleList.$productData[$i] = implode(',', $_POST[$topicPost]);
					}
					else{
						$moduleList.$productData[$i] = implode(',', $_POST[$topicPost]); 
					}
					 $moduleList=$moduleList.$productData[$i];
					 
					if ($chapc == $chapc1.$productData[$i]) {
						$chapterList.$productData[$i] = implode(',', $_POST[$chapterPost]); 
					}
					else{
						$chapterList.$productData[$i] = implode(',', $_POST[$chapterPost]); 
					}
					$chapterList=$chapterList.$productData[$i];	
					if($levelList!=''){
						$centerObj->updateBatchDataByDetails($bId,$centerId,$product_id,$levelList,$moduleList,$chapterList);
					}
			   }
			   if($batchData){
				
					 $_SESSION['succ'] = 2;
					 $_SESSION['msg'] ="$batch updated successfully."; 
					// echo "<pre>";print_r($batchData);exit;
					header("Location:../batchList.php");
					exit;
				 }else{
					 $_SESSION['error'] =1;
					 $_SESSION['msg'] ="$batch not saved. Please try again.";
					 header("location:../batchList.php");
					  exit;
				 } 
          }
   }
   
}

?>
