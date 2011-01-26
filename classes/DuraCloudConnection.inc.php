<?php

/**
 * @defgroup duracloud_classes
 */

/**
 * @file classes/DuraCloudConnection.inc.php
 *
 * Copyright (c) 2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class DuraCloudConnection
 * @ingroup duracloud_classes
 *
 * @brief DuraCloud Connection class
 */

class DuraCloudConnection {
	/** @var $baseUrl string */
	var $baseUrl;

	/** @var $username string */
	var $username;

	/** @var $password string */
	var $password;

	/**
	 * Construct a new DuraCloudConnection.
	 * @param $baseUrl Base URL to DuraCloud, i.e. https://pkp.duracloud.org
	 * @param $username Username
	 * @param $password Password
	 */
	function DuraCloudConnection($baseUrl, $username, $password) {
		$this->baseUrl = $baseUrl;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * Execute a GET request to DuraCloud. Not for external use.
	 */
	function get($path, $params = array()) {
		// Fetch the result
		$ch =& $this->_curlOpenHandle($this->username, $this->password);
		if (!$ch) return false;

		$result = $this->_curlGet($ch, $this->baseUrl . '/' . $path, $params);
		$this->_curlCloseHandle($ch);
		return $result;
	}

	//
	// The following are STATIC functions. They are not declared static for
	// the sake of PHP4 compatibility. Not for external use.
	//


	//
	// cURL / REST-related functions.
	//

	/**
	 * Open a cURL handle. Not for external use.
	 * @param $username string
	 * @param $password string
	 * @return object
	 */
	function &_curlOpenHandle($username, $password) {
		// Check to see whether or not cURL support is installed
		if (!function_exists('curl_init')) {
			$ch = false;
			return $ch;
		}

		// Initialize the cURL handle object, if possible.
		$ch = curl_init();

		if ($ch) {
			// Set common cURL options
			curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, "DuraCloud-PHP " . DURACLOUD_PHP_VERSION); 
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		}
		return $ch;
	}

	/**
	 * Close a cURL handle created with _curlOpenHandle. Not for external
	 * use.
	 * @param $ch object
	 */
	function _curlCloseHandle($ch) {
		curl_close($ch);
	}

	/**
	 * Execute an HTTP POST. Not for external use.
	 * @param $ch cURL handle from openCurlHandle
	 * @param $url URL to DuraCloud (must not contain URL parameters)
	 * @param $postVars array Associative array of POST parameters
	 */
	function _curlPost($ch, $url, $postVars = array()) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);

		// Assemble POST data into $postData
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);

		return curl_exec($ch);
	}

	/**
	 * Execute an HTTP GET. Not for external use.
	 * @param $ch cURL handle from openCurlHandle
	 * @param $url URL to DuraCloud (must not contain URL parameters)
	 * @param $getVars array Associative array of GET parameters
	 */
	function _curlGet($ch, $url, $getVars = array()) {
		// Assemble "get" variables into a string
		$getString = '';
		foreach ($getVars as $name => $value) {
			if (!empty($getString)) $getString .= '&';
			$getString .= urlencode($name) . '=' . urlencode($value);
		}
		if (!empty($getString)) $getString = '?' . $getString;

		curl_setopt($ch, CURLOPT_URL, $url);
		return curl_exec($ch);
	}
}

?>
