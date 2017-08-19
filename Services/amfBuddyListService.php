<?php

require_once dirname(__FILE__) . '/Vo/AmfResponse.php';
require_once dirname(__FILE__) . '/Vo/BuddyVO.php';
require_once dirname(__FILE__) . '/Vo/ListVO.php';

class amfBuddyListService {
	
	public function getCompleteBuddyList($playerId) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from buddies where player_id = ?");
			$stmt->bindParam(1, $playerId, PDO::PARAM_INT);
			$stmt->execute();
			
			$storage = array();
			
			foreach($stmt as $buddy) {
				$buddyStmt = $pdo->prepare("select * from users where id = ?");
				$buddyStmt->bindParam(1, $buddy['buddy_id'], PDO::PARAM_INT);
				$buddyStmt->execute();
				$buddyData = $buddyStmt->fetch(PDO::FETCH_ASSOC);
				
				$tBuddy = new BuddyVO();
				$tBuddy->ID = $buddy['buddy_id'];
				$tBuddy->name = $buddyData['username'];
				$tBuddy->premium = boolval($buddyData['premium']);
				$tBuddy->bestfriend = $buddyData['best_friend'];
				$tBuddy->currentGameServer = $buddyData['current_gameserver'];
				$tBuddy->socialLevel = $buddyData['social_level'];
				
				array_push($storage, $tBuddy);
			}
			
			$tList = new ListVO();
			$tList->list = $storage;
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = "success";
			$result->valueObject = $tList;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfBuddyListService::getCompleteBuddyList\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
}