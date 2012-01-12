<?php
//   Copyright 2012 John Collins

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

include 'php/checksecure.php';
include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';

try {
	$player = new Player();
	$player->fromid($userid);
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$ind = $_POST["ind"];
$tok = $_POST["token"];
if (strlen($ind) == 0 || strlen($tok) == 0)  {
	$mess = "No indicator given ind=$ind tok=$tok???";
	include 'php/wrongentry.php';
	exit(0);
}

$ret = mysql_query("select league,descr1,descr2,token,amount from pendpay where ind=$ind");
if (!$ret)  {
	$mess = mysql_error();
	include 'php/dataerror.php';
	exit(0);
}
if (mysql_num_rows($ret) == 0)  {
	$mess = "Cannot find pending payment, ind=$ind";
	include 'php/wrongentry.php';
	exit(0);
}
$row = mysql_fetch_assoc($ret);

// Verify that the token matches up (change this later not to display them)

$rtok = $row["token"];
if ($tok != $rtok) {
	$mess = "Mismatch tokens r=$tok, d=$rtok";
	include 'php/wrongentry.php';
	exit(0);
}

switch  ($row["league"])  {
default:
	$mess = "Do not know how to do {$row['league']} payments yet";
	include 'php/wrongentry.php';
	exit(0);
case  'T':
	$type = 'T';
	$teamname = $row["descr1"];
	break;
case  'I':
	$type = 'I';
	$first = $row["descr1"];
	$last = $row["descr2"];
	break;
}

try {
	if ($type == 'T')  {
		$team = new Team($teamname);
		$team->fetchdets();
		
		// Error if this team has paid
		
		if ($team->Paid)  {
			$mess = "Team $teamname have already paid??";
			include 'php/wrongentry.php';
			exit(0);
		}

		// Set team as having paid
		
		$team->setpaid();
	}
	else  {
		$pplayer = new Player($first, $last);
		$pplayer->fetchdets();
		
		// Error if this player has paid
		
		if ($pplayer->ILpaid)  {
			$mess = "$first $last is already paid??";
			include 'php/wrongentry.php';
			exit(0);
		}

		// Set player as having paid
		
		$pplayer->setpaid();
	}
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

// Finally delete pending payment

$ret = mysql_query("delete from pendpay where ind=$ind");
if (!$ret)  {
	$mess = mysql_error();
	include 'php/dataerror.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Payment OK";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Payment OK</h1>
<?php
if ($type == 'T') {
	print <<<EOT
<p>Recorded payment on behalf of {$team->display_name()}.</p>

EOT;
}
else {
	print <<<EOT
<p>Recorded payment on behalf of {$pplayer->display_name()}.</p>

EOT;
}
?>
<p><strong>Thank you!</strong></p>
</div>
</div>
</body>
</html>
