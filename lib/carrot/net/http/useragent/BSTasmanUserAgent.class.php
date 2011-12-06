<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Tasmanユーザーエージェント
 *
 * Mac版InternetExplorer等。
 * Tasmanエンジンを搭載するのは5.xのみだが、便宜上、それ以前のバージョンも扱う。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTasmanUserAgent extends BSUserAgent {

	/**
	 * レガシー環境/旧機種か？
	 *
	 * @access public
	 * @return boolean レガシーならばTrue
	 */
	public function isLegacy () {
		return true;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'MSIE [1-5]\\.[[:digit:]]+; Mac';
	}
}

/* vim:set tabstop=4: */
