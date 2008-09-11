<?php
/**
 * @package org.carrot-framework
 * @subpackage validator
 */

/**
 * Smartyバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSmartyValidator extends BSValidator {
	private $file;

	/**
	 * 一時ファイルを返す
	 *
	 * @access private
	 * @return BSFile 一時ファイル
	 */
	private function getFile () {
		if (!$this->file) {
			$name = BSCrypt::getSHA1(BSDate::getNow('YmdHis') . BSNumeric::getRandom());
			$dir = BSController::getInstance()->getDirectory('tmp');
			if (!$this->file = $dir->createEntry($name)) {
				throw new BSFileException('一時ファイルが生成できません。');
			}
		}
		return $this->file;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		// Smarty_Compilerクラスは例外をthorw出来ない
		function handleSmartyValidatorError ($errno, $errstr) {
			if ($errno != E_NOTICE) {
				throw new BSSmartyException($errstr);
			}
			return true;
		}
		set_error_handler('handleSmartyValidatorError');

		try {
			$smarty = new BSSmarty;
			$this->getFile()->setContents($value);
			$smarty->setTemplate($this->getFile());
			$smarty->getContents();
		} catch (Exception $e) {
			$this->error = $e->getMessage();
		}

		restore_error_handler();
		$this->getFile()->delete();
		return ($this->error == null);
	}
}

/* vim:set tabstop=4 ai: */
?>