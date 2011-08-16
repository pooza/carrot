<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSUserAgentTest extends BSTest {
	public function execute () {
		$useragent = BSUserAgent::create(null, 'default');
		$this->assert('create_Default', $useragent instanceof BSDefaultUserAgent);
		$this->assert('isSmartPhone_Default', !$useragent->isSmartPhone());
	}
}

/* vim:set tabstop=4: */
