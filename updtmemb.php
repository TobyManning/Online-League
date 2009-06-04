<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	include 'php/wrongentry.php';
	exit(0);
}
$Playerlist = list_players("club,rank desc,last,first");
$Elist = $team->list_members();
$Title = "Update Team Members for {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<script language="javascript">
var playerlist = new Array();
var currteam = new Array();
EOT;
foreach ($Playerlist as $player) {
	$player->fetchdets();
	print <<<EOT
playerlist.push({first:"{$player->display_first()}", last:"{$player->display_last()}",
rank:"{$player->display_rank()}", club:"{$player->Club->display_name()}"});
EOT;
}
foreach ($Elist as $ep) {
	$ep->fetchdets();
	print <<<EOT
currteam.push({first:"{$ep->display_first()}", last:"{$ep->display_last()}",
rank:"{$ep->display_rank()}", club:"{$ep->Club->display_name()}"});
EOT;
}
print <<<EOT
var changes = 0;
</script>
<h1>Update Team Members for {$team->display_name()}</h1>
EOT;
?>
<p>
This is the current team. To add a player to the team click here. To remove a player click
del against the player.
</p>
<table cellpadding="2" cellspacing="2" border="0">
<thead>
<tr>
<th>Name</th>
<th>Rank</th>
<th>Club</th>
<th>Del</th>
</tr>
</thead>
<tbody id="membbody">
<?php
foreach ($Elist as $ep) {
	print <<<EOT
<tr>
<td>{$ep->display_name()}</td>
<td>{$ep->display_rank()}</td>
<td>{$ep->Club->display_name()}</td>
<td>del</td>
</tr>
EOT;
}
?>
</tbody>
</table>
<p>When done click here.</p>
<p id="changepara">There are no changes at present.</p>
</body>
</html>
