<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/team.php';
?>
<html>
<?php
$Title = "Update Teams";
include 'php/head.php';
?>
<body>
<h1>Update Teams</h1>
<p>Please select the team to be updated from the following list.</p>
<p>To add a new team click on one at random and just change the entries on the form.</p>
<table cellpadding="1" cellspacing="2" border="0">
<?php
$teamlist = list_teams();
$countteams = count($teamlist);
$rows = ($countteams + 3) / 4;
for ($row = 0; $row < $rows; $row++) {
	print "<tr>\n";
	for ($col = 0; $col < 4;  $col++)  {
		$ind = $row + $col * $rows;
		print "<td>";
		if ($ind >= $countteams)
			print "&nbsp;";
		else {
			$tm = $teamlist[$ind];
			print "<a href=\"updindteam.php?{$tm->urlof()}\">{$tm->display_name()}</a>";		
		}
		print "</td>";
	}
	print "</tr>\n";
}
?>
</table>
</body>
</html>
