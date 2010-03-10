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

$pars = new Params();
$pars->fetchvalues();

// Check that we are ready to archive

$messages = array();

$ml = max_division();
for ($d = 1; $d <= $ml; $d++) {
	$tl = list_teams($d);
	$nteams = count($tl);
	if ($nteams < 3) {
		array_push($messages, "Not enough teams in division $d");
		continue;
	}
	foreach ($tl as $t) {
		$t->get_scores($pars);
	}
	usort($tl, 'score_compare');
	$maxrank = $tl[0]->Sortrank;
	$minrank = $tl[$nteams-1]->Sortrank;
	// This avoids showing prom/releg if they're all the same as with nothing played.
	if ($maxrank == $minrank)  {
		array_push($messages, "Not enough matches played in division $d");
		continue;
	}
	if ($tl[0]->Sortrank == $tl[1]->Sortrank)
		array_push($messages,
			$d == 1? "Need to have playoff for championship":
			"Need to have playoff for promotion from division $d");
	if  ($tl[$nteams-2]->Sortrank == $tl[$nteams-1]->Sortrank)
		array_push($messages,
			$d == $ml? "Need to have playoff for bottom team":
			"Need to have playoff for relegation from division $d");
	$promo[$d] = $tl[0];
	$releg[$d] = $tl[$nteams-1];
}
?>
<html>
<?php
$Title = "Promotion and Relegation / Archive";
include 'php/head.php';
?>
<body>
<h1>Promotion and Relegation / Archive</h1>
<?php
if (count($messages) > 0)  {
	print <<<EOT
<p>
Sorry but we cannot proceed with the promotion / relegation and archive because of
the following:
</p>

EOT;
	foreach ($messages as $mess)
		print "<p>$mess</p>\n";
}
else  {
	for ($d = 1; $d <= $ml; $d++) {
		$promo[$d]->fetchdets();
		$releg[$d]->fetchdets();
	}
	print "<h2>Champions</h2>\n";
	print "<p><b>{$promo[1]->display_name()} are the league champions!!!</p>\n";
	for ($d = 2; $d <= $ml; $d++) {
		print "<p>{$promo[$d]->display_name()} are champions of division $d</p>\n";
	}
	print <<<EOT
<h2>The Wooden Spoon</h2>
<p>We commiserate with {$releg[$ml]->display_name()} on coming bottom.</p>
<h2>Promotions and relegations</h2>
<p>The following promotions and relegations are proposed. Please uncheck any to be
excluded.
</p>
<form action="archive2.php" method="post" enctype="application/x-www-form-urlencoded">
EOT;
	for ($d = 1; $d < $ml; $d++)  {
		$nd = $d + 1;
		print <<<EOT
<input type="checkbox" name="pd$d" value="yes" checked>
<p>Promote {$promo[$nd]->display_name()} from division $nd and relegate
{$releg[$d]->display_name()} from division $d.</p>
EOT;
	}
	print <<<EOT
<p>
Please <input type="submit" name="submit" value="Click Here"> when ready.
</p>
</form>
EOT;
}
?>
</body>
</html>
