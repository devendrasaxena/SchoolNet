<?php 
   include_once('../header/trainerHeader.php');
   $assignment_res_path = '../user/assignment_response_files/';
   $assessmentObj = new assessmentController();
   $assignmentObj = new assignmentController();
   $productObj = new productController();
  
   $userObj = new userController();
	$batches ='';
   $batches = $assignmentObj->getAllClassForTrainer($_SESSION['user_id'], $_SESSION['center_id']);
	$exts = '[\''.implode('\',\'', $assignmentObj->getAllowedExtensions()).'\']';
   $notAllowedExts = '[\''.implode('\',\'', $assignmentObj->getNotAllowedExtensions()).'\']';
	
   $assignmentData = $studentsResponses = [];

   

   if(isset($_GET['aid'])){
     $aid = trim(base64_decode($_GET['aid']) );
	 $product_id = trim(base64_decode($_GET['prod']) );
     $assignmentData= $assignmentObj->getAssignmentById($aid);
     if(!$assignmentData){
         header('Location:assignments.php');
     }
      $studentsResponsesChecked = $assignmentObj->getAllResponsesById($aid,$product_id,1);
	  $studentsResponsesUnChecked = $assignmentObj->getAllResponsesById($aid,$product_id,0);
   //echo "<pre>"; print_r($studentsResponsesChecked);
  // echo "<pre>"; print_r($studentsResponsesUnChecked);
   }

   if($_POST['eveluateResponse']){
      foreach ($_POST['response_id'] as $response_id) {
         $data = [];
         $data['student_id'] = $_POST['student_id'.$response_id];
         $data['comment'] = $_POST['comment'.$response_id];
         $data['rating'] = $_POST['rating'.$response_id];
         $data['assignment_id'] = $_POST['assignment_id'];
         $data['teacher_id'] = $_POST['teacher_id'];
		 $data['product_id'] = $_POST['product_id'];
     $data['audio_feedback'] = isset($_POST['audio_feedback'])?$_POST['audio_feedback']:null;
		// echo "<pre>"; print_r($data); die;
         $evaluated = $assignmentObj->createEvaluation($data);
        
      }
      if($evaluated){
         echo header('Location:assignments.php');
      }
   }
   
   /* ob_clean();
   print_r($assignmentData); */
   
   ?>

<script type="text/javascript" src="<?php echo WEB_IP?>/js/recorderJs/Mp3LameEncoder.js"></script>   
<ul class="breadcrumb no-border no-radius b-b b-light">
   <li> <a href="assignments.php"><i class="fa fa-arrow-left"></i> <?php echo  'Assignment'; ?> </a></li>
</ul>
<div class="clear"></div>
<section class="padder">
   <div class="row-centered">
      <div class="col-sm-10 col-xs-12 col-centered">
         <section class="marginBottom40">
            </br>
            <?php if($errDiv!=''){?>
            <div class="alert alert-danger col-sm-12">
               <i class="fa fa-ban-circle"></i><?php echo $errDiv;?> 
            </div>
            <?php } ?>
            <?php if($succ=='1'){?>
            <div class="alert alert-success col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i><?php echo $msg;?>
            </div>
            <?php } ?>
            <?php if($succ=='2'){?>
            <div class="alert alert-success col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i> <?php echo $msg;?> 
            </div>
            <?php } ?>
            <?php if($err == '1'){?>
            <div class="alert alert-danger col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i><?php echo $msg;?> 
            </div>
            <?php } ?>
            <?php if($err == '2'){?>
            <div class="alert alert-danger col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i><?php echo $msg;?> 
            </div>
            <?php } ?> 
           <?php $_evaluated = $assignmentObj->getEvolutionByAssignmentId($assignmentData['id'],$assignmentData['product_id']);
                
				    $disabledEdit='avoid-clicks';?> 
				<div class="row">
                   <div class="panel panel-default">
                     <div class="panel-body padd20">
					 <h3 class="panel-header"><?php echo " Assignment Evaluation" ; ?>  <?php echo $studentName; ?></h3>
                          <div class="form-group col-sm-12">
	                           <div class="form-group col-sm-6">
	                              <label class="control-label"><b><?php echo $batch ;?> :</b>  </label>
                                 
                                    <?php 
									$batch_codes = explode(',',$assignmentData['batch_code']);
									$batchnames = '';
									foreach ($batches as $key => $batch) {   ?>
                                    <?php $batchnames .= in_array($batch['batch_id'], $batch_codes) ? $batch['batch_name'].', ':''?> 
                                    <?php }
									echo rtrim($batchnames,', ');
									?>
                                 
	                              
	                           </div>
							   <div class="form-group col-sm-6">
								<label class="control-label"><b><?php echo "Assignment" ;?> :</b>  </label>
								<?php echo $assignmentData['assignment_name'];?>
								</div>
	                              <!--<label class="control-label"><b>Product :</b>  </label>
                                
                                    
                                   <?php  $productDataArr =$productObj->getProdcutDetailById($assignmentData['product_id'] );   ?>
                                    <?php echo $productDataArr['product_name']
									?>
                                
	                           </div>
                              <?php 
                              /* Level Detail */
							   $courseDataArr = $assignmentObj->getProductConfigurationByClassAndTrainer($stdData['batch_code'],$stdData['center_id'], $stdData['product_id'],'course');

							   $batchCourseStr= str_replace("CRS-","",$courseDataArr[0]['course']);
					
								$courseType='0';
								$courseArr = $adminObj->getCustomCourseList($courseType,$batchCourseStr,'');
							   

								  $col  = 'sequence_id';
								  $sort = array();
								  foreach ($courseArr as $i => $obj) {
									   $sort[$i] = $obj->{$col};
									 }
								  array_multisort($sort, SORT_ASC, $courseArr); 
                              ?>
	                           <div class="form-group col-sm-6">
	                              <label class="control-label"><b>Course :</b></label>
	                             
                                    <?php foreach($courseArr as $key=>$val){ ?>
                                     <?php echo $val->course_id == $assignmentData['course_code'] ? $val->name:''?>
                                    <?php } ?>
                                
	                           </div>

                              <?php 
                             
                                $topic = $assignmentObj->getTopicOrAssessmentByCourseId($assignmentData['course_code'], $assignmentData['topic_edge_id']);
					          /* foreach($topic_arr as $key=>$val){
								   if($val->edge_id == $assignmentData['topic_edge_id']){
									$topic = $val->name;
								   }
								}*/
                              

                              ?>
	                           <div class="form-group col-sm-6">
	                              <label class="control-label"><b>Topic : </b></label>
	                              <?php echo $topic->name?>
	                              
	                           </div>

                              <?php 

                               $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($assignmentData['topic_edge_id'],$assignmentData['chapter_edge_id']);
								 foreach($chapter_arr as $key=>$val){
								   if($val->edge_id == $assignmentData['chapter_edge_id']){
									$chapter = $val->name;
								   }
								}
                              ?>
	                           <div class="form-group col-sm-6">
	                              <label class="control-label"><b>Chapter : </b></label>
	                              <?php echo $chapter?>
	                              
	                           </div>--> <div class="clear"></div>
				<?php 
				if($_evaluated){
					  
                 
				  if(count($studentsResponsesChecked) > 0) { 

				   foreach ($studentsResponsesChecked as $key => $studentsResponse) { 

						$user = $userObj->getUserLogData($studentsResponse['user_id']);
						$studentName = $user['first_name'].' '.$user['last_name'];
						
						$evaluation = $assignmentObj->getEvolutionByStudentAndAssignment($studentsResponse['assignment_id'], $studentsResponse['user_id'],$studentsResponse['product_id']);

					   $comment = isset($evaluation['evaluated_comment']) ? $evaluation['evaluated_comment']:'';
					   $rating = isset($evaluation['evaluated_rating']) ? $evaluation['evaluated_rating']:'';
            $teacher_audio_feedback = isset($evaluation['audio_feedback']) ? $evaluation['audio_feedback']:'';
			         if($rating!='') {
                    ?>
                                      
					  <div class="clear"></div>
					    <div style="padding:10px;margin-bottom:10px;height:250px;border:solid thin #ccc">
                              <div class="form-group col-sm-6 paddLef0">
                                  <label class="control-label"><b><?php echo $student; ?> :</b>
                                   <?php echo $studentName; ?>
								</div>
                                <div class="form-group col-sm-6 paddLef0">
                                          <label class="control-label"><b>Response :</b>
                                        
                                        <?php echo $studentsResponse['response_text'] ?>
                                          <div class="clear"></div>
                                          <?php if($studentsResponse['response_file']){?>
                                         
                                             <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo $assignment_res_path.$studentsResponse['response_file']?>"><i class="fa fa-download" aria-hidden="true"></i> View file</a>
                                         
                                          <?php } ?>
                                       </div><div class="clear"></div>
                                   <div class="form-group col-sm-6 "> 
                  							    <label class="control-label"><b><?php echo $teacher?> Comment : </b> </label>			 
               										
                                                 <?php echo $comment;?>
                                             </div> 
                                             <div class="form-group col-sm-6 "> 
                                             <label class="control-label"><b><?php echo $teacher?> Rating : </b> </label>
											  		  <?php echo $rating?>
												
										</div>    

                    <div class="form-group col-sm-6 "> 
                                             <label class="control-label"><b><?php echo $teacher?>  audio feedback : </b> </label>
                                             <?php if($teacher_audio_feedback != ""){

                                             
                                             echo "&nbsp; <audio controls style='position:absolute' src='".WEB_IP."/trainer/assignment_audio_feedback/$teacher_audio_feedback'></audio>";
                                           }else{

                                             echo 'Not available';
                                           }
                             ?>
                        
                    </div>   
                  			</div>   	
                  			<div class="clear"></div>
					<?php }
					    }?>
					    <div class="clear"></div>							 
					   <?php } ?>
				    <?php }?> 
                  </div>
                        
                           
                        </div>
                     </div>
                 
                  <div class="clear"></div>
                  
                
			
					
				<form action="" id="createStudentForm" class="createStudentForm" method="post"  data-validate="parsley" autocomplete="off" enctype="multipart/form-data">
                  <div class="">
				  
                  <div class="panel panel-default">
                     <div class="panel-body padd20">
                    <?php  $checkEvaluated=array();
					if(count($studentsResponsesUnChecked) > 0) {?> 
                        
						<div class="clear"></div>
						 <?php foreach ($studentsResponsesUnChecked as $key1 => $studentsResponse1) { 
					         $checkEvaluated[]=$key1;
							$user = $userObj->getUserLogData($studentsResponse1['user_id']);
							$studentName = $user['first_name'].' '.$user['last_name'];
							$evaluation1 = $assignmentObj->getEvolutionByStudentAndAssignment($studentsResponse1['assignment_id'], $studentsResponse1['user_id'],$studentsResponse1['product_id']);

						   $comment1 = isset($evaluation1['evaluated_comment']) ? $evaluation1['evaluated_comment']:'';
						   $rating1 = isset($evaluation1['evaluated_rating']) ? $evaluation1['evaluated_rating']:'';
						 
						  ?>
						   <div style="padding:10px;margin-bottom:10px;height:355px;">
							   <div class="form-group col-sm-6 paddLef0">
                                 <label class="control-label"><b><?php echo $student; ?> :</b>
                                   <?php echo $studentName; ?>
								</div>
							   
                                 <div class="form-group col-sm-6 paddLef0">
                                          <label class="control-label"><b>Response :</b>
                                        
                                             
                                             <?php echo $studentsResponse1['response_text'] ?>
                                          <div class="clear"></div>
                                          <?php if($studentsResponse1['response_file']){?>
                                         
                                             <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo $assignment_res_path.$studentsResponse1['response_file']?>"><i class="fa fa-download" aria-hidden="true"></i> View file</a>
                                         
                                          <?php } ?>
                                    </div>
                                       <input type="hidden" name="response_id[]" value="<?php echo $studentsResponse1['id']?>">
                                       <input type="hidden" name="student_id<?php echo $studentsResponse1['id']?>" value="<?php echo $studentsResponse1['user_id']?>">
                                      <div class="clear"></div>
									  <div class="form-group col-sm-6"> 
                  						 <label class="control-label"><b>Comment:</b> <span class="required text-red">*</span></label>			 
               								<div  class="input-append ">
                                             <textarea name="comment<?php echo $studentsResponse1['id']?>" id="description" placeholder="Assignment Description..." class="form-control input-lg " data-required="true" autocomplete="off" rows="5"><?php echo $comment1;?></textarea>
                                            </div> 
										 </div> 
                                          <div class="form-group col-sm-6"> 
                                             <label class="control-label"><b><?php echo $teacher?> Rating</b> <span class="required text-red">*</span></label>
                                             <div  class="input-append ">
                                                <ul style="list-style: none">
                                                   <?php for($rate = 1; $rate <= 10; $rate++){?> 
                                                   <li style="float: left; margin-left: 12px">
                                                      <input type="radio" name="rating<?php echo $studentsResponse1['id']?>" data-required="true" value="<?php echo $rate;?>" <?php echo $rating1 == $rate ? 'checked="checked"':''?>>
                                                      <div><?php echo $rate?></div>
                                                   </li>
                                                   <?php } ?>
                                                </ul>
               										  
                                             </div>   
                  									
                  							</div>
                                      
                                       <div class="clear"></div>

                                       <label>Audio Feedback: </label> <br>

                                       <input type="hidden" name="audio_feedback" id="audio_feedback">
                                       <button type="button" class="btn btn-primary js-start" > <i class="fa fa-microphone" aria-hidden="true"></i> </button>

                                        <button  type="button" class="btn btn-primary js-stop hide" > <i class="fa fa-pause" aria-hidden="true"></i> </button> 

                                        <button  type="button" class="btn btn-primary js-play hide" > <i class="fa fa-play" aria-hidden="true"></i> </button> 

                                        <small class="display-record-timer"></small> &nbsp;


                                        <audio controls style="position: absolute;" class="audio-feedback-preview hide">
 
  <source src="horse.mp3" type="audio/mpeg">
</audio>
                                       <hr>
					     <?php }?>
                              <?php } ?>

                              </fieldset>
                           </div>
                           <div class="clear"></div>
                           
                           
                           
                        </div>
						
                     </div>
                  </div>
                  <div class="clear"></div>
                  <?php if(count($checkEvaluated)>0){?>
                  <div class="text-right"> 
                     <a href='assignments.php' class="btn btn-primary  hide">Cancel</a>&nbsp;&nbsp;
                     <input id="assignment_id" type="hidden" name="assignment_id" value="<?php echo $assignmentData['id']; ?>"/>
                     <input id="teacher_id" type="hidden" name="teacher_id" value="<?php echo $_SESSION['user_id']; ?>"/>
                     <input id="product_id" type="hidden" name="product_id" value="<?php echo $assignmentData['product_id']; ?>"/>


                     
                     <button type="submit" name="eveluateResponse" value='eveluateResponse'  class="btn btn-s-md btn-primary  pre-loader">Submit</button>
                  </div>
				  <?php }?>
               </div>
                </form>
        
	
			
         </section>
      </div>
   </div>
</section>


<?php include_once('../footer/trainerFooter.php');?>
<script>



window.URL = window.URL || window.webkitURL;
/** 
 * Detecte the correct AudioContext for the browser 
 * */
window.AudioContext = window.AudioContext || window.webkitAudioContext;
navigator.getUserMedia  = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
 var  recorder = new RecordVoiceAudios();

var rTimer = null;


function recordMedia() {
        
        
  $('.audio-feedback-preview').attr('src','').addClass('hide');

        $(".js-start").addClass('hide');
        $(".js-stop").removeClass('hide');
        $(".js-play").addClass('hide');
        recorder.startRecord(); 
      recordTimer();
}

function stopMedia() {
  recorder.stopRecord(); 
  recordStopTimer();
   $(".js-stop").addClass('hide');
    $(".js-start").removeClass('hide');
    $(".js-play").removeClass('hide');

    
}

$(document).ready(function(){

    $(".js-start").click(function(){
      
        recordMedia();
        
  });


   $(".js-stop").click(function(){
    stopMedia();
  });

     $(".js-play").click(function(){
  
        this.classList.add('hide');
        $(".audio-feedback-preview").removeClass('hide');

        document.querySelector('.audio-feedback-preview').play();
        
     
  });
})



function recordStopTimer(){
clearInterval(rTimer);
rTimer =null;
$('.display-record-timer').html('');
$(".audio-feedback-preview").addClass('hide');
}

function recordTimer(){
  $('.display-record-timer').html('Recording: 00');
  let second = 0; 
  if(!rTimer){
  rTimer = setInterval(()=>{
    second++;
    var txt = second<10? `0${second}`:second;
        $('.display-record-timer').html('Recording: '+txt);
   
   if(second==30)
      stopMedia()

  },1000);

  }
}



 
 function RecordVoiceAudios() {

      //let downloadLink = document.getElementById('download');
      //let audioElement = document.querySelector('audio');
      let encoder = null;
      let microphone;
      let isRecording = false;
      var audioContext;
      let processor;
      let config = {
        bufferLen: 4096,
        numChannels: 2,
        mimeType: 'audio/mpeg'
      };

      this.startRecord = function() {
        audioContext = new AudioContext();
        /** 
        * Create a ScriptProcessorNode with a bufferSize of 
        * 4096 and two input and output channel 
        * */
        if (audioContext.createJavaScriptNode) {
          processor = audioContext.createJavaScriptNode(config.bufferLen, config.numChannels, config.numChannels);
        } else if (audioContext.createScriptProcessor) {
          processor = audioContext.createScriptProcessor(config.bufferLen, config.numChannels, config.numChannels);
        } else {
          console.log('WebAudio API has no support on this browser.');
        }

        processor.connect(audioContext.destination);
        /**
        *  ask permission of the user for use microphone or camera  
        * */
        navigator.mediaDevices.getUserMedia({ audio: true, video: false })
        .then(gotStreamMethod)
        .catch(logError);
      };

      let getBuffers = (event) => {
        var buffers = [];
        for (var ch = 0; ch < 2; ++ch)
          buffers[ch] = event.inputBuffer.getChannelData(ch);
        return buffers;
      }

      let gotStreamMethod = (stream) => {
        //startBtn.setAttribute('disabled', true);
        //stopBtn.removeAttribute('disabled');
        //audioElement.src = "";
        config = {
          bufferLen: 4096,
          numChannels: 2,
          mimeType: 'audio/mpeg'
        };
        isRecording = true;

        let tracks = stream.getTracks();
        /** 
        * Create a MediaStreamAudioSourceNode for the microphone 
        * */
        microphone = audioContext.createMediaStreamSource(stream);
        /** 
        * connect the AudioBufferSourceNode to the gainNode 
        * */
        microphone.connect(processor);
        encoder = new Mp3LameEncoder(audioContext.sampleRate, 160);
        /** 
        * Give the node a function to process audio events 
        */
        processor.onaudioprocess = function(event) {
          encoder.encode(getBuffers(event));
        };

         stopBtnRecord = function() {
          console.log('stopBtnRecord');
          isRecording = false;
          //startBtn.removeAttribute('disabled');
          //stopBtn.setAttribute('disabled', true);
          audioContext.close();
          processor.disconnect();
          tracks.forEach(track => track.stop());
          
          var blob =encoder.finish();
          uploadMedia(blob);
          //audioElement.src = URL.createObjectURL(encoder.finish());
        };

      }

      this.stopRecord = function() {
        stopBtnRecord();
      };

      let logError = (error) => {
        alert(error);
        console.log(error);
      }

}  

function uploadMedia(blob){
   var formData = new FormData();
       formData.append('recfile', blob);
 
  //$("#innerLoaderDiv").show();
   var urlMedia  = 'ajax/assignment-audio-feedback.php?aid=<?php echo $_REQUEST["aid"]?>';
  var base_dir = '<?php echo WEB_IP?>/trainer/assignment_audio_feedback/';
  $.ajax({
    url: urlMedia,
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function(res) { 
      if(res.status){
        $('#audio_feedback').val(res.filename);
        $('.audio-feedback-preview').attr('src', base_dir+res.filename);
      }else{
        alert(res.status)
      }
    }

  })

}



   var countryData='<?php echo $countryData; ?>';
   var type='<?php echo $studentData->type; ?>';
   var defaultProfilePath='<?php echo $profileImgDefault; ?>';
   var expiry_date='<?php echo $expiryDate; ?>';
   
   
   
   $(function () {
	  $("#divDate1").datepicker({ 
	  	startDate: new Date(),
	    autoclose: true, 
	    todayHighlight: true,
	    format: 'dd-mm-yyyy',
	  }); 
	});
   
   
   
   
   window.setTimeout(function() {
       $(".alertHide").fadeTo(500, 0).slideUp(500, function(){
           $(this).remove(); 
       });
   }, 4000);
   
  $("#assignment_file").on('change', function(){
          
      var validExtensions = <?php echo $exts?>; //array of valid extensions
      var notAllExtensions = <?php echo $notAllowedExts?>; //array of valid extensions
      var fileName = this.files[0].name;
      var fileNameArray = fileName.split('.');
      for(var i=0;i<notAllExtensions.length;i++){
         if($.inArray(notAllExtensions[i], fileNameArray) == 1){
            this.type = '';
              this.type = 'file';
             // $('#'+viewId).attr('src',"");  
              alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
              //$('#'+viewId).attr('src',defaultProfilePath); 
             return false;
         }
      }
    
      var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
       if ($.inArray(fileNameExt, validExtensions) == -1) {
           this.type = '';
           this.type = 'file';
          // $('#'+viewId).attr('src',"");  
           alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
           //$('#'+viewId).attr('src',defaultProfilePath); 
          return false;
          
       } 
   });
   
  
   function selectBatch(batch_id){

   	 if(batch_id!=''){
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getlevel",
				data: {batch_id:batch_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>},
				
				success: function(res) { 
				   	$('#level_id').html(res);
				
				    hideLoader();
				   
				}
   		  });
   	
      }
   }
   function selectLevel(level_id){
   	let batch_id = $('#batch_id').val();
   	 if(level_id!=''){
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=gettopic",
				data: {batch_id:batch_id,level_id:level_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>},
				
				success: function(res) { 
				   	$('#topic_id').html(res);
				    hideLoader();
				   
				}
   		  });
   	
      }
   }
   function selectTopic(topic_id){
   	let batch_id = $('#batch_id').val();
   	 if(topic_id!=''){
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getchapter",
				data: {batch_id:batch_id,topic_id:topic_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>},
				success: function(res) { 
				   	$('#chapter_id').html(res);
				    hideLoader();
				   
				}
   		  });
   	
      }
   }


</script>