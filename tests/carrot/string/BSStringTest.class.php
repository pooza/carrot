<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSStringTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$string = BSString::convertWrongCharacters('㈱㈲');
		$this->assert('convertWrongCharacters', $string == '(株)(有)');
	}
}

/* vim:set tabstop=4: */
