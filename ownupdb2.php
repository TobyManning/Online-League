<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = mysql_real_escape_string($value);
	$ret = mysql_query("select $column from player where $column='$qvalue'");
	if ($ret && mysql_num_rows($ret) != 0)  {
		include 'php/nameclash.php';
		exit(0);
	}
}

function checkname($newplayer) {
	$ret = mysql_query("select first,last from player where {$newplayer->queryof()}");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$column = "name";
		$value = $newplayer->display_name();
		include 'php/nameclash.php';
		exit(0);
	}
}

$playname = $_POST["playname"];
$email = $_POST["email"];
$kgs = $_POST["kgs"];
$igs = $_POST["igs"];
$club = $_POST["club"];
$rank = $_POST["rank"];
$passw = $_POST["passw"];

try {
	$origplayer = new Player();
	$origplayer->frompost();
	$origplayer->fetchdets();
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
	
// Check name changes and perform update if applicable
// Note that the "updatename" function does any consequent
// updates like changing team captain name if the player is a
// team captain.

$chname = false;
$newplayer = new Player($playname);
if  (!$origplayer->is_same($newplayer))  {
	checkname($newplayer);
	$origplayer->updatename($newplayer);
	$chname = true;
}
	
if ($origplayer->KGS != $kgs) {
	checkclash("kgs", $kgs);
	$origplayer->KGS = $kgs;
}
if ($origplayer->IGS != $igs) {
	checkclash("igs", $igs);
	$origplayer->IGS = $igs;
}
	
$origplayer->Rank = new Rank($rank);
$origplayer->Club = new Club($club);
$origplayer->Email = $email;
$origplayer->update();
if (strlen($passw) != 0  &&  $passw != $origplayer->get_passwd())
	$origplayer->set_passwd($passw);
$Title = "Player details updated OK";
print "<html>\n";
include 'php/head.php';
print <<<EOT
<body>
<h1>$Title</h1>
<p>$Title.</p>
EOT;
if ($chname)
	print <<<EOT
<p>As you changed your name, you should probably logout and log back in again using the
menu on the left. This will reset any "cookies" with your original name in.
</p>
EOT;
?>
<p>
Click <a href="playupd.php" target="_top">here</a> to return to the player update menu.
</p>
</body>
</html>
