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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "BGA Online Leagues";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$hasfoot = true;
include 'php/nav.php'; ?>
<h1>BGA Online Leagues</h1>
<img src="images/gogod_shield.medium.jpg" width="359" height="400" alt="Shield picture" align="left" border="0" hspace="20" vspace="5">
<p>Congratulations to the <b>Central London Go Club A</b> team (Nick Krempel, Fanciso Divers, Chuck Fisher
and Michael Webster)
on winning the third season championship of the online team league and
taking the GoGoD shield from the Dundee team.</p>
<p>Congratulations also to Andrew Simons on winning the championship in first
full season of the Individual online league, taking the <i>Xinyi Lu</i> prize.</p>
<p>The BGA League Tournaments are a pair of leagues with games
played over the Internet: a team league and an individual league.
The leagues run throughout the calendar year.</p>
<p>The purpose of the league is to encourage interaction between players
in different areas of the country and to
encourage online play amongst members of the BGA.</p>
<h2>League format</h2>
<p>
Both the team and individual leagues are played in annual seasons
starting in January and ending in December (with the last two months,
November and December, being reserved for "tie-break" matches).
</p>
<p>In the team league, teams comprise three or more players, with three players participating in any match.
The teams are assigned to league divisions, with promotion of the top team and relegation
of the bottom team after each season.
Each season, teams play a round robin tournament within their league.</p>
<p>New entrant teams are assigned to a division containing players of similar strength at the organisers'
discretion.</p>
<p>In the individual league, players are assigned to a division of roughly the same standard of players
and play each other on a "self-pairing" basis. Players may play each other as often as they like
but are encouraged to play as many different opponents as they can within their division.
The top player in each division is promoted and the bottom player relegated at the end of the season.</p>
<p>New entrant players are placed in a division with the most players of a similar rank to theirs.</p>
<p>Games are played online, usually on the <a href="http://www.gokgs.com" target="_blank">KGS Go Server</a>
in the "British Room".</p>
<p>Most games are recorded and from time to time teachers and experts may provide reviews
of games to assist player development.</p>
<h2>Prizes</h2>
<p>The champions of the top division in the team league will win the League Tournament Shield,
shown above and in addition for the first 5 years will receive three copies of the GoGoD Database
and Encyclopaedia.</p>
<p>The winner of each division in the individual league will win the <i>Xinyi Lu Prize</i>, named after
Xinyi Lu, who whilst leading (and ultimately still winning) the division 2 in the first full season
of the Individual Leauge, was tragically killed whilst on holiday in China in October 2011.
The Xinyi Lu prize consists of credits at
<a href="http://internetgoschool.com">Guo Juan's internet Go school</a>.</p>
<p>The BGA Council wishes to express its gratitude to Games of Go on Disk
for their generous donation of the online league shield and copies of the GoGoD CD.</p>
<h2>Rules</h2>
<p>A full description of playing games is to be found
under <a href="playing.php" title="Read description of rules and instructions for playing">rules</a>.</p>
<h2>Joining</h2>
<p>Please join either league before 23rd January 2012 if you want to play in the
next season, which will start properly on 1st February 2012.</p>
<h3>Team League</h3>
<p>If you would like to form a team, please contact
the Online League coordinators at online-league at britgo.org.
Players wishing to captain a team must be members of the British Go Association,
which can be joined <a href="http://www.britgo.org/join" target="_blank" title="Join the BGA">here</a>.
Non-members can join teams or play in the individual league at an extra cost. (Please note, however,
that from 2013 only BGA members may play in the individual league).
As the league is intended to mainly link to clubs,
please try to form teams from locally-based players.
</p>
<h3>Individual League</h3>
<p>You need to get an account using <a href="newacct.php" title="Create a new account">this page</a>
if you don't have one.
You should have the same account if you play in both leagues and set your account as being active
in the individual league.
You will be given a user id and a password. You can set the password to your own choice of characters later.
Please set the contact details, including your email address
(which will never be given out indiscriminately) and KGS name, on your account.</p>
<h2>Subscriptions</h2>
<p>We charge &pound;15 per season for each team in the team league, together with
an extra &pound;5 each non-BGA member in each team.</p>
<p>We charge &pound;10 per season for each player in the individual league, or &pound;15
for each non-BGA member during 2012 (after which all individual league players must be BGA members).</p>
<p>You can see the current team league status using the menu items to the left.
For any further questions, please contact
the Online League coordinators at online-league at britgo.org.</p>
</div>
</div>
<div id="Footer">
<div class="innertube">
<hr>
<p class="note">
This website was designed, authored and programmed by
<a href="http://www.john.collins.name" target="_blank">John Collins</a>.
</p>
<?php
$dat = date("Y");
print <<<EOT
<p class="note">Copyright &copy; John Collins 2009-$dat. Licensed under

EOT;
?>
<a href="http://www.gnu.org/licenses/">GPL v3</a>.</p>
</div>
</div>
</body>
</html>
