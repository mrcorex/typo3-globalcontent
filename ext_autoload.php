<?php

$extensionPath = t3lib_extMgm::extPath('globalcontent');
return array(
	'tx_globalcontent_eid' => $extensionPath . 'classes/class.tx_globalcontent_eid.php',
	'tx_globalcontent_userfuncs' => $extensionPath . 'hooks/class.tx_globalcontent_userfuncs.php',
	'tx_globalcontent_fetcher' => $extensionPath . 'classes/class.tx_globalcontent_fetcher.php',
	'tx_globalcontent_cacheProc' => $extensionPath . 'hooks/class.tx_globalcontent_cacheProc.php',
);
