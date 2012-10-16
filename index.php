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
$Title = "BGA Online League";
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
<p>Congratulations also to Andrew Simons on winning the championship in first (and last)
full season of the Individual online league, taking the <i>Xinyi Lu</i> prize.</p>
<p>The BGA League Tournament is a team tournament, with teams mostly taken from local Go clubs.
The league runs throughout the calendar year.</p>
<p>The purpose of the league is to encourage interaction between players
in different areas of the country and to
encourage online play amongst members of the BGA.</p>
<h2>League format</h2>
<p>
The team league is played in annual seasons
starting in March and ending in December (with the last two months,
November and December, being primarily reserved for "tie-break" matches).
</p>
<p>Teams comprise three or more players, with three players participating in any match.
The teams are assigned to league divisions, with promotion of the top team and relegation
of the bottom team after each season.
Each season, teams play a round robin tournament within their league.</p>
<p>New entrant teams are assigned to a division containing players of similar strength at the organisers'
discretion.</p>
<p>Games are played online, usually on the <a href="http://www.gokgs.com" target="_blank">KGS Go Server</a>
in the "British Room".</p>
<p>Most games are recorded and from time to time teachers and experts may provide reviews
of games to assist player development.</p>
<h2>Prizes</h2>
<p>The champions of the top division in the league will win the League Tournament Shield,
shown above and in addition for the first 5 years will receive three copies of the GoGoD Database
and Encyclopaedia.</p>
<p>Originally there was an Individual League, but this was discontinued in October 2012.
We named the prizes for that the <i>Xinyi Lu Prize</i>, after
Xinyi Lu, who whilst leading (and ultimately still winning) the division 2 in the first full season
of the Individual Leauge, was tragically killed whilst on holiday in China in October 2011.
</p>
<p>The BGA Council wishes to express its gratitude to Games of Go on Disk
for their generous donation of the online league shield and copies of the GoGoD CD.</p>
<h2>Rules</h2>
<p>A full description of playing games is to be found
under <a href="playing.php" title="Read description of rules and instructions for playing">rules</a>.</p>
<h2>Joining</h2>
<p>It is now too late to join the 2012 team league, which will get under way on 1st April 2012 (probably after
mid-day!). However if you would like to join the 2013 team league, please let us know.</p>
<h3>Joining the League</h3>
<p>If you would like to form a team, please contact
the Online League coordinators at online-league at britgo.org.
Players wishing to captain a team must be members of the British Go Association,
which can be joined <a href="http://www.britgo.org/join" target="_blank" title="Join the BGA">here</a>.
As the league is intended to mainly link to clubs,
please try to form teams from locally-based players, but this is not essential and many teams
include members from other locations.
</p>
<h2>Subscriptions</h2>
<p>We charge &pound;15 per season for each team in the team league, together with
an extra &pound;5 each non-BGA member in each team.</p>
<p>You can see the current league standings using the menu items to the left, together with historical records.
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
