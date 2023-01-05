<?php 
include_once ('../../header/lib.php'); 
include_once ('../../header/global.php'); 
$_html_relative_path='../../';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
$centerDetail=$adminObj->getCenterDetails();
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];

$licenseList=$clientObj->getLicenseListByCenter($customer_id,$center_id);	
//echo "<pre>";print_r($licenseList);exit;
?>
<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title>Licenses</title>
  <meta name="description" content="" />
    <link rel="shortcut icon" href="<?php echo $_html_relative_path; ?>images/favicon.ico" type="image/vnd.microsoft.icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/animate.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/font-awesome.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/font.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/app.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $_html_relative_path;?>css/common1.css" type="text/css" />
  <!--css theme -->
  <?php include_once($_html_relative_path.'css/theme.php');?>
  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
   <script src="<?php echo $_html_relative_path;?>js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="<?php echo $_html_relative_path;?>js/bootstrap.js"></script>
<body class="overFlowHidden">
	<div id="preLoaderPage" class="preloadBg" >
		<div id="overlayBlur"></div>
		<div class="loadDiv">
			<img src="<?php echo $_html_relative_path;?>images/default.svg" class="loadImg text-center"/>
			<div class="loadText">Please wait<span>.</span><span>.</span><span>.</span>
			</div>
		</div>
	</div>
	<div id="loaderDiv" class="loadBg">
	  <div id="overlayBlur"></div>
	  <div class="loadDiv"> <img src="<?php echo $_html_relative_path;?>images/default.svg" class="loadImg text-center"/>
		<div class="loadText">Please wait<span>.</span><span>.</span><span>.</span> </div>
	  </div>
	</div>

  <section class="vbox">
    <header class="bg-dark dk header navbar navbar-fixed-top-xs">
      <div class="navbar-header">
        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
          <i class="fa fa-bars"></i>
        </a>
        <a class="navbar-brand cursorDeafult"><img src="../<?php echo $systemLogo; ?>" class="m-r-sm">
		Licenses</a>
        <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
          <i class="fa fa-cog"></i>
        </a>
      </div>
    </header>
    <section class="container padd15">
      <section class="hbox stretch">
<div class="col-sm-12">  
	 <header class="panel-heading font-bold b-light" style="overflow: auto;">
		   <form role="form" method = "POST"  id="centerForm" class="form-horizontal form-centerReg" data-validate="parsley" >
		   <div class="col-md-3"> </div>
		<div class="col-md-3 padd0"> 
		<label class="lineHeight30 paddRight10 control-label">Issued Date: </label>
		<div id="divDate1" class="input-append date">
					  <input  data-date-format="DD-MM-YYYY" name="issuedDate" value=""  id="issuedDate" placeholder="DD-MM-YYYY" class="form-control" onchange="selectCenter(this)"  readonly="true" autocomplete="off" />
					  	<span class="calendarBg add-on margincon">
					   <i class="fa fa-calendar"></i>
					  </span></div>   
		</div>
		
	<div class="col-md-2">
			
			 <label class="lineHeight30 paddRight10 control-label">Select By Used/Unused: </label>
			  
				 <select id="usedType" name="usedType" onchange="selectCenter(this);" class="form-control " >
				 <!-- <option value="">All</option>-->
                
                      <option value="" selected>All</option>
                      <option value="1">Used</option>
                      <option value="2">Unused</option>
             
					</select>
				
			 </div>	
			 
		<div class="col-md-3 padd0"> 
		<label class="lineHeight30 paddRight10 control-label">Used Date: </label>
		<div id="divDate2" class="input-append date">
					  <input  data-date-format="DD-MM-YYYY" name="usedDate" value=""  id="usedDate" placeholder="DD-MM-YYYY" class="form-control" onchange="selectCenter(this)"  readonly="true" autocomplete="off" />
					  	<span class="calendarBg add-on margincon">
					   <i class="fa fa-calendar"></i>
					  </span></div>   
			</div>
		
		
		<div class="col-md-1 ">
		 <label class="lineHeight30 paddRight10 control-label">&nbsp; </label>
		<button type="button" name="btnCancel" id="btnCancel" class="btn btn-primary" onclick="resetForm();" >Reset</button>
		</div>	
			</form>
		 </header> 
	
	
	<div class="clear"></div>
	
<section class="panel panel-default ">
	<div class="panel-body marginBottom10">
    <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-4 text-left">Licenses</th>
			  <th class="col-sm-2 text-center">Issued Date</th>
			  <th class="col-sm-3 text-center">Used By</th>
			  <th class="col-sm-3 text-center">Used Date</th>
			  </tr>
			</thead>

<?php if(count($licenseList)>0)	{?>
<tbody id="tbodyRecord">
<?php 
	foreach($licenseList as $key=>$licenseDetail){

			if($licenseDetail->issued_date!=''){
				$issued_date=date('d-m-Y',strtotime($licenseDetail->issued_date));
			}
			else{$issued_date='-';}
			
			if($licenseDetail->license_used_by){
				$usedByDtl=$commonObj->getDatabyId('user','user_id',$licenseDetail->license_used_by);
				$license_used_by=$usedByDtl['first_name'].' '.$usedByDtl['last_name'];
				$usedBgcolor='style="background: #f5f1f1;"';
			}
			else{$license_used_by='-';
			   $usedBgcolor="";
			}
			
			if($licenseDetail->used_date!=''){
				$used_date=date('d-m-Y',strtotime($licenseDetail->used_date));
			}
			else{$used_date='-';}
		
		?>
		<tr class="col-sm-12 padd0" >
				
				  <td class="col-sm-4 text-left" <?php echo $usedBgcolor; ?>> <?php echo $licenseDetail->license_value;?></td>
				  <td class="col-sm-2 text-center" <?php echo $usedBgcolor; ?>><?php echo $issued_date;?> </td>
				  <td class="col-sm-3 text-center" <?php echo $usedBgcolor; ?>><?php echo $license_used_by;?> </td>
				  <td class="col-sm-3 text-center" <?php echo $usedBgcolor; ?>> <?php echo $used_date;?></td>
				
				 </tr>
	<?php } ?>
 </tbody>
 <?php
 	}
	
	else{ ?>
			 
<tbody id="tbodyNoRecord" style="display:none">
	<tr class="col-sm-12 padd0" > 
	 <td class="col-sm-11 text-center"> Records not available. </td>
	</tr>
</tbody>
 <?php 	} ?>
	
	 </table>
	</div>
	</div>
	
</section>
</div>	


<?php include('../../footer/playerFooter.php'); ?>
<script>
function selectCenter(e){
	showLoader();
	$("#tbodyRecord").html('');
	var html='';
	var j;
	var issuedDate=$("#issuedDate").val();
	var usedDate=$("#usedDate").val();
	var usedType=$("#usedType").val();
	
	console.log(issuedDate);
	console.log(usedDate);
	console.log(usedType);
	$.ajax({
      type: 'POST',
      url: "getLicense.php",
	data: {issuedDate:issuedDate,usedDate:usedDate,usedType:usedType},
      dataType: "text",
      success: function(res) { 
	  
		console.log(res);
		$("#tbodyRecord").html('');
		var data = JSON.parse(res);
		 console.log(data);
		 var myArray=data.result;
		 if(myArray.length>0){
		 for(let i = 0; i < myArray.length; i++){
			j=i+1;
			html=html+'<tr class="col-sm-12 padd0" ><td class="col-sm-4 text-left">'+myArray[i].license_value+'</td><td class="col-sm-2 text-center">'+myArray[i].issued_date+'</td><td class="col-sm-3 text-center">'+myArray[i].license_used_by+'</td><td class="col-sm-3 text-center">'+myArray[i].used_date+'</td></tr>';

			}
		 }
		 else{
			html=html+'<tr class="col-sm-12 padd0" ><td class="col-sm-11 text-center">No records available</td></tr>';
		 }
		 $("#tbodyRecord").append(html);
		
	   hideLoader();
	  }
  });

}

$(function () {
  $("#divDate1").datepicker({ 
    autoclose: true, 
    todayHighlight: true,
    format: 'dd-mm-yyyy',
  }); 
});

$(function () {
  $("#divDate2").datepicker({ 
    autoclose: true, 
    todayHighlight: true,
    format: 'dd-mm-yyyy',
  }); 
});

function resetForm(){
	document.getElementById("centerForm").reset();
	selectCenter();
}
</script>