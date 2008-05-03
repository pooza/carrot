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
class BSDefineConfigHandler extends BSConfigHandler {
	public function execute (BSIniFile $file) {
		$this->clearBody();
		$prefix = preg_replace('/_$/', '', $this->getParameter('prefix'));
		$this->putLine('$constants = array(');
		foreach ($file->getContents() as $category => $values) {
			foreach ($values as $key => $value) {
				if (preg_match('/^\\./', $category)) {
					$key = array($prefix, $key);
				} else {
					$key = array($prefix, $category, $key);
				}
				$line = sprintf(
					'  %s => %s,',
					parent::literalize(strtoupper(implode('_', $key))),
					parent::literalize($value)
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