<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");

	session_start();
	
	// Connect to the database.
	$publicPwd = file_get_contents('junk');
	$link = @mysql_connect('team102.org:3306', 'team102_readonly', $publicPwd);
	if(!$link)
	{
		echo sprintf('Could not connect to database, Err: %s', mysql_error());
		exit;
	}	
	if (!mysql_select_db('team102_2015', $link)) {
		echo sprintf('Could not select database, Err: %s', mysql_error());
		exit;
	}
	$sort = "Avg_Earned_Pts_Per_Match desc";
	if($_GET['sort'] != null)
		$sort = $_GET['sort'];

/*	$sql = "select mt.tournament_id, mt.team_number, count(mt.match_number) num_matches"
	. ", avg(mtc.tote_end_height - mtc.tote_start_height) as avgAdded"
	. ", sum(CASE mtc.container_scored"
	. "            WHEN 'Y' THEN 1"
	. "            ELSE 0"
	. "        END) AS `total_containers`"
	. ", sum(CASE mtc.litter_scored"
	. "            WHEN 'Y' THEN 1"
	. "            ELSE 0"
	. "        END) AS `total_litter`"
	. ", avg(mtc.coop_end_height - mtc.coop_start_height) as avgCoopAdded"
	. " FROM ((`match_teams` `mt`"
	. "       LEFT JOIN `match_team_cycles` `mtc` on(((`mtc`.`tournament_id` = `mt`.`tournament_id`)"
	. "                                               AND (`mtc`.`match_number` = `mt`.`match_number`)"
	. "                                               AND (`mtc`.`team_number` = `mt`.`team_number`))))"
	. "      JOIN `tournaments` `t`)"
	. " WHERE ((`mt`.`tournament_id` = `t`.`ID`)"
	. "       AND (`mt`.`completed` = 'Y')"
	. "       AND (`t`.`active` = 'Y'))"
	. " group by 1, 2 order by ";
*/
	$sql = "select * from team_avg_pts_v where tournament_id = 'B' order by ";		// Temporary.
	
	if($_GET['AllTournaments'] != null)
		$sql = "select apv.* 
				from t_team_avg_pts_v  apv
				where team_number in (select team_number from tournament_teams tt, tournaments t where tt.tournament_id = t.id and t.active = 'Y')
				order by ";	// Over all tournaments that are not Active = 'O'
	
	$sql .= mysql_real_escape_string($sort);
	
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
    <title><? echo $_SESSION['tournament']->Title; ?> Standings</title>
    <meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<script type='text/javascript' src='jqueryui/js/jquery-1.10.2.js'></script>
    <link rel="stylesheet" href="stylesheet.css" />
    <!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
<div id="button_holder">
<div id="button_holder">
	<a href="scoringapp.php" style="color:white; text-style:none;">Scoring App</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="survey.php" style="color:white; text-style:none;">Survey</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="preview.php" style="color:white; text-style:none;">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="upcoming.php" style="color:white; text-style:none;">Upcoming</a>
</div></div>
<div>&nbsp;</div>
 <table border="1" id="standings">
    <?php
 		if($colNames == null)
		{
			echo "<tr><th>There are no standings to report.</th></tr>";
		}
		else
		{
 		   //print the rows
		   $i = 1;
		   foreach($data as $row)
		   {
				if(($i - 1) % 10 == 0)
				{
					echo "<th>row</th>";
				   //print the header
				   foreach($colNames as $colName)
				   {
						if(($colName == 'team_number') || ($colName == 'luck_factor') || ($colName == 'num_matches') || ($colName == 'rank'))
						{
							$default_sort = ' asc';
							$second_sort = ' desc';
						}
						else
						{
							$default_sort = ' desc';
							$second_sort = ' asc';
						}
				   ?>
					 <th><a href="standings.php?sort=<?php echo ($sort == $colName . $default_sort) ? $colName . $second_sort: $colName . $default_sort;?>" 
									<?php echo (($sort == $colName . $default_sort) || ($sort == $colName . $second_sort)) ? ' class="currentSort"' : '';?> >
							<?php echo str_replace('_', ' ', $colName); ?></a></th>
					<?php
				   }
				}
			  echo "<tr><td>" . $i++ . "</td>";
			  foreach($colNames as $colName)
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
			  echo "</tr>";
		   }
		}
    ?>
 </table>
 </body>
</html>