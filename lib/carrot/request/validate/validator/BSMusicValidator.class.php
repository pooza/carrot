<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 楽曲バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMusicValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
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
		if (BSString::isBlank($name = $value['tmp_name'])) {
			throw new BSImageException('ファイルが存在しない、又は正しくありません。');
		}
		$file = new BSMusicFile($name);

		if (BSString::isBlank($file->getType())) {
			$this->error = $this['invalid_error'];
		}
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
