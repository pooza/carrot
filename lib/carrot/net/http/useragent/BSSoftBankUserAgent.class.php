<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * SoftBankユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSoftBankUserAgent extends BSMobileUserAgent {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'softbank.ne.jp';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/^(J-PHONE|MOT|Vodafone|SoftBank)/';
	}
}

/* vim:set tabstop=4 ai: */
?>