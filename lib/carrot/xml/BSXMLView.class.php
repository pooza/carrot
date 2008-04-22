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
 * @version $Id: BSXMLView.class.php 5 2007-07-25 08:04:01Z pooza $
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