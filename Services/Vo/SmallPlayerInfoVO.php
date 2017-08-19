<?php

class SmallPlayerInfoVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.SmallPlayerInfoVO";
	
	public $playerId;
	public $playerName;
	public $currentGameServer;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}