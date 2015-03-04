<?php

// Page that can parse the match schedule from the FIRST site and insert into the match teams.
// NOTE: the path to the FIRST URL keeps changing, so you have to go to the page, get info about the frame and use that address.
//
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
	
	function SaveMatch($match_number, $time)
	{
		global $link;
		echo sprintf("Saving match %s, time=%s\n", $match_number, $time);
		$sql = sprintf("insert into matches (tournament_id, match_number, start_time)
						 values ('%s', %s, '%s')
							ON DUPLICATE KEY UPDATE start_time = '%s'
						 "
						, "MAR"
						, mysql_real_escape_string($match_number)
						, mysql_real_escape_string($time)
						, mysql_real_escape_string($time)
						);
		$insertReturn = mysql_query($sql, $link);
		if(!$insertReturn)
			echo sprintf("Error inserting match: %s, Err: %s\n", $match_number, mysql_error());
		return null;
	}
	function SaveMatchTeam($match_number, $team_number, $alliance, $seq_no)
	{
		global $link;
		echo sprintf("Saving match-team %s, %s, %s, %s\n", $match_number, $team_number, $alliance, $seq_no);
		$sql = sprintf("insert into match_teams (tournament_id, match_number, team_number, alliance, seq_no)
						 values ('%s', %s, %s, '%s', %s)
							ON DUPLICATE KEY UPDATE tournament_id = tournament_id
						 "
						, "MAR"
						, mysql_real_escape_string($match_number)
						, mysql_real_escape_string($team_number)
						, mysql_real_escape_string($alliance)
						, mysql_real_escape_string($seq_no)
						);
		$insertReturn = mysql_query($sql, $link);
		if(!$insertReturn)
			echo sprintf("Error inserting match_team: %s, %s Err: %s\n", $match_number, $team_number, mysql_error());
		return null;
	}
	function UpdateMatch($match_number, $red_score, $blue_score)
	{
		global $link;
		if(($red_score == null) || ($blue_score == null))
			return null;
		echo sprintf("Updating match %s, redScore=%s, blueScore=%s\n", $match_number, $red_score, $blue_score);
		$sql = sprintf("update matches set red_score = %s, blue_score = %s
						where tournament_id = '%s' and match_number = %s
						"
						, mysql_real_escape_string($red_score)
						, mysql_real_escape_string($blue_score)
						, "MAR"
						, mysql_real_escape_string($match_number)
						);
		$insertReturn = mysql_query($sql, $link);
		if(!$insertReturn)
			echo sprintf("Error updating match: %s, Err: %s\n", $match_number, mysql_error());
		return null;
	}
	/* Use internal libxml errors -- turn on in production, off for debugging */
	libxml_use_internal_errors(true);
	/* Createa a new DomDocument object */
	$dom = new DomDocument;
	/* Load the HTML */
	// NOTE: need to put the correct URL for the competition here.
	// PERHAPS?: $dom->loadHTMLFile("http://www2.usfirst.org/2014comp/Events/NJCLI/matchresults.html");
//	$dom->loadHTMLFile("http://www2.usfirst.org/2014comp/Events/NJFLA/matchresults.html");
	$dom->loadHTMLFile("http://www2.usfirst.org/2014comp/Events/MRCMP/matchresults.html");
	/* Create a new XPath object */
	$xpath = new DomXPath($dom);
	/* Query all <td> nodes containing specified class name */
	$nodes = $xpath->query("//tr[@style='background-color:#FFFFFF;']//td");
//	$nodes = $xpath->query("//td");
	/* Set HTTP response header to plain text for debugging output */
	header("Content-type: text/plain");
	/* Traverse the DOMNodeList object to output each DomNode's nodeValue */
/*	foreach ($nodes as $i => $node) {
		echo "Node($i): ", $node->nodeValue, "\n";
	}
*/	
	// The first node is the time
	// Second node is the match number
	// 3rd-5th are the Red Alliance Team Numbers
	// 6th-8th are the Blue Alliance Team Numbers
	// 9th-10th are the red score and blue score
	

	$matchNumber = null;
	foreach ($nodes as $i => $node) {
		if(($i % 10) == 0)
		{
			$time = trim($node->nodeValue);
		}
		else if(($i % 10) == 1)
		{
			$matchNumber = trim($node->nodeValue);
			// stop when we get to the quarterfinals.
			if(strpos($matchNumber, 'Qtr') === 0)
				break;
			SaveMatch($matchNumber, $time);
		}
		else if(($i % 10) == 2)
		{
			$teamNumber = trim($node->nodeValue);
			SaveMatchTeam($matchNumber, $teamNumber, 'RED', 1);
		}
		else if(($i % 10) == 3)
		{
			$teamNumber = trim($node->nodeValue);
			SaveMatchTeam($matchNumber, $teamNumber, 'RED', 2);
		}
		else if(($i % 10) == 4)
		{
			$teamNumber = trim($node->nodeValue);
			SaveMatchTeam($matchNumber, $teamNumber, 'RED', 3);
		}
		else if(($i % 10) == 5)
		{
			$teamNumber = trim($node->nodeValue);
			SaveMatchTeam($matchNumber, $teamNumber, 'BLUE', 1);
		}
		else if(($i % 10) == 6)
		{
			$teamNumber = trim($node->nodeValue);
			SaveMatchTeam($matchNumber, $teamNumber, 'BLUE', 2);
		}
		else if(($i % 10) == 7)
		{
			$teamNumber = trim($node->nodeValue);
			SaveMatchTeam($matchNumber, $teamNumber, 'BLUE', 3);
		}
		else if(($i % 10) == 8)
		{
			$redScore = trim($node->nodeValue);
		}
		else if(($i % 10) == 9)
		{
			$blueScore = trim($node->nodeValue);
			UpdateMatch($matchNumber, $redScore, $blueScore);
		}
	}
?>