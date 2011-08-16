<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSIOSUserAgentTest extends BSTest {
	public function execute () {
		// iPhone
		$useragent = BSUserAgent::create(
			'Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0 like Mac OS X; ja-jp) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5A345 Safari/525.20'
		);
		$this->assert('create_iPhone', $useragent instanceof BSIOSUserAgent);
		$this->assert('isSmartPhone_iPhone', $useragent->isSmartPhone());
		$this->assert('hasSupport_flash_iPhone', !$useragent->hasSupport('flash'));

		// iPad
		$useragent = BSUserAgent::create(
			'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10'
		);
		$this->assert('create_iPad', $useragent instanceof BSIOSUserAgent);
		$this->assert('isTablet_iPad', $useragent->isTablet());
		$this->assert('hasSupport_flash_iPad', !$useragent->hasSupport('flash'));
	}
}

/* vim:set tabstop=4: */
