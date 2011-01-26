<?php

/**
 * @file classes/DuraStore.inc.php
 *
 * Copyright (c) 2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class DuraStore
 * @ingroup duracloud_classes
 *
 * @brief DuraStore client implementation
 */

class DuraStore extends DuraCloudComponent {
	/**
	 * Constructor
	 * @param $dcc DuraCloudConnection
	 */
	function DuraStore(&$dcc) {
		parent::DuraCloudComponent($dcc, 'durastore');
	}

	/**
	 * Get a list of stores.
	 * @return array List of store IDs
	 */
	function getSpaces() {
		// Get the spaces list
		$dcc =& $this->getConnection();
		$xml = $dcc->get($this->getPrefix() . 'spaces');
		if (!$xml) return false;

		// Parse the result
		$parser =& $this->_createXmlParser();
		if (!$parser) return false;

		xml_parse_into_struct($parser, $xml, $data, $index);
		if (!isset($index['space'])) return array(); // Empty

		$result = array();
		foreach ($index['space'] as $i) {
			$result[] = $data[$i]['attributes']['id'];
		}

		$this->_closeXmlParser($parser);
		return $result;
	}
}

?>
