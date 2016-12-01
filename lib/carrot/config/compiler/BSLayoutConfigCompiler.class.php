<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * ディレクトリレイアウト設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLayoutConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$this->putLine('return [');
		foreach ($file->getResult() as $name => $params) {
			$this->putLine(sprintf('  %s => [', self::quote($name)));
			foreach ($params as $key => $value) {
				$this->putLine(parent::replaceConstants(
					sprintf('    %s => %s,', self::quote($key), self::quote($value))
				));
			}
			$this->putLine('  ],');
		}
		$this->putLine('];');
		return $this->getBody();
	}
}

/* vim:set tabstop=4: */
