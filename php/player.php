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

	// Construct a player object, possibly starting from various
	// versions of the name
		
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
			$this->Rank = new Rank();
	}

	// Fill in the name of the player from a "get" request
		
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

	// Use me to get the player we are talking about from a hidden field
	// We'll still perhaps need to get the rest
	
	public function frompost($prefix = "") {
		$this->First = $_POST["${prefix}f"];
		$this->Last = $_POST["${prefix}l"];
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new PlayerException("Null post name field"); 
	}
	
	// Use me to get details starting from userid
	
	public function fromid($id) {
		$qid = mysql_real_escape_string($id);
		$ret = mysql_query("select first,last,rank,club,email,kgs,igs,admin from player where user='$qid'");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new PlayerException("Unknown player userid $id");
		$row = mysql_fetch_assoc($ret);
		$this->First = $row['first'];
		$this->Last = $row['last'];
		$this->Rank = new Rank($row["rank"]);
		$this->Club = new Club($row["club"]);
		$this->Email = $row["email"];
		$this->KGS = $row["kgs"];
		$this->IGS = $row["igs"];
		$this->Admin = $row["admin"];
		$this->Userid = $id;
	}

	// Generate a MySQL query from a player object
		
	public function queryof($prefix = "") {
		$qf = mysql_real_escape_string($this->First);
		$ql = mysql_real_escape_string($this->Last);
		return "${prefix}first='$qf' and ${prefix}last='$ql'";
	}

	// For when we just want the MySQL rendering of the First name
		
	public function queryfirst() {
		return mysql_real_escape_string($this->First);
	}

	// Ditto last name
		
	public function querylast() {
		return mysql_real_escape_string($this->Last);
	}

	// For packaging up a name as a search string
		
	public function urlof() {
		$f = urlencode($this->First);
		$l = urlencode($this->Last);
		return "f=$f&l=$l";
	}

	// For packaging up a name in a selection field
		
	public function selof() {
		$f = $this->First;
		$l = $this->Last;
		return "$f:$l";
	}

	// For undoing the above
		
	public function fromsel($pl) {
		if  (!preg_match("/(.*):(.*)/", $pl, $matches))
			throw new PlayerException("Invalid player selection");
		$this->First = $matches[1];
		$this->Last = $matches[2];
	}

	// Get the rest of the details having got the name
		
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

	// Get more info about the club
		
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

	// Are we talking about same player
		
	public function is_same($pl) {
		return $this->First == $pl->First && $this->Last == $pl->Last;
	}

	// Prepare first name for display
		
	public function display_first() {
		return htmlspecialchars($this->First);
	}

	// Prepare last name for display
	
	public function display_last() {
		return htmlspecialchars($this->Last);
	}

	// Display whole name
		
	public function display_name() {
		$f = $this->First;
		$l = $this->Last;
		return htmlspecialchars("$f $l");
	}

	// Display rank in standard format
		
	public function display_rank() {
		return $this->Rank->display();
	}

	// Get initial letter of last name
		
	public function get_initial() {
		return strtoupper(substr($this->Last, 0, 1));
	}

	// Get initial letter of club name
		
	public function get_club_initial() {
		return strtoupper(substr($this->Club->Name, 0, 1));
	}

	// Get KGS handle
		
	public function display_kgs() {
		return htmlspecialchars($this->KGS);
	}

	// Get IGS handlie
	
	public function display_igs() {
		return htmlspecialchars($this->IGS);
	}

	// Display KGS and IGS handles hopefully optimally
	
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

	// Display user id
		
	public function display_userid($wminus=1) {
		if ($wminus && strlen($this->Userid) == 0)
			return "-";
		return htmlspecialchars($this->Userid);
	}

	// Get password
		
	public function get_passwd() {
		$ret = mysql_query("select password from player where {$this->queryof()}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  "";
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}

	// Get password for "display"
		
	public function disp_passwd() {
		return htmlspecialchars($this->get_passwd());
	}

	// Set password
		
	public function set_passwd($pw)  {
		$qpw = mysql_real_escape_string($pw);
		mysql_query("update player set password='$qpw' where {$this->queryof()}");
	}
	
	// Display link to send email
	
	public function display_email() {
		if (strlen($this->Email) == 0)
			return "-";
		return "<a href=\"sendmail.php?{$this->urlof()}\" target=\"_blank\">Send email</a>";
	}

	// Display email address
		
	public function display_email_nolink() {
		return htmlspecialchars($this->Email);
	}

	// Identify player as hidden item in a form
		
	public function save_hidden($prefix = "") {
		$f = $this->First;
		$l = $this->Last;
		return "<input type=\"hidden\" name=\"${prefix}f\" value=\"$f\"><input type=\"hidden\" name=\"${prefix}l\" value=\"$l\">";
	}

	// Display club as a selection
		
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

	// Display rank as a selection
		
	public function rankopt() {
		$this->Rank->rankopt();
	}

	// Display admin priv as a selection
		
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

	// Add player record to database
		
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

	// Update player record of name
		
	public function updatename($newp) {
		$qfirst = mysql_real_escape_string($newp->First);
		$qlast = mysql_real_escape_string($newp->Last);
		mysql_query("update player set first='$qfirst',last='$qlast' where {$this->queryof()}");
		// Update any club which this player is the contact name for
		mysql_query("update club set contactfirst='$qfirst',contactlast='$qlast' where {$this->queryof('contact')}");
		// Update any team which this player is the captain of
		mysql_query("update team set captfirst='$qfirst',captlast='$qlast' where {$this->queryof('capt')}");
		// Likewise any team memberships
		mysql_query("update teammemb set tmfirst='$qfirst',tmlast='$qlast' where {$this->queryof('tm')}");
		// Any games as White
		mysql_query("update game set wfirst='$qfirst',wlast='$qlast' where {$this->queryof('w')}");
		// And as black
		mysql_query("update game set bfirst='$qfirst',blast='$qlast' where {$this->queryof('b')}");
		$this->First = $newp->First;
		$this->Last = $newp->Last;
	}
	
	// Update player record
	
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

	// MySQL juggling to get Played/Won/Drawn/Lost	
	private function get_grec($query) {
		$ret = mysql_query("select count(*) from game where $query");
		if  (!$ret || mysql_num_rows($ret) == 0)
			return  0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	// Get Played/Won/Drawn/Lost
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
	
	// Count teams this player is a member of
	
	public function count_teams() {
		$ret = mysql_query("select count(*) from teammemb where {$this->queryof('tm')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}		 	
}

// List all players in specified order

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

// Get a list of initials

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

// List of all ranks people are

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
