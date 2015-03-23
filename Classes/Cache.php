<?php

namespace Linkfactory\Globalcontent;

class Cache {

	/**
	 * Get instance of TYPO3 Caching Framework.
	 * 
	 * @return object TYPO3 Caching Framework.
	 */
	public static function getTYPO3CacheInstance() {
		$cacheIdentifier = "globalcontent_cache";

		// Initialize TYPO3 cache caching framework.
		t3lib_cache::initializeCachingFramework();
		try {
			$cacheInstance = $GLOBALS['typo3CacheManager']->getCache($cacheIdentifier);
		} catch (t3lib_cache_exception_NoSuchCache $e) {
			$cacheInstance = $GLOBALS['typo3CacheFactory']->create(
					$cacheIdentifier,
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['frontend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['backend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['options']
			);
		}
		return $cacheInstance;
	}
}
