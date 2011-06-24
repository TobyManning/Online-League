<?php
//   Copyright 2011 John Collins

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
include 'php/teammemb.php';

try {
	$player = new Player();
	$player->fromid($userid);
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

// First get ourselves a list of unpaid teams

$unpaid_teams = array();
$ret = mysql_query("select name from team where paid=0 and playing!=0 order by name");
try {
	if ($ret and mysql_num_rows($ret) > 0)  {
		while ($row = mysql_fetch_array($ret))  {
			$team = new Team($row[0]);
			$team->fetchdets();
			array_push($unpaid_teams, $team);
		}
	}
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);
}

// Go over each team and calculate subs for each

foreach ($unpaid_teams as $team)  {
	$team->Subs = 10;
	$membs = $team->list_members();
	
	// Add £5 for each non BGA member
	
	foreach ($membs as $memb) {
		$memb->fetchdets();
		if (!$memb->BGAmemb)  {
			$team->Nonbga += 1;
			$team->Subs += 5;
		}
	}
}

// Likewise get list of unpaid indiv league players

$unpaid_il = array();
$ret = mysql_query("select first,last from player where ildiv!=0 and ilpaid=0 order by last,first");
if ($ret) {
	while ($row = mysql_fetch_array($ret))  {
		$pl = new Player($row[0], $row[1]);
		$pl->fetchdets();
		$pl->ILsubs = 5;
		if (!$pl->BGAmemb)
			$pl->ILsubs = 8;
		array_push($unpaid_il, $pl);
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Payment of subscriptions";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function fillinvals() {
	var vform = document.payform;
	var asl = vform.actselect;
	var ind = asl.selectedIndex;
	if (ind < 0)
		return;
	var str = asl.options[ind].value;
	var pieces = str.split(':');
	if (pieces.length == 4) {
		vform.ltype.value = "Team";
		vform.nam.value = pieces[1];
		vform.nbga.value = pieces[2];
		vform.total.value = pices[3];
	}
	else {
		vform.ltype.value = "Individual";
		vform.nam.value = pieces[1] + ' ' + pieces[2];
		vform.nbga.value = pieces[3];
		vform.total.value = pieces[4];
	}
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Payment of subscriptions</h1>
<?php
$Name = $player->display_name(false);
print <<<EOT
<p>Thank you, $Name, for entering the payments page.</p>

EOT;
if (count($unpaid_teams) + count($unpaid_il) <= 0)
	print <<<EOT
<p>Thanks for visiting but nothing actually needs paying!</p>
<p>Please feel virtuous and visit somewhere else!</p>

EOT;
else {
	print <<<EOT
<form name="payform" action="paymentres.php" method="post" enctype="application/x-www-form-urlencoded">
<table>
<tr><td>Required action</td>
<td><select name="actselect" size="0" onchange="fillinvals();">

EOT;
$linit = $ninit = "";
$nbgainit = $totinit = 0;
$hadm = false;
foreach ($unpaid_teams as $team) {
	$seld = "";
	if (!$hadm)  {
		if ($team->Captain->is_same($player))  {
			$seld = " selected";
			$hadm = true;
			$linit = "Team";
			$ninit = $team->display_name();
			$nbgainit = $team->Nonbga;
			$totinit = $team->Subs;
		}
	}
	print <<<EOT
<option$seld value="T:{$team->display_name()}:{$team->Nonbga}:{$team->Subs}">
{$team->display_name()} - subs is {$team->Subs}</option>

EOT;
}
foreach ($unpaid_il as $pl) {
	$seld = "";
	$nbgan = $pl->BGAmemb? 0: 1;
	if (!$hadm)  {
		if ($pl->is_same($player)) {
			$seld = " selected";
			$hadm = true;
			$linit = "Individual";
			$ninit = $pl->display_name(false);
			$nbgainit = $nbgan;
			$totinit = $pl->ILsubs;
		}
	}
	print <<<EOT
<option$seld value="I:{$pl->First}:{$pl->Last}:$nbgan:{$pl->ILsubs}">
{$pl->display_name(false)} - I.L. subs is {$pl->ILsubs}</option>

EOT;
}
print <<<EOT
</select></td></tr>
<tr><td>League</td><td><input type="text" name="ltype" value="$linit" size="15"></td></tr>
<tr><td>Name</td><td><input type="text" name="nam" value="$ninit" size="30"></td></tr>
<tr><td>Non-BGA</td><td><input type="text" name="nbga" value="$nbgainit" size="2"></td></tr>
<tr><td>Total &pound;</td><td><input type="text" name="total" value="$totinit" size="6"></td></tr>
<tr><td colspan="2"><input type="submit" name="pay" value="Pay Subscription"></td></tr>
</table>
</form>

EOT;
}
?>
</div>
</div>
</body>
</html>
