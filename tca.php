<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_globalcontent_links"] = array (
	"ctrl" => $TCA["tx_globalcontent_links"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,consumer_id,consumer_cid,provider_id,provider_cid"
	),
	"feInterface" => $TCA["tx_globalcontent_links"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"consumer_id" => Array (		
			"exclude" => 1,		
			"label" => "Consumer Id",
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"consumer_cid" => Array (	
			"exclude" => 1,		
			"label" => "Consumer content id",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"provider_id" => Array (		
			"exclude" => 1,		
			"label" => "Provider Id",
			"config" => Array (
				"type" => "input",
				"size" => "30",				
			)
		),
		"provider_cid" => Array (		
			"exclude" => 1,		
			"label" => "Provider content id",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"link_data" => Array (
			"exclude" => 1,		
			"label" => "Provider content id",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),		
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, consumer_id,consumer_cid,provider_id,provider_cid")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>