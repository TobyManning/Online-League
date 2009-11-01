<?php
//   Copyright 2009 John Collins

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

session_start();
$username = $_SESSION['user_name'];
$userpriv = $_SESSION['user_priv'];
$admin = strlen($username) != 0 && ($userpriv == 'A' || $userpriv == 'SA');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
?>
<html>
<?php
$Title = "Matches";
include 'php/head.php';
?>
<body>
<h1>Matches</h1>
<table class="matchesd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
<th>Status</th>
</tr>
<?php
$ret = mysql_query("select ind from lgmatch order by divnum,matchdate,hteam,ateam");
if ($ret && mysql_num_rows($ret) > 0)  {
	$lastdiv = -99;
	while ($row = mysql_fetch_array($ret))  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		try {
			$mtch->fetchteams();
			$mtch->fetchgames();
		}
		catch (MatchException $e) {
			continue;
		}
		if ($mtch->Division != $lastdiv)  {
			$lastdiv = $mtch->Division;
			print "<tr><th colspan=\"3\" align=\"center\">Division $lastdiv</th></tr>\n";
		}
		print <<<EOT
<tr>
<td>{$mtch->Date->display_month()}</td>
EOT;
		$ht = $mtch->Hteam->display_name();
		$at = $mtch->Ateam->display_name();
		if  ($mtch->teamalloc())  {
			if ($mtch->Result == 'H')
				$ht = "<b>$ht</b>";
			else if ($mtch->Result == 'A')
				$at = "<b>$at</b>";
			$ref = "<a href=\"showmtch.php?{$mtch->urlof()}\">";
			print "<td>$ref$ht</a></td><td>$ref$at</a></td>\n";
		}
		else  {
			$href = $aref = $hndref = $andref = '';
			if ($admin)  {
				$href = $aref = "<a href=\"updmatch.php?{$mtch->urlof()}\">";
				$hndref = $andref = "</a>";
			}
			$c = $mtch->is_captain($username);
			if ($c == 'H')  {
				$href = "<a href=\"updmatch.php?{$mtch->urlof()}\">";
				$hndref = "</a>";
			}
			elseif ($c == 'A') {
				$aref = "<a href=\"updmatch.php?{$mtch->urlof()}\">";
				$andref = "</a>";
			}
			print "<td>$href$ht$hndref</td><td>$aref$at$andref</td>\n";
		}
		if ($mtch->Result == 'H' || $mtch->Result == 'A' || $mtch->result == 'D')
			print "<td>Played</td>";
		elseif ($mtch->is_allocated())
			print "<td>Not played</td>";
		else
			print "<td>TBA</td>";
		print "</tr>\n";
	}
}
else {
	print "<tr><td colspan=\"3\" align=\"center\">No matches yet</td></tr>\n";
}
?>
</table>
</body>
</html>
