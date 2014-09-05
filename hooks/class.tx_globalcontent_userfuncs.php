<?php

/**
 * User function for entering url and browsing to choose element.
 */
class tx_globalcontent_userfuncs {

	const PAGE_TYPE_SINGLE = 9002;

	/**
	 * Main of user-function.
	 * 
	 * @param array &$param
	 * @param object &$pObj
	 * @return string
	 */
	public function main(&$params, &$pObj) {

		// Extract data from row.
		$row = $params['row'];
		$table = $params["table"];
		$elementId = intval($params['row']['uid']);
		$url = $params['row']['tx_globalcontent_link'];
		$originalUrl = $params['row']['tx_globalcontent_orgurl'];

		// Setup form.
        $pObj->additionalCode_pre['iframeElement'] = '
			<script type="text/javascript">
				function getUrl() {
					var prox = "' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '?eID=globalcontent&mode=chooseElement";
					var exturl = document.getElementById("tx_globalcontent_orgurl").value;
					if (exturl.trim() == "") {
						alert("You must specify an AU-url.");
						return false;
					}
					var elementId = ' . $elementId . ';
					var url = prox + "&url=" + escape(exturl) + "&elementId=" + elementId;
					window.open(url, \'popUpID' . t3lib_div::shortMD5(time()) . '\',\'width=1000,height=850,scrollbars=yes\');
					return false;						
				}
			</script>';
		$content = '<table>';
		$content .= '<tr><td>Indtast URL</td></tr>';
		$content .= '<tr><td><input type="text" name="data[' . $table . '][' . $elementId . '][tx_globalcontent_orgurl]" id="tx_globalcontent_orgurl" size="60" onfocus="this.select();" value="' . $originalUrl . '"  /></td></tr>';
		if (strlen(trim($originalUrl)) > 0) {
			$content .= '<tr><td>Link til original side:</td></tr>';
			$content .= '<tr><td><a href="' . $originalUrl . '" target="_blank">' . $originalUrl . '</a></td></tr>';
		}
		$content .= '<tr><td><input type="button" onclick="getUrl();" value="Browse"/>';
		$content .= '</table>';
		$content .= '<input type="hidden" name="data[' . $table . '][' . $elementId . '][tx_globalcontent_link]" id="tx_globalcontent_link" value="' . $url . '" />';
		$content .= '<div id="test" style="padding: 5px 5px 5px 5px;">' . $this->getPreview($url, $originalUrl) . '</div>';

		return $content;
	}

	/**
	 * Get preview from remote url.
	 * 
	 * @param string $url
	 * @param string $originalUrl
	 * @return string
	 */
	private function getPreview($url, $originalUrl) {
		$showUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		$showUrl .= "?eID=globalcontent";
		$showUrl .= "&mode=showElement";
		$showUrl .= "&url=" . urlencode($this->prepareUrl($url, $originalUrl));
		$result = file_get_contents($showUrl);
		return $result;
	}

	/**
	 * Prepare url for preview, convert from old format to new format.
	 * 
	 * @param string $url
	 * @return string
	 */
	private function prepareUrl($url, $originalUrl) {

		// Make sure old url-format is converted to new format.
		if (strpos($url, "elementId") > 0) {

			// Convert parameters to array for lookup.
			$parameters = "";
			if (strpos($url, "?") > 0) {
				$parameters = substr($url, strpos($url, "?") + 1);
			}
			if ($parameters != "") {
				$parameters = explode("&", $parameters);
				foreach ($parameters as $index => $parameter) {
					$parameter = explode("=", $parameter);
					$name = $parameter[0];
					$value = isset($parameter[1]) ? urldecode($parameter[1]) : "";
					$parameters[$name] = $value;
				}
			}

			// Add elementId-parameter as cid-parameter (using original url as base).
			$cid = isset($parameters["elementId"]) ? intval($parameters["elementId"]) : 0;
			$url = $originalUrl;
			$url .= strpos($url, "?") > 0 ? "&" : "?";
			$url .= "type=" . tx_globalcontent_userfuncs::PAGE_TYPE_SINGLE;
			$url .= "&cid=" . $cid;
		}

		// Make sure "no_cache=1" is added to url.
		if (strpos($url, "no_cache") == 0) {
			$url .= strpos($url, "?") > 0 ? "&" : "?";
			$url .= "no_cache=1";
		}

		return $url;
	}

}
