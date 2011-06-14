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

$classid = "Nav";
$contentid = "Content";
if (isset($hasfoot))  {
	$classid = "Navwf";
	$contentid = "Contentwf";
}

print <<<EOT
<div id="$classid">

EOT;
?>
<div class="innertube">
<a href="http://www.britgo.org">
<img src="images/gohead12.gif" width="133" height="47" alt="BGA Logo" border="0" hspace="0" vspace="0"></a>
<table>
<tr><td><a href="index.php">League Home</a></td></tr>
<tr><td><a href="playing.php">Rules</a></td></tr>
<tr><td><a href="news.php">News</a></td></tr>
<tr><td><a href="clubs.php">Clubs</a></td></tr>
<tr><td><a href="teams.php">Teams</a></td></tr>
<tr><td><a href="players.php">Players</a></td></tr>
<tr><td class="subind"><a href="players.php?by=club">By club</a></td></tr>
<tr><td class="subind"><a href="players.php?by=rank">By rank</a></td></tr>
<tr><td class="subind"><a href="players.php?by=clubrank">By club/rank</a></td></tr>
<tr><td class="subind"><a href="pbt.php">By team</a></td></tr>
<tr><td><a href="matches.php">Team Matches</a></td></tr>
<tr><td><a href="results.php">Team Results</a></td></tr>
<tr><th colspan="2">Standings</th></tr>
<tr><td class="subind"><a href="league.php">Team League</a></td></tr>
<tr><td class="subind"><a href="ileague.php" class="il">Individual League</a></td></tr>
<?php
if ($logged_in) {
	print <<<EOT
<tr><td><a href="addilresult.php" class="il">Add IL Result</a></td></tr>

EOT;
	if ($admin)  {
		print <<<EOT
<tr><td><a href="admin.php" class="memb">Admin menu</a></td></tr>

EOT;
		if (isset($showadmmenu))  {
			print <<<EOT
<tr><td class="subind"><a href="newclub.php" class="memb">New club</a></td></tr>
<tr><td class="subind"><a href="newplayer.php" class="memb">New player</a></td></tr>
<tr><td class="subind"><a href="newteam.php" class="memb">New team</a></td></tr>
<tr><td class="subind"><a href="clubupd.php" class="memb">Update clubs</a></td></tr>
<tr><td class="subind"><a href="playupd.php" class="memb">Update players</a></td></tr>
<tr><td class="subind"><a href="rempw.php" class="memb">Remind password</a></td></tr>
<tr><td class="subind"><a href="teamsupd.php" class="memb">Update teams</a></td></tr>
<tr><td class="subind"><a href="matchupd.php" class="memb">Update matches</a></td></tr>
<tr><td class="subind"><a href="getosplayer.php" class="memb">O/S matches</a></td></tr>
<tr><td class="subind"><a href="sendtc.php" class="memb">Message to team captains</a></td></tr>
<tr><td class="subind"><a href="unpaidteams.php" class="memb">Unpaid message</a></td></tr>	
<tr><td class="subind"><a href="fixres.php" class="memb">Amend result</a></td></tr>
<tr><td class="subind"><a href="ilarchive.php" class="memb">Archive Ind League</a></td></tr>	
<tr><td class="subind"><a href="addsgf.php" class="memb">Add sgf</a></td></tr>
<tr><td class="subind"><a href="adjparam.php" class="memb">Adjust parameters</a></td></tr>

EOT;
		}
	}
	$qu = htmlspecialchars($username);
	print <<<EOT
<tr><td><a href="osmatches.php">Outstanding</a></td></tr>
<tr><td><a href="ownupd.php">Update account</a></td></tr>
<tr><td><a href="logout.php">Logout<br>$qu</a></td></tr>

EOT;
}
?>
</table>
<?php
if (!$logged_in)  {
	if (isset($_COOKIE['user_id']))
		$userid = $_COOKIE['user_id'];
	print <<<EOT
<form name="lifm" action="login.php" method="post" enctype="application/x-www-form-urlencoded">
<p>Userid:<input type="text" id="user_id" value="$userid" size="10"></p>
<p>Password:<input type="password" name="passwd" size="10"></p>
<p><input type="submit" value="Login"></p>
</form>
<p><a href="javascript:lostpw();">Lost password?</a></p>
<p><a href="newacct.php">Create account</a></p>

EOT;
}
print <<<EOT
</div>
</div>
<div id="$contentid">
<div class="innertube">

EOT;
?>
