<?php

class LoginResultVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.LoginResultVO";
	
	public $playerInfo;
	public $partnerTracking;
	public $ticketId;
	public $showTour;
	public $showNewsletterScreen;
	public $promoMessageKey;
	public $gameplayPanfu;
	public $gameServers = array();
	public $date;
	public $loginCount;
	public $goldPandaDay;
	public $blockedUser;
	public $membershipStatus;
	public $email;
	public $hungryPokoPets = array();
	public $promoMembership;
	public $unreadMessagesCount;
	public $undeletedMessagesCount;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}