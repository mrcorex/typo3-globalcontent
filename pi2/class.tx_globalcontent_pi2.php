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
 * Plugin 'Render Content Element' for the 'globalcontent' extension.
 *
 * @author	Thor Solli <thor@linkfactory.dk>
 * @package	TYPO3
 * @subpackage	tx_globalcontent
 */
class tx_globalcontent_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_globalcontent_pi2';			// Same as class name
	var $scriptRelPath = 'pi2/class.tx_globalcontent_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'globalcontent';				// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		//print_r($GLOBALS['TSFE']); return;
		$this->init();
		$elemId = intval(t3lib_div::_GP('elementId'));
		if(!intval($elemId)){
			$content = 'No content ID';
		} else {
		//$content  ='content: '.$elemId;
		//$content .= $this->pi_RTEcssText( $contentFromDb );
		$lCObj = t3lib_div::makeInstance("tslib_cObj");
		$lConf = array('tables' => 'tt_content', 'source'=>'tt_content_'.$elemId);
		$parsedContent = $lCObj->RECORDS($lConf);
		
		
		
		$result .=  str_replace(array('href="index.php','src="'),array('href="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'index.php','src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL')),$parsedContent);
		//$content .='<script>alert(parent.location.href);parent.location.href = "'.t3lib_div::_GP('returnUrl').'&elementId='.$elemId.'&content='.urlencode($result).'";</script>';
		//$content .='<script>window.open( "'.t3lib_div::_GP('returnUrl').'&elementId='.$elemId.'&content='.urlencode($result).'");</script>';
		$content .='<script>window.opener.location.href = "'.t3lib_div::_GP('returnUrl').'&elementId='.$elemId.'&content='.urlencode($result).'&elemUrl='.urlencode(t3lib_div::getIndpEnv('TYPO3_SITE_URL').'?eID=globalcontent&elementId='.$elemId).'";window.close();</script>';
		//$content = $result;
		}
		//header(" ");
		//print($content);
		//exit;
		return $content;// $this->pi_wrapInBaseClass($content);
	}
	
	
	/**
	 * The init method of the PlugIn
	 * 
	 * @param	array		$conf: The PlugIn configuration
	 */
	function init(){
		
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi2/class.tx_globalcontent_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/globalcontent/pi2/class.tx_globalcontent_pi2.php']);
}

?>