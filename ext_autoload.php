<?php

$extensionPath = t3lib_extMgm::extPath('globalcontent');
return array(
	'tx_globalcontent_eid' => $extensionPath . 'classes/class.tx_globalcontent_eid.php',
	'tx_globalcontent_userfuncs' => $extensionPath . 'hooks/class.tx_globalcontent_userfuncs.php',
	'tx_globalcontent_fetcher' => $extensionPath . 'classes/class.tx_globalcontent_fetcher.php',
	'tx_globalcontent_cacheproc' => $extensionPath . 'hooks/class.tx_globalcontent_cacheproc.php',
	'tx_globalcontent_configuration' => $extensionPath . 'classes/class.tx_globalcontent_configuration.php',
	'tx_globalcontent_cache' => $extensionPath . 'classes/class.tx_globalcontent_cache.php',
);
