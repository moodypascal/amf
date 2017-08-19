<?php

class BuddyVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.BuddyVO";
	
	public $ID;
	public $name;
	public $premium;
	public $bestfriend;
	public $currentGameServer;
	public $socialLevel;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}