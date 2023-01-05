<?php include_once('../header/adminHeader.php');

$reportObj = new reportController();
$country_list_arr=$reportObj->getCountryList();
//Showing message after submit
$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = $region." not saved. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = $region." created successfully.";
	}
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '2'){
		$msg = $region." updated successfully.";
	}
	
}
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	
		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
		//$msg = $_SESSION['msg'];
		$succ = $_SESSION['succ'];
	    unset($_SESSION['msg']);
	    unset($_SESSION['succ']);

	
}

$options = array();
$options['client_id'] = $client_id; 


$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'region_name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';

switch(strtoupper($dir)){
	case 'DESC': 
		$dir = 'ASC'; 
		break;
	case 'ASC': 
		$dir = 'DESC'; 
		break;
	default: 
		$dir = 'DESC'; 
		break;
}


$page_param='';

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";

$country='';
$region_id='';


if (!empty($_REQUEST['country'])) {
    $country = trim(filter_query($_REQUEST['country']));
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $batch_id;
	$page_param .= "region_id=$region_id&";
}


$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);
$response_result = $centerObj->getRegionList($options,$objPage->_db_start, $_limit,$order,$dir);

$objPage->_total = $response_result['total']; 
$regionInfoArr = $response_result['result'];



?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"><?php echo $language[$_SESSION['language']]['centres']; ?></div>
	<div class="col-md-6 col-sm-6 text-right"><a href='createRegion.php' class="btn btn-primary "><?php echo $language[$_SESSION['language']]['add_centre']; ?></a> </div>
 </div>
<section class="padder">
	<form id="serachform" name="serachform"  method = "GET"  class="form-horizontal form-centerReg" action="regionList.php" >
	<section class="marginBottom5 serachformDiv">
       <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">
			
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left ">
				
				
					
			   </div>	
		  </div>		
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right text-right padd0">
				<button type="submit" name="Submit" class="btn btn-red hide" id="btnSave" style="margin-top:0px"> Search</button> 
				
		 </div>
	</form>
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
  </section>	

   <div class="clear"></div>

  <section class="panel panel-default">
   
		<div class="panel-body">
    
		<?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
	    <div class="table-responsive">
	    <table class="table table-border dataTable table-fixed">
	    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-4"><a href="regionList.php?sort=region_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['region_name']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'region_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'region_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			   <!--<th class="col-sm-3"><?php echo $language[$_SESSION['language']]['country_name']; ?></th>-->
			   <th class="col-sm-4"><?php echo $language[$_SESSION['language']]['description']; ?></th>

			  <th class="col-sm-4 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>
			  </tr>
			</thead>
	  
		<tbody> 
		<?php $i=1;
		$regionInfoArr=array_filter($regionInfoArr);
        
		foreach($regionInfoArr as $key => $value){
		  	$region_id=$value['id'];
			$regionName=$value['region_name'];
			$countryListMapArr=$centerObj->getRegionCountryMapById($region_id);
			
			 $countryListMapList=array();
			 foreach($countryListMapArr as $key => $value1){
				 $countryListMapList[]=$value1['country_name'];
			 }
			 $countryListMap=implode(', ',$countryListMapList);

			 $desc = $value['region_description'] != '' ? $value['region_description'] : '-';
			
		?>
			 <tr id="row<?php echo $i;?>" class="col-sm-12 padd0" bid="<?php echo $value['id']; ?>">
			  <td class="col-sm-4"><?php echo $value['region_name'];?></td>
			    <!--<td class="col-sm-4"><?php echo $countryListMap;?></td>-->
			    <td class="col-sm-4"><?php echo $desc;?></td>
			  <td class="col-sm-4 text-center">
				<a class="edit" href="<?php echo "createRegion.php?rid=".base64_encode($value['id']); ?>"> <i class="fa fa-edit"></i> <?php echo $language[$_SESSION['language']]['edit']; ?></a></td>
			  </tr>
	   <?php  $i++; } ?>
	   <tr>
			<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td>
			</tr>
			<?php } else{   ?>
			 
			<div class="col-xs-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?> <br>Click <span class="capitalize">"<?php echo $language[$_SESSION['language']]['add_centre'];?>"</span> to  <span class="textLower"><?php echo $language[$_SESSION['language']]['add_centre'];?></span>.</div>
		</tbody>
		<?php 	} ?>
	   
	</table>
	 </div>

</section>
<?php include_once('../footer/adminFooter.php');?>
<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>
<script>
//On country chnage
 $('#country').change(function(){
	
	var country = $('#country option:selected').val();
	$('#Region_id').html('<option value=""></option>');
	if(country==''){
			$('#center_id').find('option').remove().end().append('<option value="">Select </option>');
		}else{
	$.post('ajax/getCenterByCountry.php', {country: country}, function(data){ 
			if(data!=''){
				  $('#center_id').html(data);
				}else{
					$('#center_id').html('<option value="">Not Available</option>');
				}
		}); }
}); 


</script>