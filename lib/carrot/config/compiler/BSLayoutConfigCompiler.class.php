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
		$this->putLine('$dirs = array();');
		foreach ($file->getResult() as $name => $params) {
			foreach ($params as $key => $value) {
				$line = sprintf(
					'$dirs[%s][%s] = %s;',
					self::quote($name),
					self::quote($key),
					self::quote($value)
				);
				$line = parent::replaceConstants($line);
				$this->putLine($line);
			}
		}
		$this->putLine('return $dirs;');
		return $this->getBody();
	}
}

/* vim:set tabstop=4: */
