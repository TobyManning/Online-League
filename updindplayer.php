<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
//   Copyright 2009 John Collins

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

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
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$Title = "Update Player {$player->display_name(false)}";
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
      if  (!nonblank(form.userid.value))  {
         alert("No userid given");
         return false;
      }
		return true;
}
</script>
<h1>Update Player {$player->display_name(false)}</h1>
<p>Please update the details of the player as required using the form below.</p>
<p>Alternatively <a href="delplayer.php?{$player->urlof()}">Click here</a> to remove
details of the player.</p>
EOT;
?>
<p>To enter a new player, you can adjust the fields appropriately
and press the "Add player" button.
</p>
<?php
print <<<EOT
<form name="playform" action="updindplayer2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$player->save_hidden()}
<p>
Player Name:
<input type="text" name="playname" value="{$player->display_name(false)}">
Club:
EOT;
$player->clubopt();
print "Rank:";
$player->rankopt();
// Try to avoid Firefox guessing userid based on the last thing we typed if not there.
$du = $player->display_userid(0);
$dp = $player->disp_passwd();
if (strlen($du) != 0)
	$du = " value=\"" . $du . "\"";
if (strlen($dp) != 0)
	$dp = " value=\"" . $dp . "\"";
$okemch = $player->OKemail?" checked": "";
print <<<EOT
</p>
<p>
Email:<input type="text" name="email" value="{$player->display_email_nolink()}">
Phone:<input type="text" name="phone" size=30 value="{$player->display_phone()}">
</p>
<p>
<input type="checkbox" name="okem"$okemch>
Check if player is happy to accept emails about matches to be played
</p>
<p>
Userid:<input type="text" name="userid"$du>
Password:<input type="password" name="passw"$dp>
</p>
<p>
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
