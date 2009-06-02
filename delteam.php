<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
?>
<html>pla
<?php
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	include 'php/wrongentry.php';
	exit(0);
}
$ret = mysql_query("delete from team where {$team->queryof()}");
if (!$ret) {
	$mess = "Cannot delete team";
	include 'php/dataerror.php';
	exit(0);
}
$nrows = mysql_affected_rows();
if ($nrows == 0) {
	$mess = "No team deleted";
	include 'php/dataerror.php';
	exit(0);
}
$Title = "Deletion of {$team->display_name()} complete";
include 'php/head.php';
print <<<EOT
<body>
<h1>Deletion of {$team->display_name()} complete</h1>
<p>
Deletion of team {$team->display_name()} was successful.</p>
EOT;
?>
<p>
Click <a href="teamupd.php" target="_top">here</a> to return to the team update menu.
</p>
</body>
</html>
