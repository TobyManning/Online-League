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

session_start();
$username = $_SESSION['user_name'];
$userpriv = $_SESSION['user_priv'];
?>
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
$editok = strlen($username) != 0 && ($userpriv == 'A' || $userpriv == 'SA' || $mtch->is_captain($username) != 'N');
?>
<html>
<?php
$Title = "Match Details";
include 'php/head.php';
?>
<body>
<h1>Match Details</h1>
<p>
Match is between
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
<p>Player and board assignments are as follows:</p>
<table class="showmatch">
<tr><th colspan="4" align="center">White</th><th colspan="4" align="center">Black</th><th>Result</th></tr>
<tr><th>Player</th><th>Rank</th><th>Online</th><th>Team</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th></tr>
EOT;
foreach ($mtch->Games as $g) {
	print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>{$g->display_result($editok)}</td>
</tr>
EOT;
}
?>
</table>
<p>Click <a href="javascript:history.back()">here</a> to view some other match.</p>
</body>
</html>
