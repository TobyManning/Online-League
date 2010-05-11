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

$div = $_GET['div'];

if (strlen($div) == 0)  {
	include 'php/wrongentry.php';
	exit(0);
}

?>
<html>
<?php
$Title = "Create a match - division $div";
include 'php/head.php';
?>
<body>
<script language="javascript">
function okmatch() {
	var fm = document.mform;
	var tas = fm.teama.selectedIndex;
	var tbs = fm.teamb.selectedIndex;
	if (tas < 0) {
		alert("No team selected for team A");
		return false;
	}
	if (tbs < 0) {
		alert("No team selected for team B");
		return false;
	}
	if (tas == tbs) {
		alert("Both selected teams are the same");
		return false;
	}
	return true;
}
</script>
<?php
$dat = new matchdate();
$teams = list_teams($div);

function teamselect($teams, $sname) {
	print <<<EOT
<select name="$sname" size="0">

EOT;
	foreach ($teams as $t) {
	print <<<EOT
<option value="$t->Name">{$t->display_name()}</option>

EOT;
	}
	print "</select>\n";
}

print <<<EOT
<h1>Create a match - division $div</h1>
<p>Use the following form to create an <i>ad hoc</i> match betewen two teams in
division $div, e.g. for tie-breaks. Use the draw page to generate a random
draw between all the teams.
</p>
<form name="mform" action="matchcreate2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return okmatch();">
<input type="hidden" name="div" value="$div">
<p>

EOT;

$dat->dateopt("Date for play");
print <<<EOT
</p>
<p>Team A:
EOT;
teamselect($teams, "teama");
print <<<EOT
</p>
<p>Team B:
EOT;
teamselect($teams, "teamb");
?>
</p>
<p>Click <input type="submit" name="sub" value="here"> when ready.</p>
</form>
</body>
</html>
