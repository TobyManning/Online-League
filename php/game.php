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
	
	public function __construct($in = 0, $min = 0, $d = 1) {
		$this->Ind = $in;
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
	
	public function frompost($prefix = "") {
		$this->Ind = $_POST["${prefix}gn"];
		if ($this->Ind == 0)
			throw new GameException("Null post ind field"); 
	}
	
	public function urlof() {
		return "gn={$this->Ind}";
	}
	
	public function queryof($prefix="") {
		return "{$prefix}ind={$this->Ind}";
	}
	
	public function save_hidden($prefix = "") {
		$f = $this->Ind;
		return "<input type=\"hidden\" name=\"${prefix}gn\" value=\"$f\">";
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
		$this->Wplayer->fetchdets();
		$this->Bplayer->fetchdets();
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
	
	private function has_sgf() {
		$ret = mysql_query("select length(sgf) from game where {$this->queryof()}");
		if (!$ret)
			return false;
		$row = mysql_fetch_array($ret);
		return $row[0] != 0;
	}
	
	public function display_result($addunpl = false) {
		if ($this->Result == 'N')  {
			if  ($addunpl)
				return  "<a href=\"addresult.php?{$this->urlof()}\">Add Result</a>";
			return  "Not played";
		}
		if (strlen($this->Resultdet) != 0)
			$res = htmlspecialchars($this->Resultdet);
		else  switch  ($this->Result) {
		case 'W':
			$res = "White Win";
			break;
		case 'B':
			$res = "Black Win";
			break;
		case 'J':
			$res = "Jigo";
			break;
		}
		if ($this->has_sgf())
			$res = "<a href=\"downloadsgf.php?{$this->urlof()}\">$res</a>";
		return $res;
	}
	
	public function reset_date($dat) {
		$this->Date = $dat;
		mysql_query("update game set matchdate='{$dat->queryof()}' where {$this->queryof()}"); 
	}
	
	public function adj_match($mtch, $mult) {
		switch ($this->Result) {
		default:
			return;
		case 'J':
			$mtch->Hscore += 0.5 * $mult;
			$mtch->Ascore += 0.5 * $mult;
			return;
		case 'W':	
			if ($this->Wteam->is_same($mtch->Hteam))
				$mtch->Hscore += $mult;
			else
				$mtch->Ascore += $mult;
			return;
		case 'B':
			if ($this->Wteam->is_same($mtch->Hteam))
				$mtch->Ascore += $mult;
			else
				$mtch->Hscore += $mult;
			return;
		}
	}
	
	public function set_result($res, $restype) {
		if (preg_match('/\d+/', $restype))
			$restype .= '.5';
		if ($res != 'J')
			$restype = "$res+$restype";
		else
			$restype = "";
		$this->Resultdet = $restype;
		$mtch = new Match($this->Matchind);
		$mtch->fetchdets();
		$this->adj_match($mtch, -1);
		$this->Result = $res;
		$this->adj_match($mtch, 1);
		$mtch->updscore();
		$qres = mysql_real_escape_string($res);
		$qrest = mysql_real_escape_string($restype);
		mysql_query("update game set result='$qres',reshow='$qrest' where {$this->queryof()}");		
	}
	
	public function get_sgf() {
		$ret = mysql_query("select sgf from game where {$this->queryof()}");
		if (!$ret  ||  mysql_num_rows($ret) == 0)
			return;
		$row = mysql_fetch_array($ret);
		$this->Sgf = $row[0];
	}
	
	public function set_sgf($sgfdata) {
		$qsgfdata = mysql_real_escape_string($sgfdata);
		mysql_query("update game set sgf='$qsgfdata' where {$this->queryof()}");
		$this->Sgf = $sgfdata;
	}
}
?>
