<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';

function checkclash($code) {
	$club = new Club($code);
	$ret = mysql_query("select code from club where {$club->queryof()}");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$column = "code";
		$value = $code;
		include 'php/nameclash.php';
		exit(0);
	}
}

$action = substr($_POST["subm"], 0, 1);
$newcode = $_POST["clubcode"];
$newname = $_POST["clubname"];
$contname = $_POST["contname"];
$contphone = $_POST["contphone"];
$contemail = $_POST["contemail"];
$website = $_POST["website"];
$night = $_POST["night"];

if (preg_match("/(.*)\s+(.*)/", $contname, $matches)) {
	$contfirst = $matches[1];
	$contlast = $matches[2];
}
else  {
	$contfirst = "";
	$contlast = "";
}
if  (preg_match("/^http:\/\/(.*)/", $website, $matches))  {
	$website = $matches[1];
}

switch ($action) {
case 'A':
	if (strlen($newcode) == 0)  {
		include 'php/wrongentry.php';
		exit(0);
	}
	checkclash($newcode);
	$club = new Club($newcode);
	$club->Name = $newname;
	$club->Contactfirst = $contfirst;
	$club->Contactlast = $contlast;
	$club->Contactemail = $contemail;
	$club->Contactphone = $contphone;
	$club->Website = $website;
	$club->Night = $night;
	$club->create();
	$Title = "Club {$club->display_name()} created OK";
	break;	
default:
	try {
		$club = new Club();
		$club->frompost();
		$club->fetchdets();
	}
	catch (ClubException $e) {
		include 'php/wrongentry.php';
		exit(0);
	}	
	// If name has changed, check it doesn't clash
	if ($newcode != $club->Code)  {
		checkclash($newcode);
		$qcode = mysql_real_escape_string($newcode);
		mysql_query("update club set code='$qcode' where {$club->queryof()}");
		$club->Code = $newcode;
	}
	$club->Name = $newname;
	$club->Contactfirst = $contfirst;
	$club->Contactlast = $contlast;
	$club->Contactemail = $contemail;
	$club->Contactphone = $contphone;
	$club->Website = $website;
	$club->Night = $night;
	$club->update();
	$Title = "Club {$club->display_name()} updated OK";
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
Click <a href="clubupd.php" target="_top">here</a> to return to the club update menu.
</p>
</body>
</html>
