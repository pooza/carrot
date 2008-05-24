<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * レガシーMozillaユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLegacyMozillaUserAgent extends BSUserAgent {

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'NetscapeNavigator';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/^Mozilla\/[1-4]\..*\((Mac|Win|X11)/';
	}
}

/* vim:set tabstop=4 ai: */
?>