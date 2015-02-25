<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
	session_start();
	
	// if we cannot get the password from session - redirect to the starting page.
	if(!$_SESSION['password'])
	{
		header("Location: team102.php");     /* Redirect browser */
		exit();
	}

	require_once "php/HTML/Template/IT.php";
	$tpl = new HTML_Template_IT("./templates");
	$tpl->loadTemplatefile("choosematch.html", true, true);

	$link = mysql_connect('team102.org:3306', 'team102_webuser', $_SESSION['password']);
	
	if (!mysql_select_db('team102_2015', $link)) {
		echo sprintf('Could not select database, Err: %s', mysql_error());
		exit;
	}

	// if we don't have an alliance, go back to get one.
	if(!$_SESSION['alliance'])
	{
		$_SESSION['match_number'] = null;
		header("Location: scoringapp.php");     /* Redirect browser */
		exit();
	}
	
	// Get the match number.
	if(isset($_POST['btnNext']))
	{
		$match_number = $_POST['rdoMatch'];
		if($match_number != null)
		{
			$_SESSION['match_number'] = $match_number;
			header("Location: autonomous.php");     /* Redirect browser */		
		}
	}

	$tournament = $_SESSION['tournament'];
	$tournament_id = $tournament->ID;

	$sql = sprintf("select mt1.match_number, m.start_time, mt1.team_number as team1, mt2.team_number as team2, mt3.team_number as team3, mt1.initials
			from matches m, match_teams mt1, match_teams mt2, match_teams mt3, tournaments t
			where t.active = 'Y'
			and m.tournament_id = t.id
			and mt1.tournament_id = m.tournament_id
			and mt1.match_number = m.match_number
			and mt1.completed = 'N'
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
			and mt3.seq_no = 3
			order by m.match_number;", $_SESSION['alliance']);
			
			# ^^^^ mt1.alliance = #
			
	$matches = mysql_query($sql, $link);
	if(!$matches)
		die(sprintf("Error querying matches Err: %s", mysql_error()));
		
	$tpl->setCurrentBlock("main");
	$tpl->setVariable("tournamentTitle", $_SESSION['tournament']->Title) ;
	$tpl->setVariable("allianceColor", $_SESSION['alliance']) ;
	
	while($row = mysql_fetch_assoc($matches)) {
		$tpl->setCurrentBlock("oneMatch");
		$tpl->setVariable("matchNumber", $row['match_number']) ;
		$tpl->setVariable("matchStartTime", $row['start_time']) ;
		$tpl->setVariable("team1", $row['team1']) ;
		$tpl->setVariable("team2", $row['team2']) ;
		$tpl->setVariable("team3", $row['team3']) ;
		$tpl->setVariable("initials", $row['initials']) ;
		$tpl->parseCurrentBlock() ;		
	}
	@mysql_close($link);
	$tpl->show();
?>