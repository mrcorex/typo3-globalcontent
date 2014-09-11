<?php

/**
 * Class to handle outside requests (eID).
 * 
 * Instantiated at the bottom of this file.
 */
class tx_globalcontent_eid {

	const PAGE_TYPE_LIST = 9001;
	const PAGE_TYPE_SINGLE = 9002;

	/**
	 * Main.
	 * 
	 * @return void
	 */
	public function main() {
		switch (t3lib_div::_GET("mode")) {

			// Show remote page to choose element.
			case "chooseElement":
				$url = t3lib_div::_GET("url");
				$this->chooseElement($url);
				break;

			// Receive chosen element-id.
			case "fetchElement":
				$fetchUrl = trim(t3lib_div::_POST("fetchUrl"));
				$cid = intval(t3lib_div::_POST("cid"));
				$elementId = intval(t3lib_div::_POST("elementId"));
				$this->fetchElement($fetchUrl, $cid, $elementId);
				break;

			// Show element for preview in backend.
			case "showElement":
				$url = trim(t3lib_div::_GET("url"));
				$this->showElement($url);
				break;

			default:
				die("No access");
				break;

		}
	}

	/**
	 * Loads remote page based on type, for choosing element.
	 * 
	 * @param string $url
	 * @return void
	 */
	private function chooseElement($url) {

		// Build url.
		$fetchUrl = $url;
		$url .= strpos($url, "?") > 0 ? "&" : "?";
		$url .= "type=" . tx_globalcontent_eid::PAGE_TYPE_LIST;
		$url .= "&no_cache=1";
		$url .= "&callbackUrl=" . urlencode($this->getSiteUrl() . "?eID=globalcontent&mode=fetchElement");
		$url .= "&fetchUrl=" . urlencode($fetchUrl);

		$content = file_get_contents($url);
		print($content);
	}

	/**
	 * Fetch single element from remote page.
	 * 
	 * @param string $url
	 * @param number $cid
	 * @param number $elementId
	 * @return void
	 */
	private function fetchElement($url, $cid, $elementId) {

		// Build url to fetch element.
		$parameters = array(
			"type" => tx_globalcontent_eid::PAGE_TYPE_SINGLE,
			"no_cache" => 1,
			"cid" => $cid
		);
		$fetchUrl = $this->buildUrl($url, $parameters);

		// Build url to store.
		$parameters = array(
				"type" => tx_globalcontent_eid::PAGE_TYPE_SINGLE,
				"cid" => $cid
		);
		$url = $this->buildUrl($url, $parameters);

		$data = file_get_contents($fetchUrl);

		// Make sure utf8-encoding are removed and clean data.
		if (mb_detect_encoding($data, 'UTF-8', true) == 'UTF-8') {
			$data = utf8_decode($data);
		}
		$data = str_replace("\r", "", $data);
		$data = str_replace("\n", "", $data);
		$data = addslashes($data);

		print("<script type=\"text/javascript\">");
		print("window.opener.document.getElementById('test').innerHTML = '" . $data . "';");
		print("window.opener.document.getElementById('tx_globalcontent_link').value = '" . $url . "';");
		print("window.close();");
		print("</script>\n");
	}

	/**
	 * Show element.
	 * 
	 * @param string $url
	 * @return void
	 */
	private function showElement($url) {
		$content = file_get_contents($url);
		print($content);
	}

	/**
	 * Return site-url
	 * 
	 * @return string.
	 */
	private function getSiteUrl() {
		return t3lib_div::getIndpEnv('TYPO3_SITE_URL');
	}

	/**
	 * Build url.
	 * 
	 * @param string $url
	 * @param array $parameters
	 * @return string
	 */
	private function buildUrl($url, $parameters = array()) {
		if (count($parameters) > 0) {
			$paramString = "";
			foreach ($parameters as $name => $value) {
				$paramString .= $paramString != "" ? "&" : "";
				$paramString .= $name . "=" . urlencode($value);
			}
			if ($paramString != "") {
				$url .= strpos($url, "?") == 0 ? "?" : "&";
				$url .= $paramString;
			}
		}
		return $url;
	}

}

$eid = t3lib_div::makeInstance("tx_globalcontent_eid");
$eid->main();
