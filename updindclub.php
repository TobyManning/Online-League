<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
try {
	$club = new Club();
	$club->fromget();
	$club->fetchdets();
}
catch (ClubException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$Title = "Update Club {$club->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.clubform;
      if  (!nonblank(form.clubcode.value))  {
         alert("No club code given");
         return false;
      }
      if  (!nonblank(form.clubname.value))  {
      	alert("No club name given");
      	return false;
      }
		return true;
}
</script>
<h1>Update Club {$club->display_name()}</h1>
<p>Please update the details of the club as required using the form below.</p>
<p>Alternatively <a href="delclub.php?{$club->urlof()}">Click here</a> to remove
details of the club.</p>
EOT;
?>
<p>To enter a new club, just fill in the fields appropriately and press the "Add club" button.
</p>
<?php
print <<<EOT
<form name="clubform" action="updindclub2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$club->save_hidden()}
<p>
Club Code:
<input type="text" name="clubcode" value="{$club->display_code()}" size="3" maxlength="3">
Name:
<input type="text" name="clubname" value="{$club->display_name()}">
</p>
<p>
Contact:<input type="text" name="contname" value="{$club->display_contact()}">
Phone:<input type="text" name="contphone" value="{$club->display_contphone()}">
Email:<input type="text" name="contemail" value="{$club->display_contemail_nolink()}">
</p>
<p>
Club website:
<input type="text" name="website" value="{$club->display_website_raw()}">
Meeting night:
EOT;
$club->nightopt();
print <<<EOT
</p>
<p>
<input type="submit" name="subm" value="Add Club">
<input type="submit" name="subm" value="Update Club">
</p>
EOT;
?>
</form>
</body>
</html>
