<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.filter
 */

/**
 * マジッククォート リクエストフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMagicQuoteRequestFilter extends BSRequestFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	protected function convert ($key, $value) {
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
