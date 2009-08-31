<?php

class MatchException extends Exception {}

// For convenience (and easy later extension) we have a "home team"
// and an "away team" on each match.

class Match {
	private $Ind;			// Ind from database
	public  $Division;	// Division number
	public  $Hteam;		// "Home" team (class object)
	public  $Ateam;		// "Away" team (class object)
	public  $Date;			// Matchdate class object
	public  $Slackdays;	// Days to arrange match
	public  $Hscore;		// "Home" Score
	public  $Ascore;		// "Away" Score
	public  $Result;		// N (not played) P (part played) D Draw H Home Win A Away win
	public  $Games;		// Array of game objects
	
	public function __construct($in = 0, $d = 1) {
		$this->Ind = $in;
		$this->Division = $d;
		$this->Hteam = new Team();
		$this->Ateam = new Team();
		$this->Date = new Matchdate();
		$this->Slackdays = 2;
		$this->Hscore = 0;
		$this->Ascore = 0;
		$this->Result = 'N';
		$this->Games = array();
	}
	
	public function query_ind() {
		return $this->Ind;
	}

	public function set_hometeam($h) {
		$this->Hteam = new Team($h);
	}
	
	public function set_awayteam($a) {
		$this->Ateam = new Team($a);
	}
	
	public function fromget() {
		$this->Ind = intval($_GET["mi"]);
	}
	
	public function frompost($prefix = "") {
		$this->Ind = $_POST["${prefix}mi"];
		if ($this->Ind == 0)
			throw new MatchException("Null post ind field"); 
	}

	public function save_hidden($prefix = "") {
		$f = $this->Ind;
		return "<input type=\"hidden\" name=\"${prefix}mi\" value=\"$f\">";
	}
	
	public function urlof() {
		return "mi={$this->Ind}";
	}
	
	public function queryof($prefix="") {
		return "{$prefix}ind={$this->Ind}";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,hteam,ateam,matchdate,hscore,ascore,result,slackdays from lgmatch where $q");
		if (!$ret)
			throw new MatchException("Cannot read database for match $q");
		if (mysql_num_rows($ret) == 0)
			throw new MatchException("Cannot find match record {$this->Ind}");
		$row = mysql_fetch_assoc($ret);
		$this->Division = $row["divnum"];
		$this->Hteam = new Team($row["hteam"]);
		$this->Ateam = new Team($row["ateam"]);
		$this->Date->fromtabrow($row);
		$this->Slackdays = $row["slackdays"];
		$this->Hscore = $row["hscore"];
		$this->Ascore = $row["ascore"];
		$this->Result = $row["result"];
	}
	
	public function fetchteams() {
		try  {
			$this->Hteam->fetchdets();
			$this->Ateam->fetchdets();
		}
		catch (TeamException $e) {
			throw new MatchException($e->getMessage());
		}
	}
	
	public function fetchgames() {
		$ret = mysql_query("select ind from game where {$this->queryof('match')} order by wrank desc,brank desc,wlast,blast,wfirst,bfirst");
		if (!$ret)
			throw new MatchException("Game read fail " . mysql_error());
		$result = array();
		try  {
			while ($row = mysql_fetch_array($ret))  {
				$g = new Game($row[0], $this->Ind, $this->Division);
				$g->fetchdets();
				array_push($result, $g);
			}
		}
		catch (GameException $e) {
			throw new MatchException($e->getMessage());
		}
		$this->Games = $result;
	}
	
	public function newgame() {
		$g = new Game(0, $this->Ind, $this->Division);
		$g->Date = $this->Date;
		array_push($this->Games, $g);
		return $g;
	}
	
	public function ngames()  {
		return count($this->Games);
	}
	
	public function teamalloc()  {
		$ret = mysql_query("select count(*) from game where {$this->queryof('match')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return false;
		$row = mysql_fetch_array($ret);
		return $row[0] != 0;
	}
	
	public function create() {
		$qhome = $this->Hteam->queryname();
		$qaway = $this->Ateam->queryname();
		$qdate = $this->Date->queryof();
		$qres = mysql_real_escape_string($this->Result);
		$ret = mysql_query("insert into lgmatch (divnum,hteam,ateam,matchdate,hscore,ascore,result,slackdays) values ({$this->Division},'$qhome','$qaway','$qdate',{$this->Hscore},{$this->Ascore},'$qres',{$this->Slackdays})");
		if (!$ret)
			throw new MatchException(mysql_error());
		$ret = mysql_query("select last_insert_id()");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new MatchException("Cannot locate match record id");
		$row = mysql_fetch_array($ret);
		$this->Ind = $row[0];
	}
	
	public function dateupdate() {
		$qdate = $this->Date->queryof();
		$ret = mysql_query("update lgmatch set matchdate='$qdate',slackdays={$this->Slackdays} where {$this->queryof()}");
		if (!$ret)
			throw new MatchException(mysql_error());
		mysql_query("update game set matchdate='$qdate' where matchind={$this->Ind} and result='N'");
		foreach ($this->Games as $g) {
			if ($g->Result == 'N')
				$g->Date = $this->Date;
		}
	}
	
	public function delmatch() {
		$ret = mysql_query("delete from lgmatch where {$this->queryof()}");
		if (!$ret)
			throw new MatchException(mysql_error());
		//  We currently don't allow deletion of played games so this shouldn't
		//  lose anything
		mysql_query("delete from game where matchind={$this->Ind}");
	}
	
	public function updscore() {
		$tot = $this->Hscore + $this->Ascore;
		if ($tot < 3)
			$this->Result = 'P';
		else if ($this->Hscore == $this->Ascore)
			$this->Result = 'D';
		else if ($this->Hscore < $this->Ascore)
			$this->Result = 'A';
		else
			$this->Result = 'H';
		mysql_query("update lgmatch set result='{$this->Result}',hscore={$this->Hscore},ascore={$this->Ascore} where {$this->queryof()}");
	}					
		
	public function slackdopt()
	{
		print "<select name=\"slackd\">\n";
		for ($i = 1;  $i <= 21; $i++) {
			if ($i == $this->Slackdays)
				print "<option selected>$i</option>\n";
			else
				print "<option>$i</option>\n";
		}
		print "</select>\n";
	}
}

function count_matches_for($divnum) {
	$ret = mysql_query("select count(*) from lgmatch where divnum=$divnum");
	if (!$ret || mysql_num_rows($ret) == 0)
		return  0;
	$row = mysql_fetch_array($ret);
	return $row[0];
}

?>
