<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = mysql_real_escape_string($value);
	$ret = mysql_query("select $column from team where $column='$qvalue'");
	if ($ret && mysql_num_rows($ret) != 0)  {
		include 'php/nameclash.php';
		exit(0);
	}
}

function checkname($newteam) {
	$ret = mysql_query("select name from team where {$newteam->queryof()}");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$column = "name";
		$value = $newteam->display_name();
		include 'php/nameclash.php';
		exit(0);
	}
}

$action = substr($_POST["subm"], 0, 1);
$teamname = $_POST["teamname"];
$teamdescr = $_POST["teamdescr"];
$teamdiv = $_POST["division"];
$teamcapt = $_POST["captain"];

if (!preg_match("/(.*):(.*)/", $teamcapt, $matches))  {
		include 'php/wrongentry.php';
		exit(0);
}

$captfirst = $matches[1];
$captlast = $matches[2];

switch ($action) {
case 'A':
	if (strlen($teamname) == 0)  {
		include 'php/wrongentry.php';
		exit(0);
	}
	$team = new Team($teamname);
	checkname($team);
	$team->Description = $teamdescr;
	$team->Division = $teamdiv;
	$team->Captain = new Player($captfirst, $captlast);
	$team->create();
	$Title = "Team {$team->display_name()} created OK";
	break;
default:
	try {
		$origteam = new Team();
		$origteam->frompost();
		$origteam->fetchdets();
	}
	catch (TeamException $e) {
		include 'php/wrongentry.php';
		exit(0);
	}
	
	// Check name changes
	
	$newteam = new Team($teamname);
	if  (!$origteam->is_same($newteam))  {
		checkname($newteam);
		$origteam->updatename($newteam);
	}
	
	$origteam->Description = $teamdescr;
	$origteam->Division = $teamdiv;
	$origteam->Captain = new Team($captfirst, $captlast);
	$origteam->update();
	$Title = "Team {$origteam->display_name()} updated OK";
	break;
}
print "<html>\n";
include 'php/head.php';
print <<<EOT
<body>
<h1>$Title</h1>
<p>$Title.</p>
EOT;
?>
<p>
Click <a href="teamupd.php" target="_top">here</a> to return to the team update menu.
</p>
</body>
</html>
