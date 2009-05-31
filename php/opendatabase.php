<?php

//  REMEMBER TO HACK THIS - Live requires username/password etc
//  ***********************************************************

if  (!mysql_connect("localhost", "apache")  ||  !mysql_select_db("bgaleague")) {

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
