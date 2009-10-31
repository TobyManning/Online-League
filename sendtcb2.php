<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/opendatabase.php';

$subj = $_POST["subject"];
$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];
$admins = $_POST["admintoo"];
$cc = $_POST["ccto"];
$tlist = list_teams();
$mlist = array();
foreach ($tlist as $team) {
	$team->fetchdets();
	if (strlen($team->Captain->Email) != 0)
		$mlist[$team->Captain->Email] = 1;
}
if (strlen($cc) != 0) {
	foreach (preg_split("/[\s,]+/", $cc) as $m)
		$mlist[$m] = 1;
}
if ($admins) {
	$la = list_admins();
	foreach ($la as $p)
		if (strlen($p->Email) != 0)
			$mlist[$p->Email] = 1;
}
foreach (array_keys($mlist) as $dest) {
	$fh = popen("mail -s 'Go League email - $subj' $dest", "w");
	fwrite($fh, "Please reply to $emailrep\n");
	fwrite($fh, "$mess\n");
	pclose($fh);
}
?>
<html>
<?php
$Title = "Message Sent to team captains";
include 'php/head.php';
?>
<body>
<h1>Message sent to team captains</h1>
<p>I think your message was sent OK to team captains.</p>
</body>
</html>
