<?php
include_once('../header/adminHeader.php');

$con = createConnection();





if(isset($_POST['batchReportButton']) && $_FILES['file']['name'] != ""){
	$uploads_dir = 'uploads';
	 $tmp_name = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];
	//print_r($_FILES['file']['name']);exit;
	if (file_exists("$uploads_dir/$name")) {
		@unlink ("$uploads_dir/$name");
	} 
	$pathtomove=$uploads_dir.'/'.$name;
	$tempArr =array();
	if(move_uploaded_file($tmp_name,$pathtomove)){
		 $userfile_extn = substr($name, strrpos($name, '.')+1);
			if($userfile_extn=="xls"){
				require_once ("excel/excel_reader2.php");
				$data = new Spreadsheet_Excel_Reader();

				//echo "<pre>"; print_r($data); die;
				$data->setOutputEncoding('utf8');
				$data->read('uploads/'.$name);
				$k=0;
				$num=0;
				$grid=array();
				
				$cnt = 0;
				for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {

					for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
						$grid[$k] = $data->sheets[0]['cells'][$i][$j];
						$k++;
					}
					
					//print_r($grid);exit;
					$district = $grid[0];
					$state = $grid[1];
					
					
					
					
					
					
					$obj = array();
					//$obj = new stdClass();
					 $obj['district'] = trim(addslashes($district));
					$obj['state'] = trim(addslashes($state));
					$tempArr[] = $obj;
						
						
						

				
					$k = 0;
				}
				}
				}
				
				
				
					
					if(count($tempArr) > 0){
						foreach($tempArr as $key => $value){
							$obj1 = array();
							$state = $value['state'];
							$district = $value['district'];
						
			
							$stmt = $con->prepare("Select center_id from tblx_center WHERE name=?");
							$stmt->bind_param("s",$state);
							$stmt->execute();
							$stmt->bind_result($center_id);
							$stmt->fetch();
							$stmt->execute();
							$stmt->close();
						echo 'State - '.$state.' center_id '.$center_id.'<br>';
							
							$stmt = $con->prepare("insert into tblx_district (state_id, district_name, created_date,status) values (?, ?, NOW(),1)");
							$stmt->bind_param("is",$center_id,$district);
							//$stmt->execute();
							$stmt->close();
        
						}
					}
			}
	
?>

<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
      <section class="panel panel-default  marginBottom5">
            <div class="row m-l-none m-r-none bg-light lter">
                  <div class="col-sm-6 col-md-4 padder-v b-light"  title="<?php echo $language[$_SESSION['language']]['maximum_learner_limit']; ?>">
                    <div class="col-sm-4 padd0 text-right">
					<span class="fa-stack fa-2x m-r-sm  iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-info"></i>
                      <i class="fa fa-users fa-stack-1x text-white"></i>
					  </span>
                    </div>
					<div class="col-sm-8 padd0">
                    <a class="">
                      <div class="h3  m-t-xs"><strong id="totalLimit"><?php echo $student_limit; ?></strong></div>
                      <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['maximum_learner_limit']; ?></small></div>
                    </a>
					</div>
                  </div>
                 
                <div class="col-sm-6 col-md-4 padder-v b-l b-light lt"  title="<?php echo $language[$_SESSION['language']]['remaining_learner_limit']; ?>">                     
                   <div class="col-sm-4 padd0 text-right">
				    	<span class="fa-stack fa-2x m-r-sm  iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-success"></i>
                      <i class="fa fa-user fa-stack-1x text-white"></i>
                       </span> 
					</div>
					<div class="col-sm-8 padd0">
					
                    <a class="clear">
                      <div class="h3  m-t-xs"><strong id="remainLimit"><?php echo $remaining; ?></strong></div>
                      <div><small class="text-muted text-uc"> <?php echo $language[$_SESSION['language']]['remaining_learner_limit']; ?></small></div>
                    </a>
                  </div>
                 	</div>
					 <div class="col-sm-6 col-md-4 padder-v b-l b-light "  title="<?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?>">                 
                   <div class="col-sm-4  padd0 text-right">
				      <a href="excel/Bulk_Student_Upload.xls" download> <span class="fa-stack fa-2x  m-r-sm iconPadd">
                      <i class="fa fa-circle fa-stack-2x text-danger"></i>
                      <i class="fa fa-download fa-stack-1x text-white"></i>
                       </span>  </a>
					</div>
					<div class="col-sm-8 padd0">
					
                    <a class="clear" href="excel/Bulk_Student_Upload.xls" download>
                      <div class="m-t-xs"> <?php echo $language[$_SESSION['language']]['download']; ?></div>
                      <div><small class="text-muted text-uc"><?php echo $language[$_SESSION['language']]['sample_data_excel_file']; ?></small></div>
                    </a>
                  </div>
                 	</div>
					</div>
          </section>
     <?php echo $customerrDiv;
		if(isset($_GET["err"])){
			if($_GET["err"] == 0){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Please check uploaded file.
				</div>";
			}else if($_GET["err"] == 2){
				$errDiv = " <div class='alert alert-success'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> Uploaded Successfully please go to  $students list Report to view registered $students.
				</div>";
			}else if($_GET["err"] == 3){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> $uploadMsg.
				</div>";
			}else if($_GET["err"] == 1){
				$errDiv = " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i> There is something wrong.
				</div>";
			}else if(($_GET["err"] == 4) && isset($_SESSION['msg'])){
				 echo  " <div class='alert alert-danger'>
				<button type='button' class='close' data-dismiss='alert'>&times;</button>
				<i class='fa fa-ok-sign'></i>";
				 echo "No record saved.<br>";
				foreach($_SESSION['msg'] as $errKey=>$errVal){
				
				 echo "Record no. ".$errVal['item_no']." Error: ".$errVal['msg']; 
				
				}
			echo "</div>";
			unset($_SESSION['msg']);
			}
			echo $errDiv;
		}
   ?>
	<form action="" id="batchReport" class="<?php echo $regClass;?>" method="post"  data-validate="parsley" enctype="multipart/form-data" onsubmit="return fileValidation();" >
	 <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		 	<h3 class="panel-header"><?php echo $language[$_SESSION['language']]['bulk_upload_learners']; ?></h3>
		   
           <div class="form-group pull-in clearfix" id="classSectionDtl1">
      
			 <div class="col-sm-2">
			 <label class="control-label"><?php echo $language[$_SESSION['language']]['file']; ?> <span class="required">*</span></label>
			 <!-- <div class="uploadClass" onclick="document.getElementById('file').click();" id="uploadDiv"><i class="fa fa-upload"></i> -->
			  <div class="uploadClass " onclick="uploadFile(this.id,'file','fileName');" style="cursor:pointer" id="uploadDiv"><i class="fa fa-upload"></i> <?php echo $language[$_SESSION['language']]['upload']; ?>
			   <input type="file" class="fileHidden" name="file" id="file" style="width: 100px;
    height: 35px;"><br />
			  </div> <div id="fileName" style="color:#111;clear:both;"></div>
			  <label class="required" id="fileError"></label>
			  </div>
			  
		    </div>
		   </div>
		</div>
		  <div class="clear"></div>
		   <div class="text-right"> 
			
			   <input id="profile_id" type="hidden" name="profile_id" value="<?php echo $studentData->profile_id; ?>"/>
			  <input id="client_id" type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
			   <button type="submit" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" name="batchReportButton"  class="btn btn-s-md btn-primary "><?php echo $language[$_SESSION['language']]['submit']; ?></button>
	      </div>
		 </div> 
     </form>
   </section> 
  </div>
 </div>
</section>

<?php include_once('../footer/adminFooter.php');?>