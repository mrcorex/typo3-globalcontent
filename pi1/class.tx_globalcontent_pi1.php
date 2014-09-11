<?php


/**
 * Plugin for fetching content.
 */
class tx_globalcontent_pi1 extends tslib_pibase {
	var $prefixId = 'tx_globalcontent_pi1';
	var $scriptRelPath = 'pi1/class.tx_globalcontent_pi1.php';
	var $extKey = 'globalcontent';
	var $pi_checkCHash = true;


	/**
	 * The main method of the PlugIns
	 *
	 * @param string $content The PlugIn content
	 * @param array $conf The PlugIn configuration
	 * @return The content that is displayed on the website
	 */
	function main($content, $conf)	{
		$fetchUrl = "";
		$fetcher = "";
		if (isset($this->cObj->data['tx_globalcontent_link'])) {
			$fetchUrl = $this->cObj->data['tx_globalcontent_link'];
		}
		if (isset($this->cObj->data['tx_globalcontent_fetcher'])) {
			$fetcher = $this->cObj->data['tx_globalcontent_fetcher'];
		}

		// Initialize fetcher and get content.
		$fetcher = t3lib_div::makeInstance("tx_globalcontent_fetcher", $fetchUrl, $fetcher);
		return $fetcher->getContent();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi1/class.tx_globalcontent_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi1/class.tx_globalcontent_pi1.php']);
}
