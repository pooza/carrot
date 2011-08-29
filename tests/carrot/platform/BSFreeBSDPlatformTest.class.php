<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFreeBSDPlatformTest extends BSTest {
	public function execute () {
		$platform = BSPlatform::create('freebsd');
		$this->assert('create', $platform instanceof BSPlatform);
		$this->assert('getProcessOwner', $platform->getProcessOwner() == 'www');
	}
}

/* vim:set tabstop=4: */
