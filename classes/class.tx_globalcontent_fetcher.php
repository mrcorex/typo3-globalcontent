<?php

/**
 * Class to fetch content.
 */
class tx_globalcontent_fetcher {

	private $url;
	private $cacheKey;
	private $cacheLifetime;
	private $fetcher;

	/**
	 * Constructor.
	 *
	 * @param string $url
	 * @param string $fetcher Default "".
	 * @return void
	 */
	public function __construct($url, $fetcher = "") {
		global $TYPO3_CONF_VARS;
		$this->url = $this->checkAndConvertOldUrlFormat($url);
		$this->fetcher = $fetcher;
		$this->cacheKey = md5($this->url);

		// Set cache-lifetime for 12 hours.
		$this->cacheLifetime = 60 * 60 * 12;

		// Get fetcher from configuration.
		if ($this->fetcher == "") {
			$this->fetcher = tx_globalcontent_configuration::getFromConfiguration("fetcher", "passthrough");
		}

	}

	/**
	 * Get content based on fetcher.
	 *
	 * @return string
	 */
	public function getContent() {
		if ($this->url == "") {
			return "";
		}

		switch ($this->fetcher) {
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

		// Fallback to passthrough if fetcher could not be found.
		return $this->getContentPassthrough();
	}

	/**
	 * Get content (passthrough).
	 *
	 * @param bool $useNoCache
	 * @return string
	 */
	private function getContentPassthrough($useNoCache = false) {
		$url = $this->url;
		if ($useNoCache) {
			$url .= "&no_cache=1";
		}
		return @file_get_contents($url);
	}

	/**
	 * Get content (cached).
	 *
	 * @return string
	 */
	private function getContentCached() {
		$cacheInstance = tx_globalcontent_cache::getTYPO3CacheInstance();

		// Get content from cache.
		$content = $cacheInstance->get($this->cacheKey);
		if ($content === false) {
			// Content not found. Get it from url and save to cache.
			$content = $this->getContentPassthrough(true);
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
		static $isInitialized;

		// Initialize if not previously initialized.
		if (is_null($isInitialized)) {
			$headerData = '<script src="' . t3lib_extMgm::siteRelPath("globalcontent") . 'pi1/res/spin.min.js" type="text/javascript"></script>';
			$headerData .= '<script src="' . t3lib_extMgm::siteRelPath("globalcontent") . 'pi1/res/jquery.ajax_autoload.js" type="text/javascript"></script>';
			$GLOBALS['TSFE']->additionalHeaderData["globalcontent"] = $headerData;
			$isInitialized = true;
		}

		$content = "<a class=\"globalcontent-ajax-autoload\" href=\"" . $this->url . "\"></a>";
		return $content;
	}

	/**
	 * Check and convert url, if in old format.
	 * 
	 * @param string $url
	 * @return string
	 */
	private function checkAndConvertOldUrlFormat($url) {

		// Extract query parts.
		$urlParts = parse_url($url);
		$query = parse_url($url, PHP_URL_QUERY);
		$parts = array();
		if (!is_null($query)) {
			parse_str($query, $parts);
		}

		// Convert to new globalcontent format, if needed.
		if (isset($parts["elementId"])) {
			$parts["type"] = 9002;
			$parts["cid"] = $parts["elementId"];

			// Unsetting un-needed parameters.
			unset($parts["eID"]);
			unset($parts["elementId"]);
		}
		$urlParts["query"] = http_build_query($parts);

		// Build url.
		$url = $urlParts["scheme"] . "://" . $urlParts["host"] . "/";
		if ($urlParts["query"] != "") {
			$url .= "?" . $urlParts["query"];
		}

		return $url;
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
