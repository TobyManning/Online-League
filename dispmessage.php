<?php
//   Copyright 2013 John Collins

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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/game.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/team.php';

// Get who I am

try {
        $player = new Player();
        $player->fromid($userid);
}
catch (PlayerException $e) {
        $mess = $e->getMessage();
        include 'php/wrongentry.php';
        exit(0);
}

// Get message

$messid = $_GET['mi'];
if  (!preg_match('/^\d+$/', $messid))  {
	$mess = "Inappropriate message id $messid";
	include 'php/wrongentry.php';
	exit(0);
}

$ret = mysql_query("select fromuser,created,gameind,subject,hasread,contents from message where ind=$messid");
if  (!$ret || mysql_num_rows($ret) == 0)  {
	$mess = "Could not find message $messid";
	include 'php/wrongentry.php';
	exit(0);
}
$row = mysql_fetch_assoc($ret);
$fu = $row["fromuser"];
$cr = $row["created"];
$gid = $row["gameind"];
$subj = $row["subject"];
$hsubj = htmlspecialchars($subj);
$hasr = $row["hasread"];
$cont = $row["contents"];
try {
	$fp = new Player();
	$fp->fromid($fu);
	$okfrom = true;
}
catch (PlayerException $e) {
	$okfrom = false;
}
if (!$hasr)
	mysql_query("update message set hasread=1 where ind=$messid");

if  (preg_match("/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+):(\d+)/", $cr, $matches))  {
	$dat = $matches[3] . '/' . $matches[2] . '/' . $matches[1];
	$tim = $matches[4] . ':' . $matches[5] . ':' . $matches[6];
}
else {
	$dat = $tim = $cr;
}
if ($gid != 0)  {
	$gam = new Game($gid);
	try {
		$gam->fetchdets();
		$mtch = new Match($gam->Matchind);
		$mtch->fetchdets();
		$mtch->fetchteams();
	}
	catch (GameException $e) {
		$gid = 0;
	}
	catch (MatchException $e) {
		$gid = 0;
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Display Message";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php';
$mhdr = $hasr? "Message": "New message";
print <<<EOT
<h1>$mhdr</h1>

<table class="resultsb">
<tr>
	<td><strong>From:</strong></td>
	<td>{$fp->display_name()}<td>
</tr>
<tr>
	<td><strong>To:</strong></td>
	<td>{$player->display_name()}<td>
</tr>
<tr>
	<td><strong>Sent:</strong</td>
	<td>$dat at $tim</td>
</tr>
<tr>
	<td><strong>Subject:</strong</td>
	<td>$hsubj</td>
</tr>

EOT;

if ($gid)  {
	print <<<EOT
<tr>
	<td><strong>Concerning match:</strong</td>
	<td>$hsubj</td>
	<td>{$mtch->Hteam->display_name(true)} -v- {$mtch->Ateam->display_name(true)}</td>
</tr>

EOT;
}
$hcont = htmlspecialchars($cont);
$hcont = preg_replace("/(\r\n)+$/", "", $hcont);
$hcont = preg_replace("/(\r?\n){2,}/", "</p>\n<p>", $hcont);
print <<<EOT
</table>
<p>$hcont</p>

EOT;
?>
</div>
</div>
</body>
</html>
