<?php

/**
 * ************************************************************
* Copyright notice
*
* (c) 2009 Thor Solli <thor@linkfactory.dk>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Plugin 'Global content' for the 'globalcontent' extension.
 *
 * @author Thor Solli <thor@linkfactory.dk>
 * @package TYPO3
 * @subpackage tx_globalcontent
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
