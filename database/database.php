<?php

class database {
	public function __construct (string $rootPath) {
		$this->handle = new SQLite3($rootPath."/database/database.db"); 
		$this->handle->exec("PRAGMA foreign_keys = ON;");
	}
	
	public function query ($sql) {
		try {
			$results = $this->handle->query($sql);
			$rows = [];
			while (true) {
				$row = $results->fetchArray();
				if (!is_array($row)) break;
				
				$rows[] = [];
				foreach ($row as $key => $value) {
					// Remove the element in the $row array that has numeric keys
					if (!is_int($key)) {
						$rows[count($rows) - 1][$key] = $value;
					}
				}
			}
			
			if ($this->handle->lastErrorCode() !== 0 && $this->handle->lastErrorCode() !== 101) {
				throw new Exception($this->handle->lastErrorCode()." ".$this->handle->lastErrorMsg());
			} else {
				return $rows;
			}
		} catch (Throwable $error) {
			throw $error;
		}
	}
}

?>