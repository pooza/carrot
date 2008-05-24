<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * InternetExplorerユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMSIEUserAgent extends BSUserAgent {

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'InternetExplorer';
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function getEncodedFileName ($name) {
		$name = BSString::convertEncoding($name, 'sjis');
		return BSString::sanitize($name);
	}

	/**
	 * キャッシングに関するバグがあるか
	 *
	 * @access public
	 * @return boolean バグがあるならTrue
	 */
	public function hasCachingBug () {
		if (!BSController::getInstance()->isResolvable()) {
			// browsecapが参照出来ないので、とりあえずTrueを返しとく
			return true;
		}

		return BSController::getInstance()->isSSL()
			&& ($this->getAttribute('Platform') == 'Win32')
			&& ($this->getAttribute('MajorVer') < 7);
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/MSIE/';
	}
}

/* vim:set tabstop=4 ai: */
?>