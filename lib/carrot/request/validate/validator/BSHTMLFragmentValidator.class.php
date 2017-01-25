<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * HTMLフラグメントバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTMLFragmentValidator extends BSValidator {
	private $allowedTags;
	private $invalidNode;

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if (!BS_REQUEST_VALIDATE_HTML_FRAGMENT_ENABLE) {
			return true;
		}
		try {
			$command = $this->createCommand();
			$html = new BSStringFormat('<!DOCTYPE html><title>0</title><body>%s</body>');
			$html[] = str_replace("\n", ' ', $value);
			$command->addValue($html->getContents());
			$errors = new BSArray;
			foreach ($command->getResult() as $line) {
				if (mb_ereg('^line [0-9]+ column [0-9]+ - (.*)$', $line, $matches)) {
					$errors[] = $matches[1];
				}
			}
			if (!!$errors->count()) {
				$this->error = $errors->join(' | ');
				return false;
			}
		} catch (BSException $e) {
			$this->error = $e->getMessage();
			return false;
		}
		return true;
	}

	private function createCommand () {
		$command = new BSCommandLine('echo');
		$tidy = new BSCommandLine('bin/tidy5');
		$tidy->setDirectory(BSFileUtility::getDirectory('tidy5'));
		$tidy->addValue('-e');
		$command->registerPipe($tidy);
		$command->setStderrRedirectable();
		return $command;
	}
}

/* vim:set tabstop=4: */
