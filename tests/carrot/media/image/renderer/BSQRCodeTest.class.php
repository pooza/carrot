<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSQRCodeTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $renderer = new BSQRCode);
		$renderer->setData('aaa');
		$this->assert('getContents', !!$renderer->getContents());

		$this->assert('__construct', $renderer = new BSQRCode);
		$renderer->setData('http://www.google.com/');
		$this->assert('getContents', !!$renderer->getContents());
	}
}

/* vim:set tabstop=4: */
