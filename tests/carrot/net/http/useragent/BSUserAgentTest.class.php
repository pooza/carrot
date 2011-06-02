<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSUserAgentTest extends BSTest {
	public function execute () {
		$useragent = BSUserAgent::create(null, 'default');
		$this->assert('getInstance_Default', $useragent instanceof BSDefaultUserAgent);
		$this->assert('getInstance_isSmartPhone_Default', !$useragent->isSmartPhone());

		$useragent = BSUserAgent::create(
			'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)'
		);
		$this->assert('getInstance_Trident', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion', $useragent->getVersion() == 8);

		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)'
		);
		$this->assert('getInstance_Trident6', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion', $useragent->getVersion() == 10);

		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9) Gecko/2008051206 Firefox/3.0'
		);
		$this->assert('getInstance_Gecko', $useragent instanceof BSGeckoUserAgent);

		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ja-jp) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16'
		);
		$this->assert('getInstance_WebKit', $useragent instanceof BSWebKitUserAgent);

		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0 like Mac OS X; ja-jp) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5A345 Safari/525.20'
		);
		$this->assert('getInstance_iPhone', $useragent instanceof BSIOSUserAgent);
		$this->assert('getInstance_isSmartPhone_iPhone', $useragent->isSmartPhone());

		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Linux; U; Android 1.0; en-us; dream) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2'
		);
		$this->assert('getInstance_Android', $useragent instanceof BSAndroidUserAgent);
		$this->assert('getInstance_isSmartPhone_Android', $useragent->isSmartPhone());

		$useragent = BSUserAgent::create(
			'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10'
		);
		$this->assert('getInstance_iPad', $useragent instanceof BSIOSUserAgent);
		$this->assert('getInstance_isSmartPhone_iPad', !$useragent->isSmartPhone());
	}
}

/* vim:set tabstop=4: */
