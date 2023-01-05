<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
if(isset($_POST['center_code']) && $_POST['center_code'] != ''){
	$centerId = $_POST['center_code'];
	$useLicList= getLicenseDetailByCenter($centerId);
	//echo "<pre>";print_r($useLicList);exit;
	if(count($useLicList) > 0){

?>
	<thead  class="fixedHeader">
		<tr>
		    
			<th class="col-xs-2 text-left" style="padding-left: 30px;">License</th>
			<th class="col-xs-2 text-left hide">Action</td>
			<th class="col-xs-2 text-left">Expiry</td>
			<th class="col-xs-2 text-left"><?php echo $teacher; ?> Limit</th>
			<th class="col-xs-2 text-left"><?php echo $students; ?> Limit</th>
			<th class="col-xs-2 text-center">Status</th>
		</tr>
	</thead>
<?php	$j = 1;
         $action='';
		foreach($useLicList as $key => $licenseDetail){	 
		  if($licenseDetail->updated_date!=""){
			   $action="License Updated";
			}else{
				$action="Center Created";
			} 
		  
		
		  $used_date = $licenseDetail->used_date;
				$expiry_days = $licenseDetail->lic_exp_day_af_reg_lan;
				$expiry_date = $licenseDetail->lic_req_license_expiry_lan;
				
				if($used_date!="" &&  $used_date != '0000-00-00 00:00:00'){
					
					if($expiry_date!="" && $expiry_date != '0000-00-00'){

						$expiry_date = date('d-m-Y',strtotime($expiry_date));

					}else{
						$expiry_date = date('d-m-Y',strtotime($used_date . "+".$expiry_days." days"));

					}
				$used_date=date('d-m-Y',strtotime($licenseDetail->used_date));
					
				}else{
					$expiry_date = '-';
					$used_date='-';
				}
				
				if($licenseDetail->issued_date!=''){
					$issued_date=date('d-m-Y',strtotime($licenseDetail->issued_date));
				}
				else{$issued_date='-';}
				
				if($licenseDetail->license_used_by){
					/*$usedByDtl=$commonObj->getDatabyId('user','user_id',$licenseDetail->license_used_by);
					$license_used_by=$usedByDtl['first_name'].' '.$usedByDtl['last_name'];
					$usedBgcolor='style="background: #f5f1f1;"';*/
					$license_used_by=$licenseDetail->license_used_by;
				}
				else{$license_used_by='-';
				   $usedBgcolor="";
				}
				
				
				
				if($licenseDetail->license_created_date!=''){
					$license_created_date=date('d-m-Y',strtotime($licenseDetail->license_created_date));
				}
				else{$license_created_date='-';}


				if($licenseDetail->lic_exp_day_af_reg_lan!='')//Days
				{
					$expiryDateDay=$licenseDetail->lic_exp_day_af_reg_lan;
					$cDate = date('Y-m-d');
					$date1 = str_replace('-', '/', $cDate);
					$expiryDateDay1 = date('m-d-Y',strtotime($date1 . "+".$expiryDateDay." days"));
					$parts = explode('-', $expiryDateDay1);
					$expiryDateDay = $parts[1] . '-' . $parts[0] . '-' . $parts[2];
					
				}else{//Date
				
					$expiryDateDay=$licenseDetail->lic_req_license_expiry_lan;
					$parts = explode('-', $expiryDateDay);
					$expiryDateDay = $parts[1] . '-' . $parts[2] . '-' . $parts[0];
				} 

				if($licenseDetail->license_used_by_name!=''){
					$license_used_by=$licenseDetail->license_used_by_name;
				}
				else{
					$license_used_by='-';
					}
				if($licenseDetail->license_type!=''){
					$license_type=$licenseDetail->license_type;
				}
				else{
					$license_type='-';
					}
				
				if($licenseDetail->license_status==1 && $licenseDetail->used_date!='' && $licenseDetail->used_date!='0000-00-00 00:00:00'){
					$status='Active';$bgcolor="style='color:Green'";
				}
				elseif($licenseDetail->license_status==1 && ($licenseDetail->used_date=='' ||  $licenseDetail->used_date=='0000-00-00 00:00:00')){
					$status='Available';
					$bgcolor="style='color:#a96500'";
				}
				elseif($licenseDetail->license_status==1 && $licenseDetail->used_date!='' && $licenseDetail->used_date!=''){
					$status='Available';
					$bgcolor="style='color:#a96500'";
				} 
				elseif($licenseDetail->license_status==4){
					
					$status='Active';$bgcolor="style='color:Green'";
					if($expiry_date!='-'){
						$current_date = date('Y-m-d');
						$dateTimestamp1 = strtotime($current_date); 
						$dateTimestamp2 = strtotime($expiry_date); 
						if($dateTimestamp1 > $dateTimestamp2) {
							$status='Expired';
							$bgcolor="style='color:Red'";
						}
					}
					
				} 
				elseif($licenseDetail->license_status==0){
					
					$status='Expired';$bgcolor="style='color:Red'";
					/* if($expiry_date!='-'){
						$current_date = date('Y-m-d');
						$dateTimestamp1 = strtotime($current_date); 
						$dateTimestamp2 = strtotime($expiry_date); 
						if($dateTimestamp1 > $dateTimestamp2) {
							$status='Expired';
							$bgcolor="style='color:Red'";
						}
					} */
					
				} 
						
    ?>       
		<tr>
			 <td class="col-sm-2 text-left" style="padding-left: 30px;" <?php echo $usedBgcolor; ?>> <?php echo $licenseDetail->license_value;?></td>
					<td class="col-sm-2 text-left hide" <?php echo $usedBgcolor; ?>> <?php echo $action;?></td>
					<td class="col-sm-2 text-left" <?php echo $usedBgcolor; ?>> <?php echo $expiry_date;?></td>
				  <td class="col-sm-2 text-left"><?php echo $licenseDetail->trainer_limit;?></td>
				  <td class="col-sm-2 text-left"><?php echo $licenseDetail->student_limit;?></td>
				   <td class="col-sm-2 text-center" <?php echo $bgcolor; ?>><?php echo $status;?> </td>
							
			
			</tr>
<?php	
		$j++;	
		}
	}else{
?>
<tr class="noRecordTr">
<td colspan="6" class="noRecord col-xs-12 text-center"><?php echo $noRecord; ?></td>
</tr>
<?php	
	}
}
?>