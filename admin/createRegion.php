<?php 
include_once('../header/adminHeader.php');
$adminObj = new centerAdminController();

$exts = '[\''.implode('\',\'', $adminObj->getAllowedExtensions('image')).'\']';
$notAllowedExts = '[\''.implode('\',\'', $adminObj->getNotAllowedExtensions('image')).'\']';

$reportObj = new reportController();
$country_list_arr=$reportObj->getAllCountryList();
$productObj = new productController();
$productListArr=$productObj->getProductByClientId($client_id);
 //echo "<pre>";print_r($country_list_arr);exit;
/* Show edit batch */
if(isset($_GET['rid']) && !empty($_GET['rid'])){
  $rId = trim( base64_decode($_GET['rid']) );
  if(is_numeric($rId)==true){		
		$regionData = $centerObj->getRegionDataByID($rId);
	}else{
		header('Location: dashboard.php');
		die;
	}

  $regionName=$regionData[0]['region_name']; 
  $region_description=$regionData[0]['region_description'];  
  $region_logo=$regionData[0]['region_logo']; 
  $tandcLink=$regionData[0]['tandc_link']; 
  $policyLink=$regionData[0]['policy_link']; 
  $faqLink=$regionData[0]['faq_link']; 
 

   $regionId=$regionData[0]['id'];  
   $productListMapData= $centerObj->getRegionProductMapById($regionId);
   $productListMapArr=array();
   foreach($productListMapData as $key => $value1){
	 $productListMapArr[]=$value1['product_id'];
  } 
 // echo "<pre>";print_r($productListMapArr);//exit;
  $countryListMapArr= $centerObj->getRegionCountryMapById($regionId);
   $countryListMapList=array();
	foreach($countryListMapArr as $key => $value1){
	 $countryListMapList[]=$value1['country_name'];
  }
 
  $countryListMap=implode(",",$countryListMapList);
 //echo "<pre>";print_r($countryListMapList);exit;
}

if(!empty($_GET['rid'])){
	   $countClass ="";
	  $regClass ="";
	  $errDiv = "";
	  $pageType ="Edit";
	  $disabled='disabledInput';
  }else{
	  $pageType ="Add";
      $countClass ="displayNone";
	  $disabled="";
	 }


$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '3'){
		$msg = $language[$_SESSION['language']]['centre']."  is already exist. Please try another.";
	}
	
}

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){

		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
}
	 
	 
?>
<style>

  .panel-heading a:after {    width: 2%;
    font-family:'FontAwesome';
    content:"\f106";
    float: right;
    color: grey;
	font-size:14px;font-weight:700;position:absolute;    right: 0px;
}
 
.panel-heading a.collapsed:after {
    content:"\f107";
}

</style>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="regionList.php"><i class="fa fa-arrow-left"></i>  <?php echo $region; ?> </a></li>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
	<?php if($err == '3'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
     <section class="marginBottom40">
      <form action="ajax/regionFormSubmit.php" id="createRegionForm" class="createRegion" method="post"  data-validate="parsley" enctype="multipart/form-data"  autocomplete="off" onsubmit="return validationRegion();">
	  <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		<h3 class="panel-header"> <?php echo $addRegion; ?></h3>
		 <input id="regionIdVal" type="hidden" name="regionIdVal" value="<?php echo $rId; ?>"/>
		 <input id="cRegionName" type="hidden" name="cRegionName" value="<?php echo $regionName; ?>"/>

		<div class="col-sm-4 paddLeft0"> 
		   <label class="control-label"> <?php echo $region; ?> <span class="required">*</span></label> 
			<div class="clear"></div>
			<input type="text" class="form-control greenBorder" id="region" name="region" data-required="true" maxlength = "250"  value="<?php echo $regionName;?>" />
            <label class="required" id="errorRegion"> </label>
		    
		  </div>
		 
		  <div class="col-sm-4 paddLeft0"> 
		   <label class="control-label"> <?php echo $region.' '.$logo_name; ?> <span class="required">*</span></label> 
			<div class="clear"></div>
			<input type="file" class="form-control greenBorder" id="region_logo" name="region_logo" <?php if($region_logo == ''){ ?> data-required="true" <?php } ?>  />
            <label class="required" id="errorLogo"> </label>
		    
		  </div>
		  <?php if($region_logo != ''){ ?>
		  <div class="col-sm-4 paddLeft0"> 
		   <img src="<?php echo '../images/region/'.$region_logo?>" width="50%">
		    
		  </div> 
		  <?php } ?>
		  <div class="clear"></div>
		  <div class="col-sm-12 paddLeft0"> 
		   <label class="control-label"> <?php echo $region.' '.$description; ?> <span class="required">*</span></label> 
			
			<textarea class="form-control greenBorder" rows="3" id="region_description" name="region_description" data-required="true" > <?php echo $region_description;?> </textarea>
            <label class="required" id="errorDisc"> </label>
		    
		  </div>
		  <div class="clear"></div>
		<div class="col-sm-12 paddLeft0"> 
		   <label class="control-label"> <?php echo $tandc; ?> <span class="required"></span></label> 
			 <div class="clear"></div>
			<input class="form-control greenBorder" id="termandcondition" name="termandcondition" value="<?php echo $tandcLink;?>"/> 
             <label class="required" id="errorTandC"> </label>
		   
		  </div>
		  <div class="clear"></div>
		  <div class="col-sm-12 paddLeft0"> 
		   <label class="control-label"> <?php echo $policy; ?> <span class="required"></span></label> 
			 <div class="clear"></div>
			<input class="form-control greenBorder" id="policy" name="policy" value="<?php echo $policyLink;?>"/> 
             <label class="required" id="errorPolicy"> </label>
		   
		  </div>
		  <div class="clear"></div>
		  
		  <div class="col-sm-12 paddLeft0"> 
		   <label class="control-label"> <?php echo $faq; ?> <span class="required"></span></label> 
			
			<input class="form-control greenBorder" id="faq" name="faq" value="<?php echo $faqLink;?>"/> 
             <label class="required" id="errorFaq"> </label>
		   
		  </div>
		  <div class="clear"></div>
		  <div class="col-sm-6 paddLeft0"> 
		   <label class="control-label"> Product List <span class="required">*</span></label> 
			<div class="clear"></div>
			 <select class="form-control input-lg parsley-validated fld_class" name="product_id[]" id="product_id" data-required="true" multiple>
				 <?php 
					 foreach ($productListArr as $value) {	
					   $productName= $value['product_name'];
					   $productId= $value['id']; 
					 $selected=(is_array($productListMapArr) && in_array($value['id'], $productListMapArr))  ? "selected" : "";
					 ?>
					<option  value="<?php echo $productId; ?>" master="<?php echo $value['master_products_ids']; ?>" <?php echo $selected; ?> ><?php echo $productName;?></option>	
					 <?php }?>
				</select>
				<label class="required" id="errorProduct"> </label>
		  
		  </div><div class="clear"></div>
         <input type="hidden" name="country[]"   value="India"  />
		 <input type="hidden" name="client_id"   value="<?php echo $client_id;?>"  />
		  
		    <div class="clear"></div>
		    </div>
			<div class="clear"></div>
		   <div class="">
		   
		   </div>
		
	    </div>
	   </div>
	   <div class="clear"></div>
		   <div class="text-right"> 
			<a href='regionList.php' class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			
			<button type="submit" class="btn btn-s-md btn-primary" name="createBatch" id="createRegion"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
	    </div>
     </form>
   </section> 
  </div>
 </div>
</section>
<?php include_once('../footer/adminFooter.php');?>
<script>

$(document).ready(function(){
})
// Validation for select Country
// function validationRegion(){
//  $("#errorCountry").html("");
// 	if ($('input:checkbox').filter(':checked').length < 1){
//         $("#errorCountry").html("Select at least one country ");
// 	   return false;
// 	 }
   
// }


$('#region_logo').change(function(){
	var validExtensions = <?php echo $exts?>; //array of valid extensions
	var notAllExtensions = <?php echo $notAllowedExts?>; //array of valid extensions
	var fileName = this.files[0].name;
	var fileNameArray = fileName.split('.');
	for(var i=0;i<notAllExtensions.length;i++){
		if($.inArray(notAllExtensions[i], fileNameArray) == 1){
			this.type = '';
			this.type = 'file';
			// $('#'+viewId).attr('src',"");  
			alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
			//$('#'+viewId).attr('src',defaultProfilePath); 
			return false;
		}
	}

	var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
	if ($.inArray(fileNameExt, validExtensions) == -1) {
		this.type = '';
		this.type = 'file';
		// $('#'+viewId).attr('src',"");  
		alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
		//$('#'+viewId).attr('src',defaultProfilePath); 
		return false;

	}
})

</script>
