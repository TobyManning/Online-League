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

session_start();
if (isset($_SESSION['user_id'])) {
	$userid = $_SESSION['user_id'];
	$username = $_SESSION['user_name'];
	$userpriv = $_SESSION['user_priv'];
	$login = true;
}
else
	$login = false;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "British Go Association League";
include 'php/head.php';
?>
<body class="nomarg">
<script language="javascript" src="webfn.js"></script>
<script language="JavaScript">
function lostpw() {
	var l = document.lifm.user_id.value;
	if (!nonblank(l)) {
		 alert("No userid given");
       return;
   }
   window.open("rempwbyuid.php?uid=" + l, "Password Reminder", "width=450,height=200,resizeable=yes,scrollbars=yes");
}
</script>
<h2>Places</h2>
<?php
$a = $_GET["abs"];
if ($a != "")
	$a = "/league/";
$adm = $_GET["adm"];
print <<<EOT
<table>
<tr><td><a href="${a}index.php" target="_top">Home</a></td></tr>
<tr><td><a href="http://www.britgo.org" target="_top">BGA Home</a></td></tr>
<tr><td><a href="${a}aboutus.php" target="_top">About The League</a></td></tr>
<tr><td><a href="${a}clubs.php" target="_top">Clubs</a></td></tr>
<tr><td><a href="${a}teams.php" target="_top">Teams</a></td></tr>
<tr><td><a href="${a}players.php" target="_top">Players</a></td></tr>
<tr><td class="subind"><a href="${a}players.php?by=club" target="_top">By club</a></td></tr>
<tr><td class="subind"><a href="${a}players.php?by=rank" target="_top">By rank</a></td></tr>
<tr><td class="subind"><a href="${a}players.php?by=clubrank" target="_top">By club/rank</a></td></tr>
<tr><td><a href="${a}pbt.php" target="_top">Players by team</a></td></tr>
<tr><td><a href="${a}matches.php" target="_top">Matches</a></td></tr>
<tr><td><a href="${a}results.php" target="_top">Results</a></td></tr>
<tr><td><a href="${a}league.php" target="_top">League</a></td></tr>
EOT;
if ($login) {
	if ($userpriv == 'A' || $userpriv == 'SA')  {
		print <<<EOT
<tr><td><a href="${a}admin.php" target="_top" class="memb">Admin menu</a></td></tr>
EOT;
		if (strlen($adm) != 0) {
			print <<<EOT
	<tr><td class="subind"><a href="${a}newclub.php" target="_top" class="memb">New club</a></td></tr>
	<tr><td class="subind"><a href="${a}newplayer.php" target="_top" class="memb">New player</a></td></tr>
	<tr><td class="subind"><a href="${a}newteam.php" target="_top" class="memb">New team</a></td></tr>
	<tr><td class="subind"><a href="${a}clubupd.php" target="_top" class="memb">Update clubs</a></td></tr>
	<tr><td class="subind"><a href="${a}playupd.php" target="_top" class="memb">Update players</a></td></tr>
	<tr><td class="subind"><a href="${a}rempw.php" target="_top" class="memb">Remind password</a></td></tr>
	<tr><td class="subind"><a href="${a}teamsupd.php" target="_top" class="memb">Update teams</a></td></tr>
	<tr><td class="subind"><a href="${a}matchupd.php" target="_top" class="memb">Update matches</a></td></tr>
	<tr><td class="subind"><a href="${a}sendtc.php" target="_top" class="memb">Message to team captains</a></td></tr>
	<tr><td class="subind"><a href="${a}addsgf.php" target="_top" class="memb">Add sgf</a></td></tr>
EOT;
		}
	}
	$qu = htmlspecialchars($username);
	print <<<EOT
<tr><td><a href="${a}ownupd.php" target="_top">Update account</a></td></tr>
<tr><td><a href="${a}logout.php">Logout<br>$qu</a></td></tr>
EOT;
}
print "</table>\n";
if (!$login)  {
	if (isset($_COOKIE['user_id']))
		$userid = $_COOKIE['user_id'];
	print <<<EOT
<form name="lifm" action="${a}login.php" method="post" enctype="application/x-www-form-urlencoded">
<p>Userid:<input type="text" name="user_id" value="$userid" size="10"></p>
<p>Password:<input type="password" name="passwd" size="10"></p>
<p><input type="submit" value="Login"></p>
</form>
<p><a href="javascript:lostpw();">Lost password?</a></p>
EOT;
}
?>
</body>
</html>
