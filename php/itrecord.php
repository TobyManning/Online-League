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
	
	public function display() {
		if ($this->Isself)
			return "X";
		$sf = $this->Won + 0.5 * $this->Drawn;
		$sa = $this->Lost + 0.5 * $this->Drawn;
		if ($sf == 0 && $sa == 0)
			return "-";
		return "$sf-$sa";
	}
}

?>
