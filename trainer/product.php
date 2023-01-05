<?php 
include_once('../header/trainerHeader.php');
include_once '../controller/productController.php';
$productPath='learning_module.php';

unset($_SESSION['topic_edge_id']);
unset($_SESSION['nid']);
unset($_SESSION['course_id']);
unset($_SESSION['lid']);
unset($_SESSION['activity']);
unset($_SESSION['product_id']);
$client_id=$_SESSION['client_id'];
$proObj = new productController();
	
$userBatchInfo = $adminObj->getUserBatch($_SESSION['user_id']);
$batch_id=$userBatchInfo[0]['batch_id'];
if($_SESSION['batch_id']!=""){
	$batch_id=$_SESSION['batch_id'];
}else{
$batch_id=$userBatchInfo[0]['batch_id'];
 $active="active";	 
}
$courseType='1';//ILT Course Only
?>
<section class="scrollable scrollableZindex"  style="padding: 10px 0px;">
<div class="sliderBg">
<ul class="nav nav-tabs" style="margin: 0px 20px;">
 <?php  if(count($userBatchInfo)>0){
	foreach($userBatchInfo as $key => $bValue){
		
		$batch_id2=$bValue['batch_id'];
		$batchNameData=$adminObj->getBatchNameByID($batch_id2);
		//echo "<pre>";print_r($batchNameData[0]['batch_name']);//exit; 
		$active=($_SESSION['batch_id']==$bValue['batch_id'])? "active" : "";	
		//$selected=(is_array($batchId) && in_array($bValue['batch_id'], $batchId))  ? "selected" : "";	
	?>
  <li class="<?php echo $active;?>" onclick="setBatchList(<?php echo $bValue['batch_id'];?>);"><a data-toggle="tab" href="#batch<?php echo $bValue['batch_id'];?>"><?php echo $batchNameData[0]['batch_name'];?></a></li>
	<?php  } ?>
</ul>	
<?php }else{?>
      There is not Class and product
<?php }?>
 
<?php 
 
  $batchDataDetails = $centerObj->getBatchDataByIDDetails($batch_id,$center_id,$product_id=null);

  $getProductData = array();
  $getProductBatchData = array();
  foreach ($batchDataDetails  as $key => $value) {
	 $courseArr= $value['course'];
	 $checkProductAndCourseType=$proObj->checkProductAndCourseType($courseType,$courseArr);
	 //echo "<pre>";print_r($checkProductAndCourseType);		 
	 if($checkProductAndCourseType){
		$getProductData[] = $value['product_id'];
		$getProductBatchData[$value['product_id']]=$value['batch_id'];
	 }
   } 
//echo "<pre>";print_r($getProductBatchData);exit;  
$getVisitProduct=$proObj->getVisitingProduct($userId);
if(count($getProductData)==0){
	$product_id = trim($getProductData[0]);
	$productInfo=$proObj->getProdcutDetailById($product_id);
	$product_master_id = trim($productInfo['master_products_ids']);
	$batch_id=$getProductBatchData[$product_id];
		//set product id in session
		$_SESSION['product_id'] = $product_id;
		$_SESSION['product_standard_id'] = $product_master_id;
		$_SESSION['batch_id']=$batch_id;
		$package_code=$productInfo['package_code'];
		$_SESSION['package_code'] = ($package_code!="")?$package_code:'';
		if($getVisitProduct){ //update visit
			header("Location:".$productPath);exit;		 
		}else{//insert visit
	
				$setVisitProduct=$proObj->setUserCurrentProductVisit($userId,$getProductData[0]['product_id']);
				if($setVisitProduct){
					if($firstTimeLogin==""){
						  header("Location:".$productPath);
							exit;
						}else{
						   //header('location:welcome.php');
						   $_SESSION['default'] = 0;
							  $firstVisitArr=json_decode($firstTime_login);
							if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
							   $_SESSION['headTitle']="Resume";
							}else{
								  $_SESSION['headTitle']="Start";
							}
							header("Location:".$productPath);
							exit;
						   
						}
				}
		  }
	  
  
}else if(count($getProductData)>=1){ 
//echo "<pre>vv";print_r($getProductData);//exit; 
?>
   <div class="sliderWrapperTopic carousel slide col-md-12 col-sm-12 nonCircular" id="Carousel" style="height:auto">
   <div class="carousel-inner" style="height:auto">
   <ul class="carousel-innerList">
 <?php 
     $i=1;
	 $col  = 'id';
	 $sort = array();
	 foreach ($getProductData as $i => $obj) {
		  $sort[$i] = $obj->{$col};
		}
	 array_multisort($sort, SORT_ASC, $getProductData);
     foreach($getProductData as $key=>$val ){
      //echo "<pre>";print_r($val);		 
	  $productPath=$productPath;
	  $productInfo=$proObj->getProdcutDetailById($val);
	  $batchId=$getProductBatchData[$val];
	  $product_master_id=$productInfo['master_products_ids'];
	 
	  $batchId=$getProductBatchData[$val];
	  $package_code=$productInfo['package_code'];
	  $_SESSION['package_code'] = ($package_code!="")?$package_code:''; 
	
	  //echo "<pre>";print_r($val['product_id']);
	 ?>
     <li class="col-md-4 text-center slideContentTopic item chap-asmt-box" pid="<?php echo $productInfo['id'];?>"><a id="pro<?php echo $i;?>" href="javascript:void(0)" path="<?php echo $productPath;?>" onclick="visitProduct('<?php echo $i;?>','<?php echo $productInfo['id']?>','<?php echo $batchId;?>','<?php echo $productPath;?>')">
	   <div class="relative">
	   <?php if($productInfo['thumbnail']==''){?> 
		  <img class="courseImg" src="<?php echo $product_img_hosting_url;?>/product<?php echo $i;?>.jpg" />  
	   <?php }else{?>
        <img class="courseImg" src="<?php echo $product_img_hosting_url;?><?php echo $productInfo['thumbnail']; ?>" />
	   <?php }?>
     </div>                                
	 	<div class="clear chapterTxtBg">
         <h6><?php echo $productInfo['product_name'];?></h6>
          <p class="description"><?php echo $productInfo['product_desc']; ?></p>
		
       <div class="bottomSection">
		<div class="pull-center"><?php echo $start_module;?></div>
	    </div></div>
	 </a></li>
	 <?php $i++;
     }?>
 </ul>
 </div>
</div>
</section>
<?php include_once('../footer/trainerFooter.php');?>

<script>
 function visitProduct(id,pid,bid,path){
	 if(pid!==''){
	  var data = {action: 'set_visitproduct',product_id:pid,batch_id:bid};
        $.ajax({url: 'set_visit_product_ajax.php', type: 'post', dataType: 'json', data: data, async: false,
           success : function(data){
			  console.log(data.status)
		  if(data.status==1){
			window.location.href='<?php echo $productPath;?>'; 
			$("#loaderDiv").hide();
		  }else{
			console.log(data.status)
		  }
		},
            error: function () {}
        });
	 }

}
function setBatchList(bid){
	// var bid = $('#batch_id option:selected').val();
	 if(bid!==''){
	  var data = {action: 'set_batch',batch_id:bid};
		$.ajax({url: 'set_visit_product_ajax.php', type: 'post', dataType: 'json', data: data, async: false,
		   success : function(data){
			  console.log(data.status)
		  if(data.status==1){
			  location.reload();
			  
			  $("#loaderDiv").hide();
		  }else{
			console.log(data.status)
		  }
		},
			error: function () {}
		});
	 }

}
</script>
<?php }else{
	
	$_SESSION['product_id'] = '';
	if($firstTimeLogin==""){
		  header('location:learning_module.php');
		  exit;
		}else{
		   //header('location:welcome.php');
		   $_SESSION['default'] = 0;
			  $firstVisitArr=json_decode($firstTime_login);
			if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
			   $_SESSION['headTitle']="Resume";
			}else{
				  $_SESSION['headTitle']="Start";
			}
			header("Location:".$productPath1);
			exit;
		   
		} 
 } 
?>
