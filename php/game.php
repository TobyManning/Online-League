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
	
	public function __construct($min = 0, $d = 1) {
		$this->Ind = 0;
		$this->Division = $d;
		$this->Date = new Matchdate();
		$this->Wteam = new Team();
		$this->Bteam = new Team();
		$this->Result = 'N';
		$this->Resultdet = "";
		$this->Sgf = "";
		$this->Matchind = $min;
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
			throw new GameException("Cannot read database for game $q");
		if (mysql_num_rows($ret) == 0)
			throw new GameException("Cannot find game record {$this->Ind}");
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
	
	public function create_game() {
		$qwfirst = $this->Wplayer->queryfirst();
		$qwlast = $this->Wplayer->querylast();
		$qbfirst = $this->Bplayer->queryfirst();
		$qblast = $this->Bplayer->querylast();
		$qwteam = $this->Wteam->queryname();
		$qbteam = $this->Bteam->queryname();
		$qwrank = $this->Wplayer->Rank->Rankvalue;
		$qbrank = $this->Bplayer->Rank->Rankvalue;
		$qdate = $this->Date->queryof();
		// These are always going to be 'N' and null but let's be consistent.
		$qres = mysql_real_escape_string($this->Result);
		$qresdat = mysql_real_escape_string($this->Resultdet);
		$qsgf = mysql_real_escape_string($this->Sgf);
		$qmi = $this->Matchind;
		if (!mysql_query("insert into game (matchdate,wfirst,wlast,wteam,wrank,bfirst,blast,bteam,brank,result,reshow,sgf,matchind,divnum) values ('$qdate','$qwfirst','$qwlast','$qwteam',$qwrank,'$qbfirst','$qblast','$qbteam',$qbrank,'$qres','$qresdat','$qsgf',{$this->Matchind},{$this->Division})"))
			throw new GameException(mysql_error());
		$ret = mysql_query("select last_insert_id()");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new GameException("Cannot locate game record id");
		$row = mysql_fetch_array($ret);
		$this->Ind = $row[0];					
	}
	
	public function update_players()  {
		$qwfirst = $this->Wplayer->queryfirst();
		$qwlast = $this->Wplayer->querylast();
		$qbfirst = $this->Bplayer->queryfirst();
		$qblast = $this->Bplayer->querylast();
		$qwteam = $this->Wteam->queryname();
		$qbteam = $this->Bteam->queryname();
		$qwrank = $this->Wplayer->Rank->Rankvalue;
		$qbrank = $this->Bplayer->Rank->Rankvalue;
		if (!mysql_query("update game set wfirst='$qwfirst',wlast='$qwlast',bfirst='$qbfirst',blast='$qblast',wteam='$qwteam',bteam='$qbteam',wrank=$qwrank,brank=$qbrank where {$this->queryof()}"))
			throw new GameException(mysql_error()); 
	}
}
?>
