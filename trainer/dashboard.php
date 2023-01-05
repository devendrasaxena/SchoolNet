<?php include_once('../header/trainerHeader.php');
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <?php echo $language[$_SESSION['language']]['home']; ?></li>
</ul><div class="clear"></div>
 <section class="padder">
  <section class="panel panel-default marginBottom40">

		  <div class="panel-body">
		   <p></p>
		   <p> <?php echo $language[$_SESSION['language']]['state_name']; ?> : <span class="font-bold"><?php echo $centerName;?></span></p>
		 </div>        

       </section>
	<section class="panel panel-default  marginBottom5">
	   <header class="panel-heading font-bold b-light" style="overflow: auto;">
		<div class="col-md-8 padd0"><?php echo $language[$_SESSION['language']]['summary']; ?></div>
		 </header> 
		<div class="row m-l-none m-r-none bg-light lter">
		 
		  <a title="<?php echo $language[$_SESSION['language']]['classes']; ?>">
			 <div class="col-sm-6 col-md-6 padder-v b-r b-light text-center">
			 <span class="fa-stack fa-2x  m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-info"></i>
			  <i class="fa fa-columns fa-stack-1x text-white"></i>
			</span>
			<span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="batchCount"><?php echo $teacherBatchCount; ?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['classes']; ?></small>
			</span>
			
		  </div></a>
		  
		  <a href="studentList.php" title="<?php echo $language[$_SESSION['language']]['learners']; ?>">
		  <div class="col-sm-6 col-md-6 padder-v b-r b-light lt  text-center"> 
		  <span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-warning"></i>
			  <i class="fa fa-user fa-stack-1x text-white"></i>
			</span>
		<span class="inlineBlock">
			 
			 <span class="clear h3 m-t-xs"><strong id="firers"><?php echo $totalStudent; ?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['learners']; ?></small>
			  
			</span>
		
		  </div>	</a>
		  <div class="col-sm-6 col-md-4 padder-v  b-light  text-center hide">                     
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-danger"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span>
			<span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalTestAttempted"><?php echo $totalTestAttempted; ?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['modules_attempted']; ?></small>
			  </span>
		  </div>
		
		</div>
	  </section>
    <section class="panel panel-default marginBottom40  hide">
                    <header class="panel-heading font-bold">Performance during last week</header>
                    <div class="panel-body">
                      <div id="flot-1ine" style="height:210px"></div>
                    </div>        
		           <footer class="panel-footer bg-white no-padder" style="display:none">
                      <div class="row text-center no-gutter">
                        <div class="col-xs-3 b-r b-light">
                          <span class="h4 font-bold m-t block">5,860</span>
                          <small class="text-muted m-b block">Orders</small>
                        </div>
                        <div class="col-xs-3 b-r b-light">
                          <span class="h4 font-bold m-t block">10,450</span>
                          <small class="text-muted m-b block">Sellings</small>
                        </div>
                        <div class="col-xs-3 b-r b-light">
                          <span class="h4 font-bold m-t block">21,230</span>
                          <small class="text-muted m-b block">Items</small>
                        </div>
                        <div class="col-xs-3">
                          <span class="h4 font-bold m-t block">7,230</span>
                          <small class="text-muted m-b block">Customers</small>                        
                        </div>
                      </div>
                    </footer>
                  </section>
</section>   

<?php include_once('../footer/trainerFooter.php');?>
