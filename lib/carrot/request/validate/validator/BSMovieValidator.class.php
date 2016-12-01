<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 動画バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMovieValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['invalid_error'] = '正しいファイルではありません。';
		return BSValidator::initialize($params);
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
			$file = new BSMovieFile($value['tmp_name']);
			if (!$file->isExists() || !$file->validate()) {
				$this->error = $this['invalid_error'];
				if (!BSString::isBlank($error = $file->getError())) {
					$this->error .= '(' . $error . ')';
				}
			}
		} catch (Exception $e) {
			$this->error = $this['invalid_error'];
		}
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
