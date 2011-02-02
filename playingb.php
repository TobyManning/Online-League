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
	else  {
		$rkomi = ($diff - 9) * 10;
		print "a handicap of 9 plus $rkomi reverse komi is used.";
	}
}

$md = max_division();
$pars = new Params();
$pars->fetchvalues();

$hdiv = $pars->Hdiv;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Playing games";
include 'php/head.php';
?>
<body>
<h1>Playing games on the league</h1>
<p>We'd like to clarify how games should be played on the league to avoid confusion.
(Please note that this is the current version,
modified in practice and takes priority over the original
specification at
<a href="http://league.britgo.org/doc/LeagueDescription.pdf" target="_blank">this document</a>).
</p>
<p>Differences for the individual league are noted as they arise.</p>
<p><b>If something goes wrong</b> please see <a href="#wrong">here</a>.</p>
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
		print "<h2>Team League Division";
		if ($luh == 1)
			 print "1</h2>\n";
		elseif ($luh == 2)
			print "s 1 and 2</h2>\n";
		else
			print "s 1 to $luh</h2>\n";
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
<h2>Team League Divisions $hdiv and below</h2>
<p>These divisions may have handicap games where the player strengths are sufficiently
different, otherwise they should be even as described above.
Please try to stick to the colours as they are assigned WBW or BWB to boards 1 to 3
regardless of strengths (wherever this is possible). One player
may have to click the button next to his name to get the colours right.</p>
</p>
<p>Games in the Individual League are always played without handicaps.</p>
EOT;
}
else {
	print <<<EOT
<p>All divisions in the team league may have handicap games
where the player strengths are sufficiently
different, otherwise they should be even as described above.
Please try to stick to the colours as they are assigned WBW or BWB to boards 1 to 3
regardless of strengths (wherever this is possible). One player
may have to click the button next to his name to get the colours right.</p>

<p>All games in the Individual League are played without handicaps, however.</p>
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
<h3>Handicaps beyond 9</h3>
<p>If the handicap "would be" more than 9, then the white player gives 9 stones plus 10 points
of reverse komi for each stone beyond 9.</p>
<p>So for example if a 1K player plays a 20K player then
<?php
phcp($hred, 19);
?>
</p>
<h2>The Individual League</h2>
<p>Games in the individual league are always played without handicaps regardless of the players'
strengths. Colours should be accepted according to whatever the server proposes and the game
and result entered later.
</p>
<p>Games are "self-pairing". To start a game in the Individual League, post a game in the
KGS British Room with the rules and time controls as given above with the title
<b>BGA Individual League Division X</b>, where X is your division. You should accept
any challenge from someone in your division, but preferring people you have played less
often if a choice arises. Try not to play the same person more than once a week and
certainly not more than once a day.</p>
<p>One of the players, usually the winner, should enter the details of the game and
the result as soon as possible afterwards.</p>
<h2>General points</h2>
<p>We suggest a "Free Game" as the KGS handicaps are ignored and in some cases wildly different
strength players meet with non-standard handicaps
and we don't think this should confuse KGS rankings.</p>
<p>If you suddenly realise during the game you've got the game settings wrong you can either
opt to continue or discount the game and replay it later, unless the colours are wrong
(this only applies to the team league) when it should always be replayed.
</p>
<p>If there is an unavoidable interruption such as one player is disconnected or KGS crashes,
please try to resume it as soon as possible
or agree the result by one side resigning possibly in consultation
with an agreed adjudicator.</p>
<p>If it is impossible to resume the game and it appears very close, record the game as
a draw (Jigo). (This is why a column has been provided for draws in the tables -
and potentially a team match could be drawn also).</p>
<a name="wrong"></a>
<h1>Something wrong?</h1>
<p>If something goes wrong, please don't flounder.
Please don't enter any results that may be trouble to unravel.
Here's what to do.
</p>
<h2>Disputes about playing games</h2>
<p>A dispute may arise about people not turning up for games or some irregularity during a
game. Perhaps the komi was wrong and the game was close enough for this to matter. Perhaps the
rules were wrong.
</p>
<p>If this happens please contact an admin person or BGA council member as soon as possible as
referee. The person chosen should not have any direct involvement with the match in question
and should refuse to act as referee if he/she believes that there is any such involvement.
</p>
<p>The person may decide:
</p>
<ul>
<li>To award the game to one side or the other.</li>
<li>To declare the game to be a draw (Jigo). Note that this may cause the match itself
to be drawn.</li>
<li>To declare the game void and order its replay. The replay should be with the same
colours, handicap or komi and time controls as the original game should have been.</li>
</ul>
<p>In the last case, the referee may supplement his/her decision
with a rider that a replay be made within a certain time or one of the other
outcomes shall apply.
</p>
<p>If you think that the decision of the referee was wrong, or that the referee had
an undeclared interest in the result, then you should appeal to the BGA council for
a final decision.</p>
<p>Games decided in this fashion are entered as <b>W+N</b> or <b>B+N</b>
or <b>Jigo</b> as appropriate and no SGF file should be included.</p>
<h2>Errors entering results</h2>
<p>If you realise that the game has been played with the wrong colours or other settings
incorrect <u>do not enter the result</u>.
Doing so may create a "news item" on the main BGA website
or RSS feed which will be wrong.</p>
<p><b>If at all possible</b>, please replay the game with the correct settings.</p>
<p>If not possible, please contact a referee as above.</p>
<p>Whatever you do, please don't change any of the settings of the other games in the match
to try to "compensate".</p>
</body>
</html>
