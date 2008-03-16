<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * レコードバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSRecordValidator extends Validator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context mojaviコンテキスト
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($context, $parameters = array()) {
		return parent::initialize(
			$context,
			array_merge(array('exist' => true), $parameters)
		);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param string $value バリデーション対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean そのIDを持ったレコードが存在すればTrue
	 */
	public function execute (&$value, &$error) {
		try {
			$class = BSString::pascalize($this->getParameter('table')) . 'Handler';
			$table = new $class;
			$isExist = ($table->getRecord($value) != null);
		} catch (Exception $e) {
			$isExist = false;
		}

		if ($this->getParameter('exist') && !$isExist) {
			$error = "存在しません。";
			return false;
		} else if (!$this->getParameter('exist') && $isExist) {
			$error = "重複します。";
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>