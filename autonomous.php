<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
	session_start();
	
	// if we cannot get the password from session - redirect to the starting page.
	if(!$_SESSION['password'])
	{
		header("Location: team102.php"); 	/* Redirect browser */
		exit();
	}
	
	require_once "php/HTML/Template/IT.php";
	$tpl = new HTML_Template_IT("./templates");
	$tpl->loadTemplatefile("Autonomous.html", true, true);
	
	// Connect to the database.
	$link = mysql_connect('team102.org:3306', 'team102_webuser', $_SESSION['password']);
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
		
	$match_number = $_SESSION['match_number'];	
	// If we don't have a match number we cannot continue.
	if(!$match_number)
	{
		header("Location: choosematch.php"); 	/* Redirect browser */
		exit();
	}
	
	$sql = sprintf("select m.tournament_id, mt1.alliance, mt1.match_number, m.start_time, mt1.team_number as team1, mt2.team_number as team2, mt3.team_number as team3
		from matches m, match_teams mt1, match_teams mt2, match_teams mt3, tournaments t
		where t.active = 'Y'
		and m.tournament_id = t.id
		and mt1.tournament_id = m.tournament_id
		and mt1.match_number = m.match_number
		and mt1.match_number = %s
		and mt1.alliance = '%s'
		and mt1.seq_no = 1
		and mt2.team_number != mt1.team_number
		and mt2.tournament_id = mt1.tournament_id
		and mt2.match_number = mt1.match_number
		and mt2.completed = mt1.completed
		and mt2.alliance = mt1.alliance
		and mt2.seq_no = 2
		and mt3.team_number != mt1.team_number
		and mt3.team_number != mt2.team_number
		and mt3.tournament_id = mt1.tournament_id
		and mt3.match_number = mt1.match_number
		and mt3.completed = mt1.completed
		and mt3.alliance = mt1.alliance
		and mt3.seq_no = 3", $match_number, $_SESSION['alliance']);
		
	$matches = mysql_query($sql, $link);
	
	$match =  mysql_fetch_object($matches);
	
	$_SESSION['match'] = $match;

	$_SESSION['match_number'] = $match_number;
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
		$sql = sprintf("update match_teams set initials = '%s' where tournament_id = '%s' and match_number = %s
							and team_number = %s;"
							, $_SESSION['initials']
							, $_SESSION['tournament']->ID
							, $_SESSION['match']->match_number
							, $teamNumber);
		$updateReturn = mysql_query($sql, $link);
		if(!$updateReturn)
			die("Error updating match_teams team: " . $_SESSION['match']->team1 . " Err: " . mysql_error());
	}

	// if the next button has been clicked, save the results and redirect to Teleop
	if(isset($_POST['btnNext']))
	{
		
//		var_dump($_POST);	// Use this to see a dump of the _POST variables.
//		echo '<br>';

//		mysql_query("START TRANSACTION;", $link);
		// No validations are necessary.
		$sql = sprintf("update match_teams
							set auto_robot = '%s', auto_num_totes = %s, auto_stack_totes = %s, auto_containers = %s
							where tournament_id = '%s' and match_number = %s and team_number = %s"
							, ($_POST['team1robot'] === "1") ? "Y" : "N"
							, $_POST['team1totevalue'] == null ? "0" : $_POST['team1totevalue']
							, $_POST['team1stackvalue'] == null ? "0" : $_POST['team1stackvalue']
							, $_POST['team1containervalue'] == null ? "0" : $_POST['team1containervalue']
							, $_SESSION['tournament']->ID
							, $match_number
							, $_SESSION['match']->team1
							);
		$updateReturn = mysql_query($sql, $link);
		if(!$updateReturn)
			die("Error updating match-team 1 " . mysql_error());
//		echo $sql;
//		echo '<br>';
		$sql = sprintf("update match_teams
							set auto_robot = '%s', auto_num_totes = '%s', auto_stack_totes = '%s', auto_containers = '%s'
							where tournament_id = '%s' and match_number = %s and team_number = %s"
							, ($_POST['team2robot'] === "1") ? "Y" : "N"
							, $_POST['team2totevalue'] == null ? "0" : $_POST['team2totevalue']
							, $_POST['team2stackvalue'] == null ? "0" : $_POST['team2stackvalue']
							, $_POST['team2containervalue'] == null ? "0" : $_POST['team2containervalue']
							, $_SESSION['tournament']->ID
							, $match_number
							, $_SESSION['match']->team2
							);
		$updateReturn = mysql_query($sql, $link);
		if(!$updateReturn)
			die("Error updating match-team 2 " . mysql_error());
//		echo $sql;
//		echo '<br>';
		$sql = sprintf("update match_teams
							set auto_robot = '%s', auto_num_totes = '%s', auto_stack_totes = '%s', auto_containers = '%s'
							where tournament_id = '%s' and match_number = %s and team_number = %s"
							, ($_POST['team3robot'] == "1") ? "Y" : "N"
							, $_POST['team3totevalue'] == null ? "0" : $_POST['team3totevalue']
							, $_POST['team3stackvalue'] == null ? "0" : $_POST['team3stackvalue']
							, $_POST['team3containervalue'] == null ? "0" : $_POST['team3containervalue']
							, $_SESSION['tournament']->ID
							, $match_number
							, $_SESSION['match']->team3
							);
		$updateReturn = mysql_query($sql, $link);
		if(!$updateReturn)
			die("Error updating match-team 3 " . mysql_error());
//		echo $sql;
//		echo '<br>';
		
//		$updateReturn = mysql_query("COMMIT;", $link);
		mysql_close ($link);
		
		header("Location: teleop.php"); /* Redirect browser */
		exit();
	}
	else
	{
		$tpl->setVariable("tournamentTitle", $_SESSION['tournament']->Title);
		$tpl->setVariable("allianceColor", $_SESSION['alliance']);
		$tpl->setVariable("team1", $_SESSION['match']->team1);
		$tpl->setVariable("team2", $_SESSION['match']->team2);
		$tpl->setVariable("team3", $_SESSION['match']->team3);
		$tpl->show();
	}
	@mysql_close($link);

?>