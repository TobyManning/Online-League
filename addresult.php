<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
$g = new Game();
try  {
	$g->fromget();
	$g->fetchdets();
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Add Game Result";
include 'php/head.php';
?>
<body>
<script language="javascript">
function loadkgs() {
	var fm = document.resform;
	var game = fm.gn.value;
	var dayel = fm.day;
	var monel = fm.month;
	var yrel = fm.year;
	var day = dayel.options[dayel.selectedIndex].value;
	var month = monel.options[monel.selectedIndex].value;
	var year = yrel.options[yrel.selectedIndex].value;
	var resel = fm.result;
	var resty = fm.resulttype;
	if (resel.selectedIndex < 0 || resty.selectedIndex < 0) {
		alert("No result selected");
		return;
	}
	var res = resel.options[resel.selectedIndex].value;
	var restype = resty.options[resty.selectedIndex].value;
	if (restype == 'N') {
		alert("Result type not set");
		return;
	}
	document.location = "loadkgs.php?gn=" + game +
							  "&md=" + year + "-" + month + "-" + day + "&r=" +
							  res + "&rt=" + restype;
}
</script>
<h1>Add Game Result</h1>
<p>
Adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name(false)}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name(false)}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black.
</p>
EOT;
if (strlen($g->Wplayer->KGS) != 0 && strlen($g->Bplayer->KGS) != 0) {
	print <<<EOT
<p><b>If the game was played on KGS</b> using the online names
{$g->Wplayer->display_online()} and
{$g->Bplayer->display_online()}, get the date played and result correct in the form
below and <a href="javascript:loadkgs();"><b>Click here</b></a>.</p>
EOT;
}
print <<<EOT
<form name="resform" action="addresult2.php" method="post" enctype="multipart/form-data">
{$g->save_hidden()}
<p>
EOT;
$today = new Matchdate();
$today->dateopt("Game was played on");
print <<<EOT
</p>
<p>
Result was
<select name="result" size="0">
<option value="W">White Win</option>
<option value="B">Black Win</option>
<option value="J">Jigo</option>
</select>
by
<select name="resulttype" size="0">
<option value="N">Not known</option>
<option value="R">Resign</option>
<option value="T">Time</option>
EOT;
for ($v = 0; $v < 50; $v++)
	print "<option value=$v>$v.5</option>\n";
?>
<option value="H">Over 50</option>
</select>
</p>
<p>
If you can please browse on your computer for an SGF file of the game to
upload <input type=file name=sgffile>
</p>
<p>When done, press this:
<input type="submit" value="Add result">
</p>
</form>
<p>If you never meant to get to this page
<a href="javascript:history.back()">click here</a> to go back.</p>
</body>
</html>
