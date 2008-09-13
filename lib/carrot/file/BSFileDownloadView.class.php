<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * ファイルダウンロード処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSFileDownloadView extends BSView {

	/**
	 * ファイルを設定
	 *
	 * setRendererのエイリアス
	 *
	 * @access public
	 * @param BSFile $file ファイル
	 * @final
	 */
	final public function setFile (BSFile $file) {
		$this->setRenderer($file);
	}

	/**
	 * ファイルを返す
	 *
	 * getRendererのエイリアス
	 *
	 * @access public
	 * @return BSFile ファイル
	 * @final
	 */
	final public function getFile () {
		return $this->getRenderer();
	}

	/**
	 * ファイル名を返す
	 *
	 * @access public
	 * @return string ファイル名
	 */
	public function getFileName () {
		if (!parent::getFileName() && $this->getFile()) {
			$this->setFileName($this->getFile()->getName());
		}
		return parent::getFileName();
	}

	/**
	 * ファイル名を設定
	 *
	 * @access public
	 * @param string $name ファイル名
	 */
	public function setFileName ($name, $mode = self::ATTACHMENT) {
		parent::setFileName($name, $mode);
	}
}

/* vim:set tabstop=4 ai: */
?>