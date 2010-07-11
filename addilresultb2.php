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
include 'php/game.php';
include 'php/news.php';

$player = new Player();
$opp = new Player();

try  {
	$player->frompost("pl");
	$opp->fromsel($_POST["opp"]);
	$player->fetchdets();
	$opp->fetchdets();
}
catch (PlayerException $e)  {
	print <<<EOT
<html>
<head>
<title>Trouble with player details</title>
</head>
<body>
<h1>Trouble fetching player details</h1>
<p>Sorry something has gone wrong with your player detail posting.
</p>
</body>
</html>

EOT;
	exit(0);
}
$dat = new Matchdate();
$dat->frompost();

$mycolour = $_POST["colour"];
$myresult = $_POST["result"];
if ($mycolour == 'B')
	$result = $myresult == 'W'? 'B': 'W';
else
	$result = $myresult == 'W'? 'W': 'B';
$rtype = $_POST["resulttype"];
$sgfdata = "";
$fn = $_FILES["sgffile"];
if ($fn['error'] == UPLOAD_ERR_OK  &&  preg_match('/.*\.sgf$/i', $fn['name']) && $fn['size'] > 0)
	$sgfdata = file_get_contents($fn['tmp_name']);

$g = new Game(0, 0, $player->ILdiv, 'I');
if  ($mycolour == 'B')  {
	$g->Bplayer = $player;
	$g->Wplayer = $opp;
}
else  {
	$g->Wplayer = $player;
	$g->Bplayer = $opp;
}
if (preg_match('/\d+/', $rtype))
	$rtype .= '.5';
if ($result != 'J')
	$rtype = "$result+$restype";
else
	$rtype = "Jigo";

if (strlen($sgfdata) != 0)
	$g->Sgf = $sgfdata;
$g->Date = $dat;
// $g->create_game();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Game Result Added";
include 'php/head.php';
?>
<body class="il">
<h1>Add Game Result</h1>
<p>
Finished adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name(false)}</b>
({$g->Wplayer->display_rank()}) as White and
<b>{$g->Bplayer->display_name(false)}</b>
({$g->Bplayer->display_rank()}) as Black was {$g->display_result()}.
</p>

EOT;
$n = new News($userid, "Individual League game completed between {$player->display_name(false)} and {$opp->display_name(false)} in Division {$player->ILdiv}", false); 
// $n->addnews();	
?>
</body>
</html>
