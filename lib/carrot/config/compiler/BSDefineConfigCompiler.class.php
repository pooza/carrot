<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * 定数設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDefineConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$this->putLine('$constants = array(');

		foreach ($this->getConstants($file->getResult()) as $key => $value) {
			$line = sprintf('  %s => %s,', self::quote($key), self::quote($value));
			$line = parent::replaceConstants($line);
			$this->putLine($line);
		}

		$this->putLine(');');
		$this->putLine('foreach ($constants as $name => $value) {');
		$this->putLine('  if (!defined($name)) {define($name, $value);}');
		$this->putLine('}');
		return $this->getBody();
	}

	private function getConstants ($arg, $prefix = BSConstantHandler::PREFIX) {
		if (BSArray::isArray($arg)) {
			if (isset($arg[0])) {
				return array(BSString::toUpper($prefix) => implode(',', $arg));
			} else {
				$constants = array();
				foreach ($arg as $key => $value) {
					$constants += $this->getConstants($value, $prefix . '_' . $key);
				}
				return $constants;
			}
		} else {
			return array(BSString::toUpper($prefix) => $arg);
		}
	}
}

/* vim:set tabstop=4: */
