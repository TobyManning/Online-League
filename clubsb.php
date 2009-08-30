<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Clubs";
include 'php/head.php';
include 'php/club.php';
?>
<body>
<h1>Clubs</h1>
<table border="0">
<tr>
<th>Abbrev</th>
<th>Name</th>
<th>Contact</th>
<th>Phone</th>
<th>Email</th>
<th>Website</th>
<th>Night</th>
</tr>
<?php
mysql_connect("localhost", "apache");
mysql_select_db("bgaleague");
$ret = mysql_query("select code from club order by name");
if ($ret && mysql_num_rows($ret)) {
	while ($row = mysql_fetch_assoc($ret)) {
		$p = new Club($row["code"]);
		$p->fetchdets();
		print <<<EOT
<tr>
<td class="clublist">{$p->display_code()}</td>
<td class="clublist">{$p->display_name()}</td>
<td class="clublist">{$p->display_contact()}</td>
<td class="clublist">{$p->display_contphone()}</td>
<td class="clublist">{$p->display_contemail()}</td>
<td class="clublist">{$p->display_website()}</td>
<td class="clublist">{$p->display_night()}</td>
</tr>
EOT;
	}
}
?>
</table>
</body>
</html>
