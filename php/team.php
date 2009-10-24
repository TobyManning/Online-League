<?php

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
	
	public function __construct($n = "") {
		if (strlen($n) != 0)
			$this->Name = $n;
		$this->Division = 1;
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
		$ret = mysql_query("select description,divnum,captfirst,captlast from team where $q");
		if (!$ret)
			throw new TeamException("Cannot read database for team $q");
		if (mysql_num_rows($ret) == 0)
			throw new TeamException("Cannot find team {$this->Name}");
		$row = mysql_fetch_assoc($ret);
		$this->Description = $row["description"];
		$this->Division = $row["divnum"];
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
	
	public function display_capt_email() {
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
	
	public function get_scores() {
		$this->Played = $this->get_n_from_matches("result!='N' and result!='P' and ({$this->queryof('hteam')} or {$this->queryof('ateam')})");
		$this->Won = $this->get_n_from_matches("({$this->queryof('hteam')} and result='H') or ({$this->queryof('ateam')} and result='A')");
		$this->Lost = $this->get_n_from_matches("({$this->queryof('hteam')} and result='A') or ({$this->queryof('ateam')} and result='H')");
		$this->Drawn = $this->get_n_from_matches("result='D' and ({$this->queryof('hteam')} or {$this->queryof('ateam')})");
		$this->Scoref = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(hscore)") +
							 $this->get_n_from_matches("{$this->queryof('ateam')}", "sum(ascore)");
		$this->Scorea = $this->get_n_from_matches("{$this->queryof('hteam')}", "sum(ascore)") +
							 $this->get_n_from_matches("{$this->queryof('ateam')}", "sum(hscore)");
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
			array_push($result, new team($row[0]));
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
	if ($teama->Won != $teamb->Won)
		return $teama->Won > $teamb->Won? -1: 1;
	$sa = $teama->Scoref - $teama->Scorea;
	$sb = $teamb->Scoref - $teamb->Scorea;
	if ($sa == $sb)
		return 0;
	return $sa > $sb? -1: 1;
}	
?>
