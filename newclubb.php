<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
$club = new Club();
$Title = "New Club";
include 'php/head.php';
?>
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
<h1>Create New Club</h1>
<p>Please set up the details of the club as required using the form below.</p>
<form name="clubform" action="updindclub2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>
Club Code:
<input type="text" name="clubcode" size="3" maxlength="3">
Name:
<input type="text" name="clubname">
</p>
<p>
Contact:<input type="text" name="contname">
Phone:<input type="text" name="contphone">
Email:<input type="text" name="contemail">
</p>
<p>
Club website:
<input type="text" name="website">
Meeting night:
<?php
$club->nightopt();
?>
</p>
<p>
<input type="submit" name="subm" value="Add Club">
</p>
</form>
</body>
</html>
