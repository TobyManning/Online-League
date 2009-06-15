<?php

class Matchdate {
	private $timestamp;
			
	public function __construct() {
		$dat = getdate();
		$this->timestamp = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
	}
	
	public function enctime($ds) {
		$yr = substr($ds, 0, 4);
		$mn = substr($ds, 5, 2);
		$dy = substr($ds, 8);
		$this->timestamp = mktime(12,0,0,$mn,$dy,$yr);
	}

	public function fromget()  {
		$this->enctime($_GET["md"]);
	}
	
	public function frompost()  {
		$yr = $_POST["year"];
		$mn = $_POST["month"];
		$dy = $_POST["day"];
		$this->timestamp = mktime(12,0,0,$mn,$dy,$yr);
	}
	
	public function fromhidden($prefix = "") {
		$this->enctime($_POST["{$prefix}md"]);
	}
	
	public function fromtabrow($mysqlrow) {
		$this->enctime($mysqlrow["matchdate"]);
	}
	
	public function display() {
		return date("D j M Y", $this->timestamp);
	}
	
	public function urlof() {
		$u = date("Y-m-d", $this->timestamp);
		return "md=$u";
	}
	
	public function queryof() {
		return date("Y-m-d", $this->timestamp);
	}
	
	public function unequal($d) {
		return $this->timestamp != $d->timestamp;
	}
	
	public function is_past() {
		$dat = getdate();
		$now = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
		return $now >= $this->timestamp;
	}
	
	public function is_future() {
		$dat = getdate();
		$now = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
		return $now < $this->timestamp;
	}
		
	public function season()  {
		$times = getdate($this->timestamp);
		$y = $times["year"];
		$sstart = mktime(12, 0, 0, 9, 1, $y);
		if  ($sstart <= $this->timestamp)
			$y++;
		return  $y;	
	}
	
	public function monthstart() {
		$times = getdate($this->timestamp);
		return  mktime(1, 0, 0, $times["mon"], 1, $times["year"]);
	} 

	public function yropt() {
		$dat = getdate($this->timestamp);
		$yrsel = $dat["year"];
		print "<select name=\"year\">\n";	
		for ($i = 2008;  $i <= 2011;  $i++) {
			if ($i == $yrsel)
				print "<option selected>$i</option>\n";
			else
				print "<option>$i</option>\n";
		}
		print "</select>\n";
	}
	
	public function monopt()
	{
		$dat = getdate($this->timestamp);
		$monsel = $dat["mon"];
		$Mnames = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		print "<select name=\"month\">\n";
		for ($i = 1;  $i <= 12; $i++) {
			if ($i == $monsel)
				print "<option value=$i selected>";
			else
				print "<option value=$i>";
			print $Mnames[$i-1];
			print "</option>\n";
		}
		print "</select>\n";
	}
	
	public function dayopt()
	{
		$dat = getdate($this->timestamp);
		$daysel = $dat["mday"];
		print "<select name=\"day\">\n";
		for ($i = 1;  $i <= 31; $i++) {
			if ($i == $daysel)
				print "<option selected>$i</option>\n";
			else
				print "<option>$i</option>\n";
		}
		print "</select>\n";
	}

	public function dateopt()
	{
		print "Date:";
		$this->dayopt();
		$this->monopt();
		$this->yropt();
	}
	
	public function disphidden($prefix = "")
	{
		$hd = date("Y-m-d", $this->timestamp);
		return  "<input type=\"hidden\" name=\"${prefix}md\" value=\"$hd\">\n";
	}
	
	public function hidden()
	{
		print $this->disphidden();
	}
	
	public function haschanged($omd)
	{
		return $this->timestamp != $omd->timestamp;
	}
}

?>
