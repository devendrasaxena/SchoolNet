<?php error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../header/adminHeader.php');
$msg='';	
$err='';	
$succ='';	
   
 $reportObj = new reportController();
 $designationObj = new designationController();

    $district_list_arr_drop_down =$reportObj->getDistrictList('',$country);
	
	if(isset($_REQUEST['did'])){
		$did = trim( base64_decode($_REQUEST['did']) );
		if(is_numeric($did)==true){		
			$designationDetail =$designationObj->getDesignationDetail($did);
		}else{
			header('Location: dashboard.php');
			die;
		}
		
		$designation = $designationDetail->designation; 
		$desination_short_code = $designationDetail->desination_short_code; 
		$description = $designationDetail->description; 

	    $pageType = $language[$_SESSION['language']]['edit'];
	

	
	} else{ 

		
		$pageType =$language[$_SESSION['language']]['add'];;
		$designation = ''; 
		$desination_short_code = ''; 
		$description = '';
		
	}
  

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
		if(isset($_SESSION['designation_details'])){
			$designationValue = $_SESSION['designation_details'];
			$designation = $designationValue['designation'];
			$desination_short_code = $designationValue['desination_short_code'];
			$description = $designationValue['description'];
		}
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
		unset($_SESSION['designation_details']);
		
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		//$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);
}

 
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "Designation not saved. Please try again.";
	}
}

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
	
}  
    
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="designationList.php" title="<?php echo $language[$_SESSION['language']]['manage_designation']; ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['manage_designation']; ?></a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">

	<div class="col-sm-12">
		
	   <?php if($err == '1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
		  <?php } ?>
		  <?php if($err == '2'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
		  <?php } ?>
    </div>
  
    <form role="form" method = "POST" action = "ajax/register_designation.php" id="centerRegForm" class="form-horizontal form-centerReg centerRegForm" data-validate="parsley" onSubmit="return confSubmit();" autocomplete="off">
	
      <div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header"> <?php echo $pageType.' '.$language[$_SESSION['language']]['designation'];; ?></h3>
			 <div class="form-group">
            
		  
		   
             
			<div class="form-group">
				<div class="col-sm-12 clear"></div>
                <div class="col-sm-6">
                  <label for="centerName" class="control-label"><?php echo $language[$_SESSION['language']]['designation_short_code']; ?> <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder parsley-validated" id="desination_short_code" name="desination_short_code" data-required="true" maxlength = "50" value="<?php echo $desination_short_code; ?>"/>
                  <label class="required" id="errorCenterName"></label>
                </div> 
				<div class="col-sm-6">
                  <label for="centerName" class="control-label"><?php echo $language[$_SESSION['language']]['designation']; ?>  <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder parsley-validated" id="designation" name="designation" data-required="true" maxlength = "50" value="<?php echo $designation; ?>"/>
                  <label class="required" id="errorCenterName"></label>
                </div>
               <div class="col-sm-12 clear"></div>
				<div class="col-sm-6">
                  <label for="centerName" class="control-label"><?php echo $language[$_SESSION['language']]['description']; ?> <span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder parsley-validated" id="description" name="description" data-required="true" maxlength = "50" value="<?php echo $description; ?>"/>
                  <label class="required" id="errorCenterName"></label>
                </div> 
				
				 </div>
				
              </div>
            </div>
         <div class="text-right">  
		  <input type="hidden" name="did" value="<?php echo (!empty($did))? $did:''?>" />
			<a href='designationList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			<button type="submit" class="btn btn-s-md btn-primary" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" onclick="return showLoaderOrNot('centerRegForm');" ondblclick="return showLoaderOrNot('centerRegForm');"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
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



 
 
