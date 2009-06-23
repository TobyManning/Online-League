<?php

class Rank {
	public $Rankvalue;
	
	public function __construct($r=0) {
		$this->Rankvalue = $r;
	}

	//  Turn rank value into display string
		
	public function display() {
		if ($this->Rankvalue >= 0) {
			$r = $this->Rankvalue + 1;
			return $r . "D";
		}
		else {
			$r = - $this->Rankvalue;
			return $r . "K";
		}
	}

	// This is for anchors based on rank - add 20
		
	public function anchor() {
		$r = $this->Rankvalue + 20;
		return "R" . $r;
	}

	// Compare functions
		
	public function equals($other) {
		return $this->Rankvalue == $other->Rankvalue;
	}
	
	public function notequals($other) {
		return $this->Rankvalue != $other->Rankvalue;
	}

	//  Generate selector list for forms
	//  NB this is a function which generates output
	//  Don't use it embedded in a string!!!
		
	public function rankopt() {
		print "<select name=\"rank\">\n";
		for ($r = 8;  $r >= 0;  $r--)  {
			$rn = $r+1;
			$rn = "$rn Dan";
			if ($r == $this->Rankvalue)
				print "<option value=$r selected>$rn</option>\n";
			else
				print "<option value=$r>$rn</option>\n";
		}
		for ($r = -1;  $r >= -20;  $r--)  {
			$rn = -$r;
			$rn = "$rn Kyu";
			if ($r == $this->Rankvalue)
				print "<option value=$r selected>$rn</option>\n";
			else
				print "<option value=$r>$rn</option>\n";
		}
		print "</select>\n";
	}
}
?>
