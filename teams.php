<?php
//   Copyright 2011 John Collins

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
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Teams";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Teams</h1>
<table class="teamsb">
<tr>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
	<th>Members</th>
	<th>Email</th>
<?php

// Function to print team details

function printteam($team) {
	// Set these global seems easier
	global $admin, $logged_in;
	print <<<EOT
<tr>
	<td><a href="teamdisp.php?{$team->urlof()}">{$team->display_name()}</a></td>
	<td>{$team->display_description()}</td>
	<td>{$team->display_captain()}</td>
	<td>{$team->count_members()}</td>
	<td>{$team->display_capt_email($logged_in)}</td>

EOT;
	if ($admin) {
		$pd = $team->Paid? 'Yes': 'No';
		print "<td>$pd</td>\n";
	}
	print "</tr>\n";
}

if ($admin)
	print "<th>Paid</th>\n";
print "</tr>\n";
$teamlist = list_teams(0, "divnum,name");
$lastdiv = -199;
foreach ($teamlist as $team) {
	$team->fetchdets();
	if ($team->Division != $lastdiv) {
		$lastdiv = $team->Division;
		print <<<EOT
<tr><th colspan="4" align="center">Division {$team->display_division()}</th></tr>

EOT;
	}
	printteam($team);
}
$teamlist = list_teams(0, "name", 0);
if (count($teamlist) != 0) {
		print <<<EOT
<tr><th colspan="4" align="center">Not in a division</th></tr>

EOT;
	foreach ($teamlist as $team) {
		$team->fetchdets();
		printteam($team);
	}
}
?>
</table>
</div>
</div>
</body>
</html>
