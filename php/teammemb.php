<?php

class TeamMembException extends Exception {}

class TeamMemb extends Player  {
	public $Team;		// A team object
	
	public function __construct($t, $f = "", $l = "") {
		parent::__construct($f, $l);
		$this->Team = $t;
	}
	
	public function create() {
		if (!mysql_query("insert into teammemb set tmfirst='{$this->queryfirst()}',tmlast='{$this->querylast()}',{$this->Team->queryof('teamname')},rank={$this->Rank->Rankvalue}"))
			throw new TeamMembException(mysql_error());
	}
}

function del_team_membs($team) {
	mysql_query("delete from teammemb where {$this->Team->queryof('teamname')}");
}
?>
