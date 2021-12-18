const cron = require("node-cron");
const fetch = require("node-fetch");

cron.schedule("0 * * * *", async function () {
	const response = await fetch("http://localhost/stracker?log=1");
	console.log("Logged at " + (new Date()).toLocaleString());
});

console.log("Logger started");