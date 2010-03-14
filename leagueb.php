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
include 'php/match.php';
include 'php/matchdate.php';
include 'php/params.php';
?>
<html>
<?php
$Title = "League Standings";
include 'php/head.php';
?>
<body>
<h1>Current League Standings</h1>
<div align="center">
<table class="league">
<tr>
<th>Team</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>F</th>
<th>A</th>
</tr>
<?php
$pars = new Params();
$pars->fetchvalues();
$ml = max_division();
for ($d = 1; $d <= $ml; $d++) {
	print <<<EOT
<tr>
<th colspan="7" align="center">Division $d</th>
</tr>
EOT;
	$tl = list_teams($d);
	foreach ($tl as $t) {
		$t->get_scores($pars);
	}
	usort($tl, 'score_compare');
	$maxrank = $tl[0]->Sortrank;
	$minrank = $tl[count($tl)-1]->Sortrank;
	// This avoids showing prom/releg if they're all the same as with nothing played.
	if ($maxrank == $minrank)
		$maxrank = $minrank = -9999999;
	foreach ($tl as $t) {
		$n = "<a href=\"teamdisp.php?{$t->urlof()}\" class=\"nound\">{$t->display_name()}</a>";
		if ($t->Sortrank == $maxrank)
			$n = "<span class=\"prom\">$n</span>";
		elseif ($t->Sortrank == $minrank)
			$n = "<span class=\"releg\">$n</span>";
		print <<<EOT
<tr>
<td>$n</td>
<td align="right">{$t->Played}</td>
<td align="right">{$t->Won}</td>
<td align="right">{$t->Drawn}</td>
<td align="right">{$t->Lost}</td>
<td align="right">{$t->Scoref}</td>
<td align="right">{$t->Scorea}</td>
</tr>

EOT;
	}
}
?>
</table>
</div>
<p>Key to above: Matches <b>P</b>layed, <b>W</b>on, <b>D</b>rawn, <b>L</b>ost, Games <b>F</b>or and Games <b>A</b>gainst.
<span class="prom">Promotion Zone</span> and <span class="releg">Relegation Zone</span>.
</p>
<h2>Previous Seasons</h2>
<p><a href="prevleagueb.php">Click here</a> to view previous seasons' league.</p>
</body>
</html>
