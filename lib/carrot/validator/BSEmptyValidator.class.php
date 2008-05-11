<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 必須バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSEmptyValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = null) {
		$this->setParameter('required_msg', '空欄です。');
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
		if (is_array($value) || ($value instanceof BSArray)) {
			if (isset($value['is_file']) && $value['is_file']) {
				$isEmpty = ($this->request->getFile($name)->getParameter('name') == null);
			} else {
				$isEmpty = (count($value) == 0);
			}
		} else {
			$isEmpty = ($value == '');
		}

		if ($isEmpty) {
			$this->error = $this->getParameter('required_msg');
			return false;
		}
		return true;
	}
}
?>