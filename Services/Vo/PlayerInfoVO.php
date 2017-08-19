<?php

class PlayerInfoVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.PlayerInfoVO";
	
	public $id;
	public $name;
	public $age;
	public $sex;
	public $birthday;
	public $coins;
	public $chatId;
	public $isPremium;
	public $isGuest;
	public $currentGameServer;
	public $socialLevel;
	public $socialScore;
	public $lastLogin;
	public $signupDate;
	public $daysOnPanfu;
	public $helperStatus;
	public $lastSeenACGlobal;
	public $isSheriff;
	public $isTourFinished;
	public $state;
	public $membershipStatus;
	public $activeInventory = array();
	public $inactiveInventory = array();
	public $buddies = array();
	public $blocked = array();
	public $bollies = array();
	public $musicCollection = array();
	public $pokoPets = array();
	public $pokoPetsWithNoHealth = array();
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}