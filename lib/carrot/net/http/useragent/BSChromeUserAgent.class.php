<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Chromeユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSChromeUserAgent extends BSWebKitUserAgent {

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		$attributes = parent::getAttributes();
		$attributes['is_webkit'] = true;
		return $attributes;
	}

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @return string タイプ名
	 */
	public function getTypeName () {
		return 'Chrome';
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/Chrome/';
	}
}

/* vim:set tabstop=4 ai: */
?>