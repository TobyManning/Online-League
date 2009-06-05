<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
?>
<html>
<?php
$Title = "Teams";
include 'php/head.php';
?>
<body>
<h1>Teams</h1>
<table cellpadding="2" cellspacing="5" border="0">
<tr>
	<th>Name</th>
	<th>Full Name</th>
	<th>Division</th>
	<th>Captain</th>
	<th>Members</th>
</tr>
<?php
$teamlist = list_teams();
foreach ($teamlist as $team) {
	$team->fetchdets();
	print <<<EOT
<tr>
	<td><a href="teamdisp.php?{$team->urlof()}">{$team->display_name()}</a></td>
	<td>{$team->display_description()}</td>
	<td>{$team->display_division()}</td>
	<td>{$team->display_captain()}</td>
	<td>{$team->count_members()}</td>
</tr>
EOT;
}
?>
</table>
</body>
</html>
