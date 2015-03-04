<?php

// Page that can parse the team names and numbers from the first site and insert into the teams and tournament_teams tables.
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
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
		
	/* Use internal libxml errors -- turn on in production, off for debugging */
	libxml_use_internal_errors(true);
	/* Createa a new DomDocument object */
	$dom = new DomDocument;
	/* Load the HTML */
	$dom->loadHTMLFile("https://my.usfirst.org/myarea/index.lasso?page=teamlist&event_type=FRC&sort_teams=number&year=2015&event=mrcmp");
	/* Create a new XPath object */
	$xpath = new DomXPath($dom);
	/* Query all <td> nodes containing specified class name */
	$nodes = $xpath->query("//tr[@bgcolor='#FFFFFF']//td");
//	$nodes = $xpath->query("//td");
	/* Set HTTP response header to plain text for debugging output */
	header("Content-type: text/plain");
	/* Traverse the DOMNodeList object to output each DomNode's nodeValue */
/*	foreach ($nodes as $i => $node) {
		echo "Node($i): ", $node->nodeValue, "\n";
	}
*/	
	// The first node is the city, stateCountry
	// Second node is the list of sponsors and the school name
	// Third node is the team number.
	
	foreach ($nodes as $i => $node) {
		if(($i % 3) == 0)
		{
			if($teamNumber != null)
			{
				// A team has been parsed.  Save it to the DB.
				echo sprintf("%s, %s, %s, %s, %s", $teamNumber, $school, $city, $state, $country);
				echo "\n";
				$sql = sprintf("insert into teams (number, name, city, state)
								 values (%s, '%s', '%s', '%s')
									ON DUPLICATE KEY UPDATE number = number
								 "
								, $teamNumber
								, mysql_real_escape_string($school)
								, mysql_real_escape_string($city)
								, mysql_real_escape_string($state)
								);
				$insertReturn = mysql_query($sql, $link);
				if(!$insertReturn)
					die(sprintf("Error inserting team: %s, Err: %s", $teamNumber, mysql_error()));
				$sql = sprintf("insert into tournament_teams (tournament_id, team_number)
								 values ('MAR', %s)
									ON DUPLICATE KEY UPDATE tournament_id = tournament_id
								 "
								, $teamNumber
								);
				$insertReturn = mysql_query($sql, $link);
				if(!$insertReturn)
					die(sprintf("Error inserting tournament_team: %s, Err: %s", $teamNumber, mysql_error()));
			}
			$city = null; $state = null; $country = null;
			$parts = explode (',', $node->nodeValue);
			$city = trim($parts[0]);
			$state = substr(trim($parts[1]), 0, 2);
			$country = substr(trim($parts[1]), 2);
		}
		else if(($i % 3) == 1)
		{
			$school = null;
			// Parse out the school name.
			$parts = explode ('/', $node->nodeValue);
			foreach($parts as $j => $schoolPart)
			{
				$schoolPos = strpos($schoolPart, 'School');
				if(!$schoolPos) $schoolPos = strpos($schoolPart, 'Academy');
				if(!$schoolPos) $schoolPos = strpos($schoolPart, 'Prep');
				if(!$schoolPos) $schoolPos = strpos($schoolPart, 'Board');
				if($schoolPos)
				{
					$moreParts = explode ('&', $schoolPart);
					foreach($moreParts as $k => $schoolMorePart)
					{
						$schoolMorePos = strpos($schoolMorePart, 'School');
						if(!$schoolMorePos) $schoolMorePos = strpos($schoolMorePart, 'Academy');
						if(!$schoolMorePos) $schoolMorePos = strpos($schoolMorePart, 'Prep');
						if(!$schoolMorePos) $schoolMorePos = strpos($schoolMorePart, 'Board');
						if($schoolMorePos)
						{
							$school = trim($schoolMorePart);
						}
					}
				}	
			}
			if($school == null)
				$school = $node->nodeValue;
		}
		else if(($i % 3) == 2)
		{
			$teamNumber = null;
			// Get team number.
			$teamNumber = trim($node->nodeValue);
		}
	}
	if($teamNumber != null)
	{
		// The last team has been parsed.  Save it to the DB.
		echo sprintf("%s, %s, %s, %s, %s", $teamNumber, $school, $city, $state, $country);
		echo "\n";
				$sql = sprintf("insert into teams (number, name, city, state)
								 values (%s, '%s', '%s', '%s')
									ON DUPLICATE KEY UPDATE number = number
								 "
								, $teamNumber
								, mysql_real_escape_string($school)
								, mysql_real_escape_string($city)
								, mysql_real_escape_string($state)
								);
				$insertReturn = mysql_query($sql, $link);
				if(!$insertReturn)
					die(sprintf("Error inserting team: %s, Err: %s", $teamNumber, mysql_error()));
				$sql = sprintf("insert into tournament_teams (tournament_id, team_number)
								 values ('MAR', %s)
									ON DUPLICATE KEY UPDATE tournament_id = tournament_id
								 "
								, $teamNumber
								);
				$insertReturn = mysql_query($sql, $link);
				if(!$insertReturn)
					die(sprintf("Error inserting tournament_team: %s, Err: %s", $teamNumber, mysql_error()));
	}

?>