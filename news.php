<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
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

$Title = "News";
include 'php/head.php';
$p = $_GET["p"];
if (strlen($p) != 0)
	$p = "#$p";
print <<<EOT
<frameset cols="15%,*">
<frame src="linkframe.php" frameborder="0" scrolling="auto" marginwidth="0" marginheight="0">
<frame src="newsb.php$p" frameborder="0" scrolling="auto" marginwidth="0" marginheight="0">
</frameset>
EOT;
?>
</html>
