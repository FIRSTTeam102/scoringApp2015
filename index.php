<?php

set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
session_start();

if(isset($_POST[btnLighterApp])){
	header('location: lighter.html');
}

require_once "php/HTML/Template/IT.php";


$tpl = new HTML_Template_IT("./templates");

$tpl->loadTemplatefile("PublicHome.html", true, true);
$tpl->touchBlock("main");

if(isset($_POST['btnScoringApp']) || isset($_POST['btnStandings']) || isset($_POST['btnSurvey']) || isset($_POST['btnPreview']) 
|| isset($_POST['btnAlliance']) || isset($_POST['btnPrivate']) || isset($_POST['btnUpcoming']))
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
				else if(isset($_POST['btnPrivate']))
					header ("location: team102.php");
			}
		}
	}
	@mysql_close($link);
}
$tpl->show();
?>
