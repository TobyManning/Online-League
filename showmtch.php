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
include 'php/params.php';

$pars = new Params();
$pars->fetchvalues();

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	if ($e->Nfound) {
		$loc = "histshowmtch.php?hmi={$e->Mid}";
		include 'php/jumpto.php';
		exit(0);
	}
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
$editok = strlen($username) != 0 && ($userpriv == 'A' || $userpriv == 'SA' || $mtch->is_captain($username) != 'N');

// Is this a "handicappable" division

$hcapable = $mtch->Division >= $pars->Hdiv;
$hred = $pars->Hreduct;

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
in
{$mtch->Date->display_month()}.
</p>
<p>Team captains are {$mtch->Hteam->display_captain()} for {$mtch->Hteam->display_name()}
and {$mtch->Ateam->display_captain()} for {$mtch->Ateam->display_name()}.
</p>

EOT;
if ($mtch->Result=='H' || $mtch->Result=='A' || $mtch->Result=='D') {
	$h = $mtch->Hscore + 0;
	$a = $mtch->Ascore + 0;
	print <<<EOT
<p>The final score was $h-$a.</p>
<p>Player and board assignments were:</p>

EOT;
}
else {
	if ($mtch->Result=='P') {
		$h = $mtch->Hscore + 0;
		$a = $mtch->Ascore + 0;
		print "<p>Score to date is $h-$a</p>\n";
	}
	print <<<EOT
<p>Player and board assignments are as follows:</p>

EOT;
}
print <<<EOT
<table class="showmatch">
<tr><th colspan="5" align="center">White</th><th colspan="4" align="center">Black</th><th>Result</th></tr>
<tr><th>Date</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th></tr>

EOT;
$hcaps = array();
$boards = array();
$board = 1;
foreach ($mtch->Games as $g) {
	$bpre = $bpost = $wpre = $wpost = "";
	switch ($g->Result)  {
	case 'W':
		$wpre = "<b>";
		$wpost = "</b>";
		break;
	case 'B':
		$bpre = "<b>";
		$bpost = "</b>";
		break;
	}
	$hstones = $g->Wplayer->Rank->Rankvalue - $g->Bplayer->Rank->Rankvalue - $hred;
	if ($hstones > 9)
		$hstones = 9;
	if ($hcapable && $hstones > 0)  {
		array_push($hcaps, $hstones);
		array_push($boards, $board);
	}
	print <<<EOT
<tr>
<td>{$g->date_played()}</td>
<td>$wpre{$g->Wplayer->display_name()}$wpost</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>$bpre{$g->Bplayer->display_name()}$bpost</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>{$g->display_result($editok)}</td>
</tr>
EOT;
	$board++;
}
?>
</table>
<?php
if (count($boards) > 0)  {
	$n = count($boards);
	print <<<EOT
<h2>Handicaps</h2>
<p>Handicaps apply to

EOT;
	if ($n == 1)
		print "1 board\n";
	else
		print "$n boards\n";
	print <<<EOT
in this match as follows:</p>
<table class="showmatch">
<tr><th>Board</th><th>Handicap</th></tr>

EOT;
	for ($board = 0; $board < $n;  $board++)  {
		print "<tr><td>$boards[$board]</td>\n<td>";
		$h = $hcaps[$board];
		if ($h == 1)
			print	"No komi";
		else
			print "$h stones";
		print "</td></tr>\n";
	}
	print "</table>\n";
}
?>
<p>Click <a href="javascript:history.back()">here</a> to return to your previous page
or <a href="matchesb.php">here</a> to look at other matches.</p>
</body>
</html>
