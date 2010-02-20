<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/game.php';
try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
	$player->fetchclub();
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Player Games";
include 'php/head.php';
?>
<body>
<h1>Player Results</h1>
<?php
print <<<EOT
<p>
These are recorded games on the league for {$player->display_name()} currently
{$player->display_rank()} of {$player->Club->display_name()}.
</p>
EOT;
if ($player->played_games(false) == 0)  {
	print <<<EOT
<p>
Sorry but there do not seem to be any recorded completed games for {$player->display_name()}.
Please <a href="javascript:history.back()">click here</a> to go back.
</p>
</body>
</html>
EOT;
	exit(0);
}
print <<<EOT
<p>
Record is Played: {$player->played_games(false)} Won: {$player->won_games()}
Drawn: {$player->drawn_games()} Lost: ($player->lost_games()}.
</p>
<div align="center">
<img src="php/piewdl.php?w={$player->won_games()}&d={$player->drawn_games()}&l=($player->lost_games()}">
</div>
<table>
<tr>
<th>Date</th>
<th>Team</th>
<th>Colour</th>
<th>Opponent</th>
<th>Team</th>
<th>Outcome</th>
<th>Detail</th>
</tr>

EOT;
$ret = mysql_query("select ind from game where result!='N' and (({$player->queryof('w')}) or ({$player->queryof('b')})) order by matchdate");
if ($ret && mysql_num_rows($ret)) {
	while ($row = mysql_fetch_assoc($ret)) {
		try  {
			$g = new Game();
			$g->fromget();
			$g->fetchdets();
		}
		catch (GameException $e) {
			print "<tr><td colspan=7>Cannot find game</td></tr>\n";
			continue;
		}
		print "<tr><td>{$g->date_played()}</td>\n";
		if ($g->Wplayer->is_same($player))  {
			print "<td>{$g->Wteam->display_name()}</td>\n";
			print "<td>White</td>\n";
			print "<td>{$g->Bplayer->display_name()}</td>\n";
			print "<td>{$g->Bteam->display_name()}</td>\n";
			switch ($g->Result) {
			default: $r = '?'; break;
			case 'W': $r = "Won"; break;
			case 'J': $r = "Jigo"; break;
			case 'B': $r = "Lost"; break;
			}
			print "<td>$r</td>\n";			
		}
		else {
			print "<td>{$g->Bteam->display_name()}</td>\n";
			print "<td>Black</td>\n";
			print "<td>{$g->Wplayer->display_name()}</td>\n";
			print "<td>{$g->Wteam->display_name()}</td>\n";
			switch ($g->Result) {
			default: $r = '?'; break;
			case 'W': $r = "Lost"; break;
			case 'J': $r = "Jigo"; break;
			case 'B': $r = "Won"; break;
			}
			print "<td>$r</td>\n";			
		}
		print "<td>{$g->display_result()}</td></tr>\n";
	}
}
?>
</table>
</body>
</html>
