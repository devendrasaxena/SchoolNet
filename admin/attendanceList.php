<?php include_once('../header/adminHeader.php');
include_once('../controller/bulkUploadController.php');
$attendanceObj = new BulkUploadController();

$centerObj = new centerController();
$reportObj = new reportController();
//$region_arr = $centerObj->getRegionDetails();
$tchRowsData = '';
$msg = '';
$err = '';
$succ = '';


if (isset($_POST['attendance_id'])) {
	$id = $_POST['attendance_id'];
	if ($id != "") {
		$res = $attendanceObj->deleteAttendance($id);

		if ($res) {
			//header("");
		}
	}
}

if (isset($_SESSION['error']) && $_SESSION['error'] != "") {
	if ($_SESSION['error'] == '1') {
		$msg = "$student not saved. Please try again.";
	}
}
if (isset($_SESSION['succ']) && $_SESSION['succ'] != "") {

	if ($_SESSION['succ'] == '1') {
		$msg = "$student created successfully.";
	}
	if ($_SESSION['succ'] == '2') {
		$msg = "$student updated successfully.";
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
$options['role_id'] = 2;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] != "") ? filter_query($_GET['sort']) : 'upload_date';
$dir = (isset($_GET['dir']) && $_GET['dir'] != "") ? filter_query($_GET['dir']) : 'ASC';

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
$batch_id = '';

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

if (!empty($_REQUEST['batch_id'])) {
	$batch_id = trim(filter_query($_REQUEST['batch_id']));
	$options['batch_id'] = $batch_id;
	$page_param .= "batch_id=$batch_id&";
}
if (!empty($_REQUEST['student'])) {
	$student_id = trim(filter_query($_REQUEST['student']));
	$options['student_id'] = $student_id;
	$page_param .= "student_id=$student_id&";
} else if (!empty($_REQUEST['roll_no']) || $_REQUEST['roll_no'] == '0') {
	$roll_no = trim(filter_query($_REQUEST['roll_no']));
	$options['roll_no'] = $roll_no;
	$page_param .= "roll_no=$roll_no&";
}

if (!empty($_REQUEST['status']) || $_REQUEST['status'] == '0') {
	$status = trim(filter_query($_REQUEST['status']));
	$options['status'] = $status;
	$page_param .= "status=$status&";
}


$_page = empty($_GET['page']) || !is_numeric($_GET['page']) ? 1 : filter_query($_GET['page']);

//$_limit = 20;
if (isset($_REQUEST['limit']))
	$_limit = intval($_REQUEST['limit']);
else
	$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export') {

	$response_result = $attendanceObj->getAttendance($options, $objPage->_db_start, '', $order, $dir);
} else {

	$response_result = $attendanceObj->getAttendance($options, $objPage->_db_start, $_limit, $order, $dir);
}

$objPage->_total = $response_result['total'];
$attendance_arr = $response_result['result'];

//echo "<pre>";print_r($attendance_arr);exit; 
?>

<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left paddLeft0">Attendance List </div>
</div>
<div class="clear"></div>
<section class="padder">

	<form id="serachform" name="serachform" method="get" class="form-horizontal form-centerReg" data-validate="parsley" onSubmit="return confSubmit();" action="attendanceList.php">
		<section class="marginBottom5 serachformDiv">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left text-left paddLeft0 paddRight0">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  text-left paddLeft0 relative" style="z-index:1">

					<div class="searchboxCSS search-box col-xs-10 padd0 pull-right">
						<input name="roll_no" id="roll_no" type="text" autocomplete="off" placeholder="Search roll number..." class="form-control  parsley-validated" <?php if ((isset($_REQUEST['roll_no']) && $_REQUEST['roll_no'] != "")) { ?> value="<?php echo filter_query($_REQUEST['roll_no']); ?>" <?php } ?> />
						<input name="student" id="student_hidden" type="hidden" class="form-control  parsley-validated" <?php if ((isset($_REQUEST['student']) && $_REQUEST['student'] != "") && (isset($_REQUEST['roll_no']) && $_REQUEST['roll_no'] != "")) { ?> value="<?php echo filter_query($_REQUEST['student']); ?>" <?php } ?> />
						<div class="result_list"></div>
					</div>

				</div>

				<div class="clear" style="margin-top:10px;">&nbsp;</div>

				<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 pull-right text-right padd0">
					<button type="submit" name="Submit" class="btn btn-red" title="Search Roll Number" id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button>
					<a class="btn btn-sm btn-red btnwidth40" href="attendanceList.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>

				</div>
			</div>

			<label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
				<option value="10" <?php echo $_limit == 10 ? 'selected' : '' ?>>10 </option>
				<option value="25" <?php echo $_limit == 25 ? 'selected' : '' ?>>25 </option>
				<option value="50" <?php echo $_limit == 50 ? 'selected' : '' ?>>50 </option>
				<option value="100" <?php echo $_limit == 100 ? 'selected' : '' ?>>100 </option>
			</select>
	</form>
	<?php if ($succ == '1') { ?>
		<div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg; ?>
		</div>
	<?php } ?>
	<?php if ($succ == '2') { ?>
		<div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i> <?php echo $msg; ?>
		</div>
	<?php } ?>
	<?php if ($err == '1') { ?>
		<div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg; ?>
		</div>
	<?php } ?>
</section>

<div class="clear"></div>
<section class="panel panel-default">

	<div class="panel-body">
		<?php if ($objPage->_total > 0) {
			$no = ($_page - 1) * $_limit + 1; ?>
			<div class="table-responsive">
				<table class="table table-border dataTable table-fixed">
					<thead class="fixedHeader">
						<tr class="col-sm-12 padd0">
							<th class="col-sm-1 text-left hide">User Id</th>
							<th class="col-sm-3 text-left">Roll Number</th>
							<th class="col-sm-3 text-left">Attendance Date</th>
							<th class="col-sm-2 text-left">Is Present</th>
							<th class="col-sm-2 text-left">Upload Date</th>
							<th class="col-sm-2 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($attendance_arr  as $key => $value) {

							if ($value->is_present == 'yes') {
								$is_present = "Yes";
								$activeClass = "style='color:Green'";
							} else {
								$is_present = "No";
								$activeClass = "style='color:Red'";
							}

							if (!isset($value->attendance_date)) {
								$attendanceDate = "NA";
							} else if ($value->attendance_date == '0000-00-00 00:00:00') {
								$attendanceDate = "NA";
							} else {

								$attendanceDate = $value->attendance_date;
								$attendanceDate = date('d-m-Y', strtotime($attendanceDate));
							}
							if (!isset($value->upload_date)) {
								$uploadDate = "NA";
							} else if ($value->upload_date == '0000-00-00 00:00:00') {
								$uploadDate = "NA";
							} else {

								$uploadDate = $value->upload_date;
								$uploadDate = date('d-m-Y', strtotime($uploadDate));
							}
							//echo "<pre>";print_r($attendance_arr);
							$uid = base64_encode($value->user_id);
							if ($region_id == '5') {
								$email_id = $value->loginid;
							} else {
								$email_id = $value->email_id;
							}
						?>
							<tr class="col-sm-12 padd0" uid="<?php echo $values['id']; ?>">
								<td class="col-sm-3"><?php echo $value->roll_no; ?></td>
								<td class="col-sm-3 text-left"><?php echo $attendanceDate; ?></td>
								<td class="col-sm-2 text-left" <?php echo $activeClass; ?>><?php echo $is_present; ?></td>
								<td class="col-sm-2 text-left"><?php echo $uploadDate; ?></td>
								<td class="col-sm-2 text-center">
									<form method="post" name="myform" onsubmit="return deleteHandler(event)">
										<input type="hidden" name="attendance_id" value="<?php echo $value->id ?>">
										<button type="submit" title="<?php echo $language[$_SESSION['language']]['delete']; ?>" style="background:none; border:none;" onclck="alert()" hreff="attendanceList.php?attendaid=<?php echo $uid ?>"> <i class="fa fa-trash"> </i> <?php echo $language[$_SESSION['language']]['delete']; ?> </button>

									</form>


								</td>

								<td class="col-sm-1 text-center hide"><a href="javascript:void(0);" onclick="return updateStudentStatusFn('ajax/updateStudentStatus.php','<?php echo $value->user_id; ?>','<?php echo $value->is_active; ?>');">
										<button type="button" id="btnAction"><?php if ($value->is_active == 1) { ?>Deactivate<?php } else { ?>
											Activate
										<?php } ?></button>
									</a></td>
							</tr>
						<?php } ?>
						<tr>
							<td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param, 5, 'pagination'); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php } else { ?>
			<div class="col-sm-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?>
			</div><?php } ?>
	</div>
</section>

<?php include_once('../footer/adminFooter.php'); ?>
<style>
	.th-sortable .th-sort {
		float: none;
		position: relative;
		margin-left: 2px;
	}
</style>
<script src="./js/sb-report-script.js"></script>

<script>
	//On region chnage
	$('#region').change(function() {
		var region = $('#region option:selected').val();
		$('#student_txt').val('');
		$('#student_hidden').val('');
		$('#center_id').html('<option value="">Select <?php echo $center; ?></option>');
		$('#batch_id').html('<option value="">Select <?php echo $batch; ?></option>');
		if (region == '') {
			$('#country').find('option').remove().end().append('<option value=""><?php echo $language[$_SESSION['language']]['select_country']; ?></option>');
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
		var region_id = $('#region').val();
		var country = $('#country option:selected').val();
		$('#student_txt').val('');
		$('#student_hidden').val('');
		$('#center_id').html('<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?></option>');
		$('#batch_id').html('<option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>');
		if (country == '') {
			$('#center_id').find('option').remove().end().append('<option value=""><?php echo $language[$_SESSION['language']]['select_state']; ?> </option>');
		} else {
			$.post('ajax/getCenterByCountry.php', {
				region_id: region_id,
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


	//On center chnage
	$('#center_id').change(function() {
		setDistrict(this);
		var region_id = $('#region').val();
		$('#student_txt').val('');
		$('#student_hidden').val('');
		var center_id = $('#center_id option:selected').val();
		if (center_id == '') {
			$('#batch_id').find('option').remove().end().append('<option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>');
		} else {
			$.post('ajax/getBatchByCenter.php', {
				region_id: region_id,
				center_id: center_id
			}, function(data) {
				if (data != '') {
					$('#batch_id').html(data);
				} else {
					$('#batch_id').html('<option value="">Not Available</option>');
				}
			});
		}
	});

	//On center chnage
	$('#batch_id').change(function() {

		$('#student_txt').val('');
		$('#student_hidden').val('');
		var batch_id = $('#batch_id option:selected').val();
		var center_id = $('#center_id option:selected').val();
		if (batch_id == '') {
			$('#student').find('option').remove().end().append('<option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?> </option>');
		} else {
			$.post('ajax/getStudentByCenterAndBatch.php', {
				batch_id: batch_id,
				center_id: center_id
			}, function(data) {
				if (data != '') {
					//$('#student').html();
					$('#student').html(data);
				} else {
					$('#student').html('<option value="">Not Available</option>');
				}
			});
		}
	});
</script>

<script type="text/javascript">
	/*	$(document).ready(function() {
		$('.search-box input[type="text"]').on("keyup input", function() {*/
	/* Get input value on change */
	/*			var inputVal = $(this).val();
				$('#student_hidden').val('');
				var region_id = $('#region').val();
				var batch_id = $('#batch_id option:selected').val();
				var center_id = $('#center_id option:selected').val();
				var country = $('#country option:selected').val();
				var status = $('#status').val();
				var resultDropdown = $(this).siblings(".result_list");
				if (inputVal.length && inputVal.length > 0) {
					$.post("ajax/search_student.php", {
						uname: inputVal,
						batch_id: batch_id,
						center_id: center_id,
						country: country,
						status: status,
						region_id: region_id,
						role_id: 2
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
		});*/


	function deleteHandler(e) {
		if (!confirm("Are you sure you want to delete this attendance ?"))
			return false;
	}
</script>