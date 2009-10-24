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
<table class="teamsb">
<tr>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
	<th>Members</th>
	<th>Email</th>
</tr>
<?php
$teamlist = list_teams(0, "divnum,name");
$lastdiv = -199;
foreach ($teamlist as $team) {
	$team->fetchdets();
	if ($team->Division != $lastdiv) {
		$lastdiv = $team->Division;
		print <<<EOT
<tr><th colspan="4" align="center">Division {$team->display_division()}</th></tr>
EOT;
	}
	print <<<EOT
<tr>
	<td><a href="teamdisp.php?{$team->urlof()}">{$team->display_name()}</a></td>
	<td>{$team->display_description()}</td>
	<td>{$team->display_captain()}</td>
	<td>{$team->count_members()}</td>
	<td>{$team->display_capt_email()}</td>
</tr>
EOT;
}
?>
</table>
</body>
</html>
