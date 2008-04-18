<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 選択バリデータ
 *
 * パラメータ
 * choices: 選択データ(array)
 * choices_error: バリデーションエラーが発生した時に表示するメッセージ
 * sensitive: 大小文字を区別するか(デフォルト：false)
 * valid: 選択データのいずれかのデータかチェックする(デフォルト：true)
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://mojavi.withit.info/ 参考
 */
class BSChoiceValidator extends Validator {

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 結果
	 */
	public function execute (&$value, &$error) {
		$found = false;

		if (!$this->getParameter('sensitive')) {
			$newValue = strtolower($value);
		} else {
			$newValue = &$value;
		}

		if (in_array($newValue, $this->getParameter('choices'))) {
			$found = true;
		}

		if (($this->getParameter('valid') && !$found)
			|| (!$this->getParameter('valid') && $found))
		{
			$error = $this->getParameter('choices_error');
			return false;
		}

		return true;
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = null) {
		$this->setParameter('choices', array());
		$this->setParameter('choices_error', 'Invalid value');
		$this->setParameter('sensitive', false);
		$this->setParameter('valid', true);
		parent::initialize($context, $parameters);

		if (!$this->getParameter('sensitive')) {
			$choices = array();
			foreach ($this->getParameter('choices') as $choice) {
				$choices[] = strtolower($choice);
			}
			$this->setParameter('choices', $choices);
		}

		return true;
	}
}
?>