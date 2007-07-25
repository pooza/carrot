<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ファイルダウンロード処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSFileDownloadView extends BSView {

	/**
	 * レンダリング前のチェック
	 *
	 * @access protected
	 */
	protected function preRenderCheck () {
		parent::preRenderCheck();
		if (!$name = $this->getFileName()) {
			throw new BSFileException('ファイル名が指定されていません。');
		}
	}

	/**
	 * ファイルを設定 - setEngineのエイリアス
	 *
	 * @access public
	 * @param BSFile $file ファイル
	 */
	public function setFile (BSFile $file) {
		$this->setEngine($file);
	}

	/**
	 * ファイルを返す - getEngineのエイリアス
	 *
	 * @access public
	 * @return BSFile ファイル
	 */
	public function getFile () {
		return $this->getEngine();
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