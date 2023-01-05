<?php include_once('../header/adminHeader.php');
$centerObj = new centerController();
$reportObj = new reportController();
$region_arr = $centerObj->getRegionDetails();
$tchRowsData = '';
$msg = '';
$err = '';
$succ = '';

if (isset($_SESSION['error']) && $_SESSION['error'] != "") {
	if ($_SESSION['error'] == '1') {
		$msg = "$center admin not saved. Please try again.";
	}
}
if (isset($_SESSION['succ']) && $_SESSION['succ'] != "") {

	if ($_SESSION['succ'] == '1') {
		$msg = $center_admin." created successfully.";
	}
	if ($_SESSION['succ'] == '2') {
		$msg = $center_admin." updated successfully.";
	}
}
if (isset($_SESSION['error']) && $_SESSION['error'] != "") {

	$msg = $_SESSION['msg'];
	$err = $_SESSION['error'];
	unset($_SESSION['msg']);
	unset($_SESSION['error']);
}
if (isset($_SESSION['succ']) && $_SESSION['succ'] != "") {

	//$msg = $_SESSION['msg'];
	$succ = $_SESSION['succ'];
	unset($_SESSION['msg']);
	unset($_SESSION['succ']);
}
$options = array();
$options['client_id'] = $client_id;
$options['role_id'] = 4;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] != "") ? filter_query($_GET['sort']) : 'u1.first_name';
$dir = (isset($_GET['dir']) && $_GET['dir'] != "") ? filter_query($_GET['dir']) : 'DESC';

switch (strtoupper($dir)) {
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


$page_param = '';

$page_param .= "sort=" . filter_query($_GET['sort']) . "&dir=" . filter_query($_GET['dir']) . "&";


$center_id = '';
$country = '';
$cadmin_txt = '';


if (!empty($_SESSION['region_id'])) {
	$options['region_id'] = $_SESSION['region_id'];
	$region_id = $_SESSION['region_id'];
	$country_list_arr = $reportObj->getCountryList($region_id);
	$options['region_id'] = $region_id;
	$country_list_arr = $reportObj->getCountryList($region_id);
} else if (!empty($_REQUEST['region_id'])) {
	$region_id = trim(filter_query($_REQUEST['region_id']));
	$country_list_arr = $reportObj->getCountryList($region_id);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
	$country_list_arr = $reportObj->getCountryList($region_id);
} else {
	$options['region_id'] = $region_id;
	$country_list_arr = $reportObj->getCountryList();
	$region_id = '';
}


if (!empty($_REQUEST['center_id'])) {
	$center_id = trim(filter_query($_REQUEST['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['country'])) {
	$country = trim(filter_query($_REQUEST['country']));
	$options['country'] = $country;
	$page_param .= "country=$country&";
}

if (!empty($_REQUEST['cadmin'])) {
	$cadmin_id = trim(filter_query($_REQUEST['cadmin']));
	$options['cadmin_id'] = $cadmin_id;
	$page_param .= "cadmin_id=$cadmin_id&";
}
if (!empty($_REQUEST['cadmin_txt'])) {
	$cadmin_txt = trim(filter_query($_REQUEST['cadmin_txt']));
	$options['cadmin_txt'] = $cadmin_txt;
	$page_param .= "cadmin_txt=$cadmin_txt&";
}



$_page = empty($_GET['page']) || !is_numeric($_GET['page']) ? 1 : filter_query($_GET['page']);

//$_limit = 20;
if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);
if (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export') {

	$response_result = $centerObj->getCenterAdminDetails($options, $objPage->_db_start, '', $order, $dir);
} else {
	$response_result = $centerObj->getCenterAdminDetails($options, $objPage->_db_start, $_limit, $order, $dir);
}

$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];
//echo $userId;
//print_r($users_arr);
?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left paddLeft0"><?php echo $center_admin; ?> </div>
	<div class="col-md-6 col-sm-6 text-right paddRight0"><span class="pull-right">
	<a href='createCenterAdmin.php' title="<?php echo $language[$_SESSION['language']]['add'].' '.$center_admin;?>" class="btn btn-primary marginTop0"><?php echo $language[$_SESSION['language']]['add'].' '.$center_admin;?> </a>
		</span> </div>
</div>
<div class="clear"></div>
<section class="padder">

	<form id="serachform" name="serachform" method="get" class="form-horizontal form-centerReg" data-validate="parsley"  action="centerAdminList.php">
		<section class="marginBottom5 serachformDiv">

			<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 <?php if ($_SESSION['role_id'] == 7) { ?> hide <?php } ?>">

					<select name="region_id" id="region" class="form-control ">
						<option value="">Select Centre</option>
						<option value="All" <?php if ($region_id == 'All') { ?> selected <?php } ?>>All</option>
						<?php
						foreach ($region_arr as $key => $value) {
							$regionName = $value['region_name'];

							if ($_SESSION['role_id'] == 7 && $_SESSION['region_id'] == $value['id']) {
								$selected = "selected";
							} elseif ($_REQUEST['region_id'] == $value['id']) {
								$selected = "selected";
							} else {
								$selected = "";
							}
						?>
							<option <?php echo $hide; ?> value="<?php echo $value['id']; ?>" <?php echo $selected; ?>><?php echo $regionName; ?></option>
						<?php
						} ?>
					</select>

				</div>
			<!--
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0">

					<select name="country" id="country" class="form-control ">
						<option value=""><?php echo $language[$_SESSION['language']]['select_country']; ?></option>
						<option value="All" <?php if ($country == 'All') { ?> selected <?php } ?>>All</option>
						<?php
						foreach ($country_list_arr as $key => $value) {
							$countryName = $country_list_arr[$key]['country_name'];

							if ($country == $countryName) {
								$selected = "selected";
							} else {
								$selected = "";
							}
						?>
							<option <?php echo $hide; ?> value="<?php echo $countryName; ?>" <?php echo $selected; ?>><?php echo $countryName; ?></option>
						<?php
						} ?>
					</select>

				</div>-->

				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-left paddLeft0 paddRight0">
					<select name="center_id" id="center_id" class="form-control ">
					   <?php $optiondisabled = ($center_id == 'All') ? "disabled" : ""; ?>
					   <option value="" <?php echo $optiondisabled; ?>><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
						if(count($center_list_arr_drop_down)>0){
						 $optionSelected = ($center_id == 'All') ? "selected" : "";
						 echo '<option value="All" '.$optionSelected.'>All</option>';
						 foreach($center_list_arr_drop_down  as $key => $value){
								$centerId=$center_list_arr_drop_down[$key]['center_id'];
								$center_name=$center_list_arr_drop_down[$key]['name'];
								$optionSelected = ($center_id == $centerId) ? "selected" : "";
								echo '<option   value="'.$centerId.'" '.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$center_name.'</option>';
									
						 }
						}
						

					   ?>
					</select>

				</div>


				<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5  text-left ">
					<div class="searchboxCSS search-box col-xs-10 padd0 pull-right">
						<input name="cadmin_txt" id="cadmin_txt" type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search']." ".$center_admin." ".$language[$_SESSION['language']]['name_or_email'];?>..." class="form-control  parsley-validated" <?php if ((isset($_REQUEST['cadmin_txt']) && $_REQUEST['cadmin_txt'] != "")) { ?> value="<?php echo filter_query($_REQUEST['cadmin_txt']); ?>" <?php } ?> />
						<input name="cadmin" id="cadmin_hidden" type="hidden" class="form-control  parsley-validated" <?php if ((isset($_REQUEST['cadmin']) && $_REQUEST['cadmin'] != "") && (isset($_REQUEST['cadmin_txt']) && $_REQUEST['cadmin_txt'] != "")) { ?> value="<?php echo filter_query($_REQUEST['cadmin']); ?>" <?php } ?> />
						<div class="result_list"></div>
					</div>

				</div>


			</div>

			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right text-right paddRight0">
				<button type="submit" name="Submit" title="<?php echo $language[$_SESSION['language']]['search']." ".$language[$_SESSION['language']]['state_admin']; ?>" class="btn btn-red" id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button>
			<a class="btn btn-sm btn-red" href="centerAdminList.php" name="refresh" title="<?php echo $language[$_SESSION['language']]['refresh']?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			</div>
			<br>
			<br>
			<label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
			 	<option value="10" <?php echo $_limit==10? 'selected':'' ?> >10 </option>
			 	<option value="25"<?php echo $_limit==25? 'selected':'' ?> >25 </option>
			 	<option value="50" <?php echo $_limit==50? 'selected':'' ?> >50 </option>
			 	<option value="100" <?php echo $_limit==100? 'selected':'' ?> >100 </option>
			 </select>
	
	<?php if ($succ == '1') { ?>
			<div class="alert alert-success col-sm-12">
				<button type="button" class="close" data-dismiss="alert">x</button>
				<i class="fa fa-ban-circle"></i><?php echo $msg; ?></div>
		<?php } ?>
		<?php if ($succ == '2') { ?>
			<div class="alert alert-success col-sm-12">
				<button type="button" class="close" data-dismiss="alert">x</button>
				<i class="fa fa-ban-circle"></i> <?php echo $msg; ?> </div>
		<?php } ?>
		<?php if ($err == '1') { ?>
			<div class="alert alert-danger col-sm-12">
				<button type="button" class="close" data-dismiss="alert">x</button>
				<i class="fa fa-ban-circle"></i><?php echo $msg; ?> </div>
		<?php } ?>
</section></form>
	<div class="clear"></div>
	<section class="panel panel-default">
	
		<div class="panel-body">
			<?php if ($objPage->_total > 0) {
				$no = ($_page - 1) * $_limit + 1; ?>
				<div class="table-responsive">
					<table class="table table-border dataTable table-fixed">
						<thead class="fixedHeader">
							<tr class="col-sm-12 padd0">
								<th class="col-sm-4"><a href="centerAdminList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&cadmin=<?php echo $cadmin_id; ?>&cadmin_txt=<?php echo filter_query($_REQUEST['cadmin_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=first_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['name']; ?>
										<span class="th-sort">
											<?php
											if (isset($_GET['sort']) && $_GET['sort'] == 'first_name' && $_GET['dir'] == 'ASC') { ?>
												<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
											<?php } else if (isset($_GET['sort']) && $_GET['sort'] == 'first_name' && $_GET['dir'] == 'DESC') { ?>
												<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
											<?php } else { ?>
												<i class='fa fa-sort'></i>
											<?php } ?>
										</span></a></th>
								<th class="col-sm-4 text-left"><a href="centerAdminList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&cadmin=<?php echo $cadmin_id; ?>&cadmin_txt=<?php echo filter_query($_REQUEST['cadmin_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=email_id&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['login_id']; ?>
										<span class="th-sort">
											<?php
											if (isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir'] == 'ASC') { ?>
												<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
											<?php } else if (isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir'] == 'DESC') { ?>
												<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
											<?php } else { ?>
												<i class='fa fa-sort'></i>
											<?php } ?>
										</span></a></th>
								<th class="col-sm-2 text-left paddLeft0 paddRight0"><?php echo $language[$_SESSION['language']]['states']; ?>
								<a href="centerAdminList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&cadmin=<?php echo $cadmin_id; ?>&cadmin_txt=<?php echo filter_query($_REQUEST['cadmin_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=name&dir=<?php echo $dir; ?>" class="th-sortable"> 
										<span class="th-sort"> 
											<?php
											if (isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir'] == 'ASC') { ?>
												<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
											<?php } else if (isset($_GET['sort']) && $_GET['sort'] == 'name' && $_GET['dir'] == 'DESC') { ?>
												<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
											<?php } else { ?>
												<i class='fa fa-sort'></i>
											<?php } ?>
										</span></a></th>

								<th class="col-sm-2 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>


							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($users_arr  as $key => $value) {
								$first_name = $value->first_name;
								$last_name = $value->last_name;

								//echo "<pre>";print_r($users_arr);

							?>
								<tr class="col-sm-12 padd0" uid="<?php echo $values['user_id']; ?>">
									<td class="col-sm-4"><?php echo $value->first_name; ?></td>
									<td class="col-sm-4 text-left"><?php echo $value->email_id; ?></td>
									<td class="col-sm-2 text-left paddLeft0">
										<?php echo $value->center_name; ?></td>

									<td class="col-sm-2 text-center">
									<a title="<?php echo $language[$_SESSION['language']]['edit']?>" class="edit" href="<?php echo "createCenterAdmin.php?uid=" . base64_encode($value->user_id); ?>"> <i class="fa fa-edit"> </i> <?php echo $language[$_SESSION['language']]['edit']; ?></a></td>
								</tr>
							<?php } ?>
							<tr>
								<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param, 5, 'pagination'); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			<?php } else { ?>

				<div class="col-sm-12 noRecord text-center">Records not available.</div>

			<?php } ?>

		</div>
	</section>
</section>
<?php include_once('../footer/adminFooter.php'); ?>
<style>
	.th-sortable .th-sort {
		float: none;
		position: relative;
		margin-left: 2px;
	}
</style>

<script>
	//On region chnage
	$('#region').change(function() {
		var region = $('#region option:selected').val();
		$('#center_id').html('<option value="">Select Organization</option>');
		if (region == '') {
			$('#country').find('option').remove().end().append('<option value="">Select </option>');
		} else {
			$.post('ajax/getCountryByRegion.php', {
				region_id: region
			}, function(data) {
				if (data != '') {
					$('#country').html(data);
				} else {
					$('#country').html('<option value="">Not Available</option>');
				}
			});
		}
	});


	//On country chnage
	$('#country').change(function() {
		var country = $('#country option:selected').val();
		if (country == '') {
			$('#center_id').find('option').remove().end().append('<option value=""><?php echo $language[$_SESSION["language"]]["select_state"]; ?> </option>');
		} else {
			$.post('ajax/getCenterByCountry.php', {
				country: country
			}, function(data) {
				if (data != '') {
					$('#center_id').html(data);
				} else {
					$('#center_id').html('<option value="">Not Available</option>');
				}
			});
		}
	});
	
		$(document).ready(function() {
			$('.search-box input[type="text"]').on("keyup input", function() {
				/ Get input value on change /
				var inputVal = $(this).val();
				$('#cadmin_hidden').val('');
			//	$('#cadmin').val('');
				var region_id = $('#region').val();
				var center_id = $('#center_id option:selected').val();
				var country = $('#country option:selected').val();
				var resultDropdown = $(this).siblings(".result_list");
				if (inputVal.length && inputVal.length > 0) {
					$.post("ajax/search_center_admin.php", {
						uname: inputVal,
						center_id: center_id,
						country: country,
						region_id: region_id
					}).done(function(data) {
						// Display the returned data in browser
						resultDropdown.addClass("resultserchDiv");
						resultDropdown.html(data);
					});
				} else {
					resultDropdown.removeClass("resultserchDiv");
					resultDropdown.empty();
				}

			});

			// Set search input value on click of result_list item
			$(document).on("click", ".result_list option", function() {

				$(this).parents(".search-box").find('input[type="hidden"]').val($(this).val());
				$(this).parents(".search-box").find('input[type="text"]').val($(this).text());
				$(this).parent(".result_list").removeClass("resultserchDiv");
				$(this).parent(".result_list").empty();

			});
		});
</script>