<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Auユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSAuUserAgent extends BSMobileUserAgent {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'ezweb.ne.jp';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/UP\.Browser/';
	}
}

/* vim:set tabstop=4 ai: */
?>