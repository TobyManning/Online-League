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

$sel = $_POST["actselect"];
$amount = $_POST["amount"];

$selarr = explode(':', $sel);
if  (count($selarr) < 3)  {
	$mess = "Unexpected POST input";
	include 'php/wrongentry.php';
	exit(0);
}

switch  ($selarr[0])  {
default:
	$mess = "Do not know how to do {$selarr[0]} payments yet";
	include 'php/wrongentry.php';
	exit(0);
case  'T':
	$type = 'T';
	$teamname = $selarr[1];
	$nonbga = $selarr[2];
	$tot = $selarr[3];
	break;
case  'I':
	$type = 'I';
	$first = $selarr[1];
	$last = $selarr[2];
	$nonbga = $selarr[3];
	$tot = $selarr[4];
	break;
}

// Just check this makes sense

if ($tot != $amount) {
	$mess = "Total $tot does not match amount $amount";
	include 'php/wrongentry.php';
	exit(0);
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

		// Check we haven't already got a pending payment for this team
		
		$ret = mysql_query("select ind from pendpay where league='T' and {$team->queryof('descr1')} and paywhen >= date_sub(current_timestamp, interval 1 day)");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		if (mysql_num_rows($ret) > 0)  {
			$mess = "Duplicated payment record for $teamname";
			include 'php/probpay.php';
			exit(0);
		}
		
		// Create a payment record for the team
		
		$qteam = mysql_real_escape_string($teamname);
		$ret = mysql_query("insert into pendpay (league,descr1) values ('T','$qteam')");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
	}
	else  {
		$pplayer = new Player($first, $last);
		$pplayer->fetchdets();
		
		// Error if this team has paid
		
		if ($pplayer->ILpaid)  {
			$mess = "$first $last is already paid??";
			include 'php/wrongentry.php';
			exit(0);
		}

		// Check we haven't already got a pending payment for this person
		
		$ret = mysql_query("select ind from pendpay where league='I' and descr1='{$pplayer->queryfirst()}' and descr2='{$pplayer->querylast()}' and paywhen >= date_sub(current_timestamp, interval 1 day)");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		if (mysql_num_rows($ret) > 0)  {
			$mess = "Duplicated payment record for $first $last";
			include 'php/probpay.php';
			exit(0);
		}
		
		// Create a payment record for the person
		
		$qfirst = mysql_real_escape_string($first);
		$qlast = mysql_real_escape_string($last);
		$ret = mysql_query("insert into pendpay (league,descr1,descr2) values ('I','$qfirst','$qlast')");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Payment of subscriptions part 2";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Payment of subscriptions (part 2)</h1>
<?php
if ($type == 'T') {
	print <<<EOT
<p>This is going to be a payment of &pound;$amount on behalf of {$team->display_name()}.</p>

EOT;
}
else {
	print <<<EOT
<p>This is going to be a payment of &pound;$amount on behalf of {$pplayer->display_name()}.</p>

EOT;
}
print <<<EOT
<p>The person making the payment is {$player->display_name()}.</p>

EOT;
?>
</div>
</div>
</body>
</html>
