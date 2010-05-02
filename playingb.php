<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
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
include 'php/params.php';
include 'php/team.php';

function phcp($hred, $diff) {
	$diff -= $hred;
	if ($diff <= 0)
		print "the game is an even game.";
	elseif ($diff == 1)
		print "the game is played with no komi.";
	elseif ($diff <= 9)
		print "a handicap of $diff is used.";
	else
		print "a handicap of 9 is used.";
}

$md = max_division();
$pars = new Params();
$pars->fetchvalues();

$hdiv = $pars->Hdiv;

$Title = "Playing games";
include 'php/head.php';
?>
<body>
<h1>Playing games on the league</h1>
<p>We'd like to clarify how games should be played on the league to avoid confusion.
</p>
<h2>Standard Even Games</h2>
<p>Games should be played with the following parameters on KGS
</p>
<ul>
<li>AGA Rules</li>
<li>No handicap</li>
<li>7.5 Komi</li>
<li>Free Game</li>
<li>Colours assigned <u>as specified in the match allocation</u> not according to strengths
of players</li>
<li>Byo-Yomi time control</li>
<li>Main time 30 minutes</li>
<li>Byo-Yomi time 30 seconds</li>
<li>5 Byo-Yomi periods</li>
<li>Indicate that it's a BGA League Game</li>
</ul>
<p>Here is an example of a correct setting for "Custom Game" which should be in
the British Room:</p>
<div align="center">
<img src="images/gamesetup.png" width="348" height="469" border="0" hspace="10" vspace="20" alt="KGS Go Screen">
</div>
<?php
$luh = $hdiv - 1;

// $luh is "last un-handicapped"

if ($luh > 0)  {
	if ($hdiv <= $md)  {		//  Some have handicaps
		if ($luh == 1)
			print "<h2>Division 1</h2>\n";
		elseif ($luh == 2)
			print "<h2>Divisions 1 and 2</h2>\n";
		else
			print "<h2>Divisions 1 to $luh</h2>\n";
	}
	print <<<EOT
<p>
All games are played even.
Please try to stick to the colours as they are assigned WBW or BWB to boards 1 to 3
regardless of strengths. One player
may have to click the button next to his name to get the colours right.</p>

EOT;
	if ($hdiv <= $md)
		print <<<EOT
<h2>Divisions $hdiv and below</h2>
<p>These divisions may have handicap games where the player strengths are sufficiently
different, otherwise they should be even as described above.
Please try to stick to the colours as they are assigned WBW or BWB to boards 1 to 3
regardless of strengths (wherever this is possible). One player
may have to click the button next to his name to get the colours right.</p>
</p>
EOT;
}
else {
	print <<<EOT
<p>All divisions may have handicap games where the player strengths are sufficiently
different, otherwise they should be even as described above.
Please try to stick to the colours as they are assigned WBW or BWB to boards 1 to 3
regardless of strengths (wherever this is possible). One player
may have to click the button next to his name to get the colours right.</p>

EOT;
}
if ($hdiv <= $md) {
	$hred = $pars->Hreduct;
	$diff = 9 - $hred;
	print <<<EOT
<h2>Playing handicap games</h2>
<p>Handicaps are assigned as the difference in rank minus $hred up to a maximum of 9.
A handicap of 1 means no komi. If the difference in rank is less than $hred, then the game is
played as even with 7.5 komi and possibly with the weaker player as White.</p>
<p>So for example if a 1K player plays a 10K player then

EOT;
	phcp($hred, 9);
	print <<<EOT
</p>
<p>Or if a 2D player plays a 4K player then

EOT;
	phcp($hred, 5);
	print <<<EOT
</p>

EOT;
}
?>
<h2>General points</h2>
<p>We suggest a "Free Game" as the KGS handicaps are ignored and in some cases wildly different
strength players meet with non-standard handicaps
and we don't think this should confuse KGS rankings.</p>
<p>If you suddenly realise during the game you've got the game settings wrong you can either
opt to continue or discount the game and replay it later, unless the colours are wrong when it
should always be replayed.
</p>
<p>If there is an unavoidable interruption such as one player is disconnected or KGS crashes,
please try to resume it or agree the result by one side resigning possibly in consultation
with an agreed adjudicator.</p>
</body>
</html>
