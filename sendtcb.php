<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Send a message to team captains";
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
<h1>Send a message to team captains</h1>
<p>Please use the form below to compose a message to all team captains.</p>
<form name="mailform" action="sendtcb2.php" method="post" enctype="application/x-www-form-urlencoded"  onsubmit="javascript:return formvalid();">
<p>Subject:<input type="text" name="subject"></p>
<p>Reply to:<input type="text" name="emailrep"></p>
<p>CC to:<input type="text" name="ccto"> (comma or space sep)</p>
<p><input type="checkbox" name="admintoo">Mail admins too</p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>
</body>
</html>
