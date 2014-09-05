<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_globalcontent_pi1.php', '_pi1','CType', 1);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['globalcontent'] = 'EXT:' . $_EXTKEY . '/classes/class.tx_globalcontent_eid.php';
