<?php

class GameException extends Exception {}

class Game {
	private $Ind;			// Ind from database
	public  $Division;	// Division number
	public  $Date;			// Matchdate class object
	public  $Wteam;		// "White" team (class object)
	public  $Bteam;		// "Black" team (class object)
	public  $Wplayer;		// White player object
	public  $Bplayer;		// Black player object
	public  $Result;		// N (not played) W white B black J Jigo
	public  $Resultdet;	// Score as W+10.5 or B+R etc
	public  $Sgf;			// Sgf file
	public  $Matchind;	// Ind in match table
	
	public function __construct($in = 0, $d = 1) {
		$this->Ind = $in;
		$this->Division = $d;
		$this->Date = new Matchdate();
		$this->Wteam = new Team();
		$this->Bteam = new Team();
		$this->Result = 'N';
		$this->Resultdet = "";
		$this->Sgf = "";
		$this->Matchind = 0;
	}
	
	public function query_ind() {
		return $this->Ind;
	}

	public function fromget() {
		$this->Ind = intval($_GET["gn"]);
	}
	
	public function urlof() {
		return "gn={$this->Ind}";
	}
	
	public function queryof($prefix="") {
		return "{$prefix}ind={$this->Ind}";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,matchdate,wteam,bteam,wfirst,wlast,bfirst,blast,result,reshow,matchind from game where $q");
		if (!$ret)
			throw new MatchException("Cannot read database for match $q");
		if (mysql_num_rows($ret) == 0)
			throw new MatchException("Cannot find match record {$this->Ind}");
		$row = mysql_fetch_assoc($ret);
		$this->Division = $row["divnum"];
		$this->Date->fromtabrow($row);
		$this->Wteam = new Team($row["wteam"]);
		$this->Bteam = new Team($row["bteam"]);
		$this->Wplayer = new Player($row["wfirst"], $row["wlast"]);
		$this->Bplayer = new Player($row["bfirst"], $row["blast"]);
		$this->Result = $row["result"];
		$this->Resultdet = $row["resshow"];
		$this->Matchind = $row["matchind"];
	}
}
?>
