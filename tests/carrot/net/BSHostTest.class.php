<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHostTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $host = new BSHost('www.b-shock.co.jp'));
		$this->assert('getName', $host->getName() == 'www.b-shock.co.jp');
		$this->assert('getAddress', $host->getAddress() == '49.212.211.238');
		$this->assert('getImageFile', $host->getImageFile('favicon') instanceof BSImageFile);
		$this->assert('getImageInfo', $host->getImageInfo('favicon') instanceof BSArray);
	}
}

/* vim:set tabstop=4: */
