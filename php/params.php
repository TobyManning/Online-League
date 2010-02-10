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

class ParamException extends Exception {}

class Params  {
	public $Played;
	public $Won;
	public $Drawn;
	public $Lost;
	public $For;
	public $Against;

	public function __construct() {
		$Played = 0;
		$Won = 100;
		$Drawn = 50;
		$Lost = 0;
		$For = 1;
		$Against = 0;
	}
	
	public function fetchvalues() {
		$ret = mysql_query("select sc,val from params");
		if (!$ret)
			throw new ParamException(mysql_error());
		while ($row = mysql_fetch_assoc($ret)) {
			switch ($row["sc"])  {
			case 'p':
				$this->Played = $row["val"];
				break;
			case 'w':
				$this->Won = $row["val"];
				break;
			case 'd':
				$this->Drawn = $row["val"];
				break;
			case 'l':
				$this->Lost = $row["val"];
				break;
			case 'f':
				$this->For = $row["val"];
				break;
			case 'a':
				$this->Against = $row["val"];
				break;
			}
		}
	}
	
	public function putvalues() {
		if (!mysql_query("delete from params"))
			throw new ParamException(mysql_error());
		mysql_query("insert into params (sc,val) values ('p', $this->Played)");
		mysql_query("insert into params (sc,val) values ('w', $this->Won)");
		mysql_query("insert into params (sc,val) values ('d', $this->Drawn)");
		mysql_query("insert into params (sc,val) values ('l', $this->Lost)");
		mysql_query("insert into params (sc,val) values ('f', $this->For)");
		mysql_query("insert into params (sc,val) values ('a', $this->Against)");
	}
}
?>
