<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDarwinPlatformTest extends BSTest {
	public function execute () {
		$platform = BSPlatform::create('darwin');
		$this->assert('create', $platform instanceof BSPlatform);
		$this->assert('getProcessOwner', $platform->getProcessOwner() == 'www');
	}
}

/* vim:set tabstop=4: */
