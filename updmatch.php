<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
?>
<html>
<?php
$Title = "Edit Match";
include 'php/head.php';
?>
<body>
<script language="javascript">
function checkteamsvalid() {
	var 	form = document.matchform;
	var	hplayers = new Array(3);
	var	aplayers = new Array(3);
	for (var n = 0;  n < 3;  n++)  {
		var el = form["htm" + n];
		if (el.selectedIndex <= 0)  {
			alert("No team 1 player " + (n+1) + " selected");
			return false;
		}
		var opt = el.options;
		hplayers[n] = opt[el.selectedIndex].value;
		el = form["atm" + n];
		if (el.selectedIndex <= 0)  {
			alert("No team 2 player " + n + " selected");
			return false;
		}
		opt = el.options;
		aplayers[n] = opt[el.selectedIndex].value;
	}
	for (var p1 = 0;  p1 < 2; p1++)  {
		for (var p2 = p+1; p2 < 3; p2++) {
			if (hplayers[p1] == hplayers[p2])  {
				alert("Team 1 players " + (p1+1) + " and " + (p2+1) + " are the same");
				return false;
			}
			if (aplayers[p1] == aplayers[p2])  {
				alert("Team 2 players " + (p1+1) + " and " + (p2+1) + " are the same");
				return false;
			}
		}
	}
	return true;		
}
</script>
<h1>Edit Match</h1>
<?php

// Load members of team and at the same time check we've got enough

function checkteam($team) {
	$result = $team->list_members();
	if (count($result) < 3)  {
		print <<<EOT
<p>
Sorry but there are not enough members in {$team->display_name()} yet to
make up a match with.
</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back
or <a href="teamsupd.php" target="_top">here</a> to update teams.</p>
</body>
</html>
EOT;
		exit(0);
	}
	foreach ($result as $p) {
		$p->fetchdets();
	}
	return $result;
}

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

$Htmemb = checkteam($mtch->Hteam);
$Atmemb = checkteam($mtch->Ateam);

// Output select team member and if recorded display W or B

function selectmemb($ha, $n, $mch, $team, $membs) {
	$colour = "-";
	$matchm = false;
	if (count($mch->Games) > $n)  {
		$g = $mch->Games[$n];
		if ($g->Wteam->is_same($team))  {
			$matchm = $g->Wplayer;
			$colour = "White";
		}
		else  {
			$matchm = $g->Bplayer;
			$colour = "Black";
		}
	}
	print <<<EOT
<td>
<select name="$ha$n" size="0">
<option value="-">-</option>
EOT;
	foreach ($membs as $memb) {
		$val = $memb->selof();
		if ($matchm && $matchm->is_same($memb))
			print <<<EOT
<option value="$val" selected>
EOT;
		else
			print <<<EOT
<option value="$val">
EOT;
		print <<<EOT
{$memb->display_name()} ({$memb->display_rank()})
</option>
EOT;
	}
	print <<<EOT
</select>
</td>
<td>$colour</td>
EOT;
}

print <<<EOT
<p>
This match is between
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()}).
</p>
<form name="matchform" action="updmatchdate.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:checkteamsvalid()">
{$mtch->save_hidden()}
<p>
EOT;
$mtch->Date->dateopt("Date set for");
print "with";
$mtch->slackdopt();
?>
days to play the games.</p>
<table>
<tr><th colspan="4" align="center">Player assignments</th></tr>
<?php
print <<<EOT
<tr>
<th colspan="2" align="center">{$mtch->Hteam->display_name()}</th>
<th colspan="2" align="center">{$mtch->Ateam->display_name()}</th>
</tr>
EOT;
for ($row = 0; $row < 3; $row++)  {
	print "<tr>\n";
	selectmemb("htm", $row, $mtch, $mtch->Hteam, $Htmemb);
	selectmemb("atm", $row, $mtch, $mtch->Ateam, $Atmemb);		
	print "</tr>\n";
}
?>
</table>
<p>
Make any adjustments and
<input type="submit" value="Click here">
</p>
</form>
</body>
</html>
