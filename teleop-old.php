<?php

set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
session_start();

require_once "php/HTML/Template/IT.php";

$tpl = new HTML_Template_IT("./templates");

$tpl->loadTemplatefile("teleop.html", true, true);

// if we cannot get the password from session - redirect to the starting page.
if(!$_SESSION['password'])
{
	header("Location: team102.php"); 	/* Redirect browser */
	exit();
}

// Connect to the database.
$link = mysql_connect('Team102.org:3306', 'team102_webuser', $_SESSION['password']);

if (!mysql_select_db('team102_2015', $link)) {
	echo sprintf('Could not select database, Err: %s', mysql_error());
	exit;
}

// Determine if we have submitted a teleop period.
if(isset($_POST['btnNext']) || isset($_POST['btnDone']))
{
	
/*	var_dump($_POST);	// Use this to see a dump of the _POST variables.
	echo '<br>';
	die;
*/	
	// Save submitted values to the database.
	if(isset($_POST['btnDone']))
	{

	}
}
if(isset($_POST['btnScoringApp']) || isset($_POST['btnStandings']) || isset($_POST['btnSurvey']) || isset($_POST['btnPreview']) 
|| isset($_POST['btnAlliance']))
{
	$publicPwd = file_get_contents('junk');
	$link = @mysql_connect('team102.org:3306', 'team102_readonly', $publicPwd);
	if(!$link)
	{
        $tpl->setCurrentBlock("reportError") ;
		$error = mysql_error();
		if(strpos($error, 'Access denied for user') >= 0)
			$tpl->setVariable("alertError", "Could not login to the database. Please try again.");
		else
			$tpl->setVariable("alertError", sprintf('Could not connect to the database.\n Err: %s', htmlentities(mysql_error())));
        $tpl->parseCurrentBlock() ;
	}
	else
	{
		if (!@mysql_select_db('team102_2015', $link)) {
        	$tpl->setCurrentBlock("reportError") ;
			$tpl->setVariable("alertError", sprintf('Could not connect to the database.\n Err: %s', htmlentities(mysql_error())));
        	$tpl->parseCurrentBlock() ;
		}
		else
		{
			$error = null;
			$_SESSION['tournament'] = null;
				
			if($publicPwd != "")
			{
				$_SESSION['publicPwd'] = $publicPwd;
				
				if(isset($_POST['btnScoringApp']))
					header ("location: scoringapp.php");
				else if(isset($_POST['btnStandings']))
					header ("location: standings.php");
				else if(isset($_POST['btnSurvey']))
					header ("location: survey.php");
				else if(isset($_POST['btnPreview']))
					header ("location: preview.php");
				else if(isset($_POST['btnAlliance']))
					header ("location: alliance.php");
				else if(isset($_POST['btnUpcoming']))
					header ("location: upcoming.php");
			}
		}
	}
	@mysql_close($link);
}
$tpl->show();
?>
