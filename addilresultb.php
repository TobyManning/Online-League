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
$userid = $_SESSION['user_id'];
$username = $_SESSION['user_name'];
$userpriv = $_SESSION['user_priv'];

if (strlen($username) == 0)  {
	print <<<EOT
<html>
<head>
<title>Not logged in</title>
</head>
<body>
<h1>Not logged in</h1>
<p>Sorry but you need to be logged in to enter this page.</p>
<p>If you were logged in before, do not worry, your session may have timed out.
Just start again from the menu on the left.</p>
</body>
</html>

EOT;
	exit(0);
}

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/matchdate.php';

$player = new Player();
try {
	$player->fromid($userid);
}
catch (PlayerException $e) {
	print <<<EOT
<html>
<head>
<title>Unknown player</title>
</head>
<body>
<h1>Unknown player</h1>
<p>Sorry, but player name $userid is not known.</p>
</body>
</html>

EOT;
	exit(0);
}

if ($player->ILdiv == 0)  {
	print <<<EOT
<html>
<head>
<title>Unknown player</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="il">
<h1>Not in individual league</h1>
<p>Sorry, but you, {$player->display_name(false)} are not currently in the individual
league.</p>
<p>If you want to join it, please update your account
<a href="ownupd.php" target="_top">here</a>.
</p>
</body>
</html>

EOT;
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Add result in individual League";
include 'php/head.php';
?>
<body class="il">
<script language="javascript">
function loadkgs() {
	var fm = document.ilresf;
	var dayel = fm.day;
	var monel = fm.month;
	var yrel = fm.year;
	var day = dayel.options[dayel.selectedIndex].value;
	var month = monel.options[monel.selectedIndex].value;
	var year = yrel.options[yrel.selectedIndex].value;
	var resty = fm.resulttype;
	
	if (resel.selectedIndex < 0 || resty.selectedIndex < 0) {
		alert("No result selected");
		return;
	}
	var resel = fm.result;
	var res;
	if (resel[0].checked)
		res = resel[0].value;
	else if (resel[1].checked)
		res = resel[1].value;
	else
		res = resel[2].value;
	var colel = fm.colour;
	var colour;
	if  (colel[0].checked)
		colour = colel[0].value;
	else
		colour = colel[1].value;
	var restype = resty.options[resty.selectedIndex].value;
	if (restype == 'N') {
		alert("Result type not set");
		return;
	}
	var plf = fm.plf.value;
	var pll = fm.pll.value;
	var oppel = fm.opp;
	if  (oppel.selectedIndex < 0)  {
		alert("No opponent selected");
		return;
	}
	var opp = oppel.options[oppel.selectedIndex.value];
	 
	//	document.location =
	alert("loadkgsil.php?plf=" + plf + "&pll=" + pll + "&opp=" + opp +
							  "&col=" + colour +
							  "&md=" + year + "-" + month + "-" + day + "&r=" +
							  res + "&rt=" + restype);
}
</script>
<h1>Add result for Individual League</h1>
<p>Welcome
<?php
print <<<EOT
<b>{$player->display_name()}</b>
online name
<b>{$player->display_online()}</b>
from Division
{$player->ILdiv}.

EOT;
?>
</p>
<p>To enter the individual league match result, please complete the form below:
</p>
<form action="addilresultb2.php" method="post" enctype="multipart/form-data" name="ilresf" onsubmit="javascript: return checkform();">
<?php $player->save_hidden("pl"); ?>
<table cellpadding="2" cellspacing="5" border="0">
<tr>
	<td>Match was played on</td>
	<td>
<?php
$dat = new Matchdate();
$dat->dateopt("");
?>
	</td>
</tr>
<tr>
	<td>Opponent was</td>
	<td><select name="opp">
<?php
$pl = list_players_ildiv($player->ILdiv);
foreach ($pl as $p) {
	if ($p->is_same($player))
		continue;
	$p->fetchdets();
	print <<<EOT
<option value="{$p->selof()}">
{$p->display_name(false)}
({$p->display_rank()}) {$p->display_online()}
</option>

EOT;
}
?>
	</select></td>
</tr>
<tr>
	<td>I was playing</td>
	<td><input type="radio" name="colour" value="B" checked>Black
	<input type="radio" name="colour" value="W">White</td>
</tr>
<tr><td>Outcome</td><td><input type="radio" name="result" value="W" checked>I won
<input type="radio" name="result" value="J">Jigo
<input type="radio" name="result" value="L">I lost</td></tr>
<tr><td>Score was</td>
<td><select name="resulttype" size="0">
<option value="N" selected>Not known</option>
<option value="R">Resign</option>
<option value="T">Time</option>
<?php
for ($v = 0; $v < 50; $v++)
	print "<option value=$v>$v.5</option>\n";
?>
<option value="H">Over 50</option>
</select>
</td>
</tr>
<tr>
	<td>SGF file of game here</td>
	<td><input type=file name=sgffile></td>
</tr>
<tr>
	<td colspan=2>Click <input type="submit" name="sub" value="Here"> if uploading file
	from my computer or no SGF</td>
</tr>
</table>
</form>
<p>Or <a href="javascript:loadkgs();"><b>Click here</b></a> if the game was played
on KGS with the right names - getting the date, result and score right.</p>
</body>
</html>
