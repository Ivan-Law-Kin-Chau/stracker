<?php

class transactionTable {
	public function generate (database $database, strategyList $strategyList) {
		ob_start();
		$strategies = $strategyList->strategyManager->getPercentages();
		$transactionList = $database->query("SELECT * FROM transactions ORDER BY transaction_timestamp ASC");
		if (count($transactionList) > 0) {
			?><table>
				<tbody>
					<tr>
						<th>Buyer</th>
						<th>Buyer Hashage</th>
						<th>Seller</th>
						<th>Seller Hashage</th>
						<th>Time</th>
						<th>Amount</th>
						<th>Fees</th>
						<th>Actions</th>
					</tr><?php
			foreach ($transactionList as $transaction) {
				try {
					$buyer = ($transaction["id_from"] === -1) ? "Me" : $strategies[$transaction["id_from"]]["name"];
					$seller = ($transaction["id_to"] === -1) ? "Me" : $strategies[$transaction["id_to"]]["name"];
					
					$buyerHashage = ($transaction["id_from"] === -1) ? "N/A" : $strategyList->getHashageFromPercentage($transaction["id_from_percentage"])."#";
					$sellerHashage = ($transaction["id_to"] === -1) ? "N/A" : $strategyList->getHashageFromPercentage($transaction["id_to_percentage"])."#";
				} catch (throwable $error) {
					echo "Buyer and/or seller not recognized. <br>";
				} finally {
					$date = new \DateTime();
					$date->setTimeStamp($transaction["transaction_timestamp"]);
					?><tr>
						<td><?=$buyer?></td>
						<td><?=$buyerHashage?></td>
						<td><?=$seller?></td>
						<td><?=$sellerHashage?></td>
						<td><?=$date->format("Y-m-d H:i:s")?></td>
						<td><?=$transaction["transaction_amount_usd"]?> USD</td>
						<td><?=$transaction["platform_fees_usd"]?> USD</td>
						<td><button onclick="
							document.getElementById('cancelForm').children[1].value = <?=$transaction["id_from"]?>;
							document.getElementById('cancelForm').children[2].value = <?=$transaction["id_to"]?>;
							document.getElementById('cancelForm').children[3].value = <?=$transaction["transaction_timestamp"]?>;
							document.getElementById('cancelForm').submit();
						">Cancel Transaction</button></td>
					</tr><?php
				}
			}
			?></tbody>
			</table>
			
			
			<form id="cancelForm" method="POST" style="display: none;">
				<input type="hidden" name="action" value="cancel"/>
				<input type="hidden" name="idFrom"/>
				<input type="hidden" name="idTo"/>
				<input type="hidden" name="time"/>
			</form><?php
		} else {
			?><p>There are currently no transactions. </p><?php
		}
		return ob_get_clean();
	}
}

?>