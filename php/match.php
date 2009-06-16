<?php

class MatchException extends Exception {}

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
	
	public function __construct($d = 1) {
		$this->Ind = 0;
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
	
	public function set_hometeam($h) {
		$this->Hteam = new Team($h);
	}
	
	public function set_awayteam($a) {
		$this->Ateam = new Team($a);
	}
	
	public function fromget() {
		$this->Ind = intval($_GET["mi"]);
	}
	
	public function urlof() {
		return "$mi={$this->Ind}";
	}
	
	public function queryof() {
		return "ind={$this->Ind}";
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
	
	public function create() {
		$qhome = $this->Hteam->queryname();
		$qaway = $this->Ateam->queryname();
		$qdate = $this->Date->queryof();
		$qres = mysql_real_escape_string($this->Result);
		$ret = mysql_query("insert into lgmatch (divnum,hteam,ateam,matchdate,hscore,ascore,result,slackdays) values ({$this->Division},'$qhome','$qaway','$qdate',{$this->Hscore},{$this->Ascore},'$qres'.{$this->Slackdays})");
		if (!$ret)
			throw new MatchException(mysql_error());
		$ret = mysql_query("select last_insert_id()");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new MatchException("Cannot locate match record id");
		$row = mysql_fetch_array($ret);
		$this->Ind = $row[0];
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
