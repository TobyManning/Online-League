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
include 'php/matchdate.php';
include 'php/params.php';
?>
<html>
<?php
$Title = "Individual League Standings";
include 'php/head.php';
?>
<body class="il">
<h1>Current Individual League Standings</h1>
<div align="center">
<table class="league">
<tr>
<th>Player</th>
<th>Rank</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
</tr>
<?php
$pars = new Params();
$pars->fetchvalues();
$ml = max_ildivision();
for ($d = 1; $d <= $ml; $d++) {
	print <<<EOT
<tr>
<th colspan="6" align="center">Division $d</th>
</tr>
EOT;
	$pl = list_players_ildiv($d);
	foreach ($pl as $p) {
		$p->fetchdets();
		print <<<EOT
<tr>
<td>{$p->display_name()}</td>
<td>{$p->display_rank()}</td>
<td align="right">{$p->played_games(true,'I')}</td>
<td align="right">{$p->won_games(true,'I')}</td>
<td align="right">{$p->drawn_games(true,'I')}</td>
<td align="right">{$p->lost_games(true,'I')}</td>
</tr>
EOT;
	}
}
?>
</table>
</div>
<p>Key to above: Matches <b>P</b>layed, <b>W</b>on, <b>D</b>rawn, <b>L</b>ost.
<span class="prom">Promotion Zone</span> and <span class="releg">Relegation Zone</span>.
</p>
<!-- <h2>Previous Seasons</h2>
<p><a href="prevleagueb.php">Click here</a> to view previous seasons' league.</p> -->
</body>
</html>
