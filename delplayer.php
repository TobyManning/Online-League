<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
?>
<html>
<?php
try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$ret = mysql_query("delete from player where {$player->queryof()}");
if (!$ret) {
	$mess = "Cannot delete player";
	include 'php/dataerror.php';
	exit(0);
}
$nrows = mysql_affected_rows();
if ($nrows == 0) {
	$mess = "No player deleted";
	include 'php/dataerror.php';
	exit(0);
}
$Title = "Deletion of {$player->display_name()} complete";
include 'php/head.php';
print <<<EOT
<body>
<h1>Deletion of {$player->display_name()} complete</h1>
<p>
Deletion of player {$player->display_name()} was successful.</p>
EOT;
?>
<p>
Click <a href="playupd.php" target="_top">here</a> to return to the player update menu.
</p>
</body>
</html>
