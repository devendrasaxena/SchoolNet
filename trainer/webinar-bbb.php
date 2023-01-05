<?php 
include_once('../header/trainerHeader.php');
include_once('../controller/webinarControllerBBB.php');

 $msg='';
 $err='';

if(isset($_SESSION['err']) && $_SESSION['err'] != ''){
	if($_SESSION['err'] == '1'){
      $msg= 'Webinar could not be created.';
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ''){
	if($_SESSION['succ'] == '1'){
      $msg= 'Webinar created Successfully!';
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ''){
	if($_SESSION['succ'] == '2'){
      $msg= 'Webinar updated Successfully!';
	}
}

if(isset($_SESSION['succ']) && $_SESSION['succ'] != ''){
	if($_SESSION['succ'] == '3'){
      $msg= 'Webinar Canceled Successfully!';
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
		 $succ=$_SESSION['succ'];
		unset($_SESSION['succ']);
	
}
if(isset($_SESSION['err']) && $_SESSION['err'] != ""){
        $err=$_SESSION['err'];
		unset($_SESSION['err']);
	
}


$webinar=getWebinarBBBListByUserId($client_id,$center_id,$userId);
//echo "<pre>";print_r($webinar);exit;


?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"><?php echo $language[$_SESSION['language']]['live']; ?> <?php echo $language[$_SESSION['language']]['session']; ?></div>
	
		<div class="col-md-6 col-sm-6 text-right"> 
			<a title="<?php echo $language[$_SESSION['language']]['build_new_event']; ?>" class="btn" id="btnBuildCourse" name="btnBuildCourse" href="addWebinar-bbb.php">  <i class="build fa fa fa-file-text-o"></i> <?php echo $language[$_SESSION['language']]['build_new_event']; ?></a>
		
 </div>
 </div>
 <div class="clear"></div>
 <div class="divider" style='text-align:center;padding-bottom:10px;color:red;'>
	<?php
				if(isset($_REQUEST['err']))
				{
				echo $errorCode[$_REQUEST['err']];
				}
				?>
	</div>
 <section class="padder"> 
   <section class="panel panel-default marginBottom5">
   
		<div class="row m-l-none m-r-none bg-light lter">
		<div id="divAdmin" >
		<div class="clear"></div>
		
            <div class="form-group  col-sm-12 col-centered paddTop5" style="margin-top:10px;">
              <?php if( $succ == '1' ){?>
                 <div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
               <?php }?>
		   <?php if( $err== '1' ){ ?>
               <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
               
            </div>
		  <div class="col-sm-12 col-md-12 padder-v  b-r b-light text-center">
				<!--Responsive grid table -->
                <div class="table-responsive promo courseGroup">
               <?php  if(count($webinar) > 0){?>
                <table class="table m-b-none dataTable">
                 <thead  class="fixedHeader">
                    <tr>
                      <th class="col-xs-3" title="<?php echo $language[$_SESSION['language']]['name']; ?>"><?php echo $language[$_SESSION['language']]['name']; ?> </th>
					  <th class="col-xs-2 text-left" title="<?php echo $language[$_SESSION['language']]['presenter']; ?>"><?php echo $language[$_SESSION['language']]['presenter']; ?></th>
                      <th class="col-xs-1 text-left" title="<?php echo $language[$_SESSION['language']]['date']; ?>"><?php echo $language[$_SESSION['language']]['date']; ?></th>
                      <th class="col-xs-2 text-center" title="<?php echo $language[$_SESSION['language']]['scheduled_time']; ?>"><?php echo $language[$_SESSION['language']]['scheduled_time']; ?></th>
                      <th class='col-xs-1 text-center' title="<?php echo $language[$_SESSION['language']]['duration']; ?>"><?php echo $language[$_SESSION['language']]['duration']; ?></th>
                     <!-- <th class='col-xs-1 text-left'>Seats Availability</th>-->
                      <th class='col-xs-1 text-center' title="<?php echo $language[$_SESSION['language']]['launch']; ?>"><?php echo $language[$_SESSION['language']]['launch']; ?></th>
					   <th class='col-xs-1 text-center' title="<?php echo $language[$_SESSION['language']]['edit']; ?>"><?php echo $language[$_SESSION['language']]['edit']; ?></th>
					 <th class='col-xs-1 text-center' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>"><?php echo $language[$_SESSION['language']]['cancel']; ?></th>
                    
                    </tr>
                  </thead> 
                  <tbody>
                   <?php
				   	$i = 1; 
					$today = date("Y-m-d H:i:s");
					foreach($webinar as $key => $value){
							$event_id=$value->id;
							$class_id=$value->class_id;
							$datetime=$value->event_date;
							$timezone=$value->event_timezone;
							$strDate=explode(" ",$datetime);
							$dtFormat=explode("-",$strDate[0]);
							$newDateFormat=$dtFormat[2]."-".$dtFormat[1]."-".$dtFormat[0];
							$title=(empty($value->title))?"NA":$value->title; 
							$tchInfoArr= userdetails($value->teacher_user_id);
							$teacher_name=$tchInfoArr->first_name;
							$lastName=$tchInfoArr->last_name;
							if($lastName!=''){
							$full_name=$tchInfoArr->first_name.' '.$tchInfoArr->last_name;}
							else{$full_name=$tchInfoArr->first_name;}
							//echo "<pre>";print_r($tchInfoArr);
							$arr_event_date=explode(" ",$datetime);
							$arrNewDate=explode("-",$arr_event_date[0]);
							$newDate=$arrNewDate[2]."-".$arrNewDate[1]."-".$arrNewDate[0];
							$arrNewTime=explode(":",$arr_event_date[1]);
							$newTime=$arrNewTime[0].":".$arrNewTime[1];
							//echo $newTime."<br>";

							$tz = new DateTimeZone($timezone);
							$date = new DateTime($newDate.' '.$newTime.' '.$timezone);
							$date->setTimezone($tz);


							//$current_date = new DateTime("now", $timezone );
							$current_date = new DateTime("now", new DateTimeZone($timezone) );
						
							$finalEventDate=$date->format('Y-m-d H:i:s');
							$event_duration_in_min="+".$value->duration_minutes."minutes";
							$finalEventDate=date('Y-m-d H:i:s',strtotime($event_duration_in_min,strtotime($finalEventDate)));
							$finalCurrentDate=$current_date->format('Y-m-d H:i:s');
							
						?>
						<tr id="rowId<?php echo $i; ?>">
						<td class="col-xs-3 text-left" title="<?php echo $title;  ?>"><?php echo $title;  ?></td>
						  <td class="col-xs-2 text-left"><?php echo $teacher_name;?></td>
						  <td class="col-xs-1 text-left"><?php echo $date->format('d-m-Y'); ?></td>
						  <td class="col-xs-2 text-center"><?php echo $date->format('H:i:s'); ?></td>
						  <td class="col-xs-1 text-center"><?php echo $value->duration_minutes;?></td>
						  <!--<td class="col-xs-1 text-left"><?php echo $value->num_seats_avail." / ".$value->num_seats_total;?></td>-->
						  <?php if($finalEventDate > $finalCurrentDate){
							  $disabled="";
							
							 ?>
						  <td class="col-xs-1 text-center">
						  
							<a href="javascript:void(0);" style="cursor:pointer;"  title="<?php echo $language[$_SESSION['language']]['launch_live_session']; ?>" class="text-center blue <?php echo $disabled;?>" style="padding-left:5px" onclick="viewWebinar('<?php echo $value->trainer_url?>');"><i class="fa fa-desktop icon"></a></td>
							
						    </td><td class="col-xs-1 text-center" >
						   
						  <a href="editWebinar.php?cid=<?php echo base64_encode($event_id);?>" title="<?php echo $language[$_SESSION['language']]['edit_live_session']; ?>" style="cursor:pointer;" class="text-center <?php echo $disabled;?>" style="padding-left:5px;"><i style="font-size:15px;" class="fa fa-edit icon"></i></a>
						  
							<!-- <a href="#" title="Edit Class" style="cursor:pointer;" class="text-center disabled" style="padding-left:5px;"><i style="font-size:15px;" class="fa fa-edit icon"></i></a>-->
							
							</td>
						<td class="col-xs-1 text-center" >
							
						  <a href="javascript:void(0);" title="<?php echo $language[$_SESSION['language']]['cancel_live_session']; ?>" style="cursor:pointer;" class="text-center <?php echo $disabled;?>" style="padding-left:5px;" onclick="CancelWebinar('<?php echo base64_encode($event_id);?>','<?php echo $_SESSION['role_id']; ?>');"><i style="font-size:15px;" class="fa fa-times-circle icon"></i></a>
						 
							
							 <!--<a href="#" title="Cancel Class" style="cursor:pointer;" class="text-center disabled" style="padding-left:5px;"><i style="font-size:15px;" class="fa fa-times-circle icon"></i></a>-->

							
							</td>
						   <?php }else{
								 $disabled="disabled";
								 
							?> 
							<td class="col-xs-1 text-center" >
						  
							<a href="javascript:void(0);" style="cursor:pointer;color: #c0c0c0 !important; opacity: .5;"  title="<?php echo $language[$_SESSION['language']]['launch_live_session']; ?>" class="text-center blue" style="padding-left:5px" onclick="expiryEvent()"><i class="fa fa-desktop icon"></a></td>
							
						    </td><td class="col-xs-1 text-center" >
						   
						  <a href="javascript:void(0);" title="<?php echo $language[$_SESSION['language']]['edit_live_session']; ?>" style="cursor:pointer;color: #c0c0c0 !important; opacity: .5" class="text-center " style="padding-left:5px;"  onclick="expiryEvent()"><i style="font-size:15px;" class="fa fa-edit icon"></i></a>
						
							</td>
						<td class="col-xs-1 text-center" >
							
						  <!--a href="javascript:void(0);" title="<?php echo $language[$_SESSION['language']]['cancel_live_session']; ?>" style="cursor:pointer;" class="text-center" style="padding-left:5px;color: #c0c0c0 !important; opacity: .5;"  onclick="expiryEvent()"><i style="font-size:15px;color: #c0c0c0 " class="fa fa-times-circle icon"></i></a-->
						 
							</td> 
							<!--<td class="col-xs-1 text-left" >
							
						  <a href="javascript:void(0);" onclick="recordWebinar('<?php echo base64_encode($class_id);?>','<?php echo $_SESSION['role_id']; ?>');"title=" <?php echo $batch ?> Recording Link" style="cursor:pointer;" class="text-center" style="padding-left:5px;color: #c0c0c0 !important; opacity: .5;"><i style="font-size:15px;color: #c0c0c0 " class="fa fa-headphones icon"></i></a>
						 
					    </td> -->	 
						<?php   }?>
						   
					   
						
						
							</tr>	 
						
						<?php
						$i++;
						}

				   ?>
                  </tbody>
                </table>
             <?php }else{?>
						
					<div style="height:280px;padding-top:80px;text-align:center"><?php echo $language[$_SESSION['language']]['for_live_class']; ?></div>	
					
			 <?php  } ?>
                 </div>
           
			</div>
		</div>
	</div>
	</section>
 </section>
 <!-- footer-->
<?php include_once('../footer/trainerFooter.php');?>
        
<script>
function editCourse(url){
	if(url == ""){
		return false;
	}else{
		window.location.href = url;
	}
}

function editWebinar(cid){
document.location.href="editWebinar.php?cid="+cid;
}

function CancelWebinar(cid,user_role_id){
	//alert(cid);
	if(confirm("Are you sure you want to cancel this <?php echo $batch;?>?"))
    {
	   document.location.href="../zoom/delete-meeting.php?meeting_id="+cid+"&user_role_id="+user_role_id;
	}
}
function recordWebinar(cid,user_role_id){
	//alert(cid);
	//if(confirm("Are you sure you want to cancel this <?php echo $batch;?>?")){
	   document.location.href="../zoom/past-meetings.php?meeting_id="+cid+"&user_role_id="+user_role_id;
	//}
}


function viewWebinar(url){
 
  /*$.ajax({
  	url: "../bigBlueButton/join.php",
  	data:{join_url:url},
  	type:"post",
   success: function(res){
    
    if(!res.status)
    	alert(res.message);
    else
    	console.log(res);
    	//window.open(res.data.url, '_blank');
  }
});*/

window.open(url, '_blank');
}

function expiryEvent(){
	
	alertPopup("The event has expired. This operation can not be performed.")
}

</script>
