<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * レガシーMozillaユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSLegacyMozillaUserAgent.class.php 290 2007-02-16 14:45:36Z pooza $
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