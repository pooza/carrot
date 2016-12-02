<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleURLShortnerServiceTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $service = new BSGoogleURLShortnerService);

		$url = BSURL::create('http://www.b-shock.co.jp/');
		$this->assert('getShortURL', ($service->getShortURL($url) instanceof BSHTTPURL));
	}
}

/* vim:set tabstop=4: */
