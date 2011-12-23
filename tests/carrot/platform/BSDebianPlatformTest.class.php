<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDebianPlatformTest extends BSTest {
	public function execute () {
		$platform = BSPlatform::create('debian');
		$this->assert('create', $platform instanceof BSPlatform);
		$this->assert('getProcessOwner', $platform->getProcessOwner() == 'www-data');
	}
}

/* vim:set tabstop=4: */
