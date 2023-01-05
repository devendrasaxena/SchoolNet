<?php include_once('../header/adminHeader.php');

$uId=$_SESSION['user_id'];
$msg='';	
$err='';	
$succ='';	
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);

	
}

?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <?php echo $language[$_SESSION['language']]['my_profile']; ?></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
     <form id="changePasswordForm" name="changePasswordForm" action="ajax/update_profile.php" method="post" data-validate="parsley" autocomplete="nope" enctype="multipart/form-data">
     <div class="row">

  <?php if($err!=''){?>
      <div class="alert alert-danger col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
      <?php } ?>
	   
	 <?php if($succ!=''){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
      <?php } ?>

	   <div class="panel panel-default noneBorder">
         <div class="panel-body">
		   <div class="col-sm-4 col-xs-4">
                <div><label class="control-label" for="logo"><?php //echo $language[$_SESSION['language']]['photo']; ?> </label>
				  </div>       
				<div class="profile text-center profileBg profileBgLogo marginTop20" style="padding-top:20px" > 
				<?php if($adminData->system_name != ''){ ?>
							<div class="thumb-md thumb-md-logo text-left  fileInputs buttonImg relative" id="logoImg" >
							<img id="viewImgProfile"  class="viewImgProfile imgBorder dataImg bdrCircle" src="<?php echo $profile_img_hosting_url.$adminData->system_name; ?>" /> 
							<span class="defaultImgShow uploadIcon" style="display:none">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="uploadProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');"> <i class="fa fa-edit"></i></a> </span>
						<span class="dataImgShow uploadIcon">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="editProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');" style="cursor:pointer"> <i class="fa fa-edit"></i> </a></span>
							
							</div>
							<input type="file" id="fileProfile" name="fileImgProfile" style="display: none;"accept="image/gif, image/jpeg, image/png"/>
							
							 <input type="hidden" name="fileImgNamePro" id="fileImgNamePro" value="<?php echo $adminData->system_name; ?>"  readonly=""/> 
							 
							   <!--<div class="clear col-sm-12" style="margin-top:10px;"></div>
							<span class="dataImgShow uploadIcon">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="editProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');" style="cursor:pointer"> <i class="fa fa-edit"></i> Edit</a>
									 | 
									 <a href="javascript:void(0)" style="cursor:pointer" onclick="removeImg('fileImgNamePro','viewImgProfile');" id="profile-pic-remove" ref="edit"  class="remove pointer"> <i class="fa fa-trash-o"></i> Remove</a>
								</span>	 -->
								     
								<?php }else{ ?>
								<div class="thumb-md thumb-md-logo fileInputs buttonImg relative" id="editProfileImg">
								<img id="viewImgProfile"  class="viewImgProfile bdrCircle"  src="<?php echo $profileImgDefault; ?>"/> 

								<span class="defaultImgShow uploadIcon">  <a  href="javascript:void(0)" class="editInputs buttonImg pointer" id="uploadProfileImg" onclick="uploadFile(this.id,'fileProfile','fileImgProfile','fileImgNamePro','viewImgProfile','profile');" style="cursor:pointer"> <i class="fa fa-edit"></i></a>
								</span>	 </div>
	                         <div class="clear"></div>
							  
								<input type="file" id="fileProfile" name="fileImgProfile" style="display: none;"accept="image/gif, image/jpeg, image/png"/>
									 <input type="hidden" name="fileImgNamePro" id="fileImgNamePro" value=""  readonly=""/> 
								<?php  }?>
								<div class="clear"></div>
							<label class="" id="profile_picError" style="margint-top:10px;font-size:12px;display:none">	(File Support- png, jpg, jpeg, gif)</label>
						</div>
                    <label class="required showErr" id="profile_picError"><?php echo $msgpic; ?></label>
				  </div>
            <div class="form-group col-sm-8">  
		   <div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['name']; ?> <span class="required">*</span></label>
			  <input type="text" name="name" id="name" placeholder="<?php echo $language[$_SESSION['language']]['name']; ?>" class="form-control input-lg "  data-required="true" value="<?php echo $adminData->first_name; ?>" maxlength = "30" autocomplete="nope"/>
			</div>
			<div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['email_id']; ?> <span class="required">*</span></label>
			  <input name="email" id="email" placeholder="abc@example.com" class="form-control input-lg disabledInput" value="<?php echo $adminData->email_id; ?>"  data-type="email"  data-required="true" maxlength = "50" autocomplete="nope" />
			</div>
			<div class="form-group col-sm-6">
			  <label class="control-label"><?php echo $language[$_SESSION['language']]['mobile']; ?> <span class="required"></span></label>
			    <input name="mobile" id="mobile" placeholder="<?php echo $language[$_SESSION['language']]['mobile_number']; ?>" class="form-control input-lg" value="<?php echo $adminData->phone; ?>"  data-type="phone"  data-minlength="[10]"  maxlength="10" data-regexp="^[1-9]\d*$" data-regexp-message="Mobile number should not be 0" autocomplete="nope" />
			</div>
			</div>
			<div class="clear"></div>
				<div  class="col-sm-12">
				<div>
				<a href="javascript:void(0)" onclick="return showHideChangePassword('ajax/changePassword.php','shcp','changePasswordForm', 'oldPassword', 'newPassword', 'cnfPassword');" class="btn btn-s-md btn-primary chpassword pointer marginTop20"><i class="passwordIcon fa fa-plus"></i> <?php echo $language[$_SESSION['language']]['change_password']; ?></a>
				</div>
				 <input type='hidden' name='user_session_id' id='user_session_id' value='<?php echo base64_encode($adminData->loginid);?>' />
				<div class="clear"></div>
				</br>
				<div id="shcp">
				</div>
			</div>
		</div>	   
      </div>
         <div class="text-right"> 
	 	 <a href='dashboard.php' class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
	       <input id="profile_id" type="hidden" name="profile_id" value="<?php echo $adminData->profile_id; ?>"/>
			<input id="userIdVal" type="hidden" name="userIdVal" value="<?php echo $uId; ?>"/>
			<input type="hidden" id="cpFlag" value="0" />
			  <button type="submit" name="uSignUp" value='adminReg' class="btn btn-s-md btn-primary"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
			
	     </div>
		
<div> 
		 <p><small><?php echo $language[$_SESSION['language']]['disclaimer']; ?>: <?php echo $language[$_SESSION['language']]['we_do_not_sell_or_share_any_personally_identifiable_information_volunteered_on_this_website_to_any_third_party_(public/private)._any_information_provided_to_this_website_will_be_protected_from_loss,_misuse,_unauthorized_access_or_disclosure,_alteration,']; ?></small></p>
		 </div>
	</div>
   </form>
  </section>
  </div>
 </div>
</section> 
<?php include_once('../footer/adminFooter.php'); ?>
<script>

function emptyErr(){
	$(".showErr").html('');
}

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
		$.post(path, {shpass: cpFlag}, function(data){ $("#"+targetDiv).html(data);$("#cpFlag").attr('value','1'); addParsely(formId,old_password,pwd, cpwd);});
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

	/*$("#profile-pic-remove").click(function(){
	   var filevalue = $("#fileImgNamePro").val('');
	   $("#viewImgProfile").attr("src","images/profile.png");
	   $("#viewImgProfileHeader").attr("src","images/avatar_default.jpg");
	   $("#viewImgProfile").removeClass("imgBorder");
	   $(".defaultImgShow").show();
	   $(".dataImgShow").hide();
	  
		});*/
 })


window.setTimeout(function() {
    $(".alertHide").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 4000);

function char_count(str, letter) 
{
 var letter_Count = 0;
 for (var position = 0; position < str.length; position++) 
 {
    if (str.charAt(position) == letter) 
      {
      letter_Count += 1;
      }
  }
  return letter_Count;
}

function uploadFile(id,inputId,input,textName,viewId,typeMode){

	 // $("#loaderDiv").show();
	
	 $("#"+inputId).click();
	$("#"+inputId).on('change', function(){
		 // alert(this)
	  var validExtensions = ['jpg','png','jpeg']; //array of valid extensions
		var fileName = this.files[0].name;
		 var countExt=char_count(fileName, '.') 
			// alert(countExt);
		var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
		//alert("fileNameExt :"+fileNameExt);
		////var fileNameExt = fileName.replace(/^.*\./, '');
		////alert("fileNameExt :"+fileNameExt);

		 if (countExt != 1)
		 {
			 this.type = '';
			  this.type = 'file';
			
			  alertPopup("Only these file types are accepted : "+validExtensions.join(', ')); 
			 return false;
		 }
		 if ($.inArray(fileNameExt, validExtensions) == -1) {
			  this.type = '';
			  this.type = 'file';
			 // $('#'+viewId).attr('src',"");  
			  alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
			  //$('#'+viewId).attr('src',defaultProfilePath); 
			 return false;
			 
		 } else{
			
				if (this.files && this.files[0]) { 
				
					var filerdr = new FileReader();
					filerdr.onload = function (e) {
					//	alert(e);
					$('#'+viewId).attr('src', e.target.result);
					//$("#"+textName).val(fileName);
					//alert(e.target.result);
					var myformData = new FormData(); 
					var file_data = $("#"+inputId).prop('files')[0];   
					//alert(file_data);
					myformData.append('file', file_data);
					myformData.append('mode', 'fileUpload');
					myformData.append('uId', <?php echo json_encode($uId); ?>);
					
						 $.ajax({
						 //  url : <?php echo json_encode($profile_img_upload_url); ?>,
						   url : 'ajax/upload_profile.php',
						   type : 'POST',
						   data : myformData,
						   async: false,
						   cache: false,
						   processData: false,  // tell jQuery not to process the data
						   contentType: false,  // tell jQuery not to set contentType
						   enctype: 'multipart/form-data',
						   dataType:'json',
						   success : function(data) {
							   console.log(data);
							   console.log(data.uploadOk);
							   if(data.uploadOk==1){
							     //alertPopup(data.msg);
								 $("#"+textName).val(data.fileName);
							   }else{
								 console.log(data.msg);  
							   }
							   //$("#loaderDiv").hide();
						    }
							
						}); 
					}
					filerdr.readAsDataURL(this.files[0]);
				}
		 }
	});
}

function removeImg(fileImgName,imgId){

	   var fileValue = $("#"+fileImgName).val('');
	   $("#"+imgId).attr("src",defaultProfilePath);
	   $(".defaultImgShow").show();
	   $(".dataImgShow").hide();
	  
		
}
$(document).ready(function(){
	
	$("#profile-pic-remove").click(function(){
	   var filevalue = $("#fileImgNamePro").val('');
	   $("#viewImgProfile").attr("src",defaultProfilePath);
	   $("#viewImgProfile").removeClass("imgBorder");
	   $(".defaultImgShow").show();
	   $(".dataImgShow").hide();
	  
		});
 })
</script>

