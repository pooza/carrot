<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * iPadユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: BSTasmanUserAgent.class.php 2113 2010-05-29 16:54:14Z pooza $
 */
class BSIPadUserAgent extends BSWebKitUserAgent {

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return 'iPad;';
	}
}

/* vim:set tabstop=4: */
