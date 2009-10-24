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
?>
<html>
<?php
$Title = "Players List by team";
include 'php/head.php';
?>
<body>
<h1>Players by team</h1>
<?php
// Provide for 11 columns
// Chop last 2 columns off if not logged in

$cs = $logged_in? 11: 9;
print <<<EOT
<table class="pllist">
<tr>
<th>Name</th>
<th>Rank</th>
<th>Club</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>Online</th>
EOT;
if ($logged_in)
	print <<<EOT
<th>Userid</th>
<th>Email</th>
EOT;
?>
</tr>
<?php
$tlist = list_teams();
foreach ($tlist as $team) {
	$team->fetchdets();
	print <<<EOT
<tr><th colspan=$cs align="center">{$team->display_name()}</th></tr>
EOT;
	$pl = $team->list_members();
	foreach ($pl as $m) {
		$m->fetchdets();
		$m->fetchclub();
		print <<<EOT
<tr>
<td>{$m->display_name()}</td>
<td>{$m->display_rank()}</td>
<td>{$m->Club->display_name()}</td>
<td>{$m->played_games()}</td>
<td>{$m->won_games()}</td>
<td>{$m->drawn_games()}</td>
<td>{$m->lost_games()}</td>
<td>{$m->display_online()}</td>
EOT;
		if ($logged_in)
			print <<<EOT
<td>{$m->display_userid()}</td>
<td>{$m->display_email()}</td>
EOT;
		print "</tr>\n";
	}
}
print <<<EOT
<tr><th colspan=$cs align="center">Not in a team</th></tr>
EOT;

$ret = mysql_query("select first,last from player order by last,first,rank desc");
if ($ret) {
	while ($row = mysql_fetch_assoc($ret)) {
		$p = new Player($row["first"], $row["last"]);
		if ($p->count_teams() != 0)
			continue;
		$p->fetchdets();
		$p->fetchclub();
		print <<<EOT
<tr>
<td>{$p->display_name()}</td>
<td>{$p->display_rank()}</td>
<td>{$p->Club->display_name()}</td>
<td>{$p->played_games()}</td>
<td>{$p->won_games()}</td>
<td>{$p->drawn_games()}</td>
<td>{$p->lost_games()}</td>
<td>{$p->display_online()}</td>
EOT;
		if ($logged_in)
			print <<<EOT
<td>{$p->display_userid()}</td>
<td>{$p->display_email()}</td>
EOT;
		print "</tr>\n";
	}
}
?>
</table>
</body>
</html>
