<?php  include_once 'header/header.php';
//echo "<pre>";print_r($checkScore);
$score=78;
$user_start_level=1;
$user_current_level=4;
$user_current_description="Advanced Score";
$user_current_mapto="B1";;

$getRange=$user_current_level;
$advance=$user_current_description;
$gse="GSE";
$cefr="CEFR";
$grade=$user_current_mapto;
$myScore=$score;
$totalScore="100"; 
$level='Level '.$getRange;
$levelJump=$level;
//echo "<pre>";print_r($courseArr);
$enableRange=count($courseArr);
//echo $enableRange;
$courseRangeArr=array();
foreach($courseArr as $key=>$val){
 $courseRangeArr[]=$val['course_id'];
}


//echo "<pre>";print_r($_POST);exit;	
if(isset($_POST['courseEdge']) && ($_POST['courseEdge'] != '') ) {
	$cid=$_POST['courseEdge'];
	$lid=$_POST['lid'];
	if($firstTime_login==''){
		$path="generic_welcome.php?cid=".$cid."&lid=".$lid;
		  header('Location:'.$path);
		  exit;
	}else{
		if(walkThrough==1){
		  $path="generic_welcome.php?cid=".$cid."&lid=".$lid;
		  header('Location:'.$path);
		  exit;
		}else{ 
	       header("Location: user/dashboard.php");
	       exit;
		 }
		
	}
}
?> 
 <form method="post" autocomplete="off">
  <div class="scoreDiv">
		<div class="scoreBox">
		<div class="scoreMsgDiv">
		<div class="scoreMsg">
			Congratulations <?php echo $username; ?>, you have achieved an <span class="bold"><?php echo $advance; ?></span> on  <span class="bold"><?php echo $gse; ?></span>  and <span class="bold"><?php echo $grade; ?></span> in <span class="bold"><?php echo $cefr; ?></span>
			</div>
		<div class="clear"></div>
		  <div class="text-left relative taketestDiv"><img src="images/baseline-help.svg"
     class="baseline-help"><a class="taketest" href="javascript:void(0)" tabindex="2" onclick="return testInst();">What is <?php echo $gse; ?> and <?php echo $cefr; ?> ?</a></div>
		</div>
		<div class="markBox">
			<div class="score"> <span id="myScore" class="myScore"><?php echo $myScore; ?></span></span><span class="scorLine">/</span><span  id="totalScore" class="totalScore"><?php echo $totalScore; ?></span></div>
			<div class="clear"></div>
			<div class="test-Score">TEST SCORE</div>
		</div>
	 </div>
      <div class="levelBox">
	  <div class="levels">Levels</div>
	   <div class="levelsRange">
		<ul class="rangeList">
		<?php $i=1;
		  //in_array($val['code'],$arrAssignCourse);
		
		//$courseEdge=$courseRangeArr[$getRange-1];
		//echo $courseEdge;
		 for($i=1;$i<=$levelTotalRang;$i++){
			 if($getRange==$i){
				$active="active"; 
				$link=$courseRangeArr[$i-1];
				$levelValue=$getRange;
                $courseEdge=$link;
				$range=$levelValue;
			 }else{
				 $active="";
				 $link=$courseRangeArr[$i-1];
				 $levelValue=$i;				
			 }
			 ?>
		  <li class="<?php echo $active;?>" link="<?php echo $link;?>"><?php echo $i;?></li>
		<?php } ?>
		</ul>
	   </div>
	  </div>
	  <div class="clear"></div>
	  <div class="basedScore">Based on your score, you can directly jump to <?php echo $levelJump;?> </div>
	  <div class="clear"></div>
		  <div class="btnDiv">
			 <div class="btnBg continueBtnBg">
			    <input type="hidden" name="courseEdge" id="courseEdge" value="<?php echo $courseEdge;?>">
				<input type="hidden" name="lid" id="lid" link="<?php echo $courseEdge;?>" value="<?php echo $range;?>">
			   <button type="submit" id="continueBtn" class="btn continueBtn link">Continue</button>
			  </div>
		</div>
	</div>
    </form>   
<?php include_once 'footer/footer.php';?>

<!-- showRangePopup Test popup-->						 
<div id="showRangePopup" class="modal fade showPopup" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content" style="border-radius: 25px;">
		 <div class="modal-header">
			<button type="button" class="close closeIcon" data-dismiss="modal">&times;</button>
			
		  </div>
			<div class="modal-body">
			
		  <div class="text-left paddTop20 fontSize18" >
            <h3 class="header">What is GSE and CEFR ?</h3>
		     <p>The Global Scale of English (GSE) is the first truly global English language standard. It extends the Common European Framework of Reference (CEFR) by pinpointing on a scale from 10 to 90 on what needs to be mastered for the four skills of speaking, listening, reading and writing within a CEFR level, using a more granular approach.
			 </p>
			 <p>
		   <img src="images/level-slider-pop-up.jpg" class="responsive"/></p>
			</div>
			
		</div>
	 </div>
  </div>
</div>	
<script>
$(document).ready(function () {

	$('.header').css("box-shadow"," 0 0 10px 0 #e5e7ee");	
	$(".link").click(function () {
	// var level=$(".rangeList").find("li.active").html();
	// var link = $(".rangeList").find("li.active").attr("link");
	 $("#lid").val(level);
	 $("#lid").attr("link",link);
	  $("#courseEdge").val(link);
	 
	 // window.location.href="generic_welcome.php?cid="+link+"&lid="+level;
	 
	});
});

function testInst(){
 $('#showRangePopup').modal({
		backdrop: 'static',
		keyboard: true, 
		show: true
		});	
		
}

	
/* ############### Vertically center Bootstrap 3 modals so they aren't always stuck at the top ######## */
function centerModal() {
    $(this).css('display', 'block');
    var $dialog = $(this).find(".modal-dialog");
    var offset = ($(window).height() - $dialog.height()) / 2;
    // Center modal vertically in window
    $dialog.css("margin-top", offset);
}

$('.modal').on('show.bs.modal', centerModal);
$(window).on("resize", function () {
    $('.modal:visible').each(centerModal);
});

$('[data-toggle="tooltip"]').tooltip();		
		
</script>
