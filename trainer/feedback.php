<?php include_once('../header/trainerHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
$uid = base64_encode($user_id);
?>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <!--h3>Notifications</h3-->
	<!-- <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>-->
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
 <!--<a class="btn" href="../user/feedback.php"
 style="float:right; cursor: pointer; margin-top: 10px;" 
 data-placeholder="Video" data-paceholderid="1"
  data-div="feedbackForm" 
  title="<?php echo $language[$_SESSION['language']]['share_feedback']; ?>">
  <?php /*echo $language[$_SESSION['language']]['share_feedback']; */?>                   
   </a>-->
 <iframe class="notification"  src="https://wfpstaging.englishedge.in/ilt/notification_ui/admin-feedback.php?user_id=<?=$uid?>&lang=<?=$_SESSION['language']?>" width="100%" height="1000px" style="border:none;" id="iframe1" frameborder="0" scrolling="yes" onload="resizeIframe(this)" style="overflow: hidden; height: 100%;
        border:none;overflow:hidden;width: 100%;height:100vh; position: abosulte;"></iframe>
  </section>
<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>
<?php include_once('../footer/centerAdminFooter.php'); ?>
<script>
  function resizeIframe(obj) {
   /*  obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px'; */
  }
</script>