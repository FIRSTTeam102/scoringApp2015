<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../");
	session_start();
	
	// if we cannot get the password from session - redirect to the starting page.
	if(!$_SESSION['password'])
	{
		header("Location: index.php");     /* Redirect browser */
		exit();
	}

	require_once "php/HTML/Template/IT.php";
	$tpl = new HTML_Template_IT("./templates");
	$tpl->loadTemplatefile("jordan.html", true, true);

	$link = mysql_connect('team102.org:3306', 'team102_webuser', $_SESSION['password']);
	
	if (!mysql_select_db('team102_2015', $link)) {
		echo sprintf('Could not select database, Err: %s', mysql_error());
		exit;
	}
	@mysql_close($link);


	$tpl->setCurrentBlock("main");
	$tpl->setVariable("tournamentTitle", "Jordan's Tournament") ;

	$tpl->show();
?>