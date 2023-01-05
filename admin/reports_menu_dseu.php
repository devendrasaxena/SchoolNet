
  <ul class="nav nav-tabs" style="margin-bottom:40px;">

   <!-- <li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'reports.php') !== false){?>class="active" <?php } ?> href="reports.php" title="<?php echo $language[$_SESSION['language']]['states_report']; ?>"><?php echo $language[$_SESSION['language']]['states_report']; ?></a></li>-->
	
	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'reports_dseu.php') !== false){?> class="active" <?php } ?> href="users_report_dseu.php" title="<?php echo $language[$_SESSION['language']]['users_report']; ?>"><?php echo $language[$_SESSION['language']]['users_report']; ?></a></li>

	
    <li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'reports_dseu.php') !== false){?> class="active" <?php } ?> href="./users_attendance_report_dseu.php" target='_blank' title="<?php echo "Download Progress Report"; ?>"><?php echo "Download Progress Report"; ?></a></li>
	
	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'reports_dseu.php') !== false){?> class="active" <?php } ?> href="./users_prepost_test_report_dseu.php" target='_blank' title="<?php echo "Download Test Progress"; ?>"><?php echo "Download Test Progress"; ?></a></li>
	
	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'reports_dseu.php') !== false){?> class="active" <?php } ?> href="./users_credential_report_dseu.php" target='_blank' title="<?php echo "User Credentials Report"; ?>"><?php echo "User Credentials Report"; ?></a></li>
	
<!--
	<li class="textUpper"><a <?php if(strpos($_SERVER['REQUEST_URI'], 'time_spent_report.php') !== false){?> class="active" <?php } ?> href="time_spent_report.php" title="<?php echo $language[$_SESSION['language']]['time_spent_report']; ?>"><?php echo $language[$_SESSION['language']]['time_spent_report']; ?></a></li> 
   
 
   <li class="textUpper hide"><a class="<?php if(strpos($_SERVER['REQUEST_URI'], 'state_ranking_report.php') !== false){?> active <?php } ?>
	 " href="state_ranking_report.php" title="<?php echo $language[$_SESSION['language']]['state_ranking_report']; ?>"> <?php echo $language[$_SESSION['language']]['state_ranking_report']; ?></a></li>


    <li class="textUpper"><a class="<?php if(strpos($_SERVER['REQUEST_URI'], 'statewise-users.php') !== false){?> active <?php } ?>
	 " href="statewise-users.php"  title="<?php echo $language[$_SESSION['language']]['graphical_reports']; ?>"><?php echo $language[$_SESSION['language']]['graphical_reports']; ?> </a></li>
	 -->
 </ul>
