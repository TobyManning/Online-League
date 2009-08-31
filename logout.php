<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_priv']);
?>
<html>
<head>
<title>Logging out</title>
</head>
<body onload="document.location='linkframe.php'">
</body>
