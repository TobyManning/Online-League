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

// Version of loadkgs for where we "believe" the existing match date
// and just want to get the SGF file for an existing game.

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

$sgfdata = "";

if ($g->Result == 'N') {
	$msg = "Game is not played yet";
}
elseif (strlen($g->Wplayer->KGS) == 0 || strlen($g->Bplayer->KGS) == 0) {
	$msg = "Cannot download game as both players need to have KGS names";
}
else {
	$prog = $_SERVER["DOCUMENT_ROOT"] . '/league/kgsfetchsgf.pl';
	$fh = popen("$prog {$g->Wplayer->KGS} {$g->Bplayer->KGS} {$g->Date->queryof()} {$g->Resultdet}", "r");
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
				$msg = "Could not find games on {$g->Date->display()}";
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
		}
	}
	else  {
		$msg = "Could not start loader";
	}
}

if (strlen($sgfdata) == 0)  {
	$Title - "Could not find game";
	print "<html>\n";
	include 'php/head.php';
	print <<<EOT
<body>
<h1>Game score add failed</h1>
<p>I could not find the game result because: $msg.</p>
<p><a href="javascript:history.back()">Click here</a> to go back.</p>
</body>		
</html>
EOT;
	exit(0);
}
$g->set_sgf($sgfdata);
?>
<html>
<?php
$Title = "Add Game SGF Complete";
include 'php/head.php';
?>
<body>
<h1>Add Game SGF complete</h1>
<p>
Finished adding SGF for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black.
</p>
EOT;
?>
</body>
</html>
