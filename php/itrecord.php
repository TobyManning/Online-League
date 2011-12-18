<?php

class itrecord {
	public $Won;
	public $Drawn;
	public $Lost;
	public $Isself;
	
	public function __construct() {
		$this->Won = 0;
		$this->Drawn = 0;
		$this->Lost = 0;
		$this->Isself = false;
	}
	
	public function dispsc($wl) {
		if ($wl == 0  &&  $this->Drawn == 1)
			return '&frac12;';
		return  preg_replace('/\.5/', '&frac12;', $wl + 0.5 * $this->Drawn);
	}
	
	public function display() {
		if ($this->Isself)
			return "X";
		$played = $this->Won + $this->Drawn + $this->Lost;
		if ($played == 0)
			return "-";
		if ($played == 1)  {
			if ($this->Won == 1)
				return "Won";
			if ($this->Lost == 1)
				return "Lost";
			return "Drawn";
		}
		return $this->dispsc($this->Won) . "-" . $this->dispsc($this->Lost);
	}
}

?>
