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
	$g->get_sgf();
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
header("content-type: application/x-go-sgf");
print $g->Sgf;
?>
