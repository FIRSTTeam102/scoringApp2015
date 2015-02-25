<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
	session_start();
	
	// if we cannot get the password from session - redirect to the starting page.
	if(!$_SESSION['password'])
	{
		header("Location: team102.php"); 	/* Redirect browser */
		exit();
	}
	require_once "php/HTML/Template/IT.php";

	$tpl = new HTML_Template_IT("./templates");

	$tpl->loadTemplatefile("scoringapp.html", true, true);

	// Connect to the database.
	$link = mysql_connect('Team102.org:3306', 'team102_webuser', $_SESSION['password']);
	
	if (!mysql_select_db('team102_2015', $link)) {
    		echo sprintf('Could not select database, Err: %s', mysql_error());
    		exit;
	}
	
	if((!$_SESSION['tournament']) || ($_SESSION['tournament'] == null))
	{
		$sql    = 'SELECT * FROM tournaments WHERE active = "Y"';
		$result = mysql_query($sql, $link);
		if(!$result)
			die(sprintf("Error querying active tournament Err: %s", mysql_error()));

		$tournament = mysql_fetch_object($result);
		if(!$tournament)
			die("No active tournament found.");
		$_SESSION['tournament'] = $tournament;
	}
	else
	{
		$tournament = $_SESSION['tournament'];
	}
	
	$tpl->setCurrentBlock("main") ;
	$tpl->setVariable("tournamentTitle", $_SESSION['tournament']->Title) ;
	$tpl->parseCurrentBlock() ;

	if(isset($_POST['btnChooseMatch']))
	{
		if($_POST['rdoAlliance'] != null)
		{
			$_SESSION['alliance'] = $_POST['rdoAlliance'];
			header ("location: choosematch.php");
		}
	}
	@mysql_close($link);
	$tpl->show();
?>
