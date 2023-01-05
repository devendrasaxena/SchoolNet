<?php include_once('../header/adminHeader.php');
$msg='';	
$err='';	
$succ='';	
error_reporting(E_ALL);
ini_set('display_errors',1);




$districtObj = new districtController(); 
	if(isset($_REQUEST['did'])){
		$did = trim( base64_decode($_REQUEST['did']) );
		if(is_numeric($did)==true){		
			$districtDetail = $districtObj->getDistrictDetail($did);
		}else{
			header('Location: dashboard.php');
			die;
		}	
		
		$stateId = $districtDetail->state_id;
		$district_name = $districtDetail->district_name; 
	    $pageType ="Edit";
	
	}else{ 
		$pageType ="Add";
		$stateId = '';
		$district_name = ''; 
	
	}

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
		if(isset($_SESSION['district_details'])){
			$districtValue = $_SESSION['district_details'];
			$district_name = $districtValue['name'];
			$stateId = $districtValue['state_dropdown'];
		}
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
		unset($_SESSION['district_details']);
		
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		//$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);
}

 
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$district not saved. Please try again.";
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
 <li> <a href="districtList.php" title="<?php echo $language[$_SESSION['language']]['manage_districts']; ?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['manage_districts']; ?></a></li>
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
  
    <form role="form" method = "POST" action = "ajax/register_district.php" id="centerRegForm" class="form-horizontal form-centerReg centerRegForm" data-validate="parsley" onSubmit="return confSubmit();" autocomplete="off">
	
      <div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header"> <?php echo $language[$_SESSION['language']]['add_district']; ?></h3>
			 <div class="form-group">
            
		  
		   
             
			<div class="form-group">
				<div class="col-sm-12 clear"></div>
				
                <div class="col-sm-6">
                  <label for="centerName" class="control-label"><?php echo $language[$_SESSION['language']]['district_name']; ?><span class="required">*</span></label>
                  <input type="text" class="form-control greenBorder parsley-validated" id="name" name="name" data-required="true" maxlength = "50" value="<?php echo $district_name; ?>"/>
                  <label class="required" id="errorCenterName"></label>
                </div>
               
				 <div class="col-sm-6">
				 <label for="state_dropdown" class="control-label"><?php echo $language[$_SESSION['language']]['select_state']; ?> <span class="required">*</span></label>
                <select name="state_dropdown" id="state_dropdown" class="form-control greenBorder" data-required="true" style="padding-right:0px;text-transform: capitalize;"  data-required="true">
					   
					   <option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
						if(count($center_list_arr_drop_down)>0){
						 $optionSelected = ($center_id == 'All') ? "selected" : "";
						 
						 foreach($center_list_arr_drop_down  as $key => $value){
								$centerId=$center_list_arr_drop_down[$key]['center_id'];
								$center_name=$center_list_arr_drop_down[$key]['name'];
								$optionSelected = ($stateId == $centerId) ? "selected" : "";
								if( $center_name=='DFPD'){
									$dfpdVar=  "disabled";
								  }else{
									$dfpdVar='';  
								  }	
								echo '<option   value="'.$centerId.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.$dfpdVar.'>'.$center_name.'</option>';
								
						 }
						}
						

					   ?>
					</select>
			</div>
				
            <div class="col-sm-12 clear"></div>

				 </div>
				
              </div>
            </div>
         <div class="text-right">
		<input type="hidden" name="did" value="<?php echo (!empty($did))? $did:''?>" />		 
			<a href='districtList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			<button type="submit" title="<?php echo $language[$_SESSION['language']]['submit']; ?>"  class="btn btn-s-md btn-primary" onclick="return showLoaderOrNot('centerRegForm');" ondblclick="return showLoaderOrNot('centerRegForm');"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
	    </div>
     </div>
   </form>
</section>
 </div>
 </div>
</section>	  
<?php include_once('../footer/adminFooter.php'); ?>

<script type="text/javascript">

function confSubmit(){

	
}
</script>



 
 
