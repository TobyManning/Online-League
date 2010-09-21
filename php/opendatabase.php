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


//  REMEMBER TO HACK THIS - Live requires username/password etc
//  ***********************************************************

//  ALSO MAKE SURE that magic_quotes_gpc is turned off in your php init!!!!
//  **********************************************************************

// Can't use "gethostname" function don't know why

$gh = popen('hostname', 'r');
$servername = fread($gh, 1024);
pclose($gh);
$servername = rtrim($servername);

if (preg_match('/baduk/', $servername))  {
	$dbserver = "localhost";
	$dbuser = "www-data";
	$dbpassw = "BGA league access";
	$dbname = "bgaleague";
}
else  {
	$dbserver = "db48c.pair.com";
	$dbuser = "maproom_4";
	$dbpassw = "QeWwhsLj";
	$dbname = "maproom_bgaleague";
}

if  (!mysql_connect($dbserver, $dbuser, $dbpassw)  ||  !mysql_select_db($dbname)) {

	$mess = mysql_error();

print <<<EOT
<html>
<head>
<title>Database error</title>
</head>
<body>
<h1>Database Error</h1>
<p>Sorry but there has been a database error - cannot proceed.
Database error message was $mess.
</p>
<p>
Please tell John Collins
<a href="mailto:jmc@xisl.com">jmc@xisl.com</a> about this ASAP, and advise the context
wherein it occurred. Thank you.
</p>
</body>
</html>
EOT;
	exit(0);
}
?>
