<?php

class actionHandler {
	public function handle (investor $investor, array $arguments) {
		if (isset($arguments["action"])) {
			try {
				if ($arguments["action"] === "buy") {
					$investor->buy((int)$arguments["id"], (float)$arguments["preAmount"], (float)$arguments["postAmount"]);
				} else if ($arguments["action"] === "sell") {
					$investor->sell((int)$arguments["id"], (float)$arguments["preAmount"], (float)$arguments["postAmount"]);
				} else if ($arguments["action"] === "transfer") {
					$investor->transfer((int)$arguments["idFrom"], (int)$arguments["idTo"], (float)$arguments["preAmount"], (float)$arguments["postAmount"]);
				} else if ($arguments["action"] === "cancel") {
					$investor->cancel((int)$arguments["idFrom"], (int)$arguments["idTo"], (int)$arguments["time"]);
				} else if ($arguments["action"] === "add") {
					$investor->strategyList->strategyManager->addStrategy((int)$arguments["id"]);
					$investor->strategyList->setState($investor->strategyList->strategyManager->getPercentages());
				}
				echo "Requested action performed. <br><br>";
			} catch (throwable $error) {
				echo "Action failed. <br><br>";
			}
		}
	}
}

?>