<?php

#### TJEK ACCESS TO SCRIPT ####
// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');


#### TJEK VALUES PASSED ####
// Exit if user tries to fiddle with the values ;-)

if(!intval(t3lib_div::_GET('elementId')) ||  intval(t3lib_div::_GET('elementId')) < 1) {
die('Nope ! thx but no thx');
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
require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(PATH_t3lib.'class.t3lib_loaddbgroup.php');
require_once(PATH_tslib.'class.tslib_pagegen.php');

require_once(t3lib_extMgm::extPath('siabscripts').'class.tx_siabscripts_contenthooks.php');

### INCLUDE templavoila and css styled content ###
require_once('typo3conf/ext/templavoila/pi1/class.tx_templavoila_pi1.php');
require_once('typo3/sysext/css_styled_content/pi1/class.tx_cssstyledcontent_pi1.php');


if(t3lib_extMgm::isLoaded('perfectlightbox')){
    @include_once(t3lib_extMgm::extPath('perfectlightbox').'class.tx_perfectlightbox.php');
}

#### PREPARE INSTANCE OF TSFE ####
$temp_TSFEclassName = t3lib_div::makeInstance('tslib_fe');
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


class globalcontent{
    
    function main(){
        $elemId = intval(t3lib_div::_GP('elementId'));
                $content = '';
        if($_GET['stage'] =='get'){
                        $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tt_content','uid='.$elemId);
            if($record[0]['CType'] == 'templavoila_pi1'){
                // find all tt_content within the templavoila
                preg_match_all('/\<value.*\>(\d+)\<\/value\>/',$record[0]['tx_templavoila_flex'],$matches);

                $cids = implode(',',$matches[1]);
                $records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tt_content',"uid IN($cids)");
                $records[] = $record[0];
                
            } else {
                $records = $record;
            }
            
            foreach($records as $id => $record){
                preg_match_all('/<link (\d+)([^\>]*)\>/',$record['bodytext'],$matches);
                if(count($matches[0])){
                    foreach($matches[0] as $i => $link){                    
                        if(strpos($matches[2][$i],'external')===false){                            
                            $tmp = t3lib_div::getUrl('http://'.$this->getDomainForPage($matches[1][$i]).'/index.php?id='.$matches[1][$i].'&type=8001');
                            $replace[$matches[0][$i]] = '<link '.$tmp.' - external-link>';
                        }
                    }
                }
                if(count($replace)){
                    $records[$id]['bodytext'] = str_replace(array_keys($replace),$replace,$record['bodytext']);
                }
                $records[$id] = array_map(utf8_encode,$records[$id]);
            }
            
                        $content = json_encode($records);


        } elseif(intval($elemId)){
            $cObj = t3lib_div::makeInstance("tslib_cObj");
            if($_GET['L']>0){
                            $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tt_content','l18n_parent='.$elemId.' AND sys_language_uid=' . intval($_GET['L']) . ' AND NOT deleted AND NOT hidden');
                    } else {
                        $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tt_content','uid='.$elemId.' AND NOT deleted AND NOT hidden');
                    }
            if(count($record[0])>1){/*go on*/} else {return false;}

                    $contentHook =  t3lib_div::makeInstance("tx_siabscripts_contenthooks");
                    $tmpObj->content = $record[0]['bodytext'];
            $contentHook->findAndReplace($record[0]['bodytext'],$tmpObj);
            $record[0]['bodytext'] = $tmpObj->content;
            // convert all internal links to external for consumer
            preg_match_all('/\<link (\d+)([^\>]*)\>/',$record[0]['bodytext'],$matches);            
            if(count($matches[0])){                
                foreach($matches[0] as $i => $link){                    
                    if(strpos($matches[2][$i],'external')===false){                        
                        $tmp = t3lib_div::getUrl('http://'.$this->getDomainForPage($matches[1][$i]).'/index.php?id='.$matches[1][$i].'&type=8001');
                        if(strlen(trim($tmp)) && strpos($tmp,'<head') === false){
                            $replace[$matches[0][$i]] = '<link '.$tmp.' '.(str_replace('internal-link','',$matches[2][$i])).'>';
                        } elseif(strlen(trim($tmp))<1){
                            // check for mop
                            $mop = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,pid,mount_pid','pages','uid='.intval($matches[1][$i]).' AND mount_pid > 0 AND deleted = 0');
                            if($mop[0]['mount_pid'] > 0){
                                $tmp = t3lib_div::getUrl('http://'.$this->getDomainForPage($mop[0]['mount_pid']).'/index.php?id='.$mop[0]['mount_pid'].'&type=8001' . ($_GET['L']>0 ? '&L='.$_GET['L']:''));
                                if(strlen(trim($tmp)) && strpos($tmp,'<head') === false){
                                    $replace[$matches[0][$i]] = '<link '.$tmp.' '.(str_replace('internal-link','',$matches[2][$i])).'>';
                                }
                            }
                        }
                    }
                }
                if(count($replace)){
                    $record[0]['bodytext'] = str_replace(array_keys($replace),$replace,$record[0]['bodytext']);
                }
            }
            
            if($_GET['L']>0){
                    $GLOBALS['TSFE']->sys_language_contentOL = 'hideNonTranslated';
                    $GLOBALS['TSFE']->sys_language_content = $_GET['L'];    
            }
                        // create content object
            $cObj->start($record[0], 'tt_content'); 
                $parsedContent = $cObj->cObjGetSingle('<tt_content',$lConf);
        
            // replace internal links for src and href to external for consumer
                        //$content =  str_replace('src="','src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL'),$parsedContent);
                        //$content = preg_replace('/src="([^(http:)][^"]+)/i', 'src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'$1', $parsedContent);
                 $content = preg_replace('/src="(fileadmin|uploads|typo3temp|typo3conf)/i', 'src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'$1', $parsedContent);

                        $content = preg_replace('/href="([^(http:)][^"]+)/i', 'href="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'$1', $content);

                }
                print($content);
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

