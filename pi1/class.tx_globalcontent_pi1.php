<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Thor Solli <thor@linkfactory.dk>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Global content' for the 'globalcontent' extension.
 *
 * @author	Thor Solli <thor@linkfactory.dk>
 * @package	TYPO3
 * @subpackage	tx_globalcontent
 */
class tx_globalcontent_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_globalcontent_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_globalcontent_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'globalcontent';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		//t3lib_div::view_array($this->cObj->data)
		if(strlen($this->cObj->data['tx_globalcontent_link'])){
			$content = unserialize(tx_localcache::get($this->prefixId.':'.$this->cObj->data['tx_globalcontent_link']));
			if(!$content){
				$content = t3lib_div::getUrl($this->cObj->data['tx_globalcontent_link'].'&no_cache=1');
				tx_localcache::set($this->prefixId . ':' . $this->cObj->data['tx_globalcontent_link'], serialize($content));
			}
		}
		if($content==false) return;
		if($content==null){
			$content = 'Fall back:<br>'.$this->cObj->data['tx_globalcontent'];
		}
		return $content;
		return 'Hello World!<HR>
			Here is the TypoScript passed to the method:'.
					t3lib_div::view_array($conf).$content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi1/class.tx_globalcontent_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi1/class.tx_globalcontent_pi1.php']);
}

?>
