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
	function getStores() {
		// Get the stores list
		$dcc =& $this->getConnection();
		$xml = $dcc->get($this->getPrefix() . 'stores');
		if (!$xml) return false;

		// Parse the result
		$parser = new DuraCloudXMLParser();
		if (!$parser->parse($xml)) return false;

		$returner = array();
		$storageProviderAccounts =& $parser->getResults();
		assert($storageProviderAccounts['name'] === 'storageProviderAccounts');
		foreach ((array) $storageProviderAccounts['children'] as $i => $storageAcct) {
			assert($storageAcct['name'] === 'storageAcct');
			foreach ($storageAcct['children'] as $c) {
				assert(in_array($c['name'], array('id', 'storageProviderType')));
				if (!isset($returner[$i])) {
					$returner[$i] = array(
						'primary' => $storageAcct['attributes']['isPrimary'] == 'true'?true:false
					);
				}
				$returner[$i][$c['name']] = $c['content'];
			}
		}

		$parser->destroy();
		return $returner;
	}

	/**
	 * Get a list of spaces.
	 * @return array List of space IDs
	 */
	function getSpaces() {
		// Get the spaces list
		$dcc =& $this->getConnection();
		$xml = $dcc->get($this->getPrefix() . 'spaces');
		if (!$xml) return false;

		// Parse the result
		$parser = new DuraCloudXMLParser();
		if (!$parser->parse($xml)) return false;

		$returner = array();
		$spaces =& $parser->getResults();
		assert($spaces['name'] === 'spaces');
		foreach ($spaces['children'] as $c) {
			assert($c['name'] === 'space');
			$returner[] = $c['attributes']['id'];
		}

		$parser->destroy();

		return $returner;
	}
}

?>
