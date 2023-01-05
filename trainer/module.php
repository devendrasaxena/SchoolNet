<?php include_once('../header/trainerHeader.php');
$objILT = new ILT();
$objTR = new trackController();
$proObj = new productController();

$batchId=$_SESSION['batch_id'];
$productId=$_SESSION['product_id'];
$getCourseProductData=$centerObj->getBatchDataByIDDetails($batchId,$center_id,$productId);

$productInfo=$proObj->getProdcutDetailById($productId);
//echo "<pre>";print_r($getCourseProductData); exit; 
$product_name=$productInfo['product_name'];
$master_products_id= $productInfo['master_products_ids'];

$batchCourseStr1 = $getCourseProductData[0]['course'];
$customTopic = $getCourseProductData[0]['topic'];
$customChapter = $getCourseProductData[0]['chapter'];

$group_act_chapter_ids = $objILT->getAllGroupActivityChaptersID();
$batchId=$_SESSION['batch_id'];
$chaps_comp_per = $objILT->getUserSessionStatusBatch($user_id, $batchId);

$product_standard_id=$_SESSION['product_id'];
if($product_standard_id==10){
	$COURSE_NAME="Class";
	$COURSE_NAMES="Classes";
 }else{
   $COURSE_NAME="Level";
   $COURSE_NAMES="Levels"; 
 }						
ini_set('max_execution_time', '0');
if(isset($_GET['cid'])){
    $course_id = trim($_GET['cid']);
	$courseEdge= trim($_GET['cid']);
	$_SESSION['course_id']=trim($_GET['cid']);
	$courseInfo=$assessmentObj->getCourseByCourseId($course_id);
}
if(isset($_GET['lid'])){
    $level_id = trim($_GET['lid']);
	$level=$COURSE_NAME." ".$level_id;
	 
}else{
	$level_id = $getRange;
	$level=$COURSE_NAME." ".$getRange;
}
if(isset($_GET['tEdge_id'])){
    $topic_edge_id = trim($_GET['tEdge_id']);
	$_SESSION['topic_edge_id']=$topic_edge_id;
}
if(isset($_GET['nid'])){
    $tree_node_id = trim($_GET['nid']);
	$_SESSION['nid']=$tree_node_id;
}
if(isset($_GET['tcount'])){
    $tCount = trim($_GET['tcount']);
	if($tCount<10){
	 $topicCount="0".$tCount;
	}else{
	 $topicCount=$tCount;  
	}
}

/* $arrVisit = array();
$arrVisit['token'] = $userToken;
$getVisitLevelArr =  $objTR->getVisitLevel($arrVisit);
 if($getVisitLevelArr==0){
  $modulePath= "learning_module.php?cid=".$course_id."&lid=".$level_id;    
 
}else{
  $modulePath= "learning_module.php?cid=".$course_id."&lid=".$getVisitLevelArr;    
}	 */

$modulePath= 'learning_module.php?cid='.base64_encode($course_id).'&lid='.base64_encode($level_id); 


$topic=$assessmentObj->getTopicName($tree_node_id);
//echo "<pre>";print_r($topic);
 $assessment_type=$topic[0]->assessment_type;
$topic_label=$topic[0]->topic_label;
 if($assessment_type==''){
  $topicName=$topic[0]->name;
  $topicDescription=$topic[0]->description;
 } 
$chaptersArr = $assessmentObj->getChapterByTopicEdgeId($topic_edge_id,$customChapter);
//echo "<pre>";print_r($courseArr);exit;
$col  = 'sequence_no';
$sort = array();
foreach ($chaptersArr as $i => $obj) {
	  $sort[$i] = $obj->{$col};
	}
array_multisort($sort, SORT_ASC, $chaptersArr);

//echo "<pre>";print_r($chaptersArr);
$totalCh=count($chaptersArr);

$firstVisitArr=json_decode($firstVisit);

if($firstVisitArr[2]->tag=='module' && $firstVisitArr[2]->visit=='0') {
    $show_popup=0; ////1 show popup 
}else{
	if(walkThrough==1){
	   $show_popup=0;////1 show  popup
	}else{
	  $show_popup=0;////0 hide  popup
	}	
}

//echo "<pre>";print_r($_POST);exit;	
if(isset($_POST['courseEdge']) && ($_POST['courseEdge'] != '') ) {
	$firstlogArr=array();
	$genericArr=array('tag' =>'generic-welcome','visit' =>'1');
	$firstlogArr[]=$genericArr;
	$dashArr=array('tag' =>'dashboard','visit' =>'1');
	$firstlogArr[]=$dashArr;
	$modulArr=array('tag' =>'module','visit' =>'1');
	$firstlogArr[]=$modulArr;
	$logArr=array('tag' =>'firstLog','visit' =>'1');
	$firstlogArr[]=$logArr;
	$firstlog=json_encode($firstlogArr);	
     $data=firstTimeLogin($firstlog,$userId);
	 
	 if($data){
		$path="module.php?cid=".$_GET['cid']."&lid=".$_GET['lid']."&tEdge_id=".$_GET['tEdge_id']."&nid=".$_GET['nid']."&tcount=".$_GET['tcount'];
		header("Location:".$path);
		exit;
	 }
}
$course_code='CRS-'.$course_id;	

?>
<section class="scrollable padderNone">
<form method="post" autocomplete="off">

	<div class="moduleHeader relative">
	 <div class="leftSide">
	     <div class="welcomePara">
		 <a href="<?php echo $modulePath; ?>"><?php echo $courseInfo[0]['title'];?> : Learning Modules </a> 
		 </div>
		   <div class="welcomeDiv"><?php echo $topicName; ?>
		<!-- Module <?php echo $topicCount;?>-->
		 </div>
		 
		 
	 </div>
	  <div class="rightSideOther">
		<div class="pull-right rightImg">
			<img src="<?php echo $_html_relative_path; ?>images/right-top.png" /> 
		</div>
	 </div>
	
	</div> 
       <div class="clear"></div>
		<div class="moduleRightOtherBg"><div class="moduleRightMidOtherBg">&nbsp;</div></div> 
	<div class="clear"></div>
	
   <div class="padder2055 marignRight40 top0 relative componentDiv">	
       <div class="module">
		  <div class="topicHead">
				<!--<div class="title"><?php echo $topicName; ?></div>-->
				<div class="description" title="<?php echo $topicDescription; ?>"><?php echo $topicDescription; ?><!--This module revolves around Bharat, who is applying for a job and seeks support from his friend Anjali to improve his CV.--></div>			
			   <div class="view" style="display:none;">View Story</div> 
			 </div>
		</div>
        <div class="clear"></div>
		<div class="moduleList">
			<?php 
				if($totalCh >0){
					$j=1; 
					for($j=1;$j<=$totalCh;$j++){
						$ch_edge_id=$chaptersArr[$j-1]->edge_id; 
						$ch_tree_node_id=$chaptersArr[$j-1]->tree_node_id; 
						$chName=$chaptersArr[$j-1]->name; 
						$chDescription=$chaptersArr[$j-1]->description; 
						$thumnailImg=$chaptersArr[$j-1]->thumnailImg;
						if($thumnailImg!=''){
							 $chapterImg=$thumnail_Img_url.$thumnailImg;
						 }else{
								//$chapterImg=$_html_relative_path."images/chapter/ch-".$j.".png"; 
							$chapterImg=$_html_relative_path."images/defaultChapter.png";
						 }
						 
						$chIconLeft=$_html_relative_path."images/chapter/ch-".$j."-icon1.png";
						$chIconRight=$_html_relative_path."images/chapter/ch-".$j."-icon2.png";
						$chapterSkill=$chaptersArr[$j-1]->chapterSkill;
						//$quesCount=$chaptersArr[$j-1]->quesCount;
						//$duration=$chaptersArr[$j-1]->duration;
						
						//$chTextLeft=$duration." "."mins";
						//$chTextRight=$quesCount." "."Questions";
						$componenturl="iltComponent.php?cid=".$_GET['cid']."&tEdge_id=".$_GET['tEdge_id']."&nid=".$_GET['nid']."&batch_id=".$batchId."&cEdge_id=".$ch_edge_id."&chtid=".$ch_tree_node_id."&lid=".$level_id."&tCount=".$tCount."&cCount=".$j;
						
						$arr = array();
						$arr['edge_id'] = $ch_edge_id;//chapter edge id
						$arr['userToken'] = $userToken;
						$arr['package_code'] = $package_code;
						$course_code="CRS-".$course_id;
						$arr['course_code'] = $course_code;
						$arr['batch_id'] = $batch_id;
						
					    $returnCompletion=  $objTR->getCompletion($arr);
						//echo "<pre>";print_r($returnCompletion);//exit;	
						$completeChapterStatus = $returnCompletion['completion_status'];
						//$completeChapterPer = !empty($returnCompletion[$ch_edge_id]['complete_per'])?$returnCompletion[$ch_edge_id]['complete_per']:0;
						//echo "<pre>";print_r($completeChapterStatus); 
						if($completeChapterStatus=="na"){
							$completeChapterStatusImg=$_html_relative_path."images/blank1.png";
						     $chap_comp_per=0;
						}else if($completeChapterStatus=="nc"){
							$completeChapterStatusImg=$_html_relative_path."images/half.png";
							$chap_comp_per=50;
						}else if($completeChapterStatus=="c"){
							$completeChapterStatusImg=$_html_relative_path."images/full.png";
						    $chap_comp_per=100;
						} else {
							$completeChapterStatusImg=$_html_relative_path."images/blank1.png";
						    $chap_comp_per=0;
						} 
						
						if($j<10){
							$chapterCount	="0".$j;
						  }else{
							$chapterCount	=$j;  
						}						
						//$completeChapterStatusImg=$_html_relative_path."images/blank1.png";
						//$completeChapterPer="";
						//$chap_comp_per = isset($chaps_comp_per) ? $chaps_comp_per : 0;
                        ?>
						<div class="boxDiv" id="boxDiv<?php echo $j; ?>" skill="<?php  echo $chapterSkill;?>"> 
						 <div class="box" id="chBoxDiv<?php echo $j; ?>">
						 <a id="chapterLink<?php echo $j; ?>" path="<?php echo $componenturl; ?>" href="javascript:void(0)" onclick="componentLink(this.id,'<?php echo $ch_edge_id;?>')">
						<div class="chLeftMain"> 
						<div class="chLeft">
							<div class="chImg chImg<?php echo $j; ?>"><img src="<?php echo $chapterImg; ?>"/></div>
					    </div>
					   
						<div class="chMid">
						  <div class="chTitle chTitle<?php echo $j; ?>"><?php echo displayText($chName); ?></div>
						  <div class="chDescription chDescription<?php echo $j; ?>"><?php echo displayText($chDescription);//echo truncateString($chDescription,chDescription); ?></div>
						   <div class="chIcon chIcon<?php echo $j; ?>">
							 <span class="chIconLeft hide"><img src="<?php echo $chIconLeft; ?>"/> <span class="chTextLeft"><?php echo $chTextLeft;?></span></span>
							  <span class="chIconRight hide"><img src="<?php echo $chIconRight; ?>"/><span class="chTextRight"><?php echo $chTextRight;?></span> </span>
							   <span class="chIconRight">Activity :  <i class="fa fa-user"></i>
                              <?php if(in_array($ch_edge_id, $group_act_chapter_ids)):?>
                             <i class="fa fa-user"></i>
                             <?php endif;?></span>
						   </div>
						   <div class="chapterCount"><?php echo $chapterCount; ?></div>
						  </div>
						 </div>
						  <div class="chRight" totalCom="<?php echo $totalComponent;?>" completeChapterStatus="<?php echo $chap_comp_per;?>">
						   <!--<img style="width: 60px;vertical-align: middle;line-height: 60px; margin-top: 10px;" src="<?php //echo $completeChapterStatusImg; ?>"/> -->
					   
						   <div class="easypiechart" data-track-color="#f5f5f5" data-bar-color="#336187" data-percent="<?php echo $chap_comp_per.'%';?>" data-scale-color="#fff" data-line-cap="butt" data-line-width="7" data-loop="false">
						   <div class="easypie-text"><span id="completeComponent" class="goalAchieveTime"><?php echo $chap_comp_per.'%';?> </span></div>
						  </div>
						   
							<div class="arrowImgDiv">
							   <img src="<?php echo $_html_relative_path; ?>images/chArrowRight.png"/>
							 </div>
						  </div>
						  </a>
					  </div> 
						 <?php if($j==1){?>
						   <div class="boxBottomWalk"><div class="arrow-up"></div><p class="text-left">Click any of the tasks to start your learning journey</p><div class="clear"></div><div class="btnDiv text-left"><div class="btnBg continueBtnBg pull-left"><input type="hidden" name="courseEdge" id="courseEdge" value="<?php echo $courseEdge;?>"><button id="btnCloseNext" type="submit" onclick="closeNext()" class="btn continueBtn">Done</button></div></div></div>
						  <?php }?>
						</div>  
					<?php } ?> 
				
				<?php }else{?>
			 <div class="topic">Chapter is not available</div>
		  <?php }?>
			
		</div>

	</div>

 </form> 
 </section> 
<?php include_once('../footer/trainerFooter.php');?>
<!-- Easy Pie Chart -->
<script src="<?php echo $_html_relative_path; ?>js/charts/easypiechart/jquery.easy-pie-chart.js"></script>
<!-- showModulePopup popup-->						 
<div id="showModulePopup" class="modal fade showModulePopup" role="dialog">
	<div class="modal-dialog  modal-lg">
      <div class="modal-header">
		  </div>
		<!-- Modal content-->
		<div class="modal-content">
		
			<div class="modal-body">
			<button type="button" class="close closeIcon" data-dismiss="modal">&times;</button>
		  <div class="text-center" id="contentShow">
           </div>
			
		</div>
	 </div>
  </div>
</div>	


<script>
var course_code = <?php echo json_encode($course_code);?>; 	
var topic_edge_id = <?php echo json_encode($topic_edge_id);?>;
var level_id = parseInt(<?php echo json_encode($level_id );?>);
var getRange = <?php echo json_encode($getRange);?>;

var chapter_edge_id;
$(document).ready(function(){
var show_popup = <?php echo json_encode($show_popup);?>;
 var pieSize;
pieResize();
 function pieResize(){
 var winHt,winWd,docH,docWd;
  winWd=$(window).width();
 /*  if(winWd >= 991){
	 $(".easypiechart").attr('data-size','90');
	  $(".easypiechart").attr('data-line-width','7');
	  pieSize=90;
	}else  */
     if(winWd<=980 && winWd>=466){
	 $(".easypiechart").attr('data-size','80');
	  $(".easypiechart").attr('data-line-width','6');
	   pieSize=60;
   }else{
	 pieSize=50;
	  $(".easypiechart").attr('data-size','70');
	  $(".easypiechart").attr('data-line-width','4');
	}  
} 

// Attaching the event listener function to window's resize event
window.addEventListener("resize", pieResize); 

/* var boxDivWidth=$(".allTopicDiv").width();
boxDivWidth=boxDivWidth-70;
boxDivWidth=boxDivWidth/3;
$(".topicHead").css("width",boxDivWidth+"px"); */
 //$("#chBoxDiv1").removeClass("boxModuleZindex");
 $(".boxBottomWalk").removeClass("boxBottomModuleZindex");
 $(".boxBottomWalk").hide();
 $("#chBoxDiv1").after('');
 if( show_popup == 1){
/* startPopup */
 $('#showModulePopup').modal({
	backdrop: 'static',
	keyboard: true, 
	show: true
	});	
	/* end Popup */
	$("#contentShow").html('<h3 class="headerShow" >Welcome to your first module</h3> <p>All MePro modules are theme-based, set around work and social scenarios.<br>This module revolves around Bharat, who is applying for a job and seeks support from his friend Anjali to improve his CV</p><div class="clear"></div><div class="btnDiv"><div class="btnBg continueBtnBg"><button id="btnConceptNext" type="button" class="btn continueBtn" onclick="showNext()">Next</button></div></div><img src="<?php echo $_html_relative_path; ?>images/chList.png" style="max-width: 100%;height: 400px;"/>');

 }	
});
function showNext(){
	   $('#showModulePopup').modal('hide');
	   $(".boxZindexBG").show();
		$("#chBoxDiv1").addClass("boxModuleZindex");
		$(".boxBottomWalk").show();
		//$("#chBoxDiv1").after('<div class="boxBottomWalk"></div>');
		$(".boxBottomWalk").addClass("boxBottomModuleZindex");
	    //$(".boxBottomModuleZindex").html('');
		
    }
function closeNext(){
	    showLoader();
	    $(".boxBottomWalk").removeClass("boxBottomModuleZindex");
		//$(".boxBottomModuleZindex").html("");
		//$(".boxBottomWalk").html("");
		//$(".boxBottomWalk").hide();
		$("#chBoxDiv1").removeClass("boxModuleZindex");
		$(".boxZindexBG").hide();		
    }

	
$(function(){	
	// pie

  $('.easypiechart').easyPieChart({
	//barColor: '#ef1e25',
    trackColor: '#f2f2f2',
    scaleColor: '#dfe0e0',
    lineCap: 'round',
    rotate: 0,
    size: 90,
	responsive: true,
    lineWidth: 3,
    animate: false,
    delay: false,
    onStart: $.noop,
    onStop: $.noop,
    onStep: $.noop
    /* // The color of the curcular bar. You can pass either a css valid color string like rgb, rgba hex or string colors. But you can also pass a function that accepts the current percentage as a value to return a dynamically generated color.
    barColor: '#ef1e25',
    // The color of the track for the bar, false to disable rendering.
    trackColor: '#e5e5e5',
    // The color of the scale lines, false to disable rendering.
    scaleColor: '#e5e5e5',
    // Defines how the ending of the bar line looks like. Possible values are: butt, round and square.
    lineCap: 'round',
    // Width of the bar line in px.
    lineWidth: 0,
    // Size of the pie chart in px. It will always be a square.
    size: 40,
    // Time in milliseconds for a eased animation of the bar growing, or false to deactivate.
    animate: 1000,
    // Callback function that is called at the start of any animation (only if animate is not false).
    onStart: $.noop,
    // Callback function that is called at the end of any animation (only if animate is not false).
    onStop: $.noop */
  });
/*   $('.updatePieCharts').on('click', function(e) {
    e.preventDefault();
    var newValue = Math.floor(100 * Math.random());
    $('.timeChart').data('easyPieChart').update(newValue);
    $('span', $('.chart')).text(newValue);
  }); */
  
 
 
 
}); 
function componentLink(id,cEdgeId){
	var componentPath=$("#"+id).attr('path');
	console.log(cEdgeId);
	if(componentPath!==''){
	  
	  //if(level_id==getRange){

		chapter_edge_id =cEdgeId;
     /*  sendBookmarkTrackingData(chapter_edge_id);  */
	  //}
	  window.location.href=componentPath;
	}
}
function sendBookmarkTrackingData(chapter_edge_id){
	 console.log("bookmark Chapter");
	  $("#loaderDiv").show();
	  var data = {action: 'set_bookmark',course_code:course_code, topic_edge_id:topic_edge_id,$bookmark_type:'chapter', chapter_edge_id:chapter_edge_id,component_edge_id: '',other: ''};
        $.ajax({url: 'ajax/bookmark-ajax.php', type: 'post', dataType: 'json', data: data, async: false,
           success : function(data){
		  if(data.status==1){
			  //console.log(data.res)
			  $("#loaderDiv").hide();
		  }else{
			console.log(data.res)
		  }
		},
            error: function () {}
        }); 
 }
$(document).ready(function(){
  //chapter_edge_id ='';
  //sendBookmarkTrackingData(); 
});

 
</script>
