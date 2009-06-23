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
$mtch = new Match();
try  {
	$mtch->frompost();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

function getgdets($m, $n, &$wplay, &$bplay, &$wteam, &$bteam)  {
	// Assume "home" player is white.
	$wplay = new Player();
	$bplay = new Player();
	$wplay->fromsel($_POST["htm$n"]);
	$bplay->fromsel($_POST["atm$n"]);
	$wteam = $m->Hteam;
	$bteam = $m->Ateam;
	$col = $_POST["colours$n"];

	// Colour will be 0 for Nigiri 1 for Home White 2 for Home Black
	// If set to Nigiri reset randomly

	if ($col == 0)
		$col = rand(1,2);

	if ($col == 2)  {		// Swap round players and teams
		$tmp = $wplay;
		$wplay = $bplay;
		$bplay = $tmp;
		$tmp = $wteam;
		$wteam = $bteam;
		$bteam = $tmp;
	}
}

function setgdets($g, $wplay, $bplay, $wteam, $bteam)  {
	$g->Wteam = $wteam;
	$g->Bteam = $bteam;
	$wplay->fetchdets();
	$bplay->fetchdets();
	$g->Wplayer = $wplay;
	$g->Bplayer = $bplay;
}

//  Check for update of date and days

$newdate = new Matchdate();
$newdate->frompost();
$newslack = $_POST["slackd"];

// Fix changed date

if ($newdate->unequal($mtch->Date) || $newslack != $mtch->Slackdays)  {
	$mtch->Date = $newdate;
	$mtch->Slackdays = $newslack;
	$mtch->dateupdate();
}

// Get and create or update each game
 
$gnum = $mtch->ngames();

for ($gm = 0;  $gm < 3;  $gm++)  {
	if  ($gm < $gnum)  {
		$g = $mtch->Games[$gm];
		// Don't try to change any played games
		if  ($g->Result != 'N')
			continue;
			
		getgdets($mtch, $gm, $wplay, $bplay, $wteam, $bteam);

		// Now see if players have changed and if W or B has changed
		
		if (!($g->Wteam->is_same($wteam) && $g->Wplayer->is_same($wplay) && $g->Bplayer->is_same($bplay)))  {
			setgdets($g, $wplay, $bplay, $wteam, $bteam);
			$g->update_players();
		}
	}
	else  {
		$g = $mtch->newgame();
		getgdets($mtch, $gm, $wplay, $bplay, $wteam, $bteam);
		setgdets($g, $wplay, $bplay, $wteam, $bteam);
		$g->create_game();
	}
}
?>
<html>
<?php
$Title = "Match Edit Result";
include 'php/head.php';
?>
<body>
<h1>Match Edit Result</h1>
<p>
Match is set up between
<?php
print <<<EOT
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()})
on
{$mtch->Date->display()} with {$mtch->Slackdays} to play the games.
</p>
<p>Team captains are {$mtch->Hteam->Captain->display_name()} for {$mtch->Hteam->display_name()}
and {$mtch->Ateam->Captain->display_name()} for {$mtch->Ateam->display_name()}.
</p>
<p>Player assignments are as follows:</p>
<table>
<tr><th colspan="3" align="center">White</th><th colspan="3" align="center">Black</th><th>Result</th></tr>
<tr><th>Player</th><th>Rank</th><th>Team</th></tr>
EOT;
foreach ($mtch->Games as $g) {
	switch ($g->Result) {
	default:
		$res = '&nbsp;';
		break;
	case 'W':
		$res = "White Win";
		break;
	case 'J':
		$res = "Jigo";
		break;
	case 'B':
		$res = "Black Win";
		break;
	}
	print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>$res</td>
</tr>
EOT;
}
print <<<EOT
</table>
<p>Click <a href="updmatch.php?{$mtch->urlof()}">here</a> to change any details of the match.</p>
EOT;
?>
<p>Click <a href="matchupdb.php">here</a> to edit some other match.</p>
</body>
</html>
