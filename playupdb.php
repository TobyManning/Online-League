<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/player.php';
?>
<html>
<?php
$Title = "Update Players";
include 'php/head.php';
?>
<body>
<h1>Update Players</h1>
<p>Please select the player to be updated from the following list.</p>
<p>To add a new player click on one at random and just change the entries on the form.</p>
<table cellpadding="1" cellspacing="2" border="0">
<?php
$playerlist = list_players();
$countplayers = count($playerlist);
$rows = ($countplayers + 3) / 4;
for ($row = 0; $row < $rows; $row++) {
	print "<tr>\n";
	for ($col = 0; $col < 4;  $col++)  {
		$ind = $row + $col * $rows;
		print "<td>";
		if ($ind >= $countplayers)
			print "&nbsp;";
		else {
			$pl = $playerlist[$ind];
			print "<a href=\"updindplayer.php?{$pl->urlof()}\">{$pl->display_name()}</a>";		
		}
		print "</td>";
	}
	print "</tr>\n";
}
?>
</table>
</body>
</html>
