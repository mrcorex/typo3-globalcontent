<?php

#### TJEK ACCESS TO SCRIPT ####
// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');


#### TJEK VALUES PASSED ####
// Exit if user tries to fiddle with the values ;-)

if(!strlen(t3lib_div::_GET('stage'))) {
die('false');
}

#### INCLUDE FILES FOR LATER INSTANTIATION OF CLASSES ####
require_once(PATH_tslib.'class.tslib_fe.php');
require_once(PATH_t3lib.'class.t3lib_page.php');
require_once(PATH_t3lib.'class.t3lib_userauth.php');
require_once(PATH_tslib.'class.tslib_feuserauth.php');
require_once(PATH_tslib.'class.tslib_content.php');
require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
require_once(PATH_t3lib.'class.t3lib_cs.php');
require_once(PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once(PATH_tslib.'class.tslib_gifbuilder.php');
require_once(PATH_tslib.'class.tslib_eidtools.php');



#### PREPARE INSTANCE OF TSFE ####
$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
$TSFE = new $temp_TSFEclassName(
$TYPO3_CONF_VARS,
t3lib_div::_GP('id'),
t3lib_div::_GP('type'),
t3lib_div::_GP('no_cache'),
t3lib_div::_GP('cHash')
);
### INSTANCE OF DIFFERENT TYPO3 FEATURES ###
### LOOK IN ts_index.php IN ORDER TO ANALYSE ###
$TSFE->connectToDB();
$TSFE->initFEuser();
//$eidTools = t3lib_div::makeInstance('tslib_eidtools');
//$eidTools->initFeUser();
//$eidTools->connectDB();


//$TSFE->checkAlternativeIdMethods();
//$TSFE->clear_preview();
$TSFE->determineId();


//$TSFE->makeCacheHash();
$TSFE->getCompressedTCarray();
$TSFE->initTemplate();
$TSFE->getConfigArray();
//$TSFE->convPOSTCharset();




### MAKE INSTANCE OF CLASS RESPONDING TO AJAX CALL ###
//$obj = t3lib_div::makeInstance('fe_index');
//### MAKE COBJ AVAILABLE FOR CLASS ###
//$obj->cObj = t3lib_div::makeInstance('tslib_cObj');

//
//require_once(PATH_t3lib.'class.t3lib_befunc.php');
//
//#### INCLUDE FILES FOR LATER INSTANTIATION OF CLASSES ####
//require_once(PATH_tslib.'class.tslib_fe.php');
//require_once(PATH_tslib.'class.tslib_content.php');
require_once(PATH_t3lib.'class.t3lib_loaddbgroup.php');
require_once(PATH_tslib.'class.tslib_pagegen.php');
//#### PREPARE INSTANCE OF TSFE ####
//
//$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
//$TSFE = new $temp_TSFEclassName(
//		$TYPO3_CONF_VARS,
//		t3lib_div::_GP('id'),
//		t3lib_div::_GP('type'),
//		t3lib_div::_GP('no_cache'),
//		t3lib_div::_GP('cHash')
//);
//### INSTANCE OF DIFFERENT TYPO3 FEATURES ###
//### LOOK IN ts_index.php IN ORDER TO ANALYSE ###
//$TSFE->initTemplate();
//$TSFE->connectToDB();
////$TSFE->rootLine = t3lib_befunc::BEgetRootLine($pid);

class globalcontent{
	
	function main(){
		$this->readStaticSitelist();
		$stage = t3lib_div::_GP('stage');
		switch($stage){
			case 'save':
				print('Saving<br>');
				$result = $this->saveToLog();
			break;
			case 'get':
				$result = $this->getLogData();
			break;
			case 'delete':
				$result = $this->deleteEntry();
			break;
			default:
		}
		print($result);
	}
	
	
	function saveToLog(){
		$consId = intval(t3lib_div::_GP('consId'));
		$consContId = intval(t3lib_div::_GP('consContId'));
		$provId = intval(t3lib_div::_GP('provId'));
		$provContId = intval(t3lib_div::_GP('provContId'));		
		$consName = t3lib_div::_GP('consDomain');		
		$provName = t3lib_div::_GP('provDomain');
		$linkData = trim(t3lib_div::_GP('datalink'));
		$consPid = intval(t3lib_div::_GP('consPid'));
		if(!strlen($consName)){			
			preg_match('/http:\/\/([^\/]+)/',t3lib_div::getIndpEnv('HTTP_REFERER'),$matches);			
			$consId = $this->sitenameToId($matches[1]);
		} else {
			preg_match('/http:\/\/([^\/]+)/',$consName,$matches);
			//echo $matches[1];
			$consId = $this->sitenameToId($matches[1]);
		}
		if(strlen($provName)){
			preg_match('/http:\/\/([^\/?]+)/',$provName,$matches2);
			if(strlen($matches2[1])){
				$site = $matches2[1];
			} else {
				$site = $provName;
			}
			//echo $site;
			$provId = $this->sitenameToId($site);
		}
		return $this->saveGlobalLink($consId,$consContId,$provId,$provContId,$linkData,$consPid);
	}
	
	
	/**
	 * Function to save global link data
	 *
	 * @param	int	consumer domainId
	 * @param	int	consumer contentId
	 * @param	int	provider domainId
	 * @param	int	provider contentId
	 */	
        function saveGlobalLink($consId,$consContId,$provId,$provContId,$linkData='',$consPid=0){
		if($consId<1 || $consContId<1 || $provId<1 || $provContId<1 || !strlen($linkData) || $consPid<1){
			/*
			echo 'cons Id: '. $consId.chr(10);
			echo 'cons cont Id: '. $consContId.chr(10);
			echo 'prov Id: '. $provId.chr(10);
			echo 'prov cont Id: '. $provContId.chr(10);
			echo 'link: '. $linkData.chr(10);
			echo 'cons pid: '. $consPid.chr(10);			
			*/
			return 'false';
		} else {
			/* update if exists?! */
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_globalcontent_links','consumer_cid='.$consContId);			
			$insertArray = array(
				'pid' => 2,
				'crdate' 		=> intval($record[0]['crdate'])?$record[0]['crdate']:time(),
				'tstamp' 		=> time(),
				'hidden' 		=> 0,
				'deleted'		=> 0,
				'consumer_id' 	=> $consId,
				'consumer_cid' 	=> $consContId,
				'provider_id' 	=> $provId,
				'provider_cid' 	=> $provContId,
				'link_data' => $linkData,
				'cons_pid' => $consPid,
			);
			if(intval($record[0]['uid'])){
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_globalcontent_links','uid='.$record[0]['uid'],$insertArray);
			} else {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_globalcontent_links',$insertArray);
			}
			
		}
		if(intval($GLOBALS['TYPO3_DB']->sql_insert_id())){
			return 'true';
		} else {
			return 'false';
		}
	}
	
	function getLogData(){
		$provDomain = t3lib_div::_GP('provDomain');
		$consId = intval(t3lib_div::_GP('consId'));
		$consContId = intval(t3lib_div::_GP('consContId'));
		$provId = intval(t3lib_div::_GP('provId'));
		$provContId = intval(t3lib_div::_GP('provContId'));
		if(intval($provId<1) && strlen($provDomain)){
			$provId = $this->sitenameToId($provDomain);
		}
		if(intval($consId)){
			$where[] = 'consumer_id='.$consId;
		}
		if(intval($consContId)){
			$where[] = 'consumer_cid='.$consContId;
		}
		if(intval($provId)){
			$where[] = 'provider_id='.$provId;
		}
		if(intval($provContId)){
			$where[] = 'provider_cid='.$provContId;
		}
		
		if(count($where)){
			$where = implode(' AND ',$where);
			$where .= ' AND NOT hidden AND NOT deleted';
		} else {
			return 'false';
		}
		
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_globalcontent_links',$where);
		foreach($records as $i => $record){			
			$records[$i]['consumer_name'] = $this->idToSitename($record['consumer_id']);
			$records[$i]['provider_name'] = $this->idToSitename($record['provider_id']);
			$records[$i]['link_data'] = strlen($record['link_data'])?$record['link_data'].'#c'.$record['consumer_cid']:'';
		}
		
		return json_encode($records);
	}
	
	
	function deleteEntry(){
		if($this->checkReferer(t3lib_div::_GP('consDomain'))){
			$consId = intval(t3lib_div::_GP('consId'));
			$consContId = intval(t3lib_div::_GP('consContId'));
			$provId = intval(t3lib_div::_GP('provId'));
			$provContId = intval(t3lib_div::_GP('provContId'));		
			$consName = t3lib_div::_GP('consDomain');		
			$provName = t3lib_div::_GP('provDomain');
			if(!strlen($consName)){			
				preg_match('/http:\/\/([^\/]+)/',t3lib_div::getIndpEnv('HTTP_REFERER'),$matches);			
				$consId = $this->sitenameToId($matches[1]);
			} else {
				preg_match('/http:\/\/([^\/]+)/',$consName,$matches);
				//echo $matches[1];
				$consId = $this->sitenameToId($matches[1]);
			}
			if(strlen($provName)){
				preg_match('/http:\/\/([^\/?]+)/',$provName,$matches2);
				if(strlen($matches2[1])){
					$site = $matches2[1];
				} else {
					$site = $provName;
				}
				//echo $site;
				$provId = $this->sitenameToId($site);
			}
			$update = array(
				'hidden' => 1,
				'deleted' => 1,
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_globalcontent_links','consumer_id='.$consId.' AND consumer_cid='.$consContId,$update);
			return 'deleted:<br>'.$consId.'_'.$consContId;
		} else {
			return 'false';
		}
		
	}
	
	function checkReferer($site){
		preg_match('/http:\/\/([^\/?]+)/',$site,$matches);
		if($this->sites==null){
			$this->readStaticSitelist();
		}
		if(in_array($matches[1],$this->sites)){		        
			// good to go
			return true;
		} else {
			return false;
		}
	}
	
	
	function readStaticSitelist(){
		// read sites from file
		//$read = file_get_contents('typo3conf/konfiguration/sites.txt');
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_lfremoteadmin_site','1=1');
		foreach($records as $record){
			$this->sites[$record['uid']] = $record['domain'];
			$this->sitesId[$record['domain']] = $record['uid'];
		}
		
	}
	
	function idToSitename($id){
		return $this->sites[$id];
	}
	
	function sitenameToId($name){
		return $this->sitesId[$name];
	}
	
        /**
	* Function to fetch the proper domains.
	* This must be a sys_domain record from the page tree.
	*	
	* @param    array       Record of page to get the correct domain for.
	* @return   string      Correct domain.
	*/
	function getDomainForPage($pid) {
		/* What pages to search */
		$pids = array_reverse(t3lib_befunc::BEgetRootLine($pid));
		//print_r($pids);
		foreach ($pids as $page) {
			/* Domains: Find domain for page */			
			$rs = $GLOBALS['TYPO3_DB']->sql_query("SELECT domainName FROM sys_domain
			    INNER JOIN pages ON sys_domain.pid = pages.uid
			    WHERE NOT sys_domain.hidden
			    AND NOT pages.hidden
			    AND NOT pages.deleted
			    AND pages.uid = {$page['uid']}
			    ORDER BY sys_domain.sorting
			    LIMIT 0,1");
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($rs)) {
				list($domain) = $GLOBALS['TYPO3_DB']->sql_fetch_row($rs);
			}
		}
		return $domain;
	}
}

$run =  t3lib_div::makeInstance('globalcontent');
$run->main();

?>