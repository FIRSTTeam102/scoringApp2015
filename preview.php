<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");

	session_start();
	
	// Connect to the database.
	$link = mysql_connect('Team102.org:3306', 'team102_webuser', 'Gearheads');
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
	

	$sql = "select match_number, start_time
						from matches tm, tournaments t
						where tm.tournament_id = t.id
						and t.active = 'Y'
						and tm.match_number in (select distinct mt.match_number from match_teams mt, tournaments t2 
													where mt.completed = 'N' 
													and mt.tournament_id = t2.id
													and t2.active = 'Y')"
						;	
		
	
	$matchesQ = mysql_query($sql, $link);

	if (!$matchesQ) {
			echo "DB Error, could not query matches\n";
			echo 'MySQL Error: ' . mysql_error();
			exit;
		}
	
	if($_GET['match'] != null)
		$matchNumber = $_GET['match'];
	else
		$matchNumber = $_POST['match_number'];
	
	
	
	if($_POST['show_competitions'] == "Hide All Competitions" || $_POST['show_competitions'] == null){
		$view = "team_avg_pts_v";
	}else{
		$view = "t_team_avg_pts_v";
	}
	
	if($matchNumber != null){
		$sql = sprintf("select mt.alliance as alliy, s.*
					from tournaments t
					join match_teams mt on mt.tournament_id = t.id
					left join %s s on s.team_number = mt.team_number and s.tournament_id = mt.tournament_id
					where 
				          t.active = 'Y'
  					  and mt.match_number = %s
					order by alliance", $view, $matchNumber);
	
		
		$previewQ = mysql_query($sql, $link);
		if (!$previewQ) {
			echo "DB Error, could not query match preview stats or something\n";
			echo 'MySQL Error: ' . mysql_error();
			exit;
		}

	  $data = array();
	  while($row = mysql_fetch_assoc($previewQ))
	  {
		 $data[] = $row;
	  }
	  if($data != null)
		$colNames = array_keys(reset($data));
	  else 
		$colNames = null;
	}
	
	
	
	
	
	
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><? echo $_SESSION['tournament']->Title; ?> Preview</title>
    <meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<script type='text/javascript' src='jqueryui/js/jquery-1.10.2.js'></script>
    <link rel="stylesheet" href="stylesheet.css" />
    <!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	<script type='text/javascript'>//<![CDATA[ 
		$(window).load(function(){
			// Event handler to recalculate the score whenever an input control changes.
			$( "#selectMatchID" ).change(function() 
			{ 
				this.form.submit();
			})

		});
	//]]>
	</script>
</head>
<body>
<form id="MatchesForm" action="preview.php" method="POST">
<div id="button_holder">
	<a href="standings.php" style="color:white; text-style:none;">Standings</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="survey.php" style="color:white; text-style:none;">Survey</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="upcoming.php" style="color:white; text-style:none;">Upcoming</a>
</div>
<select name="match_number" id="selectMatchID" style="width: 20em;">
	<option value="" disabled="disabled" <?php echo ($matchNumber == null) ? 'selected="selected"' : ''; ?> >Please select a Match</option>
	<?php
	while($row = mysql_fetch_assoc($matchesQ)) {
	?>
	<option value="<?php echo $row['match_number'] ?>" <?php echo ($matchNumber == $row['match_number']) ? 'selected="selected"' : ''; ?> >
		Match: <?php echo $row['match_number'] ?> - <?php echo $row['start_time'] ?></option>
	<?php
	}
	?>
</select>
<!-- <input value="<?php echo ((!$_POST['show_competitions']) || ($_POST['show_competitions'] == 'Hide All Competitions')) ? 'Show All Competitions' : 'Hide All Competitions'; ?>" name="show_competitions" type="submit"/> -->
 <table border="1" id="preview">
 <tr>
    <?php
		if($colNames == null)
		{
			echo "</tr><td>&nbsp;<tr><th>There is no preview.</th>";
		}
		else
		{
		   //print the header
		   foreach($colNames as $colName)
		   {
			  echo "<th>" . str_replace('_', ' ', $colName) . "</th>";
		   }
    ?>
 </tr>

    <?php
		   //print the rows
		   foreach($data as $row)
		   {
			  echo '<tr style="background-color:'.$row['alliy'].';">';
			  foreach($colNames as $colName)
			  {
				if($colName == "alliy"){
					$color = $row[$colName];
				}
				if($colName == "team_number")
				{
					echo '<td><a href="survey.php?team=' . $row[$colName] . '">' . $row[$colName] . "</a></td>";
				}
				else
				{
					echo '<td>'.$row[$colName].'</td>';
				}
			  }
			  echo "</tr>";
		   }
		}
    ?>
 </table>
 </form>
 </body>
</html>