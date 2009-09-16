<?php
session_start();
$userpriv = $_SESSION['user_priv'];
?>
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

$action = substr($_POST["subm"], 0, 1);
$playname = $_POST["playname"];
$email = $_POST["email"];
$userid = $_POST["userid"];
$kgs = $_POST["kgs"];
$igs = $_POST["igs"];
$club = $_POST["club"];
$rank = $_POST["rank"];
$admin = $_POST["admin"];
$passw = $_POST["passw"];

switch ($action) {
case 'A':
	if (strlen($playname) == 0 || strlen($userid) == 0)  {
		include 'php/wrongentry.php';
		exit(0);
	}
	$player = new Player($playname);
	checkname($player);
	checkclash('user', $userid);
	checkclash('kgs', $kgs);
	checkclash('igs', $igs); 
	$player->Rank = new Rank($rank);
	$player->Club = new Club($club);
	$player->Email = $email;
	$player->KGS = $kgs;
	$player->IGS = $igs;
	// Force Admin priv to N unless Super-admin
	$player->Admin = $userpriv != 'SA'? $admin: 'N';
	$player->Userid = $userid;
	$player->create();
	// If no password specified, invent one
	if ($strlen($passw) == 0)  {
		$passw = "";
		$poss = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$lp = strlen($poss) - 1;
		for ($i = 0; $i < 8; $i++)
			$passw = $passw . $poss[rand(0,$lp)];
	}
	print "Password set to $passw\n";
	//$player->set_passwd($passw);
	$Title = "Player {$player->display_name()} created OK";
	if (strlen($email) != 0)  {
		$fh = popen("mail -s 'BGA League account created' $email", "w");
		fwrite($fh, "Please DO NOT reply to this message!!!\n\n");
		fwrite($fh, "A BGA League account has been created for you on http://league.britgo.org\n\n");
		fwrite($fh, "Your user id is $userid and your password is $passw\n\n"); 
		fwrite($fh, "Please log in and reset your password if you wish\n");
		pclose($fh);
	}
	break;
default:
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
	
	// Check name changes
	
	$newplayer = new Player($playname);
	if  (!$origplayer->is_same($newplayer))  {
		checkname($newplayer);
		$origplayer->updatename($newplayer);
	}
	
	// Check user kgs and igs clashes
	
	if ($origplayer->Userid != $userid)  {
		checkclash('user', $userid);
		$origplayer->Userid = $userid;
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
	// Leave priv alone unless super-admin
	if ($userpriv == 'SA')
		$origplayer->Admin = $admin;
	$origplayer->update();
	if (strlen($passw) != 0  &&  $passw != $origplayer->get_passwd())
		$origplayer->set_passwd($passw);
	$Title = "Player {$origplayer->display_name()} updated OK";
	break;
}
print "<html>\n";
include 'php/head.php';
print <<<EOT
<body>
<h1>$Title</h1>
<p>$Title.</p>
EOT;
?>
<p>
Click <a href="playupd.php" target="_top">here</a> to return to the player update menu.
</p>
</body>
</html>
