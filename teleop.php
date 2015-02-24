<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");

	function getScalar($sql,$def="") {
		// execute a $sql query and return the first
		// value, or, if none, the $def value
		$rs = mysql_query($sql) or die(mysql_error().$sql);
		if (mysql_num_rows($rs)) {
			$r = mysql_fetch_row($rs);
			mysql_free_result($rs);
			return $r[0];
		}
		return $def;
	}

	session_start();
	
	// if we cannot get the password from session - redirect to the starting page.
	if(!$_SESSION['password'])
	{
		header("Location: index.php"); 	/* Redirect browser */
		exit();
	}
	
	require_once "php/HTML/Template/IT.php";
	$tpl = new HTML_Template_IT("./templates");
	$tpl->loadTemplatefile("Teleop.html", true, true);
	
	// Connect to the database.
	$link = mysql_connect('Team102.org:3306', 'team102_webuser', $_SESSION['password']);
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
	
	// Determine if we have submitted a teleop period.
	if(isset($_POST['btnNext']) || isset($_POST['btnDone']))
	{
/*		var_dump($_POST);	// Use this to see a dump of the _POST variables.
		echo '<br>';
		die;
*/		
		if($_POST['team'] == null)
		{
			echo '<!-- Please select a team. -->';
			$tpl->setCurrentBlock("reportError") ;
			$tpl->setVariable("alertError", "Please select a team.");
			$tpl->parseCurrentBlock() ;
		}
		else
		{
			// Insert the submitted cycle into the database.
			// No validations are necessary.
			$sql = sprintf("insert into match_team_cycles
								(tournament_id, match_number, team_number, cycle_number
									, tote_start_height
									, tote_end_height
									, container_scored
									, litter_scored
									, coop_start_height
									, coop_end_height)
								 values ('%s', %s, %s, %s, %s, %s, '%s', '%s', %s, %s)
									ON DUPLICATE KEY UPDATE 
									tote_start_height = %s
									, tote_end_height = %s
									, container_scored = '%s'
									, litter_scored = '%s'
									, coop_start_height = %s
									, coop_end_height = %s
								 "
								, $_SESSION['tournament']->ID
								, $_SESSION['match']->match_number
								, ($_POST['team'] == 1) ? $_SESSION['match']->team1 : (($_POST['team'] == 2) ? $_SESSION['match']->team2 : $_SESSION['match']->team3)
								, $_SESSION['cycleNumber']
								, $_POST['tote_start_height'] == null ? "0" : $_POST['tote_start_height']
								, $_POST['tote_end_height'] == null ? "0" : $_POST['tote_end_height']
								, isset($_POST['container_scored']) ? "Y" : "N"
								, isset($_POST['litter_scored']) ? "Y" : "N"
								, $_POST['coop_start_height'] == null ? "0" : $_POST['coop_start_height']
								, $_POST['coop_end_height'] == null ? "0" : $_POST['coop_end_height']
								, $_POST['tote_start_height'] == null ? "0" : $_POST['tote_start_height']
								, $_POST['tote_end_height'] == null ? "0" : $_POST['tote_end_height']
								, isset($_POST['container_scored']) ? "Y" : "N"
								, isset($_POST['litter_scored']) ? "Y" : "N"
								, $_POST['coop_start_height'] == null ? "0" : $_POST['coop_start_height']
								, $_POST['coop_end_height'] == null ? "0" : $_POST['coop_end_height']
								);
			$insertReturn = mysql_query($sql, $link);
			if(!$insertReturn)
				die("Error inserting match_team_cycles team: " . $_SESSION['match']->team1 . " Err: " . mysql_error());

			// Redirect to the Recap if we are done.
			if(isset($_POST['btnDone']))
			{
				for($team = 1; $team <= 3; $team++)
				{
					if($team == 1)
					{
						$teamNumber = $_SESSION['match']->team1;
					}
					else if($team == 2)
					{
						$teamNumber = $_SESSION['match']->team2;
					}
					else if($team == 3)
					{
						$teamNumber = $_SESSION['match']->team3;
					}
					$sql = sprintf("update match_teams set completed = 'Y', initials = '%s' where tournament_id = '%s' and match_number = %s
										and team_number = %s;"
										, $_SESSION['initials']
										, $_SESSION['tournament']->ID
										, $_SESSION['match']->match_number
										, $teamNumber);
					$updateReturn = mysql_query($sql, $link);
					if(!$updateReturn)
						die("Error updating match_teams team: " . $_SESSION['match']->team1 . " Err: " . mysql_error());
				}

				header("Location: recap.php"); /* Redirect browser */
				exit();
			}
		}
	}
	// If not, get the current cycle of the teleop	period.
	$sql = sprintf("select ifnull(max(ifnull(cycle_number, 0)), 0) + 1 cycle_number
					from match_team_cycles
					where tournament_id = '%s' and match_number = %d and team_number in (%d, %d, %d)"
		, $_SESSION['match']->tournament_id, $_SESSION['match']->match_number, $_SESSION['match']->team1, $_SESSION['match']->team2, $_SESSION['match']->team3);
	$cycleNumber = getScalar($sql, "NULL");
	$_SESSION['cycleNumber'] = $cycleNumber;
	
	$tpl->setCurrentBlock("main") ;
	$tpl->setVariable("tournamentTitle", $_SESSION['tournament']->Title);
	$tpl->setVariable("matchNumber", $_SESSION['match']->match_number);
	$tpl->setVariable("cycleNumber", $_SESSION['cycleNumber']);
	$tpl->setVariable("allianceColor", $_SESSION['alliance']);
	$tpl->setVariable("team1", $_SESSION['match']->team1);
	$tpl->setVariable("team2", $_SESSION['match']->team2);
	$tpl->setVariable("team3", $_SESSION['match']->team3);

	$tpl->show();

?>
