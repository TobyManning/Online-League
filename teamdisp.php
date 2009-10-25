<?php
session_start();
$username = $_SESSION['user_name'];
$userpriv = $_SESSION['user_priv'];
$logged_in = strlen($username) != 0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
?>
<html>
<?php
$Title = "Team {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<h1>Team {$team->display_name()}</h1>
<p>
Team {$team->display_name()} - {$team->display_description()} - division
{$team->display_division()}
</p>
<p>
Team captain is {$team->display_captain()}.
{$team->display_capt_email($logged_in)}
</p>
EOT;
if (($userpriv == 'A' || $userpriv == 'SA') && !$team->Paid)
	print <<<EOT
<p><b>Team has not paid.</b></p>
EOT;
print <<<EOT
<h3>Members</h3>
<table class="teamdisp">
<tr>
	<th>Name</th>
	<th>Rank</th>
	<th>Club</th>
</tr>
EOT;
$membs = $team->list_members();
foreach ($membs as $m) {
	$m->fetchdets();
	$m->fetchclub();
	print <<<EOT
<tr>
	<td>{$m->display_name()}</td>
	<td>{$m->display_rank()}</td>
	<td>{$m->Club->display_name()}</td>
</tr>
EOT;
}
?>
</table>
</body>
</html>
