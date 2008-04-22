<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 選択バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSChoiceValidator.class.php 199 2008-04-19 04:12:14Z pooza $
 */
class BSChoiceValidator extends Validator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = null) {
		$this->setParameter('choices', null);
		$this->setParameter('choices_error', '正しくありません。');
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
		$choices = BSString::explode(',', $this->getParameter('choices'));
		if (!$choices->isIncluded($value)) {
			$error = $this->getParameter('choices_error');
			return false;
		}
		return true;
	}
}
?>