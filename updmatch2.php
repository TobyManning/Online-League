<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
//   Copyright 2009 John Collins

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

// If colours are set up don't change anything

$colset = false;
$hadnig = false;
$hadw = false;
$hadb = false;

for ($b = 0;  $b < 3;  $b++)  {
	$h = new Player();
	$a = new Player();
	$h->fromsel($_POST["htm$b"]);
	$a->fromsel($_POST["atm$b"]);
	$h->fetchdets();
	$a->fetchdets();
	$cols[$b] = $_POST["colours$b"];
	switch ($cols[$b]) {
	default:
		$hadnig = true;
		break;
	case 1:		//  Home player white
		$hadw = true;
		$colset = true;
		break;
	case 2:		//  Home player black
		$hadb = true;
		$colset = true;
		break;
	}
	$hplayer[$b] = $h;
	$aplayer[$b] = $a;
}

//  Sort an array of players by descending rank - stably
//  Use bubble sort

function sortrank($arr) {
	if ($arr[0]->Rank->Rankvalue < $arr[1]->Rank->Rankvalue) {
		$t = $arr[0];
		$arr[0] = $arr[1];
		$arr[1] = $t;
	}
	if ($arr[1]->Rank->Rankvalue < $arr[2]->Rank->Rankvalue) {
		$t = $arr[1];
		$arr[1] = $arr[2];
		$arr[2] = $t;
	}
	if ($arr[0]->Rank->Rankvalue < $arr[1]->Rank->Rankvalue) {
		$t = $arr[0];
		$arr[0] = $arr[1];
		$arr[1] = $t;
	}
}

// If colours not set, sort players into rank order and assign
// otherwise set nigiri randomly 

if ($colset)  {
	//  Colours set - if had nigiri try to make at least one different
	
	if ($hadnig)  {
		if ($hadw && $hadb) {
			// Had both white and black - if middle one nigiri choose at random
			// Otherwise make nigiri one opposite of middle one
			if ($cols[1] <= 1)
				$cols[1] = rand(1,2);
			elseif ($cols[0] <= 1)
				$cols[0] = 3 - $cols[1];
			else
				$cols[2] = 3 - $cols[1];
		}
		elseif ($hadw)  {
			//  White set set nigiri ones to black
			for ($b = 0;  $b < 3;  $b++)
				if ($cols[$b] <= 1)
					$cols[$b] = 2;
		}
		else {
			//  Black set set nigiri ones to white
			for ($b = 0;  $b < 3; $b++)
				if ($cols[$b] <= 1)
					$cols[$b] = 1;
		}
	}
}
else {

	// So now we sort each time into order
	
	sortrank($hplayer);
	sortrank($aplayer);

	//  Now assign colours either WBW or BWB
	
	$cols[0] = rand(1,2);
	$cols[1] = 3 - $cols[0];
	$cols[2] = $cols[0];
}

// If matchdate has changed, fix it

$newdate = new Matchdate();
$newdate->frompost();
$newslack = $_POST["slackd"];

// Fix changed date

if ($newdate->unequal($mtch->Date) || $newslack != $mtch->Slackdays)  {
	$mtch->Date = $newdate;
	$mtch->Slackdays = $newslack;
	$mtch->dateupdate();
}

// Set up game details according to who is white/black
// col=1 means "home" player is white otherwise black

function setgteams($g, $col, $hteam, $ateam, $hplay, $aplay)  {
	if ($col == 1)  {
		$g->Wteam = $hteam;
		$g->Bteam = $ateam;
		$g->Wplayer = $hplay;
		$g->Bplayer = $aplay;
	}
	else  {
		$g->Wteam = $ateam;
		$g->Bteam = $hteam;
		$g->Wplayer = $aplay;
		$g->Bplayer = $hplay;
	}
}

// Get and create or update each game
 
$gnum = $mtch->ngames();

for ($gm = 0;  $gm < 3;  $gm++)  {
	if  ($gm < $gnum)  {
		// Game already allocated
		$g = $mtch->Games[$gm];
		// Don't try to change any played games
		if  ($g->Result == 'N') {
			setgteams($g, $cols[$gm], $mtch->Hteam, $mtch->Ateam, $hplayer[$gm], $aplayer[$gm]);
			$g->update_players();
		}
	}
	else  {
		$g = $mtch->newgame();
		setgteams($g, $cols[$gm], $mtch->Hteam, $mtch->Ateam, $hplayer[$gm], $aplayer[$gm]);		
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
{$mtch->Date->display()} with {$mtch->Slackdays} days to play the games.
</p>
<p>Team captains are {$mtch->Hteam->Captain->display_name()} for {$mtch->Hteam->display_name()}
and {$mtch->Ateam->Captain->display_name()} for {$mtch->Ateam->display_name()}.
</p>
<p>Player assignments are as follows:</p>
<table>
<tr><th colspan="3" align="center">White</th><th colspan="3" align="center">Black</th><th>Result</th></tr>
<tr><th>Player</th><th>Rank</th><th>Team</th><th>Player</th><th>Rank</th><th>Team</th></tr>
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
$mtch->mail_allocated();
print <<<EOT
</table>
<p>Click <a href="updmatch.php?{$mtch->urlof()}">here</a> to change any details of the match.</p>
EOT;
?>
<p>Click <a href="matchupdb.php">here</a> to edit some other match.</p>
</body>
</html>
