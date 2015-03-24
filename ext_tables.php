<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumnsTtContent = Array (
	'tx_globalcontent_link' => Array(
		'exclude' => 0,
		'label' => 'Test',
		'config' => Array (
			'type' => 'passthrough',
			'size' => 30,
		)
	),
	'tx_globalcontent_orgurl' => Array(
		'exclude' => 0,
		'label' => 'Orginal url',
		'config' => Array (
			'type' => 'passthrough',
			'size' => 30,
		)
	),
	'tx_globalcontent_fetcher' => Array(
			'exclude' => 0,
			'label' => 'Fetcher',
			'config' => Array (
					'type' => 'passthrough',
					'size' => 30,
			)
	),
	"tx_globalcontent" => Array (
		"exclude" => 0,
		"label" => "LLL:EXT:" . $_EXTKEY . "/locallang.xml:pi_title",
		"config" => Array(
			"type" => "user",
			"userFunc" => '\\Linkfactory\\Globalcontent\\Hooks\\Userfuncs->main',
		),
	),
);

if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');
	t3lib_extMgm::addTCAcolumns('tt_content', $tempColumnsTtContent, 1);
} else {
	t3lib_extMgm::addTCAcolumns('tt_content', $tempColumnsTtContent);
}

if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');
}

t3lib_extMgm::addStaticFile($_EXTKEY, "static/", "Global Content Page types");

$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = 'CType;;4;button;1-1-1, header;;3;;2-2-2,tx_globalcontent,tx_globalcontent_link,tx_globalcontent_orgurl,tx_globalcontent_fetcher';
t3lib_extMgm::addPlugin(array('LLL:EXT:globalcontent/locallang_db.xml:tt_content.CType_pi1', $_EXTKEY . '_pi1'), 'CType');

// Add plugin to list of plugins in backend (when adding plugin).
if (TYPO3_MODE == 'BE')   {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['Linkfactory\\Globalcontent\\Hooks\Wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Hooks/Wizicon.php';
}
