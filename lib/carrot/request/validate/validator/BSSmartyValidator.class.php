<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * Smartyバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSmartyValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['invalid_encoding_error'] = '正しいエンコードではありません。';
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
		$tempfile = BSFileUtility::createTemporaryFile('.tpl', 'BSTemplateFile');
		if (is_array($value) && isset($value['is_file']) && !!$value['is_file']) {
			$file = new BSFile($value['tmp_name']);
			if (!mb_check_encoding($file->getContents())) {
				$this->error = $this['invalid_encoding_error'];
				return false;
			}
			$tempfile->setContents($file->getContents());
		} else {
			$tempfile->setContents($value);
		}

		try {
			$smarty = new BSSmarty;
			$smarty->setTemplate($tempfile);
			$smarty->getContents();
		} catch (Exception $e) {
			$this->error = $e->getMessage();
		}

		$tempfile->delete();
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
