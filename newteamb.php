<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
$team = new Team();
$Title = "New Team";
include 'php/head.php';
?>
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
<h1>Create New Team</h1>
<p>Please set up the details of the team as required using the form below.</p>
<p>You can set up the team members once the team has been created.</p>
<form name="teamform" action="updindteam2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>
Team Name:
<input type="text" name="teamname" size=20>
Full Name:
<input type="text" name="teamdescr" size=40>
</p>
<p>
Division:
<?php
$team->divopt();
print "Captain:";
$team->captainopt();
?>
</p>
<p>
<input type="submit" name="subm" value="Add Team">
</p>
</form>
</body>
</html>
