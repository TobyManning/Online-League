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

include 'php/checksecure.php';
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
if (count($unpaid_teams) + count($unpaid_il) <= 0)
	print "<body>\n";
else
   print "<body onload=\"fillinvals();\">\n";
?>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">

function replacecell(tab, row, val, hl)  {
	var trow = tab.rows[row];
	var tcell = trow.cells[1];
	var vnode;
	if (hl)  {
		vnode = document.createElement('span');
		vnode.innerHTML = val;
	}
	else
		vnode = document.createTextNode(val);
	tcell.replaceChild(vnode, tcell.firstChild);
}	

function fillinvals() {
	var vform = document.payform;
	var asl = vform.actselect;
	var ind = asl.selectedIndex;
	if (ind < 0)
		return;
	var str = asl.options[ind].value;
	var pieces = str.split(':');
	var pftab = document.getElementById('pftab');
	var typev,namev,bgav,totv;
	if (pieces.length == 4) {
		typev = "Team &pound;10";
		namev = pieces[1];
		var nm = parseInt(pieces[2]);
		if (nm == 0)
			bgav = "All BGA members";
		else if (nm == 1)
			bgav = "One non-BGA member &pound;5";
		else  {
			surch = 5 * nm;
			bgav = nm + " non-BGA members @ &pound;5 per non-member - &pound;" + surch;
		}
		totv = pieces[3];			
	}
	else {
		typev = "Individual &pound;5";
		namev = pieces[1] + ' ' + pieces[2];
		if (parseInt(pieces[3]) != 0)
			bgav = "Not BGA member &pound;3";
		else
			bgav = "None";
		totv = pieces[4];
	}
	replacecell(pftab, 1, typev, 1);
	replacecell(pftab, 2, namev, 0);
	replacecell(pftab, 3, bgav, 1);
	replacecell(pftab, 4, "&pound;" + totv, 1);
	vform.amount.value = totv;
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
<table id="pftab">
<tr><td>Paying for</td>
<td><select name="actselect" size="0" onchange="fillinvals();">

EOT;
$total = 0;
$hadm = false;
foreach ($unpaid_teams as $team) {
	$seld = "";
	if (!$hadm)  {
		if ($team->Captain->is_same($player))  {
			$seld = " selected";
			$hadm = true;
			$total = $team->Subs;
		}
	}
	print <<<EOT
<option$seld value="T:{$team->display_name()}:{$team->Nonbga}:{$team->Subs}">
Team: {$team->display_name()}</option>

EOT;
}
foreach ($unpaid_il as $pl) {
	$seld = "";
	$nbgan = $pl->BGAmemb? 0: 1;
	if (!$hadm)  {
		if ($pl->is_same($player)) {
			$seld = " selected";
			$hadm = true;
			$total = $pl->ILsubs;
		}
	}
	print <<<EOT
<option$seld value="I:{$pl->First}:{$pl->Last}:$nbgan:{$pl->ILsubs}">
Individual: {$pl->display_name(false)}</option>

EOT;
}
print <<<EOT
</select></td></tr>
<tr><td>League</td><td>None</td></tr>
<tr><td>For</td><td>None</td></tr>
<tr><td>Surcharge</td><td>None</td></tr>
<tr><td>Total</td><td>$total</td></tr>
<tr><td colspan="2"><input type="submit" name="pay" value="Pay Subscription"></td></tr>
</table>
<input type="hidden" name="amount" value="$total">
</form>

EOT;
}
?>
</div>
</div>
</body>
</html>
