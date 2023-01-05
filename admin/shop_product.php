<?php
include_once('header/lib.php');
//echo "<pre>";print_r($_SESSION);exit;
include_once 'header/header.php';
 unset($_SESSION['product_id']);
$client_id=$_SESSION['client_id'];
$uId=$_SESSION['user_id'];
$proObj = new productController();
$voucherObj = new voucherController();
$userInfo=userdetails($uId);
$email_id=$userInfo->email_id;
$domain_name = substr(strrchr($email_id,"@"),1);
/* ini_set('display_errors',1);
error_reporting(E_ALL);   */
$req=$_REQUEST['exp'];

if($req=='1'){
	
  $showExpClass="active";
  $showExp="active show";  
  $showActClass="";	
  $showAct="";
  
}else{
	$showActClass="active";	
	$showAct="active show";
	$showExpClass="";
	$showExp="show";

}


 $getProduct=$proObj->getMasterProductDetails();
 $getProductUserInfo=$proObj->getExpiredProductByUserId($uId);
 $getVoucherProductList=$voucherObj->getProductByUserVoucher($uId);
 $expired=array();
 $voucher_product_id_arr=array();
if($getVoucherProductList){

	foreach ($getVoucherProductList as $key => $val) {
	  $duration=$val['duration'];
	  $productid=$val['product_id'];
	  $used_date =$val['used_date'];
	  $expiry_on=date('d-m-Y',strtotime($used_date.'+'.$duration.' day'));
	  $currdate = date('d-m-Y');
	  $expired[]=date('d-m-Y',strtotime($expire_date1));
	  if($expiry_on < $currdate){
		 $voucher_product_id_arr[]=$productid;
	   }
	}
}
$voucher_product_id_arr = array_unique($voucher_product_id_arr);
$getProductData=array();

$product_id_arr=array();
 if(count($getProductUserInfo)>0){
	
	foreach ($getProductUserInfo as $key => $val) {
	  $expire_date1=date('d-m-Y',strtotime($val->expiry_date));
	  $expired[]=date('d-m-Y',strtotime($expire_date1));
	  $productid=$val->master_products_ids;
	  $product_id_arr=explode(',',$productid);

	}
	
	if($voucher_product_id_arr){
		$product_id_arr =array_unique(array_merge($product_id_arr, $voucher_product_id_arr));// array_intersect($product_id_arr, $voucher_product_id_arr);
	}
	foreach ($product_id_arr as $key => $val) {
	    $getProductData[]=$proObj->getProdcutDetailById($val,'master');
	}
}
else{
	if($voucher_product_id_arr){
			 
		   if($voucher_product_id_arr){
			//echo "<pre>vo";print_r($voucher_product_id_arr);
			  $product_id_arr =array_unique(array_merge($product_id_arr, $voucher_product_id_arr));// array_intersect($product_id_arr, $voucher_product_id_arr);
			}
			
			foreach ($product_id_arr as $key => $val) {
				$getProductData[]=$proObj->getProdcutDetailById($val,'master');
			}
			
		}

}



$expired_product=$active_product=0;

?>
<style>
.productList h5{margin: 25px 25px 0px 25px;} 
.productList span{border-bottom: solid 2px #047margin: 25px 25px 25px 5px; padding: 0px 0px;font-size: 16px;text-transform: uppercase;} 
.productList a.active span.active,.productList a.active span{border-bottom: solid 2px #047a9c;} 
</style>
<section class="scrollable scrollableZindex">
<div class="sliderBg">
<div class="col-md-1 col-sm-1  pull-left">&nbsp;
</div>
 <div class="chapterTxtBg col-md-6 col-sm-6 pull-left">
   <div class="sliderWrapperTopic carousel slide nonCircular " id="Carousel">
 <ul class="nav nav-tabs" style="border:none">
  <li class="nav-item productList" id="activeLi">
    <a class="<?php echo $showActClass;?>" style="border:none" data-toggle="tab" href="#activeCourse"> <h5>  <span class="<?php echo $showActClass;?>" >Courses</span></h5></a>
  </li>
 <!--
  <li class="nav-item productList" id="expiryLi">
    <a class="<?php echo $showExpClass;?>" style="border:none" data-toggle="tab" href="#expireCourse">  <h5>  <span class="<?php echo $showExpClass;?>" >Expired Courses</span></h5></a>
  </li>-->

  </ul>
 
	<div class="tab-content">
     <div id="activeCourse" class="tab-pane fade <?php echo $showAct;?>">
		<div class="carousel-inner">
			<ul class="carousel-innerList">
			<?php 
			if(count($getProduct)>0){ 
			
			 $i=1;
			 foreach($getProduct as $key=>$val ){
					 
				if($val['master_products_ids']==2){
					$productPath=$productPath2;
					$shopPath= $ming_hub_url;
					$target= "_blank";
					$showShopButton='display:block';
				}else{
					$productPath=$productPath1;
					$shopPath= "cart.php?pid=".base64_encode($val['master_products_ids']);
					$target= "";
					$showShopButton='display:none';
				}
				$productStatus = $proObj->getProductStatusByUserId($user_id,$val['master_products_ids']);
				if($productStatus->status=='' || $productStatus->status=='active'){
			 ?>
			 <li class="col-md-6 text-center slideContentTopic item chap-asmt-box" pid="<?php echo $val['master_products_ids']?>" style="height: 420px;">
			   <div class="relative">
			   <?php if($val['thumbnail']==''){?>
				  <img class="courseImg" src="images/product<?php echo $i;?>.jpg" />  
			   <?php }else{?>
				<img class="courseImg" src="images/<?php echo $val['thumbnail']; ?>" />
			   <?php }?>
			 </div>                                
				<div class="clear chapterTxtBg" style="min-height: 100px;">
				 <h6><?php echo $val['product_name'];?></h6>
				 <!-- <p><?php echo $val['product_desc']; ?></p>-->
				<?php 
									if($productStatus->expiry_date!="" && $productStatus->reason==1){
									
										$expired_text= 'Product expires on : '.$productStatus->expiry_date;
									
									}elseif($productStatus->expiry_date!="" && $productStatus->reason==2){
										$center_name = $proObj->getCenterNameByCenterId($productStatus->center_id);
										if($center_name){
											if($productStatus->days_left>1){
											$expired_text= $center_name.' - '.$productStatus->days_left.' days left';
											}else{
												$expired_text= $center_name.' - '.$productStatus->days_left.' day left';
											}
										}else{
											$expired_text= 'Code expires on : '.$productStatus->expiry_date;
										}
										
									}elseif($productStatus->expiry_date!="" && $productStatus->reason==3){
										
										$expired_text= 'Trial expires on : '.$productStatus->expiry_date;
										
									}else{
										$expired_text= '';
									}
									
								?>
								<p class="" style="margin-bottom:0px;color:#007fa3;"><?php echo $expired_text; ?></p>
								<?php if($productStatus->expiry_date!="" && $productStatus->reason==3){
									if($proObj->checkDomain($domain_name)==true && $val['master_products_ids']==1){
										$addClass = 'hide';
									}else{$addClass = '';}
									?>
								<p class="<?php echo $addClass;?>" style="margin-bottom:0px;color:#007fa3;"> <?php echo $proObj->getProductTrailDays($val['master_products_ids']);?> <?php if($proObj->getProductTrailDays($val['master_products_ids'])>1){ echo 'days';}else{ echo 'day';}?> (demo)</p>
								<?php }?>
								
				</div>
			   <div class="bottomSectionDiv">
			   <?php if($productStatus->expiry_date=="" || $productStatus->reason!=1){?>
			   
			   <a id="btnVoucher" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 10px;margin-right:10px;margin-top:10px;" onclick="showVoucherModal(<?php echo $val['master_products_ids'];?>);" href="javascript:void(0)">Enter Code</a>

			   <?php }?>
			   
				<?php if($_SESSION['isActive']==1 && $productStatus->status=='active'){
					
						if($val['master_products_ids']==2){
								$_SESSION['product_id']=2;
								$go_to_link= $productPath2;
							
						}else{
					
							if($firstTime_login==""){
								if(($checkScore->imsx_messageIdentifier!="" && $checkScore->is_skip=='no') || $checkScore->is_skip=='yes'){
										$_SESSION['headTitle']="Start";
										$_SESSION['default']=0;
										$go_to_link= "score.php";
									}
								elseif(isset($checkScore->imsx_messageIdentifier) && $checkScore->imsx_messageIdentifier=='0'&& $checkScore->is_skip=='no'){
									$go_to_link= "welcome.php?pause=1";
								}else{
									$go_to_link= "welcome.php";
								}
								
							}else{
								$_SESSION['default'] = 0;
								$firstVisitArr=json_decode($firstTime_login);
								if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
								  $_SESSION['headTitle']="Resume";
								}else{
								  $_SESSION['headTitle']="Start";
								}
								$_SESSION['product_id']=1;
								$go_to_link= $productPath1;
							 
							} 
						}
		
						 ?>
						  
						<?php if($productStatus->expiry_date=="" || $productStatus->reason!=1){?>
							<a id="btnShop" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 10px;margin-right:10px;<?php echo $showShopButton?>" href="<?php echo $shopPath; ?>" target="<?php echo $target;?>">Buy Course</a>
							<?php if($showShopButton=='display:none'){?>
								<p style=" width:100%; line-height: 40px;height: 40px;margin-bottom: 10px;margin-right:10px;">&nbsp;</p>
							<?php }?>
						<?php } ?>
							<a id="" href="javascript:void(0)"  path="<?php echo $go_to_link;?>" onclick="visitProduct('<?php echo $i;?>','<?php echo $val['master_products_ids']?>','<?php echo $go_to_link;?>')">
								<div class="bottomSection" style="padding-top:0px;">
									<div class="text-center">Go to Course</div>
								
								</div>
							</a>
						<?php } 
					elseif($productStatus->status=='' && $_SESSION['isActive']==1){ ?>
						<a id="btnActivateTrial" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 10px;margin-right:10px;margin-top:0px;" href="javascript:void(0)" onclick="activateTrial('<?php echo $val['master_products_ids'];?>','<?php echo base64_encode($_SESSION['user_id']);?>');">Activate Trial</a>  
						<?php if($productStatus->expiry_date=="" || $productStatus->reason!=1){?>
						<a id="btnShop" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 0px;margin-right:10px;<?php echo $showShopButton?>" href="<?php echo $shopPath; ?>" target="<?php echo $target;?>">Buy Course</a>
						
			         <?php }?>
			
				
						<?php }?>
					
				
			  </div>
				
			 </li>
		  
		  <?php $i++;
			 $active_product++;
			 }elseif($productStatus->status=='expired'){
					$expired_product++;
				?>
				 <li class="col-md-6 col-sm-12 text-center slideContentTopic item chap-asmt-box" pid="<?php echo $val['master_products_ids']?>" style="height: 420px;">
				   <div class="relative">
				   <?php if($val['thumbnail']==''){?>
					  <img class="courseImg" src="images/product<?php echo $i;?>.jpg" />  
				   <?php }else{?>
					<img class="courseImg" src="images/<?php echo $val['thumbnail']; ?>" />
				   <?php }?>
				 </div>                                
					<div class="clear chapterTxtBg" style="min-height: 100px;">
					 <h6><?php echo $val['product_name'];?></h6>
					  <p><?php echo $val['product_desc']; ?></p>
					<?php 
									if($productStatus->expiry_date!="" && $productStatus->reason==1){
									
										$expired_text= 'Product expired : '.$productStatus->expiry_date;
									
									}elseif($productStatus->expiry_date!="" && $productStatus->reason==2){
										$center_name = $proObj->getCenterNameByCenterId($productStatus->center_id);
										if($center_name){
											$expired_text= $center_name. ' expired : '.$productStatus->expiry_date;
										}else{
											$expired_text= 'Code expired : '.$productStatus->expiry_date;
										}
										
									}elseif($productStatus->expiry_date!="" && $productStatus->reason==3){
										
										$expired_text= 'Trial expired : '.$productStatus->expiry_date;
										
									}else{
										$expired_text= '';
									}
									
								?>
								<p class="" style="color:#007fa3;"><?php echo $expired_text; ?></p>
								<?php if($productStatus->expiry_date!="" && $productStatus->reason==3){
									if($proObj->checkDomain($domain_name)==true && $val['master_products_ids']==1){
										$addClass = 'hide';
									}else{$addClass = '';}
										?>
									
								<p class="<?php echo $addClass;?>" style="color:#007fa3;"> <?php echo $proObj->getProductTrailDays($val['master_products_ids']);?> <?php if($proObj->getProductTrailDays($val['master_products_ids'])>1){ echo 'days';}else{ echo 'day';}?> (demo)</p>
								
								<?php }?> 
					</div>
				   <div class="bottomSectionDiv">
				   
				 
				   <a id="btnVoucher" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 10px;margin-right:40px;margin-top:10px;" onclick="showVoucherModal(<?php echo $val['master_products_ids'];?>);" href="javascript:void(0)">Enter Code</a>
					 <a id="btnShop" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 0px;margin-right:40px; <?php echo $showShopButton?>" href="<?php echo $shopPath; ?>" target="<?php echo $target;?>">Buy Course</a>

					<?php 					
							if($val['master_products_ids']==2){
									$_SESSION['product_id']=2;
									$go_to_link= $productPath2;
								
							}else{
						
								if($firstTime_login==""){
									if(($checkScore->imsx_messageIdentifier!="" && $checkScore->is_skip=='no') || $checkScore->is_skip=='yes'){
											$_SESSION['headTitle']="Start";
											$_SESSION['default']=0;
											$go_to_link= "score.php";
										}
									elseif(isset($checkScore->imsx_messageIdentifier) && $checkScore->imsx_messageIdentifier=='0'&& $checkScore->is_skip=='no'){
										$go_to_link= "welcome.php?pause=1";
									}else{
										$go_to_link= "welcome.php";
									}
									
								}else{
									$_SESSION['default'] = 0;
									$firstVisitArr=json_decode($firstTime_login);
									if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
									  $_SESSION['headTitle']="Resume";
									}else{
									  $_SESSION['headTitle']="Start";
									}
									$_SESSION['product_id']=1;
									$go_to_link= $productPath1;
								 
								} 
							}
			
							 ?>
							
						
						
					
				  </div>
					
				 </li>	
			 <?php }
			 
			 
			 
			 
			 
			 
			 } }?>
		 <?php if($active_product==0){
		 ?>
		 <!-- <div class="bottomSection text-left">
		 No Active Courses Available
		  </div>-->
		 <?php } ?>
		</ul>
		
		</div> 
 
</div> 
     <!--<div id="expireCourse" class="tab-pane fade <?php echo $showExp;?>">
	  <div class="carousel-inner">
			<ul class="carousel-innerList">
			<?php 
			if(count($getProduct)>0){ 
			
			$i=1;
				
			 foreach($getProduct as $key=>$val ){
					 
				if($val['master_products_ids']==2){
					$productPath=$productPath2;
					$shopPath= $ming_hub_url;
					$target= "_blank";
					$showShopButton='display:block';
				}else{
					$productPath=$productPath1;
					$shopPath= "cart.php?pid=".base64_encode($val['master_products_ids']);
					$target= "";
					$showShopButton='display:none';
				}
				
				$productStatus = $proObj->getProductStatusByUserId($user_id,$val['master_products_ids']);
				
				if($productStatus->status=='expired'){
					$expired_product++;
				?>
				 <li class="col-md-6 col-sm-12 text-center slideContentTopic item chap-asmt-box" pid="<?php echo $val['master_products_ids']?>" style="height: 420px;">
				   <div class="relative">
				   <?php if($val['thumbnail']==''){?>
					  <img class="courseImg" src="images/product<?php echo $i;?>.jpg" />  
				   <?php }else{?>
					<img class="courseImg" src="images/<?php echo $val['thumbnail']; ?>" />
				   <?php }?>
				 </div>                                
					<div class="clear chapterTxtBg" style="min-height: 100px;">
					 <h6><?php echo $val['product_name'];?></h6>
					  <p><?php echo $val['product_desc']; ?></p>
					<?php 
									if($productStatus->expiry_date!="" && $productStatus->reason==1){
									
										$expired_text= 'Product expired : '.$productStatus->expiry_date;
									
									}elseif($productStatus->expiry_date!="" && $productStatus->reason==2){
										$center_name = $proObj->getCenterNameByCenterId($productStatus->center_id);
										if($center_name){
											$expired_text= $center_name. ' expired : '.$productStatus->expiry_date;
										}else{
											$expired_text= 'Code expired : '.$productStatus->expiry_date;
										}
										
									}elseif($productStatus->expiry_date!="" && $productStatus->reason==3){
										
										$expired_text= 'Trial expired : '.$productStatus->expiry_date;
										
									}else{
										$expired_text= '';
									}
									
								?>
								<p class="" style="color:#007fa3;"><?php echo $expired_text; ?></p>
								<?php if($productStatus->expiry_date!="" && $productStatus->reason==3){
									if($proObj->checkDomain($domain_name)==true && $val['master_products_ids']==1){
										$addClass = 'hide';
									}else{$addClass = '';}
										?>
									
								<p class="<?php echo $addClass;?>" style="color:#007fa3;"> <?php echo $proObj->getProductTrailDays($val['master_products_ids']);?> <?php if($proObj->getProductTrailDays($val['master_products_ids'])>1){ echo 'days';}else{ echo 'day';}?> (demo)</p>
								
								<?php }?> 
					</div>
				   <div class="bottomSectionDiv">
				   
				 
				   <a id="btnVoucher" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 10px;margin-right:40px;margin-top:10px;" onclick="showVoucherModal(<?php echo $val['master_products_ids'];?>);" href="javascript:void(0)">Enter Code</a>
					 <a id="btnShop" class="btn btn-primary" style=" width:100%; line-height: 40px;margin-bottom: 0px;margin-right:40px; <?php echo $showShopButton?>" href="<?php echo $shopPath; ?>" target="<?php echo $target;?>">Buy Course</a>

					<?php 					
							if($val['master_products_ids']==2){
									$_SESSION['product_id']=2;
									$go_to_link= $productPath2;
								
							}else{
						
								if($firstTime_login==""){
									if(($checkScore->imsx_messageIdentifier!="" && $checkScore->is_skip=='no') || $checkScore->is_skip=='yes'){
											$_SESSION['headTitle']="Start";
											$_SESSION['default']=0;
											$go_to_link= "score.php";
										}
									elseif(isset($checkScore->imsx_messageIdentifier) && $checkScore->imsx_messageIdentifier=='0'&& $checkScore->is_skip=='no'){
										$go_to_link= "welcome.php?pause=1";
									}else{
										$go_to_link= "welcome.php";
									}
									
								}else{
									$_SESSION['default'] = 0;
									$firstVisitArr=json_decode($firstTime_login);
									if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
									  $_SESSION['headTitle']="Resume";
									}else{
									  $_SESSION['headTitle']="Start";
									}
									$_SESSION['product_id']=1;
									$go_to_link= $productPath1;
								 
								} 
							}
			
							 ?>
							
						
						
					
				  </div>
					
				 </li>	
				 <?php }?>
		  
		  <?php $i++;
			 } }
			 if($expired_product==0){
			 ?>
			  <div class="bottomSection text-center">
			 No Expired Courses Available 
			  </div>
			 <?php } ?>
		</ul>
 
		</div>
 </div>
 -->
	</div>
   </div>
</div>

 <div class="chapterTxtBg col-md-4 col-sm-12 pull-left" style="padding-top:100px;">
         <h6><strong>Activate a product to get started</strong></h6>
          <p>You'll need to buy a product or use a code that you may have received from our partner.
           </p>
		  <p>Product Access Codes looks similar to this:
		VC-879MUJ</p>
		</div>
</section>
 <!-- Start voucher Modal here-->
<div id="voucherModal" class="voucherModal modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
	
		<form class="form-horizontal" method="post" id="radeemForm" name="radeemForm"  data-validate="parsley" autocomplete="off" enctype="multipart/form-data" style="width:100%">
			
			  <div class="alert alert-danger col-sm-12" style="display:none"  id="vErr">
					<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;display:none">x</button>
					<i class="fa fa-ban-circle"></i><span id="vErrMsg"><?php echo $msg;?></span> </div>

				  <div class="alert alert-success col-sm-12"  style="display:none"  id="vSucc">
					<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;display:none">x</button>
					<i class="fa fa-ban-circle"></i><span id="vSuccMsg"><?php echo $msg;?></span></div>	
				 
			<div class="modal-header">
                
                <h4 class="pull-left modal-title text-left st-absent-hd att-abs att-abs-0" >Enter Code 
				</h4><button type="button" class="pull-right close closeCustom" onclick="hidevoucherModal();" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body model-section-body model-section-body att-abs att-abs-some att-abs-all" style="height:180px;overflow-y:auto;">
                	
                <div class="col-sm-12 col-xs-12 text-left paddLeft5">
                 <div class="form-group col-sm-12">
			      <label class="control-label  pull-left access_code" for="radeemAccess">Code <span class="text-red required" >*</span> </label>
				 <input name="radeemAccess" id="radeemAccess"  class="form-control input-lg " data-required="true" autocomplete="off"/>
			  </div> 
                </div>
            </div>
            

            <div class="modal-footer" style="padding: 20px 20px;margin-top:0px;">
				<input  name="userId" id="userId" type="hidden"  value="<?php echo $uId; ?>" />
					   <input  name="cCenter" id="cCenter" type="hidden" value="<?php echo $center_id ;?>" />
					   <input  name="cBatch" id="cBatch" type="hidden" value="<?php echo $batch_id ;?>" />
					  
					   <?php 
					      if($studentData->last_name!=''){
						  $uName=$studentData->first_name." ".$studentData->last_name;
						 }else{
							$uName=$studentData->first_name;
						 }
						
						 $cemail=$studentData->email_id;//$studentData->loginid;
						 $user_from='b2c';
						 ?>
					   <input  name="uName" id="uName" type="hidden" value="<?php echo $uName ;?> "/>
					   <input  name="cemail" id="cemail" type="hidden" value="<?php echo $cemail ;?>" />
						<input  name="user_from" id="user_from" type="hidden" value="<?php echo $user_from ;?>" />
						
						<input  name="product_id" id="product_id" type="hidden" value="" />
			          <label class="required showErr" id="radeemError"></label>
					
				<label class="pull-left text-left required" id="licError"> </label>
				<!--<button type="button" class="btn btn-primary"  onclick="hidevoucherModal();"  style="margin-bottom: 0px;">Cancel</button>-->
				<button type="submit" class="btn btn-primary" id="add-voucher">Submit</button>
            </div>
			</form>
        </div>
    </div>
</div>
<!-- End lic Modal here-->
<?php include_once 'footer/footer.php';?>

<script>
function hidevoucherModal(){	
	$('#voucherModal').modal('hide');	
	$("#radeemAccess").val("");
	$("#voucherError").html("");
}
function showVoucherModal(pid){
	$('#product_id').val(pid);
	$('#voucherModal').modal({
		backdrop: 'static',
		keyboard: true, 
		show: true
	});	
}
$('#radeemForm').submit(function(){
	//$("#vErrMsg").text('');
	//$("#vSuccMsg").text('');
	let code = $('#radeemAccess').val();
	var pid = $('#product_id').val();
	if(code != ''){
		$.ajax({
			url : 'user/ajax/update_radeem.php?action=redeem_access',
			type : 'POST',
			dataType : 'json',
			async: false,
			data : {
					radeemAccess:code,
				    userId : $('#userId').val(),
				    cCenter : $('#cCenter').val(),
				    cBatch : $('#cBatch').val(),
				    uName : $('#uName').val(), 
				    cemail : $('#cemail').val(),
				    product_id : pid,
				    user_from : 'b2c'
				},
			success : function(result){
				// console.log(result); return;
				if(result.status != 'success'){
					$('#common-msg-text').css('color', 'red');
					//$("#vErr").show();
					//$("#vErrMsg").text(result.msg);
					//$('#radeemAccess').val('');
					alertPopup(result.msg);
				} else {
					$('#common-msg-text').css('color', 'green');
					alertPopup(result.msg);
					//$("#vSucc").show();
					//$("#vSuccMsg").text(result.msg);
					//window.href.reload();
					window.location = "shop_product.php";
					
				}
			}
		});
	}
	return false;

	
})

function activateTrial(pid,uid){

		$.ajax({
			url : 'user/ajax/activate_trail.php?action=activate_trail',
			type : 'POST',
			dataType : 'json',
			async: false,
			data : {
				    pid : pid,
				    uid : uid
				},
			success : function(data){
				console.log(data); 
				if(data.status==1){
					  window.location = "shop_product.php";
					//visitProduct(pid);
					$("#loaderDiv").hide();
				} else {
					
				}
			}
		});
	
}
	

function centerModal() {
		$(this).css('display', 'block');
		var $dialog = $(this).find(".modal-dialog");
		var offset = ($(window).height() - $dialog.height()) / 2;
		// Center modal vertically in window
		$dialog.css("margin-top", offset);
	}
	$('.modal').on('show.bs.modal', centerModal);

	$(window).on("resize", function () {
		$('.modal:visible').each(centerModal);
	});	
</script>

<script>
function visitProduct(id,pid,path){
if(pid!==''){
var data = {action: 'set_visitproduct',product_id:pid};
$.ajax({url: 'set_visit_product_ajax.php', type: 'post', dataType: 'json', data: data, async: false,
success : function(data){
console.log(data.status)
if(data.status==1){
window.location.href=path; 

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
<script>
var expired_product = '<?php echo $expired_product;?>';
var active_product = '<?php echo $active_product;?>';

if(active_product==0 && expired_product>0){
	$('#activeCourse').removeClass('active');
	$('#activeCourse').removeClass('show');
	$('#activeLi').find('a').removeClass('active');
	$('#activeLi').find('a > span').removeClass('active');
	$('#expireCourse').addClass('active');
	$('#expireCourse').addClass('show');
	$('#expireCourse .productList a').removeClass('active');
	$('#expireCourse .productList a span').removeClass('active');
	$('#expiryLi').find('a').addClass('active');
	$('#expiryLi').find('a > span').addClass('active');
}
</script>