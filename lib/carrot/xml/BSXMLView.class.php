<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml
 */

/**
 * XML表示
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSXMLView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function initialize () {
		$this->setEngine(new BSXMLDocument('root'));
		return parent::initialize();
	}
}

/* vim:set tabstop=4 ai: */
?>