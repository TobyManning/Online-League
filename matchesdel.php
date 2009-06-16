<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
$div = $_GET["div"];
if (strlen($div) == 0) {
	include 'php/wrongentry.php';
	exit(0);
}
mysql_query("delete from lgmatch where divnum=$div");
mysql_query("delete from game where divnum=$div and result='N'");
?>
<html>
<?php
$Title = "Delete Matches complete";
include 'php/head.php';
?>
<body onload="javascript:opener.location.reload()">
<h1>Delete Matches Completed</h1>
<?php
print <<<EOT
<p>Finished deleting matches for Division $div</p>
EOT;
?>
<p>Click <a href="matchupdb.php">here</a> to return to the match editing page.</p>
</body>
</html>
