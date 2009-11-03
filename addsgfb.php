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
include 'php/matchdate.php';
include 'php/match.php';
include 'php/game.php';
?>
<html>
<?php
$Title = "Add SGF file";
include 'php/head.php';
?>
<body>
<h1>Add SGF record</h1>
<p>
Use this page to add SGF records to results which don't have them.
</p>
<?php
$glist = list_nosgf_games();
if (count($glist) == 0)  {
print <<<EOT
<p>(None at present).</p>
EOT;
}
else {
	print <<<EOT
<form action="addsgf2.php" method="post" enctype="multipart/form-data">
<p>
Select game:
<select name="gn">
EOT;
	foreach ($glist as $g) {
		print <<<EOT
<option value={$g->query_ind()}>
{$g->Date->display()} {$g->Wplayer->display_name()}-v-{$g->Bplayer->display_name()}
</option>
EOT;
	}
	print <<<EOT
</select>
</p>
<p>Upload SGF file from your computer:<input type=file name=sgffile></p>
<p>When done, press this:
<input type="submit" value="Add SGF">
</p>
</form>
EOT;
}
?>
</body>
</html>
