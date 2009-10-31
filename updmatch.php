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
			alert("No team 2 player " + (n+1) + " selected");
			return false;
		}
		opt = el.options;
		aplayers[n] = opt[el.selectedIndex].value;
	}
	for (var p1 = 0;  p1 < 2; p1++)  {
		for (var p2 = p1 + 1; p2 < 3; p2++) {
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
<?php

// Load members of team and at the same time check we've got enough

function checkteam($team) {
	$result = $team->list_members();
	if (count($result) < 3)  {
		print <<<EOT
<h1>Edit Match</h1>
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

$Htmemb = checkteam($mtch->Hteam);
$Atmemb = checkteam($mtch->Ateam);
?>
<h1>Edit Match</h1>
<?php
// Output select team member and if recorded display W or B

function selectmemb($ha, $n, $mch, $team, $membs) {
	$colour = 0;
	$matchm = false;
	if (count($mch->Games) > $n)  {
		$g = $mch->Games[$n];
		
		//  We might have half-allocated teams in which case
		//  the White or Black team is not defined
		
		if ($g->Wteam && $g->Wteam->is_same($team))  {
			$matchm = $g->Wplayer;
			$colour = 1;
		}
		elseif ($g->Bteam)  {
			$matchm = $g->Bplayer;
			$colour = 2;
		}
		$readonly = $g->Result != 'N';
	}
	print "<td>\n";

	// readonly will only be set if result is not "N" which
	// will only be if the colours are assigned and $matchm
	// will be set
	 
	if ($readonly)
		print $matchm->display_name();
	else  {
		print <<<EOT
<select name="$ha$n">
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
EOT;
	}
	print "</td>\n";
	return $colour;	// 0 nigiri 1 white 2 black
}

print <<<EOT
<p>
This match is between
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()}).
</p>
<form name="matchform" action="updmatch2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkteamsvalid()">
{$mtch->save_hidden()}
<p>
EOT;
$mtch->Date->dateopt("Date set for");
print "with";
$mtch->slackdopt();
?>
days to play the games.</p>
<table class="updmatch">
<tr><th colspan="3" align="center">Player assignments</th></tr>
<?php
print <<<EOT
<tr>
<th align="center">{$mtch->Hteam->display_name()}</th>
<th align="center">Colours</th>
<th align="center">{$mtch->Ateam->display_name()}</th>
</tr>
EOT;
$cols = array("Nigiri", "White-Black", "Black-White");
for ($row = 0; $row < 3; $row++)  {
	print "<tr>\n";
	$col = selectmemb("htm", $row, $mtch, $mtch->Hteam, $Htmemb);
	print "<td><select name=\"colours$row\">\n";
	for ($c = 0;  $c < 3;  $c++)  {
		$s = $c == $col? " selected": "";
		print "<option$s value=$c>$cols[$c]</option>\n";
	}
	print "</select></td>\n";
	selectmemb("atm", $row, $mtch, $mtch->Ateam, $Atmemb);		
	print "</tr>\n";
}
?>
</table>
<p>
Make any adjustments and
<input type="submit" value="Click here"> or <input type="reset" value="Reset form">
</p>
<p>
Set colours to "Nigiri" throughout to randomly pick a colour which will be assigned to boards
1 and 3 and the opposite to board 2.
</p>
</form>
</body>
</html>
