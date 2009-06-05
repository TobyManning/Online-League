<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$Title = "Update Team {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.teamform;
      if  (!nonblank(form.teamname.value))  {
         alert("No team name given");
         return false;
      }
      if  (!nonblank(form.teamdescr.value))  {
         alert("No team description given");
         return false;
      }
		return true;
}
</script>
<h1>Update Team {$team->display_name()}</h1>
<p>Please update the details of the team as required using the form below.</p>
<p>Alternatively <a href="delteam.php?{$team->urlof()}">Click here</a> to remove
details of the team.</p>
<p>To update the team members, <a href="updtmemb.php?{$team->urlof()}">Click here</a>.</p>
EOT;
?>
<p>To enter a new team, just fill in the fields appropriately and press the "Add team" button.
</p>
<?php
print <<<EOT
<form name="teamform" action="updindteam2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$team->save_hidden()}
<p>
Team Name:
<input type="text" name="teamname" value="{$team->display_name()}" size=20>
Full Name:
<input type="text" name="teamdescr" value="{$team->display_description()}" size=40>
</p><p>
Division:
EOT;
$team->divopt();
print "Captain:";
$team->captainopt();
?>
</p>
<p>
<input type="submit" name="subm" value="Add Team">
<input type="submit" name="subm" value="Update Team">
</p>
</form>
</body>
</html>
