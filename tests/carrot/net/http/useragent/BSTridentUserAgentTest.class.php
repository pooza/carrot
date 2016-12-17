<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTridentUserAgentTest extends BSTest {
	public function execute () {
		// IE 5.5
		$useragent = BSUserAgent::create(
			'Mozilla/4.0 (compatible; MSIE 5.5; Windows 95)'
		);
		$this->assert('create_IE55', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion_IE55', $useragent->getVersion() == 5.5);
		$this->assert('isLegacy_IE55', $useragent->isLegacy());

		// IE6
		$useragent = BSUserAgent::create(
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'
		);
		$this->assert('create_IE6', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion_IE6', $useragent->getVersion() == 6);
		$this->assert('isLegacy_IE6', $useragent->isLegacy());
		$this->assert('hasSupport_flash_IE6', $useragent->hasSupport('flash'));

		// IE10
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)'
		);
		$this->assert('create_IE10', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion_IE10', $useragent->getVersion() == 10);
		$this->assert('hasSupport_flash_IE10', $useragent->hasSupport('flash'));
	}
}

/* vim:set tabstop=4: */
