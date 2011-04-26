<?php

class tx_globalcontent_userfunctions{
	function parseContent(&$params, &$pObj){
		if($_GET['type']==9000){
			$replace = array();
			$firstPass = false;
			$runner = 0;
			preg_match_all('/\<!--[^\>]+ uid:(\d+)\/(text|textpic|templavoila_pi1)[^\>]+ --\>/m',$pObj->content,$matches);
			
			foreach($matches[0] as $id => $val){				
				if(strpos($val,'begin')){
					$key = $id;
					$start[$matches[1][$id]] = $matches[0][$id];					
					$firstPass = true;
					$firstVal = $matches[1][$id];
					$cid[$runner] = $matches[1][$id];
					$runner++;

				} elseif(strpos($val,'end') && strpos($val,"uid:{$cid[$runner-1]}")){
					$runner--;
					$lang = '';
					if($_GET['L']>0){
						$lang = '&L='.$_GET['L'];
					}					
					$replace[$start[$cid[$runner]]] = $start[$cid[$runner]] . '<div style="border: solid 1px; margin-bottom:5px;">';
					$onclick = isset($_GET['callback'])?'window.opener.location.href=\''.$_GET['callback'].'&elementId='.$cid[$runner].'&site='.$_GET['site'].$lang.'\';window.close();':'alert(\'Not valid\');';
					$replace[$matches[0][$id]] = '<input type="button" onclick="'.$onclick.'" value="V&aelig;lg"  /></div>'.$matches[0][$id];
					$firstPass = false;
					
				} else {
					
				}
				
			}
			
			if(count($replace)){
				$pObj->content = str_replace(array_keys($replace),$replace,$pObj->content);
			}
		}
	}
}
?>
