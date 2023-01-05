<?php
	header('Content-Type: text/html;charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');
	$_html_relative_path='../';//dirname(dirname(__FILE__)).'/';
	$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
	// $_html_relative_path;exit;
	include_once($_html_relative_path.'header/lib.php');
	if(!isset($_SESSION['user_id'])){
		header('location:'.$_html_relative_path.'index.php');
	}
if($_SESSION['role_id']!=2){
	header('location:'.$_html_relative_path.'index.php');
	exit;

}
	include_once($_html_relative_path.'header/global.php');
?>
<!DOCTYPE html>
<html lang="en-GB" class="app loginRegSection">
  <head>
    <!-- Required meta tags -->
  <Meta HTTP-EQUIV = "Cache-Control:max-age=2628000,public" />
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<title><?php echo APP_NAME;?></title>
	
	<link rel="shortcut icon" href="<?php echo $_html_relative_path; ?>images/favicon.ico" type="image/vnd.microsoft.icon"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/app.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/animate.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/font.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/common.css?<?php echo date('Y-m-d'); ?>"/>
   <?php if(SHOW_REGION_THEME==1 && client_reg_id!=''){?>
		<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>css/theme<?php echo client_reg_id;?>.css"/>
	<?php }?>
    <?php if(SHOW_THEME==1): ?>
		<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>css/theme.css"/>
	<?php endif;?>
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!-- Le styles -->
	<!-- Le fav and touch icons -->

	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

  </head>  
<?php
if($_SESSION['role_id']==1){
	$menu_relative='trainer/';
   include_once('trainer_menu.php');
  
}elseif($_SESSION['role_id']==4){
	$menu_relative='centerAdmin/';
   include_once('center_menu.php');
  
}elseif($_SESSION['role_id']==7){
	$menu_relative='admin/';
   include_once('admin_menu.php');
  
}elseif($_SESSION['role_id']==3){
	$menu_relative='admin/';
   include_once('admin_menu.php');
  
}else{ 
	include_once('learner_menu.php');	
}

?>
 <section id="contentDiv" class="contentDiv">
        <section class="vbox vBoxContent">          
          

 <script src="<?php echo $_html_relative_path; ?>js/jquery.min.js"></script>
	<script src="<?php echo $_html_relative_path; ?>js/popper.min.js"></script>
	<script src="<?php echo $_html_relative_path; ?>js/bootstrap.min.js"></script>
<?php 
if(isset($_SESSION['user_id'])){ ?>

	<script type="text/javascript">
		localStorage.setItem("user_id","<?=$_SESSION['user_id']?>");
	</script>
<?php 
}
?>	