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
class BSKanaValidator extends BSValidator {
	const PATTERN = '^[ぁ-んァ-ンヴー0-9]*$';

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this->setParameter('invalid_error', 'フリガナとして使用出来ない文字が含まれています。');
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute (&$value, &$error) {
		if (!mb_ereg(self::PATTERN, $value)) {
			$error = $this->getParameter('invalid_error');
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>