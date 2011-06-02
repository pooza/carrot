<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAjaxZip3ServiceTest extends BSTest {
	public function execute () {
		$service = new BSAjaxZip3Service;
		$entries = $service->getAddresses('106');
		$entry = $entries->getIterator()->getFirst();
		$this->assert('getAddresses_1', (1 < $entries->count()));
		$this->assert('getAddresses_2', ($entry[1] == '港区'));
	}
}

/* vim:set tabstop=4: */
