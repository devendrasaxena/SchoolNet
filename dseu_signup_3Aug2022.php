<?php
//require_once 'header/is_logged_in.php';
include_once 'header/loginHeader.php';

if( isset($_SESSION['REGISTRATION']['FIELDS']) ){
    array_walk_recursive($_SESSION['REGISTRATION']['FIELDS'], 'htmlentityCallback');
}

$name = isset($_SESSION['REGISTRATION']['FIELDS']['reg_name']) ? $_SESSION['REGISTRATION']['FIELDS']['reg_name'] : '';
$email = isset($_SESSION['REGISTRATION']['FIELDS']['reg_email']) ? $_SESSION['REGISTRATION']['FIELDS']['reg_email'] : '';
$mobile = isset($_SESSION['REGISTRATION']['FIELDS']['mobile']) ? $_SESSION['REGISTRATION']['FIELDS']['mobile'] : '';
$password = isset($_SESSION['REGISTRATION']['FIELDS']['reg_password']) ? $_SESSION['REGISTRATION']['FIELDS']['reg_password'] : '';


 $msg = '';
 $reg_status = 0;

if( isset($_SESSION['REGISTRATION']['ERR']['MSG']) ){
    $msg = trim($_SESSION['REGISTRATION']['ERR']['MSG']);
    $reg_status = 0;
	//unset($_SESSION['reg_status']);
	unset($_SESSION['REGISTRATION']['ERR']['MSG']);
	unset($_SESSION);
}


 if(isset($_GET['err']) && $_GET['err'] == 'deactivated'){ 
   $msg= "Your account deactivated";
 }
if(isset($_GET['err']) && $_GET['err'] == '0'){ 
  $msg= "Something wrong . Please try again.";
}

?> 

<div class="bgImg bgLoginImg">
 
	  <div class="login_mainDiv">
		<div class="relative">
		<div class="midDiv row">
			<div class="col-sm-4  col-xs-6"><img src="images/theme5/leftTop.png" class="lefttopImg"/></div>
			<div class="col-sm-8  col-xs-6"><img src="images/theme5/midTop.png" class="midtopImg"/></div>
		</div>
		<div class="midDiv row">
		<div class="col-sm-2 xs-hidden"></div>
		<div class="col-sm-4  col-xs-4 text-left">
			<div class="col-sm-12 text-center"><img src="images/theme5/leftMidImg1.png" class="leftMidImg1"/></div>
			<div class="clear"></div>
			<div class="col-sm-12 text-center"> <img src="images/theme5/leftMidImg2.png" class="leftMidImg2"/></div>
		</div>
		<div class="col-sm-4  col-xs-8 text-left">
		     <div class="loginDiv">

		
	  <form class="reg-form" id="regForm" name ="regForm" action="do-registration.php" method="post" data-validate="parsley" enctype="multipart/form-data" onsubmit="return regValidation('reg_email','reg_password','reg_name','reg_mobile_number');" autocomplete="off">	
       <input type="hidden" name="registration_form" value="1">	  
		 <div class="regBox relative" style="    min-height: 490px;">
		<div class="loginRegBox regBox relative">
		 <p class="heading reg-heading relative">Create your <?php echo APP_NAME;?> account</p>
		
		  <div class="formMainDiv">
		   <div class="form-Div">
			  <label class="label">Email</label>
			  <div class="inputDiv">
				<input type="text" id="reg_email" name="reg_email" class="inputText" value="" maxlength="100" tabindex="1" autocomplete="off"/>
			  </div>
		  </div>
		  <div class="form-Div">
			  <label class="label">Name</label>
			  <div class="inputDiv">
				<input type="text" id="reg_name" name="reg_name" class="inputText" value=""  maxlength="50" tabindex="2"  autocomplete="off"/>
			  </div>
		  </div>
			<div class="form-Div">
			  <label class="label">Create Password</label>
			  <div class="inputDiv">
				<input type="password" id="reg_password" name="reg_password" class="inputText password" value="" tabindex="3" type="password"   maxlength="15" autocomplete="off" />
				<label class="" style="font-size: 12px;margin-top:5px" id="login_pass"><?php echo $passRegexpMsg; ?></label>
				<span class="reveal"><img class="imgOff" src="images/hide-on.png" class="img"/><img class="imgOn hide" src="images/show-on.png" class="img"/><!--<i class="fa fa-eye-slash"></i>--></span>
			  </div>
		 </div>
		 <div class="form-Div">
			  <label class="label">Mobile</label>
				
				<div class="inputDiv inputMobileDiv">
				<input type="phone" id="reg_mobile_number" name="reg_mobile" class="inputText inputMobile" value="" maxlength="11" tabindex="6" autocomplete="off"/>
				 <input id="mobile" type="hidden" name="mobile" value="">
				 <div id="reg_mobile_number_err"></div>
			  </div>
			   <input type="hidden" id="country_code" name="country_code"  value="" autocomplete="off"/>
			  <input id="reg_mobile_selected" name="reg_mobile_selected" class="inputText dailcode" value="" tabindex="5" autocomplete="off"/>
		  </div>
		  <div class="clear"></div>
		  <div class="btnDiv">
			 <div class="btnBg">
			   <input id="region_id" type="hidden" name="region_id" value="<?php echo client_reg_id;?>">
		 		
			   <button id="registration" type="submit" class="btn" tabindex="7" value="Register">Create Account</button>
			  </div>
			</div>
		  </div>
		</div>
		 <div class="clear"></div>
		 <div class="text-center relative taketestDiv">
		    <a class="taketest text-right underLine" href="index.php" tabindex="8">Login</a> | <a class="taketest text-right underLine" href="forgotPassword.php" tabindex="9">Forgot password?</a></div>
		   <div class="clear"></div>
		   </div>

		</form>
		 </div>
			</div>	
			<div class="col-sm-2 col-xs-1 vMidlle text-center"><img src="images/theme5/rightMidImg.png" class="rightMidImg"/></div>
	 <div class="clear"></div>	
  
	</div>
	 	
  </div>	
  <div class="bgFooter"></div>
<?php include_once 'footer/loginFooter.php';?>
<link rel="stylesheet" href="js/phoneCode/intlTelInput.css">
<link rel="stylesheet" href="js/phoneCode/jquerysctipttop.css">
<script src="js/phoneCode/intlTelInput.js"></script>

<script>
var msg ="<?php echo $msg?>";
	if(msg!=""){
	 alertPopup(msg);
	}


   $("#reg_mobile_selected").intlTelInput({
        formatOnInit: false,
        allowDropdown: false,
        autoHideDialCode: false,
        autoPlaceholder: false,
        dropdownContainer: "body",
        excludeCountries: [],
        geoIpLookup: function (callback) {
          $.get("https://ipinfo.io", function(response) {
				console.log(response.city, response.country);
				callback(response.country);
			}, "jsonp");
			//callback('GG');
        /*  $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
         var countryCode = (resp && resp.country) ? resp.country : "";
         callback(countryCode);//alert(countryCode);
         }); */
           //callback('IN');
        },
        initialCountry: "auto",
        nationalMode: true,
        numberType: "MOBILE",
        onlyCountries: [],
        // the countries at the top of the list. defaults to united states and united kingdom
        preferredCountries: ["gg","in"],
        separateDialCode: true,
        utilsScript: "js/phoneCode/utils.js"
    });

    //$("#reg_mobile_selected").intlTelInput("setNumber", reg_mobile_number);


function regValidation(email,pass,name,mobile){
           $("#"+email).after('');	  
		   $("#"+pass).after('');
		   $("#"+name).after('');
		   $("#"+mobile).after('');
			$("#login_email_err").remove();
			$("#login_name_err").remove();
			$("#login_pass_err").remove();
			$("#login_mobile_err").remove();
        var setFlag = 0
	
	
		if($("#"+email).val() == ""){
			
			 $("#"+email).after('<label class="required showErr error" id="login_email_err">Please enter email address.</label>');
			$("#"+email).addClass("errorClassbdr");
			setFlag = 1;
			$("#"+email).focus();
			return false;
		}
		if($("#"+email).val() != ""){
			var emailValue=$("#"+email).val();
			
			var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(!regex.test(emailValue)) {
				 $("#"+email).after('<label class="required showErr error" id="login_email_err">Please enter a valid email address.</label>');
				 $("#"+email).addClass("errorClassbdr");
				setFlag = 1;
				$("#"+email).focus();
			   return false;
			}
		
      }
	  
	if($("#"+name).val() == ""){
		    $("#"+name).after('<label class="required showErr error" id="login_email_err">Please enter name.</label>');
			$("#"+name).addClass("errorClassbdr");
			setFlag = 1;
			$("#"+name).focus();
			return false;
     }
	 
	 if($("#"+name).val() != ""){
			var nameValue=$("#"+name).val();
			//var regex = /^[A-Za-z]+$/; //only alpha
			var regex = /^[A-Za-z][A-Za-z\s]*/;//only alpha with space

			if(!regex.test(nameValue)) {
				 $("#"+name).after('<label class="required showErr error" id="login_name_err">Name should contain alphabets only and minimum 3 characters or more.</label>');
				 $("#"+name).addClass("errorClassbdr");
				setFlag = 1;
				$("#"+name).focus();
			   return false;
			}
	 }
		
	if($("#"+pass).val() == ""){
		 $("#"+pass).after('<label class="required showErr error" id="login_pass_err">Please enter a password</label>');
		$("#"+pass).addClass("errorClassbdr");
			setFlag = 1;
			$("#"+pass).focus();
			return false;
		}
	if($("#"+pass).val() != ""){
		var passValue=$("#"+pass).val();
		//var regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,15
			//var regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,15}$/;
	     //var regex =/^(?=.*[A-Za-z])(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,15}$/;
		var regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_@.-])[A-Za-z\d_@.-]{8,15}$/;
		if(!regex.test(passValue)) {
		 //$("#"+pass).after('<label class="required showErr error" id="login_pass_err">The password should be between 6 to 15 characters, at least 1 character and 1 number.</label>');
		 //$("#"+pass).after('<label class="required showErr error" id="login_pass_err">The password should be between 6 to 15 characters,  1 uppercase and lowercase character ,at least 1 number and at least 1 special character.</label>');
		 $("#"+pass).addClass("errorClassbdr");
			setFlag = 1;
			$("#"+pass).focus();
			return false;
		}
	}
	
	if($("#"+mobile).val() != ""){
		var mobileValue=$("#"+mobile).val();
//alert(mobileValue)
		var regex = /^[0][1-9]\d{9}$|^[1-9]\d{9}$/;
		if((!regex.test(mobileValue)) && (mobileValue.length<9)) {
		 $("#"+mobile+"_err").after('<label class="required showErr error" id="login_mobile_err">Invalid mobile number.</label>');
		 $("#"+mobile).addClass("errorClassbdr");
			setFlag = 1;
			$("#"+mobile).focus();
			return false;
		}
	}
	
	if(setFlag == 1){
	  return false;
	  }else{
		return true;
	  }	
}


 $(document).ready(function () {
     $('input').attr('autocomplete', 'false');
        $("#regForm").submit(function () {
			var reg_mobile_selected=$("#reg_mobile_selected").intlTelInput("getNumber");
			var reg_mobile_number=$("#reg_mobile_number").val();
			if(reg_mobile_number!=""){
				console.log(reg_mobile_selected+""+reg_mobile_number);
             $("#mobile").val(reg_mobile_selected+""+reg_mobile_number);
			  $("#country_code").val(reg_mobile_selected);
			}
        });
	/* $(".password").hover(function () {
		$(this).popover({
			html:true,
			title: "Password Strength: Strong",
			content: "<p> <i class='fa fa-check fa-success'></i> 6 or more characters</p> <p> <i class='fa fa-times fa-danger'></i> One uppercase and lowercase character </p> <p><i class='fa fa-check fa-success'></i> At least one number </p> <p><i class='fa fa-times fa-danger'></i> At least one special character</p>"

		}).popover('show');
	}, function () {
		$(this).popover('hide');
	}); */
//data-toggle="popover" data-trigger="hover" data-html="true"  data-content="" title="Password Strength: Strong"

	$(".inputText").blur(function() {
	   // console.log(dInput);
		//$(".showErr").html('');
		//$(".showErr").removeClass("errorClassbdr");
	});
	$("#reg_password").blur(function() {
	  $(".password").popover('hide');
		popOverPosition();
	});
	$(".inputText").keypress(function() {
		$(".showErr").html('');
		$(".inputText").removeClass("errorClassbdr");
		$('.showErr').remove();
	});
});

function checkStrongPass(id){
	
	var passValue=$("#"+id).val();
	var text='';
	var regex=/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,15}$/;
	//data-regexp-message="Password should be between 6 to 15 characters, at least 1 letter, 1 number and 1 special character."
	console.log(passValue);
	console.log(passValue.length);
	var strength = 0;
	var strengthCheck='';
	var strengthValue='';
	var caseValue='';
	var numberValue='';
	var specialValue='';
	if (passValue.length < 6) {
		 strengthValue="<i class='fa fa-times fa-danger'></i>";
		//return 'Too short'
		  strengthCheck= 'Too short' ;  
	  }else{
	   strengthValue="<i class='fa fa-check fa-success'></i>";
	   strength += 1;	 
	 }
	 console.log(strengthValue);
// If password contains both lower and uppercase characters, increase strength value.
if (passValue.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){ 
     strength += 1 ;
      caseValue="<i class='fa fa-check fa-success'></i>";
   }else{
	  caseValue="<i class='fa fa-times fa-danger'></i>" ;
	   
   }
 // If it has numbers and characters, increase strength value.
  //if (passValue.match(/([a-zA-Z])/) && passValue.match(/([0-9])/)){ 
	if (passValue.match(/([0-9])/)){ 
		strength += 1 ;
		numberValue="<i class='fa fa-check fa-success'></i>";
	}else{
		  numberValue="<i class='fa fa-times fa-danger'></i>" ;
		
	}
	
// If it has one special character, increase strength value.
//if (passValue.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) {
  if (passValue.match(/([.,_,-,@])/)) {
	  strength += 1
      specialValue="<i class='fa fa-check fa-success'></i>";
	}else{
		  specialValue="<i class='fa fa-times fa-danger'></i>" ;
		
	} 
	
if (strength < 2 ){
   strengthCheck= 'Weak' ;  
}else if (strength == 2 ){
	strengthCheck= 'Good' ;
}else if (strength == 3 ){
	strengthCheck ='Very Good' 	;
}else{
	strengthCheck= 'Strong';
}
	text= "<p> "+strengthValue+ " 6 or more characters</p> <p> "+caseValue+" One uppercase and lowercase character </p> <p> "+numberValue+" At least one number </p> <p> "+specialValue+" At least one special character</p>";

  
	console.log(text)
	
		$(".password").popover({
			html:true,
			trigger: 'manual',
			title: strengthCheck,
			content:text,
			placement:popOverPosition()

		}).popover('show');
	 $('.popover .popover-body').html(text);
	 $('.popover .popover-header').html("Password Strength: " +strengthCheck);
	 
		
 }
   function popOverPosition(){
	  return $(window).width() < 975 ? 'top' : 'right'; 
   }
   	  
window.addEventListener("resize", popOverPosition);	
	
$(".reveal").on('click',function() {
    var $pwd = $(".password");
    if ($pwd.attr('type') === 'password') {
        $pwd.attr('type', 'text');
		$(".imgOff").addClass('hide');
		$(".imgOn").removeClass('hide');
		
    } else {
        $pwd.attr('type', 'password');
		$(".imgOff").removeClass('hide');
		$(".imgOn").addClass('hide');
		
    }
});

</script>
