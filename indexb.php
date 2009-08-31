<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "British Go Association League";
include 'php/head.php';
?>
<body>
<h1>British Go Association League</h1>
<div align="center"><img src="images/go2.jpeg" width="500" height="375" border="0" alt="Go Board Picture" align="middle"></div>
<p>Welcome to the new British Go Association League Website.
</p>
<p>This site is mostly complete apart from a page which needs writing by someone else.
</p>
<?php
if (preg_match("/^62\.253\.222\.[0-9]*/", $_SERVER["REMOTE_ADDR"])) {
	print "<p>(Internal visitor not counted)</p>\n";
}
else {
	$dr = $_SERVER["DOCUMENT_ROOT"];
	$Vc = "$dr/league/Visitors.count";
	$Count = file_get_contents($Vc);
	$fc = number_format($Count);
	print "<p>You might like to know that you are visitor number <strong>$fc</strong> to our site</p>\n";
	$Count = $Count + 1;
	$h = fopen($Vc, "w");
	fwrite($h, (string) $Count);
	fclose($h);
}
?>
</body>
</html>
