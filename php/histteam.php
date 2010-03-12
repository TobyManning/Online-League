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

class HistteamException extends Exception {}

class Histteam  {
	public $Name;			// Team short name
	public $Description;	// Team full name
	public $Division;		// League division for season
	public $Seas;			// Season object
	public $Played;		// Played matches
	public $Won;			// Won matches
	public $Drawn;			// Drawn matches
	public $Lost;			// Lost matches
	public $Scoref;		// Scores for
	public $Scorea;		// Scores against
	public $Sortrank;		// Ranking for league sort
	
	public function __construct($s) {
		$this->Name = "";
		$this->Seas = $s;
		$this->Division = 1;
		$this->Sortrank = 0;
	}
	
	public function fromget() {
		$this->Name = $_GET["htn"];
		if (strlen($this->Name) == 0)
			throw new HistteamException("Null name field"); 
	}

	public function queryof($colname = "name") {
		$qn = mysql_real_escape_string($this->Name);
		return "$colname='$qn' and seasind={$this->Seas->Ind}";
	}
	
	public function queryname() {
		return mysql_real_escape_string($this->Name);
	}
	
	public function noquote() {
		$p = array('/"/', "/'/");
		$r = array("", "");
		return preg_replace($p, $r, $this->Name);
	}
	
	public function urlof() {
		$n = urlencode($this->Name);
		return "htn=$n";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select description,divnum from histteam where $q");
		if (!$ret)
			throw new HistteamException("Cannot read database for histteam {$this->Name}");
		if (mysql_num_rows($ret) == 0)
			throw new HistteamException("Cannot find histteam {$this->Name}");
		$row = mysql_fetch_assoc($ret);
		$this->Description = $row["description"];
		$this->Division = $row["divnum"];
	}
	
	public function display_name() {
		return htmlspecialchars($this->Name);
	}
	
	public function display_description() {
		return htmlspecialchars($this->Description);
	}
	
	// Trivial but room for expansion
	
	public function display_division() {
		return $this->Division;
	}
	
	public function create() {
		$qname = mysql_real_escape_string($this->Name);
		$qdescr = mysql_real_escape_string($this->Description);
		$qdiv = $this->Division;
		$qseas = $this->Seas->Ind;
		// Delete any team with the same name for the season
		mysql_query("delete from histteam where {$this->Seas->queryof()} and name='$qname'");
		if (!mysql_query("insert into histteam (name,description,divnum,seasind) values ('$qname','$qdescr',$qdiv,$qseas)"))
			throw new HistteamException(mysql_error());
	}
	
	public function get_n_from_matches($crit, $wot="count(*)") {
		$ret = mysql_query("select $wot from histmatch where seasind={$this->Seas->Ind} and $crit");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function get_scores($p = Null) {
		$tn = $this->queryname();
		$ht = "hteam='$tn'";
		$at = "ateam='$tn'";
		$this->Played = $this->get_n_from_matches("result!='N' and result!='P' and ($ht or $at)");
		$this->Won = $this->get_n_from_matches("(($ht and result='H') or ($at and result='A'))");
		$this->Lost = $this->get_n_from_matches("(($ht and result='A') or ($at and result='H'))");
		$this->Drawn = $this->get_n_from_matches("result='D' and ($ht or $at)");
		$this->Scoref = $this->get_n_from_matches($ht, "sum(hscore)") +
							 $this->get_n_from_matches($at, "sum(ascore)");
		$this->Scorea = $this->get_n_from_matches($ht, "sum(ascore)") +
							 $this->get_n_from_matches($at, "sum(hscore)");
		if ($p)
			$this->Sortrank = $this->Played * $p->Played +
									$this->Won * $p->Won +
									$this->Drawn * $p->Drawn +	
									$this->Lost * $p->Lost +
									$this->Scoref * $p->For +
									$this->Scorea * $p->Against;
	}
	
	public function count_members() {
		$ret = mysql_query("select count(*) from histteammemb where {$this->queryof('teamname')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function list_members($order = "rank desc,tmlast,tmfirst") {
		$ret = mysql_query("select tmfirst,tmlast from histteammemb where {$this->queryof('teamname')} order by $order");
		$result = array();
		if ($ret) {
			while ($row = mysql_fetch_array($ret)) {
				array_push($result, new HistteamMemb($this, $row[0], $row[1]));
			}
		}
		return $result;			
	}
}

function hist_list_teams($s, $div = 0, $order = "name") {
	$divsel = $div == 0? "": " and divnum=$div";
	$i = $s->Ind;
	$ret = mysql_query("select name from histteam where seasind=$i$divsel order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_array($ret)) {
			array_push($result, new Histteam($s, $row[0]));
		}
	}
	return $result;
}

function hist_max_division($s) {
	$ret = mysql_query("select max(divnum) from histteam where seasind={$s->Ind}");
	if ($ret && mysql_num_rows($ret) > 0) {
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	return 1;	
}

function hist_score_compare($teama, $teamb) {
	// Decide ordering when compiling PWDL then fall back on name order.
	if ($teama->Sortrank != $teamb->Sortrank)
		return $teama->Sortrank > $teamb->Sortrank? -1: 1;
	return strcasecmp($teama->Name, $teamb->Name);
}	
?>
