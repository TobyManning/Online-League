<?php

class TeamException extends Exception {}

class Team  {
	public $Captain;		// A player object
	public $Name;			// Team short name
	public $Description;	// Team full name
	public $Division;		// League division
	
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
			throw new TeamException("Cannot find team");
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
	
	public function display_captain() {
		return $this->Captain->display_name();
	}
	
	public function save_hidden($prefix = "") {
		$f = $this->Name;
		return "<input type=\"hidden\" name=\"${prefix}tm\" value=\"$f\">";
	}
	
	public function create() {
		$qname = mysql_real_escape_string($this->Name);
		$qdescr = mysql_real_escape_string($this->Description);
		$qcfirst = $this->Captain->queryfirst();
		$qclast = $this->Captain->querylast();
		$qdiv = $this->Division;
		if (!mysql_query("insert into team (name,description,division,captfirst,captlast) values ('$qname','$qdescr',$qdiv,'$qcfirst','$qclast')"))
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
		if (!mysql_query("update team set description='$qdescr',division=$qdiv,captfirst='$qcfirst',captlast='$qclast' where {$this->queryof()}"))
			throw new TeamException(mysql_error());
	}
	
	public function divopt() {
		print "<select name=\"division\">\n";
		for ($d = 1;  $d < 7;  $d++)  {
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
	
	// public function won_matches() {
	//}
	
	// public function lost_matches() {
	//}

	// public function drawn_matches() {
	//}
	
	// public function played_matches() {
	//}		 	
}

function list_teams($order = "name") {
	$ret = mysql_query("select name from team order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_array($ret)) {
			array_push($result, new team($row[0]));
		}
	}
	return $result;
}	
?>
