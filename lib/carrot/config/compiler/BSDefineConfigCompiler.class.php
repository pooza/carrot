<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * 定数設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDefineConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$prefix = preg_replace('/_$/', '', $this->getParameter('prefix'));
		$this->putLine('$constants = array(');

		foreach ($this->getConstants($file->getResult(), $prefix) as $key => $value) {
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

	private function getConstants ($arg, $prefix) {
		if (BSArray::isArray($arg)) {
			if (isset($arg[0])) { //配列であっても、連想配列でなければノードと見なす
				return array(strtoupper($prefix) => implode(',', $arg));
			} else { //連想配列だけがブランチを持つ
				$constants = array();
				foreach ($arg as $key => $value) {
					if (preg_match('/^\./', $key)) { //"."で始まるキーはプリフィックスに含まない
						$constants += $this->getConstants($value, $prefix);
					} else {
						$constants += $this->getConstants($value, $prefix . '_' . $key);
					}
				}
				return $constants;
			}
		} else { //$argが配列でない場合は、無条件にノードと見なす
			return array(strtoupper($prefix) => $arg);
		}
	}
}

/* vim:set tabstop=4 ai: */
?>