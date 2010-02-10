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
include 'php/params.php';
?>
<html>
<?php
$Title = "Adjustment of Parameters";
include 'php/head.php';
?>
<body>
<h1>Adjusting parameters</h1>
<p>
Use the following form to adjust the parameters used to order the league results.
Each field may be a number, possibly negative and with decimal places. The corresponding value
is added to the score for each result to determine the order in which the league teams
are displayed.
</p>
<p>Please note there is no real check! Only enter sensible values!!!</p>
<form action="adjparam2.php" method="post" enctype="application/x-www-form-urlencoded" name='pform' onsubmit='return checkform();'>
<table>
<tr><th>For each</th><th>Add to score</th></tr>
<?php
$pars = new Params();
$pars->fetchvalues();
print <<<EOT
<tr><td>Match Played</td><td><input type="text" name="p" value="{$pars->Played}" size="20"></td></tr>
<tr><td>Match Won</td><td><input type="text" name="w" value="{$pars->Won}" size="20"></td></tr>
<tr><td>Match Drawn</td><td><input type="text" name="d" value="{$pars->Drawn}" size="20"></td></tr>
<tr><td>Match Lost</td><td><input type="text" name="l" value="{$pars->Lost}" size="20"></td></tr>
<tr><td>Game Won</td><td><input type="text" name="f" value="{$pars->For}" size="20"></td></tr>
<tr><td>Game Lost</td><td><input type="text" name="a" value="{$pars->Against}" size="20"></td></tr>
EOT;
?>
</table>
<p>Then <input type="submit" name="Sub" value="Click Here"> when ready.</p>
<p>If a game is drawn (Jigo) then half the "Game Won" value plus half the "Game Lost" value
is added.</p>
</form>
</body>
</html>
