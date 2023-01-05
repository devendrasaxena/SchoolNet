<?php include_once('../header/adminHeader.php');
$productObj = new productController();
$msg='';	
$err='';	
$succ='';
$productDataRegion= $centerObj->getRegionProductMapById($region_id);
//echo "<pre>"; print_r($productDataRegion); die;
$productDataArr=array();
	foreach($productDataRegion as $key => $value1){
	 $productDataArr[]=$value1['product_id'];
  } 
$product_id = implode(",", $productDataArr);

$productListArr=$productObj->getProdcutDetailByIdArr($product_id);

    $education=$commonObj->getEducation();
	if(isset($_REQUEST['cid'])){
		$cid = trim( base64_decode($_REQUEST['cid']) ); 
		
		$productListMapData= $centerObj->getCenterProductMapById($region_id,$cid);
	    $productListMapArr=array();
	   foreach($productListMapData as $key => $value1){
		 $productListMapArr[]=$value1['product_id'];
	  } 
		if(is_numeric($cid)==true){
			$userCenterDetail =$centerObj->getUserCenterDetail($cid);
		}else{
			header('Location: dashboard.php');
			die;
	    }
		$userCenter = userdetails($userCenterDetail->user_id);
		$centerloginid=$userCenter->loginid;

	    $centreValue=$centerObj->getCenterOnline($cid);
	     //echo "<pre>";print_r($centreValue);exit;
		$countryData= $centreValue['country'];
		if($countryData=='India'){
		 $stateData= $centreValue['state'];
		 $cityData= $centreValue['city'];
		 $hideIndia='';
		 $hideOther='style="display:none;"';
		}else{
		 $stateOther= $centreValue['state'];
		 $cityOther= $centreValue['city'];	
		 $hideIndia='style="display:none;"';
		 $hideOther='';
		}
		//echo "<pre>";print_r($cityData);exit;
	    $pageType =$language[$_SESSION['language']]['edit'];;
	
		if($centreValue['org_short_code']==""){
			$shortcode= $centerObj->shortcode_strings();
			$chk_short_code = $centerObj->chk_exist_shortcode($shortcode); 
			while($chk_short_code==false){
				$shortcode= $centerObj->shortcode_strings();
				$chk_short_code = $centerObj->chk_exist_shortcode($shortcode);
			}
		}
	
	
	} else{ 

		
		$pageType =$language[$_SESSION['language']]['add'];;
	    //$resCountry=$commonObj->getCountry();	
		$resCountry=$reportObj->getCountryList();
		$countryData= $resCountry[0]['country_name'];
		if($countryData=='India'){
			$countryData=$resCountry[0]['country_name'];
		}else{
			$countryData='';
		}
		$stateData='';
        $cityData='';
		if(isset($_SESSION['center_details'])){
			$centreValue = $_SESSION['center_details'];
			$centreValue['phone'] = $centreValue['center_phone'];
			$centreValue['mobile'] = $centreValue['user_mobile'];
			$centreValue['description'] = $centreValue['user_full_name'];
			$centreValue['pincode'] = $centreValue['postal_code'];
			$centreValue['address1'] = $centreValue['address'];
			$countryData= $centreValue['country'];
			if($countryData=='India'){
				 $stateData= $centreValue['state'];
				 $cityData= $centreValue['city'];
				 $hideIndia='';
				 $hideOther='style="display:none;"';
			} else {
				 $stateOther= $centreValue['state'];
				 $cityOther= $centreValue['city'];	
				 $hideIndia='style="display:none;"';
				 $hideOther='';
			}
		 	unset($_SESSION['center_details']);
		}
		
		if(!isset($centreValue['org_short_code']) && $centreValue['org_short_code']==''){	
			$shortcode= $centerObj->shortcode_strings();
			$chk_short_code = $centerObj->chk_exist_shortcode($shortcode); 
			while($chk_short_code==false){
				$shortcode= $centerObj->shortcode_strings();
				$chk_short_code = $centerObj->chk_exist_shortcode($shortcode);
			}
		}
	
	
	}
  
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = $language[$_SESSION['language']]['state']." not saved. Please try again.";
	}
}

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
	
} 

 if($centreValue['org_short_code']!='' && $cid!=''){
	   $shortHide="disabled";
	   $shortVal=$centreValue['org_short_code'];
	 }
elseif($centreValue['org_short_code']!='' && $cid==''){
	$shortHide="";
	$shortVal=$centreValue['org_short_code'];
}else{
   $shortHide="";
   $shortVal=$shortcode;
}  

$regions = $centerObj->getRegionDetails();

?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="centerList.php"  title="<?php echo $language[$_SESSION['language']]['manage_states']; ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['manage_states']; ?></a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">

	<div class="col-sm-12">

	   <?php if(isset($_GET["succ"]) && $_SESSION['succ']==1){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo (!empty($_SESSION['succ']))?'Update':'Add';?>  is successful.</div>
      <?php } ?>
    </div>
  
    <form role="form" method = "POST" action = "ajax/register_waiting.php" id="centerRegForm" class="form-horizontal form-centerReg centerRegForm" data-validate="parsley" onSubmit="return confSubmit();" autocomplete="nope">
	
      <div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header"> <?php echo $pageType." ". $language[$_SESSION['language']]['state']; ?></h3>
			 <div class="form-group">
                <div class="col-sm-6 hide" style="display:none">
                  <label for="licenseKey" class="control-label"><?php echo $language[$_SESSION['language']]['license_key']; ?> <span class="required">*</span></label>
		 <input type="text" class="form-control greenBorder parsley-validated" id="license_key" name="license_key" data-required="true" data-minlength="[10]" data-maxlength="[10]" maxlength = "10" value="<?php echo 'DEMOWILEY0';//$centreValue['license_key']; ?>" <?php echo (!empty($cid))?'disabled':''?>/>
        <label class="required" id="errorLicenseKey"></label>
		         <div  class="col-sm-12  padd0 <?php if($cid==''){echo "hide";}?>">
				 <input type="hidden" name="trainer_limit" value="<?php echo $centreValue['trainer_limit']?>"/>
				  <input type="hidden" name="student_limit"value="<?php echo $centreValue['student_limit']?>"/>
				   <input type="hidden" name="expiry_days" value="<?php echo $centreValue['expiry_days']?>"/>
				   <input type="hidden" name="expiry_date" value="<?php echo $centreValue['expiry_date']?>"/>
				   <input type="hidden" name="used_email" value="<?php echo $centreValue['email_id']; ?>"/>
				<div >
				<!--<a href="javascript:void(0)"  onclick="return showHideChangeLicense('ajax/changeLicense.php','chLicDiv','centerRegForm', 'new_license_key');"  class="btn btn-s-md btn-primary chlic pointer marginTop20"><i class="chlicIcon fa fa-plus"></i> Change License</a>-->
				 <input type='hidden' name='used_license' id='used_license' value='<?php echo $centreValue['license_key']; ?>' />
				</div>
				<div class="clear"></div>
				</br>
				<div id="chLicDiv">
				</div>
			  
			   </div>
           </div>
		  
				<div class="col-sm-6 hide">
                 <label for="licenseKey" class="control-label"><?php echo $language[$_SESSION['language']]['state_short_code']; ?> <span class="required"> *</span></label>
				 <div class="">
				 <input type="text" id="shortcode" name="shortcode" class="form-control" value="<?php echo $shortVal;?>" data-minlength="[3]" data-maxlength="[8]" maxlength = "8" data-regexp="^[A-Za-z0-9]+$" data-regexp-message="This value should be alphanumeric only no spaces, no special characters," data-required="true" <?php echo $shortHide;?>/>
		          
                </div>
		   </div> 
		   
                <div class="col-sm-6 hide">
                 <label for="licenseKey" class="control-label">Learning Mode <span class="required">*</span></label>
				 <div class="">
				 <input type="radio" id="lmode1" name="lmode" value="master"  class="testCheckbox"   checked=<?php if($centreValue['learning_mode'] == "master") { echo "checked"; }?> /> Master Mode
		           &nbsp;&nbsp;<input type="radio" id="lmode2" name="lmode" value="guided"  class="testCheckbox" <?php if($centreValue['learning_mode'] =="guided") { echo "checked"; }?>/> Guided  Mode
                     <label class="required" id="errorLmode">
                </div>
              </div>
			   <div class="col-sm-6">
                  <label for="centerName" class="control-label"><?php echo $language[$_SESSION['language']]['state_name']; ?> <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder parsley-validated" id="name" name="name" data-required="true" maxlength = "50" value="<?php echo $centreValue['name']; ?>"/>
                  <label class="required" id="errorCenterName"></label>
                </div>


                 <div class="col-sm-6">
                  <label for="country_dropdown" class="control-label"><?php echo $language[$_SESSION['language']]['country']; ?> <span class="required">*</span></label>
				  <?php if(!empty($centreValue)){?>
				  <select id="country_dropdown" name = "country_dropdown"  class="form-control greenBorder" data-required="true" style="padding-right:0px;text-transform: capitalize;"  onchange="selectCountry(this.id,'state_dropdown','city_dropdown');">
                      <option <?php //echo $selected; ?> value="<?php echo $country; ?>"><?php echo $country;?></option>	  
					  </select>
                    <?php  }else{?>
					 <select id="country_dropdown" name = "country_dropdown"  class="form-control greenBorder"  style="padding-right:0px;text-transform: capitalize;"  onchange="selectCountry(this.id,'state_dropdown','city_dropdown');">	
						 
						 </select>
					<?php 
					}?>
                 <span id="countryError" class="error required"></span>
                </div>
                
               
                <div class="col-sm-6 hide"> <div class="col-sm-12 clear"></div>
                 <?php if($_SESSION['role_id']==7){?>
					 <label for="region_dropdown" class="control-label"><?php echo $language[$_SESSION['language']]['centres']; ?> <span class="required"></span></label>
				 <?php foreach($regions as $region){	
				   if($region_id == $region['id']){?>				
					 <input type="hidden" class="form-control greenBorder" id="region_dropdown" name = "region_dropdown"  autocomplete="nope" value="<?php echo $region['id'];?>"/>
					 <div>
					<strong><?php echo $region['region_name'];?> </div></strong>
				   <?php } } ?>
					<?php }else{?>
					<label for="region_dropdown" class="control-label"><?php echo $language[$_SESSION['language']]['centres']; ?> <span class="required">*</span></label>
				 
					<select id="region_dropdown" name = "region_dropdown"  class="form-control greenBorder"  style="padding-right:0px;text-transform: capitalize;" data-required="true"> 
					  <option value=""><?php echo $language[$_SESSION['language']]['select_center']; ?></option>
						 <?php foreach($regions as $region){
						 	$selectRegion = $centreValue['region'] == $region['id'] ? 'selected="1"':'';
						 	?>
						 <option <?php echo $selectRegion?> value="<?php echo $region['id']?>"><?php echo $region['region_name']?></option>
						 <?php } ?>
						 </select>
					<?php }?>
					
					
                 <span id="regionError" class="error required"></span>
                </div>
               
            <div class="col-sm-12 clear"></div>
			 <!--
                <div class="col-sm-6">
                  <label for="state_dropdown" class="control-label"><?php echo $language[$_SESSION['language']]['states']; ?> <span class="required"></span></label>  
				  <input type="text" class="form-control greenBorder" id="other_state" name = "other_state" value="<?php echo $centreValue['state']; ?>"  <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                  <select id="state_dropdown" name = "state_dropdown" onclick="selectState(this.options[this.selectedIndex].value)" onChange="selectState(this.options[this.selectedIndex].value)" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;"  <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                    <option value="">Select state</option>
                  </select>
                  <span id="state_loader"></span> 
				  <span id="stateError" class="error required"></span>
					</div>
				 
                 <div class="col-sm-6">
                  <label for="city_dropdown" class="control-label"><?php echo $language[$_SESSION['language']]['city']; ?> <span class="required"></span></label>
				  <input type="text" class="form-control greenBorder" id="other_city" name = "other_city" value="<?php echo $centreValue['city']; ?>"  <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                  <select id="city_dropdown" name = "city_dropdown" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;" <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                    <option value="">Select city</option>
                  </select>
                  <span id="city_loader"></span> 
				  <span id="cityError" class="error required"></span>
				  </div>
				 	 <div class="col-sm-12 clear">&nbsp;</div> 
				 
		
                <div class="col-sm-6">
                  <label for="centerPincode" class="control-label"><?php echo $language[$_SESSION['language']]['pin_code']; ?> <span class="required"></span></label>
                  <input type="text" class="form-control greenBorder" id="centerPincode" name = "centerPincode"  maxlength="7" value="<?php echo $centreValue['pincode']; ?>" autocomplete="nope"/>
                </div> 
				 <div class="col-sm-6">
                  <label for="centerAddress" class="control-label"> <?php echo $language[$_SESSION['language']]['address']; ?> <span class="required"></span></label>
                  <textarea class="form-control greenBorder" id="centerAddress" name = "centerAddress" style="height:40px; resize: none;"  spellcheck="false" autocomplete="nope"><?php echo $centreValue['address1']; ?></textarea>
                </div>
               
              <div class="col-sm-12 clear"style="border-bottom: 1px solid #eee;">&nbsp;</div> -->
			  <div class="col-sm-6">
                  <label for="emailId" class=" control-label"><?php echo $center_admin; ?> <span class="required">*</span></label>
                  <input type="email" class="form-control greenBorder" placeholder = "<?php echo $language[$_SESSION['language']]['email_id']; ?>" id="emailId" name="emailId" data-type="email" data-required="true" maxlength = "50" style="text-transform:none" value="<?php echo $centreValue['email_id']; ?>" <?php echo (!empty($cid))?'disabled':''?> autocomplete="nope"/>
				  <div class="col-sm-12" style="padding-left:0px">
                    <p><small> <?php echo $language[$_SESSION['language']]['email_id_will_be_used_as_username_for_login.']; ?><!--(Admin login credentials will be sent to this Email ID.)--></small></p>
                  </div>
			  <div class="required error" id="emailErr"></div>
                  
                </div>
                <div class="col-sm-6">
                  <label for="centerAdminName" class="control-label"><?php echo $language[$_SESSION['language']]['state_admin']; ?> <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder" id="centerAdminName" placeholder = "<?php echo $language[$_SESSION['language']]['name']; ?>" name = "centerAdminName" data-required="true" maxlength = "50" value="<?php echo $centreValue['description']; ?>" autocomplete="nope"/>
                  <label class="required" id="errorName"></label>
                </div>
               
                
              <div class="col-sm-12 clear"></div>
                <div class="col-sm-6">
                  <label for="mobileNumber" class="control-label"><?php echo $language[$_SESSION['language']]['mobile_number']; ?> <span class="required"></span></label>
                  <input type="text" class="form-control greenBorder" placeholder = "<?php echo $language[$_SESSION['language']]['mobile_number']; ?> " id="mobileNumber" name="mobileNumber" data-type="phone" data-minlength="[10]"  maxlength="10" data-regexp="^[1-9]\d*$" data-regexp-message="Mobile number should not be 0" value="<?php echo $centreValue['mobile']; ?>" autocomplete="nope"/>
                  <div class="col-sm-12" style="padding-left:0px">
                    <p style="display:none"><!--<small>(Admin login credentials will be sent to this Mobile Number.)</small>--></p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label for="centerContactNumber" class="control-label"><?php echo $language[$_SESSION['language']]['contact_number']; ?> <span class="required"></span></label>
                  <input type="text" placeholder = "<?php echo $language[$_SESSION['language']]['contact_number']; ?>" class="form-control greenBorder" id="centerContactNumber" name="centerContactNumber" data-type="phone" maxlength = "12" value="<?php echo $centreValue['phone']; ?>" autocomplete="nope"/>
                  <div class="col-sm-12" style="padding-left:0px">
                    <p ><small><?php echo $language[$_SESSION['language']]['enter_landline_number_with_std_code.']; ?></small></p>
                  </div>
                </div>	

				 <div class="col-sm-12 clear"></div>
				<div class="col-sm-6 <?php if($cid!=''){echo "hide";}?>">
				  <label class="control-label"><?php echo $language[$_SESSION['language']]['password']; ?> <span class="required">*</span></label>
				  <input type = "password" name="password" id="password" placeholder="<?php echo $language[$_SESSION['language']]['password']; ?>" class="form-control input-lg " value=""   maxlength="15"   autocomplete="pwd" <?php if($cid!=''){echo "readonly";}else{?> data-required="true"  data-regexp="<?php echo $passRegexp;?>" data-regexp-message="<?php echo $passRegexpMsg;?>" <?php }?>/>
			 <label class="" style="font-size: 12px;margin-top:5px" id="login_pass"><?php echo $language[$_SESSION['language']]['the_password_must_have_8_or_more_characters,_at_least_one_uppercase_letter,_and_one_number']; ?></label>
				</div>
				<div class="col-sm-6 <?php if($cid!=''){echo "hide";}?>">
				 <label class="control-label"><?php echo $language[$_SESSION['language']]['confirm_password']; ?> <span class="required">*</span></label>
				  <input type = "password" name="cpassword" id="cpassword" placeholder="<?php echo $language[$_SESSION['language']]['confirm_password']; ?>" class= "form-control input-lg " maxlength = "15" value=""  data-equalto="#password" autocomplete="nope" <?php if($cid!=''){echo "readonly";}else{?> data-required="true"<?php }?>/>
				</div> 
				
				<div  class="col-sm-12  <?php if($cid==''){echo "hide";}?>">
				<div >
				<a href="javascript:void(0)"  onclick="return showHideChangePassword('ajax/changePassword.php','shcpDiv','centerRegForm', 'oldPassword', 'newPassword', 'cnfPassword');"  class="btn btn-s-md btn-primary chpassword pointer marginTop20"><i class="passwordIcon fa fa-plus"></i> <?php echo $language[$_SESSION['language']]['change_password']; ?></a>
				 <input type='hidden' name='user_session_id' id='user_session_id' value='<?php echo base64_encode($centerloginid);?>' />
				</div>
				<div class="clear"></div>
				</br>
				<div id="shcpDiv">
				</div>
			  
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
        
				 </div>
				<div class="col-sm-12 clear"></div>
				<div class="text-center">
				 <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
				 <input type="hidden" name="cid" value="<?php echo (!empty($cid))? $cid:''?>" />
				 <input type="hidden" name="aid" value="<?php echo (!empty($cid))? $userCenterDetail->address_id:''; ?>" />
				 <input type="hidden" name="uid" value="<?php echo (!empty($cid))? $userCenterDetail->user_id:''; ?>" />
				 <input type="hidden" name="ugid" value="<?php echo (!empty($cid))? $userCenterDetail->user_group_id:''; ?>" />
				 	<input type="hidden" id="cpFlag" value="0" />
					<input type="hidden" id="licFlag" value="0" />
                </div>
              </div>
            </div>
         <div class="text-right"> 
			<a href='centerList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			<button type="submit" class="btn btn-s-md btn-primary" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" onclick="return showLoaderOrNot('centerRegForm');" ondblclick="return showLoaderOrNot('centerRegForm');"><?php echo $language[$_SESSION['language']]['submit']; ?> </button>
	    </div>
     </div>
   </form>
</section>
 </div>
 </div>
</section>	  
<?php include_once('../footer/adminFooter.php'); ?>

<script type="text/javascript">
var countryData='<?php echo $countryData; ?>';
var stateData='<?php echo $stateData; ?>';
var cityData='<?php echo $cityData; ?>';
var stateOther='<?php echo $stateOther; ?>';
var cityOther='<?php echo $cityOther; ?>';
if(countryData=='India'){
		$("#other_state").hide();
        $("#other_city").hide();
		$("#state_dropdown").show();
		$("#city_dropdown").show();
	}else{
		$("#other_state").show();
        $("#other_city").show();
		$("#city_loader").hide();
		$("#state_loader").hide();
		$("#state_dropdown").hide();
		$("#city_dropdown").hide();
		
	}
 selectCity(countryData);
 selectState(stateData);


function selectCity(country_id){
	
	if(country_id!="-1"){
		loadData('state',country_id);
		$("#city_dropdown").html("<option value=''>Select city</option>");	
	}else{
		$("#state_dropdown").html("<option value=''>Select state</option>");
		$("#city_dropdown").html("<option value=''>Select city</option>");		
	}
}

function selectState(state_id){
	if(state_id!="-1"){
		loadData('city',state_id);
	}else{
		$("#city_dropdown").html("<option value=''>Select city</option>");		
	}
}

function loadData(loadType,loadId){
	//alert(loadType))
	var dataString = 'loadType='+ loadType +'&loadId='+ loadId + '&stateData='+ stateData +'&cityData='+ cityData;
	$("#"+loadType+"_loader").show();
    //$("#"+loadType+"_loader").fadeIn(400).html('Please wait... <img src="image/loading.gif" />');
	$.ajax({
		type: "POST",
		url: "ajax/loadData.php",
		data: dataString,
		cache: false,
		success: function(result){
			$("#"+loadType+"_loader").hide();
			$("#"+loadType+"_dropdown").html("<option value=''><?php echo $language[$_SESSION['language']]['select']; ?> "+loadType+"</option>");
			$("#"+loadType+"_dropdown").append(result);  
		}
	});
}


function confSubmit(){
	
	var country = document.getElementById("country_dropdown");
	var countryVal = country.options[country.selectedIndex].value;
	if(countryVal == -1){
	    alert("Please select the country.");
	 //document.getElementById("countryError").html("Please select the country.");
	  return false;
	}
	//alert(countryVal);

	if(countryVal=='India'){
		/* var state = document.getElementById("state_dropdown");
		var stateVal = state.options[state.selectedIndex].value;
		if(stateVal == -1){
			//alert("Please select the state.");
		 document.getElementById("stateError").innerHTML="Please select the state.";
		return false;
		}
		
		var city = document.getElementById("city_dropdown");
		var cityVal = city.options[city.selectedIndex].value;
		if(cityVal == -1){
			//alert("Please select the city.");
		 document.getElementById("cityError").innerHTML="Please select the city.";
		return false;
		} */
	
	}else{
		
		/* var stateVal = document.getElementById("other_state").value;
		var cityVal = document.getElementById("other_city").value;
		if(stateVal == ''){
			//alert("Please enter the state.");
		 document.getElementById("stateError").innerHTML="Please enter the state.";
		  return false;
		}
		if(cityVal == ''){
			//alert("Please enter the city.");
		 document.getElementById("cityError").innerHTML="Please enter the city.";//This value is required.
		 return false;
		} */
		
	}

	
}


function checkNumber(curVal, curId,targetId){
	//$("#errorCenterName").html('');
	if (isNaN( curVal )) {
    // It isn't a number
		//alert('not');
		$("#"+targetId).html('');
	} else {
		//alert('yes');
		// It is a number
		$("#"+curId).val("");
		$("#"+targetId).html('This field accepts alpha-numeric value');
	}
}

function loadCountry(countryData, reg_id = ''){

	console.log(countryData);
	var cValue=$("#country_dropdown :selected").val();
	var dataString = 'country='+ countryData+'&reg_id='+reg_id;
	//$("#"+loadType+"_loader").show();
    $.ajax({
		type: "POST",
		url: "ajax/allCountry.php",
		data: dataString,
		cache: false,
		success: function(result){
			//console.log(result)
			$("#country_dropdown").html("<option  value=''>Select</option>"+result);
			//$("#country_dropdown").html("<option value=''>"+result+"</option>");
			
		}
	});
}

<?php if ($centreValue['region'] != ''){ ?>
loadCountry(countryData, '<?php echo $centreValue['region']?>');
<?php } ?>
<?php if($_SESSION['role_id']==7){?>
loadCountry(countryData, '<?php echo $region_id;?>');
<?php } ?>
function selectCountry(id,state,city){
	
	var cValue=$("#"+id+" :selected").val();
	var region_id=$("#region_dropdown").val();
	 loadCountry(cValue,region_id);
	if(cValue=='India'){
		$("#"+state).show();
		$("#"+city).show();
		$("#other_state").hide();
		$("#other_city").hide();
		selectCity(cValue);
        selectState(stateData);
	
	}else{
		$("#"+state).hide();
		$("#"+city).hide();
		$("#other_state").show();
		$("#other_city").show();
	  if(cValue==countryData){
		 $("#other_state").val(stateOther);
		  $("#other_city").val(cityOther);			
		}else{
		  $("#other_state").val('');
		  $("#other_city").val('');
        }
	}
}

$(document).ready(function(){
	
   $(".form-control").blur(function() {
       // console.log(dInput);
		$("#stateError").html('');
		$("#countryError").html('');
		$("#cityError").html('');
    });
	$(".form-control").keypress(function() {
		$("#stateError").html('');
		$("#countryError").html('');
		$("#cityError").html('');
    });
	
});


function addParsely(formId,old_password,pwd, cpwd){
	//$('#'+formId).parsley('addItem', '#'+old_password);
	$('#'+formId).parsley('addItem', '#'+pwd);
	$('#'+formId).parsley('addItem', '#'+cpwd);
}

function removeParsely(formId,old_password,pwd, cpwd){
	$('#'+formId).parsley('destroy');
	$('#'+formId).parsley();
	//$('#'+formId).parsley('removeItem',  '#'+old_password);
	$('#'+formId).parsley('removeItem', '#'+pwd);
	$('#'+formId).parsley('removeItem', '#'+cpwd);
	
}

function showHideChangePassword(path,targetDiv,formId,old_password,pwd, cpwd){	
	showLoader();
	$('#'+formId).parsley();
	var cpFlag = $("#cpFlag").val();
	if(cpFlag == 0){
		$.post(path, {shpass: cpFlag}, function(data){
			$("#"+targetDiv).html(data);
			$("#cpFlag").attr('value','1');
			addParsely(formId,old_password,pwd, cpwd);
		});
		hideLoader();
	}else{	
		removeParsely(formId,old_password,pwd, cpwd);			
		$("#"+targetDiv).html('');
		$("#cpFlag").attr('value','0');		
		hideLoader();
	}
}


$(document).ready(function(){
	$(".chpassword").click(function(){
	   $(".passwordIcon").toggleClass('fa-plus fa-minus')
	});
	$(".chlic").click(function(){
	   $(".chlicIcon").toggleClass('fa-plus fa-minus')
	});
 })
 
 function addParsely1(formId,newLic){
	$('#'+formId).parsley('addItem', '#'+newLic);
}

function removeParsely1(formId,newLic){
	$('#'+formId).parsley('destroy');
	$('#'+formId).parsley();
	$('#'+formId).parsley('removeItem', '#'+newLic);

	
}

function showHideChangeLicense(path,targetDiv,formId,newLic){	
	showLoader();
	$('#'+formId).parsley();
	var licFlag = $("#licFlag").val();
	if(licFlag == 0){
		$.post(path, {shpass: licFlag}, function(data){
			$("#"+targetDiv).html(data);
			$("#licFlag").attr('value','1');
			addParsely1(formId,newLic);
		});
		hideLoader();
	}else{	
		removeParsely1(formId,newLic);			
		$("#"+targetDiv).html('');
		$("#licFlag").attr('value','0');		
		hideLoader();
	}
	

	
}
function checkLicLimit(id){
	   $("#newLicenseError").html("");
		 let new_license = $("#"+id).val();
		let old_license = "<?php echo $centreValue['license_key']; ?>";
		//console.log(old_license)
		if(new_license != ''){
			$.ajax({
				type : 'POST',
				url : "ajax/getLicenseDetail.php",
				data : {newlicense:new_license,oldlicense:old_license},
				cache: false,
		         success: function(result){
				 console.log(result)
				var obj = JSON.parse(result.trim());
					if(obj.status == '1'){
						
						return true;
					}else{
						 $("#"+id).val('');
						$("#newLicenseError").html("New License limit is less than from old license");
						return false;
					} 
				}
				
			})
		}
	}

$('#region_dropdown').change(function(){
	let region_id = $(this).val();
	if(region_id != ''){
		loadCountry(countryData, region_id);
	} else {
		$("#country_dropdown").html("<option  value=''>Select</option>");
	}
	console.log($(this).val());
})

</script>



 
 
