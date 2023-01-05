<?php
include_once('../../header/lib.php');

//echo "<pre>";print_r($_POST);//exit;
/*
Check Post Type Save,Edit,Del Then Call condition according to That;
*/
 $manageproductObj = new productController();  //Contrler Object For Executing Database Task

if ((isset($_POST['mname'])) && ($_POST['mname'] != "") && (empty($_POST['master_product_id']))) {
   

    $name = $_POST['mname'];
	$publish='yes';
	$is_show_dashboard=1;
	$is_show_lti=0;
	
    $dataarray[] = "('$name','$publish','$is_show_lti','$is_show_dashboard')";
    //echo "<pre>";print_r($dataarray);
	$master_product_id = $manageproductObj->savemasterproduct($dataarray);
  
	$master_products_ids = $master_product_id;
    if(!empty($master_products_ids)){
	 $client_id = $_POST['client_id'];
     $product_name = $_POST['mname'];
	 
     $price = 0;
     $thumbnail = 'product.jpg';
    //echo $exipredate;exit;
     $str1=str_replace(" ", "", $product_name);
	 if(strlen($str1)>7){
		$str1=substr($str1,0,7).'0'.$master_product_id; 
	 }else{
		$str1=$str1.'0'.$master_product_id; 
	 }
    $code =strtoupper($str1);
	$package_code = strtoupper($str1);
    $currency_code = 0;
    $discount = 0;
    $discount_type = 0;
    $exipredate='';
    $status = 1; 
	$product_type='master';
	$master_products_ids = $master_products_ids;
    $productarray = array();
   
   
    $productarray[] = "('$client_id','$product_name','$price','$thumbnail','$code','$package_code','$currency_code','$discount','$discount_type','$master_products_ids','$exipredate','$status','$product_type')";
    //echo "<pre>";print_r($productarray);
	$prodinsertedid = $manageproductObj->mapmasterproduct($productarray);
     
	  header("Location: ../productList.php");
	}
} elseif ((isset($_POST['master_product_id'])) && ($_POST['master_product_id'] != "") &&(isset($_POST['name'])) && ($_POST['name'] != "") && (!empty($_POST['product_id']))) {
   // echo "<pre>";print_r($_POST);exit;
	
    $pid = $_POST['product_id'];
    $product_name = $_POST['name'];
	$product_description = $_POST['description'];
    $status = $_POST['status'];
    $master_product_id = $_POST['master_product_id'];
	
	$cCode = $_POST['cCode'];
	$coursecode_arr = explode(',', $cCode);
	//echo "<pre>";print_r($coursecode_arr);exit;
	if(count($coursecode_arr)>0){
        foreach($coursecode_arr as $key=>$value){
			//echo "<pre>";print_r($value);
			$sequence_id=$key+1;
            $course_products_ids_update = $manageproductObj->updateProductInCourse($value, $master_product_id,$sequence_id);
		}
	 }
	 //echo "<pre>";print_r($coursecode_arr);exit;
    $master_products_ids = $master_product_id;
    if(!empty($master_products_ids)){
        $data = "`master_products_ids`='$master_products_ids',`client_id`='$client_id'";
        $master_products_ids_update = $manageproductObj->updateproduct($pid, $data);
    }
    if (isset($_FILES['prod_thumbnail'])) {
        $allowed_ext = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $file_name = $_FILES["prod_thumbnail"]["name"];
        $file_type = $_FILES["prod_thumbnail"]["type"];
        //$file_size = $_FILES["prod_thumbnail"]["size"];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $ProdThumbName = 'Thumb-' .str_replace(" ","-",$_POST['name']).'.'. $ext;
        if (in_array($file_type, $allowed_ext)) {
            if (move_uploaded_file($_FILES["prod_thumbnail"]["tmp_name"], "../../images/product_thumb/" . $ProdThumbName)) {
                $thumbnail = 'images/product_thumb/' . $ProdThumbName;
                $data = "`thumbnail`='$thumbnail'";
                $updatethumb = $manageproductObj->updateproduct($pid, $data);
            }
        }
    }
	$descQuery = "`product_desc`='$product_description'";
    $master_products_product_desc_update = $manageproductObj->updateproduct($pid, $descQuery);
     
   
    $statusQuery = "`status`='$status'";
    $master_products_status_update = $manageproductObj->updateproduct($pid, $statusQuery);
      header("Location: ../productList.php");
	  
} elseif(isset($_POST['check_prod_name']) && !empty($_POST['check_prod_name'])){
    $productname = $_POST['check_prod_name'];
    $uniqproduct = $manageproductObj->ProductExitsByName($productname);
    echo ($uniqproduct)?'True':'False';
}elseif(isset($_POST['check_prod_code']) && !empty($_POST['check_prod_code'])){
    $productcode = $_POST['check_prod_code'];
    $uniqproduct = $manageproductObj->ProductExitsByCode($productcode);
    echo ($uniqproduct)?'True':'False';
}else {
    header("Location: ../masterProduct.php");
}
