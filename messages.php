<?php
//   Copyright 2013 John Collins

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

include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';

try {
        $player = new Player();
        $player->fromid($userid);
}
catch (PlayerException $e) {
        $mess = $e->getMessage();
        include 'php/wrongentry.php';
        exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Qun = htmlspecialchars($username);
$Sun = mysql_real_escape_string($userid);
$Title = "Messages for $Qun";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php';
print "<h1>Messages for $Qun</h1>\n";
$ret = mysql_query("select fromuser,created,gameind,subject,hasread,ind from message where touser='$Sun' order by created,subject");
if (!$ret || mysql_num_rows($ret) == 0)  {
	print "No pending messages for $Qun\n";
}
else  {
	print <<<EOT
<table class="resultsb">
<tr>
	<th>From</th>
	<th>Date</th>
	<th>Time</th>
	<th>Subject</th>
EOT;
	while ($row = mysql_fetch_assoc($ret))  {
		$fu = $row["fromuser"];
		$cr = $row["created"];
		$gid = $row["gameind"];
		$subj = $row["subject"];
		$qsubj = htmlspecialchars($subj);
		$hasr = $row["hasread"];
		$ind = $row["ind"];
		$fp = new Player();
		$fp->fromid($fu);
		$pre = $post = "";
		if (!$hasr)  {
			$pre = "<strong>";
			$post = "</strong>";			
		}
		if  (preg_match($cr, "/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+):(\d+)", $matches))  {
			$dat = $matches[3] . '/' . $matches[2] . '/' . $matches[1];
			$tim = $matches[4] . ':' . $matches[5] . ':' . $matches[6];
		}
		else {
			$dat = $tim = $cr;
		}
		print <<<EOT
<tr>
	<td>$pre{$fu->display_name()}$post</td>
	<td>$pre$dat$post</td>
	<td>$pre$tim$post</td>
	<td><a href="dispmessage.php?mi=$ind">$pre$qsubj$post</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
?>
</div>
</div>
</body>
</html>
