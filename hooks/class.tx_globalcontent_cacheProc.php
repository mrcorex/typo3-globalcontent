<?php

class tx_globalcontent_cacheProc {
	function clearCache($params, &$pObj){
		if($params['cacheCmd'] == 'pages' || $params['cacheCmd'] == 'all'){
			$deleteQuery = "DELETE FROM tx_localcache_cache WHERE keystring LIKE 'tx_globalcontent%'";
			$GLOBALS['TYPO3_DB']->sql_query($deleteQuery);
		}
	}
}
?>
