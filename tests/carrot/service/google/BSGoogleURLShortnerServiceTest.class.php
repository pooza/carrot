<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSGoogleURLShortnerServiceTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $service = new BSGoogleURLShortnerService);

		$url = BSURL::getInstance('http://www.b-shock.co.jp/');
		$this->assert('getShortURL', ($service->getShortURL($url) instanceof BSHTTPURL));
	}
}

/* vim:set tabstop=4: */
