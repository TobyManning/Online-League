<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
?>
<html>
<?php
$Title = "Adjust Matches";
include 'php/head.php';
?>
<body>
<h1>Update Matches</h1>
<table class="matchupd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
</tr>
<?php
$div = $_GET['div'];
$crit = "";
if (strlen($div) != 0)
	$crit = " where divnum=$div";
$ret = mysql_query("select ind from lgmatch$crit order by divnum,matchdate,hteam,ateam");
if ($ret && mysql_num_rows($ret) > 0)  {
	$lastdiv = -99;
	while ($row = mysql_fetch_array($ret))  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		if ($mtch->Division != $lastdiv)  {
			$lastdiv = $mtch->Division;
			print "<tr><th colspan=\"3\" align=\"center\">Division $lastdiv</th></tr>\n";
		}
		print <<<EOT
<tr>
<td>{$mtch->Date->display()}</td>
EOT;
		if ($mtch->Result == 'N' || $mtch->Result == 'P') {
print <<<EOT
<td><a href="updmatch.php?{$mtch->urlof()}">{$mtch->Hteam->display_name()}</a></td>
<td><a href="updmatch.php?{$mtch->urlof()}">{$mtch->Ateam->display_name()}</a></td>
EOT;
		}
		else {
print <<<EOT
<td>{$mtch->Hteam->display_name()}</td>
<td>{$mtch->Ateam->display_name()}</td>
EOT;
		}
print <<<EOT
</tr>
EOT;
	}
}
else {
	print "<tr><td colspan=\"3\" align=\"center\">No matches yet please create some</td></tr>\n";
}
?>
</table>
</body>
</html>
