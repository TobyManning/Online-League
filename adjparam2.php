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
include 'php/params.php';
$p = $_POST["p"];
if (strlen($p) == 0) {
    include 'php/wrongentry.php';
    exit(0);
}
$pars = new Params();
$pars->fetchvalues();
$pars->Played = $_POST["p"] + 0.0;
$pars->Won = $_POST["w"] + 0.0;
$pars->Drawn = $_POST["d"] + 0.0;
$pars->Lost = $_POST["l"] + 0.0;
$pars->For = $_POST["f"] + 0.0;
$pars->Against = $_POST["a"] + 0.0;
$pars->putvalues();
?>
<html>
<?php
$Title = "Adjustment of Parameters Complete";
include 'php/head.php';
?>
<body>
<h1>Adjusting parameters Complete</h1>
<p>Finished adjusting parameters.</p>
<p><a href="leagueb.php">Click here</a> to see what the league looks like now.</p>
</body>
</html>
