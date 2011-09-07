<?php
class user_ContentFromAU {
	function main(&$params, &$pObj) {
		
                if($_GET['globalcontent']=='savecopy' && strlen($params['row']['tx_globalcontent_link'])){
			
			$json = json_decode(t3lib_div::getUrl($params['row']['tx_globalcontent_link'].'&stage=get&no_cache=1'),TRUE);
			if(count($json)==1){
				// only 1 content element to update
				$json[0]['uid'] = $params['row']['uid'];
				$json[0]['pid'] = $params['row']['pid'];
				unset($json[0]['tx_globalcontent_refererinfo']);
				
				preg_match_all('/src="([^\s"]+(?=\.(jpg|gif|png))\.\2)"/',$json[0]['bodytext'],$matches);
				preg_match('/(http:\/\/[^\/\?]+)/',$params['row']['tx_globalcontent_link'],$domainMatch);
				$domain = $domainMatch[1];
				foreach($matches[1] as $id => $val){
					if(!is_file($val)){
						$img = file_get_contents($domain.'/'.$val);
						file_put_contents($val,$img);
					}
				}
				$insertArray = array_map(utf8_decode,$json[0]);
                                                                $insertArray['deleted'] = 0;
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content','uid='.$params['row']['uid'],$insertArray);
				
			} elseif(count($json)>1) {
				// if more than 1 element, then its a fce, insert child nodes, then the parent with right child-element ids
				foreach($json as $id => $tt_content){
					if($tt_content['CType']=='templavoila_pi1'){
						$parentTT = $tt_content;
					} else {
						$child = $tt_content['uid'];
						unset($tt_content['uid']);
						$tt_content['pid'] = $params['row']['pid'];
						$insertArray = array_map(utf8_decode,$tt_content);
						$insertArray['deleted'] = 0;
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_content',$insertArray);
						$inserId = $GLOBALS['TYPO3_DB']->sql_insert_id();
						$childRec['>'.$child.'<'] = '>'.$inserId.'<';
						$childRec['>'.$child.','] = '>'.$inserId.',';
						$childRec[','.$child.'<'] = ','.$inserId.'<';
						$childRec[','.$child.','] = ','.$inserId.',';
					}
				}
				$parentTT['tx_templavoila_flex'] = str_replace(array_keys($childRec),$childRec,$parentTT['tx_templavoila_flex']);
				$parentTT['uid'] = $params['row']['uid'];
				$parentTT['pid'] = $params['row']['pid'];
				unset($parentTT['tx_globalcontent_refererinfo']);
				$insertArray = array_map(utf8_decode,$parentTT);
				$insertArray['deleted'] = 0;
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content','uid='.$params['row']['uid'],$insertArray);
			}
			// delete reference in index table @ common
			$url = "http://aucommon.cs.au.dk/?eID=lgc&stage=delete&consContId={$params['row']['uid']}&consDomain=".t3lib_div::getIndpEnv('TYPO3_SITE_URL');
			$result = t3lib_div::getUrl($url);
			
			$urlParams = t3lib_div::explodeUrl2Array($pObj->returnUrl);
			$returnUrl = urldecode($urlParams['returnUrl']);
		
			unset($_GET['globalcontent']);
			header('Location: '.$returnUrl);
			
		}
		unset($_GET['globalcontent']);
		$conf = $params['fieldConf']['config'];
                $pObj->additionalCode_pre['iframeElement'] = '
				<script type="text/javascript">
					function getUrl() {
						var prox = "'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'typo3conf/ext/globalcontent/hooks/getcontent.php?url=";
						var url = document.getElementById("exturl").value;
                                                                                                
						//document.getElementById("orgurl").value = url;
						if(url[url.length-1] == "/"){
							var addurl = "?";
						} else if(url.match(/\?/)){
							var addurl = "&";
						} else {
							var addurl = "/?";
						}
						var win = window.open("/typo3/tce_db.php?&data[tt_content]['.$params['row']['uid'].'][tx_globalcontent_orgurl]="+escape(url)+"&vC='.$GLOBALS['BE_USER']->veriCode().'&prErr=1",\'popUpID'.t3lib_div::shortMD5(time()).'\',\'width=1,height=1\');
						//window.location.href = "tce_db.php?data[tt_content]['.$params['row']['uid'].'][tx_globalcontent_orgurl]="+escape(url)+"&vC='.$GLOBALS['BE_USER']->veriCode().'&prErr=1&redirect='.urlencode($pObj->returnUrl).'";
						win.close();
                                                window.open(prox+url+addurl+"type=9000&callback='.urlencode(t3lib_div::getIndpEnv('TYPO3_SITE_URL').substr($pObj->returnUrl,1)).'",\'popUpID'.t3lib_div::shortMD5(time()).'\',\'width=850,height=850,scrollbars=yes\');
                                                return false;						
					}
                                        function createCopy(){
						window.location.href = "'.$pObj->returnUrl.'&globalcontent=savecopy";
						return false;
                                        }
				</script>
				';
                $content = '<table>';
                $content .= '<tr><td>Indtast URL</td></tr>';
                $content .= '<tr><td><input type="text" id="exturl" size="40" onfocus="this.select();" value="'.$params['row']['tx_globalcontent_orgurl'].'"  /></td></tr>';
                if(strlen($params['row']['tx_globalcontent_orgurl'])){
	                $content .= '<tr><td>Link til original side:</td></tr>';
                	$content .= '<tr><td><a href="'.$params['row']['tx_globalcontent_orgurl'].'" target="_blank">'.$params['row']['tx_globalcontent_orgurl'].'</a></td></tr>';
                }
                $content .= '<tr><td><input type="button" onclick="getUrl();" value="Browse"/>';
		if(strlen($params['row']['tx_globalcontent_link']) && preg_match('/#{3}.*#{3}/i',$params['itemFormElValue'])===false){
			$content .= '<input type="button" id="createcopy" onclick="createCopy();" value="Opret lokal kopi" style="margin-left:5px;" />';
		}                
                $content .= '</td></tr></table>';
		if(intval($_GET['elementId'])){
			$url = $_GET['site'].'?eID=globalcontent&elementId='.$_GET['elementId'].($_GET['L']>0?'&L='.intval($_GET['L']):'');
			$result = t3lib_div::getUrl($url);
			$content .= '<input type=hidden name="data['.$params['table'].']['.$params['row']['uid'].'][tx_globalcontent_link]" value="'.$url.'" />';
			//unset($_GET['elementId']);
		}
		$content .= '<textarea type="hidden" name="'.$params['itemFormElName'].'" id="'.$params['itemFormElID'].'"  '.implode('',$params['fieldChangeFunc']).' style="display:none;">'.(intval($_GET['elementId'])?$result:$params['itemFormElValue']).'</textarea>';
                $content .= '<div id="test" style="">'.(intval($_GET['elementId'])?$result:$params['itemFormElValue']).'</div>';
//		$content .= '<input id="orgurl" type=text name="data['.$params['table'].']['.$params['row']['uid'].'][tx_globalcontent_orgurl]" value="'.$params['row']['tx_globalcontent_orgurl'].'" />';

		return $content;//.t3lib_div::view_array($json);//.$_GET['site'];//.t3lib_div::view_array($params);//.$_GET['elemUrl']//.stripslashes($_GET['content']);//.t3lib_div::view_array($params);
	}
}
?>
