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
$Title = "British Go Association Online Leagues";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$hasfoot = true;
include 'php/nav.php'; ?>
<h1>British Go Association League</h1>
<img src="images/gogod_shield.medium.jpg" width="359" height="400" alt="Shield picture" align="left" border="0" hspace="20" vspace="5">
<p><strong>Welcome to the British Go Association League Website.</strong></p>
<p>Congratulations to the <b>Dundee</b> team (Yohei Negi, David Lee and Robby Geotschalckx)
on winning the second season championship
taking the GoGoD Shield and the title from the previous winners - Cambridge.
</p>
<p>The British Go Association League Tournament is a team-based
Go league designed to be played over the Internet.
Following the success of this in 2010, an individual league is starting in 2011.
The leagues run throughout the calendar year.</p>
<p>The purpose of the league is to encourage interaction between players
in different areas of the country and to
encourage online play amongst members of the BGA.</p>
<h2>League format</h2>
<p>
The league is played in annual seasons for both the team and individual leagues,
starting in January and ending in December (although the last two months, November and
December, are reserved for "tie-break" matches).
</p>
<p>In the team league, teams of up to six players may enter the
tournament, and are assigned to league divisions of six or seven teams.
Each season, teams play a round robin tournament within their league.
</p>
<p>In the individual league, players are assigned to a division of roughly the same
standard of players and play each other on a "self-pairing" basis. Players may play each
other as often as they like but are encouraged to play as many different opponents as
they can within their division.
</p>
Games are played online on the <a href="http://www.gokgs.com" target="_blank">KGS Go Server</a>
in the "British Room".
</p>
<h2>Prizes</h2>
<p>
The winners in each league division are promoted at the end of the season,
whilst losers face relegation to the league below. There may be more promotions or
relegations in the individual league, depending upon numbers.
The champions of the top division in the team league will win the League Tournament Shield,
shown above and in addition for the first 5 years
will receive three copies of the
GoGoD Database and Encyclopaedia.</p>
<p>The champions of the individual league will win credits
at <a href="http://internetgoschool.com">Guo Juan's internet Go school</a>.
</p>
<p>The BGA Council wishes to express its gratitude to Games of Go on Disk
for their generous donation of the online league shield and copies of the GoGoD CD.</p>
<h2>Rules</h2>
<p>A full description of playing games is to be found
under <a href="playing.php">rules</a>.</p>
<h2>Joining</h2>
<p>Please join either league before 24th January 2011 if you want to play in the
current seasons, which start properly on 1st February 2011.</p>
<p>If you would like to form a team, please contact
the Online League coordinators at online-league at britgo.org.
Players wishing to captain a team must be members of the British Go Association,
which can be joined <a href="http://www.britgo.org/join" target="_blank">here</a>.
Non-members can join teams or play in the individual league at an extra cost.
As the league is intended to mainly link to clubs,
please try to form teams from locally-based players.
</p>
<p>If you would like to play in the individual league, first get an account
if you don't have
one. You should have the same account if you play in both leagues and set your
account as being active in the individual league. You can get an account online
using <a href="newacct.php">this page</a>.
You will be given a user id and a password.
You can set the password to your own choice of characters later. Please set the contact
details, including your email address
(which will never be given out indiscriminately), KGS name, on your account.
</p>
<h2>Subscriptions</h2>
<p>We charge &pound;10 per season for each team in the team league, together with
an extra &pound;5 each non-BGA member in each team.</p>
<p>We charge &pound;5 per season for each player in the individual league, or &pound;8
for each non-BGA member.</p>
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