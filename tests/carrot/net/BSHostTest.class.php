<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSHostTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $host = new BSHost('www.b-shock.co.jp'));
		$this->assert('getImageFile', $host->getImageFile('favicon') instanceof BSImageFile);
		$this->assert('getImageInfo', $host->getImageInfo('favicon') instanceof BSArray);
	}
}

/* vim:set tabstop=4: */
