<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * Flashムービーバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFlashValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['type_error'] = 'ファイル形式が正しくありません。';
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
		try {
			if (BSString::isBlank($name = $value['tmp_name'])) {
				throw new BFlashException('ファイルが存在しない、又は正しくありません。');
			}
			$file = new BSFlashFile($name);
			$file->getAttributes();
		} catch (BSException $e) {
			$this->error = $this['type_error'];
			return false;
		}
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
