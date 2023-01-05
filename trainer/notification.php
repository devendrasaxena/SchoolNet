<?php include_once('../header/trainerHeader.php');
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
 <iframe class="notification" src="https://wfpstaging.englishedge.in/ilt/notification_ui/notification-theory.php?user_id=<?php echo $uid?>&lang=<?php echo $_SESSION['language']?>&id=<?php echo $noti_id ?>" width="100%" height="1200px" style="border:none;" id="iframe1" frameborder="0" scrolling="yes" onload="resizeIframe(this)" style="
        border:none;"></iframe>
</section>
 
<?php include_once('../footer/trainerFooter.php');?>
<script>
  
/*  var boxDivWidth=$(".allTopicDiv").css(width);
boxDivWidth=boxDivWidth-15;
boxDivWidth=boxDivWidth/3;
$(".topicHead").css("width",boxDivWidth+"%"); */



// start code for search added by sb

const app ={};

app.get = (t, a) => {
    $.ajax({
      type: "GET",
      url: t,
      success: a
    });
  };


app.post = (t, a, e, o = !1) => {
    o
      ? $.ajax({
          type: "POST",
          url: t,
          data: a,
          contentType: !1,
          processData: !1,
          success: e
        })
      : $.ajax({
          type: "POST",
          url: t,
          data: a,
          success: e
        });
 };


function _search(t){
 var _input = $('.search-input').val();
	 if(_input.length>1){
	 	app.get('_search.php?input='+_input,(res)=>{
	 		console.log(res);
			if(res.err){
				alert("no record found!");
			}else{
				str = '';
				for(i in res.data)
					str += res.data[i].table;
				alert(str);
			}
	 	});
	 }
}

// end code for search added by sb

function setNoti(id){
		if(id == 2){
			jQuery('.notification').addClass('hide');
			jQuery('.feedback').removeClass('hide');
		}else{
			jQuery('.feedback').addClass('hide');
			jQuery('.notification').removeClass('hide');
		}
}


  function resizeIframe(obj) {
    /* obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px'; */
  }
</script>

