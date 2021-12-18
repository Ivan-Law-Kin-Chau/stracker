<?php

class strategyManager extends fetcher {
	public function __construct (database $database) {
		$this->database = $database;
		$this->setTimeLimits();
	}
	
	public function getPercentage ($id) {
		$url = "https://api.primexbt.com/v2/public/covesting/strategies/".((string)$id);
		$data = json_decode($this->fetch($url));
		return [
			"total" => (float)$data->totalYield, 
			"today" => (float)$data->dailyYield
		];
	}
	
	public function getPercentages () {
		$strategies = $this->getStrategies();
		foreach ($strategies as &$strategy) {
			$percentage = $this->getPercentage($strategy["id"]);
			$strategy["percentage"] = $percentage["total"];
			$strategy["percentageToday"] = $percentage["today"];
		}
		
		return $strategies;
	}
	
	public function getStrategies () {
		$strategyList = $this->database->query("SELECT * FROM strategies WHERE id != -1");
		
		$strategies = [];
		foreach ($strategyList as $strategy) {
			$strategies[$strategy["id"]] = $strategy;
		}
		
		return $strategies;
	}
	
	public function addStrategy ($id) {
		$strategies = $this->getStrategies();
		if (!isset($strategies[$id])) {
			$url = "https://api.primexbt.com/v2/public/covesting/strategies/".((string)$id);
			$data = json_decode($this->fetch($url));
			$id = $data->strategyId;
			$name = $data->strategyName;
			$initial_timestamp = $data->openingDate;
			$this->database->query("INSERT OR IGNORE INTO strategies VALUES ($id, \"$name\", $initial_timestamp)");
			echo "Strategy ".$name." added. <br>";
		} else {
			echo "Add strategy ".$strategies[$id]["name"]." failed, strategy already exists. <br>";
		}
	}
}

?>