<?php
/**
 * @package org.carrot-framework
 * @subpackage pdf
 */

/**
 * PDF表示処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSPDFView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function initialize () {
		parent::initialize();
		$this->setRenderer(new BSFPDF);
		$this->getRenderer()->addPage();
		$this->getRenderer()->setFont(BSFPDF::MINCHO_FONT);
		$this->setFileName('export.pdf');
		return true;
	}

	/**
	 * ファイル名を設定
	 *
	 * @access public
	 * @param string $name ファイル名
	 */
	public function setFileName ($name, $mode = null) {
		// 拡張子は必須
		if (!preg_match('/\.pdf$/', $name)) {
			$name .= '.pdf';
		}

		if (!$mode) {
			if ($this->useragent->isBuggy()) {
				$mode = self::ATTACHMENT;
			} else {
				$mode = self::INLINE;
			}
		}
		parent::setFileName($name, $mode);
	}
}

/* vim:set tabstop=4 ai: */
?>