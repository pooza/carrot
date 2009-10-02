<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * Smartyバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSmartyValidator extends BSValidator {

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		$tempfile = BSFile::getTemporaryFile('.tpl');
		if (is_array($value) && isset($value['is_file']) && !!$value['is_file']) {
			$file = new BSFile($value['tmp_name']);
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
