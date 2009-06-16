<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
?>
<html>
<?php
$Title = "Update Matches";
include 'php/head.php';
?>
<body>
<script language="javascript">
var miwind = null;

function killwind() {
	if (miwind) {
		miwind.close();
		miwind = null;
	}
}

function initmatches(div) {
	killwind();
	miwind = window.open("matchsup.php?div="+div, "Initialise Matches", "width=450,height=400,resizeable=yes,scrollbars=yes");
}

function okdel(div) {
	killwind();
	if (confirm("OK to delete matches for Division " + div))
		location = "matchesdel.php?div=" + div;
}
</script>
<h1>Update Matches</h1>
<p>
Use this page to allocate matches and assign players. 
</p>
<?php
$maxdiv = max_division();
for ($div = 1;  $div <= $maxdiv;  $div++)  {
	print <<<EOT
<h2>Division $div</h2>
EOT;
	$nm = count_matches_for($div);
	if ($nm == 0)  {
		print <<<EOT
<p>
Click <a href="javascript:initmatches($div)">here</a> to set up matches for division $div.
</p>
EOT;
	}
	else  {
		print <<<EOT
<p>Click <a href="matchupd.php?div=$div">here</a> to allocate/update team members for matches in division $div.</p>
<p>Click <a href="javascript:okdel($div)">here</a> to delete the matches for division $div.</p>
<p>Click here to consign played matches to history.</p>
EOT;
	}
}
?>
</body>
</html>
