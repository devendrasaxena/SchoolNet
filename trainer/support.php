<?php include_once('../header/trainerHeader.php');
$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['err']) && $_SESSION['err'] != ""){
	    
	 if($_SESSION['err'] == 1){
		$msg ="Something wrong please try again!" ; 
	}	
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == 1){
		   $msg =  $language[$_SESSION['language']]['your_feedback_successfully_recorded!'];   
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
   $succ = $_SESSION['succ'];
   unset($_SESSION['succ']);
}
if(isset($_SESSION['err']) && $_SESSION['err'] != ""){
	$err=$_SESSION['err'];
	unset($_SESSION['err']);
	
}
 if( isset($_POST['submitFeedback']) && !empty($_POST['feedbackSubject']) ){
            $platform = WEB_SERVICE_PLATFORM;
            $product = CLIENT_NAME;

            $device = WEB_SERVICE_DEVICE_TYPE;
            $device_id = WEB_SERVICE_DEVICE_ID;

            $sub = trim($_POST['feedbackSubject']);
            $mess = trim($_POST['feedbackDescription']);

            $fieldValues = array('userid'=> $userId, 'platform'=>$platform, 'device'=>$device, 'device_id'=>$device_id, 'product'=>$product, 'subject'=>$sub, 'mess'=>$mess,'client_id'=>$client_id);

			//echo "<pre>";print_r($fieldValues);exit;
            $res = $commonObj->insertFeedbackVal($fieldValues);
			$retValue=$commonObj->aduroGetUserDetailsFromUserId($userId);
			//echo "---->".$res;exit;

            if( $res ){
				$_SESSION['succ']=1;
				/* $mess.="<br><br>";
				$mess.="Name : ".$retValue->first_name." ".$retValue->last_name."<br>";
				//$mess.="User ID : ".$retValue->loginid."<br>";
				$mess.="Email ID : ".$retValue->email_id."<br>";
				if(isset($retValue->center_name))
				{
				$mess.="Organization : ".$retValue->center_name."<br>";
				$mess.="Class : ".$retValue->batch_name;
				}
				

				require_once('../library/phpMailer/mail.php');
				$subject = "Support Ticket - ".$sub;
				$recEmail='support-mepro@pearson.com';
				$mail = sendMailSupport($recEmail, $subject, $mess); */
			//echo "here1";exit;
                header("Location: support.php");
                exit;
            }else{
				$_SESSION['err']=1;
                header("Location: support.php");
                exit;
            }
        
    }

?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="dashboard.php"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['support']; ?></a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
  <div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40"> 
      <div class="feedback">
  
        <!-- Start  form -->
        <form name="feedbackForm" id="feedbackForm" class="formClass formMainDiv"  action="" method="POST" data-validate="parsley" autocomplete="off" enctype="multipart/form-data">
          <div class="row-centered  marginTop0" id="showiframe">
       
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
                
       <div class="col-sm-12 col-centered " id="inputDiv">
              <div class="col-sm-12 form-group well">
                <label><?php echo $language[$_SESSION['language']]['subject']; ?> <span class="required">*</span>:</label>
                <input name="feedbackSubject" id="feedbackSubject" type="text" class="form-control" maxlength="150" data-required="true" />
              </div>
              <div class="col-sm-12  form-group  well">
                <label><?php echo $language[$_SESSION['language']]['feedback']; ?> <span class="required">*</span>:</label>
                <textarea name="feedbackDescription" id="feedbackDescription" type="text" class="form-control textarea height100" data-minlength="[10]" maxlength="1000"
             data-required="true"></textarea>
              </div>
            </div>
          
                <?php //} ?>
            <!--End  form-->
      <div class="clear"></div>
      
    <!--  Start footer-->
          
              <div class="col-sm-12 col-centered ">
                <div class="btnBg">
                  <div class="text-center">
                    <button type="submit" name='submitFeedback' id ="submitFeed" class="btn btn-info" onClick="showLoaderOrNot('feedbackForm')"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
                  </div>
                </div>
              </div>
            
      <!--  End footer-->
      </div>
         
        </form>
        <!-- End  form -->
    </div>  
      
  </section>
  </div>
 </div>
</section>   
<?php include_once('../footer/trainerFooter.php');?>
