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
?>
<html>
<?php
$Title = "Teams";
include 'php/head.php';
?>
<body>
<h1>Teams</h1>
<table class="teamsb">
<tr>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
	<th>Members</th>
	<th>Email</th>
<?php
if ($userpriv == 'A' || $userpriv == 'SA')
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
	print <<<EOT
<tr>
	<td><a href="teamdisp.php?{$team->urlof()}">{$team->display_name()}</a></td>
	<td>{$team->display_description()}</td>
	<td>{$team->display_captain()}</td>
	<td>{$team->count_members()}</td>
	<td>{$team->display_capt_email($logged_in)}</td>
EOT;
	if ($userpriv == 'A' || $userpriv == 'SA') {
		$pd = $team->Paid? 'Yes': 'No';
		print "<td>$pd</td>\n";
	}
	print "</tr>\n";
}
?>
</table>
</body>
</html>
