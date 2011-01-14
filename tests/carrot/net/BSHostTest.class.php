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

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this->assert('__construct', $host = new BSHost('www.b-shock.co.jp'));
		$this->assert('getImageFile', $host->getImageFile('favicon') instanceof BSImageFile);
	}
}

/* vim:set tabstop=4: */
