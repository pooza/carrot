<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSHTTPURLTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this->assert('getInstance', $url = BSURL::getInstance('http://www.b-shock.co.jp/'));
		$this->assert('getImageFile', $url->getImageFile('favicon') instanceof BSImageFile);
		$this->assert('getImageFile', $url->getImageFile('qr') instanceof BSImageFile);
		$this->assert('getImageInfo', $url->getImageInfo('favicon') instanceof BSArray);
		$this->assert('getImageInfo', $url->getImageInfo('qr') instanceof BSArray);
	}
}

/* vim:set tabstop=4: */
