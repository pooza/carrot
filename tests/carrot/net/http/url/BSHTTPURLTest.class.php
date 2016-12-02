<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTPURLTest extends BSTest {
	public function execute () {
		$this->assert('getInstance', $url = BSURL::create('http://www.b-shock.co.jp/'));
		$this->assert('getImageFile', $url->getImageFile('favicon') instanceof BSImageFile);
		$this->assert('getImageFile', $url->getImageFile('qr') instanceof BSImageFile);
		$this->assert('getImageInfo', $url->getImageInfo('favicon') instanceof BSArray);
		$this->assert('getImageInfo', $url->getImageInfo('qr') instanceof BSArray);
	}
}

/* vim:set tabstop=4: */
