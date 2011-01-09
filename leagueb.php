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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/itrecord.php';
include 'php/params.php';
include 'php/season.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "League Standings";
include 'php/head.php';
?>
<body>
<h1>Current League Standings</h1>
<p>Click <a href="#prev">here</a> to view previous seasons.</p>
<div align="center">
<?php
$pars = new Params();
$pars->fetchvalues();
$ml = max_division();
for ($d = 1; $d <= $ml; $d++) {
	$tl = list_teams($d);
	$cn = 7 + count($tl);
	print <<<EOT
<table class="league">
<tr>
<th colspan="$cn" align="center">Division $d</th>
</tr>
<tr>
<th>Team</th>

EOT;
	
	foreach ($tl as $t) {
		$t->get_scores($pars);
	}
	usort($tl, 'score_compare');
	
	// Insert column header
	
	foreach ($tl as $t)  {
		$hd = substr($t->Name, 0, 3);
		print "<th>$hd</th>\n";
	}
	print <<<EOT
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>F</th>
<th>A</th>
</tr>

EOT;
	
	$maxrank = $tl[0]->Sortrank;
	$minrank = $tl[count($tl)-1]->Sortrank;
	// This avoids showing prom/releg if they're all the same as with nothing played.
	if ($maxrank == $minrank)
		$maxrank = $minrank = -9999999;
	foreach ($tl as $t) {
		$n = $t->display_name();
		if ($t->Sortrank == $maxrank)
			$n = "<span class=\"prom\">$n</span>";
		elseif ($t->Sortrank == $minrank)
			$n = "<span class=\"releg\">$n</span>";
		$n = "<a href=\"teamdisp.php?{$t->urlof()}\" class=\"nound\">$n</a>";
		print <<<EOT
<tr>
<td>$n</td>

EOT;
		foreach ($tl as $ot) {
			$reca = $t->record_against($ot);
			print "<td>{$reca->display()}</td>\n";
		}
		print <<<EOT
<td align="right">{$t->Played}</td>
<td align="right">{$t->Won}</td>
<td align="right">{$t->Drawn}</td>
<td align="right">{$t->Lost}</td>
<td align="right">{$t->Scoref}</td>
<td align="right">{$t->Scorea}</td>
</tr>

EOT;
	}
	print "</table>\n";
	if ($d != $ml)
		print "<br><br><br>\n";
}
?>
</div>
<p>Key to above: Matches <b>P</b>layed, <b>W</b>on, <b>D</b>rawn, <b>L</b>ost, Games <b>F</b>or and Games <b>A</b>gainst.
<span class="prom">Promotion Zone</span> and <span class="releg">Relegation Zone</span>.
</p>
<h2>Previous Seasons</h2>
<a name="prev"></a>
<?php
$seasons = list_seasons();
if (count($seasons) == 0) {
	print <<<EOT
<p>There are currently no past seasons to display.
Please come back soon!
</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back.
</p>

EOT;
}
else {
	print <<<EOT
<table class="teamsb">
<tr>
	<th>Season Name</th>
	<th>Start Date</th>
	<th>End Date</th>
	<th>League table</th>
	<th>Matches</th>
</tr>

EOT;
	foreach ($seasons as $seas) {
		$seas->fetchdets();
		print <<<EOT
<tr>
	<td>{$seas->display_name()}</td>
	<td>{$seas->display_start()}</td>
	<td>{$seas->display_end()}</td>
	<td><a href="seasleague.php?{$seas->urlof()}">Click</a></td>
	<td><a href="seasmatches.php?{$seas->urlof()}">Click</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
?>
</body>
</html>
