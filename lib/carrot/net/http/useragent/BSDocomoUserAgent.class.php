<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Docomoユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDocomoUserAgent extends BSMobileUserAgent {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'docomo.ne.jp';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/DoCoMo/';
	}
}

/* vim:set tabstop=4 ai: */
?>