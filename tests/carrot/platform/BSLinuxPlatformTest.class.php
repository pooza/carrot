<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLinuxPlatformTest extends BSTest {
	public function execute () {
		$platform = BSPlatform::create('linux');
		$this->assert('create', $platform instanceof BSPlatform);
		$this->assert('getProcessOwner', $platform->getProcessOwner() == 'nobody');
	}
}

/* vim:set tabstop=4: */
