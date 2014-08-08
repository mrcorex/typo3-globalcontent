<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (TYPO3_MODE=='BE')	{
        $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_globalcontent_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_globalcontent_pi1_wizicon.php';
}


if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');	
}

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';
t3lib_extMgm::addPlugin(array('LLL:EXT:globalcontent/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","Render Content Element");
t3lib_extMgm::addStaticFile($_EXTKEY,'static/global_content/', 'global content');


include_once(t3lib_extMgm::extPath('globalcontent').'hooks/class.tx_globalcontent_userfuncs.php');


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
				"userFunc" => 'user_ContentFromAU->main',
				
			),
		),
	"tx_globalcontent_refererinfo" => Array (	
		"exclude" => 0,		
		"label" => "LLL:EXT:globalcontent/locallang_db.xml:tt_content.tx_globalcontent_refererinfo",	
		"config" => Array (
                "type" => "user",
				"userFunc" => "user_TCAform_getrefererinfo",
         )
	),
);

// Load into tt_news

if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');	
}
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumnsTTContent, 1);
t3lib_extMgm::addToAllTCAtypes("tt_content","--div--;LLL:EXT:globalcontent/locallang_db.xml:tt_content.tabs.refererinfo,tx_globalcontent_refererinfo;;;;1-1-1");



if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('tt_content');	
}
$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem']='CType;;4;button;1-1-1, header;;3;;2-2-2,tx_globalcontent,tx_globalcontent_link,tx_globalcontent_orgurl';
t3lib_extMgm::addPlugin(array('LLL:EXT:globalcontent/locallang_db.xml:tt_content.CType_pi1', $_EXTKEY.'_pi1'),'CType');

/*
 * Used on common only
$TCA["tx_globalcontent_links"] = array (
	"ctrl" => array (
		'title'     => 'Global content',		
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY tstamp DESC",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_lfabprojectbase_project.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden,consumer_id,consumer_cid,provider_id,provider_cid",
	)
);
*/

/**
 *  Ask service on aucommon site to get the consumers of currently displayed record
 *  Return HTML string with formatted list/links to consumer pages
 *  Display disclaimer
 */
if (!function_exists('user_TCAform_getrefererinfo')) {
	function user_TCAform_getrefererinfo($PA, $fObj) {
		global $TYPO3_CONF_VARS, $LANG;
		$LANG->includeLLFile("EXT:globalcontent/locallang_db.xml");	
		
		$commonDomain = 'aucommon.cs.au.dk';
		if(isset($TYPO3_CONF_VARS['EXTCONF']['AU']['commonDomain']) && $TYPO3_CONF_VARS['EXTCONF']['AU']['commonDomain'] != ''){
			$commonDomain  = $TYPO3_CONF_VARS['EXTCONF']['AU']['commonDomain'];
		}
		
		$provDomain = getDomainForPage($PA['row']['pid']);
		$provContId = $PA['row']['uid'];
		
		$serviceUrl = 'http://'.$commonDomain.'/?eID=lgc&stage=get&provId='.$provDomain.'&provContId='.$provContId;
		
		$json = t3lib_div::getURL($serviceUrl);
		$records = json_decode($json, TRUE);
		
		$message = array();
		if(is_array($records)){
			foreach($records as $record){
				if(strlen(trim($record['link_data'])) > 0){
					$link = '<a href="' . $record['link_data'] . '" target="_blank">' . $record['link_data'] . '</a>';
					$message[] = '<li>' . $link . '</li>';
				}
			}
		}
		
		// Warn against deleting or hiding the element
		$disclaimer = $LANG->getLL('tt_content.tx_globalcontent_refererinfo_disclaimer');
		$disclaimer = '<p style="margin:30px 0 0 0;color:red;">' . $disclaimer . '</p>';

		// If we have no messages
		if(count($message) < 1){
			$disclaimer = '';
			$noRecordsMsg = $LANG->getLL('tt_content.tx_globalcontent_refererinfo_norecords');
			$message[] = '<li>' . $noRecordsMsg . '</li>';
		} 
		
		$header = $LANG->getLL('tt_content.tx_globalcontent_refererinfo_header');
		$header = '<strong>' . $header .'</strong>';
		
		$message = implode("\n", $message);
		$message = '<ul>' . $message  . '</ul>';
		
		return '<div style="padding:3px 0px;">' . $header . $message . $disclaimer .'</div>';
	}
	
	function getDomainForPage($pid) {
		global $TYPO3_DB;
	
		/* What pages to search */
		$pids = array_reverse(t3lib_befunc::BEgetRootLine($pid));
	
		foreach ($pids as $page) {
			/* Domains */
			$rs = $TYPO3_DB->sql_query("SELECT domainName FROM sys_domain
						    INNER JOIN pages ON sys_domain.pid = pages.uid
						    WHERE NOT sys_domain.hidden
						    AND NOT pages.hidden
						    AND NOT pages.deleted
						    AND pages.uid = $page[uid]
						    ORDER BY sys_domain.sorting
						    LIMIT 0,1");
	
			if ($TYPO3_DB->sql_num_rows($rs)) {
				list($domain) = $TYPO3_DB->sql_fetch_row($rs);
			}
		}
	
		return $domain;
	}
}

?>
