<?php

class PlayerException extends Exception {}

class Player  {
	protected $First;
	protected $Last;
	public $Rank;
	public $Club;
	public $Email;
	public $KGS;
	public $IGS;
	public $Admin;
	public $Userid;
	private $Gotrecs;
	private $Played;
	private $Won;
	private $Drawn;
	private $Lost;
	
	public function __construct($f = "", $l = "") {
		if (strlen($f) != 0)  {
			if (strlen($l) != 0) {
				$this->First = $f;
				$this->Last = $l;
			}
			elseif (preg_match("/(.*)\s+(.+)/", $f, $matches))  {
				$this->First = $matches[1];
				$this->Last = $matches[2];
			}
			else
				throw new PlayerException("Cannot parse name");
			}
			$Gotrecs = false;
	}
	
	public function fromget($prefix = "", $htd = false) {
		$this->First = $_GET["${prefix}f"];
		$this->Last = $_GET["${prefix}l"];
		if ($htd) {
			$this->First = htmlspecialchars_decode($this->First);
			$this->Last = htmlspecialchars_decode($this->Last);
		}
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new PlayerException("Null name field"); 
	}

	public function frompost($prefix = "") {
		$this->First = $_POST["${prefix}f"];
		$this->Last = $_POST["${prefix}l"];
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new PlayerException("Null post name field"); 
	}				
	
	public function queryof($prefix = "") {
		$qf = mysql_real_escape_string($this->First);
		$ql = mysql_real_escape_string($this->Last);
		return "${prefix}first='$qf' and ${prefix}last='$ql'";
	}
	
	public function queryfirst() {
		return mysql_real_escape_string($this->First);
	}
	
	public function querylast() {
		return mysql_real_escape_string($this->Last);
	}
	
	public function urlof() {
		$f = urlencode($this->First);
		$l = urlencode($this->Last);
		return "f=$f&l=$l";
	}
	
	public function selof() {
		$f = $this->First;
		$l = $this->Last;
		return "$f:$l";
	}
	
	public function fromsel($pl) {
		if  (!preg_match("/(.*):(.*)/", $pl, $matches))
			throw new PlayerException("Invalid player selection");
		$this->First = $matches[1];
		$this->Last = $matches[2];
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select rank,club,email,kgs,igs,admin,user from player where $q");
		if (!$ret)
			throw new PlayerException("Cannot read database for player $q");
		if (mysql_num_rows($ret) == 0)
			throw new PlayerException("Cannot find player");
		$row = mysql_fetch_assoc($ret);
		$this->Rank = new Rank($row["rank"]);
		$this->Club = new Club($row["club"]);
		$this->Email = $row["email"];
		$this->KGS = $row["kgs"];
		$this->IGS = $row["igs"];
		$this->Admin = $row["admin"];
		$this->Userid = $row["user"];
	}
	
	public function fetchclub() {
		try {
			$this->Club->fetchdets();
		}
		catch (ClubException $e) {
			// If unknown club set to No club
			$this->Club = new Club('xxx');
			$this->Club->fetchdets();
		}
	}
	
	public function is_same($pl) {
		return $this->First == $pl->First && $this->Last == $pl->Last;
	}
	
	public function display_first() {
		return htmlspecialchars($this->First);
	}

	public function display_last() {
		return htmlspecialchars($this->Last);
	}
	
	public function display_name() {
		$f = $this->First;
		$l = $this->Last;
		return htmlspecialchars("$f $l");
	}
	
	public function display_rank() {
		return $this->Rank->display();
	}
	
	public function get_initial() {
		return strtoupper(substr($this->Last, 0, 1));
	}
	
	public function get_club_initial() {
		return strtoupper(substr($this->Club->Name, 0, 1));
	}
	
	public function display_kgs() {
		return htmlspecialchars($this->KGS);
	}

	public function display_igs() {
		return htmlspecialchars($this->IGS);
	}

	public function display_online() {
		$k = $this->KGS;
		$i = $this->IGS;
		if ($k == $i) {
			if ($k == "")
				$online = "-";
			else
				$online = $k;
		}
		else  {
			if ($k != "") {
				if ($i != "")
					$online = "KGS:$k IGS:$i";
				else
					$online = "KGS:$k";
			}
			else
				$online = "IGS:$i";
		}
		return htmlspecialchars($online);
	}
	
	public function display_userid($wminus=1) {
		if ($wminus && strlen($this->Userid) == 0)
			return "-";
		return htmlspecialchars($this->Userid);
	}
	
	public function display_email() {
		if (strlen($this->Email) == 0)
			return "-";
		return "<a href=\"sendmail.php?{$this->urlof()}\" target=\"_blank\">Send email</a>";
	}
	
	public function display_email_nolink() {
		return htmlspecialchars($this->Email);
	}
	
	public function save_hidden($prefix = "") {
		$f = $this->First;
		$l = $this->Last;
		return "<input type=\"hidden\" name=\"${prefix}f\" value=\"$f\"><input type=\"hidden\" name=\"${prefix}l\" value=\"$l\">";
	}
	
	public function clubopt() {
		$clubs = listclubs();
		print "<select name=\"club\">\n";
		foreach ($clubs as $club) {
			$code = $club->Code;
			$name = $club->Name;
			if ($code == $this->Club->Code)
				print "<option value=\"$code\" selected>$name</option>\n";
			else
				print "<option value=\"$code\">$name</option>\n";
		}
		print "</select>\n";
	}
	
	public function rankopt() {
		$this->Rank->rankopt();
	}
	
	public function adminopt() {
		print "<select name=\"admin\">\n";
		$poss = array('N', 'A', 'SA');
		foreach ($poss as $pa) {
			if ($this->Admin == $pa)
				print "<option selected>$pa</option>\n";
			else
				print "<option>$pa</option>\n";
		}
		print "</select>\n";	
	}
	
	public function create() {
		$qfirst = mysql_real_escape_string($this->First);
		$qlast = mysql_real_escape_string($this->Last);
		$qclub = mysql_real_escape_string($this->Club->Code);
		$quser = mysql_real_escape_string($this->Userid);
		$qadmin = mysql_real_escape_string($this->Admin);
		$qemail = mysql_real_escape_string($this->Email);
		$qkgs = mysql_real_escape_string($this->KGS);
		$qigs = mysql_real_escape_string($this->IGS);
		$r = $this->Rank->Rankvalue;
		mysql_query("insert into player (first,last,rank,club,user,kgs,igs,email,admin) values ('$qfirst','$qlast',$r,'$qclub','$quser','$qkgs','$qigs','$qemail','$qadmin')");
	}
	
	public function updatename($newp) {
		$qfirst = mysql_real_escape_string($newp->First);
		$qlast = mysql_real_escape_string($newp->Last);
		mysql_query("update player set first='$qfirst',last='$qlast' where {$this->queryof()}");
		$this->First = $newp->First;
		$this->Last = $newp->Last;
	}
	
	public function update() {
		$qclub = mysql_real_escape_string($this->Club->Code);
		$quser = mysql_real_escape_string($this->Userid);
		$qadmin = mysql_real_escape_string($this->Admin);
		$qemail = mysql_real_escape_string($this->Email);
		$qkgs = mysql_real_escape_string($this->KGS);
		$qigs = mysql_real_escape_string($this->IGS);
		$r = $this->Rank->Rankvalue;
		mysql_query("update player set club='$qclub',user='$quser',admin='$qadmin',email='$qemail',kgs='$qkgs',igs='$qigs',rank=$r where {$this->queryof()}");
		// Fix rank in teams that this player is a member of
		mysql_query("update teammemb set rank=$r where {$this->queryof('tm')}");
		// Fix rank in unplayed games where this player is black
		mysql_query("update game set brank=$r where result='N' and {$this->queryof('b')}");
		// Ditto for where this player is white
		mysql_query("update game set wrank=$r where result='N' and {$this->queryof('w')}");
	}
	
	private function get_grec($query) {
		$ret = mysql_query("select count(*) from game where $query");
		if  (!$ret || mysql_num_rows($ret) == 0)
			return  0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	private function get_grecs()  {
		if  ($this->Gotrecs)
			return;
		$this->Gotrecs = true;
		// Get SQL to do all the work
		$this->Played = $this->get_grec("result!='N' and ({$this->queryof('w')} or {$this->queryof('b')})");
		$this->Won = $this->get_grec("({$this->queryof('w')} and result='W') or ({$this->queryof('b')} and result='B')");
		$this->Drawn = $this->get_grec("result='J' and ({$this->queryof('w')} or {$this->queryof('b')})");
		$this->Lost = $this->get_grec("({$this->queryof('w')} and result='B') or ({$this->queryof('b')} and result='W')");
	}
	
	public function won_games() {
		$this->get_grecs();
		return  $this->Won;
	}
	
	public function lost_games() {
		$this->get_grecs();
		return  $this->Lost;
	}

	public function drawn_games() {
		$this->get_grecs();
		return  $this->Drawn;
	}
	
	public function played_games() {
		$this->get_grecs();
		return  $this->Played;
	}
	
	public function count_teams() {
		$ret = mysql_query("select count(*) from teammemb where {$this->queryof('tm')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}		 	
}

function list_players($order = "last,first,rank desc") {
	$ret = mysql_query("select first,last from player order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_assoc($ret)) {
			array_push($result, new player($row['first'], $row['last']));
		}
	}
	return $result;
}

function list_player_initials() {
	$ret = mysql_query("select last from player order by last");
	$result = array();
	if  ($ret)  {
		$li = "none";
		while ($row = mysql_fetch_array($ret)) {
			$ni = strtoupper(substr($row[0], 0, 1));
			if ($ni != $li) {
				array_push($result, $ni);
				$li = $ni;
			}
		}
	}
	return $result;
}

function list_player_ranks() {
	$ret = mysql_query("select rank from player group by rank order by rank desc");
	$result = array();
	if  ($ret)  {
		while ($row = mysql_fetch_array($ret)) {
			array_push($result, $row[0]);
		}
	}
	return $result;
}	
?>
