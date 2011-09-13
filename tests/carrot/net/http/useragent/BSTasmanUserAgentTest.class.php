<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTasmanUserAgentTest extends BSTest {
	public function execute () {
		// IE 5.23
		$useragent = BSUserAgent::create(
			'Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)'
		);
		$this->assert('create', $useragent instanceof BSTasmanUserAgent);
		$this->assert('isLegacy', $useragent->isLegacy());
	}
}

/* vim:set tabstop=4: */
