<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Tridentユーザーエージェント
 *
 * Windows版 InternetExplorer 4.x以降
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTridentUserAgent extends BSUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_msie'] = true;
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function getEncodedFileName ($name) {
		$name = BSString::convertEncoding($name, 'sjis-win');
		return BSString::sanitize($name);
	}

	/**
	 * キャッシングに関するバグがあるか？
	 *
	 * @access public
	 * @return boolean バグがあるならTrue
	 */
	public function isBuggy () {
		return BSRequest::getInstance()->isSSL();
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/MSIE [4-9]+\.[0-9]+; Windows/';
	}
}

/* vim:set tabstop=4 ai: */
?>