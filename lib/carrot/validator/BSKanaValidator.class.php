<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * フリガナ項目バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSKanaValidator extends Validator {
	const PATTERN = '^[ぁ-んァ-ンヴー0-9]*$';

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value 検査対象
	 * @param string $error エラーメッセージ
	 */
	public function execute (&$value, &$error) {
		if (!mb_ereg(self::PATTERN, $value)) {
			$error = '使用出来ない文字が含まれています。';
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>