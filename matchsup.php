<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/matchdate.php';
$div = $_GET["div"];
if (strlen($div) == 0) {
	include 'php/wrongentry.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Initialise Matches for division $div";
include 'php/head.php';
$md = new Matchdate();
//  Initialise to start of season which we say is September
$md->set_season(9);
print <<<EOT
<body>
<h1>Initialise Matches for division $div</h1>
<form action="matchinit.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="div" value="$div">
<p>
{$md->dateopt('Starting date')}
</p>
EOT;
?>
<p>
<button name="Generate" value="Generate Matches" type="submit"></button>
</p>
</form>
<p>Click <a href="javascript:self.close()">here</a> to close this window.</p>
</body>
</html>
