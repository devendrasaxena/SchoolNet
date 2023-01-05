
  <ul class="nav nav-tabs" style="margin-bottom:40px;">
    <li class="textUpper">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'statewise-users.php') !== false){?>
	class="active" <?php } ?> href="statewise-users.php" title="<?php echo  'School Wise Users'; ?>">
	
	<?php echo  'School Wise Users'; ?>
	</a>
	</li>
	<li class="textUpper hide">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'graph_state_ranking.php') !== false){?>
	class="active" <?php } ?> href="graph_state_ranking.php" title="<?php echo  $language[$_SESSION['language']]['state_score_and_ranking'] ?>">
	
	<?php echo  $language[$_SESSION['language']]['state_score_and_ranking'] ?>
	</a>
	</li>
	<li class="textUpper  hide">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'graph_module_score.php') !== false){?>
	class="active" <?php } ?> href="graph_module_score.php" title="<?php echo  $language[$_SESSION['language']]['module_score'] ?>">
	
	<?php echo  $language[$_SESSION['language']]['module_score'] ?>
	</a>
	</li>
	
	<!--
	<li class="textUpper">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'graph_module_time.php') !== false){?>
	class="active" <?php } ?> href="graph_module_time.php" title="<?php echo  $language[$_SESSION['language']]['time_spent'] ?>">
	
	<?php echo  $language[$_SESSION['language']]['time_spent'] ?>
	</a>
	</li>-->
	<li class="textUpper">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'graph_active_learners.php') !== false){?>
	class="active" <?php } ?> href="graph_active_learners.php" title="<?php echo  $language[$_SESSION['language']]['active_users'] ?>">
	
	<?php echo  $language[$_SESSION['language']]['active_users'] ?>
	</a>
	</li>
	
	<li class="textUpper">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'graph_logins_learner.php') !== false){?>
	class="active" <?php } ?> href="graph_logins_learner.php" title="<?php echo  $language[$_SESSION['language']]['learner_logins'] ?>">
	 
	<?php echo  $language[$_SESSION['language']]['learner_logins'] ?>
	</a>
	</li>
	<!--
    <li class="textUpper">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'performance-graph.php') !== false){?>
	class="active" <?php } ?> href="performance-graph.php">
	Performance Graph
	</a>
	</li>
	
    <li class="textUpper">
	<a <?php if(strpos($_SERVER['REQUEST_URI'], 'time_spent_graph.php') !== false){?>
	class="active" <?php } ?> href="time_spent_graph.php">
	Time Spent Graph
	</a>
	</li>-->
	

    <li class="textUpper" style="float:right; position:relative; top:-60px"><a class=""
	  href="reports.php" title="<?php echo  $language[$_SESSION['language']]['back_in_report'] ?>">   <?php echo  $language[$_SESSION['language']]['back_in_report'] ?> </a></li>
 </ul>
