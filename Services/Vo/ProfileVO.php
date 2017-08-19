<?php

class ProfileVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.ProfileVO";
	
	public $id;
	public $lastBlocked;
	public $bestFriend;
	public $movie;
	public $movieChecked;
	public $color;
	public $colorChecked;
	public $book;
	public $bookChecked;
	public $hobby;
	public $hobbyChecked;
	public $song;
	public $songChecked;
	public $band;
	public $bandChecked;
	public $schoolSubject;
	public $schoolSubjectChecked;
	public $sport;
	public $sportChecked;
	public $animal;
	public $animalChecked;
	public $relStatus;
	public $relStatusChecked;
	public $motto;
	public $mottoChecked;
	public $bestChar;
	public $bestCharChecked;
	public $worstChar;
	public $worstCharChecked;
	public $likeMost;
	public $likeMostChecked;
	public $likeLeast;
	public $likeLeastChecked;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}