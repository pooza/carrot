<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * Tasmanユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTasmanUserAgent extends BSMSIEUserAgent {

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		$attributes = parent::getAttributes();
		$attributes['is_tasman'] = true;
		return $attributes;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/MSIE [0-9]+\.[0-9]+; Mac/';
	}
}

/* vim:set tabstop=4 ai: */
?>