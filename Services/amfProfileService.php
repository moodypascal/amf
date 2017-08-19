<?php

require_once dirname(__FILE__) . '/Vo/AmfResponse.php';
require_once dirname(__FILE__) . '/Vo/ProfileVO.php';

class amfProfileService {
	
	public function getProfile($id, $premium) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from users where id = ?");
			$stmt->bindParam(1, $id, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				$player = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$tProfile = new ProfileVO();
				$tProfile->id = $player['id'];
				$tProfile->bestFriend = $player['best_friend'];
				$tProfile->movie = $player['movie'];
				$tProfile->color = $player['color'];
				$tProfile->book = $player['book'];
				$tProfile->hobby = $player['hobby'];
				$tProfile->song = $player['song'];
				$tProfile->band = $player['band'];
				$tProfile->schoolSubject = $player['school_subject'];
				$tProfile->sport = $player['sport'];
				$tProfile->animal = $player['animal'];
				$tProfile->relStatus = $player['rel_status'];
				$tProfile->motto = $player['motto'];
				$tProfile->bestChar = $player['best_char'];
				$tProfile->worstChar = $player['worst_char'];
				$tProfile->likeMost = $player['like_most'];
				$tProfile->likeLeast = $player['like_least'];
				
				$result = new AmfResponse();
				$result->statusCode = 0;
				$result->message = "Loaded " . $player['username'] . "'s profile";
				$result->valueObject = $tProfile;
				return $result;
			}
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfProfileService::getProfile\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
}