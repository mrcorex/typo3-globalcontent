<?php

/**
 * Class to fetch content.
 */
class tx_globalcontent_fetcher {

	const extKey = "globalcontent";

	private $url;
	private $fetcherEngine;

	/**
	 * Constructor.
	 * 
	 * @param string $url
	 */
	public function __construct($url) {
		global $TYPO3_CONF_VARS;
		$this->url = $url;

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
		return @file_get_contents($this->url);
	}

	/**
	 * Get content (cached).
	 * 
	 * @return string
	 */
	private function getContentCached() {
		return "Not implemented yet.";
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
