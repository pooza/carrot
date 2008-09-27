<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * CLI環境用 ダミーユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConsoleUserAgent extends BSUserAgent {

	/**
	 * browscap.iniの情報をインポートする
	 *
	 * @access public
	 */
	public function importBrowscap () {
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/./';
	}
}

/* vim:set tabstop=4 ai: */
?>