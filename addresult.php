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
	$g->fromget();
	$g->fetchdets();
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Add Game Result";
include 'php/head.php';
?>
<body>
<h1>Add Game Result</h1>
<p>
Adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black.
</p>
<form action="addresult2.php" method="post" enctype="multipart/form-data">
{$g->save_hidden()}
<p>
EOT;
$g->Date->dateopt("Game was played on");
print <<<EOT
</p>
<p>
Result was
<select name="result" size="0">
<option value="W">White Win</option>
<option value="B">Black Win</option>
<option value="J">Jigo</option>
</select>
by
<select name="resulttype" size="0">
<option value="N">Not known</option>
<option value="R">Resign</option>
<option value="T">Time</option>
EOT;
for ($v = 0; $v < 50; $v++)
	print "<option value=$v>$v.5</option>\n";
?>
<option value="H">Over 50</option>
</select>
</p>
<p>
If you can please browse on your computer for an SGF file of the game to
upload <input type=file name=sgffile>
</p>
<p>When done, press this:
<input type="submit" value="Add result">
</p>
</form>
<p>If you never meant to get to this page
<a href="javascript:history.back()">click here</a> to go back.</p>
</body>
</html>
