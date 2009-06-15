<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/match.php';
$div = $_GET["div"];
if (strlen($div) == 0) {
	include 'php/wrongentry.php';
	exit(0);
}
?>
<html>
<?php
$Title = "Initialise Matches";
include 'php/head.php';
?>
<body>
<h1>Initialise Matches</h1>
<?php

class Gameres {
	public $Home;
	public $Away;
	
	public __construct($h, $a) {
		$this->Home = $h;
		$this->Away = $a;
	}
}

class MatchData {
	public $Nteams;
	public $Ngames;
	public $Selected;
	public $Played;
	public $Pending;
	public $Resmatch;
	
	public function __construct($n = 6) {
		$this->Nteams = $n;
		$this->Ngames = $n/2;
		$this->Selected = array_fill(0, $n, 0);
		$this->Played = array();
		for ($i = 0; $i < $n; $i++) {
			array_push($this->Played, array_fill(0, $n, 0));
		}
		$this->Pending = array();
		$this->Resmatch = array();
	}
	
	private function pmatch() {
		$res = array();
		for ($t = 0;  $t < $this->Nteams;  $t += 2)  {
			$h = $this->Pending[$t];
			$a = $this->Pending[$t+1];
			array_push($res, new Gameres($h, $a));
			$this->Played[$h][$a] = 1;
			$this->Played[$a][$h] = 1;
		}
		array_push($this->Resmatch, $res);
	}
	
	private function permute_inner() {
		for ($ht = 0; $ht < $this->Nteams; $ht++)  {
			if ($this->Selected[$ht])
				continue;
			$this->Selected[$ht] = 1;
			for  ($at = 0; $at < $this->Nteams; $at++)  {
				if ($this->Selected[$at])
					continue;
				if ($this->Played[$ht][$at])
					continue;
	    		$this->Selected[$at] = 1;
	    		array_push($this->Pending, $ht);
	    		array_push($this->Pending, $at);
	    		if  (count($this->Pending) == $this->Nteams)  {
	    			$this->pmatch();
	    			array_pop($this->Pending);
	    			array_pop($this->Pending);
	    			$this->Selected[$at] = 0;
	    			$this->Selected[$ht] = 0;
	    			return  1;
	    		}
	    		else  {
	    			$ret = $this->permute_inner();
	    			array_pop($this->Pending);
	    			array_pop($this->Pending);
	    			$this->Selected[$at] = 0;
	    			if  ($ret)  {
	    				$this->Selected[$ht] = 0;
	    				return  1;
	    			}
	    		}
	    	}
	    	$this->Selected[$ht] = 0;
		}
		return  0;
	} 

	public function permute() {
		$this->Selected[0] = 1;
		array_push($this->Pending, 0);
		for (at = 1; $at < $this->Nteams; $at++)  {
			if ($this->Played[0][$at])
				continue;
			$this->Selected[$at] = 1;
			array_push($this->Pending, $at);
			$this->permute_inner();
			array_pop($this->Pending);
			$this->Selected[$at] = 0;
		}
	}	
}

$teams = list_teams($div);
$nteams = count($teams);

if ($nteams <= 2)  {
	print <<<EOT
<p>
Sorry but not enough teams in Division $div for a match yet.
</p>
<p>Click <a href="matchupdb.php">here</a> to go back to match updates.</p>
EOT;
}
else  {
	$loops = $nteams + 4;
	for ($cnt = 0; $cnt < $loops; $cnt++)  {
		$f = rand(0, $nteams-1);
		$t = rand(0, $nteams-1);
		if ($f != $t)  {
			$tmp = $teams[$f];
			$teams[$f] = $teams[$t];
			$teams[$t] = $tmp;
		}
	}
	if (($nteams & 1) == 1)  {
		array_push($teams, new Team("Bye"));
		$nteams++;
	}
	$md = new MatchData($nteams);
	$md->permute();
	$cnt = 1;
	foreach ($md->Resmatch as $rm)  {
		print "<h2>Month $cnt</h2>\n";
		foreach ($rm as $rim) {
			print "<p>{$teams[$rim->Home]->display_name()} -v- {$teams[$rim->Away]->display_name()}</p>\n";
		}	
	}		 
}?>
</body>
</html>
