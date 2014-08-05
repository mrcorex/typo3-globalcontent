<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skårhøj (kasper@typo3.com)
*  (c) 2004-2007 Rupert Germann (rupi@gmx.li)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
/**
 * Class that adds the wizard icon.
 *
 * $Id: class.tx_ttnews_wizicon.php 4750 2007-01-25 20:46:23Z rupertgermann $
 *
* @author Rupert Germann <rupi@gmx.li>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   55: class tx_ttnews_wizicon
 *   63:     function proc($wizardItems)
 *   83:     function includeLocalLang()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */





/**
 * Class that adds the wizard icon.
 *
 * @author Rupert Germann <rupi@gmx.li>
 */
class tx_globalcontent_wizicon {

	/**
	 * Adds the newloginbox wizard icon
	 *
	 * @param	array		Input array with wizard items for plugins
	 * @return	array		Modified input array, having the item for newloginbox added.
	 */
	function proc($wizardItems)	{
		global $LANG;
                

		$LL = $this->includeLocalLang();
                //$wizardItems['common_7'] = array(
		$wizardItems['plugins_tx_globalcontent_pi1'] = array(
			'icon'=>t3lib_extMgm::extRelPath('globalcontent').'res/icons/network-server.png',
			'title'=>$LANG->getLLL('pi_title',$LL),
			'description'=>$LANG->getLLL('pi_plus_wiz_description',$LL),
			'params'=>'&defVals[tt_content][CType]=globalcontent_pi1'
		);
                // &defVals[tt_content][CType]=globalcontent_pi1
                //print_r($wizardItems);
                
		return $wizardItems;
                            
	}

	/**
	 * Includes the locallang file for the 'tt_news' extension
	 *
	 * @return	array		The LOCAL_LANG array
	 */
	function includeLocalLang()    {
		if ( class_exists("\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility") ) {
			$LOCAL_LANG = \TYPO3\CMS\Core\Utility\GeneralUtility::readLLfile(
				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('globalcontent').'locallang.xml',
				$GLOBALS['LANG']->lang
			);
		} else {
			$llFile = t3lib_extMgm::extPath('globalcontent').'locallang.xml';
			$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);
		}
		return $LOCAL_LANG;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi1/class.tx_globalcontent_pi1_wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi1/class.tx_globalcontent_pi1_wizicon.php']);
}

?>
