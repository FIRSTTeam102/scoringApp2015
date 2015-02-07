
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>FRC 2015 Scoring App</title>
    <meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<script type='text/javascript' src='jqueryui/js/jquery-1.10.2.js'></script>
<!--    <link rel="stylesheet" href="stylesheet.css" /> -->
	<style type="text/css">
	body {
	background-image: url("http://www.team102.com/2015Dev/Geary.jpg");
	background-repeat: repeat-y;
	}
	.header{
	display: flex;
	flex-direction:row-reverse;
	flex-wrap: wrap;
	align-content: space-between;
	}
	.nav {
    padding: 0 25px;
    border: 25px;
    margin: 0 15 25 0px;
	order: 2;
	align-self: flex-end;
	flex-grow: 5;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
	font-weight: 300;
	color: d5d5d8;
	}
	.logo {
	 width: 300px;
    padding: 25px;
    border: 25px;
    margin: 0;
	order: 1;
	flex-grow: 1;
	font-size: 21px;
	color: d5d5d8;
	}
	.bigButton {
	background-color: #F7b448;
	color: 151a2e;
	height: 60 px;
	width: 102 px;
	font-size: 20px;
	margin-top: 17px;
	}
	.fieldLabel	{
		font-size: 20px;
	}
	</style>
    <!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	<script type='text/javascript'>//<![CDATA[ 
		$(document).ready(function(){
				});
	//]]>
	</script>
</head>
<body class="no-js">
    <div id="page">
        <div class="header">
			<div class="logo">
			<img src="favicon.ico" style="float: right; padding: 3px 15px 0px 10px;"/>
				<div id="team102" style="text-align: right">FIRST Team 102</div>
				<div style="text-align: right">The Gearheads</div>
				<div style="text-align: right">Somerville High School, NJ</div>
			</div>
		</div>
		<form id="indexForm" action="index.php" method="POST">
			<div id="login" style="margin-left: 15px; padding-left: 10px;">
				<table style="background-color: #F7b448; padding: 5px;">
				<tr><td><label for="txtInitials" class="fieldLabel">Your Initials:</label></td>
					<td><input type="text" maxLength="4" name="txtInitials" tabindex="1" style="width: 90px;"/></td>
				</tr>
				<tr><td><label for="txtPassword" class="fieldLabel">Password:</label></td>
					<td><input type="password" name="txtPassword" tabindex="4" style="width: 90px;"/></td>
				</tr>
				</table>
            </div>
			<div class="nav">
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnScoringApp" value="Team 102 Scoring App" /></div>
				Scoring App
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnStandings" value="Standings Page" /></div>
				Each team's ranking and statistics.
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnSurvey" value="Survey Results" /></div>
				Team results based on Team 102's scouting survey.
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnUpcoming" value="Upcoming Matches" /></div>
				Schedule for matches that will be taking place
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnAlliance" value="Alliance Selection" /></div>
				Alliances that have been formed for the final matches
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnLighterApp" value="Alliance Color Lighter" /></div>
				Illuminate screen in blue or red to show support for your alliance!
				<div style="padding-top: 10px;padding-bottom: 10px;"><input class="bigButton" type="submit" name="btnOverview" value="Game Overview" /></div>
				An overview of the game.
			</div>
		</form>
    </div>
</body>