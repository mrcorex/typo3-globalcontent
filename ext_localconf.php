<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_globalcontent_pi1.php', '_pi1','CType', 1);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['globalcontent'] = 'EXT:' . $_EXTKEY . '/eid/eid.php';

// Enable cache for extension.
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['globalcontent_cache'])) {
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['globalcontent_cache'] = array();
}

// Set cache to use string for frontend.
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_hash'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['globalcontent_cache'] = array(
		'frontend' => 't3lib_cache_frontend_VariableFrontend',
		'backend' => 't3lib_cache_backend_DbBackend',
		'options' => array()
	);
}

// Add cache-handler.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
	'EXT:' . $_EXTKEY . '/Classes/Hooks/Cacheproc.php:&\\Linkfactory\\Globalcontent\\Hooks\\Cacheproc->clearCache';

// Add tcemain-handler.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
	'EXT:' . $_EXTKEY . '/Classes/Hooks/Tcemain.php:\\Linkfactory\\Globalcontent\\Hooks\\Tcemain';
