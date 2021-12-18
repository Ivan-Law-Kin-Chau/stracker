PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "strategies" ("id" integer NOT NULL, "name" text NOT NULL, "initial_timestamp" integer NOT NULL, PRIMARY KEY ("id"));
INSERT INTO strategies VALUES(-1,"User",-1);
CREATE TABLE IF NOT EXISTS "transactions" ("id_from" integer NOT NULL, "id_from_percentage" text NOT NULL, "id_to" integer NOT NULL, "id_to_percentage" text NOT NULL, "transaction_timestamp" integer NOT NULL, "transaction_amount_usd" text NOT NULL, "platform_fees_usd" text NOT NULL, PRIMARY KEY ("id_from", "id_to", "transaction_timestamp"), FOREIGN KEY ("id_from") REFERENCES "strategies" ("id"), FOREIGN KEY ("id_to") REFERENCES "strategies" ("id"));
COMMIT;