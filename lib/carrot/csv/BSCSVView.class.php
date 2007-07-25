<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage csv
 */

/**
 * CSVダウンロード処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSCSVView.class.php 270 2007-02-03 04:21:24Z pooza $
 * @abstract
 */
abstract class BSCSVView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 */
	public function initialize ($context) {
		$this->setEngine(new BSCSVData());
		$this->setFileName('export.csv');
		return parent::initialize($context);
	}
}

/* vim:set tabstop=4 ai: */
?>