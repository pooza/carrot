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
 * @version $Id$
 */
class BSChoiceValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = null) {
		$this->setParameter('choices', null);
		$this->setParameter('choices_error', '正しくありません。');
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		$choices = BSString::explode(',', $this->getParameter('choices'));
		if (!$choices->isIncluded($value)) {
			$this->error = $this->getParameter('choices_error');
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>