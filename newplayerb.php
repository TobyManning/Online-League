<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
$player = new Player();
$Title = "New Player";
include 'php/head.php';
?>
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
<h1>Add Player</h1>
<p>Please set up the details of the player as required using the form below.</p>
<form name="playform" action="updindplayer2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>
Player Name:
<input type="text" name="playname">
Club:
<?php
$player->clubopt();
print "Rank:";
$player->rankopt();
?>
</p>
<p>
Email:<input type="text" name="email">
Userid:<input type="text" name="userid">
Password:<input type="password" name="passw">
KGS:<input type="text" name="kgs" size="10" maxlength="10">
IGS:<input type="text" name="igs" size="10" maxlength="10">
</p>
<p>
Admin Privs:
<?php
$player->adminopt();
?>
</p>
<p>
<input type="submit" name="subm" value="Add Player">
</p>
</form>
</body>
</html>
