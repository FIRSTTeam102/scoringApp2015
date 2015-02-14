<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
session_start();

if(isset($_POST[btnLighterApp])){
	header('location: lighter.html');
}

require_once "php/HTML/Template/IT.php";


$tpl = new HTML_Template_IT("./templates");

$tpl->loadTemplatefile("team102.html", true, true);
$tpl->touchBlock("main");

if(isset($_POST['btnScoringApp']) || isset($_POST['btnStandings']) || isset($_POST['btnSurvey']) || isset($_POST['btnPreview']) 
|| isset($_POST['btnAlliance']))
{
	$password = $_POST['txtPassword'];
	$initials = $_POST['txtInitials'];

	$link = @mysql_connect('team102.org:3306', 'team102_webuser', $password);
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

			if($password && $initials != "")
			{
				$_SESSION['initials'] = strtoupper($initials);
				$_SESSION['password'] = $password;
				
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
else
{
}
$tpl->show();
?>