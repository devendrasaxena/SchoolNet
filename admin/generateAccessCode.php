<?php include_once('../header/adminHeader.php');
$msg='';	
$err='';	
$succ='';

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
	if($_SESSION['error'] == '1'){
		$msg = "Access code not saved. Please try again.";
	}
	if($_SESSION['error'] == '2'){
		$msg = "Access code generation limit exceed.";
	}
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "Access code  created successfully.";
	}
	
}
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		if($_SESSION['error'] == '3'){
			$msg = "Access code not saved. Please try again.";
		}

}

if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		//$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);

	
}
 if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		//$msg = $_SESSION['msg'];
		$error = $_SESSION['error'];
	   unset($_SESSION['msg']);
		unset($_SESSION['error']);

	
}
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
	<li> <a href="user_access_list.php"><i class="fa fa-arrow-left"></i>  Generate Access Codes</a></li>
</ul>
<div class="clear"></div>

 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
	
	<div class="col-sm-12">
		  <?php if($succ=='1'){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
      <?php } ?>
	<?php if($succ=='2'){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i> <?php echo $msg;?> </div>
      <?php } ?>
	    <?php if($err == '1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
		 <?php if($err == '2'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
		  <?php if($err == '3'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
    </div>
  
     <section class="marginBottom40">
        <section class="panel panel-default  marginBottom5">
	   <div class="row m-l-none m-r-none bg-light lter">
	   <div class="col-sm-4 col-md-4 padder-v b-light">
		<div class="col-sm-4 padd0 text-right">
		<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-info"></i>
		  <i class="fa fa-columns fa-stack-1x text-white"></i>
		  </span>
		</div>
		<div class="col-sm-8 padd0">
		 <a class="">
			<div class="h3  m-t-xs"><strong id="avLimit">0</strong></div>
			<div><small class="text-muted text-uc">Available</small></div>
		 </a>
		</div>
	  </div>
	   
	  <div class="col-sm-4 col-md-4 padder-v b-light lt">
		<div class="col-sm-4 padd0 text-right">
		<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-success"></i>
		  <i class="fa fa-columns fa-stack-1x text-white"></i>
		  </span>
		</div>
		<div class="col-sm-8 padd0">
		 <a class="">
			<div class="h3  m-t-xs"><strong id="createdLimit">0</strong></div>
			<div><small class="text-muted text-uc">Created</small></div>
		 </a>
		</div>
	  </div>
	 <div class="col-sm-4 col-md-4 padder-v b-l b-light">                     
		<div class="col-sm-4 padd0 text-right">
			<span class="fa-stack fa-2x m-r-sm  iconPadd">
		  <i class="fa fa-circle fa-stack-2x text-warning"></i>
		  <i class="fa fa-columns fa-stack-1x text-white"></i>
		   </span> 
		</div>
		 <div class="col-sm-8 padd0">
			<a class="clear">
			  <div class="h3  m-t-xs"><strong id="inactiveLimit">0</strong></div>
			  <div><small class="text-muted text-uc">Inactive</small></div>
			</a>
		 </div>
		</div>
	 
	
	</div>
      </section>
    </br>
	 
    <form role="form" method = "POST" action = "ajax/access_generate.php" id="generateAccForm" class="form-horizontal generateAccForm" data-validate="parsley"  autocomplete="off">
	
      <div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			<h3 class="panel-header">Generate Access Codes</h3>
			
			 <div class="form-group">
                <div class="col-sm-4">
                  <label class="control-label"><?php echo $center; ?> Name  <span class="required">*</span></label>
				 <select class="form-control input-md parsley-validated fld_class <?php echo $disabled;?>" name="center_id" id="center_id" data-required="true" >
				 <option  value="" >Select <?php echo $center;?></option>
				  <?php 
					 foreach ($centers_arr as $key => $value) {	
					   $centerName= $centers_arr[$key]['name'];
					   $centerId= $centers_arr[$key]['center_id']; 
					 
					  $selectedCenter =  (  $centerId == $batchCenterId ) ?  'selected ="selected"' : '';
					 ?>
					<option  value="<?php echo $centerId; ?>" <?php echo $selectedCenter; ?> ><?php echo $centerName;?></option>	
					 <?php }?>
				</select>
				
				<label class="required" id="errorCenter"> </label>
                </div>

                <div class="col-sm-4">
                  <label class="control-label">Quantity <span class="required">*</span> </label>
                  <input type="text" name="qty" id="qty" data-minlength="[1]" class="form-control input-md parsley-validated fld_class" data-regexp="^[1-9]\d*$" maxlength="10" data-required="true">
                 <label id="qty_error" class="required"></label>
				 <!-- <select class="form-control input-md parsley-validated fld_class <?php echo $disabled;?>" name="qty" id="qty" data-required="true">
				 	<option  value="" >Select Qty</option>
				  
				</select> -->

				<label class="required" id="errorCenter"> </label>
                </div>

                <div class="col-sm-4">
				 <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
				 <label class="control-label"> &nbsp;</label>
			    <button type="submit" class="btn btn-s-md btn-primary" style="margin-top:25px">Submit</button>
             </div>
			</div>
		   </div> 
		 </div>
	 </div>
   </form>
</section>
 </div>
 </div>
</section>	  
<?php include_once('../footer/adminFooter.php'); ?>

<script>
	let qty = $('#qty');
	let qty_limit = $('#qty_limit'), qtyy=0;
	$('#center_id').change(function(){
		let center_id = $(this).val();
		
		let site_url = window.location.href.replace('generateAccessCode', 'ajax/access_generate');
		console.log(site_url)
		if(center_id != ''){
			$.ajax({
				url : site_url,
				type : 'POST',
				data : {center:center_id},
				success: function(result){
					var obj = JSON.parse(result.trim());
					if(obj.status == '1'){
						$("#createdLimit").html(obj.created);
						$("#inactiveLimit").html(obj.inactive)
						$("#avLimit").html(obj.limit)
						//qty_limit.html("Access code limit: <span><strong>"+obj.limit+"<strong></span>").show();
						qtyy = obj.limit;
					}
				}
				
			})
		}
	})
	$('#generateAccForm').submit(function(){
		var center_id=$('#center_id').val();
		if(center_id!=''){
			if(qty.val() > qtyy){
				qty.css('border-color', 'red');
				$('#qty_error').html("Access code limit exceed.");
				return false;
			}
		}
		return true;
	})
	qty.keydown(function(){ 
	  $(this).css('border-color', '')
	  $('#qty_error').html('');
	});

</script>



 
 
