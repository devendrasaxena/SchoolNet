	
	 <!--  </section>
	</section>-->
	 </section>
	</section>
</section>
<?php include_once "alertPopup.php"; ?>  
  <!-- App -->
 <!--<script src="js/app.js"></script>-->
  <script src="js/parsley/parsley.min.js"></script>
  <script src="js/parsley/parsley.extend.js"></script>
  <script src="js/common.js"></script>
 
 </body>
</html>
<script>
/* $(".reveal").on('click',function() {
    var $pwd = $(".password");
    if ($pwd.attr('type') === 'password') {
        $pwd.attr('type', 'text');
		$(".reveal > i").removeClass('fa fa-eye-slash').addClass('fa fa-eye');
    } else {
        $pwd.attr('type', 'password');
		$(".reveal > i").removeClass('fa fa-eye').addClass('fa fa-eye-slash');
    }
});
 */
function resizeWindow(){
	  var winHeight =$(window).height();
	  var docHeight =$(document).height();
		if(docHeight > 607){
		
	  }
	  if(docHeight<608){
		//alert(winHeight+"R Default")
		
	 }
	 if(winHeight<608){
		//alert(winHeight+"R Default")
		
	 }	 
	 
 }

resizeWindow();
$( window ).resize(function() {
  resizeWindow();
});
$(document).ready(function(){
 resizeWindow();
  $("#preLoaderPage").delay(0).fadeOut();
  $("#loaderDiv").delay(0).fadeOut();
});
window.onresize = function (event) {
  resizeWindow;
 
}

</script>
