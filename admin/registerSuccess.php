<?php include_once('../header/adminHeader.php');?>	
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="centerList"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['state']; ?> </a></li>
</ul>
<div class="clear"></div>
<section class="padder">
 <div class="row-centered">
   <div class="col-sm-10 col-xs-12 col-centered">
   <section class="marginBottom40">
	  <div class="panel panel-default noneBorder">
       <div class="panel-body">
        <h5 class="text-center fontGreen" style="color: #11a11a;">Registration is successful. Following are your login credentials.</h2>		
	  <div class="form-group">
	    <div class="row">              
		  <div class="col-sm-12 paddTop10">
		    <p class="page-normal-text " align="center"> Username: <?php echo $_SESSION['email_id']; ?>  </p><br />
			<p class="page-normal-text " align="center"> Password: <?php echo $_SESSION['password']; ?> </p>
			</div>
		</div>
		<div class="col-sm-12 paddTop10 marginBottom40">
		  <div class="text-center"> 
		     <a href='centerList.php' class="btn btn-primary ">Back to <?php echo $language[$_SESSION['language']]['states']; ?></span> </a>
			 <a href='createCenter.php' class="btn btn-primary ">Back to Add <span class="capitalize"><?php echo $language[$_SESSION['language']]['state']; ?></span> </a>
		  </div>
	   </div>
    </div>
   </div>
</div>
</section>
</div>
</div>
</section>
<?php include_once('../footer/adminFooter.php'); ?>
