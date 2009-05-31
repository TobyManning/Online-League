<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/opendatabase.php';
try {
	$via = $_POST["via"];
	switch ($via) {
	default:
		$player = new Player();
		$player->frompost();
		$player->fetchdets();
		$name = $player->display_name();
		$dest = $player->Email;
		break;
	case "club":
		$club = new Club();
		$club->frompost();
		$club->fetchdets();
		$name = $club->display_contact();
		$dest = $club->Contactemail;
		break;
	}
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
catch (ClubException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
if (strlen($dest) == 0) {
$mess = "No email dest";
include 'php/dataerror.php';
exit(0);
}
$subj = $_POST["subject"];
$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];
$fh = popen("mail -s 'Go League email - $subj' $dest", "w");
fwrite($fh, "Please reply to $emailrep\n");
fwrite($fh, "$mess\n");
pclose($fh);
?>
<html>
<?php
$Title = "Message Sent to $name";
include 'php/head.php';
?>
<body>
<?php
print <<<EOT
<h1>Message sent to $name</h1>
<p>I think your message was sent OK to $name.</p>
EOT;
?>
<p>Please click <a href="javascript:self.close();">here</a> to close this window.</p>
</body>
</html>
