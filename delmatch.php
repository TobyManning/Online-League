<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->delmatch();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Delete match completed";
include 'php/head.php';
?>
<body>
h1>Delete Match Completed</h1>

<?php
print <<<EOT
<p>
Successfully completed deletion of Match between
{$mtch->Hteam->display_name()} and
{$mtch->Ateam->display_name()} set for
{$tach->Date->display()}.
</p>
<p>
<a href="matchtmupd.php?div={$mtch->Division}">Click here</a> to go back
to editing matches for division {$mtch->Division}.
</p>
EOT;
?>
</body>
</html>
