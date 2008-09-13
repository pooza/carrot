<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
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
	public function initialize ($parameters = array()) {
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
		if (!BSArray::isArray($value)) {
			$value = array($value);
		}
		foreach ($value as $item) {
			if (!$this->getChoices()->isIncluded($item)) {
				$this->error = $this->getParameter('choices_error');
				return false;
			}
		}
		return true;
	}

	private function getChoices () {
		$choices = $this->getParameter('choices');
		if (is_array($choices)) {
			$choices = new BSArray($choices);
		} else {
			$choices = BSString::explode(',', $choices);
		}
		return $choices;
	}
}

/* vim:set tabstop=4 ai: */
?>