<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 日付バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDateValidator extends Validator {

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 */
	public function execute (&$value, &$error) {
		try {
			$date = new BSDate($value);
		} catch (BSDateException $e) {
			$error = '日付が正しくありません。';
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>