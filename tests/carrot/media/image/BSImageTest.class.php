<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSImageTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this->assert('getTypes', BSImage::getTypes()->isContain('image/jpeg'));
		$this->assert('getSuffixes', BSImage::getSuffixes()->isContain('.gif'));
		$this->assert('__construct', $image = new BSImage);
		$this->assert('getType', mb_ereg('^image\\/', $image->getType()));
	}
}

/* vim:set tabstop=4: */
