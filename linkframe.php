<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "British Go Association League";
include 'php/head.php';
?>
<body class="nomarg">
<h2>Places</h2>
<?php
$a = $_GET["abs"];
if ($a != "")
	$a = "/league/";
$adm = $_GET["adm"];
print <<<EOT
<table>
<tr><td><a href="${a}index.php" target="_top">Home</a></td></tr>
<tr><td><a href="http://www.britgo.org" target="_top">BGA Home</a></td></tr>
<tr><td><a href="${a}aboutus.php" target="_top">About The League</a></td></tr>
<tr><td><a href="${a}clubs.php" target="_top">Clubs</a></td></tr>
<tr><td><a href="${a}teams.php" target="_top">Teams</a></td></tr>
<tr><td><a href="${a}players.php" target="_top">Players</a></td></tr>
<tr><td class="subind"><a href="${a}players.php?by=club" target="_top">By club</a></td></tr>
<tr><td class="subind"><a href="${a}players.php?by=rank" target="_top">By rank</a></td></tr>
<tr><td class="subind"><a href="${a}players.php?by=clubrank" target="_top">By club/rank</a></td></tr>
<tr><td><a href="${a}matches.php" target="_top">Matches</a></td></tr>
<tr><td><a href="${a}results.php" target="_top">Results</a></td></tr>
<tr><td><a href="${a}league.php" target="_top">League</a></td></tr>
<tr><td><a href="${a}admin.php" target="_top" class="memb">Admin menu</a></td></tr>
EOT;
if (strlen($adm) != 0) {
	print <<<EOT
	<tr><td class="subind"><a href="${a}newclub.php" target="_top" class="memb">New club</a></td></tr>
	<tr><td class="subind"><a href="${a}clubupd.php" target="_top" class="memb">Update clubs</a></td></tr>
	<tr><td class="subind"><a href="${a}playupd.php" target="_top" class="memb">Update players</a></td></tr>
	<tr><td class="subind"><a href="${a}teamsupd.php" target="_top" class="memb">Update teams</a></td></tr>
	<tr><td class="subind"><a href="${a}matchupd.php" target="_top" class="memb">Update matches</a></td></tr>
EOT;
}
?>
</table>
</body>
</html>
