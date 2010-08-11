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

include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/game.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Add SGF file";
include 'php/head.php';
?>
<body>
<h1>Amend game record</h1>
<p>
Use this page to amend game records.
</p>
<?php
$glist = list_played_games();
if (count($glist) == 0)  {
print <<<EOT
<p>(None at present).</p>
EOT;
}
else {
	print <<<EOT
<p>These are the existing played game records. Click on the result column to edit
one.</p>
<table>
<tr>
<th>Date</th>
<th>White</th>
<th>Online</th>
<th>Black</th>
<th>Online</th>
<th>Result</th>
</tr>
EOT;
	foreach ($glist as $g) {
		$rd = htmlspecialchars($g->Resultdet);
		print <<<EOT
<tr>
<td>{$g->Date->display()}</td>
<td>{$g->Wplayer->display_name(false)}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Bplayer->display_name(false)}</td>
<td>{$g->Bplayer->display_online()}</td>
<td><a href="fixres2.php?{$g->urlof()}">$rd</a></td>
</tr>
EOT;
	}
	print <<<EOT
</table>
EOT;
}
?>
</body>
</html>
