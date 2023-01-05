<?php include_once('../header/adminHeader.php');

  //$centreData=$adminObj->getCenterDetails(); 
   //echo "<pre>";print_r($centreData);exit;	
  //  $centerId=$centreData[0]['center_id'];
   // $client_id=$centreData[0]['client_id'];	
	//$exitCenter = $_SESSION[$centerId];
	if(isset($_GET['uid'])){
  
    // $teacherData = $objAdmin->getUserDataByID( $uId, 1);
   }
     $education=$commonObj->getEducation();
	if(isset($_REQUEST['cid'])){
		 //$cid =$_REQUEST['cid'];
        //echo $cid;exit;
		$cid = trim( base64_decode($_REQUEST['cid']) ); 
		
		$userDetail =$centerObj->getUserCenterDetail($cid);
		//echo "<pre>";print_r($userDetail);exit;
	    $centreValue=$centerObj->getCenterOnline($cid);
	  // echo "<pre>";print_r($centreValue);exit;
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
	
	} else{ 
	 
	    $resCountry=$commonObj->getCountry();	
		$countryData= $resCountry[0]['country_name'];
		$stateData='';
        $cityData='';
	}
	
	
               
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="centerList.php"><i class="fa fa-arrow-left"></i> Manage Accounts </a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">

	<div class="col-sm-12">
		 <?php if(isset($_GET["error"])&& $_SESSION['error']==0){?>
      <div class="alert alert-danger col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i>Invalid License Key or check internet connection.</div>
      <?php } ?>
	   <?php if(isset($_GET["succ"]) && $_SESSION['succ']==1){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo (!empty($_SESSION['succ']))?'Update':'Create';?>  is successful.</div>
      <?php } ?>
    </div>
  
    <form role="form" method = "POST" action = "ajax/register_waiting.php" id="centerRegForm" class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" autocomplete="off">
      <div class="row">
      
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header">Add <?php echo $centers; ?></h3>
			 
			<div class="form-group">
				<div class="col-sm-12 clear"></div>
                <div class="col-sm-6">
                  <label for="centerName" class="control-label"><?php echo $center; ?> Name <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder parsley-validated" id="name" name="name" data-required="true" maxlength = "50" data-regexp="^[a-zA-Z][a-zA-Z0-9 ]*$" data-regexp-message="Value should be alpha/alphanumeric" value="<?php echo $centreValue['name']; ?>"/>
                  <label class="required" id="errorCenterName"></label>
                </div>
                 <div class="col-sm-6">
                  <label for="country_dropdown" class="control-label">Country <span class="required">*</span></label>
				  <?php if(!empty($centreValue)){?>
				  <select id="country_dropdown" name = "country_dropdown"  class="form-control greenBorder" data-required="true" style="padding-right:0px;text-transform: capitalize;"  onchange="selectCountry(this.id,'state_dropdown','city_dropdown');">
                      <option <?php //echo $selected; ?> value="<?php echo $country; ?>"><?php echo $country;?></option>	  
					  </select>
                    <?php  }else{?>
					 <select id="country_dropdown" name = "country_dropdown"  class="form-control greenBorder" data-required="true" style="padding-right:0px;text-transform: capitalize;"  onchange="selectCountry(this.id,'state_dropdown','city_dropdown');">	
						 
						 </select>
					<?php 
					}?>
                 <span id="countryError" class="error required"></span>
                </div>
            <div class="col-sm-12 clear"></div>
			 
                <div class="col-sm-6">
                  <label for="state_dropdown" class="control-label">State <span class="required">*</span></label>  
				  <input type="text" class="form-control greenBorder" id="other_state" name = "other_state" value="<?php echo $centreValue['state']; ?>"  <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                  <select id="state_dropdown" name = "state_dropdown" onclick="selectState(this.options[this.selectedIndex].value)" onChange="selectState(this.options[this.selectedIndex].value)" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;"  <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                    <option value="">Select state</option>
                  </select>
                  <span id="state_loader"></span> 
				  <span id="stateError" class="error required"></span>
					</div>
				 
                 <div class="col-sm-6">
                  <label for="city_dropdown" class="control-label">City <span class="required">*</span></label>
				  <input type="text" class="form-control greenBorder" id="other_city" name = "other_city" value="<?php echo $centreValue['city']; ?>"  <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                  <select id="city_dropdown" name = "city_dropdown" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;" <?php echo $hideIndia;?> <?php echo $hideOther;?>>
                    <option value="">Select city</option>
                  </select>
                  <span id="city_loader"></span> 
				  <span id="cityError" class="error required"></span>
				  </div>
			 <div class="col-sm-12 clear">&nbsp;</div>
                <div class="col-sm-6">
                  <label for="centerPincode" class="control-label">Pin Code <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder" id="centerPincode" name = "centerPincode" data-type="digits" maxlength="6" data-required="true" value="<?php echo $centreValue['pincode']; ?>">
                </div>
				 <div class="col-sm-6">
                  <label for="centerAddress" class="control-label"> Address <span class="required"></span></label>
                  <textarea class="form-control greenBorder" id="centerAddress" name = "centerAddress" style="height:40px; resize: none;"  spellcheck="false"><?php echo $centreValue['address1']; ?></textarea>
                </div>		
				 </div>
				<div class="col-sm-12 clear"></div>
				<div class="text-center">
				 <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
				 <input type="hidden" name="cid" value="<?php echo (!empty($cid))? $cid:''?>" />
				 <input type="hidden" name="aid" value="<?php echo (!empty($cid))? $userDetail->address_id:''; ?>" />
				 <input type="hidden" name="uid" value="<?php echo (!empty($cid))? $userDetail->user_id:''; ?>" />
				 <input type="hidden" name="ugid" value="<?php echo (!empty($cid))? $userDetail->user_group_id:''; ?>" />
                </div>
              </div>
            </div>
         
		  <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header">Select Course</h3>
			 
			   <div class="form-group">
				<div class="col-sm-12 clear"></div>
               
                 <div class="col-sm-6" style="line-height:0px;">
                 <input type="checkbox" class="form-control" id="course" name="course" checked style="width: 15px;display: inline-block;height: 20px;">
                  <label for="course" style="display: inline-block;">Mepro Course</label>
                </div>
                      </div>
 
            </div>
         </div>
		 <div class="col-sm-12 clear"></div>
		 <div class="text-right"> 
			<a href='centerList.php' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
			<a href='addCenterDetail.php' class="btn btn-s-md btn-primary textCap" onclick="return showLoaderOrNot('centerRegForm');" ondblclick="return showLoaderOrNot('centerRegForm');">Add <?php echo $center; ?></a>
			<!--<button type="submit" class="btn btn-s-md btn-primary textCap" onclick="return showLoaderOrNot('centerRegForm');" ondblclick="return showLoaderOrNot('centerRegForm');">Add <?php echo $center; ?></button>-->
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
			$("#"+loadType+"_dropdown").html("<option value=''>Select "+loadType+"</option>");
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
		var state = document.getElementById("state_dropdown");
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
		}
	
	}else{
		
		var stateVal = document.getElementById("other_state").value;
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
		}
		
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

function loadCountry(countryData){
	var cValue=$("#country_dropdown :selected").val();
	var dataString = 'country='+ countryData;
	//$("#"+loadType+"_loader").show();
    $.ajax({
		type: "POST",
		url: "ajax/allCountry.php",
		data: dataString,
		cache: false,
		success: function(result){
			//console.log(result)
			$("#country_dropdown").html(result);
			//$("#country_dropdown").html("<option value=''>"+result+"</option>");
			
		}
	});
}
loadCountry(countryData);

function selectCountry(id,state,city){
	
	var cValue=$("#"+id+" :selected").val();
	 loadCountry(cValue);
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
</script>



 
 
