<?php

require_once dirname(__FILE__) . '/../Services/Vo/DateVO.php';
require_once dirname(__FILE__) . '/../Services/Vo/GameServerVO.php';
require_once dirname(__FILE__) . '/../Services/Vo/ItemVO.php';
require_once dirname(__FILE__) . '/../Services/Vo/PlayerInfoVO.php';
require_once dirname(__FILE__) . '/../Services/Vo/SmallPlayerInfoVO.php';

class Database {
	
	public static function getConnection() {
		return new PDO("mysql:dbname=" . MYSQL_DB . ";host=" . MYSQL_HOST . ";charset=utf8",
			MYSQL_USER,
			MYSQL_PASS
		);
	}
	
	/* Database Utils */
	
	public static function addItems($id, $items) {
		try {
			$pdo = self::getConnection();
			
			$timestamp = floor(microtime(true) * 1000);
			$list = explode(',', $items);
			
			foreach($list as $item) {
				$insert = $pdo->prepare("insert into inventory (player_id, item_id, timestamp) values (?, ?, ?)");
				$insert->bindParam(1, $id, PDO::PARAM_INT);
				$insert->bindParam(2, $item, PDO::PARAM_INT);
				$insert->bindParam(3, $timestamp, PDO::PARAM_INT);
				$insert->execute();
			}
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\Database::addItems\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public static function getPlayerInfo($id) {
		try {
			$pdo = self::getConnection();
			
			$stmt = $pdo->prepare("select * from users where id = ?");
			$stmt->bindParam(1, $id, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				$player = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$diff = time() - $player['account_created'];
				
				$activeInventory = Database::getPlayerInventory($player['id']);
				$inactiveInventory = Database::getPlayerInventory($player['id'], false);
				$buddies = Database::getPlayerBuddies($player['id']);
				
				$tPlayerInfo = new PlayerInfoVO();
				$tPlayerInfo->id = $player['id'];
				$tPlayerInfo->name = $player['username'];
				$tPlayerInfo->sex = $player['gender'];
				
				if ($player['birthday']) {
					$tDate = new DateVO();
					$tDate->date = $player['birthday'];
					
					$tPlayerInfo->birthday = $tDate;
				} else {
					$tPlayerInfo->birthday = null;
				}
				
				$tPlayerInfo->coins = $player['coins'];
				$tPlayerInfo->chatId = $player['chat_id'];
				$tPlayerInfo->isPremium = boolval($player['premium']);
				$tPlayerInfo->currentGameServer = $player['current_gameserver'];
				$tPlayerInfo->socialLevel = $player['social_level'];
				$tPlayerInfo->socialScore = $player['social_score'];
				$tPlayerInfo->lastLogin = $player['last_login'];
				$tPlayerInfo->signupDate = $player['account_created'];
				$tPlayerInfo->daysOnPanfu = floor($diff / (60*60*24));
				$tPlayerInfo->helperStatus = boolval($player['helper_status']);
				$tPlayerInfo->isSheriff = boolval($player['sheriff']);
				$tPlayerInfo->isTourFinished = boolval($player['tour_finished']);
				$tPlayerInfo->state = $player['state'];
				$tPlayerInfo->membershipStatus = $player['premium'];
				$tPlayerInfo->activeInventory = $activeInventory;
				$tPlayerInfo->inactiveInventory = $inactiveInventory;
				$tPlayerInfo->buddies = $buddies;
				$tPlayerInfo->blocked = null;
				$tPlayerInfo->bollies = null;
				$tPlayerInfo->pokoPets = null;
				$tPlayerInfo->pokoPetsWithNoHealth = null;
				
				return $tPlayerInfo;
			}
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\Database::getPlayerInfo\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public static function getPlayerInventory($id, $active = true) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from inventory where player_id = ? and active = ?");
			$stmt->bindParam(1, $id, PDO::PARAM_INT);
			$stmt->bindParam(2, $active, PDO::PARAM_INT);
			$stmt->execute();
			
			$storage = array();
			
			foreach($stmt as $row) {
				$itemStmt = $pdo->prepare("select * from items where id = ?");
				$itemStmt->bindParam(1, $row['item_id'], PDO::PARAM_INT);
				$itemStmt->execute();
				$item = $itemStmt->fetch(PDO::FETCH_ASSOC);
				
				$tItem = new ItemVO();
				$tItem->id = $row['item_id'];
				$tItem->name = $item['name'];
				$tItem->type = $item['type'];
				$tItem->price = $item['price'];
				$tItem->zettSort = $item['zett_sort'];
				$tItem->premium = boolval($item['premium']);
				$tItem->bought = false;
				$tItem->active = boolval($row['active']);
				$tItem->movementType = $item['movement_type'];
				
				array_push($storage, $tItem);
			}
			
			return $storage;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\Database::getPlayerInventory\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public static function getGameServers($locale) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from servers where locale = ?");
			$stmt->bindParam(1, $locale, PDO::PARAM_STR);
			$stmt->execute();
			
			$storage = array();
			
			foreach($stmt as $row) {
				$tGameServer = new GameServerVO();
				$tGameServer->id = $row['id'];
				$tGameServer->name = $row['name'];
				$tGameServer->playercount = $row['playercount'];
				$tGameServer->url = $row['url'];
				$tGameServer->port = $row['port'];
				$tGameServer->ageFrom = 0;
				$tGameServer->ageTo = 99;
				$tGameServer->premiumonly = boolval($row['premium_only']);
				$tGameServer->availableFor = 0;
				
				array_push($storage, $tGameServer);
			}
			
			return $storage;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\Database::getGameServers\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public static function getPlayerBuddies($id) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from buddies where player_id = ?");
			$stmt->bindParam(1, $id, PDO::PARAM_INT);
			$stmt->execute();
			
			$storage = array();
			
			foreach($stmt as $row) {
				$buddyStmt = $pdo->prepare("select * from users where id = ?");
				$buddyStmt->bindParam(1, $row['buddy_id'], PDO::PARAM_INT);
				$buddyStmt->execute();
				$buddy = $buddyStmt->fetch(PDO::FETCH_ASSOC);
				
				$tPlayerInfo = new SmallPlayerInfoVO();
				$tPlayerInfo->playerId = $row['buddy_id'];
				$tPlayerInfo->playerName = $buddy['username'];
				$tPlayerInfo->currentGameServer = $buddy['current_gameserver'];
				
				array_push($storage, $tPlayerInfo);
			}
			
			return $storage;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\Database::getPlayerBuddies\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
}
