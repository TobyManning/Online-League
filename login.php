<?php
include 'php/opendatabase.php';

$userid = $_POST['user_id'];
$passwd = $_POST['passwd'];

$quserid = mysql_real_escape_string($userid);
$ret = mysql_query("select first,last,password,admin from player where user='$quserid'");

if ($ret || mysql_num_rows($ret) == 0)  {
	print <<<EOT
<html>
<head>
<title>Unknown User</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="nomarg">
<h1>Unknown User</h1>
<p>User $userid is not known.
Please <a href="index.php" target="_top">click here</a> to return to the top.
</p>
</body>
</html>
EOT;
	exit(0);
}
$row = mysql_fetch_assoc($ret);
if ($passwd != $row['password'])  {
	print <<<EOT
<html>
<head>
<title>Incorrect Password</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="nomarg">
<h1>Incorrect Password</h1>
<p>The password is not correct.
Please <a href="index.php" target="_top">click here</a> to return to the top.
</p>
</body>
</html>
EOT;
	exit(0);
}
$username = $row['first'] . ' ' . $row['last'];
$priv = $row['admin'];
session_start();
$_SESSION['user_id'] = $userid;
$_SESSION['user_name'] = $username;
$_SESSION['user_priv'] = $priv;
setcookie("user_id", $userid, time()+60*60*24*60, "/");
setcookie("user_name", $username, time()+60*60*24*60, "/");
?>
<html>
<head>
<title>Login OK</title>
</head>
<body onload="document.location='linkframe.php'">
</body>
</html>
