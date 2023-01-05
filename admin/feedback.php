<?php include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
$uid = base64_encode($user_id);
?>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	<?php echo $language[$_SESSION['language']]['feedback']?></div>
	<div class="col-md-6 col-sm-6 text-right hide">
	   <a class="btn" href="javascript:void(0)" class="feedbackInnerForm" 
	 style="float:right; cursor: pointer; margin-top: 10px;" 
	 data-placeholder="Video" data-paceholderid="1"
	  data-div="feedbackForm" 
	  title="<?php echo $language[$_SESSION['language']]['share_feedback']; ?>">
	  <?php echo $language[$_SESSION['language']]['share_feedback']; ?>                   
	   </a>
	</div>
		
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
   <iframe class="notification"  src="https://wfpstaging.englishedge.in/ilt/notification_ui/admin-feedback.php?user_id=<?=$uid?>&lang=<?=$_SESSION['language']?>" width="100%" height="1000px" style="border:none;" id="iframe1" frameborder="0" scrolling="yes" onload="resizeIframe(this)" style="overflow: hidden; height: 100%;
        border:none;overflow:hidden;width: 100%;height:100vh; position: abosulte;"></iframe>
</section>

<div class="modal fade feedbackInnerForm" id="feedbackInnerForm" style="z-index:1070;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> <?php echo $language[$_SESSION['language']]['share_feedback'] ?></h5>
        <button type="button" class="close" title="<?php echo $language[$_SESSION['language']]['close'] ?>" onclick="hideFeedBackModal();" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body search-output">
	  <form id="SB_FormFeedBack" action="javascript:void(0);" >
  <div class="tab-pane " >
  <div id="feedback_msg"></div>
        <div class="feedback-sec">
            <div class="feed-form">
            	<div class="feed-row">
            		<label ><?php echo $language[$_SESSION['language']]['subject'] ?> <span class="text-danger">*</span></label>
	                <input type="text" name="title" id="title" placeholder="<?php echo $language[$_SESSION['language']]['enter_issue_title'] ?>" class="form-control" value="" style="width:100%"  required></textarea>   
	            </div>   
                 <div class="feed-row">
                    <label ><?php echo $language[$_SESSION['language']]['category'] ?> <span class="text-danger">*</span></label>
                    <select name="placeholder" id="placeholder" class="form-control" value="" style="max-width:100%"  required>
                        <option value="">Select Category</option>
                        <option value="Audio">Audio</option>
                        <option value="Video">Video</option> 
                        <option value="Documents">Documents</option>
                        <option value="General Queries">General Queries</option>
                        <option value="Quiz">Practice/Quiz</option>
                    </select>  

                </div>  
                <div class="feed-row">
                	<label ><?php echo $language[$_SESSION['language']]['feedback'] ?> <span class="text-danger">*</span></label>
	                <textarea name="feedback" id="feedbackId" class="feedbackId feedback form-control" required maxlength="240" minlength="5"></textarea>  
	                <p><span class="word_left">240</span>  <?php echo $language[$_SESSION['language']]['characters_remaining'] ?> </p> 
	            </div>  


				<div class="modal-footer">   
				<button type="button" title="<?php echo $language[$_SESSION['language']]['close'] ?>" class="btn btn-secondary" onclick="hideFeedBackModal();" style="width: 180px;"><?php echo $language[$_SESSION['language']]['close'] ?></button>     
      <button type="button" class="btn btn-primary sbsbSubmitFeedback" title="<?php echo $language[$_SESSION['language']]['send_feedback'] ?>" onclick="validateAllfields();" style="width: 180px;"><?php echo $language[$_SESSION['language']]['send_feedback'] ?></button>
    
      </div>              

            </div>
        </div>
      </div>
</form>
	  </div>
      
    </div>
  </div>
</div>


<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>
<?php include_once('../footer/adminFooter.php'); ?>
<script>
  function resizeIframe(obj) {
   /*  obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px'; */
  }
$(document).ready(function () {
  var uid = '<?php echo $user_id ?>';
     uid = window.atob(uid);
 
    localStorage.setItem('user_id', uid);
	$('.feedbackInnerForm').click(function(){
		$('#feedbackInnerForm').modal('show');
		
	});


	var submit_uri = 'https://wfpstaging.englishedge.in/ilt/notification_api/public/api/'; //where feedback will be sent
	  var feedbackFormPlace = ''; //feedback form div to show the pop up
	  var placeholder = '';  //placeholder e.g. video/audio, Quiz, Queries, Documents 
	  var placeholder_id = '1'; // specific id for this placeholder
	  var form_id = '';
	  var  fd = {};
	 /*  fd.course_id = '<?php echo isset($_GET["cEdge_id"])?$_GET["cEdge_id"]:0?>';
	  fd.topic_id = topic_edge_id;
	  fd.chapter_id ='<?php echo isset($_GET["chtid"])?$_GET["chtid"]:0?>';
	  fd.component_id=quiz_edge_id; */

});



$('#feedbackId').on('keyup', function() {
     var maxLimit = 240;
     var lengthCount = this.value.length;              
     if (lengthCount > maxLimit) {
       this.value = this.value.substring(0, maxLimit);
       var charactersLeft = maxLimit - lengthCount + 1;                   
     }
     else {
      var charactersLeft = maxLimit - lengthCount;  
      $('.word_left').text(charactersLeft);        
       
     }
     });

function validateAllfields(){
	fd.title=$('#title').val();
	fd.feedback=$('#feedbackId').val();
	placeholder=$('#placeholder').val();
	fd.placeholder=placeholder;

   if(fd.placeholder.length>1 && fd.title.length>1 && fd.feedback.length>4)
 {
	sendFeedBack();
	$('#title').val('');
	$('#feedbackId').val('');
	$('#placeholder').val('');
 }else{
	$('#feedback_msg').show();
	$('#feedback_msg').html('<div class="alert alert-danger" role="alert">Please fill out this form</div>').delay(2500).fadeOut();
 }

 }


 function sendFeedBack(){
	
    fd.placeholder_id = placeholder_id;
    fd.created_by= uid;
    fd.creation_time = new Date();

    $('.sbSubmitFeedback').prop('disabled', true);

    $.post(submit_uri+'sendFeedbackNotification', fd, function(res){
		$('#feedback_msg').show();
      if(res.success){
        
        $('#feedback_msg').html('<div class="alert alert-primary" role="alert">Thank you for submitting your feedback.</div>').delay(5000).fadeOut('slow');
        
        setTimeout(() => { $('#feedbackInnerForm').modal('hide'); }, 2000);

      }else{
        $('.sbSubmitFeedback').prop('disabled', false);
        $('#feedback_msg').html('<div class="alert alert-danger" role="alert">'+res.message+'</div>').delay(5000).fadeOut('slow');
      }
    })
  
}
 
function hideFeedBackModal(){
  $('#feedbackInnerForm').modal('hide');
  
} 
  
</script>