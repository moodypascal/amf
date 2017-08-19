<?php

class HomeDataVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.HomeDataVO";
	
	public $id;
	public $playerID;
	public $locked;
	public $furnitureList = array();
	public $trackList = array();
	public $pets = array();
	public $pokopets = array();
	public $bollies = array();
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}