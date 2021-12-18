<?php

class investor extends fetcher {
	public function __construct (database $database, strategyList $strategyList) {
		$this->database = $database;
		$this->strategyList = $strategyList;
	}
	
	public function buy (int $id, float $preAmount, float $postAmount) {
		$time = time();
		$platformFees = $preAmount - $postAmount;
		$percentage = (string)$this->strategyList->strategyManager->getPercentage($id)["total"];
		
		$this->database->query("INSERT OR IGNORE INTO transactions VALUES 
			(-1, '0', $id, '$percentage', $time, '$preAmount', '$platformFees')");
	}
	
	public function sell (int $id, float $preAmount, float $postAmount) {
		$time = time();
		$platformFees = $preAmount - $postAmount;
		$percentage = (string)$this->strategyList->strategyManager->getPercentage($id)["total"];
		
		$this->database->query("INSERT OR IGNORE INTO transactions VALUES 
			($id, '$percentage', -1, '0', $time, '$preAmount', '$platformFees')");
	}
	
	public function transfer (int $idFrom, int $idTo, float $preAmount, float $postAmount) {
		$time = time();
		$platformFees = $preAmount - $postAmount;
		$percentageFrom = (string)$this->strategyList->strategyManager->getPercentage($idFrom)["total"];
		$percentageTo = (string)$this->strategyList->strategyManager->getPercentage($idTo)["total"];
		
		$this->database->query("INSERT OR IGNORE INTO transactions VALUES 
			($idFrom, '$percentageFrom', $idTo, '$percentageTo', $time, '$preAmount', '$platformFees')");
	}
	
	public function cancel (int $idFrom, int $idTo, int $time) {
		$this->database->query("DELETE FROM transactions WHERE 
			id_from = $idFrom AND id_to = $idTo AND transaction_timestamp = $time");
	}
}

?>