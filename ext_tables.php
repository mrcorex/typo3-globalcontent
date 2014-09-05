<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


$tempColumnsTTContent = Array (
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
	"tx_globalcontent" => Array (		
		"exclude" => 0,		
		"label" => "Global content",
		"config" => Array(
			"type" => "user",
			"userFunc" => 'tx_globalcontent_userfuncs->main',
		),
	),
);

if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');	
	t3lib_extMgm::addTCAcolumns('tt_content', $tempColumnsTTContent, 1);
} else {
	t3lib_extMgm::addTCAcolumns('tt_content', $tempColumnsTTContent);
}

if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');	
}

t3lib_extMgm::addStaticFile($_EXTKEY, "static/","Global Content Page types");

$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = 'CType;;4;button;1-1-1, header;;3;;2-2-2,tx_globalcontent,tx_globalcontent_link,tx_globalcontent_orgurl';
t3lib_extMgm::addPlugin(array('LLL:EXT:globalcontent/locallang_db.xml:tt_content.CType_pi1', $_EXTKEY.'_pi1'), 'CType');
