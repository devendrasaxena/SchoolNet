<?php
include_once('../../header/lib.php');
$msgnew ='';
$msgold ='';
?> <div class="divider"></div>
				
  <div class="col-sm-12 col-xs-12  paddLeft0">
  <label > New license <span class="required">*</span></label>
   <input type = "text" name="new_license_key" id="new_license_key" placeholder="" class="form-control input-lg " value=""  data-required="true" data-minlength="[10]" data-maxlength="[10]" maxlength = "10"  autocomplete="pwd" placeholder="Enter new license"/>
  <label class="" style="font-size: 12px;margin-top:5px"></label>
  <label class="required showErr" id="newLicenseError"></label>

</div>

<div class="clear"></div>