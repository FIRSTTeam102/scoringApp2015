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
	$link = mysql_connect('Team102.org:3306', 'team102_webuser', 'Gearheads');
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
	
	if($_GET['team'] != null)
		$teamNumber = $_GET['team'];
	else
		$teamNumber = $_POST['team'];

	if ($_POST['btnEdit'] == 'Save')
	{
		// Save the answers.
		foreach ($_POST as $key => $value) 
		{ 
			// Look at each answer input.
			if(strpos($key,'txtQ') == 0)
			{
				$qID = intval(substr($key, 4));
			}
			// Try to update the answer row, if that fails, insert it.
			$sql = sprintf("INSERT INTO answers (question_id, team_number, answer)
				VALUES (%s, %s, '%s')
				ON DUPLICATE KEY UPDATE
				answer = '%s'", $qID, $teamNumber, mysql_real_escape_string($value), mysql_real_escape_string($value));
			$updateReturn = mysql_query($sql, $link);
			if(!$updateReturn)
				die(sprintf("Error updating answers: qID: %d, team: %d, Err: %s", $qID, $teamNumber, mysql_error()));
		}		
	}
	
	// Get a list of teams to put in the list box.
	$sql = "select t.number, t.name
						from teams t, tournament_teams tt, tournaments tr 
						where tr.active = 'Y'
						and tt.tournament_id = tr.ID
						and t.number = tt.team_number
						order by t.number;";
						
	$teams = mysql_query($sql, $link);
	if (!$teams) {
		echo "DB Error, could not query teams\n";
		echo 'MySQL Error: ' . mysql_error();
		exit;
	}
	if($teamNumber != null)
	{
		$sql = sprintf("SELECT q.id, q.question, q.seq_no, q.active, q.q_type, q.domain, a.answer, a.team_number 
							FROM questions q LEFT JOIN answers a ON q.id = a.question_id
							where a.team_number = %d
								and q.active = 'Y'
							UNION
							SELECT q.id, q.question, q.seq_no, q.active, q.q_type, q.domain, null, null 
							FROM questions q
							where q.id not in (SELECT q.id 
												FROM questions q LEFT JOIN answers a ON q.id = a.question_id
												where a.team_number = %d)
							and q.active = 'Y'
							order by 3;", $teamNumber, $teamNumber);
		$questions = mysql_query($sql, $link);
		if (!$questions) {
			echo "DB Error, could not query questions and answers\n";
			echo 'MySQL Error: ' . mysql_error();
			exit;
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>2015 Questionnaire</title>
    <meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<script type='text/javascript' src='jqueryui/js/jquery-1.10.2.js'></script>
    <link rel="stylesheet" href="stylesheet.css" />
    <!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	<script type='text/javascript'>//<![CDATA[ 
		$(window).load(function(){
			// Event handler to recalculate the score whenever an input control changes.
			$( "#selectTeamID" ).change(function() 
			{ 
				this.form.submit();
			})

		});
	//]]>
	</script>
</head>
<body class="no-js">
    <div id="page">
	<div id="button_holder">
	<a href="scoringapp.php" style="color:white; text-style:none;">Scoring App</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="standings.php" style="color:white; text-style:none;">Standings</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="preview.php" style="color:white; text-style:none;">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="upcoming.php" style="color:white; text-style:none;">Upcoming</a>
		</div>
        <div class="header">2015 FRC Questionnaire</div>
        <form id="QuestionsForm" action="survey.php" method="POST">
			<label for="selectTeamID">Team: </label>			
			<select name="team" id="selectTeamID" style="width: 20em;">
				<option value="" disabled="disabled" <?php echo ($teamNumber == null) ? 'selected="selected"' : ''; ?> >Please select a Team</option>
				<?php
				while($row = mysql_fetch_assoc($teams)) {
				?>
				<option value="<?php echo $row['number'] ?>" <?php echo ($teamNumber == $row['number']) ? 'selected="selected"' : ''; ?> ><?php echo $row['number'] ?> - <?php echo $row['name'] ?></option>
				<?php
				}
				?>
			</select>&nbsp;<input type="submit" name="btnEdit" value="<?php echo ($_POST['btnEdit'] == 'Edit') ? 'Save' : 'Edit'; ?>" <?php echo ($teamNumber == null) ? 'disabled' : '' ?>/>
				<?php
				if($teamNumber != null)
				{
					while($row = mysql_fetch_assoc($questions)) {
						if($row['q_type'] == 'SEP')
						{
							?><div class="qSection"><?php echo $row['question'] ?></div>
					<?php
						}
						else if($row['q_type'] == 'MULT')
						{
							$sql = sprintf("select abbreviation, meaning
									from ref_codes
									where domain = '%s'", $row['domain']);
							$domainValues = mysql_query($sql, $link);

							if (!$domainValues) {
								echo "DB Error, could not query ref_codes domainValues\n";
								echo 'MySQL Error: ' . mysql_error();
								exit;
							}

							if($row['domain'] == 'YES_NO')
							{
								$width = '4em';
							}
							else
								$width = '7em';
							
							?><div class="question"><label for="txtQ<?php echo $row['id'] ?>"><?php echo $row['question'] . ':' ?></label>
									<select name="txtQ<?php echo $row['id'] ?>" id="txtQ<?php echo $row['id'] ?>Id"  
										style="width: <?php echo $width ?>;" <?php echo ($_POST['btnEdit'] == 'Edit') ? '' : 'disabled' ?>>
									<option value=""></option>
									<?php
										while($valRow = mysql_fetch_assoc($domainValues)) {
										?>
										<option value="<?php echo $valRow['abbreviation']; ?>"
										<?php echo ($valRow['abbreviation'] ==  $row['answer']) ? ' selected ' : ''; ?>
										><?php echo $valRow['meaning']; ?>
										</option>
										<?php
										}
									?>
									</select>
									</div>
					<?php
						}
						else if($row['q_type'] == 'SHORT')
						{
							?><div class="question"><label for="txtQ<?php echo $row['id'] ?>"><?php echo $row['question'] . ':' ?></label>
									<input type="text" name="txtQ<?php echo $row['id'] ?>" id="txtQ<?php echo $row['id'] ?>Id" style="width: 7em;" 
										value="<?php echo $row['answer']; ?>" <?php echo ($_POST['btnEdit'] == 'Edit') ? '' : 'disabled' ?>/></div>
					<?php
						}
						else if($row['q_type'] == 'LONG')
						{
							?><div class="question"><label for="txtQ<?php echo $row['id'] ?>"><?php echo $row['question'] . ':' ?></label>
											  <textarea cols="30" rows="2"
															name="txtQ<?php echo $row['id'] ?>" <?php echo ($_POST['btnEdit'] == 'Edit') ? '' : 'disabled' ?>><?php echo $row['answer']; ?></textarea></div>
					<?php
						}
					?>
					<?php
					}
				}
				?>
            <div style="clear:both;"></div>
		</form>
	</div>
</body>
</html>
