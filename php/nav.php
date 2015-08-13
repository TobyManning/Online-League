<?php
//   Copyright 2011 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

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
<a href="http://www.britgo.org" title="Go to BGA main site">
<img src="images/gohead12.gif" width="133" height="47" alt="BGA Logo" border="0" hspace="0" vspace="0"></a>
<ul class="pmen">
<li><a href="index.php" title="Go to league home page">League Home</a></li><br/>
<li><a href="#">Information &#9662;</a>
<ul class="pdropdown">
<li><a href="info.php" title="General Information about the league">Information</a></li>
<li><a href="playing.php" title="How to play in the league">League Rules</a></li>
<li><a href="news.php" title="Read about recent results and update">League News</a></li>
<li><a href="clubs.php" title="View the list of clubs with members in the league">Clubs</a></li>
<li><a href="teams.php" title="View the teams participating in the league">Teams</a></li>
</ul></li><br/>
<li><a href="#">Players &#9662;</a>
<ul class="pdropdown">
<li><a href="players.php" title="View players in alphabetical order">Sorted by name</a></li>
<li><a href="players.php?by=club" title="VIew players sorted by club">Sorted by club</a></li>
<li><a href="players.php?by=rank" title="View players sorted by rank">Sorted by rank</a></li>
<li><a href="players.php?by=clubrank" title="View players sorted by club then by rank">Sorted by club/rank</a></li>
<li><a href="pbt.php" title="View players by team">Sorted by team</a></li>
</ul></li><br/>
<li><a href="#">Matches &#9662;</a>
<ul class="pdropdown">
<li><a href="matches.php" title="View league matches for current season">Match status</a></li>
<li><a href="results.php" title="View results table for league matches">Results</a></li>
</ul></li><br/>
<li><a href="#">League Table &#9662;</a>
<ul class="pdropdown">
<li><a href="league.php" title="Display current team league table">Current League table</a></li>
<li><a href="leaguehist.php" title="Display historical league tables including individual league">Historical</a></li>
<!-- <li><a href="ileague.php" class="il" title="View league table for Individual League">Individual League</a></li> -->
</ul></li><br/>
<?php
if ($logged_in) {
//	print <<<EOT
//<li><a href="addilresult.php" class="il" title="For player use - add result in Individual League">Add IL Result</a></li>
//
//EOT;
	if ($admin)  {
		print <<<EOT
<li><a href="admin.php" class="memb" title="Administer the leagues">Admin menu</a></li><br/>

EOT;
	}
	print <<<EOT
<li><a href="#">User choices &#9662;</a>
<ul class="pdropdown">

EOT;
	$qu = htmlspecialchars($username);
	$nummsgs = num_unread_msgs();
	if ($nummsgs == 0)  {
		print <<<EOT
<li><a href="messages.php" title="Send and receive messages to/from opponents">Messages</a></li>

EOT;
	}
	elseif($nummsgs == 1) {
		print <<<EOT
<li<a href="messages.php" class="message" title="Send and receive messages to/from opponents">1 Message</a></li>

EOT;
	}
	else  {
		print <<<EOT
<li><a href="messages.php" class="message" title="Send and receive messages to/from opponents">$nummsgs Messages</a></li>

EOT;
	}
	print <<<EOT
<li><a href="osmatches.php" title="Display your outstanding games in the league">Outstanding</a></li>
<li><a href="ownupd.php" title="Update your own account - rank etc">Update account</a></li>
<li><a href="https://league.britgo.org/payments.php" title="Pay league subscriptions via PayPal">Pay subscriptions</a></li>
<li><a href="logout.php" title="Log yourself out">Logout<br>$qu</a></li>
</ul></li>

EOT;
}
?>
</ul>
<?php
if (!$logged_in)  {
	if (isset($_COOKIE['user_id']))
		$userid = $_COOKIE['user_id'];
	print <<<EOT
<form name="lifm" action="login.php" method="post" enctype="application/x-www-form-urlencoded">
<p>Userid:<input type="text" name="user_id" id="user_id" value="$userid" size="10"></p>
<p>Password:<input type="password" name="passwd" size="10"></p>
<p><input type="submit" value="Login"></p>
</form>
<p><a href="javascript:lostpw();" title="Get your lost password">Lost password?</a></p>
<p><a href="newacct.php" title="Create yourself an account">Create account</a></p>

EOT;
}
print <<<EOT
<p class="smallnotetm">(Please note a cookie will be used to save login name and session id only, no other
information).</p>
</div>
</div>
<div id="$contentid">
<div class="innertube">

EOT;
?>
