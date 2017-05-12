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
				if (!mb_ereg('^line [0-9]+ column [0-9]+ - (.*)$', $line, $matches)) {
					continue;
				}
				if (!BSString::isBlank($message = $this->translateMessage($matches[1]))) {
					$errors[$message] = $message;
				}
			}
			if (!!$errors->count()) {
				$this->error = $errors->join();
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
		$tidy->addValue('-errors');
		$command->registerPipe($tidy);
		$command->setStderrRedirectable();
		return $command;
	}

	private function translateMessage ($message) {
		$templates = [
			'^<([^>]+)> lacks "([^"]+)" attribute' => '<%s>タグには%s属性が必要です。',
			'^missing </([^>]+)>' => '<%s>タグが閉じられていません。',
			'^discarding unexpected <([^>]+)>' => '<%s>タグが予期せぬ場所に書かれています。',
			'^<([^>]+)> attribute "([^"]+)" not allowed' => '<%s>タグに%s属性を含めることはできません。',
			'^content occurs after end of body' => '<body>タグを閉じてはいけません。',
			'^trimming empty <([^>]+)>' => '<%s>タグの中身が空です。',
		];
		$message = str_replace('Warning: ', '', $message);
		foreach ($templates as $pattern => $template) {
			if (mb_ereg($pattern, $message, $matches)) {
				if (BSString::isBlank($template)) {
					return;
				}
				$matches = BSArray::create($matches);
				$matches->shift();
				$format = new BSStringFormat($template);
				foreach ($matches as $match) {
					$format[] = $match;
				}
				return $format->getContents();
			}
		}
		return $message;
	}
}

