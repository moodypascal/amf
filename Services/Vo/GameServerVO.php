<?php

class GameServerVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.GameServerVO";
	
	public $id;
	public $name;
	public $playercount;
	public $url;
	public $port;
	public $ageFrom;
	public $ageTo;
	public $premiumonly;
	public $availableFor;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}