<?php
header("content-type: image/png");

$w = $_GET["w"];
$d = $_GET["d"];
$l = $_GET["l"];
$s = $_GET["s"];

if (strlen($s) == 0)
	$s = 100;

$tot = $w + $d + $l;

$wdeg = 360 * $w / $tot;
$ddeg = 360 * $d / $tot;
$ldeg = 360 - $wdeg - $ddeg;

$image = imagecreatetruecolor(106,106);

$bground = imagecolorallocate($image, 255, 255, 0xB3);
$red = imagecolorallocate($image, 255, 0, 0);
$green = imagecolorallocate($image, 0, 255, 0);
$blue = imagecolorallocate($image, 0, 0, 255);

imagefill($image, 0, 0, $bground);
if  ($wdeg > 0)
	imagefilledarc($image, 53, 53, $s, $s, 0, $wdeg, $green, IMG_ARC_PIE);
if  ($ddeg > 0)
	imagefilledarc($image, 53, 53, $s, $s, $wdeg, $wdeg+$ddeg, $blue, IMG_ARC_PIE);
if  ($ldeg > 0)
	imagefilledarc($image, 53, 53, $s, $s, $wdeg+$ddeg, 360, $red, IMG_ARC_PIE);
imagepng($image);
imagedestroy($image);
?>
