<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
	session_start();
	
	// if we cannot get the password from session - redirect to the starting page.
	if(!$_SESSION['password'])
	{
		header("Location: index.php"); 	/* Redirect browser */
		exit();
	}
	
	// Connect to the database.
	$link = mysql_connect('team102.org:3306', 'team102_webuser', $_SESSION['password']);
	
	if (!mysql_select_db('team102_2014', $link)) {
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

	{
		$_SESSION['match_number'] = $match_number;
		// Update the match with the user's initials
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
	}

	// if the next button has been clicked, save the results and redirect to either the AutoExtra or the Teleop
	if(isset($_POST['btnNext']))
	{
		
//		var_dump($_POST);	// Use this to see a dump of the _POST variables.
//		echo '<br>';

//		mysql_query("START TRANSACTION;", $link);
		// No validations are necessary.
		$sql = sprintf("update match_teams
							set has_ball = '%s', auto_goal = '%s', auto_goal_hot = '%s', auto_mobility = '%s'
							where tournament_id = '%s' and match_number = %s and team_number = %s"
							, isset($_POST['chkTeam1HasBallName']) ? "Y" : "N"
							, $_POST['rdoScore1']
							, isset($_POST['chkTeam1HotName']) ? "Y" : "N"
							, isset($_POST['chkTeam1MobilityName']) ? "Y" : "N"
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
							set has_ball = '%s', auto_goal = '%s', auto_goal_hot = '%s', auto_mobility = '%s'
							where tournament_id = '%s' and match_number = %s and team_number = %s"
							, isset($_POST['chkTeam2HasBallName']) ? "Y" : "N"
							, $_POST['rdoScore2']
							, isset($_POST['chkTeam2HotName']) ? "Y" : "N"
							, isset($_POST['chkTeam2MobilityName']) ? "Y" : "N"
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
							set has_ball = '%s', auto_goal = '%s', auto_goal_hot = '%s', auto_mobility = '%s'
							where tournament_id = '%s' and match_number = %s and team_number = %s"
							, isset($_POST['chkTeam3HasBallName']) ? "Y" : "N"
							, $_POST['rdoScore3']
							, isset($_POST['chkTeam3HotName']) ? "Y" : "N"
							, isset($_POST['chkTeam3MobilityName']) ? "Y" : "N"
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
		
		// Figure out where to redirect (autoextra if not all balls have been scored)
		$numBalls = (isset($_POST['chkTeam1HasBallName']) ? 1 : 0) + (isset($_POST['chkTeam2HasBallName']) ? 1 : 0) + (isset($_POST['chkTeam3HasBallName']) ? 1 : 0);
//		echo "Balls: " + $numBalls;
//		echo '<br>';
		$numScored = ((($_POST['rdoScore1'] == 'H') || ($_POST['rdoScore1'] == 'L')) ? 1 : 0)
						+ ((($_POST['rdoScore2'] == 'H') || ($_POST['rdoScore2'] == 'L')) ? 1 : 0)
						+ ((($_POST['rdoScore3'] == 'H') || ($_POST['rdoScore3'] == 'L')) ? 1 : 0);
//		echo "Num Scored: " + $numScored;
//		echo '<br>';
		$numRemaining = $numBalls - $numScored;
		$_SESSION['numExtraBalls'] = $numRemaining;
		$_SESSION['score'] = $_POST[scoreFieldName];
		if($numRemaining > 0)
		{
			header("Location: autoextra.php"); /* Redirect browser */
			exit();
		}
		else
		{
			header("Location: teleop.php"); /* Redirect browser */
			exit();		
		}
	}
?>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><? echo $_SESSION['tournament']->Title; ?> Autonomous</title>
    <meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<script type='text/javascript' src='jqueryui/js/jquery-1.10.2.js'></script>
    <link rel="stylesheet" href="stylesheet.css" />
    <!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type='text/javascript'>//<![CDATA[ 
		$(window).load(function(){
			calcScore();
			
			// Event handler to recalculate the score whenever an input control changes.
			$( "input" ).change(function() { calcScore(); })

			// Generate three event handlers to disable/enable inputs when the "Has Ball" checkbox changes.
			<?php
			for ($i = 1; $i <= 3; $i++) {
			?>
				$("#chkTeam<?php echo $i ?>HasBall").click(function() {				
					$("#chkScoreHot<?php echo $i ?>").attr("checked", false); 
					$("#rdoScoreNone<?php echo $i ?>").prop("checked", true);
					$("#rdoScoreHigh<?php echo $i ?>").attr("disabled", !this.checked); 
					$("[for='rdoScoreHigh<?php echo $i ?>']").css("color", !this.checked ? "grey" : "white"); 
					$("#rdoScoreLow<?php echo $i ?>").attr("disabled", !this.checked); 
					$("[for='rdoScoreLow<?php echo $i ?>']").css("color", !this.checked ? "grey" : "white"); 
					// $("#rdoScoreNone<?php echo $i ?>").attr("disabled", !this.checked); 
					$("[for='rdoScoreNone<?php echo $i ?>']").css("color", !this.checked ? "grey" : "white"); 
					$("#chkScoreHot<?php echo $i ?>").attr("disabled", !this.checked); 
					$("[for='chkScoreHot<?php echo $i ?>']").css("color", !this.checked ? "grey" : "white"); 
					calcScore();
				});
			<?php
			}
			?>
		});

		// Function to calculate the score.
		function calcScore()
		{
			total = 0;
			<?php
			for ($i = 1; $i <= 3; $i++) {
			?>
				if(!( $( "#rdoScoreNone<?php echo $i ?>" ).prop( "checked" ) ))
				{
					if( $( "#rdoScoreHigh<?php echo $i ?>" ).prop( "checked" ) )
						total += 10;			
					if( $( "#rdoScoreLow<?php echo $i ?>" ).prop( "checked" ) )
						total += 1;
					total += 5;
					
					if( $( "#chkScoreHot<?php echo $i ?>" ).prop( "checked" ) )
						total += 5;
				}
				total += ( $( "#chkMobility<?php echo $i ?>" ).prop( "checked" ) ) ? 5 : 0;
			<?php }?> 
			$("#scoreField").val(total);
			$("#Score").text("Score: " + total);
			
		};
	//]]>
	</script>
</head>
<body class="no-js">
    <div id="page">
        <div class="header">
            <div id="competition"><? echo $_SESSION['tournament']->Title . ' ' . $_SESSION['initials']; ?></div>
            <div id="match">Match <? echo $_SESSION['match']->match_number . " - " . $_SESSION['match']->start_time . " - " . $_SESSION['alliance']; ?></div>
            <div id="autonomous">Autonomous</div>
        </div>
        <form id="autonomousForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<?php
			for ($i = 1; $i <= 3; $i++) {
				if($i == 1)
					$teamNumber = $_SESSION['match']->team1;
				else if($i == 2)
					$teamNumber = $_SESSION['match']->team2;
				else
					$teamNumber = $_SESSION['match']->team3;
			?>
            <div id="Team<?php echo $i ?>" class="team">
                <div id="Team<?php echo $i ?>Number" class="teamNumber"><?php echo $teamNumber ?></div>
                <div id="Team<?php echo $i ?>AutoScore">
					<div id="Team<?php echo $i ?>HasBall">
						<input type="checkbox" name="chkTeam<?php echo $i ?>HasBallName" id="chkTeam<?php echo $i ?>HasBall" 
							value="Team-<?php echo $i ?>-HasBall"  checked/>
						<label for="chkTeam<?php echo $i ?>HasBall">Has Ball</label>
					</div>
                    <div id="Team<?php echo $i ?>ScoreHigh">
                        <input type="radio" name="rdoScore<?php echo $i ?>" id="rdoScoreHigh<?php echo $i ?>" value="H"/>
                        <label for="rdoScoreHigh<?php echo $i ?>">High</label>
                    </div>
                    <div id="Team<?php echo $i ?>ScoreLow">
                        <input type="radio" name="rdoScore<?php echo $i ?>" id="rdoScoreLow<?php echo $i ?>" value="L"/>
                        <label for="rdoScoreLow<?php echo $i ?>">Low</label>
                    </div>
                    <div id="Team<?php echo $i ?>ScoreNone">
                        <input type="radio" name="rdoScore<?php echo $i ?>" id="rdoScoreNone<?php echo $i ?>" value="N" checked="true"/>
                        <label for="rdoScoreNone<?php echo $i ?>">None</label>
                    </div>
					<div id="Team<?php echo $i ?>Hot">
						<input type="checkbox" name="chkTeam<?php echo $i ?>HotName" id="chkScoreHot<?php echo $i ?>" value="Team-<?php echo $i ?>-Hot"/>
						<label for="chkScoreHot<?php echo $i ?>">Hot</label>
					</div>
					<div id="Team<?php echo $i ?>Mobility">
						<input type="checkbox" name="chkTeam<?php echo $i ?>MobilityName" id= "chkMobility<?php echo $i ?>" value="Team-<?php echo $i ?>-Mobility" />
						<label for="chkMobility<?php echo $i ?>">Mobility</label>
					</div>
                </div>
            </div>
		<?php
			}
		?>
            <div style="clear:both;"></div>
            <div class="footer">
                <div id="Score" class="<?php echo strtolower($_SESSION['alliance']) ?>"></div>
				<input type="hidden" name="scoreFieldName" id="scoreField"/>
                <div id="nav">
                    <input type="submit" name="btnNext" value="Next" />
                </div>
            </div>
        </form>
    </div>
</body>
</html>