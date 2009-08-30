<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
?>
<html>
<?php
$Title = "Players List";
include 'php/head.php';
?>
<body>
<h1>Players</h1>
<?php
switch ($_GET["by"])  {
default:
	$pclub = 1;
	$order = "last,first,rank desc";
	$next = "club";
	$initials = list_player_initials();
	$byrank = 0;
	break;
case  "club":
	$pclub = 0;
	$order = "club.name,last,first,rank desc";
	$next = "rank";
	$initials = list_club_initials();
	$byrank = 0;
	break;
case  "rank":
	$pclub = 1;
	$order = "rank desc,last,first,rank desc";
	$next = "clubrank";
	$initials = list_player_ranks();
	$byrank = 1;
	break;
case  "clubrank":
	$pclub = 0;
	$order = "club.name,rank desc,last,first";
	$next = "name";
	$initials = list_club_initials();
	$byrank = 0;	// Did mean that
	break;
}
if (count($initials) != 0)  {
	print <<<EOT
<a name="Top"></a>
<table class="plinits"><tr>
EOT;
	if ($byrank) {
		foreach ($initials as $init) {
			$r = new Rank($init);
			print "<td><a href=\"#{$r->anchor()}\">{$r->display()}</a></td>\n";
		}
	}
	else  {			
		foreach ($initials as $init) {
			print "<td><a href=\"#$init\">$init</a></td>\n";	
		}
	}
	print "</tr></table>\n";
}
$ref = "<a href=\"playersb.php?by=$next\">";
print <<<EOT
<table class="pllist">
<tr>
<th>${ref}Name</a></th>
<th>${ref}Rank</a></th>
EOT;
if ($pclub)
	print "<th>${ref}Club</a></th>\n";
print <<<EOT
<th>${ref}P</a></th>
<th>${ref}W</a></th>
<th>${ref}D</a></th>
<th>${ref}L</a></th>
<th>${ref}Online</a></th>
<th>${ref}Userid</a></th>
<th>${ref}Email</a></th>
EOT;
?>
</tr>
<?php
$ret = mysql_query("select first,last,club.name from player,club where player.club=club.code order by $order");
if ($ret && mysql_num_rows($ret)) {
	$lclub = "not set";
	$linit = "-";
	$lrank = new Rank(4000);
	while ($row = mysql_fetch_assoc($ret)) {
		$p = new Player($row["first"], $row["last"]);
		$p->fetchdets();
		$club = $row["name"];
		$nrank = $p->Rank;
		if ($byrank)  {
			if ($lrank->notequals($nrank)) {
				$lrank = $nrank;
				print <<<EOT
<tr>
<th colspan=11 align="center">
<a name="{$nrank->anchor()}"></a>
<a href="#Top">{$nrank->display()}</a>
</th>
</tr>
EOT;
			}
		}
		elseif  ($pclub)  {
			$pinit = $p->get_initial();
			if  ($linit != $pinit)  {
				$linit = $pinit;
				print <<<EOT
<tr>
<th colspan=11 align="center">
<a name="$pinit"></a>
<a href="#Top">$pinit</a>
</th>
</tr>
EOT;
			}
		}
		else  {
			if  ($lclub != $club)  {
				$cinit = strtoupper(substr($club, 0, 1));
				$ref = "";
				if ($linit != $cinit) {
					$linit = $cinit;
					$ref = "<a name=\"$cinit\"></a>";
				}
				print "<tr><th colspan=\"10\" align=\"center\">" . $ref . "<a href=\"#Top\">" . htmlspecialchars($club) . "</a></th></tr>\n";
				$lclub = $club;
			}
		}
		print <<<EOT
<tr>
<td>{$p->display_name()}</td>
<td>{$p->display_rank()}</td>

EOT;
		if ($pclub)
			print "<td>" . htmlspecialchars($club) . "</td>\n";
		print <<<EOT
<td>{$p->played_games()}</td>
<td>{$p->won_games()}</td>
<td>{$p->drawn_games()}</td>
<td>{$p->lost_games()}</td>
<td>{$p->display_online()}</td>
<td>{$p->display_userid()}</td>
<td>{$p->display_email()}</td>
</tr>
EOT;
	}
}
?>
</table>
</body>
</html>
