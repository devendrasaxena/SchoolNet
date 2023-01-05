<?php
include_once('header/lib.php');

include_once 'header/header.php';
include_once 'controller/productController.php';
include_once('controller/placementController.php'); 
$productPath='user/dashboard.php';

unset($_SESSION['topic_edge_id']);
unset($_SESSION['nid']);
unset($_SESSION['course_id']);
unset($_SESSION['lid']);
unset($_SESSION['activity']);
unset($_SESSION['product_id']);
$client_id=$_SESSION['client_id'];
$proObj = new productController();
$placementObj = new placementController();


//$getProductUserInfo=$proObj->getPaymentStatusByUserId($userId);
//$getProduct=$proObj->getProduct();


/* $userBatchInfo = $adminObj->getUserBatch($_SESSION['user_id']);
$batch_id=$userBatchInfo[0]['batch_id'];
$batchDataDetails = $centerObj->getBatchDataByIDDetails($batch_id,$center_id,$product_id=null);
 */
$userBatchInfo = $adminObj->getUserBatch($_SESSION['user_id']);
$batch_id=$userBatchInfo[0]['batch_id'];
if($_SESSION['batch_id']!=""){
	$batch_id=$_SESSION['batch_id'];
}else{
$batch_id=$userBatchInfo[0]['batch_id'];
 $active="active";	 
}
$courseType='0';//WBT Course Only

  if(SHOW_PRE_EXAM==1){
	    //echo "<pre>";print_r($_SESSION);exit;
	       $testTrackInfo = $placementObj->placementTestTracking($user_id,$batch_id,0,$region_id,'pre');
		    //echo "<pre>";print_r($testTrackInfo);//exit;
		   if(empty($testTrackInfo)){
			   if(isset($_SESSION['is_skip']) && $_SESSION['is_skip']==1){
					$showTest=0;
					$level_assigned='';
					$testUrl=''; 
			   }else{
					 $showTest=1;
					 $level_assigned='';
					 $examType=$exam_type[0];
					 //$testUrl=$testTrackInfo['placement_url'];  
					 $testUrl=$globalLink.'/user/pTest.php?user_id='.base64_encode($_SESSION['user_id'].'&type='.base64_encode($examType));//$testTrackInfo['placement_url'];  
			   }

		   }else{
			   $level_assigned=$testTrackInfo['level_assigned'];
			   $testUrl='';
			   $showTest=0;

		  }
	  }else{
		   $showTest=0;
		   $level_assigned='';
	}
?>
<section class="scrollable scrollableZindex" style="padding: 10px 40px;">
<div class="sliderBg">
<ul class="nav nav-tabs">
 <?php  if(count($userBatchInfo)>0){
	foreach($userBatchInfo as $key => $bValue){
		
		$batch_id2=$bValue['batch_id'];
		$batchNameData=$adminObj->getBatchNameByID($batch_id2);
		//echo "<pre>";print_r($batchNameData[0]['batch_name']);//exit; 
		$active=($_SESSION['batch_id']==$bValue['batch_id'])? "active" : "active";	
		//$selected=(is_array($batchId) && in_array($bValue['batch_id'], $batchId))  ? "selected" : "";	
	?>
  <li class="<?php echo $active;?>" ><a data-toggle="tab"><?php echo $batchNameData[0]['batch_name'];?></a></li>
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
//echo "<pre>";print_r($getProductData);//exit;  
$getVisitProduct=$proObj->getVisitingProduct($userId);

if(count($getProductData)==1){
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
		
      //echo "<pre>";print_r($productInfo);exit;  
 	
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
	
} else if(count($getProductData)>=1){ 
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
	  $productPath=$productPath;
	  $productInfo=$proObj->getProdcutDetailById($val);
	  $batchId=$getProductBatchData[$val];
	  $product_master_id=$productInfo['master_products_ids'];

	  //echo "<pre>";print_r($productInfo);
	  $package_code=$productInfo['package_code'];
	  $_SESSION['package_code'] = ($package_code!="")?$package_code:''; 
	 // $testTrackInfo = $placementObj->placementTracking($userToken,$productInfo['id']);
	  //echo "<pre>";print_r($testTrackInfo);//exit;
	  
		//if($productInfo['id']=='3'){

	 ?>
     <li class="col-md-4 text-center slideContentTopic item chap-asmt-box" pid="<?php echo $productInfo['id'];?>" style="height: 360px;"><a id="pro<?php echo $i;?>" href="javascript:void(0)" path="<?php echo $productPath;?>" onclick="visitProduct('<?php echo $i;?>','<?php echo $batchId;?>','<?php echo $productInfo['id'];?>','<?php echo $productPath;?>','<?php echo $package_code;?>','<?php echo $showTest;?>','<?php echo $level_assigned;?>')" style="height: 360px;">
	   <div class="relative">
	   <?php if($productInfo['thumbnail']==''){?> 
		  <img class="courseImg" src="<?php echo $product_img_hosting_url;?>/product<?php echo $i;?>.jpg" />  
	   <?php }else{?>
        <img class="courseImg" src="<?php echo $product_img_hosting_url;?><?php echo $productInfo['thumbnail']; ?>" />
	   <?php }?>
     </div>                                
	 	<div class="clear chapterTxtBg" >
         <h6><?php echo $productInfo['product_name'];?></h6>
          <p class="description" style="height:100px"><?php echo $productInfo['product_desc']; ?></p>
		
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

<div id="showquizModel" class="modal fade regCnfModel">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
    <div class="modal-header" id="testHeader" style="display:none">  
		<h4 class="modal-title"><span class="pull-right closeBg"> <a href="javascript:void(0)" onClick="cancelTest();" class="closeLinkTimes" id="cancelTest" title="Close" style="top: 20px;
right: 10px; color: #111;position: absolute;z-index: 99;"><i class="fa fa-times"></i></a></span></h4>
	  </div>
			<div class="modal-body " style="overflow:hidden;background-color: #f3f6f7;">
			<div class="iframe-container" id="iframe-container">
		    <iframe id="iframeId"  class="iframeId" target="_PARENT" allow="autoplay"  allowfullscreen src="<?php echo $testUrl;?>" width='100%' style="width:100%;border:0px;" border='0' frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" > </iframe>
		  </div>
			<?php 	//include_once 'pTest.php';
      // die;?>
			</div>
			

		</div>
	</div>
</div>
	


<?php include_once 'footer/footer.php';?>


<script>
var showTest='<?php echo $showTest;?>';
 function visitProduct(id,bid,pid,path,package_code,showTest,levelAssign){
	//alert(showTest)
	if(showTest=='1'){
		showquizModel();
	}else{
		
		 if(pid!==''){
		  var data = {action: 'set_visitproduct',product_id:pid,batch_id:bid,package_code:package_code};
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

}
function cancelTest(){
	window.location.href="product.php";
}
if(showTest=='1'){
		showquizModel();
}
function showquizModel(){
	$("#showquizModel").modal({
	backdrop: 'static',
	keyboard: true,  // to prevent closing with Esc button (if you want this too)
	show: true
	});
} 

  function showFullScreenCourse(){
	   var vHeight=$( window ).height();
     // Returns height of HTML document
     //var vHeight=$( document ).height();
     vHeight=vHeight-32;
	
	$('#showquizModel').css("height",vHeight+"px");
	$('#showquizModel .modal-dialog').css("margin-top","20px");
	$('#showquizModel .modal-body').css("height",vHeight-20+"px");
	$('#iframe-container').css("height",vHeight+"px");
	$('#iframeId').css("height",vHeight-20+"px");
	$('#iframeId').css("height",vHeight-20+"px");
   //iframe-container
   }
    showFullScreenCourse();
window.addEventListener("resize", showFullScreenCourse()); 


</script>

<?php }else{
	
	$_SESSION['product_id'] = '';
	if($firstTimeLogin==""){
		  header('location:user/dashboard.php');
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
?>
