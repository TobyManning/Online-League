<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
//   Copyright 2010 John Collins

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
include 'php/matchdate.php';
include 'php/news.php';
?>
<html>
<?php
$Title = "News items";
include 'php/head.php';
?>
<body>
<h1>News Items</h1>
<p>The following events have taken place recently - please watch this space!</p>
<table>
<tr>
<th>Date</th>
<th>Userid</th>
<th>Item</th>
</tr>
<?php
$ret = mysql_query("select ndate,user,item  from news order by ndate desc");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_assoc($ret))  {
		$n = new News();
		$n->fromrow($row);
		print <<<EOT
<tr>
<td valign="top">{$n->display_date()}</td>
<td valign="top">{$n->display_user()}</td>
<td>{$n->display_item()}</td>
</tr>

EOT;
	}
}
?>
</table>
</body>
</html>
