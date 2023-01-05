<?php
include_once('../../header/lib.php');
/*
Check Post Type Save,Edit,Del Then Call condition according to That;
*/
 $manageproductObj = new productController();  //Contrler Object For Executing Database Task
 
if ((isset($_POST['name'])) && ($_POST['name'] != "") && (empty($_POST['product_id']))) {
   
    $client_id = $_POST['client_id'];
    $product_name = $_POST['name'];
    $price = $_POST['price'];
    $thumbnail = '';
    if(isset($_POST['expire_on'])){
        /*$today = date("Y-m-d");
        $numofdays = trim($_POST['expire_on']);
        $exipredate = date('Y-m-d', strtotime($today. ' + '.$numofdays.' days'));*/
        $exipredate = $_POST['expire_on'];
    }
    //echo $exipredate;exit;
    if (isset($_FILES['prod_thumbnail'])) {
        $allowed_ext = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $file_name = $_FILES["prod_thumbnail"]["name"];
        $file_type = $_FILES["prod_thumbnail"]["type"];
        //$file_size = $_FILES["prod_thumbnail"]["size"];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $ProdThumbName = 'Thumb-'.str_replace(" ","-",$_POST['name']).'.'. $ext;
        if (in_array($file_type, $allowed_ext)) {
            if (move_uploaded_file($_FILES["prod_thumbnail"]["tmp_name"], "../../images/product_thumb/" . $ProdThumbName)) {
                $thumbnail = 'images/product_thumb/' . $ProdThumbName;
            }
        }
    }else{
		$thumbnail='product.jpg';
	}
	$productmasterarray = array();
    foreach ($_POST['level'] as $val) {
        $prodid_master = $manageproductObj->getCourseProductid($val);
        $prodid_master = $prodid_master[0]['product_id'];
        if(!in_array ($prodid_master, $productmasterarray)){
            array_push($productmasterarray,$prodid_master);
        }
    }
    $master_products_ids = implode(',', $productmasterarray);
	$master_products_ids2 = implode('', $productmasterarray);
    $str1=str_replace(" ", "", $product_name);
	 if(strlen($str1)>7){
		$str1=substr($str1,0,7).'0'.$master_products_ids2; 
	 }else{
		$str1=$str1.'0'.$master_products_ids2; 
	 }
    $code =strtoupper($str1);
	$package_code = strtoupper($str1);
    $currency_code = $_POST['currency_code'];
    $discount = $_POST['discount'];
    $discount_type = $_POST['discount_type'];
    $product_desc = $_POST['product_desc'];
    $status = $_POST['status'];
    
    $dataarray[] = "('$client_id','$product_name','$product_desc','$price','$thumbnail','$code','$package_code','$currency_code','$discount','$discount_type','$master_products_ids','$exipredate','$status')";
    $prodinsertedid = $manageproductObj->saveproduct($dataarray);
    $levelList = implode(',', $_POST['level']);
    $moduleList = implode(',', $_POST['module']);
    $chapterList = implode(',', $_POST['chapter']);
    $LevelListData[] = "('$prodinsertedid','Level','$levelList')";
    $ModuleListData[] = "('$prodinsertedid','Topic','$moduleList')";
    $ChapterListData[] = "('$prodinsertedid','Chapter','$chapterList')";
    $ProductConfigLevl = $manageproductObj->saveproductconfig($LevelListData);
    $ProductConfigModule = $manageproductObj->saveproductconfig($ModuleListData);
    $ProductConfigChapter = $manageproductObj->saveproductconfig($ChapterListData);
    $result =  'LevelList ' . $ProductConfigLevl . 'ModuleList ' . $ProductConfigModule . 'ChapterList ' . $ProductConfigChapter;
    //$result =  'LevelList ' . $ProductConfigLevl ;
   
	header("Location: ../customProductList.php");
	
} elseif ((isset($_POST['name'])) && ($_POST['name'] != "") && (!empty($_POST['product_id']))) {
	
    //echo "<pre>";print_r($_POST);//exit;
	$client_id = $_POST['client_id'];
    $pid = $_POST['product_id'];
    $product_name = $_POST['name'];
    $price = $_POST['price'];
    $code = $_POST['code'];
	$package_code = $_POST['code'];
    $currency_code = $_POST['currency_code'];
    $discount = $_POST['discount'];
    $discount_type = $_POST['discount_type'];
	$product_desc = $_POST['product_desc'];
    $status = $_POST['status'];
    $productmasterarray = array();
    foreach ($_POST['level'] as $val) {
        $prodid_master = $manageproductObj->getCourseProductid($val);
        $prodid_master = $prodid_master[0]['product_id'];
        if(!in_array ($prodid_master, $productmasterarray)){
            array_push($productmasterarray,$prodid_master);
        }
    }
	
	
    $master_products_ids = implode(',', $productmasterarray);
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
    }else{
		$thumbnail='product.jpg';
	}
	
    if (!empty($price)) {
        //$data = "`price`='$price',`code`='$code'";
        $data = "`price`='$price',`code`='$code',`currency_code`='$currency_code',`discount`='$discount',`discount_type`='$discount_type'";
        $updateprice = $manageproductObj->updateproduct($pid, $data);
    }
    $statusQuery = "`status`='$status',`product_name`='$product_name',`product_desc`='$product_desc'";
    $master_products_status_update = $manageproductObj->updateproduct($pid, $statusQuery);
    $levelList = implode(',', $_POST['level']);
    $moduleList = implode(',', $_POST['module']);
    $chapterList = implode(',', $_POST['chapter']);
    //echo "<pre>";print_r($levelList);exit;
    $UpdateProductConfigLevel = $manageproductObj->UpdateProductConfigByTypeID($pid, 'Level', $levelList);
    $UpdateProductConfigModule = $manageproductObj->UpdateProductConfigByTypeID($pid, 'Topic', $moduleList);
    $UpdateProductConfigChapter = $manageproductObj->UpdateProductConfigByTypeID($pid, 'Chapter', $chapterList);
    header("Location: ../customProductList.php");
} elseif(isset($_POST['check_prod_name']) && !empty($_POST['check_prod_name'])){
    $productname = $_POST['check_prod_name'];
    $uniqproduct = $manageproductObj->ProductExitsByName($productname);
    echo ($uniqproduct)?'True':'False';
}elseif(isset($_POST['check_prod_code']) && !empty($_POST['check_prod_code'])){
    $productcode = $_POST['check_prod_code'];
    $uniqproduct = $manageproductObj->ProductExitsByCode($productcode);
    echo ($uniqproduct)?'True':'False';
}else {
    header("Location: ../addCustomProduct.php");
}
