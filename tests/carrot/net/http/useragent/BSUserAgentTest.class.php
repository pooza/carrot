<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSUserAgentTest extends BSTest {
	public function execute () {
		$useragent = BSUserAgent::create(null, 'default');
		$this->assert('getInstance_Default', $useragent instanceof BSDefaultUserAgent);
		$this->assert('isSmartPhone_Default', !$useragent->isSmartPhone());

		// IE 5.5
		$useragent = BSUserAgent::create(
			'Mozilla/4.0 (compatible; MSIE 5.5; Windows 95)'
		);
		$this->assert('getInstance_IE55', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion_IE55', $useragent->getVersion() == 5.5);
		$this->assert('isLegacy_IE55', $useragent->isLegacy());

		// IE6
		$useragent = BSUserAgent::create(
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'
		);
		$this->assert('getInstance_IE6', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion_IE6', $useragent->getVersion() == 6);
		$this->assert('isLegacy_IE6', !$useragent->isLegacy());

		// IE10
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)'
		);
		$this->assert('getInstance_IE10', $useragent instanceof BSTridentUserAgent);
		$this->assert('getVersion_IE10', $useragent->getVersion() == 10);

		// Firefox 0.10
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (X11; U; Linux i686; rv:1.7.3) Gecko/20040913 Firefox/0.10'
		);
		$this->assert('getInstance_Fx010', $useragent instanceof BSGeckoUserAgent);
		$this->assert('getVersion_Fx010', $useragent->getVersion() == '1.7.3');
		$this->assert('isLegacy_Fx010', $useragent->isLegacy());

		// Firefox 1.0
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9) Gecko/2008051206 Firefox/3.0'
		);
		$this->assert('getInstance_Fx3', $useragent instanceof BSGeckoUserAgent);
		$this->assert('getVersion_Fx3', $useragent->getVersion() == '1.9');
		$this->assert('isLegacy_Fx3', !$useragent->isLegacy());

		// Safari 0.8
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; ja-jp) AppleWebKit/85.7 (KHTML, like Gecko) Safari/85.6'
		);
		$this->assert('getInstance_Safari08', $useragent instanceof BSWebKitUserAgent);
		$this->assert('getVersion_Safari08', $useragent->getVersion() == '85.7');
		$this->assert('isLegacy_Safari08', $useragent->isLegacy());

		// Safari 5.0
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ja-jp) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16'
		);
		$this->assert('getInstance_Safari5', $useragent instanceof BSWebKitUserAgent);
		$this->assert('getVersion_Safari5', $useragent->getVersion() == '533.16');
		$this->assert('isLegacy_Safari5', !$useragent->isLegacy());

		// iPhone
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0 like Mac OS X; ja-jp) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5A345 Safari/525.20'
		);
		$this->assert('getInstance_iPhone', $useragent instanceof BSIOSUserAgent);
		$this->assert('isSmartPhone_iPhone', $useragent->isSmartPhone());

		// iPad
		$useragent = BSUserAgent::create(
			'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10'
		);
		$this->assert('getInstance_iPad', $useragent instanceof BSIOSUserAgent);
		$this->assert('isTablet_iPad', $useragent->isTablet());

		// Xperia SO-01B
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Linux; U; Android 1.6; ja-jp; SonyEricssonSO-01B Build/R1EA018) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1'
		);
		$this->assert('getInstance_Xperia', $useragent instanceof BSAndroidUserAgent);
		$this->assert('isSmartPhone_Xperia', $useragent->isSmartPhone());
		$this->assert('isLegacy_Xperia', !$useragent->isLegacy());

		// Galaxy Tab
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Linux; U; Android 2.2; ja-jp; SC-01C Build/FROYO) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
		);
		$this->assert('getInstance_GalaxyTab', $useragent instanceof BSAndroidUserAgent);
		$this->assert('isTablet_GalaxyTab', $useragent->isTablet());

		// Optimus Pad
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (Linux; U; Android 3.0.1; ja-jp; L-06C Build/HRI66) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13'
		);
		$this->assert('getInstance_OptimusPad', $useragent instanceof BSAndroidUserAgent);
		$this->assert('isTablet_OptimusPad', $useragent->isTablet());
	}
}

/* vim:set tabstop=4: */
