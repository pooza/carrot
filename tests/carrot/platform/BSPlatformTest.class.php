<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPlatformTest extends BSTest {
	public function execute () {
		$platform = $this->controller->getPlatform();
		$this->assert('getPlatform', $platform instanceof BSPlatform);
		$this->assert('getName', !!$platform->getName());
	}
}

/* vim:set tabstop=4: */
