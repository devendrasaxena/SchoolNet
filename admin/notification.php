<?php include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
//error_reporting(E_ALL);ini_set('display_erros',1);
ini_set('max_execution_time', 0);
 $user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
$uid = base64_encode($user_id); 
$noti_id = isset($_GET['id'])?$_GET['id']:0;
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
	<iframe class="notification" src="https://wfpstaging.englishedge.in/new/notification_ui/notification-theory.php?user_id=<?php echo $uid.'&id='.$noti_id ?>&lang=<?php echo $_SESSION['language']?>" width="100%" height="1200px" style="border:none;" id="iframe1" frameborder="0" scrolling="yes" onload="resizeIframe(this)" style="
        border:none;"></iframe>
</section>
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
</script>