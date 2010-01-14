<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 文字列バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStringValidator extends BSValidator {
	const MAX_SIZE = 1024;

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['max'] = self::MAX_SIZE;
		$this['max_error'] = '長すぎます。';
		$this['min'] = null;
		$this['min_error'] = '短すぎます。';
		$this['invalid_error'] = '正しくありません。';
		return parent::initialize($params);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $entry) {
				$this->execute($entry);
			}
		} else {
			if (!mb_check_encoding($value)) {
				$this->error = $this['invalid_error'];
			}
			if (!BSString::isBlank($this['min']) && (BSString::getWidth($value) < $this['min'])) {
				$this->error = $this['min_error'];
			}
			if (!BSString::isBlank($this['max']) && ($this['max'] < BSString::getWidth($value))) {
				$this->error = $this['max_error'];
			}
		}
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
