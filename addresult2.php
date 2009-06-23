<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
$g = new Game();
try  {
	$g->frompost();
	$g->fetchdets();
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
$date_played = new Matchdate();
$date_played->frompost();
$sgfdata = "";
$fn = $_FILES["sgffile"];
if ($fn['error'] == UPLOAD_ERR_OK  &&  preg_match('/.*\.sgf$/i', $fn['name']) && $fn['size'] > 0)
	$sgfdata = file_get_contents($fn['tmp_name']);
if ($date_played->unequal($g->Date))
	$g->resetdate($date_played);
$g->set_result($_POST["result"], $_POST["resulttype"]);
if (strlen($sgfdata) != 0)
	$g->set_sgf($sgfdata);
?>
<html>
<?php
$Title = "Game Result Added";
include 'php/head.php';
?>
<body>
<h1>Add Game Result</h1>
<p>
Finished adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black as {$g->display_result}.
</p>
EOT;
?>
<p><a href="javascript:history.go(-2)">Click here</a> to go back to where you were.</p>
</body>
</html>
