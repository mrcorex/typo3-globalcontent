<?php

namespace Linkfactory\Globalcontent;

/**
 * Class to fetch content.
 */
class Fetcher {

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

		$this->url = $url;
		$this->fetcher = $fetcher;
		$this->cacheKey = md5($this->url);

		// Set cache-lifetime for 12 hours.
		$this->cacheLifetime = 60 * 60 * 12;

		// Get fetcher from configuration.
		if ($this->fetcher == "") {
			$this->fetcher = \Linkfactory\Globalcontent\Configuration::getFromConfiguration("fetcher", "passthrough");
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
		$cacheInstance = \Linkfactory\Globalcontent\Cache::getTYPO3CacheInstance();

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
	 * Get content (Varnish).
	 *
	 * @return string
	 */
	private function getContentVarnish() {
		return "<esi src=\"" . $this->url . "\">";
	}
}
