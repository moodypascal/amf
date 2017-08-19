<?php

class UserActionDailyVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.UserActionDailyVO";
	
	public $playerId;
	public $actionId;
	public $doneToday;
	public $time;
	public $doneInTime;
	public $lastDoneActionTime;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}