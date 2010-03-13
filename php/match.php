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
	
	// Just a few places where we need the ind but we don't want people fiddling
	// so we make it private
	
	public function query_ind() {
		return $this->Ind;
	}

	public function set_hometeam($h) {
		$this->Hteam = new Team($h);
	}
	
	public function set_awayteam($a) {
		$this->Ateam = new Team($a);
	}

	// Find out match ind from a page with ?mi=nnn
		
	public function fromget() {
		$this->Ind = intval($_GET["mi"]);
	}

	// For when we leave a hidden input in form telling
	// the next page which prefix we're talking about
		
	public function frompost($prefix = "") {
		$this->Ind = $_POST["${prefix}mi"];
		if ($this->Ind == 0)
			throw new MatchException("Null post ind field"); 
	}

	// For saving input in form
	
	public function save_hidden($prefix = "") {
		$f = $this->Ind;
		return "<input type=\"hidden\" name=\"${prefix}mi\" value=\"$f\">";
	}

	// For generation of query string with match ind in
		
	public function urlof() {
		return "mi={$this->Ind}";
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
	
	// Get the team details for a match
	
	public function fetchteams() {
		try  {
			$this->Hteam->fetchdets();
			$this->Ateam->fetchdets();
		}
		catch (TeamException $e) {
			throw new MatchException($e->getMessage());
		}
	}

	// Fetch the game list (not including score)
		
	public function fetchgames() {
		$ret = mysql_query("select ind from game where {$this->queryof('match')} order by ind");
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
	
	public function is_allocated() {
		if ($this->ngames() < 3)
			return false;
		foreach ($this->Games as $game)
			if (!$game->is_allocated())
				return false;
		return true;
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
		mysql_query("update game set matchdate='$qdate' where {$this->queryof('match')} and result='N'");
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
		mysql_query("delete from game where {$this->queryof('match')}");
	}
	
	// Adjust result of match for incoming score
	
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

	// Push out a selection option for the number of spare days
			
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
	
	public function is_captain($name) {
		try  {
			$possp = new Player($name);
		}
		catch (PlayerException $e) {
			return 'N';
		}
		if ($this->Hteam->Captain->is_same($possp))
			return 'H';
		if ($this->Ateam->Captain->is_same($possp))
			return 'A';
		return 'N';
	}
	
	public function mail_allocated() {
		if (!$this->is_allocated())
			return;
		$fh = popen("mail -s 'Go League match set up' online-league@britgo.org {$this->Hteam->Captain->Email} {$this->Ateam->Captain->Email}", "w");
		$mess = <<<EOT
Completed allocation of players to match in division {$this->Division} for {$this->Date->display_month()} between
{$this->Hteam->display_name()} ({$this->Hteam->display_description()}) and {$this->Ateam->display_name()} ({$this->Ateam->display_description()}).

Allocation is:
EOT;
		fwrite($fh, "$mess\n");
		foreach ($this->Games as $g) {
			$mess = <<<EOT
White: {$g->Wplayer->display_name(false)} {$g->Wplayer->display_rank()} {$g->Wteam->display_name()} Black: {$g->Bplayer->display_name(false)} {$g->Bplayer->display_rank()} {$g->Bteam->display_name()}
EOT;
			fwrite($fh, "$mess\n");
		}
		$mess = <<<EOT

Team Captains are:

For {$this->Hteam->display_name()}: {$this->Hteam->display_captain()}, {$this->Hteam->Captain->display_email_nolink()}
For {$this->Ateam->display_name()}: {$this->Ateam->display_captain())}, {$this->Ateam->Captain->display_email_nolink()}
EOT;
		fwrite($fh, "$mess\n");
		pclose($fh);
	}
}

// Return the number of matches for a division

function count_matches_for($divnum) {
	$ret = mysql_query("select count(*) from lgmatch where divnum=$divnum");
	if (!$ret || mysql_num_rows($ret) == 0)
		return  0;
	$row = mysql_fetch_array($ret);
	return $row[0];
}

?>
