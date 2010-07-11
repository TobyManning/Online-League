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
<title>Not in league</title>
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

$opp = new Player();
try {
	$opp->fromsel($_GET["opp"]);
}
catch (PlayerException $e) {
	print <<<EOT
<html>
<head>
<title>No opponent</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="il">
<h1>No opponent</h1>
<p>Sorry but I couldn't work out who your opponent was.</p>
</body>
</html>

EOT;
	exit(0);
}

$mycolour = $_GET["col"];
$dat = new Matchdate();
$dat->fromget();
$myres = $_GET["r"];
$myrt = $_GET["rt"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Add Game Result";
include 'php/head.php';
?>
<body class="il">
<?php
print <<<EOT
<h1>Result recording</h1>
<p>
Got result of {$player->display_name()} ($mycolour) versus {$opp->display_name()} as
$myres and $myrt.
</p>

EOT;
?>
<p>This is still a testing version and hasn't really done anything.
</p>
</body>
</html>
