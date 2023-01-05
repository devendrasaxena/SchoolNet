<?php 
  include_once('../header/adminHeader.php');
 $msg='';
 $err='';

if(isset($_SESSION['err']) && $_SESSION['err'] != ''){
	if($_SESSION['err'] == '1'){
      $msg= 'Webinar could not be created.';
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ''){
	if($_SESSION['succ'] == '1'){
      $msg= 'Webinar Created Successfully!';
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

$webinar=getWebinarListByClient($client_id);
//echo "<pre>";print_r($webinar);//exit;

?>
 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">Live Classes</div>
		<div class="col-md-6 col-sm-6 text-right hide"> 
		<a  class="btn" id="btnBuildCourse" name="btnBuildCourse" href="addWebinar.php">  <i class="build fa fa fa-file-text-o"></i> Build New Event</a></div>
 </div>
 <div class="clear"></div>
 <section class="padder"> 
   <?php  if(count($centers_arr) > 0){?>
    <section class="marginBottom5" style="height:80px;">
		 <div class="col-md-4 text-left">
			 <form role="form" method = "POST"  id="centerForm" class="form-horizontal form-centerReg" data-validate="parsley" >
				 <select id="center" name="center" onchange="selectCenter(this);" class="form-control " style="width:200px;">
                    <option value="">Select <?php echo $center; ?></option>
					<?php 
					 foreach ($centers_arr as $key => $value) {	
					  $centerName= $centers_arr[$key]['name'];
					  $centerId= $centers_arr[$key]['center_id']; 
					 // echo B2C_CENTER;
					  //if(B2C_CENTER!=$centerId){?>
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>	
					  <?php }
					  // } ?>
                   </select>
				</form>
			 </div>
			 <div class="col-md-8 padd0"></div>
	</section>	
   <div class="clear"></div>	
   <?php  }?>
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
                      <th class="col-xs-2">Name </th>
					  <th class="col-xs-2 text-left">Presenter</th>
                      <th class="col-xs-2 text-left">Date</th>
                      <th class="col-xs-2 text-left">Scheduled Time</th>
                      <th class='col-xs-1 text-left'>Duration</th>
                      <th class='col-xs-2 text-left'>Seats Availability</th>
                      <th class='col-xs-1 text-left'>Action</th>
                    
                    </tr>
                  </thead> 
                  <tbody>
                   <?php
				   	$i = 1; 
					foreach($webinar as $key => $value){
							$datetime=$value->event_date;
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
						?>
						<tr id="rowId<?php echo $i; ?>">
						<td class="col-xs-2 text-left" title="<?php echo $title;  ?>"><?php echo $title;  ?></td>
						  <td class="col-xs-2 text-left"><?php echo $teacher_name;?></td>
						  <td class="col-xs-2 text-left"><?php echo $newDateFormat;?></td>
						  <td class="col-xs-2 text-left"><?php echo $strDate[1];?></td>
						  <td class="col-xs-1 text-left"><?php echo $value->duration_minutes;?></td>
						  <td class="col-xs-2 text-left"><?php echo $value->num_seats_avail." / ".$value->num_seats_total;?></td>
						  <td class="col-xs-1 text-left" style="cursor:pointer;" >
						  <a href="javascript:void(0);" title="View" class="text-center" style="padding-left:5px" onclick="viewWebinar('<?php echo $value->trainer_url?>');"><i class="fa fa-desktop icon"></i></a></td>
					   
						</tr>
						<?php
						$i++;
						}

				   ?>
                  </tbody>
                </table>
             <?php }else{?>
						
					<div style="height:280px;padding-top:80px;text-align:center">Please click to "Build New Event" button for add live classes</div>	
					
			 <?php  } ?>
                 </div>
           
			</div>
		</div>
	</div>
	</section>
 </section>
 <!-- footer-->
<?php include '../footer/adminFooter.php'; ?>
        
<script>
function editCourse(url){
	if(url == ""){
		return false;
	}else{
		window.location.href = url;
	}
}

function editWebinar(cid)
{
document.location.href="editWebinar.php?cid="+cid;
}

function deleteWebinar(wid,cid)
{
document.location.href="../../techwiz/deleteClass.php?wid="+wid+"&cid="+cid;
}

function viewWebinar(url){
 window.open(url);
}
</script>
