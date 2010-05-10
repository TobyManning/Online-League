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

include 'php/matchdate.php';
$div = $_GET["div"];
if (strlen($div) == 0) {
	include 'php/wrongentry.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Initialise Matches for Division $div";
include 'php/head.php';
$md = new Matchdate();
$md->set_season();
print <<<EOT
<body>
<h1>Initialise Matches for division $div</h1>
<form action="matchinit.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="div" value="$div">
<p>
EOT;
$md->dateopt('Starting date');
?>
<p>
<input type="submit" value="Generate Matches">
</p>
</form>
<p>Click <a href="javascript:self.close()">here</a> to close this window.</p>
</body>
</html>
