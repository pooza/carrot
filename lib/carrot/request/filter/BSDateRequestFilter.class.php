<?php
/**
 * @package org.carrot-framework
 * @subpackage request.filter
 */

/**
 * 日付 リクエストフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDateRequestFilter extends BSRequestFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	protected function convert ($key, $value) {
		if ($value && !BSArray::isArray($value) && mb_ereg('(day|date)$', $key)) {
			if ($date = BSDate::getInstance($value)) {
				if ($date['hour'] || $date['minute'] || $date['second']) {
					$value = $date->format('Y-m-d H:i:s');
				} else {
					$value = $date->format('Y-m-d');
				}
			}
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
