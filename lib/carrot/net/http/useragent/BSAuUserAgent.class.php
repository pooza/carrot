<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * Auユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSAuUserAgent.class.php 5 2007-07-25 08:04:01Z pooza $
 */
class BSAuUserAgent extends BSMobileUserAgent {

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'au';
	}

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