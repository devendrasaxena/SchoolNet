<?php

// $_html_relative_path;exit;
include_once('header/lib.php');
if(!isset($_SESSION['user_id'])){
	header('location:index.php');
}
include_once('header/global.php');
include_once 'controller/productController.php';

   $proObj = new productController();
    
    $userInfo=userdetails($user_id);
    $email_id=$userInfo->email_id;
	$uToken=getUserRefreshToken($email_id,$user_id);
	$package_code=$_SESSION['package_code'];
	$userToken=$uToken->token;
	
	$getVisitProduct=$proObj->getVisitingProduct($user_id);
	
if($_POST['action'] == 'set_visitproduct'){
	$pId=$_POST['product_id'];
	$bId=$_POST['batch_id'];
	$packageCode=$_POST['package_code'];
	
	$_SESSION['product_id']=$pId;
	$_SESSION['batch_id']=$bId;
	$_SESSION['package_code']=$packageCode;
	 if($getVisitProduct){//update visit
	     $status=1;
		      if($getVisitProduct['current_product_id']!=$pId){
					$old_product=$getVisitProduct['current_product_id'];		   
					$updateVisitProduct=$proObj->updateUserCurrentProductVisit($user_id,$pId,$old_product);
					  if($updateVisitProduct){
						 echo json_encode( array('status' => $status, 'res' => $pId));
                          die;  
					  }else{ 
						 echo json_encode( array('status' => $status, 'res' => $pId));
                          die;  
					  }
				}else{
					echo json_encode( array('status' => $status, 'res' => $pId));
                     die; 
			   }  

	}else{//insert visit

			  $setVisitProduct=$proObj->setUserCurrentProductVisit($user_id,$pId);
			  if($setVisitProduct){
				echo json_encode( array('status' => $status, 'res' => $pId));
                     die; 
			  }
			  

	  }	
}

if($_POST['action'] == 'set_batch'){
	$bId=$_POST['batch_id'];
	$_SESSION['batch_id']=$bId;
	$status=1;
	echo json_encode( array('status' => $status, 'res' => $bId));
    die; 
			  
}