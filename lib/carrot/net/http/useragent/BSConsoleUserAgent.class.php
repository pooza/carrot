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
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'Console';
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