<?php
//   Copyright 2013 John Collins

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

include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';

try {
        $player = new Player();
        $player->fromid($userid);
}
catch (PlayerException $e) {
        $mess = $e->getMessage();
        include 'php/wrongentry.php';
        exit(0);
}

// Get the teams this player is captain of
	
try {	
	$captain_of = list_teams_captof($player);
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

// Get matches we might want to send messages about.

$capt_matches = array();
if  (count($captain_of) != 0)  {

	// Do each team in turn it's easier to code

	foreach ($captain_of as $team) {
		$ret = mysql_query("select ind from lgmatch where (result='N' or result='P') and ({$team->queryof('hteam')} or {$team->queryof('ateam')}) order by matchdate");
		if ($ret && mysql_num_rows($ret) > 0)  {
			while ($row = mysql_fetch_array($ret))  {
				try {
					$mtch = new Match($row[0]);
					$mtch->fetchdets();
					$mtch->fetchteams();
					$mtch->fetchgames();
					array_push($capt_matches, $mtch);
				}
				catch (MatchException $e) {
					continue;
				}
			}
		}
	}	 
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Qun = htmlspecialchars($username);
$Sun = mysql_real_escape_string($userid);
$Title = "Messages for $Qun";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php';
print "<h1>Messages for $Qun</h1>\n";
$ret = mysql_query("select fromuser,created,gameind,subject,hasread,ind from message where touser='$Sun' order by created,subject");
if (!$ret || mysql_num_rows($ret) == 0)  {
	print "<p>No received messages for $Qun.</p>\n";
}
else  {
	print <<<EOT
<p>The following messages have been received for $Qun.</p>
<table class="resultsb">
<tr>
	<th>From</th>
	<th>Date</th>
	<th>Time</th>
	<th>Game</th>
	<th>Subject</th>
EOT;
	while ($row = mysql_fetch_assoc($ret))  {
		$fu = $row["fromuser"];
		$cr = $row["created"];
		$gid = $row["gameind"];
		$subj = $row["subject"];
		$qsubj = htmlspecialchars($subj);
		$hasr = $row["hasread"];
		$ind = $row["ind"];
		$fp = new Player();
		$fp->fromid($fu);
		$pre = $post = "";
		if (!$hasr)  {
			$pre = "<strong>";
			$post = "</strong>";			
		}
		if  (preg_match("/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+):(\d+)/", $cr, $matches))  {
			$dat = $matches[3] . '/' . $matches[2] . '/' . $matches[1];
			$tim = $matches[4] . ':' . $matches[5] . ':' . $matches[6];
		}
		else {
			$dat = $tim = $cr;
		}
		$ag = $gid == 0? "Other": "About game";
		print <<<EOT
<tr>
	<td>$pre{$fp->display_name()}$post</td>
	<td>$pre$dat$post</td>
	<td>$pre$tim$post</td>
	<td>$pre$ag$post</td>
	<td><a href="dispmessage.php?mi=$ind">$pre$qsubj$post</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
if  (count($capt_matches) != 0)  {
	print <<<EOT
<h1>Outstanding matches</h1>
<p>You might want to send the opposing team captain a message about
one of the following outstanding matches.</p>
<table class="showmatch">
<tr><th>Team A</th><th>Capt</th><th>Team B</th><th>Capt</th><th>Send</th></tr>

EOT;
	foreach ($capt_matches as $mtch) {
		print <<<EOT
<tr>
	<td>{$mtch->Hteam->display_name(true)}</td>
	<td>{$mtch->Hteam->display_captain(true)}</td>
	<td>{$mtch->Ateam->display_name(true)}</td>
	<td>{$mtch->Ateam->display_captain(true)}</td>
	<td><a href="composemsg.php?{$mtch->urlof()}">Message</a></td>
</tr>

EOT;
	}
}
?>
<h1>Outstanding games</h1>
<?php

// Now for user's games

$osgames = array();
$ret = mysql_query("select ind from game where result='N' and (({$player->queryof('w')}) or ({$player->queryof('b')})) order by matchdate");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_array($ret))  {
		try {
			$g = new Game($row[0]);
			$g->fetchdets();
			if (!$g->Wteam || !$g->Bteam)
				continue;
			array_push($osgames, $g);
		}
		catch (GameException $e) {
			continue;
		}
	}
}
if (count($osgames) == 0)
	print <<<EOT
<p>You currently do not have any outstanding games to play.</p>

EOT;
else  {
	print <<<EOT
<p>You might want to send a message about one of the following
pending games.</p>
<table class="showmatch">
<tr>
<th colspan="3" align="center">White</th>
<th colspan="3" align="center">Black</th></tr>
<tr>
<th>Player</th>
<th>Online name</th>
<th>Team</th>
<th>Player</th>
<th>Online name</th>
<th>Team</th>
<th>Message</th>
</tr>

EOT;
	foreach ($osgames as $g) {
		print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
<td><a href="composemsg.php?{$g->urlof()}">Send</a></td>
</tr>

EOT;
	}
print <<<EOT
</table>

EOT;
}
?>
<h1>General message</h1>
<p>If you want to send someone a new message about any subject,
<a href="composegmsg.php">Click Here</a>.</p>
</div>
</div>
</body>
</html>
