<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');

$adminObj = new adminController();

if(isset($_POST['createBatch']) && $_POST['createBatch']!=''){
	  echo "<pre>";print_r( $_POST);exit;
		$batch = addslashes(trim($_POST["batch"]));
		
		  $obj=new $obj;
		
		  if(isset($_POST["section"]) && $_POST["section"]!=''){
	        $section = addslashes(trim($_POST["section"]));
            $obj->section =$section;
		  }
		
            $obj->batch_name =$batch;
			
		    //echo "<pre>";print_r($obj);exit;
		    $_SESSION['batch_details'] = $obj;
			
			$batch = $adminObj->createBatch($obj);
			//echo "<pre>";print_r($batch);exit;
		
			 if($batch){
				$batch = (object) $batch;
				header("Location: ../batch.php");
				exit;
			 }else{
				 header("location:../createBatch.php?error");
				  exit;
				} 
		

	/* }else{
         header("Location: ../createCenter.php?error");
		 exit;
    } */

}

?>