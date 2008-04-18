<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 一致バリデータ
 *
 * パラメータ
 * comparison_field: 他入力項目データのHTMLフィールド名を設定する
 * match_error: 他入力項目データと一致しない時に表示するエラーメッセージ
 * sensitive: 大小文字を区別するか(デフォルト：true)
 * is_equal: 他入力項目データと入力データが一致するか(デフォルト：true)
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://mojavi.withit.info/ 参考
 */
class BSPairValidator extends Validator {

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 結果
	 */
	public function execute (&$value, &$error) {
		$name = $this->getParameter('comparison_field');
		$comparisonField = $this->getContext()->getRequest()->getParameter($name);

		if ($this->getParameter('sensitive')) {
			$result = strcmp($value, $comparisonField);
		} else {
			$result = strcasecmp($value, $comparisonField);
		}

		if ($this->getParameter('is_equal') && ($result == 0)) {
			return true;
		}
		if (!$this->getParameter('is_equal') && ($result != 0)) {
			return true;
		}

		$error = $this->getParameter('match_error');
		return false;
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = null) {
		$this->setParameter('comparison_field', '');
		$this->setParameter('sensitive', true);
		$this->setParameter('is_equal', true);
		$this->setParameter('match_error', '一致しません。');
		return parent::initialize($context, $parameters);
	}
}
?>