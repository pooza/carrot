<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPrestoUserAgentTest extends BSTest {
	public function execute () {
		// Opera 10
		$useragent = BSUserAgent::create(
			'Opera/9.80 (Macintosh; Intel Mac OS X; U; en) Presto/2.2.15 Version/10.00'
		);
		$this->assert('create_Opera10', $useragent instanceof BSPrestoUserAgent);
		$this->assert('getVersion_Opera10', $useragent->getVersion() == '2.2.15');
		$this->assert('isLegacy_Opera10', !$useragent->isLegacy());
		$this->assert('hasSupport_flash_Opera10', $useragent->hasSupport('flash'));
	}
}

/* vim:set tabstop=4: */
