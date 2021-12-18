<?php

class strategyList extends fetcher {
	public function __construct (database $database, strategyManager $strategyManager) {
		$this->database = $database;
		$this->strategyManager = $strategyManager;
		$this->setState($this->strategyManager->getPercentages());
	}
	
	public function setState ($state) {
		$this->state = $state;
		foreach ($this->state as &$strategy) {
			$strategy["percentage"] = $strategy["percentage"];
			$strategy["transaction_amount_usd"] = 0;
		}
		$this->stateBackup = $this->state;
	}
	
	private function setPercentage ($id, $percentage) {
		$multiplier = (100 + $percentage) / (100 + $this->state[$id]["percentage"]);
		$this->state[$id]["transaction_amount_usd"] *= $multiplier;
		$this->state[$id]["percentage"] = $percentage;
	}
	
	public function getHashageFromPercentage ($percentage) {
		// Hashage is like percentage but both losses and gains look as severe as each other
		// Losses can range from -100% to 0% while gains can range from 0% to 100%
		// In percentages, gains range from 0% to infinity%, so it has to be converted to the new format
		if ($percentage > 0) {
			$percentage = 100 - ((100 / ($percentage + 100)) * 100);
		}
		
		return floor($percentage * 100) / 100;
	}
	
	public function getCurrentHoldings ($log = false) {
		echo "Strategy list loaded: <br>";
		
		$transactionList = $this->database->query("SELECT * FROM transactions ORDER BY transaction_timestamp ASC");
		
		foreach ($transactionList as $transaction) {
			$transaction["transaction_amount_usd"] = (float)$transaction["transaction_amount_usd"];
			
			if ($transaction["id_from"] !== -1) {
				$this->setPercentage($transaction["id_from"], (float)$transaction["id_from_percentage"]);
				$this->state[$transaction["id_from"]]["transaction_amount_usd"] -= $transaction["transaction_amount_usd"];
			}
			
			$transaction["transaction_amount_usd"] -= (float)$transaction["platform_fees_usd"];
			
			if ($transaction["id_to"] !== -1) {
				$this->setPercentage($transaction["id_to"], (float)$transaction["id_to_percentage"]);
				$this->state[$transaction["id_to"]]["transaction_amount_usd"] += $transaction["transaction_amount_usd"];
			}
		}
		
		// Calculate the total balance
		$balance = 0;
		foreach ($this->state as &$strategy) {
			$this->setPercentage($strategy["id"], $this->stateBackup[$strategy["id"]]["percentage"]);
			
			$percentage = $this->getHashageFromPercentage($this->stateBackup[$strategy["id"]]["percentage"]);
			$percentageToday = $this->getHashageFromPercentage($this->stateBackup[$strategy["id"]]["percentageToday"]);
			
			$strategy["transaction_amount_usd"] = floor($strategy["transaction_amount_usd"] * 100) / 100;
			echo "&nbsp;- You have ".$strategy["transaction_amount_usd"]." USD in ".$strategy["id"]." - ".$strategy["name"]." (".$percentage."# total, ".$percentageToday."# today). <br>";
			$balance += $strategy["transaction_amount_usd"];
		}
		
		if (count($this->state) > 0) {
			echo "Your total balance is $balance USD. <br><br>";
		} else {
			echo "No strategy. <br><br>";
		}
		
		if ($log === true) {
			// Log the results
			$logFile = fopen("logger/log.txt", "a");
			fwrite($logFile, "\r\n".((string)time())."|".$balance);
			fclose($logFile);
		}
		
		return $this->state;
	}
}

?>