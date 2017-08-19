<?php

require_once dirname(__FILE__) . '/Vo/AmfResponse.php';
require_once dirname(__FILE__) . '/Vo/UserActionDailyVO.php';

class amfActionService {
	
	private $lastDoneActionTime = 0;
	private $doneToday = 0;
	private $doneTimes = 0;
	
	public function getLastDoneActionToday($playerId, $action, $timeBetweenUse) {
		try {
			$pdo = Database::getConnection();
			
			$stmt = $pdo->prepare("select * from actions where player_id = ? and action = ?");
			$stmt->bindParam(1, $playerId, PDO::PARAM_INT);
			$stmt->bindParam(2, $action, PDO::PARAM_STR);
			$stmt->execute();
			
			$timestamp = floor(microtime(true) * 1000);
			
			if ($stmt->rowCount() > 0) {
				$actionData = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$this->lastDoneActionTime = $timestamp - $actionData['timestamp'];
				$this->doneToday = 1;
				$this->doneTimes = $actionData['done_times'];
				
				if ($actionData['timestamp'] <= $betweenUse) {
					$update = $pdo->prepare("update actions set done_times = done_times + 1, timestamp = ? where player_id = ? and action = ?");
					$update->bindParam(1, $timestamp, PDO::PARAM_INT);
					$update->bindParam(2, $playerId, PDO::PARAM_INT);
					$update->bindParam(3, $action, PDO::PARAM_STR);
					$update->execute();
				} else {
					$update = $pdo->prepare("update actions set done_times = 0 where player_id = ? and action = ?");
					$update->bindParam(1, $playerId, PDO::PARAM_INT);
					$update->bindParam(2, $action, PDO::PARAM_STR);
					$update->execute();
				}
			} else {
				$insert = $pdo->prepare("insert into actions (player_id, action, timestamp) values (?, ?, ?)");
				$insert->bindParam(1, $playerId, PDO::PARAM_INT);
				$insert->bindParam(2, $action, PDO::PARAM_STR);
				$insert->bindParam(3, $timestamp, PDO::PARAM_INT);
				$insert->execute();
			}
			
			$tUserAction = new UserActionDailyVO();
			$tUserAction->playerId = $_SESSION['playerId'];
			$tUserAction->actionId = $action;
			$tUserAction->doneToday = $this->doneToday;
			$tUserAction->time = $timestamp;
			$tUserAction->doneInTime = $this->doneTimes;
			$tUserAction->lastDoneActionTime = $this->lastDoneActionTime;
			
			if (strpos($action, "master") !== false) {
				$result = new AmfResponse();
				$result->statusCode = 1;
				$result->message = $action;
				$result->valueObject = $tUserAction;
				return $result;
			}
			
			$result = new AmfResponse();
			$result->statusCode = 0;
			$result->message = $action;
			$result->valueObject = $tUserAction;
			return $result;
		} catch(PDOException $e) {
			$error = date("d.m.Y H:i:s") . "\amfActionService::getLastDoneActionToday\tError: (" . $e->getCode . ") " . $e->getMessage;
            throw new Exception($error);
		}
	}
	
	public function performAction($playerId, $action) {
		switch($action) {
			case 'played10':
				break;
			default:
				$result = new AmfResponse();
				$result->statusCode = 0;
				$result->message = $action;
				$result->valueObject = null;
				return $result;
		}
	}
	
}