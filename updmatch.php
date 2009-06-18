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
?>
<html>
<?php
$Title = "Edit Match";
include 'php/head.php';
?>
<body>
<h1>Edit Match</h1>
<?php

// Load members of team and at the same time check we've got enough

function checkteam($team) {
	$result = $team->list_members();
	if (count($result) < 3)  {
		print <<<EOT
<p>
Sorry but there are not enough members in {$team->display_name()} yet to
make up a match with.
</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back
or <a href="teamsupd.php" target="_top">here</a> to update teams.</p>
</body>
</html>
EOT;
		exit(0);
	}
	foreach ($result as $p) {
		$p->fetchdets();
	}
	return $result;
}

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

$Htmemb = checkteam($mtch->Hteam);
$Atmemb = checkteam($mtch->Ateam);

print <<<EOT
<p>
This match is between
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()}).
</p>
<form action="updmatchdate.php" method="post" enctype="application/x-www-form-urlencoded">
{$mtch->save_hidden()}
<p>
EOT;
$mtch->Date->dateopt("Date set for");
print "with";
$mtch->slackdopt();
print <<<EOT
days to play the games.
</p>
<p>
To change date adjust and
<input type="submit" value="Click here">
</p>
EOT;
//if (count($mtch->Games) != 0)  {
//
//}
?>
</form>
</body>
</html>
