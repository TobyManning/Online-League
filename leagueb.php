<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
?>
<html>
<?php
$Title = "League Standings";
include 'php/head.php';
?>
<body>
<h1>Current League Standings</h1>
<table class="league">
<tr>
<th>Team</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>For</th>
<th>Against</th>
</tr>
<?php
$ml = max_division();
for ($d = 1; $d < $ml; $d++) {
	print <<<EOT
<tr>
<th colspan="7" align="center">Division $d</th>
</tr>
EOT;
	$tl = list_teams($d);
	foreach ($tl as $t) {
		$t->get_scores();
	}
	usort($tl, 'score_compare');
	foreach ($tl as $t) {
		print <<<EOT
<tr>
<td>{$t->display_name()}</td>
<td>{$t->Played}</td>
<td>{$t->Won}</td>
<td>{$t->Drawn}</td>
<td>{$t->Lost}</td>
<td>{$t->Scoref}</td>
<td>{$t->Scorea}</td>
</tr>
EOT;
	}
}
?>
</table>
</body>
</html>
