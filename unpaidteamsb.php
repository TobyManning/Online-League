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

session_start();
$username = $_SESSION['user_name'];
$userpriv = $_SESSION['user_priv'];
$logged_in = strlen($username) != 0;
if (!$logged_in || ($userpriv != 'A' && $userpriv != 'SA')) {
	$mess = "You have to be logged in as an admin to see this page";
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
?>
<html>
<?php
$Title = "Unpaid Teams";
include 'php/head.php';
?>
<body>
<h1>Teams which have not paid</h1>
<?php
$ret = mysql_query("select name from team where paid=0 order by divnum,name");
if (!$ret || mysql_num_rows($ret) == 0)  {
	print <<<EOT
<p>There does not seem to be any team which has not paid.</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>
</body>
</html>
EOT;
	exit(0);
}
?>
<form name="mailform" action="unpaidteams2.php" method="post" enctype="application/x-www-form-urlencoded">
<table class="teamsb">
<tr>
	<th>Send mail</th>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
</tr>
<?php
$num = 0;
while ($row = mysql_fetch_array($ret))  {
	$team = new Team($row[0]);
	$team->fetchdets();
	print <<<EOT
<tr>
<td><input type="checkbox" name="tnum[]" value="$num" checked></td>
<td>{$team->display_name()}</td>
<td>{$team->display_description()}</td>
<td>{$team->display_captain()}</td>
</tr>
EOT;
	$num++;
}
?>
</table>
<p>Reply to:<input type="text" name="emailrep"></p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>
<h2>Set all teams as unpaid</h2>
<p>At the start of the season, you will want to set all teams as not having paid.
If you want to do this now, <a href="setunpaid.php">click here</a>.
</p>
</body>
</html>
