<?php

/**
 * Class to fetch content.
 */
class tx_globalcontent_fetcher {

	const extKey = "globalcontent";

	private $url;
	private $cacheKey;
	private $cacheLifetime;
	private $fetcherEngine;

	/**
	 * Constructor.
	 * 
	 * @param string $url
	 */
	public function __construct($url) {
		global $TYPO3_CONF_VARS;
		$this->url = $url;
		$this->cacheKey = md5($this->url);

		// Set cache-lifetime for 12 hours.
		$this->cacheLifetime = 60 * 60 * 12;

		// Get configuraton for fetcher-engine.
		$configuration = array();
		if (isset($TYPO3_CONF_VARS["EXT"]["extConf"][tx_globalcontent_fetcher::extKey])) {
			$configuration = unserialize($TYPO3_CONF_VARS["EXT"]["extConf"][tx_globalcontent_fetcher::extKey]);
		}
		$this->fetcherEngine = "";
		if (isset($configuration["fetcherEngine"])) {
			$this->fetcherEngine = $configuration["fetcherEngine"];
		}
	}

	/**
	 * Get content based on fetcher-engine.
	 * 
	 * @return string
	 */
	public function getContent() {
		if ($this->url == "") {
			return "";
		}

		switch ($this->fetcherEngine) {
			case "passthrough":
				return $this->getContentPassthrough();
				break;

			case "cached":
				return $this->getContentCached();
				break;

			case "jquery":
				return $this->getContentJquery();
				break;

			case "varnish":
				return $this->getContentVarnish();
				break;

		}

		// Fallback to passthrough if fetcher-engine could not be found.
		return $this->getContentPassthrough();
	}

	/**
	 * Get content (passthrough).
	 * 
	 * @return string
	 */
	private function getContentPassthrough() {
		return @file_get_contents($this->url . "&no_cache=1");
	}

	/**
	 * Get content (cached).
	 * 
	 * @return string
	 */
	private function getContentCached() {
		$cacheIdentifier = tx_globalcontent_fetcher::extKey . "_cache";

		// Initialize TYPO3 cache caching framework.
		t3lib_cache::initializeCachingFramework();
		try {
			$cacheInstance = $GLOBALS['typo3CacheManager']->getCache($cacheIdentifier);
		} catch (t3lib_cache_exception_NoSuchCache $e) {
			$cacheInstance = $GLOBALS['typo3CacheFactory']->create(
					$cacheIdentifier,
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['frontend'],
					array(),
					array()
			);
		}

		// Get content from cache.
		$content = $cacheInstance->get($this->cacheKey);
		if ($content === false) {
			// Content not found. Get it from url and save to cache.
			$content = $this->getContentPassthrough();
			$cacheInstance->set($this->cacheKey, $content, array(), $this->cacheLifetime);
		}

		return $content;
	}

	/**
	 * Get content (jQuery).
	 *
	 * @return string
	 */
	private function getContentJquery() {
		return "Not implemented yet.";
	}

	/**
	 * Get content (Varnish).
	 * 
	 * @return string
	 */
	private function getContentVarnish() {
		return "<esi src=\"" . $this->url . "\">";
	}

}
