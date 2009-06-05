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
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

// Slurp up player names until we can't find any more

$membs = array();

for ($i = 0;; $i++)  {
	try  {
		$m = new TeamMemb($team);
		$m->fromget("tm$i", true);
		$m->fetchdets();
		array_push($membs, $m);
	}
	catch (PlayerException $e) {
		break;
	}
}

try {
	// Delete existing members
	del_team_membs($team);
	// Add new team members
	foreach ($membs as $m) {
		$m->create();
	}
}
catch (TeamMembException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);
}

$Title = "Updated Team Members for {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<h1>Update of {$team->display_name()} complete</h1>
<p>
Updating team members for {$team->display_description()} is complete.
</p>
EOT;
?>
<p>
Click <a href="teamsupdb.php">here</a> to resume editing teams.
</p>
</body>
</html>
