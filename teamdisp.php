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
$logged_in = strlen($username) != 0;
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
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Team {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<h1>Team {$team->display_name()}</h1>
<p>
Team {$team->display_name()} - {$team->display_description()} - division
{$team->display_division()}
</p>
<p>
Team captain is {$team->display_captain()}.
{$team->display_capt_email($logged_in)}
</p>
EOT;
if (($userpriv == 'A' || $userpriv == 'SA') && !$team->Paid)
	print <<<EOT
<p><b>Team has not paid.</b></p>
EOT;
print <<<EOT
<h3>Members</h3>
<table class="teamdisp">
<tr>
	<th>Name</th>
	<th>Rank</th>
	<th>Club</th>
	<th>Played</th>
	<th>Won</th>
	<th>Drawn</th>
	<th>Lost</th>
</tr>
EOT;
$membs = $team->list_members();
foreach ($membs as $m) {
	$m->fetchdets();
	$m->fetchclub();
	print <<<EOT
<tr>
	<td>{$m->display_name()}</td>
	<td>{$m->display_rank()}</td>
	<td>{$m->Club->display_name()}</td>
	<td>{$m->played_games()}</td>
	<td>{$m->won_games()}</td>
	<td>{$m->drawn_games()}</td>
	<td>{$m->lost_games()}</td>
</tr>
EOT;
}
?>
</table>
<?php
$team->get_scores();
if ($team->Played != 0)  {
	print <<<EOT
<h2>Match Record</h2>
<p>
Match record is Played: {$team->Played} Won: {$team->Won}
Drawn: {$team->Drawn} Lost: {$team->Lost}.
</p>
<img src="php/piewdl.php?w={$team->Won}&d={$team->Drawn}&l={$team->Lost}">
<br />
<table class="teamdisp">
<tr>
	<th>Date</th>
	<th>Opponent</th>
	<th>Result</th>
</tr>

EOT;
	$ret = mysql_query("select ind from lgmatch where {$team->queryof('hteam')} or {$team->queryof('ateam')} order by matchdate");
	if ($ret)  {
		while ($row = mysql_fetch_array($ret))  {
			$mtch = new Match($row[0]);
			$mtch->fetchdets();
			$oppteam = $mtch->Hteam;
			if ($oppteam->is_same($team))  {
				$oppteam = $mtch->Ateam;
				switch ($mtch->Result) {
				case 'H':	$res = 'Won';	break;
				case 'D':	$res = 'Drawn'; break;
				case 'A':	$res = 'Lost';	break;
				}
			}
			else
				switch ($mtch->Result) {
				case 'H':	$res = 'Lost';	break;
				case 'D':	$res = 'Drawn'; break;
				case 'A':	$res = 'Won';	break;
				}
			print <<<EOT
<tr>
	<td>{$mtch->Date->display_month()}</td>
	<td><a href="teamdisp.php?{$oppteam->urlof()}" class="nound">{$oppteam->display_name()}</a></td>
	<td><a href="showmtch.php?{$mtch->urlof()}" class="nound">$res</a></td>
</tr>

EOT;
		}
	}
	print "</table>\n";
}
if ($team->Scoref + $team->Scorea != 0)  {
	print <<<EOT
<h2>Game Record</h2>
<p>
Game record is For: {$team->Scoref} Against: {$team->Scorea}. (Drawn games are 0.5 each).
</p>
<img src="php/piewdl.php?w={$team->Scoref}&d=0&l={$team->Scorea}">
<br />

EOT;
}
?>
</body>
</html>
