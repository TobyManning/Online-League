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

//  Sort an array of players by descending rank - stably
//  Use bubble sort

function sortrank($arr) {
	if ($arr[0]->Rank->Rankvalue < $arr[1]->Rank->Rankvalue) {
		$t = $arr[0];
		$arr[0] = $arr[1];
		$arr[1] = $t;
	}
	if ($arr[1]->Rank->Rankvalue < $arr[2]->Rank->Rankvalue) {
		$t = $arr[1];
		$arr[1] = $arr[2];
		$arr[2] = $t;
	}
	if ($arr[0]->Rank->Rankvalue < $arr[1]->Rank->Rankvalue) {
		$t = $arr[0];
		$arr[0] = $arr[1];
		$arr[1] = $t;
	}
}

?>
