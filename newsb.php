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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/matchdate.php';
include 'php/news.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "News items";
include 'php/head.php';
?>
<body>
<h1>Important announcement regarding the leagues</h1>
<p>Following feedback from various people and a recent discussion at Council,
it has been decided to make the following changes to the League schedule and other
provisions as follows:</p>
<h2>Current league season (Season 2)</h2>
<p>The current league season will be extended until the end of the year and Season 3
will commence at the beginning of January.</p>
<p>Please can all team captains and members aim to complete their games by the
end of November at the latest.
December should be reserved for "play-off" matches
where teams tie for promotion or relegation.
At the organiser's discretion, we may ask teams which are very
close to each other, such as where the championship
and/or promotion or relegation battles
are separated only by one or two "games for/games against",
to play an additional match against each other.
Teams which are in this position at the end of November
should make an appropriate request no later than 1st December.</p>
<h2>Next league season (Season 3)</h2>
<p>The next league season will commence on 1st January 2011.</p>
<p>The matches will be spread out over the year so as to end in October,
with "play-offs" in November and December.
Match dates will be nominally assigned at evenly-spaced dates
but play will not be expected to take place during July and August
(although some matches may be nominally assigned to these months).</p>
<h2>Organising and arranging matches</h2>
<p>We desperately need more phone numbers and email addresses to be provided for the
players' list to aid the arrangement of matches.
We do stress that we will only use these for this purpose
and they will not be available to someone who hasn't got an account and password
on the system. Email addresses will in particular not be abused.</p>
<p>We do have a marker against email addresses to permit the system
to send out reminders; please set this on if you possibly can,
you will only get a message when the match is first arranged and
weekly thereafter (usually on a Wednesday) until the match is played. If you do not set
this your team captain will receive the reminder instead!</p>
<p>Against the phone number is a "latest time to call" field for where this is required.</p>
<p>There is also a "notes" field - please put relevant information
such as "Young player - contact details are those of parents" where appropriate.</p>
<p>Please also ensure that every player has their own KGS account which is recorded
and that account (if the user has more than one) is the only one used for league games.
If the user does have more than one KGS account,
it should be the one with the highest rating.</p>
<p>The draw for each match is random, but aimed so that each team plays each other
team once during the season.</p>
<p>The first named team in each match, however, will be for the purposes
of arranging matches considered to be the "home" team and the team captain of that
team should consider him/herself primarily responsible for organising
the matches and making sure the games are played.</p>
<p>Note that an extra menu entry has been added - "Outstanding".
This lists the games the relevant player has outstanding to play,
and for team captains the matches to complete team allocations for
and to chase completion of.</p>
<p>If there are any suggestions about arranging matches which could ease
setting them up, please let us know.</p>
<h2>Payment of subscriptions</h2>
<p>We hope to have a Paypal account set up to automatically
take subscriptions on behalf of teams (and the individual league). </p>
<h1>The Individual League</h1>
<p>We also intend to launch the Individual League fully starting
on 1st January 2011 and running throughout 2011.
Each season of the Individual League will run over the calendar year.</p>
<p>If you want to join the individual league,
please just log in to your account,
make sure that your ranking is correct and then select the box to join the league.
You will initially be assigned to one of 3 divisions according to your ranking. </p>
<h2>Playing games in the individual league</h2>
<p>Playing games in the individual league should be entirely "self-pairing".
You can play as many or as few games as you wish.
However more credit is given in the rankings to "valiant losers"
who turn out to play than those who do not play at all.</p>
<p>To play a game, put up a game on the KGS British Room
with the standard parameters (AGA rules 7.5 komi no handicap, 30 minutes main time
5x30 secs byo-yomi) with the title <b>BGA Individual League Division X</b>,
where X is your division. Anyone else in your division can join your game.
Accept whatever colours KGS proposes but adjust the komi and handicap if necessary.
Afterwards enter the game result from
the <a href="addilresult.php" target="_top">menu entry</a>.</p>
<p>The only rule we stipulate about the pairing of games is that if
you have a choice, you should always try to play with someone
you haven't played before or haven't played very often.</p>
<h2>Format and promotions/relegations in individual league</h2>
<p>We need to see how many people want to play in the league before we make any firm decisions
about the format of the league.</p>
<p>Various people have suggested that a small number of larger divisions
with possibly more than one player promoted or relegated would be best.
Others have suggested that newly promoted players get extra rating points
in the next season to avoid them immediately dropping down again
and similarly newly relegated players should have rating points deducted.</p>
<p>However we really can't decide how that should operate
until we see the way the Individual League works,
so please sign up to the current "trial season" and play a few games.
If you want the records expunged before the first "real" season starts,
we will be happy to do this. </p>
<h2>Subscription for Individual league</h2>
<p>The current proposal is
that the subscription be &pound;5 per season for BGA members and &pound;8 for
non BGA members but this has not been firmly decided.</p>
<h2>Prizes</h2>
<p>We have not decided what prize or prizes to award
for the Individual League winners. Again this may depend on the number of players.</p>
<h2>Results</h2>
<p>Results of games in the Individual League may be obtained by selecting the
<a href="ileague.php" target="_top">league table</a> and clicking on the relevant
player's name.</p>
<p>The displayed results will show all the player's games
in the team league and the individual league in date order
but with "Individual" in place of the team name. </p>
<h1>Referees</h1>
<p>A few incidents have highlighted the need for referees in some circumstances.
These might be a dispute over the playing of game,
such as when one or both parties do not turn up,
or the game being played with the wrong rules, handicap etc.</p>
<p>If this happens please contact an admin person or BGA council
member as soon as possible as referee.
The person chosen should not have any direct involvement
with the match in question and should refuse to act as referee
if he/she believes that there is any such involvement.</p>
<p>The person may decide:</p>
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
<p>If you think that the decision of the referee was wrong,
or that the referee had an undeclared interest in the result,
then you should appeal to the BGA council for a final decision.</p>
<p>Games decided in this fashion are entered as <b>W+N</b> or <b>B+N</b>
or <b>Jigo</b> as appropriate and no SGF file should be included.</p>
<p>If you make a mistake entering a result or you realise
that the game has been played with incorrect colours,
please do not try to fix it yourself or alter any other games
but contact an admin person as soon as possible.
This may possibly entail a decision as per a referee in some cases.</p>
<h1>Comments and Suggestions</h1>
<p>If you have comments or suggestions regarding this or any aspect of the league and
this website, please <a href="sendmail.php?f=John&l=Collins">contact John Collins</a>
before the end of October 2010.</p>
<a name="log"></a>
<h1>News Log</h1>
<p>The following is a list in reverse date order of events on the league and
the website.</p>
<p>The userid is that of the person who made the update and the date is when the
update was made not necessarily when a game was played.</p>
<table class="news">
<tr>
<th>Date</th>
<th>Userid</th>
<th>Item</th>
</tr>
<?php
$ret = mysql_query("select ndate,user,item,link  from news order by ndate desc");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_assoc($ret))  {
		$n = new News();
		$n->fromrow($row);
		print <<<EOT
<tr>
<td valign="top">{$n->display_date()}</td>
<td valign="top">{$n->display_user()}</td>
<td>{$n->display_item()} {$n->display_link()}</td>
</tr>

EOT;
	}
}
?>
</table>
</body>
</html>
