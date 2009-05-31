<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Players";
include 'php/head.php';
$by = $_GET["by"];
print <<<EOT
<frameset cols="15%,*">
<frame src="linkframe.php?edres=y" frameborder="0" scrolling="no" marginwidth="0" marginheight="0">
<frame src="playersb.php?by=$by" frameborder="0" scrolling="auto" marginwidth="0" marginheight="0">
</frameset>
EOT;
?>
</html>
