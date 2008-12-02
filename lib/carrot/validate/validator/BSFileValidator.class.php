<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * ファイルバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFileValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['size'] = 2;
		$this['size_error'] = 'ファイルサイズが大きすぎます。';
		$this['invalid_error'] = '正しいファイルではありません。';
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
			$this->error = $this['invalid_error'];
			return false;
		} else if ($value['name']) {
			if (($this['size'] * 1024 * 1024) < $value['size']) {
				$this->error = $this['size_error'];
				return false;
			} else if ($value['error'] == 2) {
				$this->error = $this['size_error'];
				return false;
			} else if ($value['error']) {
				$this->error = $this['invalid_error'];
				return false;
			}

			$file = new BSFile($value['tmp_name']);
			if (!$file->isExists() || !$file->isUploaded()) {
				$this->error = $this['invalid_error'];
				return false;
			}
		}
		return true;
	}
}

/* vim:set tabstop=4 ai: */
?>