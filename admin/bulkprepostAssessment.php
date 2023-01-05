<?php
include_once('../header/adminHeader.php');
include_once('../controller/bulkUploadController.php');

/* error_reporting(E_ALL);
ini_set('display_errors',1); */

$centerDetail = $adminObj->getCenterDetails();
//echo "<pre>";print_r($centerDetail);
$centerName = $centerDetail[0]['name'];
$center_id = $centerDetail[0]['center_id'];
$defaultCountry = $centerDetail[0]['country'];
if (isset($_GET['uid'])) {
	$uId = trim(base64_decode($_GET['uid']));
	$studentData = $adminObj->getUserDataByID($uId, 2); // student role 2
}
$getSignedUpUser = $adminObj->getSignedUpUserCount();
//echo "<pre>";print_r($getSignedUpUser->student);exit;

$student_limit = $centerDetails[0]['student_limit'];
//total user limit
if ($student_limit != 0) {
	$remaining = $student_limit - $getSignedUpUser->student;
	if ($remaining > 0) {
		$uploadMsg = "Upload was not successful. You can upload only $remaining more $students. Please make corrections in the Excel file and upload it again.";
		$regClass = "";
		$customerrDiv = "";
	} else {

		$uploadMsg = "You have reached maximum limit of $students allowed. You can not upload more $students. Please contact administrator.";

		if ($_GET["err"] == '' && $remaining == 0) {
			$regClass = "cursorDefault";
			$customerrDiv = "<div class='col-sm-12  marginTop20  marginBottom40 paddLeft40  paddRight40 '> <div class='alert alert-danger height60 fontSize16 paddTop20'>
								
								<i class='fa fa-ok-sign'></i> You have reached maximum limit of $students allowed. You can not upload more $students. Please contact administrator.
								</div> </div>";
		}
	}
} else {
	$remaining = 0;
}

//echo "=============".$remaining;exit;
if (isset($_POST['batchReportButton']) && $_FILES['file']['name'] != "") {
	$att = new BulkUploadController();
	$err = 0;
	$uploads_dir = 'uploads';
	$tmp_name = $_FILES["file"]["tmp_name"];
	$name = $_FILES["file"]["name"];
	//print_r($_FILES['file']['name']);//exit;
	if (file_exists("$uploads_dir/$name")) {
		@unlink("$uploads_dir/$name");
	}
	$pathtomove = $uploads_dir . '/' . $name;
	$status = true;
	//print_r($pathtomove);
	if (move_uploaded_file($tmp_name, $pathtomove)) {
		$userfile_extn = substr($name, strrpos($name, '.') + 1);

		if ($userfile_extn == "xls") {
			require_once("excel/excel_reader2.php");
			$data = new Spreadsheet_Excel_Reader();

			//echo "<pre>"; print_r($data); die;


			$data->setOutputEncoding('utf8');
			$data->read('uploads/' . $name);
			$k = 0;
			$num = 0;
			$grid = array();
			$cntExit = 0;
			$cnt = 0;
			$rows = $data->sheets[0]['numRows'];
			$cols = $data->sheets[0]['numCols'];
			$cells = $data->sheets[0]['cells'];

			for ($i = 2; $i <= $rows; $i++) {

				$tmp = array();
				$k = 0;
				for ($j = 1; $j <= $cols; $j++) {
					$tmp[$k] = $cells[$i][$j];
					$k++;
				}
				$grid[$cnt] = $tmp;
				$cnt++;
			}

			foreach ($grid as $index => $value) {

				$roll_no = trim(addslashes(filter_string($value[0])));
				$exam_type = trim(addslashes(filter_string($value[1])));
				$level_value = trim(addslashes(filter_string($value[2])));
				$date_created = date('Y-m-d');

				$data_array = array();
				$data_array['roll_no'] = $roll_no;
				$data_array['exam_type'] = $exam_type;
				$data_array['level_value'] = $level_value;
				$data_array['date_created'] = $date_created;
				
				if($level_value!='' && $roll_no!='' && $exam_type!='')
					$status = $att->insertBulkAssessment($data_array);
				
				//print_r($status);
				if (!$status)
					$err++;
			}
			if (!$err) {
				@unlink("$uploads_dir/$name");
				header('location:bulkprepostAssessment.php?err=2');
				exit;
			}
		} else {
			@unlink("$uploads_dir/$name");
			header('location:bulkprepostAssessment.php?err=0');
			exit;
		}
	} else {
		header('location:bulkprepostAssessment.php?err=1');
		exit;
	}
}

?>
<!--<ul class="breadcrumb no-border no-radius b-b b-light">
	<li> 
		<a href="studentList.php" title="<?php //echo $language[$_SESSION['language']]['attendance'];?>"><i class="fa fa-arrow-left"></i> <?php //echo $language[$_SESSION['language']]['attendance'];?> </a>
	</li>
</ul>-->
<div class="clear"></div>
<section class="padder">
	<div class="row-centered">
		<div class="col-sm-10 col-xs-12 col-centered">
			<section class="marginBottom40">
				<section class="panel panel-default  marginBottom5">
					<div class="row m-l-none m-r-none bg-light lter">

						<div class="col-sm-6 col-md-4 padder-v b-l b-light ">
							<div class="col-sm-4  padd0 text-right">
								<a href="excel/Bulk_Prepost_Assessment.xls" download title="<?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?>"> <span class="fa-stack fa-2x  m-r-sm iconPadd">
										<i class="fa fa-circle fa-stack-2x text-danger"></i>
										<i class="fa fa-download fa-stack-1x text-white"></i>
									</span>
								</a>
							</div>
							<div class="col-sm-8 padd0">

								<a class="clear" title="<?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?>" href="excel/Bulk_Prepost_Assessment.xls" download>
									<div class="m-t-xs"> <?php echo $language[$_SESSION['language']]['download']; ?></div>
									<div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?></small></div>
								</a>
							</div>
						</div>
					</div>
				</section>
				<?php echo $customerrDiv;
				if (isset($_GET["err"])) {
					if ($_GET["err"] == 0) {
						$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Please check uploaded file.
				</div>";
					} else if ($_GET["err"] == 2) {
						$errDiv = " <div class='alert alert-success'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Assessment File Uploaded Successfully.
				</div>";
					} else if ($_GET["err"] == 3) {
						$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> $uploadMsg.
				</div>";
					} else if ($_GET["err"] == 1) {
						$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> There is something wrong.
				</div>";
					} else if (($_GET["err"] == 4) && isset($_SESSION['msg'])) {
						echo  " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i>";
						echo "No record saved.<br>";
						foreach ($_SESSION['msg'] as $errKey => $errVal) {

							echo "Record no. " . $errVal['item_no'] . " Error: " . $errVal['msg'];
						}
						echo "</div>";
						unset($_SESSION['msg']);
					} else if ($_GET["err"] == 5) {
						$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Records are not uploaded due to duplicate " . $_SESSION['msg'] . " Please remove and try again.</div>";
						unset($_SESSION['msg']);
					}
					echo $errDiv;
				}
				?>
				<form action="" id="bulkUploadForm" name="bulkUploadForm" class="<?php echo $regClass; ?>" method="post" data-validate="parsley" enctype="multipart/form-data" onsubmit="return fileValidation();">
					<div class="row">
						<div class="panel panel-default bdrNone">
							<div class="panel-body padd20">
								<h3 class="panel-header">Upload Pre-Post Assessment<?php //echo $language[$_SESSION['language']]['upload_attendance'];?></h3>

								<div class="col-sm-2">
									<label class="control-label"><?php echo $language[$_SESSION['language']]['file']; ?> <span class="required">*</span></label>
									<!-- <div class="uploadClass" onclick="document.getElementById('file').click();" id="uploadDiv"><i class="fa fa-upload"></i> -->
									<div class="uploadClass unabled" onclick="uploadFile(this.id,'file','fileName');" style="cursor:pointer" id="uploadDiv"><i class="fa fa-upload"></i> <?php echo $language[$_SESSION['language']]['upload']; ?>
										<input type="file" class="fileHidden" name="file" id="file" style="width: 100px;
    height: 35px;"><br />
									</div>
									<div id="fileName" style="color:#111;clear:both;"></div>
									<label class="required" id="fileError"></label>
								</div>

							</div>
						</div>
					</div>
					<div class="clear"></div>
					<div class="text-right">
						<a href='studentList.php' class="btn btn-primary " title="<?php echo $language[$_SESSION['language']]['cancel']; ?>"><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
						<input id="profile_id" type="hidden" name="profile_id" value="<?php echo $studentData->profile_id; ?>" />
						<input type="hidden" name="country" id="country" value="<?php echo $defaultCountry; ?>" />
						<input type="hidden" name="motherTongue" id="motherTongue" value="38" />
						<input type="hidden" name="center_id" id="center_id" value="<?php echo $center_id; ?>" />

						<button type="submit" name="batchReportButton" class="btn btn-s-md btn-primary  pre-loader" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" onclick="showLoaderOrNot('bulkUploadForm');" ondblclick="showLoaderOrNot('bulkUploadForm');"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
					</div>
		</div>
		</form>
</section>
</div>
</div>
</section>
<style>
	.fileHidden {
		font-size: 0px;
		position: absolute;
		top: 0px;
		left: 0px;
		opacity: 0;
		cursor: pointer;
	}

	.uploadClass {
		width: 100px;
		height: 35px;
		position: relative;

		left: 12px;
		background: rgb(1, 137, 197) none repeat scroll 0% 0%;
		color: rgb(255, 255, 255);
		text-align: center;
		padding: 8px;
		border-radius: 5px;
		cursor: pointer
	}
</style>
<?php include_once('../footer/adminFooter.php'); ?>
<script>
	function uploadFile(id, inputId, fileName) {

		$("#" + inputId).on('change', function() {
			var filevalue = this.files[0].name;
			// var filevalue =  $("#"+inputId).val();
			$("#" + fileName).text(filevalue);
			if ($("#" + fileName).text() != '') {
				$("#fileError").text("");
			}
		});
	}

	function checkUploadFile(cId, UploadId, uploadName) {
		var cValue = $("#" + cId + " option:selected").val();
		if (cValue != '') {
			$("#uploadDiv").removeClass('disabled');
		} else {
			$("#uploadDiv").addClass('disabled');
		}
	}

	function fileValidation() {
		var fileText = $("#fileName").text();
		if (fileText == '') {
			$("#fileError").text("Please upload file")
		}

	}

	$(document).ready(function() {

	});
</script>