<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';
ini_set('post_max_size', '300M');
ini_set('upload_max_filesize', '300M');
ini_set('max_execution_time', 20000000); 
//print_r($_FILES);exit;
$user_id = $_POST['uId'];
//print_r($_POST);exit;
$fileName = $_FILES['file']['name'];
//$target_dir ='../profile_pic/'; default
$target_dir =dirname(dirname(dirname(dirname(__FILE__)))).'/emp/view/profile_pic/';

//print_r($target_dir);exit;
$status = uploadFiles($target_dir,$fileName,$user_id);
//echo json_encode($status);

function uploadFiles($target_dir,$fileName,$user_id){
	$msg = "";
//	$target_file = $target_dir . basename($fileName);
	//echo $target_file;exit;
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$pi = pathinfo($fileName);
	$fileTxt = substr($pi['filename'],0,30);
	$fileExt = $pi['extension'];
	//$fileName = $fileTxt."-".time().".".$fileExt;
	$fileName = $user_id."-".$fileTxt.".".$fileExt;
	$target_file = $target_dir . basename($fileName);
	// Check file size
	if ($_FILES['file']["size"] > 20971520) {
		$msg = "large";
		$uploadOk = 0;
		//return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '');
		echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => ''));
		die();		 
	}
	
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 1) {
	     //echo $target_file;
		if(move_uploaded_file($_FILES['file']['tmp_name'],$target_file)) {
			//file_put_contents('t.txt',$target_file);
			//echo $target_file;exit;
			//$msg = $msg."The file ". basename($fileName). " has been uploaded successfully.";
			$msg = "The file has been uploaded successfully.";
			//echo $msg;exit;
			$file = $target_file;
			$ftype = 'application/octet-stream';
			$finfo = @new finfo(FILEINFO_MIME);
			$fres = @$finfo->file($file);
			if (is_string($fres) && !empty($fres)) {
			  $ftype = $fres;
			}
			//echo $fileExt."===";
			$app = explode(';',$ftype);
			//echo $app[0];exit;
			if(strtolower($fileExt) == 'jpeg' && $app[0] == 'image/jpeg'){
				$uploadOk = 1;
			//return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' =>$fileName);		
			 echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => $fileName));	 
			 die();		
			}else if(strtolower($fileExt) == 'jpg' && $app[0] ==  'image/jpeg'){
				$uploadOk = 1;
				//return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => $fileName);
			 echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => $fileName)); 
			 die();
			}else if(strtolower($fileExt) == 'png' && $app[0] ==  'image/png'){
				$uploadOk = 1;
				//return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => $fileName);	
				echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => $fileName));	 
				die();				
			}else{	
				  if($uploadOk==1){
					echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '')); die();	  
				  }else{
				   $uploadOk = 0;
				  $msg = "invalid";
					//return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '');	
				echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '')); die();	
				
			}
		  }			
		} else {
			 $uploadOk = 0;
			 $msg = "invalid path";
		   // return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '');	
		    echo  json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '')); die();
		}
	}else{
	 $msg = "invalid";
		//return array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '');	
	 echo json_encode(array('msg' => $msg, 'uploadOk' => $uploadOk, 'fileName' => '')); die();	
	}	

}

?>