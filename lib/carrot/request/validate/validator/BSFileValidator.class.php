<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * ファイルバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFileValidator extends BSValidator {
	const ATTACHABLE = 'ATTACHABLE';

	/**
	 * 許可される拡張子を返す
	 *
	 * @access private
	 * @return BSArray 許可される拡張子、全てを許可する場合は空配列
	 */
	private function getAllowedSuffixes () {
		if (BSArray::isArray($this['suffixes'])) {
			$suffixes = new BSArray($this['suffixes']);
		} else if (BSString::toUpper($this['suffixes']) == self::ATTACHABLE) {
			$suffixes = BSMIMEType::getAttachableTypes()->getKeys();
		} else {
			$suffixes = BSString::explode(',', $this['suffixes']);
		}
		$suffixes->uniquize();
		$suffixes->trim();
		return $suffixes;
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['size'] = 2;
		$this['size_error'] = 'ファイルサイズが大きすぎます。';
		$this['invalid_error'] = '正しいファイルではありません。';
		$this['suffixes'] = null;
		$this['suffix_error'] = 'ファイル形式が正しくありません。';
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
		if (!BSArray::isArray($value)) {
			$this->error = $this['invalid_error'];
			return false;
		} else if (!BSString::isBlank($value['name'])) {
			$suffix = BSString::toLower(BSMIMEUtility::getFileNameSuffix($value['name']));
			$suffixes = $this->getAllowedSuffixes();
			if (($this['size'] * 1024 * 1024) < $value['size']) {
				$this->error = $this['size_error'];
				return false;
			} else if ($suffixes->count() && !$suffixes->isContain($suffix)) {
				$this->error = $this['suffix_error'];
				return false;
			} else if (in_array($value['error'], range(1, 2))) {
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

/* vim:set tabstop=4: */
