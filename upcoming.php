<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");

	session_start();
	
	// Connect to the database.
	$link = mysql_connect('Team102.org:3306', 'team102_webuser', 'Gearheads');
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
	$sql = "select mt.match_number,  m.start_time, mt.alliance, tap.*
				from team_avg_pts_v tap, match_teams mt, matches m
				where mt.completed = 'N' and mt.match_number = m.match_number and mt.tournament_id = m.tournament_id
				and mt.tournament_id in (select id from tournaments where active = 'Y')
				and mt.match_number in (select distinct match_number from match_teams 
					where tournament_id in (select id from tournaments where active = 'Y')
					and team_number = 102
					and completed = 'N')
				and tap.team_number = mt.team_number
				and tap.tournament_id in (select id from tournaments where active = 'Y')
				order by mt.match_number, mt.alliance";
//				order by mt.match_number, mt.alliance, tap.rank";
	
	$standingsQ = mysql_query($sql, $link);
	if (!$standingsQ) {
		echo "DB Error, could not query match teams\n";
		echo 'MySQL Error: ' . mysql_error();
		exit;
	}

  $data = array();
  while($row = mysql_fetch_assoc($standingsQ))
  {
     $data[] = $row;
  }
  if($data != null)
	$colNames = array_keys(reset($data));
  else 
	$colNames = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><? echo $_SESSION['tournament']->Title; ?> Upcoming Matches</title>
    <meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<script type='text/javascript' src='jqueryui/js/jquery-1.10.2.js'></script>
    <link rel="stylesheet" href="stylesheet.css" />
    <!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
<div id="button_holder">
	<a href="scoringapp.php" style="color:white; text-style:none;">Scoring App</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="survey.php" style="color:white; text-style:none;">Survey</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="standings.php" style="color:white; text-style:none;">Standings</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="preview.php" style="color:white; text-style:none;">Preview</a>
</div>
<div>&nbsp;</div>
    <?php
 		if($colNames == null)
		{
			echo "<div>There are no upcoming matches to report.</div>";
		}
		else
		{
 		   //print the rows
		   $previousMatch = 0;
		   foreach($data as $row)
		   {
				if($row["match_number"] != $previousMatch)
				{
					if($previousMatch != 0)
						echo "</table>&nbsp;";
					echo '<div class="white">Match ' . $row["match_number"] . ' ~ ' . $row["start_time"] . '</div><table class="upcoming">';
					$previousMatch = $row["match_number"];
				   //print the header
				   foreach($colNames as $colName)
				   {
						if(($colName != "match_number") && ($colName != "alliance") && ($colName != "start_time"))
						{
							?>
							<th><?php echo str_replace('_', ' ', $colName); ?></th>
						<?php
						}
					}
				}
				echo "<tr class='" . strtolower($row["alliance"]) . "'>";
				foreach($colNames as $colName)
				{
					if(($colName != "match_number") && ($colName != "alliance") && ($colName != "start_time"))
					{
						if($colName == "team_number")
						{
							echo '<td><a href="survey.php?team=' . $row[$colName] . '">' . $row[$colName] . "</a></td>";
						}
						else
						{
							echo "<td>".$row[$colName]."</td>";
						}
					}
				}
			  echo "</tr>";
		   }
		}
		echo "</table>";
    ?>
 </table>
 </body>
</html>