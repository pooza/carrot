<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTinyURLServiceTest extends BSTest {
	public function execute () {
		$service = new BSTinyURLService;
		$url = BSURL::create('http://www.google.com/');
		$this->assert('getShortURL', ($service->getShortURL($url) instanceof BSHTTPURL));
	}
}

/* vim:set tabstop=4: */
