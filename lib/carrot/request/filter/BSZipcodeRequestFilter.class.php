<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.filter
 */

/**
 * 郵便番号 リクエストフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSZipcodeRequestFilter extends BSRequestFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	protected function convert ($key, $value) {
		if (mb_ereg('zipcode$', $key)) {
			$value = mb_ereg_replace('[^[:digit:]]', null, $value);
			$value = substr($value, 0, 3) . '-' . substr($value, 3, 4);
		}
		return $value;
	}
}

