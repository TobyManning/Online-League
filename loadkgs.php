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
$date_played = new Matchdate();
$date_played->fromget();
$result = $_GET["r"];
$resulttype = $_GET["rt"];
$sgfdata = "";

$res = $result . '+' . $resulttype;
if (preg_match('/\d+/', $resulttype))
	$res .= '.5';

$prog = $_SERVER["DOCUMENT_ROOT"] . '/league/kgsfetchsgf.pl';

$fh = popen("$prog {$g->Wplayer->KGS} {$g->Bplayer->KGS} {$date_played->queryof()} $res", "r");
if ($fh)  {
	while ($part = fread($fh, 200))
		$sgfdata .= $part;
	$code = pclose($fh);
	if ($code != 0 || strlen($sgfdata) == 0)  {
		switch ($code) {
		default:
			$msg = "I cannot tell why code was $code (prog $prog)";
			break;
		case 10:
			$msg = "Could not find games on {$date_played->display()}";
			break;
		case 11:
			$msg = "Confused by which game was meant";
			break;
		case 12:
			$msg = "Found some games but they did not match result";
			break;
		case 13:
			$msg = "Unable to fetch game";
			break;
		}
		$Title - "Could not find game";
		print "<html>\n";
		include 'php/head.php';
		print <<<EOT
<body>
<h1>Game result add failed</h1>
<p>I could not find the game result because: $msg.</p>
<p>In order to avoid problems I have not updated anything.</p>
<p><a href="javascript:history.back()">Click here</a> to go back.</p>
</body>		
</html>
EOT;
		exit(0);
	}
}
if ($date_played->unequal($g->Date))
	$g->reset_date($date_played);
$g->set_result($result, $resulttype);
$g->set_sgf($sgfdata);
?>
<html>
<?php
$Title = "Game Result Added";
include 'php/head.php';
?>
<body>
<h1>Add Game Result complete</h1>
<p>
Finished adding result with SGF for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black was {$g->display_result()}.
</p>
EOT;
?>
</body>
</html>
