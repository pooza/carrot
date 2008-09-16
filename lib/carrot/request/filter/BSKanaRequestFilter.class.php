<?php
/**
 * @package org.carrot-framework
 * @subpackage request.filter
 */

/**
 * フリガナ リクエストフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSKanaRequestFilter extends BSRequestFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	protected function convert ($key, $value) {
		return BSString::convertKana($value, $this['option']);
	}

	public function initialize ($parameters = array()) {
		$this['option'] = 'KV';
		return parent::initialize($parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>