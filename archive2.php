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
include 'php/teammemb.php';
include 'php/match.php';
include 'php/game.php';
include 'php/matchdate.php';
include 'php/params.php';
include 'php/season.php';
include 'php/histteam.php';
include 'php/histteammemb.php';
include 'php/histmatch.php';

// Regurgitate what we did before in case we've been entered wrongly.

$pars = new Params();
$pars->fetchvalues();

// Check that we are ready to archive

$Sname = $_POST["seasname"];
if (strlen($Sname) == 0)  {
	include 'php/wrongentry.php';
	exit(0);
}

include 'php/promoreleg.php';

if (count($messages) > 0)  {
	$mess = "Not ready to do end-of-season";
	include 'php/wrongentry.php';
	exit(0);
}

// Create the Season
// Set the name and dates

$Seas = new Season();
$Seas->Name = $Sname;
$earliest = new Matchdate();
$latest = new Matchdate();
$ret = mysql_query("select matchdate from lgmatch order by matchdate limit 1");
if ($ret && mysql_num_rows($ret) > 0)  {
	$row = mysql_fetch_array($ret);
	if ($row)
		$earliest->enctime($row[0]);	
}
$ret = mysql_query("select matchdate from lgmatch order by matchdate desc limit 1");
if ($ret && mysql_num_rows($ret) > 0)  {
	$row = mysql_fetch_array($ret);
	if ($row)
		$latest->enctime($row[0]);	
}
$Seas->Startdate = $earliest;
$Seas->Enddate = $latest;
$Seas->create();

//  Create the historic teams from the current ones

$Full_teams = list_teams();

foreach ($Full_teams as $team) {
	$team->fetchdets();
	$hteam = new Histteam($Seas);
	$hteam->Name = $team->Name;
	$hteam->Description = $team->Description;
	$hteam->Division = $team->Division;
	$hteam->create();
	// Create the members
	$membs = $team->list_members();
	foreach ($membs as $memb)  {
		$hmemb = new HistteamMemb($team, $memb->First, $memb->Last);
		$hmemb->create();
	}
}

//  Now apply promotions and relegations

for ($d = 1; $d < $ml; $d++)  {
	$nd = $d + 1;
	if ($_POST["pd$d"])  {
		array_push($messages,
		"Promoted {$promo[$nd]->display_name()} from division $nd and relegated {$releg[$d]->display_name()} from division $d");
		// $promo[$nd]->updatediv($d);
		// $releg[$d]->updatediv($nd);
	}
}

//  Create the historic matches from the current ones
//  Also need to set referenced games to non-current.
//  Delete unplayed games

$ret = mysql_query("select ind from lgmatch");
if (!$ret)  {
	$mess = mysql_error();
	include 'php/dataerror.php';
	exit(0);
}

try {
	while ($row = mysql_fetch_array($ret))  {
		$matchind = $row[0];
		$mtch = new Match($matchind);
		$mtch->fetchdets();
//  Set games as non-current (TURNED OFF FOR DEBUG)
//		$mtch->fetchgames();
//		foreach ($mtch->Games as $g) {
//			$g->set_current(false);
//		}
		// Now set up the hist match unless it's not been played at all
		if ($mtch->Result != 'N')  {
			$hmtch = new HistMatch($Seas, $mtch->query_ind(), $mtch->Division);
			$hmtch->Hteam = $mtch->Hteam;
			$hmtch->Ateam = $mtch->Ateam;
			$hmtch->Date = $mtch->Date;
			$hmtch->Hscore = $mtch->Hscore;
			$hmtch->Ascore = $mtch->Ascore;
			$hmtch->Result = $mtch->Result;
			$hmtch->create();
		}
	}
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);
}
catch (HistMatchException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);
}

//  Now delete all unplayed games (TURNED OFF FOR DEBUG)
//  Delete all matches (TURNED OFF FOR DEBUG)

// mysql_query("delete from game where result='N'");
// mysql_query("delete from lgmatch");

// I think that just about does it.

?>
<html>
<?php
$Title = "End of season complete";
include 'php/head.php';
?>
<body>
<h1>End of season complete</h1>
<?php
$Sname = htmlspecialchars($Sname);
print <<<EOT
<p>Cleared and archived the season as $Sname.</p>

EOT;
if (count($messages) > 0)  {
	print <<<EOT
<p>
Performed the following promotions and relegations:
</p>

EOT;
	foreach ($messages as $mess)  {
		print "<p>$mess</p>\n";
	}
}
else {
	print <<<EOT
<p>No promotions or relegations were done.</p>

EOT;
}
?>
</body>
</html>
