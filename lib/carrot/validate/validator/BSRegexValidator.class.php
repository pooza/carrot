<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 正規表現バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRegexValidator extends BSStringValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['match'] = true;
		$this['match_error'] = '正しくありません。';
		$this['pattern'] = null;
		parent::initialize($parameters);

		if (!$this['pattern']) {
			throw new BSValidateException('正規表現パターンが指定されていません。');
		}
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if (!parent::execute($value)) {
			return false;
		}

		$match = $this['match'];
		$pattern = $this['pattern'];
		if (($match && !preg_match($pattern, $value))
			|| (!$match && preg_match($pattern, $value)))
		{
			$this->error = $this['match_error'];
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4: */
