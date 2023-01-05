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
			 <h3 class="panel-header">Add Admin</h3>
			 
			<div class="form-group">
		
			  <div class="col-sm-6">
                  <label for="emailId" class=" control-label">Admin Email<span class="required">*</span></label>
                  <input type="email" class="form-control greenBorder" placeholder = "" id="emailId" name="emailId" data-type="email" data-required="true" maxlength = "50" style="text-transform:none" value="<?php echo $centreValue['email_id']; ?>" <?php echo (!empty($centreValue['email_id']))?'disabled':''?>/>
                  <div class="col-sm-12" style="padding-left:0px">
                    <p><small> Email id will be used as username for login.<!--(Admin login credentials will be sent to this Email ID.)--></small></p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label for="centerAdminName" class="control-label">Admin Name <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder" id="centerAdminName" placeholder = "" name = "centerAdminName" data-required="true" maxlength = "50" data-regexp="^[a-zA-Z][a-zA-Z0-9 ]*$" data-regexp-message="Value should be alpha/alphanumeric" value="<?php echo $centreValue['description']; ?>"/>
                  <label class="required" id="errorName"></label>
                </div>
               <div class="col-sm-12 clear"></div>
                 <div class="col-sm-6">
                  <label for="password" class=" control-label">Password<span class="required">*</span></label>
                  <input type="password" class="form-control greenBorder" placeholder = "" id="password" name="password" data-type="password" data-required="true" maxlength = "50" style="text-transform:none" value="" />
                 
                </div>
                <div class="col-sm-6">
                  <label for="password" class=" control-label">Confirm Password<span class="required">*</span></label>
                  <input type="password" class="form-control greenBorder" placeholder = "" id="cpassword" name="cpassword" data-type="password" data-required="true" maxlength = "50" style="text-transform:none" value="" />
                 
                </div>
              <div class="col-sm-12 clear"></div>
                <div class="col-sm-6">
                  <label for="mobileNumber" class="control-label">Mobile Number <span class="required"></span></label>
                  <input type="text" class="form-control greenBorder" placeholder = "" id="mobileNumber" name="mobileNumber" data-type="phone" data-minlength="[10]"  maxlength="10" data-regexp="^[1-9]\d*$" data-regexp-message="Mobile number should not be 0" value="<?php echo $centreValue['mobile']; ?>"/>
                  <div class="col-sm-12" style="padding-left:0px">
                   
                  </div>
                </div>
                <div class="col-sm-6">
                 
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
         <div class="text-right"> 
			<a href='centerList.php' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
			<button type="submit" class="btn btn-s-md btn-primary" onclick="return showLoaderOrNot('centerRegForm');" ondblclick="return showLoaderOrNot('centerRegForm');">Next</button>
	    </div>
        
     </div>
   </form>
</section>
 </div>
 </div>
</section>	  
<?php include_once('../footer/adminFooter.php'); ?>

<script type="text/javascript">
   
</script>



 
 
