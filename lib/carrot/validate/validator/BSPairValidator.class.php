<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 一致バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPairValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['field'] = null;
		$this['match_error'] = '一致しません。';
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
		if (!$name = $this['field']) {
			return true;
		}

		if ($value != BSRequest::getInstance()->getParameter($name)) {
			$this->error = $this['match_error'];
			return false;
		}

		return true;
	}
}
?>