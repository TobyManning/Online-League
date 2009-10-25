<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
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
$Title = "Update Paid status for Team {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<h1>Update Paid for Team {$team->display_name()}</h1>
EOT;
if ($team->Paid) {
	print <<<EOT
<p>
Team {$team->display_name()} was previously marked as paid but setting to <b>unpaid</b>.
</p>
EOT;
	$v = false;
}
else {
	print <<<EOT
<p>
Team {$team->display_name()} was previously marked as unpaid.
Now setting to <b>paid</b>.
</p>
EOT;
	$v = true;
}
$team->setpaid($v);
?>
<p>
Click <a href="teamsupd.php" target="_top">here</a> to return to the team update menu.
</p>
</body>
</html>
