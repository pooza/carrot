<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleMapsServiceTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $service = new BSGoogleMapsService);

		$element = $service->createElement('東京都港区');
		$this->assert('createElement', BSString::isContain('<script', $element->getContents()));
	}
}

/* vim:set tabstop=4: */
