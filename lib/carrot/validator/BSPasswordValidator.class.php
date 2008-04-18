<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * パスワードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSPasswordValidator extends RegexValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		if (isset($parameters['digits'])) {
			$digits = $parameters['digits'];
		} else {
			$digits = 6;
		}

		$this->setParameter('match', true);
		$this->setParameter('match_error', $digits' . '桁以上の英数字を入力して下さい。');
		$this->setParameter('pattern', '/[[:print:]]{' . $digits . ',}/');
		return parent::initialize($context, $parameters);
	}
}

/* vim:set tabstop=4 ai: */
?>