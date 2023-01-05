<?php 
include_once('header/lib.php');
 ?>
<!doctype html>
<html lang="en" class="loginRegSection">
  <head>
     <Meta HTTP-EQUIV = "Cache-Control:max-age=2628000,public" />
	<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<title><?php echo APP_NAME;?></title>

	<link rel="shortcut icon" href="images/favicon.ico" type="image/vnd.microsoft.icon"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
	<!--<link rel="stylesheet" type="text/css" href="css/app.css"/>-->
	<link rel="stylesheet" type="text/css" href="css/animate.css"/>
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/font.css"/>
	<link rel="stylesheet" type="text/css" href="css/login.css?<?php echo date('Y-m-d'); ?>"/>
	<?php if(SHOW_REGION_THEME==1 && client_reg_id!=''){?>
		<link rel="stylesheet" href="css/theme<?php echo client_reg_id;?>.css"/>
	<?php }?>
    <?php if(SHOW_THEME==1): ?>
		<link rel="stylesheet" href="css/theme.css"/>
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
  <body class="bgDiv">
   <div id="loaderDiv" class="submitPopup">
   <div class="overlay"></div><div class="loaderImageDiv"></div>
  </div>
    <section class="vbox">
	<div class="header relative">
		<div class="logo"><img src="<?php echo applogo; ?>" class="logoImg ">
		<?php if($is_secondary_logo==1){?>
		
		  <img class="logoImg2" src="<?php echo SECONDARY_LOGO; ?>" />
	     
		<?php }?></div>
		<div class="headerRight hidden-xs">
		  <div class="logo2"> </div>
		  </div>
	</div>
	<!--<section>
      <section class="hbox stretch">-->
	  
 <section id="contentDiv" class="contentDiv">
   <section class="vbox vBoxContent">
     

<script src="js/jquery.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>