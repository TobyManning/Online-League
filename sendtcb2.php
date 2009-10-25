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
$tlist = list_teams();
$destarr = array();
foreach ($tlist as $team) {
	$team->fetchdets();
	if (strlen($team->Captain->Email) != 0)
		array_push($destarr, $team->Captain->Email);
}
$dest = implode(' ', $destarr);
if (strlen($dest) != 0)  {
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
