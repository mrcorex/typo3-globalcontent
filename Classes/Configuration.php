<?php

namespace Linkfactory\Globalcontent;

/**
 * Class to handle extension-configuration.
 */
class Configuration {

	/**
	 * Get setting from configuration.
	 *
	 * @param string $name
	 * @param string $defaultValue Default value if $name is not set.
	 * @return string
	 */
	public static function getFromConfiguration($name, $defaultValue = "") {
		$configuration = array();
		if (isset($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["globalcontent"])) {
			$configuration = unserialize($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["globalcontent"]);
		}
		$result = $defaultValue;
		if (isset($configuration[$name])) {
			$result = $configuration[$name];
		}
		return $result;
	}
}
