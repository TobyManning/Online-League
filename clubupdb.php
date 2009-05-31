<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
?>
<html>
<?php
$Title = "Update Clubs";
include 'php/head.php';
?>
<body>
<h1>Update Clubs</h1>
<p>Please select the club to be updated from the following list.</p>
<p>To add a club click on one at random and just change the entries on the form.</p>
<table cellpadding="1" cellspacing="1" border="0">
<tr>
<th>Abbrev</th>
<th>Name</th>
</tr>
<?php
$ret = mysql_query("select code from club order by name");
if ($ret && mysql_num_rows($ret)) {
	while ($row = mysql_fetch_assoc($ret)) {
		$p = new Club($row["code"]);
		$p->fetchdets();
		print <<<EOT
<tr>
<td><a href="updindclub.php?{$p->urlof()}">{$p->display_code()}</a></td>
<td><a href="updindclub.php?{$p->urlof()}">{$p->display_name()}</a></td>
</tr>
EOT;
	}
}
?>
</table>
</body>
</html>
