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
		$file = BSFile::getTemporaryFile('.tpl');
		$file->setContents($value);

		try {
			$smarty = new BSSmarty;
			$smarty->setTemplate($file);
			$smarty->getContents();
		} catch (Exception $e) {
			$this->error = $e->getMessage();
		}

		$file->delete();
		return BSString::isBlank($this->error);
	}
}

/* vim:set tabstop=4: */
