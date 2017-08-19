<?php

require_once dirname(__FILE__) . '/Vo/AmfResponse.php';
require_once dirname(__FILE__) . '/Vo/FurnitureDataVO.php';
require_once dirname(__FILE__) . '/Vo/HomeDataVO.php';
require_once dirname(__FILE__) . '/Vo/InventoryVO.php';
require_once dirname(__FILE__) . '/Vo/ItemVO.php';
require_once dirname(__FILE__) . '/Vo/ListVO.php';
require_once dirname(__FILE__) . '/Vo/StateVO.php';

class amfPlayerService {
	
	public function addToBuddyList($playerId) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from buddies where (player_id = ? and buddy_id = ?) or (player_id = ? and buddy_id = ?)");
			$stmt->bindParam(1, $playerId, PDO::PARAM_INT);
			$stmt->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
			$stmt->bindParam(4, $playerId, PDO::PARAM_INT);
			$stmt->bindParam(3, $_SESSION['playerId'], PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() == 0) {
				$timestamp = time();
				
				$insert = $pdo->prepare("insert into buddies values (?, ?, ?)");
				
				$insert->bindParam(1, $playerId, PDO::PARAM_INT);
				$insert->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
				$insert->bindParam(3, $timestamp, PDO::PARAM_INT);
				$insert->execute();
				
				$insert->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
				$insert->bindParam(2, $playerId, PDO::PARAM_INT);
				$insert->bindParam(3, $timestamp, PDO::PARAM_INT);
				$insert->execute();
			}
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::addToBuddyList\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function purchaseItem($itemId, $hash) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from items where id = ?");
			$stmt->bindParam(1, $itemId, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				$item = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$checkStmt = $pdo->prepare("select * from inventory where player_id = ? and item_id = ?");
				$checkStmt->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
				$checkStmt->bindParam(2, $itemId, PDO::PARAM_INT);
				$checkStmt->execute();
				
				if ($checkStmt->rowCount() == 0) {
					$playerStmt = $pdo->prepare("select * from users where id = ?");
					$playerStmt->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
					$playerStmt->execute();
					$player = $playerStmt->fetch(PDO::FETCH_ASSOC);
					
					if ($player['coins'] >= $item['price']) {
						$timestamp = time();
						
						$update = $pdo->prepare("update users set coins = coins - ? where id = ?");
						$update->bindParam(1, $item['price'], PDO::PARAM_INT);
						$update->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
						$update->execute();
						
						$insert = $pdo->prepare("insert into inventory (player_id, item_id, timestamp) values (?, ?, ?)");
						$insert->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
						$insert->bindParam(2, $itemId, PDO::PARAM_INT);
						$insert->bindParam(3, $timestamp, PDO::PARAM_INT);
						$insert->execute();
						
						$tItem = new ItemVO();
						$tItem->id = $item['id'];
						$tItem->name = $item['name'];
						$tItem->type = $item['type'];
						$tItem->price = $item['price'];
						$tItem->zettSort = $item['zett_sort'];
						$tItem->premium = boolval($item['premium']);
						$tItem->bought = true;
						$tItem->active = false;
						$tItem->movementType = $item['movement_type'];
						
						$result = new AmfResponse();
						$result->statusCode = 0;
						$result->message = "success";
						$result->valueObject = $tItem;
						return $result;
					}
					
					$result = new AmfResponse();
					$result->statusCode = 6;
					$result->message = "Not enough coins";
					$result->valueObject = null;
					return $result;
				}
			}
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::purchaseItem\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function setState($categoryId, $nameId, $value) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from states where player_id = ? and category_id = ? and name_id = ?");
			$stmt->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
			$stmt->bindParam(2, $categoryId, PDO::PARAM_INT);
			$stmt->bindParam(3, $nameId, PDO::PARAM_INT);
			$stmt->execute();
			
			$timestamp = floor(microtime(true) * 1000);
			
			if ($stmt->rowCount() > 0) {
				$update = $pdo->prepare("update states set value = ?, last_changed = ? where player_id = ? and category_id = ? and name_id = ?");
				$update->bindParam(1, $value, PDO::PARAM_INT);
				$update->bindParam(2, $timestamp, PDO::PARAM_INT);
				$update->bindParam(3, $_SESSION['playerId'], PDO::PARAM_INT);
				$update->bindParam(4, $categoryId, PDO::PARAM_INT);
				$update->bindParam(5, $nameId, PDO::PARAM_INT);
				$update->execute();
			} else {
				$insert = $pdo->prepare("insert into states values (?, ?, ?, ?, ?)");
				$insert->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
				$insert->bindParam(2, $categoryId, PDO::PARAM_INT);
				$insert->bindParam(3, $nameId, PDO::PARAM_INT);
				$insert->bindParam(4, $value, PDO::PARAM_INT);
				$insert->bindParam(5, $timestamp, PDO::PARAM_INT);
				$insert->execute();
			}
			
			$tState = new StateVO();
			$tState->playerId = $_SESSION['playerId'];
			$tState->cathegoryId = $categoryId;
			$tState->nameId = $nameId;
			$tState->stateValue = $value;
			$tState->lastChanged = $timestamp;
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = $tState;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::setState\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function getStates($states = array()) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from states where player_id = ?");
			$stmt->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				$storage = array();
			
				foreach($stmt as $state) {
					if (in_array($state['category_id'], $states)) {
						$tState = new StateVO();
						$tState->playerId = $_SESSION['playerId'];
						$tState->cathegoryId = $state['category_id'];
						$tState->nameId = $state['name_id'];
						$tState->stateValue = $state['value'];
						$tState->lastChanged = $state['last_changed'];
						
						array_push($storage, $tState);
					}
				}
				
				$tList = new ListVO();
				$tList->list = $storage;
				
				$result = new AmfResponse();
				$result->statusCode = 0;
				$result->message = "success";
				$result->valueObject = $tList;
				return $result;
			}
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::getStates\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function getPlayerCard($playerId) {
		$playerInfo = Database::getPlayerInfo($playerId);
		
		$result = new AmfResponse();
		$result->statusCode = 0;
		$result->message = "success";
		$result->valueObject = $playerInfo;
		return $result;
	}
	
	public function getPlayerHome($playerId) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from users where id = ?");
			$stmt->bindParam(1, $playerId, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				$player = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$inventoryStmt = $pdo->prepare("select * from inventory where player_id = ?");
				$inventoryStmt->bindParam(1, $playerId, PDO::PARAM_INT);
				$inventoryStmt->execute();
				
				$storage = array();
				
				foreach($inventoryStmt as $invItem) {
					$itemStmt = $pdo->prepare("select * from items where id = ?");
					$itemStmt->bindParam(1, $invItem["item_id"], PDO::PARAM_INT);
					$itemStmt->execute();
					$item = $itemStmt->fetch(PDO::FETCH_ASSOC);
					
					switch($item["type"]) {
						case "00":
						case "13":
						case "14":
						case "17":
							$tFurniture = new FurnitureDataVO();
							$tFurniture->parameters = $invItem["parameters"];
							$tFurniture->x = $invItem["x"];
							$tFurniture->y = $invItem["y"];
							$tFurniture->rot = $invItem["rot"];
							$tFurniture->uid = $invItem["player_id"];
							$tFurniture->id = $invItem["item_id"];
							$tFurniture->type = $item["type"];
							$tFurniture->active = boolval($invItem["active"]);
							$tFurniture->premium = boolval($item["premium"]);
							$tFurniture->bought = true;
							$tFurniture->roomID = 0;
							
							array_push($storage, $tFurniture);
					}
				}
				
				$tHomeData = new HomeDataVO();
				$tHomeData->id = 0;
				$tHomeData->playerID = $playerId;
				$tHomeData->locked = boolval($player["home_locked"]);
				$tHomeData->furnitureList = $storage;
				$tHomeData->trackList = null;
				$tHomeData->pets = null;
				$tHomeData->pokopets = null;
				$tHomeData->bollies = null;
				
				$result = new AmfResponse();
				$result->statusCode = 0;
				$result->message = "success";
				$result->valueObject = $tHomeData;
				return $result;
			}
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::getPlayerHome\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function getPlayerInfoList($players = array(), $detailed) {
		try {
			$storage = array();
			
			foreach($players as $player) {
				$playerInfo = Database::getPlayerInfo($player);
				array_push($storage, $playerInfo);
			}
			
			$tList = new ListVO();
			$tList->list = $storage;
				
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = $tList;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::getPlayerInfoList\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function lockHome($locked) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("update users set home_locked = ? where id = ?");
			$stmt->bindParam(1, $locked, PDO::PARAM_INT);
			$stmt->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
			$stmt->execute();
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::lockHome\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function removeFromBuddyList($playerId) {
		try {
			$pdo = Database::getConnection();
			
			$delete = $pdo->prepare("delete from (player_id = ? and buddy_id = ?) or (player_id = ? and buddy_id = ?)");
			$delete->bindParam(1, $playerId, PDO::PARAM_INT);
			$delete->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
			$delete->bindParam(4, $playerId, PDO::PARAM_INT);
			$delete->bindParam(3, $_SESSION['playerId'], PDO::PARAM_INT);
			$delete->execute();
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::removeFromBuddyList\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function removeItems($items = array()) {
		try {
			$pdo = Database::getConnection();
			
			foreach($items as $item) {
				$delete = $pdo->prepare("delete from inventory where player_id = ? and item_id = ?");
				$delete->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
				$delete->bindParam(2, $item, PDO::PARAM_INT);
				$delete->execute();
			}
			
			$activeInventory = Database::getPlayerInventory($_SESSION['playerId']);
			$inactiveInventory = Database::getPlayerInventory($_SESSION['playerId'], false);
			
			$tInventory = new InventoryVO();
			$tInventory->activeItems = $activeInventory;
			$tInventory->inactiveItems = $inactiveInventory;
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = $tInventory;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::removeItems\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function updateItems($active, $inactive) {
		try {
			$pdo = Database::getConnection();
			
			foreach($active as $item) {
				$update = $pdo->prepare("update inventory set active = 1 where player_id = ? and item_id = ?");
				$update->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
				$update->bindParam(2, $item->id, PDO::PARAM_INT);
				$update->execute();
			}
			
			foreach($inactive as $item) {
				$update = $pdo->prepare("update inventory set active = 0 where player_id = ? and item_id = ?");
				$update->bindParam(1, $_SESSION['playerId'], PDO::PARAM_INT);
				$update->bindParam(2, $item->id, PDO::PARAM_INT);
				$update->execute();
			}
			
			$playerInfo = Database::getPlayerInfo($_SESSION['playerId']);
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = $playerInfo;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::updateItems\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function updatePlayerState($playerId, $state) {
		try {
			$pdo = Database::getConnection();
			
			$update = $pdo->prepare("update users set state = ? where id = ?");
			$update->bindParam(1, $state, PDO::PARAM_STR);
			$update->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
			$update->execute();
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::updateState\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function updateTourFinished($status) {
		try {
			$pdo = Database::getConnection();
			
			$update = $pdo->prepare("update users set tour_finished = ? where id = ?");
			$update->bindParam(1, $status, PDO::PARAM_BOOL);
			$update->bindParam(2, $_SESSION['playerId'], PDO::PARAM_INT);
			$update->execute();
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = null;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfPlayerService::updateTourFinished\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
}
