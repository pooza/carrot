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
 * @version $Id: BSXMLView.class.php 270 2007-02-03 04:21:24Z pooza $
 * @abstract
 */
abstract class BSXMLView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 */
	public function initialize ($context) {
		$this->setEngine(new BSXMLDocument('root'));
		return parent::initialize($context);
	}
}

/* vim:set tabstop=4 ai: */
?>