<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 一致バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSPairValidator extends Validator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = null) {
		$this->setParameter('field', '');
		$this->setParameter('match_error', '一致しません。');
		return parent::initialize($context, $parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 結果
	 */
	public function execute (&$value, &$error) {
		if (!$name = $this->getParameter('field')) {
			return true;
		}

		$field = $this->getContext()->getRequest()->getParameter($name);

		if ($value != $field) {
			$error = $this->getParameter('match_error');
			return false;
		}

		return true;
	}
}
?>