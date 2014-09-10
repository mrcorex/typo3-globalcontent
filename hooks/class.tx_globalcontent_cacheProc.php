<?php

/**
 * Class to clear cache.
 */
class tx_globalcontent_cacheProc {

	/**
	 * Clear cache.
	 * 
	 * @param array $params
	 * @param object $pObj
	 */
	public function clearCache($params, &$pObj){
		$paramUid = $params["uid"];

		$table = isset($params["table"]) ? $params["table"] : "";

		// Prepare list of ids to clear.
		$ids = array();
		if ($table == "tt_content") {
			$ids[] = $params["uid"];
		} elseif ($table == "pages") {
			// Get list of ids based on page-id.
			$ids = $this->getListOfUidsByPageId($params["uid"], "globalcontent_pi1");
		} elseif (isset($params["cacheCmd"])) {
			// Get list of ids based on page-id specified as "cacheCmd".
			$ids = $this->getListOfUidsByPageId($params["cacheCmd"], "globalcontent_pi1");
		}

		// Clear items from cache.
		$hashList = $this->getListOfHashesByElementIds($ids);
		if (count($hashList) > 0) {
			foreach ($hashList as $hash) {
				$GLOBALS["TYPO3_DB"]->exec_DELETEquery("cf_globalcontent_cache", "identifier = '" . $hash . "'");
			}
		}
	}

	/**
	 * Get list of element-ids based on page-id and CType.
	 * 
	 * @param number $uid
	 * @param string  $cType
	 * @return array
	 */
	private function getListOfUidsByPageId($uid, $cType) {
		$ids = array();
		$rows = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid", "tt_content", "hidden = 0 AND deleted = 0 AND CType = '" . $cType . "' AND pid = " . intval($uid));
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$ids[] = $row["uid"];
			}
		}
		return $ids;
	}

	/**
	 * Get list of hash'es based on element-id-list.
	 * 
	 * @param array $ids
	 * @return array
	 */
	private function getListOfHashesByElementIds($ids) {
		if (count($ids) == 0) {
			return array();
		}
		$hashList = array();
		$rows = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("tx_globalcontent_link", "tt_content", "uid IN (" . implode(", ", $ids) . ")");
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$hashList[] = md5($row["tx_globalcontent_link"]);
			}
		}
		return $hashList;
	}

}
