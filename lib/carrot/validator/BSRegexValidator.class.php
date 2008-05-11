<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 正規表現バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSRegexValidator extends BSStringValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = null) {
		$this->setParameter('match', true);
		$this->setParameter('match_error', '正しくありません。');
		$this->setParameter('pattern', null);
		parent::initialize($parameters);

		if (!$this->getParameter('pattern')) {
			throw new BSValidatorException('正規表現パターンが指定されていません。');
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

		$match = $this->getParameter('match');
		$pattern = $this->getParameter('pattern');
		if (($match && !preg_match($pattern, $value))
			|| (!$match && preg_match($pattern, $value)))
		{
			$this->error = $this->getParameter('match_error');
			return false;
		}

		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>