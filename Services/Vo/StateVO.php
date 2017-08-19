<?php

class StateVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.StateVO";
	
	public $playerId;
	public $cathegoryId;
	public $nameId;
	public $stateValue;
	public $lastChanged;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}