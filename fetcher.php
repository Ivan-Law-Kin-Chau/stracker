<?php

class fetcher {
	public function setTimeLimits (int $cacheTimeLimit = 60, int $deleteTimeLimit = 60) {
		// The time elapsed between now and the cache collection time must be less than $cacheTimeLimit
		$this->cacheTimeLimit = $cacheTimeLimit;
		
		// Caches that have existed for longer than $deleteTimeLimit will be deleted
		$this->deleteTimeLimit = $deleteTimeLimit;
	}
	
	public function fetch (string $url) {
		// Search for an already existing cache
		$returnFileName = null;
		$mostRecentCacheTimestamp = -1;
		$directory = scandir("cache");
		foreach ($directory as $fileName) {
			$fileNameDelimited = explode("_", $fileName);
			if (count($fileNameDelimited) === 2) {
				$originalURL = base64_decode($fileNameDelimited[0]);
				$cacheTimestamp = (int)$fileNameDelimited[1];
				
				if (time() - $cacheTimestamp >= $this->deleteTimeLimit) {
					unlink("cache/".$fileName);
				} else if (time() - $cacheTimestamp >= $this->cacheTimeLimit) {
					continue;
				} else if ($originalURL === $url) {
					if ($mostRecentCacheTimestamp < $cacheTimestamp) {
						$mostRecentCacheTimestamp = $cacheTimestamp;
						if ($returnFileName !== null) unlink("cache/".$returnFileName."_".((string)$mostRecentCacheTimestamp));
						$returnFileName = $fileNameDelimited[0];
					} else {
						unlink("cache/".$fileName);
					}
				}
			}
		}
		
		if ($returnFileName === null) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($curl);
			curl_close($curl);
			
			if ($this->deleteTimeLimit > 0) {
				// Cache $output
				$originalURL = base64_encode($url);
				$cacheTimestamp = time();
				$fileName = $originalURL."_".$cacheTimestamp;
				file_put_contents("cache/".$fileName, $output);
			}
			
			return $output;
		} else {
			return file_get_contents("cache/".$returnFileName."_".((string)$mostRecentCacheTimestamp));
		}
	}
}

?>