<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
//   Copyright 2010 John Collins

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
include 'php/match.php';
include 'php/matchdate.php';

$div = $_POST['div'];
if (strlen($div) == 0)  {
	include 'php/wrongentry.php';
	exit(0);
}
try {
	$teama = new Team();
	$teamb = new Team();
	$teama->frompost("a");
	$teamb->frompost("b");
	$teama->fetchdets();
	$teamb->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$dat = new Matchdate();
$dat->frompost();

try {
	$mtch = new Match(0, $div);
	$mtch->Hteam = $teama;
	$mtch->Ateam = $teamb;
	$mtch->Date = $dat;
	$mtch->create();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Match created OK";
include 'php/head.php';
?>
<body>
<h1>Match created OK</h1>
<p>
Finished creating match between:
<?php
print <<<EOT
{$teama->display_name()} and {$teamb->display_name()}
for round about
{$dat->display()}.

EOT;
?>
</p>
<p>Click <a href="matchupdb.php">here</a> to edit other matches.</p>
</body>
</html>
