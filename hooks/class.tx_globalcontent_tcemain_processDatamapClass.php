<?php
class tx_globalcontent_tcemain_processdatamapclass {
	   
	   function processCmdmap_preProcess($command, $table, $id, &$value, &$pObj) {
			  GLOBAL $TYPO3_CONF_VARS;
	   		  $this->commonDomain = $TYPO3_CONF_VARS['EXTCONF']['AU']['commonDomain'];
		      if($command == 'delete' && $table == 'tt_content'){
				 $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tt_content',"uid=$id AND CType = 'globalcontent_pi1'");
				 if(count($record[0])>1){
					    $url = "http://".$this->commonDomain."/?eID=lgc&stage=delete&consContId=$id&consDomain=".t3lib_div::getIndpEnv('TYPO3_SITE_URL');
					    $result = t3lib_div::getUrl($url);
					    // warning if delete fails at common?
				 }
				 
		      } elseif($command == 'move' && $table == 'tt_content'){
				 $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('pid','tt_content',"uid=$id AND CType = 'globalcontent_pi1'");
				 if(count($record[0])>1){
					    $cid = $id;
					    $provDomain = urlencode($record[0]['tx_globalcontent_link']);
					    preg_match('/elementId=(\d+)/',$record[0]['tx_globalcontent_link'],$matches);
					    $provContId = $matches[1];
					    $pid = intval(t3lib_div::_GP('id'));
					    if(intval($provContId) && intval($pid)){
						       $dataLink = urlencode(t3lib_div::getUrl(t3lib_div::getIndpEnv('TYPO3_SITE_URL').'index.php?id='.$pid.'&type=8001'));
						       $url = "http://".$this->commonDomain."/?eID=lgc&stage=save&consContId=$cid&consDomain=".t3lib_div::getIndpEnv('TYPO3_SITE_URL')."&provDomain=$provDomain&provContId=$provContId&datalink=$dataLink&consPid=$pid";
						       $result = t3lib_div::getUrl($url);
					    }
				 }
		      }
	   }
        
        
	   function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj) {
	   		  GLOBAL $TYPO3_CONF_VARS;
	   		  $this->commonDomain = $TYPO3_CONF_VARS['EXTCONF']['AU']['commonDomain'];
	   	
		      if($table == 'tt_content'  && strlen($fieldArray['tx_globalcontent_link']) ){
				 
				 $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('pid','tt_content','uid='.$id);				 
				 $provDomain = urlencode($fieldArray['tx_globalcontent_link']);
				 preg_match('/elementId=(\d+)/',$fieldArray['tx_globalcontent_link'],$matches);				 
				 $pid = $record[0]['pid'];				 
				 $provContId = $matches[1];
				 if(intval($provContId)){
					    $dataLink = urlencode(t3lib_div::getUrl(t3lib_div::getIndpEnv('TYPO3_SITE_URL').'index.php?id='.$pid.'&type=8001'));
					    $url = "http://".$this->commonDomain."/?eID=lgc&stage=save&consContId=$id&consDomain=".t3lib_div::getIndpEnv('TYPO3_SITE_URL')."&provDomain=$provDomain&provContId=$provContId&datalink=$dataLink&consPid=$pid";
					    $result = t3lib_div::getUrl($url);
				 }
		      }
	   }
}
?>