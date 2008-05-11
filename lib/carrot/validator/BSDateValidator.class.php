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
class BSDateValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this->setParameter('invalid_error', '日付が正しくありません。');
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
		try {
			$date = new BSDate($value);
		} catch (BSDateException $e) {
			$error = $this->getParameter('invalid_error');
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>