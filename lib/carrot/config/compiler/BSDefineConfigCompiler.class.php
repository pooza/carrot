<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 定数定義
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

		foreach ($file->getResult() as $category => $values) {
			if (!is_array($values) && !($values instanceof BSArray)) {
				continue;
			}
			foreach ($values as $key => $value) {
				if (preg_match('/^\\./', $category)) { // .iniフォーマットとの互換性
					$key = array($prefix, $key);
				} else {
					$key = array($prefix, $category, $key);
				}
				$line = sprintf(
					'  %s => %s,',
					self::quote(strtoupper(implode('_', $key))),
					self::quote($value)
				);
				$line = parent::replaceConstants($line);
				$this->putLine($line);
			}
		}
		$this->putLine(');');
		$this->putLine('foreach ($constants as $name => $value) {');
		$this->putLine('  if (!defined($name)) {define($name, $value);}');
		$this->putLine('}');
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>