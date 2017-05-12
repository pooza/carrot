<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage js
 */

/**
 * JavaScriptファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJavaScriptFile extends BSFile {

	/**
	 * バイナリファイルか？
	 *
	 * @access public
	 * @return boolean バイナリファイルならTrue
	 */
	public function isBinary () {
		return false;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('js');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return 'utf-8';
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		BSUtility::includeFile('jsmin');
		$this->controller->setAttribute($this, JSMin::minify($this->getContents()));
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('JavaScriptファイル "%s"', $this->getShortPath());
	}
}

