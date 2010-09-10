<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.smartphone
 */

/**
 * iPhoneユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSIPhoneUserAgent extends BSWebKitUserAgent {

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 */
	public function isSmartPhone () {
		return true;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'i(Phone|Pod);';
	}
}

/* vim:set tabstop=4: */
