<?php
include_once('../../header/lib.php');
$msgnew ='';
$msgold ='';
?> <div class="divider"  style="border-top:solid thin #ccc"></div>
			</br><div class="col-sm-4 padd0 hide">
				<div class="col-sm-12 col-xs-12  paddLeft0">
                  <label> Old Password <span class="required">*</span></label>
                  <input class="form-control input-lg" name="oldPassword" id="oldPassword" type="password"  maxlength="15"  placeholder="Enter old password"  autocomplete="off" data-minlength="[6]" data-maxlength="[15]" />
                  <label class="required showErr" id="oldPasswordError"><?php echo $msgold; ?></label>
                </div>
                </div>
                
				<div class="col-sm-6 padd0">
				  <div class="col-sm-12 col-xs-12  paddLeft0">
                  <label > <?php echo $language[$_SESSION['language']]['new_password']; ?> <span class="required">*</span></label>
				   <input type = "password" name="fld_password" id="newPassword"  class="form-control input-lg " value=""  data-required="true"  data-regexp="<?php echo $passRegexp;?>" data-regexp-message="<?php echo $passRegexpMsg;?>" maxlength="15" placeholder="<?php echo $language[$_SESSION['language']]['new_password']; ?>" autocomplete="nope"/>
				  <label class="" style="font-size: 12px;margin-top:5px" id="login_pass"> <?php echo $language[$_SESSION['language']]['the_password_must_have_8_or_more_characters,_at_least_one_uppercase_letter,_and_one_number']; ?> <span class="required"></label>
                  <label class="required showErr" id="newPasswordError"><?php echo $msgnew; ?></label>
                </div>
				
               </div>
                
                <div class="col-sm-6 padd0">
				 <div class="col-sm-12 col-xs-12   paddLeft0  paddRight0">
                 <label > <?php echo $language[$_SESSION['language']]['confirm_password']; ?> <span class="required">*</span></label>
                  <input class="form-control input-lg" name="cnfPassword" id="cnfPassword" type="password" data-required= "true"   data-equalto="#newPassword" maxlength="15" placeholder="<?php echo $language[$_SESSION['language']]['confirm_password']; ?>" autocomplete="nope"/>
                  <label class="required" id="cnfPasswordError"></label>
                </div>
              </div>
                <div class="clear"></div>