<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage pdf
 */

/**
 * PDF表示処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSPDFView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 */
	public function initialize ($context) {
		parent::initialize($context);
		$this->setEngine(new BSFPDF());
		$this->getEngine()->addPage();
		$this->getEngine()->setFont(BSFPDF::MINCHO_FONT);
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
			if ($this->useragent->getType() == 'MSIE') {
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