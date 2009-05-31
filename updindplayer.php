<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
	$player->fetchclub();
}
catch (PlayerException $e) {
	include 'php/wrongentry.php';
	exit(0);
}
$Title = "Update Player {$player->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.playform;
      if  (!nonblank(form.playname.value))  {
         alert("No player name given");
         return false;
      }
		return true;
}
</script>
<h1>Update Player {$player->display_name()}</h1>
<p>Please update the details of the player as required using the form below.</p>
<p>Alternatively <a href="delplayer.php?{$player->urlof()}">Click here</a> to remove
details of the player.</p>
EOT;
?>
<p>To enter a new player, just fill in the fields appropriately and press the "Add player" button.
</p>
<?php
print <<<EOT
<form name="playform" action="updindplayer2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$player->save_hidden()}
<p>
Player Name:
<input type="text" name="playname" value="{$player->display_name()}">
Club:
EOT;
$player->clubopt();
print "Rank:";
$player->rankopt();
print <<<EOT
</p>
<p>
Email:<input type="text" name="email" value="{$player->display_email_nolink()}">
Userid:<input type="text" name="userid" value="{$player->display_userid(0)}">
KGS:<input type="text" name="kgs" value="{$player->display_kgs()}" size="10" maxlength="10">
IGS:<input type="text" name="igs" value="{$player->display_igs()}" size="10" maxlength="10">
</p>
<p>
Admin Privs:
EOT;
$player->adminopt();
?>
</p>
<p>
<input type="submit" name="subm" value="Add Player">
<input type="submit" name="subm" value="Update Player">
</p>
</form>
</body>
</html>
