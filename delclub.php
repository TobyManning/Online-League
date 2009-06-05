<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
?>
<html>
<?php
try {
	$club = new Club();
	$club->fromget();
	$club->fetchdets();
}
catch (ClubException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$ret = mysql_query("delete from club where {$club->queryof()}");
if (!$ret) {
	$mess = "Cannot delete club";
	include 'php/dataerror.php';
	exit(0);
}
$nrows = mysql_affected_rows();
if ($nrows == 0) {
	$mess = "No clubs deleted";
	include 'php/dataerror.php';
	exit(0);
}
$Title = "Deletion of {$club->display_name()} complete";
include 'php/head.php';
print <<<EOT
<body>
<h1>Deletion of {$club->display_name()} complete</h1>
<p>
Deletion of club {$club->display_name()} was successful.</p>
EOT;
?>
<p>
Click <a href="clubupd.php" target="_top">here</a> to return to the club update menu.
</p>
</body>
</html>
