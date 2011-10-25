<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_globalcontent_pi1.php','_pi1','CType',1);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_globalcontent_pi2 = < plugin.tx_globalcontent_pi2.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_globalcontent_pi2.php','_pi2','list_type',0);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['globalcontent'] = 'EXT:' . $_EXTKEY . '/pi2/class.getContentElement.php';
//$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['lgc'] = 'EXT:' . $_EXTKEY . '/hooks/class.logGlobalContent.php';

/* TCEmain hooks */
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['tx_globalcontent'] = 'EXT:globalcontent/hooks/class.tx_globalcontent_tcemain.php:tx_globalcontent_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['tx_globalcontent'] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_globalcontent_tcemain_processDatamapClass.php:tx_globalcontent_tcemain_processdatamapclass';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_globalcontent_tcemain_processDatamapClass.php:tx_globalcontent_tcemain_processdatamapclass';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['globalcontent'] = 'EXT:' . $_EXTKEY . '/hooks/class.globalcontent.php:&tx_globalcontent_userfunctions->parseContent';

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]= 'EXT:' . $_EXTKEY . '/hooks/class.tx_globalcontent_cacheProc.php:&tx_globalcontent_cacheProc->clearCache';
?>
