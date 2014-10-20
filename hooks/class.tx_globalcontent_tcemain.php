<?php

/**
 * tcemain-handler.
 */
class tx_globalcontent_tcemain {

	/**
	 * Post-process field-array.
	 *
	 * @param string $status
	 * @param string  $table
	 * @param number $id
	 * @param array &$fieldArray
	 * @param object &$pObj
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj) {
		$fetcher = tx_globalcontent_configuration::getFromConfiguration("fetcher", "passthrough");
		if ($fetcher != "cached") {
			// Do not continue. Only TYPO3 Caching Framework needs to be cleaned.
			return;
		}

		// Make sure old hash is deleted from cache.
		$typo3CacheInstance = tx_globalcontent_cache::getTYPO3CacheInstance();
		if ($table == "tt_content" && $status == "update") {
			$row = $GLOBALS["TYPO3_DB"]->exec_SELECTgetSingleRow("tx_globalcontent_link", "tt_content", "uid = " . intval($id));
			$hash = isset($row["tx_globalcontent_link"]) ? md5($row["tx_globalcontent_link"]) : "";
			if ($hash != "") {
				$typo3CacheInstance->remove($hash);
			}
		}

	}
}
