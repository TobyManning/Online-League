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

class TeamException extends Exception {}

class Team  {
	public $Captain;		// A player object
	public $Name;			// Team short name
	public $Description;	// Team full name
	public $Division;		// League division
	public $Played;		// Played matches
	public $Won;			// Won matches
	public $Drawn;			// Drawn matches
	public $Lost;			// Lost matches
	public $Scoref;		// Scores for
	public $Scorea;		// Scores against
	public $Paid;			// Paid
	public $Sortrank;		// Ranking for league sort
	
	public function __construct($n = "") {
		if (strlen($n) != 0)
			$this->Name = $n;
		$this->Division = 1;
		$this->Sortrank = 0;
	}
	
	public function fromget() {
		$this->Name = $_GET["tn"];
		if (strlen($this->Name) == 0)
			throw new TeamException("Null name field"); 
	}

	public function frompost($prefix = "") {
		$this->Name = $_POST["${prefix}tn"];
		if (strlen($this->Name) == 0)
			throw new TeamException("Null post name field"); 
	}				
	
	public function queryof($colname = "name") {
		$qn = mysql_real_escape_string($this->Name);
		return "$colname='$qn'";
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
		return "tn=$n";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select description,divnum,captfirst,captlast,paid from team where $q");
		if (!$ret)
			throw new TeamException("Cannot read database for team $q");
		if (mysql_num_rows($ret) == 0)
			throw new TeamException("Cannot find team {$this->Name}");
		$row = mysql_fetch_assoc($ret);
		$this->Description = $row["description"];
		$this->Division = $row["divnum"];
		$this->Paid = $row["paid"];
		try {
			$this->Captain = new Player($row["captfirst"], $row["captlast"]);
			$this->Captain->fetchdets();
		}
		catch (PlayerException $e) {
			$this->Captain = new Player("Unknown", "Captain");
		}
	}
	
	public function is_same($tm) {
		return $this->Name == $tm->Name;
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
	
	public function display_captain() {
		return $this->Captain->display_name();
	}
	
	public function display_capt_email($l = true) {
		if (!$l)
			return "";
		$m = $this->Captain->display_email();
		if (strlen($m) < 2)
			return "";
		return $m;
	}
	
	public function save_hidden($prefix = "") {
		$f = $this->Name;
		return "<input type=\"hidden\" name=\"${prefix}tn\" value=\"$f\">";
	}
	
	public function create() {
		$qname = mysql_real_escape_string($this->Name);
		$qdescr = mysql_real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!mysql_query("insert into team (name,description,divnum,captfirst,captlast) values ('$qname','$qdescr',$qdiv,'$qcfirst','$qclast')"))
			throw new TeamException(mysql_error());
	}
	
	public function updatename($newt) {
		$qname = mysql_real_escape_string($newt->Name);
		mysql_query("update team set name='$qname' where {$this->queryof()}");
		// Need to change team in teammemb as well
		mysql_query("update teammemb set teamname='$qname' where {$this->queryof('teamname')}");
		// Also reset any matches
		mysql_query("update lgmatch set hteam='$qname' where {$this->queryof('hteam')}");
		mysql_query("update lgmatch set ateam='$qname' where {$this->queryof('ateam')}");
		// And games
		mysql_query("update game set wteam='$qname' where {$this->queryof('wteam')}");
		mysql_query("update game set bteam='$qname' where {$this->queryof('bteam')}");
		$this->Name = $newt->Name;
	}

	public function update() {
		$qdescr = mysql_real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!mysql_query("update team set description='$qdescr',divnum=$qdiv,captfirst='$qcfirst',captlast='$qclast' where {$this->queryof()}"))
			throw new TeamException(mysql_error());
	}
	
	// Update division only for when we've not read in the lot
	
	public function updatediv($newdiv) {
		if (!mysql_query("update team set divnum=$newdiv where {$this->queryof()}"))
			throw new TeamException(mysql_error());
		$this->Division = $newdiv;		
	}
	
	public function setpaid($v = true) {
		$vv = $v? 1: 0;
		mysql_query("update team set paid=$vv where {$this->queryof()}");
	}
	
	public function divopt() {
		print "<select name=\"division\">\n";
		$maxt = max_division() + 1; // Allow for 1 more than number of existing
		for ($d = 1;  $d <= $maxt;  $d++)  {
			if ($d == $this->Division)
				print "<option selected>$d</option>\n";
			else
				print "<option>$d</option>\n";
		}
		print "</select>\n";
	}
	
	public function captainopt() {
		$plist = list_players();
		print "<select name=\"captain\">\n";
		foreach ($plist as $p) {
			$v = $p->selof();
			if ($p->is_same($this->Captain))
				print "<option value=\"$v\" selected>{$p->display_name()}</option>\n";
			else
				print "<option value=\"$v\">{$p->display_name()}</option>\n";
		}
		print "</select>\n";
	}
	
	public function get_n_from_matches($crit, $wot="count(*)") {
		$ret = mysql_query("select $wot from lgmatch where $crit");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function get_scores($p = Null) {
		$this->Played = $this->get_n_from_matches("result!='N' and result!='P' and ({$this->queryof('hteam')} or {$this->queryof('ateam')})");
		$this->Won = $this->get_n_from_matches("({$this->queryof('hteam')} and result='H') or ({$this->queryof('ateam')} and result='A')");
		$this->Lost = $this->get_n_from_matches("({$this->queryof('hteam')} and result='A') or ({$this->queryof('ateam')} and result='H')");
		$this->Drawn = $this->get_n_from_matches("result='D' and ({$this->queryof('hteam')} or {$this->queryof('ateam')})");
		$this->Scoref = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(hscore)") +
							 $this->get_n_from_matches("{$this->queryof('ateam')}", "sum(ascore)");
		$this->Scorea = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(ascore)") +
							 $this->get_n_from_matches("{$this->queryof('ateam')}", "sum(hscore)");
		if ($p)
			$this->Sortrank = $this->Played * $p->Played +
									$this->Won * $p->Won +
									$this->Drawn * $p->Drawn +	
									$this->Lost * $p->Lost +
									$this->Scoref * $p->For +
									$this->Scorea * $p->Against;
	}
	
	public function count_members() {
		$ret = mysql_query("select count(*) from teammemb where {$this->queryof('teamname')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function list_members($order = "rank desc,tmlast,tmfirst") {
		$ret = mysql_query("select tmfirst,tmlast from teammemb where {$this->queryof('teamname')} order by $order");
		$result = array();
		if ($ret) {
			while ($row = mysql_fetch_array($ret)) {
				array_push($result, new TeamMemb($this, $row[0], $row[1]));
			}
		}
		return $result;			
	}
}

function list_teams($div = 0, $order = "name") {
	$divsel = $div == 0? "": " where divnum=$div";
	$ret = mysql_query("select name from team$divsel order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_array($ret)) {
			array_push($result, new Team($row[0]));
		}
	}
	return $result;
}

function max_division() {
	$ret = mysql_query("select max(divnum) from team");
	if ($ret && mysql_num_rows($ret) > 0) {
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	return 1;	
}

function score_compare($teama, $teamb) {
	// Decide ordering when compiling PWDL then fall back on name order.
	if ($teama->Sortrank != $teamb->Sortrank)
		return $teama->Sortrank > $teamb->Sortrank? -1: 1;
	return strcasecmp($teama->Name, $teamb->Name);
}	
?>
