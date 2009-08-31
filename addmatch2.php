<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';

$div = $_POST['div'];
if (strlen($div) == 0)  {
	$mess = "No division?";
	include 'php/wrongentry.php';
	exit(0);	
}
$hteam = $_POST['hteam'];
$ateam = $_POST['ateam'];
$slack = $_POST['slackd'];
if (strlen($hteam) == 0 || strlen($ateam) == 0)  {
	$mess = "Missing teams?";
	include 'php/wrongentry.php';
}
$dat = new Matchdate();
$dat->frompost();
$mtch = new Match(0, $div);
$mtch->set_hometeam($hteam);
$mtch->set_awayteam($ateam);
$mtch->Date = $dat;
$mtch->Slackdays = $slack;
try {
	// Fetch the team details not because we need them, but
	// so as to check for garbled team names.
	$mtch->fetchteams();
	$mtch->create();
	// That sets the match ind in $mtch which the updmatch call uses later.
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Add Match division $div OK";
include 'php/head.php';
?>
<body>
<?php
print <<<EOT
<h1>Create Match division $div successful</h1>
<p>
Successfully completed creation of Match between
{$mtch->Hteam->display_name()} and
{$mtch->Ateam->display_name()} set for
{$mtch->Date->display()}.
</p>
<p>
<a href="updmatch.php?{$mtch->urlof()}</a>">Click here</a> to add team members.
</p>
EOT;
?>
</body>
</html>
