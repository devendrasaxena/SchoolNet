
  <ul class="nav nav-tabs" style="margin-bottom:40px;">

    <li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'reports.php') !== false){?>class="active" <?php } ?> href="reports.php" title="<?php echo 'Centers Reoprt'; ?>"><?php echo $language[$_SESSION['language']]['states_report']; ?></a></li>

	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'users_report.php') !== false){?> class="active" <?php } ?> href="users_report.php" title="<?php echo $language[$_SESSION['language']]['users_report']; ?>"><?php echo $language[$_SESSION['language']]['users_report']; ?></a></li>
	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'users_report_schoolnet.php') !== false){?> <?php } ?> href="users_report_schoolnet.php" title="<?php echo 'Download Course Completion Report'; ?>"><?php echo 'Download Course Completion Report'; ?></a></li>
	
	
	<!--
    <li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'learning_objective_report.php') !== false){?> class="active" <?php } ?> href="learning_objective_report.php" title="<?php echo $language[$_SESSION['language']]['perfomance_report']; ?>"><?php echo $language[$_SESSION['language']]['perfomance_report']; ?></a></li>
	

	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'time_spent_report.php') !== false){?> class="active" <?php } ?> href="time_spent_report.php" title="<?php echo $language[$_SESSION['language']]['time_spent_report']; ?>"><?php echo $language[$_SESSION['language']]['time_spent_report']; ?></a></li> 
   
 
   <li class="textUpper hide"><a class="<?php if(strpos($_SERVER['REQUEST_URI'], 'state_ranking_report.php') !== false){?> active <?php } ?>
	 " href="state_ranking_report.php" title="<?php echo $language[$_SESSION['language']]['state_ranking_report']; ?>"> <?php echo $language[$_SESSION['language']]['state_ranking_report']; ?></a></li>
-->

    <li class="textUpper"><a class="<?php if(strpos($_SERVER['REQUEST_URI'], 'statewise-users.php') !== false){?> active <?php } ?>
	 " href="statewise-users.php"  title="<?php echo $language[$_SESSION['language']]['graphical_reports']; ?>"><?php echo $language[$_SESSION['language']]['graphical_reports']; ?> </a></li>
 </ul>
