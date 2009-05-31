<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/rank.php';
include 'php/player.php';
include 'php/club.php';
try {
	$via = $_GET["via"];
	switch ($via) {
	default:
		$player = new Player();
		$player->fromget();
		$name = $player->display_name();
		$hidden = $player->save_hidden();
		break;
	case "club":
		$club = new Club();
		$club->fromget();
		include 'php/opendatbase.php';
		$club->fetchdets();
		$name = $club->display_contact();
		$hidden = $club->save_hidden();
		break;
	}
}
catch (PlayerException $e) {
	include 'php/wrongentry.php';
	exit(0);
}
catch (ClubException $e) {
	include 'php/wrongentry.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Send a message to $name";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.mailform;
      if  (!nonblank(form.subject.value))  {
         alert("No subject given");
         return false;
      }
      if  (!nonblank(form.emailrep.value))  {
      	alert("No email given");
      	return false;
      }
		return true;
}
</script>
<?php
print <<<EOT
<h1>Send a message to $name</h1>
<p>Please use the form below to compose a message to $name.</p>
<form name="mailform" action="sendmail2.php" method="post" enctype="application/x-www-form-urlencoded"  onsubmit="javascript:return formvalid();">
<input type="hidden" name="via" value="$via">
$hidden
EOT;
?>
<p>Subject:<input type="text" name="subject"></p>
<p>Reply to:<input type="text" name="emailrep"></p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>
</body>
</html>
