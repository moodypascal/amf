<?php

require_once dirname(__FILE__) . '/Vo/AmfResponse.php';
require_once dirname(__FILE__) . '/Vo/LoginResultVO.php';

class amfConnectionService {
	
	public function doLoginSession($sessionKey, $lang) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from users where auth_token = ?");
			$stmt->bindParam(1, $sessionKey, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				$player = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if ($player['tour_finished']) {
					$update = $pdo->prepare("update users set login_count = login_count + 1 where id = ?");
					$update->bindParam(1, $player['id'], PDO::PARAM_INT);
					$update->execute();
				}
				
				$_SESSION['playerId'] = $player['id'];
				
				$playerInfo = Database::getPlayerInfo($player['id']);
				$gameServers = Database::getGameServers($lang);
				
				$tLoginResult = new LoginResultVO();
				$tLoginResult->playerInfo = $playerInfo;
				$tLoginResult->ticketId = $sessionKey;
				$tLoginResult->showNewsletterScreen = false;
				$tLoginResult->gameplayPanfu = 100;
				$tLoginResult->gameServers = $gameServers;
				$tLoginResult->date = floor(microtime(true) * 1000);
				$tLoginResult->loginCount = $player['login_count'];
				$tLoginResult->goldPandaDay = true;
				$tLoginResult->membershipStatus = $player['premium'];
				$tLoginResult->email = $player['email'];
				
				$result = new AmfResponse();
				$result->statusCode = 0;
				$result->message = "success";
				$result->valueObject = $tLoginResult;
				return $result;
			}
			
			$result = new AmfResponse();
			$result->statusCode = 2;
			$result->message = "User was not found";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfConnectionService::doLoginSession\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function doLogout() {
		unset($_SESSION['playerId']);
		
		$result = new AmfResponse();
		$result->statusCode = 0;
		$result->message = "Session has been destroyed";
		$result->valueObject = null;
		return $result;
	}
	
	public function setBirthday($birthday) {
		try {
			$pdo = Database::getConnection();
			
			$update = $pdo->prepare("update users set birthday = ? where id = ?");
			$update->bindParam(1, $birthday->date, PDO::PARAM_INT);
			$update->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
			$update->execute();
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfConnectionService::setBirthday\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function ping() {
		if ($_SESSION['playerId'] !== null) {
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "ping";
			$result->valueObject = null;
			return $result;
		} else {
			$result = new AmfResponse();
			$result->statusCode = 412;
			$result->message = "Session has expired";
			$result->valueObject = null;
			return $result;
		}
	}
	
}