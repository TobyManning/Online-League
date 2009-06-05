<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$Playerlist = list_players("club,rank desc,last,first");
$Elist = $team->list_members();
$Title = "Update Team Members for {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body onload="javascript:loadtab()" onunload="javascript:killwind()">
<script language="javascript">
var playerlist = new Array();
var currteam = new Array();
EOT;
foreach ($Playerlist as $player) {
	$player->fetchdets();
	$player->fetchclub();
	print <<<EOT
playerlist.push({first:"{$player->display_first()}", last:"{$player->display_last()}",
rank:"{$player->display_rank()}", club:"{$player->Club->display_name()}"});

EOT;
}
foreach ($Elist as $ep) {
	$ep->fetchdets();
	$ep->fetchclub();
	print <<<EOT
currteam.push({first:"{$ep->display_first()}", last:"{$ep->display_last()}",
rank:"{$ep->display_rank()}", club:"{$ep->Club->display_name()}"});

var teamurl = "{$team->urlof()}";
EOT;
}
?>
var changes = 0;
var createwind = null;

function killwind() {
	if (createwind) {
		createwind.close();
		createwind = null;
	}
}

// Replace message in final paragraph to warn users that there are
// changes to save

function set_changes() {
	changes++;
	var par = document.getElementById('changepara');
	var newtext = document.createTextNode("There are changes to save");
	var btext = document.createElement("b");
	btext.appendChild(newtext);
	var kids = par.childNodes;
	par.replaceChild(btext, kids[0]);
}

function addmembs() {
	killwind();
	createwind = window.open("membpick.html", "Select Team Member", "width=450,height=400,resizeable=yes,scrollbars=yes");
}

function insertmemb(pl) {
	var ttbod = document.getElementById('membbody');
	var rownum = ttbod.rows.length;
	var rownode = ttbod.insertRow(rownum);
	var cellnode = rownode.insertCell(0);
	var text = document.createTextNode(pl.first + " " + pl.last);
	cellnode.appendChild(text);

	cellnode = rownode.insertCell(1);
	text = document.createTextNode(pl.rank);
	cellnode.appendChild(text);

	cellnode = rownode.insertCell(2);
	text = document.createTextNode(pl.club);
	cellnode.appendChild(text);

	cellnode = rownode.insertCell(3);
	text = document.createTextNode("Del");
	var anch = document.createElement('a');
	anch.href = "javascript:delmembrow(" + rownum + ")";
	anch.appendChild(text);
	cellnode.appendChild(anch);
}

function delmembrow(rownum) {
	var ttbod = document.getElementById('membbody');
	while (ttbod.rows.length != 0)
		ttbod.deleteRow(0);
	currteam.splice(rownum,1);
	loadtab();
	set_changes();
	killwind();
}

function loadtab() {
	for (var i = 0;  i < currteam.length;  i++)
		insertmemb(currteam[i]);
}

function savemembs() {
	if (changes == 0)
		return;
	var args = new Array();
	args.push(teamurl);
	for (var i = 0; i < currteam.length; i++)  {
		var pl = currteam[i];
		args.push("tm" + i + "f=" + urlencode(htmlspecialchars_decode(pl.first)));
		args.push("tm" + i + "l=" + urlencode(htmlspecialchars_decode(pl.last)));
	}
	var arglist = args.join("&");
	var newloc = "updmembs.php?" + arglist;
	document.location = newloc; 
}

</script>
<?php
print <<<EOT
<h1>Update Team Members for {$team->display_name()}</h1>
EOT;
?>
<p>
This is the current team. To add a player to the team
<a href="javascript:addmembs()">click here</a>.
To remove a player click
del against the player.
</p>
<table cellpadding="2" cellspacing="2" border="0">
<thead>
<tr>
<th>Name</th>
<th>Rank</th>
<th>Club</th>
<th>Del</th>
</tr>
</thead>
<tbody id="membbody">
</tbody>
</table>
<p>When done <a href="javascript:savemembs()">click here</a> or to forget the changes
click somewhere else.</p>
<p id="changepara">There are no changes at present.</p>
</body>
</html>
