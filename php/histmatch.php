<?php
//   Copyright 2010 John Collins

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

class HistMatchException extends Exception {}

// For convenience (and easy later extension) we have a "home team"
// and an "away team" on each match.

class HistMatch {
	public  $Seas;
	public  $Ind;			// Ind from database
	public  $Division;	// Division number
	public  $Hteam;		// "Home" team (class object)
	public  $Ateam;		// "Away" team (class object)
	public  $Date;			// Matchdate class object
	public  $Hscore;		// "Home" Score
	public  $Ascore;		// "Away" Score
	public  $Result;		// N (not played) P (part played) D Draw H Home Win A Away win
	public  $Games;		// Array of game objects
	public  $Defaulted;	// Match defaulted
	
	public function __construct($s, $in = 0, $d = 1) {
		$this->Seas = $s;
		$this->Ind = $in;
		$this->Division = $d;
		$this->Hteam = new Histteam($s);
		$this->Ateam = new Histteam($s);
		$this->Date = new Matchdate();
		$this->Hscore = 0;
		$this->Ascore = 0;
		$this->Result = 'N';
		$this->Games = array();
		$this->Defaulted = false;
	}
	
	public function query_ind() {
		return $this->Ind;
	}

	public function set_hometeam($h) {
		$this->Hteam = new Histteam($this->Seas, $h);
	}
	
	public function set_awayteam($a) {
		$this->Ateam = new Histteam($this->Seas, $a);
	}

	// Find out match ind from a page with ?hmi=nnn
		
	public function fromget() {
		$this->Ind = intval($_GET["hmi"]);
	}
	
	// From match ind get season ind and season
	
	public function getseason() {
		$ret = mysql_query("select seasind from histmatch where ind={$this->Ind}");
		if (!$ret || mysql_num_rows($ret) != 1)
			throw new HistMatchException("Cannot read database for season ind");
		$row = mysql_fetch_array($ret);
		try  {
			$this->Seas = new Season($row[0]);
			$this->Seas->fetchdets();
		}
		catch (SeasonException $e)  {
			throw new HistMatchException($e->getMessage());
		}	
	}		

	// For generation of query string with match ind in
		
	public function urlof() {
		return "hmi={$this->Ind}";
	}

	// Use for generating a database query component referring to the match
	// $prefix is set to a non-empty string where the column name has some
	// prefix to "ind" mostly for game where column is "matchind"
	 
	public function queryof($prefix="") {
		return "{$prefix}ind={$this->Ind}";
	}
	
	// Fetch the rest of the stuff relating to a match
	// apart from the teams
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select divnum,hteam,ateam,matchdate,hscore,ascore,result,defaulted from histmatch where $q");
		if (!$ret)
			throw new HistMatchException("Cannot read database for match $q");
		if (mysql_num_rows($ret) == 0)
			throw new HistMatchException("Cannot find hist match record {$this->Ind}");
		$row = mysql_fetch_assoc($ret);
		$this->Division = $row["divnum"];
		$this->Hteam = new Histteam($this->Seas, $row["hteam"]);
		$this->Ateam = new Histteam($this->Seas, $row["ateam"]);
		$this->Date->fromtabrow($row);
		$this->Hscore = $row["hscore"];
		$this->Ascore = $row["ascore"];
		$this->Result = $row["result"];
		$this->Defaulted = $row["defaulted"];
	}
	
	// Get the team details for a match
	
	public function fetchteams() {
		try  {
			$this->Hteam->fetchdets();
			$this->Ateam->fetchdets();
		}
		catch (HistteamException $e) {
			throw new HistMatchException($e->getMessage());
		}
	}

	// Fetch the game list (not including score)
		
	public function fetchgames() {
		$result = array();
		if  (!$this->Defaulted)  {
			$ret = mysql_query("select ind from game where {$this->queryof('match')} order by ind");
			if (!$ret)
				throw new HistMatchException("Game read fail " . mysql_error());
			try  {
				while ($row = mysql_fetch_array($ret))  {
					$g = new Game($row[0], $this->Ind, $this->Division);
					$g->fetchhistdets($this->Seas);
					array_push($result, $g);
				}
			}
			catch (GameException $e) {
				throw new HistMatchException($e->getMessage());
			}
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
	
	public function is_allocated() {
		if ($this->Defaulted)
			return true;
		if ($this->ngames() < 3)
			return false;
		foreach ($this->Games as $game)
			if (!$game->is_allocated())
				return false;
		return true;
	}
	
	public function teamalloc()  {
		if ($this->Defaulted)
			return true;
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
		$qdef = $this->Defaulted? 1: 0;
		$ret = mysql_query("insert into histmatch (ind,divnum,hteam,ateam,matchdate,hscore,ascore,result,seasind,defaulted) values ({$this->Ind},{$this->Division},'$qhome','$qaway','$qdate',{$this->Hscore},{$this->Ascore},'$qres',{$this->Seas->Ind},$qdef)");
		if (!$ret)
			throw new HistMatchException(mysql_error());
	}
}

// Return the number of matches for a division

function hist_count_matches_for($s, $divnum) {
	$ret = mysql_query("select count(*) from histmatch where seasind={$s->Ind} and divnum=$divnum");
	if (!$ret || mysql_num_rows($ret) == 0)
		return  0;
	$row = mysql_fetch_array($ret);
	return $row[0];
}

?>
