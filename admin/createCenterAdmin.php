<?php include_once('../header/adminHeader.php');

$regionArr = $centerObj->getRegionDetails();
if (isset($_REQUEST['uid'])) {
	$uid = trim(base64_decode($_REQUEST['uid']));
	if(is_numeric($uid)==true){
		$userCenter = userdetails($uid);
	}else{
		header('Location: dashboard.php');
		die;
	}
	$cid = $userCenter->center_id;
	$addressId = $userCenter->address_id;
	$centreValue = $centerObj->getCenterOnline($cid);
	$address = $centerObj->get_master_address($addressId);
	//echo "<pre>";print_r($address);exit;
	$countryData = $address[0]['country'];
	if ($countryData == 'India') {
		$stateData = $address[0]['state'];
		$cityData = $address[0]['city'];
		$hideIndia = '';
		$hideOther = 'style="display:none;"';
	} else {
		$stateOther = $countryData[0]['state'];
		$cityOther = $countryData[0]['city'];
		$hideIndia = 'style="display:none;"';
		$hideOther = '';
	}

	$pageType = $language[$_SESSION['language']]['edit'] ;
} else {
	$pageType = $language[$_SESSION['language']]['add'] ;;
	$resCountry = $commonObj->getCountry();
	$countryData = $resCountry[0]['country_name'];
	$stateData = '';
	$cityData = '';
}

?>
<ul class="breadcrumb no-border no-radius b-b b-light">
	<li> <a href="centerAdminList.php" title="<?php echo $center_admin; ?>"><i class="fa fa-arrow-left"></i> <?php echo $center_admin; ?></a></li>
</ul>
<div class="clear"></div>
<section class="padder">
	<div class="row-centered">
		<div class="col-sm-10 col-xs-12 col-centered">
			<section class="marginBottom40">



				<form role="form" method="POST" action="ajax/centerAdminFormSubmit.php"  id="adminRegForm" name="adminRegForm" class="form-horizontal form-centerReg adminRegForm" data-validate="parsley" onSubmit="return confSubmit();" autocomplete="nope">

					<div class="row">
						<div class="panel panel-default bdrNone">
							<div class="panel-body padd20">
								<h3 class="panel-header"> <?php echo $pageType ." " .$center_admin; ?> </h3>
								<div class="form-group">
									<div class="col-sm-12 clear"></div>
									<div class="col-sm-6">
										<label for="center" class="control-label"><?php echo $language[$_SESSION['language']]['states']; ?><span class="required"> *</span></label>
										<select class="form-control input-lg parsley-validated fld_class " name="center" id="center" data-required="true">
											<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
											<?php
											foreach ($centers_arr as $key => $value) {
												$centerName = $centers_arr[$key]['name'];
												$centerId = $centers_arr[$key]['center_id'];

												$selectedCenter =  ($centerId == $cid) ?  'selected ="selected"' : '';
											?>
												<option value="<?php echo $centerId; ?>" <?php echo $selectedCenter; ?>><?php echo $centerName; ?></option>
											<?php } ?>
										</select>
										<label class="required" id="errorName"></label>
									</div>
									<div class="col-sm-6">
										<label for="centerAdminName" class="control-label"><?php echo $center_admin.' '.$language[$_SESSION['language']]['name']; ?><span class="required"> *</span></label>
										<input type="text" class="form-control greenBorder" id="centerAdminName" placeholder="<?php echo $language[$_SESSION['language']]['name']; ?>" name="centerAdminName" data-required="true" maxlength="50" value="<?php echo $userCenter->first_name; ?>" autocomplete="nope"/>
										<label class="required" id="errorName"></label>
									</div>
									<div class="col-sm-6">
										<label for="emailId" class=" control-label"><?php echo $center_admin.'  '.$language[$_SESSION['language']]['email']; ?><span class="required"> <?php echo (!empty($userCenter->email_id)) ? '' : '*' ?></span></label>
										<input type="email" class="form-control greenBorder" placeholder="<?php echo $language[$_SESSION['language']]['email_id']; ?>" id="emailId" name="emailId" data-type="email" data-required="true" maxlength="50" autocomplete="email-12" style="text-transform:none" value="<?php echo $userCenter->email_id; ?>" <?php echo (!empty($regionUserDetail->email_id)) ? 'disabled' : '' ?> /><div class="col-sm-12" style="padding-left:0px">
											<p><small> <?php echo $language[$_SESSION['language']]['email_id_will_be_used_as_username_for_login.']; ?>
													<!--(Admin login credentials will be sent to this Email ID.)--></small></p>
										</div>
										<div class="required error" id="emailErr"></div>
										
									</div>


									<div class="col-sm-12 clear"></div>
									<div class="col-sm-6">
										<label for="mobileNumber" class="control-label"><?php echo $language[$_SESSION['language']]['mobile_number']; ?><span class="required"></span></label>
										<input type="text" class="form-control greenBorder" placeholder="<?php echo $language[$_SESSION['language']]['mobile_number']; ?>" id="mobileNumber" name="mobileNumber" data-type="phone" data-minlength="[10]" maxlength="10" data-regexp="^[1-9]\d*$" data-regexp-message="Mobile number should not be 0" value="<?php echo $address[0]['phone']; ?>" autocomplete="nope"/>
										<div class="col-sm-12" style="padding-left:0px">
											<p style="display:none">
												<!--<small>(Admin login credentials will be sent to this Mobile Number.)</small>-->
											</p>
										</div>
									</div>
									<div class="col-sm-6">
										<label for="centerContactNumber" class="control-label"><?php echo $language[$_SESSION['language']]['contact_number']; ?><span class="required"></span></label>
										<input type="text" placeholder="<?php echo $language[$_SESSION['language']]['contact_number']; ?>" class="form-control greenBorder" id="centerContactNumber" name="centerContactNumber" data-type="phone" maxlength="12" value="<?php echo $address[0]['landline_no']; ?>" autocomplete="nope"/>
										<div class="col-sm-12" style="padding-left:0px">
											<p><small><?php echo $language[$_SESSION['language']]['enter_landline_number_with_std_code.']; ?></small></p>
										</div>
									</div>

									<div class="col-sm-12 clear"></div>
									<div class="col-sm-6 <?php if ($uid != '') {
																echo "hide";
															} ?>">
										<label class="control-label"><?php echo $language[$_SESSION['language']]['password']; ?> <span class="required"> *</span></label>
										<input type="password" name="password" id="password" placeholder="<?php echo $language[$_SESSION['language']]['password']; ?> " class="form-control input-lg " value="" maxlength="15" autocomplete="pwd" <?php if ($uid != '') {
																																																								echo "readonly";
																																																							} else { ?> data-required="true" data-regexp="<?php echo $passRegexp; ?>" data-regexp-message="<?php echo $passRegexpMsg; ?>" <?php } ?> />
										<label class="" style="font-size: 12px;margin-top:5px" id="login_pass"><?php echo $language[$_SESSION['language']]['the_password_must_have_8_or_more_characters,_at_least_one_uppercase_letter,_and_one_number']; ?></label>
									</div>
									<div class="col-sm-6 <?php if ($uid != '') {
																echo "hide";
															} ?>">
										<label class="control-label"><?php echo $language[$_SESSION['language']]['confirm_password']; ?><span class="required"> *</span></label>
										<input type="password" name="cpassword" id="cpassword" placeholder="<?php echo $language[$_SESSION['language']]['confirm_password']; ?>" class="form-control input-lg " maxlength="15" value="" data-equalto="#password" autocomplete="nope" <?php if ($uid != '') {
																																																															echo "readonly";
																																																														} else { ?> data-required="true" <?php } ?> />
									</div>

									<div class="col-sm-12  <?php if ($uid == '') {
																echo "hide";
															} ?>">
										<div>
											<a href="javascript:void(0)" onclick="return showHideChangePassword('ajax/changePassword.php','shcpDiv','adminRegForm', 'oldPassword', 'newPassword', 'cnfPassword');" class="btn btn-s-md btn-primary chpassword pointer marginTop20"><i class="passwordIcon fa fa-plus"></i><?php echo $language[$_SESSION['language']]['change_password']; ?></a>
											<input type='hidden' name='user_session_id' id='user_session_id' value='<?php echo base64_encode($regionloginid); ?>' />
										</div>
										<div class="clear"></div>
										</br>
										<div id="shcpDiv">
										</div>

									</div>
									<div class="col-sm-12 clear" style="border-bottom: 1px solid #eee; display:none"></div>
									<div class="col-sm-6" style="display:none">
										<label for="country_dropdown" class="control-label">Country <span class="required"> *</span></label>
										<?php if (!empty($countryData)) { ?>
											<select id="country_dropdown" name="country_dropdown" class="form-control greenBorder" data-required="true" style="padding-right:0px;text-transform: capitalize;" onchange="selectCountry(this.id,'state_dropdown','city_dropdown');">
												<option <?php echo $selected; ?> value="<?php echo $country; ?>"><?php echo $country; ?></option>
											</select>
										<?php  } else { ?>
											<select id="country_dropdown" name="country_dropdown" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;" onchange="selectCountry(this.id,'state_dropdown','city_dropdown');">

											</select>
										<?php
										} ?>
										<span id="countryError" class="error required"></span>
									</div>


									<div class="col-sm-6" style="display:none">
										<label for="state_dropdown" class="control-label">State <span class="required"></span></label>
										<input type="text" class="form-control greenBorder" id="other_state" name="other_state" value="<?php echo $address[0]['state']; ?>" <?php echo $hideIndia; ?> <?php echo $hideOther; ?>>
										<select id="state_dropdown" name="state_dropdown" onclick="selectState(this.options[this.selectedIndex].value)" onChange="selectState(this.options[this.selectedIndex].value)" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;" <?php echo $hideIndia; ?> <?php echo $hideOther; ?>>
											<option value="">Select state</option>
										</select>
										<span id="state_loader"></span>
										<span id="stateError" class="error required"></span>
									</div>
									<div class="col-sm-12 clear" style="display:none">&nbsp;</div>
									<div class="col-sm-6" style="display:none">
										<label for="city_dropdown" class="control-label">City <span class="required"></span></label>
										<input type="text" class="form-control greenBorder" id="other_city" name="other_city" value="<?php echo $address[0]['city']; ?>" <?php echo $hideIndia; ?> <?php echo $hideOther; ?>>
										<select id="city_dropdown" name="city_dropdown" class="form-control greenBorder" style="padding-right:0px;text-transform: capitalize;" <?php echo $hideIndia; ?> <?php echo $hideOther; ?>>
											<option value="">Select city</option>
										</select>
										<span id="city_loader"></span>
										<span id="cityError" class="error required"></span>
									</div>

									<div class="col-sm-6" style="display:none">
										<label for="centerPincode" class="control-label">Pin Code <span class="required"></span></label>
										<input type="text" class="form-control greenBorder" id="centerPincode" name="centerPincode" maxlength="7" value="<?php echo $address[0]['postal_code']; ?>">
									</div>
									<div class="col-sm-12 clear" style="display:none">&nbsp;</div>
									<div class="col-sm-6" style="display:none">
										<label for="centerAddress" class="control-label"> Address <span class="required"></span></label>
										<textarea class="form-control greenBorder" id="centerAddress" name="centerAddress" style="height:40px; resize: none;" spellcheck="false"><?php echo $address[0]['address_line1']; ?></textarea>
									</div>

									<div class="col-sm-12 clear" style="display:none">&nbsp;</div>


								</div>
								<div class="col-sm-12 clear"></div>
								<div class="text-center">
									<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
									<input type="hidden" name="uid" value="<?php echo (!empty($uid)) ? $uid : '' ?>" />
									<input type="hidden" name="aid" value="<?php echo (!empty($uid)) ? $userCenter->address_id : ''; ?>" />
									<input type="hidden" name="uid" value="<?php echo (!empty($uid)) ? $userCenter->user_id : ''; ?>" />
									<input type="hidden" name="ugid" value="<?php echo (!empty($uid)) ? $userCenter->user_group_id : ''; ?>" />
									<input type="hidden" id="cpFlag" value="0" />
								</div>
							</div>
						</div>
						<div class="text-right">
							<a href='centerAdminList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
							<button type="submit" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" class="btn btn-s-md btn-primary" onclick="return showLoaderOrNot('adminRegForm');" ondblclick="return showLoaderOrNot('adminRegForm');"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
						</div>
					</div>
				</form>
			</section>
		</div>
	</div>
</section>
<?php include_once('../footer/adminFooter.php'); ?>

<script type="text/javascript">
	var countryData = '<?php echo $countryData; ?>';
	var stateData = '<?php echo $stateData; ?>';
	var cityData = '<?php echo $cityData; ?>';
	var stateOther = '<?php echo $stateOther; ?>';
	var cityOther = '<?php echo $cityOther; ?>';
	if (countryData == 'India') {
		$("#other_state").hide();
		$("#other_city").hide();
		$("#state_dropdown").show();
		$("#city_dropdown").show();
	} else {
		$("#other_state").show();
		$("#other_city").show();
		$("#city_loader").hide();
		$("#state_loader").hide();
		$("#state_dropdown").hide();
		$("#city_dropdown").hide();

	}
	selectCity(countryData);
	selectState(stateData);


	function selectCity(country_id) {

		if (country_id != "-1") {
			loadData('state', country_id);
			$("#city_dropdown").html("<option value=''>Select city</option>");
		} else {
			$("#state_dropdown").html("<option value=''>Select state</option>");
			$("#city_dropdown").html("<option value=''>Select city</option>");
		}
	}

	function selectState(state_id) {
		if (state_id != "-1") {
			loadData('city', state_id);
		} else {
			$("#city_dropdown").html("<option value=''>Select city</option>");
		}
	}

	function loadData(loadType, loadId) {
		//alert(loadType))
		var dataString = 'loadType=' + loadType + '&loadId=' + loadId + '&stateData=' + stateData + '&cityData=' + cityData;
		$("#" + loadType + "_loader").show();
		//$("#"+loadType+"_loader").fadeIn(400).html('Please wait... <img src="image/loading.gif" />');
		$.ajax({
			type: "POST",
			url: "ajax/loadData.php",
			data: dataString,
			cache: false,
			success: function(result) {
				$("#" + loadType + "_loader").hide();
				$("#" + loadType + "_dropdown").html("<option value=''>Select " + loadType + "</option>");
				$("#" + loadType + "_dropdown").append(result);
			}
		});
	}


	function confSubmit() {

		var country = document.getElementById("country_dropdown");
		var countryVal = country.options[country.selectedIndex].value;
		if (countryVal == -1) {
			alert("Please select the country.");
			//document.getElementById("countryError").html("Please select the country.");
			return false;
		}
		//alert(countryVal);

		if (countryVal == 'India') {
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

		} else {

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


	function checkNumber(curVal, curId, targetId) {
		//$("#errorCenterName").html('');
		if (isNaN(curVal)) {
			// It isn't a number
			//alert('not');
			$("#" + targetId).html('');
		} else {
			//alert('yes');
			// It is a number
			$("#" + curId).val("");
			$("#" + targetId).html('This field accepts alpha-numeric value');
		}
	}

	function loadCountry(countryData) {
		var cValue = $("#country_dropdown :selected").val();
		var dataString = 'country=' + countryData;
		//$("#"+loadType+"_loader").show();
		$.ajax({
			type: "POST",
			url: "ajax/allCountry.php",
			data: dataString,
			cache: false,
			success: function(result) {
				//console.log(result)
				$("#country_dropdown").html(result);
				//$("#country_dropdown").html("<option value=''>"+result+"</option>");

			}
		});
	}
	loadCountry(countryData);

	function selectCountry(id, state, city) {

		var cValue = $("#" + id + " :selected").val();
		loadCountry(cValue);
		if (cValue == 'India') {
			$("#" + state).show();
			$("#" + city).show();
			$("#other_state").hide();
			$("#other_city").hide();
			selectCity(cValue);
			selectState(stateData);

		} else {
			$("#" + state).hide();
			$("#" + city).hide();
			$("#other_state").show();
			$("#other_city").show();
			if (cValue == countryData) {
				$("#other_state").val(stateOther);
				$("#other_city").val(cityOther);
			} else {
				$("#other_state").val('');
				$("#other_city").val('');
			}
		}
	}
	$(document).ready(function() {

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

	function addParsely(formId, old_password, pwd, cpwd) {
		//$('#'+formId).parsley('addItem', '#'+old_password);
		$('#' + formId).parsley('addItem', '#' + pwd);
		$('#' + formId).parsley('addItem', '#' + cpwd);
	}

	function removeParsely(formId, old_password, pwd, cpwd) {
		$('#' + formId).parsley('destroy');
		$('#' + formId).parsley();
		//$('#'+formId).parsley('removeItem',  '#'+old_password);
		$('#' + formId).parsley('removeItem', '#' + pwd);
		$('#' + formId).parsley('removeItem', '#' + cpwd);

	}

	function showHideChangePassword(path, targetDiv, formId, old_password, pwd, cpwd) {
		showLoader();
		$('#' + formId).parsley();
		var cpFlag = $("#cpFlag").val();
		if (cpFlag == 0) {
			$.post(path, {
				shpass: cpFlag
			}, function(data) {
				$("#" + targetDiv).html(data);
				$("#cpFlag").attr('value', '1');
				addParsely(formId, old_password, pwd, cpwd);
			});
			hideLoader();
		} else {
			removeParsely(formId, old_password, pwd, cpwd);
			$("#" + targetDiv).html('');
			$("#cpFlag").attr('value', '0');
			hideLoader();
		}
	}


	$(document).ready(function() {
		$(".chpassword").click(function() {
			$(".passwordIcon").toggleClass('fa-plus fa-minus')
		});
	})
</script>