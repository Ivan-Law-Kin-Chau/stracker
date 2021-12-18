<?php

require "fetcher.php";
require "database/database.php";
require "strategyList.php";
require "strategyManager.php";
require "investor.php";
require "actionHandler.php";
require "transactionTable.php";

ob_implicit_flush();

?>

<title>Stracker - the Strategy Tracker</title>

<style>

* {
	font-family: Tahoma;
}

table, tbody, tr, th, td {
	border-collapse: collapse;
	border: 1px solid #000000;
}

.script {
	font-size: 12px;
	background-color: #FFFFCC;
	border: 2px solid #000000;
	border-radius: 0.5em;
	padding: 1em;
}

</style>

<div><h3 style="display: inline;">Stracker</h3> - the Strategy Tracker</div><br>
<div class="script">
<b>Script started. </b><br><br>

<?php

$database = new database("C:/xampp/htdocs/stracker");
$strategyList = new strategyList($database, new strategyManager($database));
(new actionHandler())->handle(new investor($database, $strategyList), $_POST);
$holdings = json_encode($strategyList->getCurrentHoldings(isset($_GET["log"]) ? (bool)$_GET["log"] : false));

?>

<b>Script ended. </b>
</div><br>

<form id="strategyForm" method="POST">

<input type="hidden" name="action" value="add"/>

<label>ID: </label>
<input type="text" id="strategyId" name="id"/><br>

<button id="strategyButton" onclick="this.form.submit();">Add Strategy</button>

</form>

<script>

const strategyFunction = function () {
	if (document.getElementById("strategyId").value.length > 0) {
		document.getElementById("strategyButton").disabled = false;
	} else {
		document.getElementById("strategyButton").disabled = true;
	}
}

document.getElementById("strategyId").addEventListener("input", strategyFunction);
document.getElementById("strategyId").addEventListener("change", strategyFunction);
strategyFunction();

</script>

<form id="transactionForm" method="POST">

<label>Action: </label>
<select id="selector" name="action">
	<option value="" selected></option>
	<option value="buy">Buy</option>
	<option value="sell">Sell</option>
	<option value="transfer">Transfer</option>
</select><br>

<div id="nonTransferDiv" style="display: none;">
<label>ID: </label>
<input type="text" id="id"/><br>
</div>

<div id="transferDiv" style="display: none;">
<label>ID From: </label>
<input type="text" id="idFrom"/><br>

<label>ID To: </label>
<input type="text" id="idTo"/><br>
</div>

<label>Start Amount: </label>
<input type="text" name="preAmount"/> USD<br>

<label>End Amount: </label>
<input type="text" name="postAmount"/> USD<br>

<button id="submitButton" onclick="this.form.submit();" disabled>Add Transaction</button>

</form>

<script>

const selector = document.getElementById("selector");
const selectorFunction = function () {
	const enable = function (element) {
		element.name = element.id;
		element.disabled = false;
	}
	
	const disable = function (element) {
		element.name = null;
		element.disabled = true;
		element.value = "";
	}
	
	if (selector.value === "buy" || selector.value === "sell") {
		document.getElementById("nonTransferDiv").style.display = "initial";
		document.getElementById("transferDiv").style.display = "none";
		
		enable(document.getElementById("id"));
		disable(document.getElementById("idFrom"));
		disable(document.getElementById("idTo"));
		
		document.getElementById("submitButton").disabled = false;
	} else if (selector.value === "transfer") {
		document.getElementById("nonTransferDiv").style.display = "none";
		document.getElementById("transferDiv").style.display = "initial";
		
		disable(document.getElementById("id"));
		enable(document.getElementById("idFrom"));
		enable(document.getElementById("idTo"));
		
		document.getElementById("submitButton").disabled = false;
	} else {
		document.getElementById("nonTransferDiv").style.display = "none";
		document.getElementById("transferDiv").style.display = "none";
		
		disable(document.getElementById("id"));
		disable(document.getElementById("idFrom"));
		disable(document.getElementById("idTo"));
		
		document.getElementById("submitButton").disabled = true;
	}
}

document.getElementById("selector").addEventListener("input", selectorFunction);
document.getElementById("selector").addEventListener("change", selectorFunction);
selectorFunction();

</script>

<?php echo (new transactionTable())->generate($database, $strategyList); ?>